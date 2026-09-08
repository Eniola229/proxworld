<?php

namespace App\Types;

class ProductType
{
    const RESIDENTIAL = 'residential';
    const DATACENTER = 'datacenter';
    const ISP = 'isp';
    const MOBILE = 'mobile';

    public static function all(): array
    {
        return [self::RESIDENTIAL, self::DATACENTER, self::ISP, self::MOBILE];
    }
}
