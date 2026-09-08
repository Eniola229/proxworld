<?php

namespace App\Types;

/** Where an order originated — used for profit reporting splits. */
class OrderChannel
{
    const DIRECT = 'direct';           // normal customer, storefront checkout
    const RESELLER_PANEL = 'reseller_panel'; // reseller buying for themselves
    const RESELLER_API = 'reseller_api';     // reseller's own API key, on behalf of their client
    const PUBLIC_API = 'public_api';   // "pay for API" customer via Sanctum token

    public static function all(): array
    {
        return [self::DIRECT, self::RESELLER_PANEL, self::RESELLER_API, self::PUBLIC_API];
    }
}
