<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Geo Redirection Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file defines the country-to-domain mapping for the
    | geo-based redirection system. When a user visits from a specific country,
    | they will be redirected to the corresponding domain.
    |
    */

    // Enable or disable geo redirection
    'enabled' => env('GEO_REDIRECT_ENABLED', true),

    // Main/fallback domain when country domain doesn't exist
    'main_domain' => env('GEO_MAIN_DOMAIN', 'massagerepublic.com.co'),

    // Use HTTPS for redirects
    'use_https' => env('GEO_USE_HTTPS', true),

    // Cookie name to store user's preferred domain (to avoid redirect loops)
    'cookie_name' => 'geo_redirect_checked',

    // Cookie expiry in minutes (24 hours)
    'cookie_expiry' => 1440,

    // Skip redirection for these paths
    'excluded_paths' => [
        'admin/*',
        'api/*',
        'livewire/*',
        '_debugbar/*',
        'storage/*',
        'sanctum/*',
    ],

    // Skip redirection for these user agents (bots, crawlers)
    'excluded_user_agents' => [
        'googlebot',
        'bingbot',
        'slurp',
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'facebot',
        'ia_archiver',
    ],

    // Allow bots to be redirected (set to true if you want SEO redirects)
    'redirect_bots' => false,

    /*
    |--------------------------------------------------------------------------
    | Country to Domain Mapping
    |--------------------------------------------------------------------------
    |
    | Map ISO 3166-1 alpha-2 country codes to their respective domains.
    | Set 'active' => true for domains that are currently live.
    | The system will only redirect to active domains.
    |
    */
    'domains' => [
        'AE' => [
            'domain' => 'ae.massagerepublic.com.co',
            'active' => true,
            'name' => 'United Arab Emirates',
        ],
        'PK' => [
            'domain' => 'pk.massagerepublic.com.co',
            'active' => true,
            'name' => 'Pakistan',
        ],
        'DE' => [
            'domain' => 'de.massagerepublic.com.co',
            'active' => false, // Set to true when domain is ready
            'name' => 'Germany',
        ],
        'GB' => [
            'domain' => 'gb.massagerepublic.com.co',
            'active' => false,
            'name' => 'United Kingdom',
        ],
        'US' => [
            'domain' => 'us.massagerepublic.com.co',
            'active' => false,
            'name' => 'United States',
        ],
        'IN' => [
            'domain' => 'in.massagerepublic.com.co',
            'active' => false,
            'name' => 'India',
        ],
        'SA' => [
            'domain' => 'sa.massagerepublic.com.co',
            'active' => false,
            'name' => 'Saudi Arabia',
        ],
        'QA' => [
            'domain' => 'qa.massagerepublic.com.co',
            'active' => false,
            'name' => 'Qatar',
        ],
        'KW' => [
            'domain' => 'kw.massagerepublic.com.co',
            'active' => false,
            'name' => 'Kuwait',
        ],
        'BH' => [
            'domain' => 'bh.massagerepublic.com.co',
            'active' => false,
            'name' => 'Bahrain',
        ],
        'OM' => [
            'domain' => 'om.massagerepublic.com.co',
            'active' => false,
            'name' => 'Oman',
        ],
        // Add more countries as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain to Country Mapping (Reverse lookup)
    |--------------------------------------------------------------------------
    |
    | This is auto-generated from the domains array above.
    | Used to detect which country a domain belongs to.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | GeoIP Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the method for detecting user's country.
    | Options: 'ip-api', 'maxmind', 'cloudflare', 'header'
    |
    */
    'geoip_driver' => env('GEOIP_DRIVER', 'ip-api'),

    // IP-API configuration (free, limited to 45 requests per minute)
    'ip_api' => [
        'endpoint' => 'http://ip-api.com/json/',
    ],

    // MaxMind configuration (requires license key)
    'maxmind' => [
        'database_path' => storage_path('app/geoip/GeoLite2-Country.mmdb'),
        'license_key' => env('MAXMIND_LICENSE_KEY'),
    ],

    // If using Cloudflare, the country is passed in headers
    'cloudflare' => [
        'country_header' => 'CF-IPCountry',
    ],

    // Custom header for testing or proxy setups
    'header' => [
        'country_header' => env('GEO_COUNTRY_HEADER', 'X-Country-Code'),
    ],

    // Cache country lookups (in minutes)
    'cache_ttl' => env('GEO_CACHE_TTL', 60),

];
