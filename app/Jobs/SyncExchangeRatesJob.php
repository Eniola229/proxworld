<?php

namespace App\Jobs;

use App\Services\ExchangeRateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queued so the admin "Refresh" buttons return instantly instead of making
 * the request wait on the exchange rate API round-trip. ExchangeRateService
 * already handles the primary/backup fallback and leaves old cached rates
 * alone on total failure, so this job just needs to call it and log.
 */
class SyncExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 30;

    public function handle(ExchangeRateService $rates): void
    {
        $rates->syncAll();
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SyncExchangeRatesJob failed: '.$e->getMessage());
    }
}