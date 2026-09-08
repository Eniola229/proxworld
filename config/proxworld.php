<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reseller storefront base domain
    |--------------------------------------------------------------------------
    | The root domain a reseller's subdomain is appended to for their
    | white-label storefront (e.g. subdomain "acme" + this value =
    | acme.proxworld.com). Defaults to the host of APP_URL if not set
    | explicitly, so you usually only need BASE_DOMAIN in production if it
    | differs from APP_URL's host.
    */

    'base_domain' => env('BASE_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),

    /*
    |--------------------------------------------------------------------------
    | Admin panel route prefix
    |--------------------------------------------------------------------------
    | The URL segment the admin panel is mounted under, e.g.
    | https://yourdomain.com/{this}. Change ADMIN_ROUTE_PREFIX in .env to
    | move the admin login off the guessable default "control-panel" —
    | no code changes needed. Must not start or end with a slash.
    */

    'admin_route_prefix' => env('ADMIN_ROUTE_PREFIX', 'control-panel'),

];
