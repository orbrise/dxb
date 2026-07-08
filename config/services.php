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

    // Wasender WhatsApp gateway — used by the "Claim Your Profile" flow.
    // We generate the OTP locally, hash it, and check it ourselves (see
    // ProfileClaimController); Wasender just delivers the plaintext body.
    // When any credential is missing, the service returns a simulated
    // success so the flow stays testable locally and any 4-8 digit code
    // is accepted at verify time.
    //
    // base_url is the API host from the Wasender dashboard, e.g.
    // https://wasender.online/external-api. IP whitelisting on the
    // dashboard is mandatory — requests from non-whitelisted IPs are
    // rejected outright.
    'wasender' => [
        'base_url' => env('WASENDER_BASE_URL', 'https://wasender.online/external-api'),
        'client_id' => env('WASENDER_CLIENT_ID'),
        'client_secret' => env('WASENDER_CLIENT_SECRET'),
        // Optional. Leave empty to use the account's default WhatsApp
        // sender; set to a specific number or account id when the
        // account has multiple senders and you need to pin one.
        'from_number' => env('WASENDER_FROM_NUMBER'),
        'whatsapp_account_id' => env('WASENDER_WHATSAPP_ACCOUNT_ID'),
        // Optional. When set, OTP sends use the template endpoint (which
        // delivers outside the 24-hour customer window) instead of the
        // free-form send-message endpoint.
        'template_id' => env('WASENDER_TEMPLATE_ID'),
    ],

    // Firebase Phone Auth — used by the SMS channel of the profile-claim
    // flow. Firebase Phone Auth is client-driven: the browser SDK sends
    // the SMS via Google and returns an ID token which we verify with
    // FirebaseTokenVerifier. Only project_id is required server-side; the
    // rest are exposed to the blade view so the JS SDK can initialise.
    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'app_id' => env('FIREBASE_APP_ID'),
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
