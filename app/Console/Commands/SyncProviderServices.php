<?php

namespace App\Console\Commands;

use App\Jobs\SyncProviderServicesJob;
use App\Models\Provider;
use Illuminate\Console\Command;

class SyncProviderServices extends Command
{
    protected $signature = 'providers:sync-services';
    protected $description = "Queue a catalog refresh for each active provider's plans/products.";

    public function handle(): int
    {
        $providers = Provider::active()->get();

        foreach ($providers as $provider) {
            SyncProviderServicesJob::dispatch($provider->id);
            $this->info("{$provider->name}: queued.");
        }

        $this->info("Queued {$providers->count()} provider sync job(s). Run `php artisan queue:work` (or check your worker) to process them.");

        return self::SUCCESS;
    }
}