<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * NGN is the base currency. Rates are cached in the DB so a provider outage
 * never breaks checkout — it just uses the last known rate (isStale() lets
 * the UI/admin flag it, the scheduled `exchange-rates:sync` command keeps
 * it fresh every 2 hours).
 *
 * Two free, no-key providers are tried in order on every sync:
 *   1. open.er-api.com   — ExchangeRate-API's Open Access endpoint (daily updates)
 *   2. api.frankfurter.dev — free, no key (coarser update cadence for non-ECB
 *      currencies like NGN, used only when the primary is unreachable)
 * If both fail, existing cached rates are left untouched and a warning is logged.
 */
class ExchangeRateService
{
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rate = $this->rate($from, $to);

        return round($amount * $rate, 4);
    }

    public function rate(string $from, string $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $direct = ExchangeRate::where('from_currency', $from)->where('to_currency', $to)->first();

        if ($direct) {
            return (float) $direct->rate;
        }

        // Fall back to going via the base currency if a direct pair isn't cached.
        $base = config('services.exchange.base_currency', 'NGN');
        $toBase = ExchangeRate::where('from_currency', $from)->where('to_currency', $base)->first();
        $fromBase = ExchangeRate::where('from_currency', $base)->where('to_currency', $to)->first();

        if ($toBase && $fromBase) {
            return (float) $toBase->rate * (float) $fromBase->rate;
        }

        Log::warning("No exchange rate found for {$from} -> {$to}; defaulting to 1.0");

        return 1.0;
    }

    /** Called by the `exchange-rates:sync` scheduled command, and by the admin "Refresh Exchange Rates" button. */
    public function syncAll(): void
    {
        $base = config('services.exchange.base_currency', 'NGN');

        $result = $this->fetchFromPrimary($base) ?? $this->fetchFromBackup($base);

        if ($result === null) {
            Log::error("Exchange rate sync failed: both primary (open.er-api.com) and backup (frankfurter.dev) providers were unreachable for base {$base}. Keeping last known rates.");

            return;
        }

        [$rates, $source] = $result;

        foreach ($rates as $currency => $rate) {
            if ($currency === $base || (float) $rate <= 0) {
                continue;
            }

            // Store both directions so `rate()` never has to invert on the fly.
            ExchangeRate::updateOrCreate(
                ['from_currency' => $base, 'to_currency' => $currency],
                ['rate' => $rate, 'source' => $source, 'fetched_at' => now()]
            );

            ExchangeRate::updateOrCreate(
                ['from_currency' => $currency, 'to_currency' => $base],
                ['rate' => 1 / $rate, 'source' => $source, 'fetched_at' => now()]
            );
        }

        Log::info("Exchange rates synced from {$source} for base {$base} (".count($rates)." currencies).");
    }

    /** @return array{0: array<string,float>, 1: string}|null [$rates, $sourceName] */
    protected function fetchFromPrimary(string $base): ?array
    {
        $url = rtrim(config('services.exchange.primary_base_url'), '/');

        try {
            $response = Http::timeout(10)->get("{$url}/{$base}");

            if ($response->successful() && $response->json('result') === 'success') {
                return [$response->json('rates', []), 'open-er-api'];
            }

            Log::warning('Primary exchange rate provider (open.er-api.com) returned a bad response: '.$response->body());
        } catch (\Throwable $e) {
            Log::warning('Primary exchange rate provider (open.er-api.com) threw an error: '.$e->getMessage());
        }

        return null;
    }

    /** @return array{0: array<string,float>, 1: string}|null [$rates, $sourceName] */
    protected function fetchFromBackup(string $base): ?array
    {
        $url = config('services.exchange.backup_base_url'); // should be: https://api.frankfurter.dev/v2/rates

        try {
            $response = Http::timeout(10)->get($url, ['base' => $base]);

            if ($response->successful() && is_array($response->json())) {
                // v2 returns a flat array of {date, base, quote, rate} rows, not {rates: {...}}
                $rates = collect($response->json())
                    ->filter(fn ($row) => isset($row['quote'], $row['rate']))
                    ->pluck('rate', 'quote')
                    ->toArray();

                if (! empty($rates)) {
                    return [$rates, 'frankfurter'];
                }
            }

            Log::warning('Backup exchange rate provider (frankfurter.dev) returned a bad response: '.$response->body());
        } catch (\Throwable $e) {
            Log::warning('Backup exchange rate provider (frankfurter.dev) threw an error: '.$e->getMessage());
        }

        return null;
    }
}