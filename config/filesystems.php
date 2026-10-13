<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // CDN Assets Disk - Local Development
        'assets_local' => [
            'driver' => 'local',
            'root' => public_path('assets'),
            'url' => env('APP_URL').'/assets',
            'visibility' => 'public',
            'throw' => false,
        ],

        // CDN Assets Disk - Production/External Domain
        'assets_cdn' => [
            'driver' => 'local',
            'root' => env('ASSETS_ROOT_PATH', public_path('assets')), // Can be external path
            'url' => env('ASSETS_CDN_URL', env('APP_URL').'/assets'),
            'visibility' => 'public',
            'throw' => false,
        ],

        // External Asset Storage (for production CDN folder)
        'assets_external' => [
            'driver' => 'local',
            'root' => env('EXTERNAL_ASSETS_PATH', 'C:\assets_storage'), // External folder
            'url' => env('ASSETS_EXTERNAL_URL', 'https://assets.evoory.com'),
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        /*
         * Cloudflare R2 — S3-compatible object storage used for chat video
         * uploads (and anything else we don't want chewing up local disk).
         * Set these in .env:
         *   R2_ACCESS_KEY_ID         access-key from R2 → API token
         *   R2_SECRET_ACCESS_KEY     matching secret
         *   R2_BUCKET                bucket name
         *   R2_ENDPOINT              https://<account>.r2.cloudflarestorage.com
         *   R2_PUBLIC_URL            optional — public bucket URL (r2.dev
         *                            or custom domain). If set, serve()
         *                            redirects to it; otherwise it falls
         *                            back to a presigned URL.
         *
         * region=auto is required by R2 (no AWS regions); path_style must
         * be true because R2 does not do subdomain-virtual-host style.
         */
        'r2' => [
            'driver'                   => 's3',
            'key'                      => env('R2_ACCESS_KEY_ID'),
            'secret'                   => env('R2_SECRET_ACCESS_KEY'),
            'region'                   => 'auto',
            'bucket'                   => env('R2_BUCKET'),
            'endpoint'                 => env('R2_ENDPOINT'),
            'url'                      => env('R2_PUBLIC_URL'),
            'use_path_style_endpoint'  => true,
            'throw'                    => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
