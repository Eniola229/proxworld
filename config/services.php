<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ], 
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL').'/auth/google/callback'),
    ],

    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
        'sender_email' => env('BREVO_SENDER_EMAIL'),
        'sender_name' => env('BREVO_SENDER_NAME', env('APP_NAME')),
    ],

    'flutterwave' => [
        'base_url'    => 'https://api.flutterwave.com/v3',
        'public_key'  => env('FLW_PUBLIC_KEY'),
        'secret_key'  => env('FLW_SECRET_KEY'),
        'secret_hash' => env('FLW_SECRET_HASH'),
    ],

    'geo' => [
        'trust_cloudflare' => env('GEO_TRUST_CLOUDFLARE', false),
        'fake_country'     => env('GEO_FAKE_COUNTRY'),
    ],
    
    'exchange' => [
        'base_currency' => env('EXCHANGE_BASE_CURRENCY', 'NGN'),
        'primary_base_url' => env('EXCHANGE_PRIMARY_URL', 'https://open.er-api.com/v6/latest'),
        'backup_base_url' => env('EXCHANGE_BACKUP_BASE_URL', 'https://api.frankfurter.dev/v1/latest'),
    ],

    'telegram' => [
        'support_url' => env('SUPPORT_TELEGRAM_URL'),
    ],

    'tiktok' => [
        'pixel_code' => env('TIKTOK_PIXEL_CODE'),
        'access_token' => env('TIKTOK_ACCESS_TOKEN'),
        'enabled' => env('TIKTOK_ENABLED', false),
    ],

    'didit' => [
        'base_url'       => env('DIDIT_BASE_URL', 'https://verification.didit.me'),
        'api_key'        => env('DIDIT_API_KEY'),
        'workflow_id'    => env('DIDIT_WORKFLOW_ID'),
        'webhook_secret' => env('DIDIT_WEBHOOK_SECRET'),
    ],

];
