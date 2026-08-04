<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

if (!function_exists('asset_disk')) {
    /**
     * Get the appropriate asset disk based on environment
     */
    function asset_disk() {
        if (config('assets.auto_detect')) {
            $environment = config('app.env');
            $diskMapping = config('assets.disk_mapping');
            
            return $diskMapping[$environment] ?? config('assets.default_disk');
        }
        
        return config('assets.default_disk');
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate asset URL using Laravel filesystem disks
     */
    function asset_url($path) {
        $disk = asset_disk();
        
        // Try to get URL from the configured disk
        try {
            $storage = Storage::disk($disk);
            
            // For external disk, always return the external URL if configured
            // This allows local development to use external assets without file existence checks
            if ($disk === 'assets_external' && config('filesystems.disks.assets_external.url')) {
                return $storage->url($path);
            }
            
            // For other disks, check if file exists first
            if ($storage->exists($path)) {
                return $storage->url($path);
            }
            
            // Fallback to alternative disk if enabled
            if (config('assets.fallback.enabled')) {
                $fallbackDisk = config('assets.fallback.disk');
                
                // If fallback is also external, just return the URL
                if ($fallbackDisk === 'assets_external') {
                    return Storage::disk($fallbackDisk)->url($path);
                }
                
                $fallbackStorage = Storage::disk($fallbackDisk);
                if ($fallbackStorage->exists($path)) {
                    return $fallbackStorage->url($path);
                }
            }
            
            // Last resort: return standard asset URL with Laravel's default behavior
            return app('url')->asset($path);
            
        } catch (Exception $e) {
            // Log error and fallback
            logger()->warning("Asset disk error: " . $e->getMessage());
            return app('url')->asset($path);
        }
    }
}

/**
 * Our custom asset function that handles external assets
 * Since Laravel's asset() is already loaded, we'll use this as the main function
 */
function external_asset($path, $secure = null)
{
    // Handle empty or null paths - return empty string or a placeholder
    if (empty($path)) {
        return '';
    }
    
    // If it's already a full URL, return as-is
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    
    // Check if this looks like an asset file (has extension)
    $pathInfo = pathinfo($path);
    $hasExtension = !empty($pathInfo['extension']);
    
    // If it has an extension and matches our asset types, use our disk system
    if ($hasExtension) {
        $assetExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'eot', 'ico'];
        if (in_array(strtolower($pathInfo['extension']), $assetExtensions)) {
            return asset_url($path);
        }
    }
    
    // For non-asset files, use Laravel's default behavior
    return app('url')->asset($path, $secure);
}

// Create an alias for backward compatibility
if (!function_exists('smart_asset')) {
    function smart_asset($path, $secure = null) {
        return external_asset($path, $secure);
    }
}

if (!function_exists('should_hide_us_profile_pics')) {
    /**
     * Resolve once per request: is the "hide profile pics from US visitors"
     * toggle enabled AND does the incoming request originate from the US?
     *
     * Country comes from Cloudflare's CF-IPCountry header (site is behind CF).
     * The admin toggle is cached for 5 minutes so we're not hitting the DB
     * on every image URL — Setting::update() calls Cache::forget() so
     * changes still propagate promptly.
     */
    function should_hide_us_profile_pics(): bool {
        static $resolved = null;
        if ($resolved !== null) {
            return $resolved;
        }

        try {
            $enabled = \Illuminate\Support\Facades\Cache::remember(
                'hide_us_profile_pics',
                300,
                function () {
                    $setting = \App\Models\Setting::first();
                    return (bool) ($setting->hide_us_profile_pics ?? false);
                }
            );
        } catch (\Throwable $e) {
            // DB not reachable during e.g. artisan calls — fail open (don't hide).
            return $resolved = false;
        }

        if (! $enabled) {
            return $resolved = false;
        }

        $country = request()->header('CF-IPCountry');
        return $resolved = ($country && strtoupper($country) === 'US');
    }
}

if (!function_exists('webp_asset')) {
    /**
     * Get WebP version of image if available, otherwise return original
     * Usage: webp_asset('userimages/1/2/image.jpg')
     * Returns: URL to image.webp from assets CDN
     *
     * When the "hide US profile pics" admin toggle is on AND the request
     * comes from the US (via Cloudflare's CF-IPCountry header), userimages
     * paths are swapped for /assets/images/defaultprofile.png. Non-userimages
     * paths (site chrome, icons, etc.) are unaffected.
     */
    function webp_asset($path, $secure = null) {
        static $assetsBase = null;
        if ($assetsBase === null) {
            $assetsBase = rtrim(config('filesystems.disks.assets_external.url', 'https://assets.evoory.com'), '/');
        }

        if (strncmp($path, 'userimages/', 11) === 0) {
            if (should_hide_us_profile_pics()) {
                return app('url')->asset('assets/images/no-photo.webp');
            }

            $dot = strrpos($path, '.');
            if ($dot !== false) {
                $ext = strtolower(substr($path, $dot + 1));
                if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
                    return $assetsBase . '/' . substr($path, 0, $dot) . '.webp';
                }
            }
            return $assetsBase . '/' . $path;
        }

        return external_asset($path, $secure);
    }
}

if (!function_exists('versioned_asset_url')) {
    /**
     * Generate versioned asset URL for cache busting
     */
    function versioned_asset_url($path) {
        $url = asset_url($path);
        
        if (config('assets.versioning.enabled')) {
            $version = config('assets.versioning.version');
            $param = config('assets.versioning.query_param');
            
            $separator = strpos($url, '?') !== false ? '&' : '?';
            return $url . $separator . $param . '=' . $version;
        }
        
        return $url;
    }
}

if (!function_exists('cdn_asset')) {
    /**
     * Get CDN asset URL (alias for asset_url)
     */
    function cdn_asset($path) {
        return asset_url($path);
    }
}

if (!function_exists('local_or_cdn_asset')) {
    /**
     * Smart asset URL - uses disk configuration
     */
    function local_or_cdn_asset($path) {
        return asset_url($path);
    }
}

if (!function_exists('versioned_asset')) {
    /**
     * Versioned asset (alias for versioned_asset_url)
     */
    function versioned_asset($path) {
        return versioned_asset_url($path);
    }
}

if (!function_exists('copy_to_cdn')) {
    /**
     * Copy asset from local to CDN disk
     */
    function copy_to_cdn($path) {
        $localDisk = Storage::disk('assets_local');
        $cdnDisk = Storage::disk(asset_disk());
        
        if ($localDisk->exists($path) && $localDisk !== $cdnDisk) {
            $content = $localDisk->get($path);
            return $cdnDisk->put($path, $content);
        }
        
        return false;
    }
}

if (!function_exists('sync_assets_to_cdn')) {
    /**
     * Sync all assets from local to CDN disk
     */
    function sync_assets_to_cdn() {
        $localDisk = Storage::disk('assets_local');
        $cdnDisk = Storage::disk(asset_disk());
        
        if ($localDisk === $cdnDisk) {
            return true; // Same disk, no sync needed
        }
        
        $files = $localDisk->allFiles();
        $synced = 0;
        
        foreach ($files as $file) {
            $content = $localDisk->get($file);
            if ($cdnDisk->put($file, $content)) {
                $synced++;
            }
        }
        
        return $synced;
    }
}
