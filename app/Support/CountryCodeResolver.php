<?php

namespace App\Support;

class CountryCodeResolver
{
    private static array $map = [
        'afghanistan' => 'AF', 'albania' => 'AL', 'algeria' => 'DZ', 'andorra' => 'AD',
        'angola' => 'AO', 'argentina' => 'AR', 'armenia' => 'AM', 'australia' => 'AU',
        'austria' => 'AT', 'azerbaijan' => 'AZ', 'bahamas' => 'BS', 'bahrain' => 'BH',
        'bangladesh' => 'BD', 'belarus' => 'BY', 'belgium' => 'BE', 'belize' => 'BZ',
        'benin' => 'BJ', 'bolivia' => 'BO', 'bosnia and herzegovina' => 'BA', 'botswana' => 'BW',
        'brazil' => 'BR', 'brunei' => 'BN', 'bulgaria' => 'BG', 'burkina faso' => 'BF',
        'cambodia' => 'KH', 'cameroon' => 'CM', 'canada' => 'CA', 'chad' => 'TD',
        'chile' => 'CL', 'china' => 'CN', 'colombia' => 'CO', 'costa rica' => 'CR',
        'croatia' => 'HR', 'cuba' => 'CU', 'cyprus' => 'CY', 'czech republic' => 'CZ',
        'czechia' => 'CZ', 'denmark' => 'DK', 'dominican republic' => 'DO', 'ecuador' => 'EC',
        'egypt' => 'EG', 'el salvador' => 'SV', 'estonia' => 'EE', 'ethiopia' => 'ET',
        'finland' => 'FI', 'france' => 'FR', 'georgia' => 'GE', 'germany' => 'DE',
        'ghana' => 'GH', 'greece' => 'GR', 'guatemala' => 'GT', 'honduras' => 'HN',
        'hong kong' => 'HK', 'hungary' => 'HU', 'iceland' => 'IS', 'india' => 'IN',
        'indonesia' => 'ID', 'iran' => 'IR', 'iraq' => 'IQ', 'ireland' => 'IE',
        'israel' => 'IL', 'italy' => 'IT', 'jamaica' => 'JM', 'japan' => 'JP',
        'jordan' => 'JO', 'kazakhstan' => 'KZ', 'kenya' => 'KE', 'kuwait' => 'KW',
        'kyrgyzstan' => 'KG', 'laos' => 'LA', 'latvia' => 'LV', 'lebanon' => 'LB',
        'libya' => 'LY', 'liechtenstein' => 'LI', 'lithuania' => 'LT', 'luxembourg' => 'LU',
        'macau' => 'MO', 'madagascar' => 'MG', 'malaysia' => 'MY', 'maldives' => 'MV',
        'mali' => 'ML', 'malta' => 'MT', 'mauritius' => 'MU', 'mexico' => 'MX',
        'moldova' => 'MD', 'monaco' => 'MC', 'mongolia' => 'MN', 'montenegro' => 'ME',
        'morocco' => 'MA', 'mozambique' => 'MZ', 'myanmar' => 'MM', 'namibia' => 'NA',
        'nepal' => 'NP', 'netherlands' => 'NL', 'new zealand' => 'NZ', 'nicaragua' => 'NI',
        'niger' => 'NE', 'nigeria' => 'NG', 'north macedonia' => 'MK', 'norway' => 'NO',
        'oman' => 'OM', 'pakistan' => 'PK', 'panama' => 'PA', 'papua new guinea' => 'PG',
        'paraguay' => 'PY', 'peru' => 'PE', 'philippines' => 'PH', 'poland' => 'PL',
        'portugal' => 'PT', 'qatar' => 'QA', 'romania' => 'RO', 'russia' => 'RU',
        'rwanda' => 'RW', 'saudi arabia' => 'SA', 'senegal' => 'SN', 'serbia' => 'RS',
        'seychelles' => 'SC', 'singapore' => 'SG', 'slovakia' => 'SK', 'slovenia' => 'SI',
        'somalia' => 'SO', 'south africa' => 'ZA', 'south korea' => 'KR', 'spain' => 'ES',
        'sri lanka' => 'LK', 'sudan' => 'SD', 'sweden' => 'SE', 'switzerland' => 'CH',
        'syria' => 'SY', 'taiwan' => 'TW', 'tajikistan' => 'TJ', 'tanzania' => 'TZ',
        'thailand' => 'TH', 'togo' => 'TG', 'trinidad and tobago' => 'TT', 'tunisia' => 'TN',
        'turkey' => 'TR', 'turkmenistan' => 'TM', 'uganda' => 'UG', 'ukraine' => 'UA',
        'united arab emirates' => 'AE', 'uae' => 'AE', 'united kingdom' => 'GB', 'uk' => 'GB',
        'united states' => 'US', 'usa' => 'US', 'uruguay' => 'UY',
        'uzbekistan' => 'UZ', 'venezuela' => 'VE', 'vietnam' => 'VN', 'yemen' => 'YE',
        'zambia' => 'ZM', 'zimbabwe' => 'ZW',
    ];

    /** Pulls the trailing "... — Country" segment out of a service name string. */
    public static function extractCountryName(string $serviceName): ?string
    {
        $parts = preg_split('/\s*[—–-]\s*/u', $serviceName);
        if (! $parts || count($parts) < 2) {
            return null;
        }

        $last = trim(end($parts));

        return $last !== '' ? $last : null;
    }

    public static function codeForName(string $countryName): ?string
    {
        return self::$map[strtolower(trim($countryName))] ?? null;
    }

    /** name -> ['code' => .., 'name' => ..] resolved live from a service name. */
    public static function resolve(string $serviceName): ?array
    {
        $name = self::extractCountryName($serviceName);
        if (! $name) {
            return null;
        }

        $code = self::codeForName($name);
        if (! $code) {
            return null;
        }

        return ['code' => $code, 'name' => $name];
    }
}