<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Support\CurrencyCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        foreach (CurrencyCatalog::all() as $code => $meta) {
            Currency::updateOrCreate(
                ['currency' => $code],
                ['name' => $meta['name'], 'symbol' => $meta['symbol'], 'is_default' => $code === CurrencyCatalog::FALLBACK, 'is_active' => true]
            );
        }

        Cache::forget('currencies.supported.v1');
    }
}