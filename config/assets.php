<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Asset Disk
    |--------------------------------------------------------------------------
    |
    | This is the default asset disk that will be used by the asset helpers.
    | You can switch between 'assets_local', 'assets_cdn', 'assets_external'
    | based on your environment.
    |
    */

    'default_disk' => env('ASSET_DISK', 'assets_external'),

    /*
    |--------------------------------------------------------------------------
    | Asset Environment Detection
    |--------------------------------------------------------------------------
    |
    | Automatically detect which asset disk to use based on environment
    |
    */

    'auto_detect' => env('ASSET_AUTO_DETECT', true),

    /*
    |--------------------------------------------------------------------------
    | Asset Disk Mapping
    |--------------------------------------------------------------------------
    |
    | Map environments to specific asset disks - ALL use external now
    |
    */

    'disk_mapping' => [
        'local' => 'assets_external',
        'development' => 'assets_external', 
        'staging' => 'assets_external',
        'production' => 'assets_external',
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Versioning
    |--------------------------------------------------------------------------
    |
    | Enable asset versioning for cache busting
    |
    */

    'versioning' => [
        'enabled' => env('ASSET_VERSIONING_ENABLED', true),
        'version' => env('ASSET_VERSION', '1.0.0'),
        'query_param' => env('ASSET_VERSION_PARAM', 'v'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Cache Control
    |--------------------------------------------------------------------------
    |
    | Cache settings for different asset types
    |
    */

    'cache' => [
        'css' => [
            'max_age' => env('ASSET_CSS_CACHE', 31536000), // 1 year
            'extensions' => ['css'],
        ],
        'js' => [
            'max_age' => env('ASSET_JS_CACHE', 31536000), // 1 year
            'extensions' => ['js'],
        ],
        'images' => [
            'max_age' => env('ASSET_IMAGE_CACHE', 31536000), // 1 year
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
        ],
        'fonts' => [
            'max_age' => env('ASSET_FONT_CACHE', 31536000), // 1 year
            'extensions' => ['woff', 'woff2', 'ttf', 'eot'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Fallback settings when primary asset disk is unavailable
    |
    */

    'fallback' => [
        'enabled' => env('ASSET_FALLBACK_ENABLED', true),
        'disk' => env('ASSET_FALLBACK_DISK', 'assets_local'),
        'timeout' => env('ASSET_FALLBACK_TIMEOUT', 3), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | Settings specific to development environment
    |
    */

    'development' => [
        'hot_reload' => env('ASSET_HOT_RELOAD', true),
        'debug_mode' => env('ASSET_DEBUG', false),
        'disable_cache' => env('ASSET_DISABLE_CACHE', true),
    ],

];
