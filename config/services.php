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
        'environment' => env('FLUTTERWAVE_ENV', 'sandbox'), // sandbox | production
        'base_url' => env('FLUTTERWAVE_ENV', 'sandbox') === 'production'
            ? 'https://f4bexperience.flutterwave.com'
            : 'https://developersandbox-api.flutterwave.com',
        'auth_url' => 'https://idp.flutterwave.com/realms/flutterwave/protocol/openid-connect/token',
        'client_id' => env('FLUTTERWAVE_CLIENT_ID'),
        'client_secret' => env('FLUTTERWAVE_CLIENT_SECRET'),
        'secret_hash' => env('FLUTTERWAVE_SECRET_HASH'), // webhook verif-hash, unchanged from v3
    ],
    
    'exchange' => [
        'base_currency' => env('EXCHANGE_BASE_CURRENCY', 'NGN'),
        'primary_base_url' => env('EXCHANGE_PRIMARY_URL', 'https://open.er-api.com/v6/latest'),
        'backup_base_url' => env('EXCHANGE_BACKUP_BASE_URL', 'https://api.frankfurter.dev/v1/latest'),
    ],

    'telegram' => [
        'support_url' => env('SUPPORT_TELEGRAM_URL'),
    ],

];
