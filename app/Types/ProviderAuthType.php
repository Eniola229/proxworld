<?php

namespace App\Types;

/** How a proxy provider's driver authenticates its HTTP requests. */
class ProviderAuthType
{
    const BEARER_TOKEN = 'bearer_token';       // Authorization: Bearer <key>
    const HEADER_TOKEN = 'header_token';       // custom header, e.g. X-Access-Token
    const LOGIN_TOKEN = 'login_token';         // POST /auth returns a short-lived token first
    const API_KEY_QUERY = 'api_key_query';     // ?api_key=... on every request

    public static function all(): array
    {
        return [self::BEARER_TOKEN, self::HEADER_TOKEN, self::LOGIN_TOKEN, self::API_KEY_QUERY];
    }
}
