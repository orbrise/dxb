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

    // Infobip raw SMS / WhatsApp — used by the "Claim Your Profile" flow.
    // Unlike Twilio Verify, Infobip does NOT own the OTP code; we generate
    // it locally, hash it, and check it ourselves (see
    // ProfileClaimController). When any credential is missing, the service
    // returns a simulated success so the flow stays testable locally and
    // any 4-8 digit code is accepted at verify time.
    //
    // base_url is the per-account host shown on the Infobip dashboard —
    // looks like https://abc123.api.infobip.com, NOT the generic
    // api.infobip.com.
    'infobip' => [
        'base_url' => env('INFOBIP_BASE_URL'),
        'api_key' => env('INFOBIP_API_KEY'),
        // Alphanumeric sender ID (e.g. "evoory") or a registered short
        // code / long number. Some countries enforce sender-ID approval.
        'sms_sender' => env('INFOBIP_SMS_SENDER', 'evoory'),
        // E.164 WhatsApp business number associated with the Infobip
        // account. Optional — leave empty until WhatsApp is approved, and
        // the WhatsApp channel will return a clear "not configured" error.
        'whatsapp_sender' => env('INFOBIP_WHATSAPP_SENDER'),
        // Name of the WhatsApp authentication template registered on the
        // Infobip account. Setting this switches the WhatsApp OTP send
        // from free-form text (only allowed inside the 24-hour customer
        // window) to a template message, which is delivered to any
        // recipient without prior opt-in — the canonical OTP path.
        'whatsapp_template_name' => env('INFOBIP_WHATSAPP_TEMPLATE_NAME'),
        'whatsapp_template_language' => env('INFOBIP_WHATSAPP_TEMPLATE_LANGUAGE', 'en'),
        // Most Meta-authentication templates include a "Copy code" URL
        // button whose parameter is the same OTP. Disable when your
        // template has only a body placeholder and no buttons.
        'whatsapp_template_has_button' => env('INFOBIP_WHATSAPP_TEMPLATE_HAS_BUTTON', true),
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
