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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'whatsapp' => [
        'api_token' => env('WHATSAPP_API_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'your_verify_token'),
    ],

    'cloudflare' => [
        // API token with Zone.Cache Purge permission. Leave empty to disable edge purging.
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        // Zone ID of the production site (Cloudflare dashboard → Overview → API section).
        'zone_id' => env('CLOUDFLARE_ZONE_ID'),
        // Site base URL (e.g. https://evoory.com) — used to build absolute URLs for purges.
        'site_url' => env('CLOUDFLARE_SITE_URL', env('APP_URL')),
        // Queue purges instead of calling the API inline. Recommended in production.
        'queue' => env('CLOUDFLARE_PURGE_QUEUE', false),
        // Skip purging in local/testing environments.
        'enabled' => env('CLOUDFLARE_PURGE_ENABLED', false),
    ],

];
