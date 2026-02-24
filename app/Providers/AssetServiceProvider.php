<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;

class AssetServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Override the asset URL generation at the application level
        $this->app->singleton('asset.override', function () {
            return true;
        });
    }

    public function boot()
    {
        // Register custom Blade directives for external assets
        Blade::directive('external_asset', function ($expression) {
            return "<?php echo external_asset($expression); ?>";
        });
        
        Blade::directive('smart_asset', function ($expression) {
            return "<?php echo smart_asset($expression); ?>";
        });
        
        // Override Laravel's global asset helper
        if (!defined('LARAVEL_ASSET_OVERRIDE_REGISTERED')) {
            define('LARAVEL_ASSET_OVERRIDE_REGISTERED', true);
            $this->overrideAssetHelper();
        }
    }
    
    /**
     * Override Laravel's asset helper function
     */
    private function overrideAssetHelper()
    {
        // Since we can't redefine the asset() function directly, 
        // we'll use a different approach by extending the URL facade
        URL::macro('customAsset', function ($path, $secure = null) {
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
        });
    }
}
