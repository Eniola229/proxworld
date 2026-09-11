<?php

namespace App\Jobs;

use App\Models\Provider;
use App\Models\ProviderServiceCache;
use App\ProxyProviders\ProxyProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Syncs ONE provider's catalog. Dispatched per-provider by
 * `providers:sync-services` so a slow provider doesn't hold up the others —
 * each one runs as its own queue job instead of all providers being fetched
 * back-to-back in a single blocking command.
 */
class SyncProviderServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 60;

    public function __construct(public string $providerId)
    {
    }

    public function handle(): void
    {
        $provider = Provider::find($this->providerId);

        if (! $provider) {
            return;
        }

        try {
            $driver = ProxyProviderFactory::make($provider);
            $products = $driver->getProducts();

            foreach ($products as $product) {
                ProviderServiceCache::updateOrCreate(
                    ['provider_id' => $provider->id, 'external_service_id' => $product['external_service_id']],
                    [
                        'name' => $product['name'],
                        'type' => $product['type'] ?? null,
                        'unit' => $product['unit'] ?? 'unit',
                        'raw_rate' => $product['rate'],
                        'raw_currency' => $product['currency'] ?? 'USD',
                        'raw_payload' => $product['raw'] ?? null,
                        'is_active' => true,
                        'synced_at' => now(),
                    ]
                );
            }

            Log::info("{$provider->name}: synced ".count($products).' plan(s).');
        } catch (\Throwable $e) {
            Log::error("Failed to sync services for provider {$provider->name}: ".$e->getMessage());
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SyncProviderServicesJob permanently failed for provider {$this->providerId}: ".$e->getMessage());
    }
}