<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\User;
use App\Support\CurrencyCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public function __construct(protected ExchangeRateService $rates)
    {
    }

    // ─── What we offer ──────────────────────────────────────────────

    public function supported(): array
    {
        return Cache::remember('currencies.supported.v1', 600, function () {
            $rows = Currency::where('is_active', true)->get()->keyBy(fn ($r) => strtoupper($r->currency));
            $out = [];

            foreach (CurrencyCatalog::all() as $code => $meta) {
                if ($rows->isNotEmpty() && ! $rows->has($code) && ! in_array($code, ['NGN', 'USD'], true)) {
                    continue;
                }

                $row = $rows->get($code);

                $out[$code] = [
                    'code' => $code,
                    'name' => $row?->name ?: $meta['name'],
                    'symbol' => $row?->symbol ?: $meta['symbol'],
                    'decimals' => $meta['decimals'],
                ];
            }

            return $out;
        });
    }

    public function isSupported(?string $code): bool
    {
        return $code !== null && isset($this->supported()[strtoupper($code)]);
    }

    public function walletCurrency(?User $user): string
    {
        return strtoupper($user?->preferred_currency ?: CurrencyCatalog::BASE);
    }

    // ─── Formatting ─────────────────────────────────────────────────

    public function meta(?string $code): array
    {
        $code = strtoupper($code ?: CurrencyCatalog::BASE);
        $all = $this->supported();
        $catalog = CurrencyCatalog::all();

        if (isset($all[$code])) {
            return $all[$code];
        }

        if (isset($catalog[$code])) {
            return ['code' => $code] + $catalog[$code];
        }

        return ['code' => $code, 'name' => $code, 'symbol' => $code, 'decimals' => 2];
    }

    public function symbol(?string $code): string
    {
        return $this->meta($code)['symbol'];
    }

    public function decimals(?string $code): int
    {
        return (int) $this->meta($code)['decimals'];
    }

    public function prefix(?string $code): string
    {
        $symbol = $this->symbol($code);

        return preg_match('/[A-Za-z]$/', $symbol) ? $symbol.' ' : $symbol;
    }

    public function format(float|int|string|null $amount, ?string $code = null, ?int $decimals = null): string
    {
        $code = strtoupper($code ?: $this->walletCurrency(auth()->user()));
        $number = (float) $amount;
        $places = $decimals ?? $this->decimals($code);

        return ($number < 0 ? '-' : '').$this->prefix($code).number_format(abs($number), $places);
    }

    public function formatCompact(float $amount, string $code): string
    {
        $places = $this->decimals($code) === 0 ? 0 : (abs($amount) >= 100 ? 0 : 2);

        return $this->format($amount, $code, $places);
    }

    public function display(?string $code): array
    {
        $meta = $this->meta($code);

        return ['code' => $meta['code'], 'prefix' => $this->prefix($meta['code']), 'decimals' => (int) $meta['decimals']];
    }

    public function roundForCharge(float $amount, string $code): float
    {
        return round($amount, $this->decimals($code));
    }

    /** Smallest top-up: 500 for NGN, 5 (in the wallet's own currency) for everything else. */
    public function minTopUp(string $code): float
    {
        return strtoupper($code) === CurrencyCatalog::BASE ? 500.0 : 5.0;
    }

    /** "≈ $0.13" for a non-NGN user's NGN referral balance; null for NGN users. */
    public function referralApprox(User $user, ExchangeRateService $rates): ?string
    {
        $currency = $this->walletCurrency($user);

        if ($currency === CurrencyCatalog::BASE) {
            return null;
        }

        $rate = $rates->rateOrNull(CurrencyCatalog::BASE, $currency);

        return $rate ? '≈ '.$this->format((float) $user->referral_balance * $rate, $currency) : null;
    }

    // ─── IP → country → currency ────────────────────────────────────

    /** @return array{country: ?string, currency: string} */
    public function detect(Request $request): array
    {
        $country = $this->countryFromRequest($request);

        return ['country' => $country, 'currency' => $this->fromCountry($country)];
    }

    public function fromCountry(?string $iso2): string
    {
        $code = CurrencyCatalog::currencyForCountry($iso2);

        return ($code && $this->isSupported($code)) ? $code : CurrencyCatalog::FALLBACK;
    }

    /** Logged in → their wallet currency. Guest → IP-detected (remembered in session). Falls back to NGN if no rate exists. */
    public function forVisitor(Request $request): string
    {
        if ($user = $request->user()) {
            return $this->walletCurrency($user);
        }

        if ($request->hasSession()) {
            $remembered = $request->session()->get('visitor_currency');

            if ($remembered && $this->isSupported($remembered)) {
                return $remembered;
            }
        }

        $currency = $this->detect($request)['currency'];

        if ($currency !== CurrencyCatalog::BASE && $this->rates->rateOrNull(CurrencyCatalog::BASE, $currency) === null) {
            $currency = CurrencyCatalog::BASE;
        }

        if ($request->hasSession()) {
            $request->session()->put('visitor_currency', $currency);
        }

        return $currency;
    }

    public function countryFromRequest(Request $request): ?string
    {
        if (! app()->isProduction() && ($fake = config('services.geo.fake_country'))) {
            return strtoupper($fake);
        }

        if (config('services.geo.trust_cloudflare')) {
            $cf = strtoupper((string) $request->header('CF-IPCountry'));

            if (preg_match('/^[A-Z]{2}$/', $cf) && ! in_array($cf, ['XX', 'T1'], true)) {
                return $cf;
            }
        }

        $ip = $request->ip();

        if (! $ip || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        return $this->lookupCountry($ip);
    }

    protected function lookupCountry(string $ip): ?string
    {
        $key = 'geo:country:'.md5($ip);
        $cached = Cache::get($key);

        if ($cached !== null) {
            return $cached === '' ? null : $cached;
        }

        $country = $this->queryIpwho($ip) ?? $this->queryCountryIs($ip);

        Cache::put($key, $country ?? '', $country ? now()->addDays(7) : now()->addMinutes(10));

        return $country;
    }

    protected function queryIpwho(string $ip): ?string
    {
        try {
            $response = Http::timeout(3)->acceptJson()->get("https://ipwho.is/{$ip}");

            if ($response->successful() && $response->json('success') === true) {
                return $this->cleanCode($response->json('country_code'));
            }
        } catch (\Throwable $e) {
            Log::warning('Geo lookup (ipwho.is) failed: '.$e->getMessage());
        }

        return null;
    }

    protected function queryCountryIs(string $ip): ?string
    {
        try {
            $response = Http::timeout(3)->acceptJson()->get("https://api.country.is/{$ip}");

            if ($response->successful()) {
                return $this->cleanCode($response->json('country'));
            }
        } catch (\Throwable $e) {
            Log::warning('Geo lookup (country.is) failed: '.$e->getMessage());
        }

        return null;
    }

    protected function cleanCode(mixed $value): ?string
    {
        $value = strtoupper((string) $value);

        return preg_match('/^[A-Z]{2}$/', $value) ? $value : null;
    }
}