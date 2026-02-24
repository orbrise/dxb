<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class AssetService
{
    /**
     * Get the appropriate asset disk based on environment
     */
    public static function disk()
    {
        if (config('assets.auto_detect')) {
            $environment = config('app.env');
            $diskMapping = config('assets.disk_mapping', []);
            
            return $diskMapping[$environment] ?? config('assets.default_disk');
        }
        
        return config('assets.default_disk', 'assets_local');
    }

    /**
     * Generate asset URL using Laravel filesystem disks
     */
    public static function url($path)
    {
        $disk = self::disk();
        
        try {
            $storage = Storage::disk($disk);
            
            // Check if file exists
            if ($storage->exists($path)) {
                return $storage->url($path);
            }
            
            // Fallback to alternative disk if enabled
            if (config('assets.fallback.enabled')) {
                $fallbackDisk = config('assets.fallback.disk');
                $fallbackStorage = Storage::disk($fallbackDisk);
                
                if ($fallbackStorage->exists($path)) {
                    return $fallbackStorage->url($path);
                }
            }
            
            // Last resort: return standard asset URL
            return asset($path);
            
        } catch (\Exception $e) {
            // Log error and fallback
            logger()->warning("Asset disk error: " . $e->getMessage());
            return asset($path);
        }
    }

    /**
     * Generate versioned asset URL for cache busting
     */
    public static function versionedUrl($path)
    {
        $url = self::url($path);
        
        if (config('assets.versioning.enabled')) {
            $version = config('assets.versioning.version');
            $param = config('assets.versioning.query_param', 'v');
            
            $separator = strpos($url, '?') !== false ? '&' : '?';
            return $url . $separator . $param . '=' . $version;
        }
        
        return $url;
    }

    /**
     * Get CDN asset URL (alias for url)
     */
    public static function cdn($path)
    {
        return self::url($path);
    }

    /**
     * Smart asset URL - uses disk configuration
     */
    public static function smartUrl($path)
    {
        return self::url($path);
    }

    /**
     * Copy asset from local to CDN disk
     */
    public static function copyToCdn($path)
    {
        $localDisk = Storage::disk('assets_local');
        $cdnDisk = Storage::disk(self::disk());
        
        if ($localDisk->exists($path) && $localDisk !== $cdnDisk) {
            $content = $localDisk->get($path);
            return $cdnDisk->put($path, $content);
        }
        
        return false;
    }

    /**
     * Sync all assets from local to CDN disk
     */
    public static function syncToCdn()
    {
        $localDisk = Storage::disk('assets_local');
        $cdnDisk = Storage::disk(self::disk());
        
        if ($localDisk === $cdnDisk) {
            return true; // Same disk, no sync needed
        }
        
        $files = $localDisk->allFiles();
        $synced = 0;
        
        foreach ($files as $file) {
            try {
                $content = $localDisk->get($file);
                if ($cdnDisk->put($file, $content)) {
                    $synced++;
                }
            } catch (\Exception $e) {
                logger()->warning("Failed to sync asset: $file - " . $e->getMessage());
            }
        }
        
        return $synced;
    }
}
