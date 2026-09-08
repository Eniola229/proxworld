<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['currency' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'is_default' => true],
            ['currency' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['currency' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['currency' => 'GHS', 'name' => 'Ghanaian Cedi', 'symbol' => 'GH₵'],
            ['currency' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh'],
            ['currency' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R'],
            ['currency' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
        ];

        foreach ($currencies as $c) {
            Currency::updateOrCreate(['currency' => $c['currency']], array_merge($c, ['is_active' => true]));
        }
    }
}
