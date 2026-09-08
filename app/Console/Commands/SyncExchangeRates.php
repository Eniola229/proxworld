<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class SyncExchangeRates extends Command
{
    protected $signature = 'exchange-rates:sync';
    protected $description = 'Refresh cached currency exchange rates from the configured provider.';

    public function handle(ExchangeRateService $service): int
    {
        $this->info('Syncing exchange rates...');
        $service->syncAll();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
