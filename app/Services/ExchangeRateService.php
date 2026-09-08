<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * NGN is the base currency. Rates are cached in the DB so a provider outage
 * never breaks checkout — it just uses the last known rate (isStale() lets
 * the UI/admin flag it, the scheduled `exchange-rates:sync` command keeps
 * it fresh every few hours).
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

    /** Called by the `exchange-rates:sync` scheduled command. */
    public function syncAll(): void
    {
        $base = config('services.exchange.base_currency', 'NGN');
        $apiKey = config('services.exchange.api_key');
        $baseUrl = config('services.exchange.base_url');

        if (! $apiKey) {
            Log::warning('Exchange rate API key not configured — skipping sync.');

            return;
        }

        $response = Http::get("{$baseUrl}/{$apiKey}/latest/{$base}");

        if (! $response->successful()) {
            Log::error('Exchange rate sync failed: '.$response->body());

            return;
        }

        $rates = $response->json('conversion_rates', []);

        foreach ($rates as $currency => $rate) {
            // Store both directions so `rate()` never has to invert on the fly.
            ExchangeRate::updateOrCreate(
                ['from_currency' => $base, 'to_currency' => $currency],
                ['rate' => $rate, 'source' => 'exchangerate-api', 'fetched_at' => now()]
            );

            if ((float) $rate > 0) {
                ExchangeRate::updateOrCreate(
                    ['from_currency' => $currency, 'to_currency' => $base],
                    ['rate' => 1 / $rate, 'source' => 'exchangerate-api', 'fetched_at' => now()]
                );
            }
        }
    }
}
