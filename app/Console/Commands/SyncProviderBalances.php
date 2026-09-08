<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\ProxyProviders\ProxyProviderFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncProviderBalances extends Command
{
    protected $signature = 'providers:sync-balances';
    protected $description = "Refresh each active provider's cached account balance.";

    public function handle(): int
    {
        foreach (Provider::active()->get() as $provider) {
            try {
                $driver = ProxyProviderFactory::make($provider);
                $balance = $driver->getBalance();

                $provider->update([
                    'cached_balance' => $balance,
                    'cached_balance_currency' => $driver->getBalanceCurrency(),
                    'balance_checked_at' => now(),
                ]);

                $this->info("{$provider->name}: {$balance} {$driver->getBalanceCurrency()}");
            } catch (\Throwable $e) {
                Log::error("Failed to sync balance for provider {$provider->name}: ".$e->getMessage());
                $this->error("{$provider->name}: failed — {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
