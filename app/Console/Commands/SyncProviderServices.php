<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\Models\ProviderServiceCache;
use App\ProxyProviders\ProxyProviderFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncProviderServices extends Command
{
    protected $signature = 'providers:sync-services';
    protected $description = "Refresh the cached catalog of each active provider's plans/products.";

    public function handle(): int
    {
        foreach (Provider::active()->get() as $provider) {
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

                $this->info("{$provider->name}: synced ".count($products).' plan(s).');
            } catch (\Throwable $e) {
                Log::error("Failed to sync services for provider {$provider->name}: ".$e->getMessage());
                $this->error("{$provider->name}: failed — {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
