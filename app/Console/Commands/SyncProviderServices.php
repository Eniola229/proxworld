<?php

namespace App\Console\Commands;

use App\Jobs\SyncProviderServicesJob;
use App\Models\Provider;
use Illuminate\Console\Command;

class SyncProviderServices extends Command
{
    protected $signature = 'providers:sync-services {--provider=* : Only sync providers whose name contains this (case-insensitive). Repeatable.}';
    protected $description = "Queue a catalog refresh for each active provider's plans/products.";

    public function handle(): int
    {
        $query = Provider::active();
        $names = $this->option('provider');

        if (! empty($names)) {
            $query->where(function ($q) use ($names) {
                foreach ($names as $name) {
                    $q->orWhere('name', 'like', "%{$name}%");
                }
            });
        }

        $providers = $query->get();

        if ($providers->isEmpty()) {
            $this->warn($names ? 'No active provider matching any of: '.implode(', ', $names) : 'No active providers found.');

            return self::SUCCESS;
        }

        foreach ($providers as $provider) {
            SyncProviderServicesJob::dispatch($provider->id);
            $this->info("{$provider->name}: queued.");
        }

        $this->info("Queued {$providers->count()} provider sync job(s). Run `php artisan queue:work` if it isn't already running.");

        return self::SUCCESS;
    }
}