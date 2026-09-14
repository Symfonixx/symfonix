<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'admin_email' => env('ADMIN_EMAIL'),

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
        ],
    ],

    'leads' => [
        'admin_email' => env('LEADS_ADMIN_EMAIL', env('ADMIN_EMAIL')),
    ],

    'fixer' => [
        'api_key' => env('FIXER_API_KEY'),
        'base_url' => env('FIXER_BASE_URL', 'https://data.fixer.io/api'),
    ],

    'whatsapp' => [
        'api_token' => env('WHATSAPP_API_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
    ],

    'fingerprint' => [
        'enabled' => env('FINGERPRINT_ENABLED', false),
        'host' => env('FINGERPRINT_HOST'),
        'port' => (int) env('FINGERPRINT_PORT', 4370),
        'comm_key' => (int) env('FINGERPRINT_COMM_KEY', 0),
        'timeout' => (float) env('FINGERPRINT_TIMEOUT', 10),
        'name_encoding' => env('FINGERPRINT_NAME_ENCODING', 'UTF-8'),
    ],

];
