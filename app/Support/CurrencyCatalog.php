<?php

namespace App\Support;

class CurrencyCatalog
{
    public const BASE = 'NGN';
    public const FALLBACK = 'USD';

    private static array $currencies = [
        'NGN' => ['name' => 'Nigerian Naira',            'symbol' => '₦',    'decimals' => 2],
        'USD' => ['name' => 'United States Dollar',      'symbol' => '$',    'decimals' => 2],
        'GBP' => ['name' => 'British Pound Sterling',    'symbol' => '£',    'decimals' => 2],
        'EUR' => ['name' => 'Euro',                      'symbol' => '€',    'decimals' => 2],
        'CAD' => ['name' => 'Canadian Dollar',           'symbol' => 'CA$',  'decimals' => 2],
        'GHS' => ['name' => 'Ghanaian Cedi',             'symbol' => 'GH₵',  'decimals' => 2],
        'KES' => ['name' => 'Kenyan Shilling',           'symbol' => 'KSh',  'decimals' => 2],
        'ZAR' => ['name' => 'South African Rand',        'symbol' => 'R',    'decimals' => 2],
        'UGX' => ['name' => 'Ugandan Shilling',          'symbol' => 'USh',  'decimals' => 0],
        'TZS' => ['name' => 'Tanzanian Shilling',        'symbol' => 'TSh',  'decimals' => 2],
        'RWF' => ['name' => 'Rwandan Franc',             'symbol' => 'FRw',  'decimals' => 0],
        'ZMW' => ['name' => 'Zambian Kwacha',            'symbol' => 'ZK',   'decimals' => 2],
        'EGP' => ['name' => 'Egyptian Pound',            'symbol' => 'E£',   'decimals' => 2],
        'XAF' => ['name' => 'Central African CFA Franc', 'symbol' => 'FCFA', 'decimals' => 0],
        'XOF' => ['name' => 'West African CFA Franc',    'symbol' => 'CFA',  'decimals' => 0],
        'COP' => ['name' => 'Colombian Peso',            'symbol' => 'COL$', 'decimals' => 2],
        'SLL' => ['name' => 'Sierra Leonean Leone',      'symbol' => 'Le',   'decimals' => 2],
    ];

    private static array $countryGroups = [
        'NGN' => ['NG'], 'GHS' => ['GH'], 'KES' => ['KE'], 'ZAR' => ['ZA'], 'UGX' => ['UG'],
        'TZS' => ['TZ'], 'RWF' => ['RW'], 'ZMW' => ['ZM'], 'EGP' => ['EG'], 'SLL' => ['SL'],
        'COP' => ['CO'], 'CAD' => ['CA'], 'USD' => ['US'],
        'GBP' => ['GB', 'JE', 'GG', 'IM'],
        'XAF' => ['CM', 'GA', 'CG', 'CF', 'TD', 'GQ'],
        'XOF' => ['SN', 'CI', 'BJ', 'BF', 'ML', 'NE', 'TG', 'GW'],
        'EUR' => ['AT', 'BE', 'BG', 'HR', 'CY', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LV', 'LT',
                  'LU', 'MT', 'NL', 'PT', 'SK', 'SI', 'ES', 'AD', 'MC', 'SM', 'VA', 'ME', 'XK'],
    ];

    private static ?array $countryMap = null;

    public static function all(): array
    {
        return self::$currencies;
    }

    public static function has(string $code): bool
    {
        return isset(self::$currencies[strtoupper($code)]);
    }

    public static function currencyForCountry(?string $iso2): ?string
    {
        if (! $iso2) {
            return null;
        }

        if (self::$countryMap === null) {
            self::$countryMap = [];
            foreach (self::$countryGroups as $currency => $countries) {
                foreach ($countries as $country) {
                    self::$countryMap[$country] = $currency;
                }
            }
        }

        return self::$countryMap[strtoupper($iso2)] ?? null;
    }
}