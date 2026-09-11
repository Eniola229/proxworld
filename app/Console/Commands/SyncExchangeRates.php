<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class SyncExchangeRates extends Command
{
    protected $signature = 'exchange-rates:sync';

    protected $description = 'Refresh cached exchange rates from the free provider APIs (open.er-api.com, fallback frankfurter.dev)';

    public function handle(ExchangeRateService $rates): int
    {
        $this->info('Syncing exchange rates...');

        $rates->syncAll();

        $this->info('Done.');

        return self::SUCCESS;
    }
}