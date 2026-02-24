<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Blog subdomain routes
            // Matches: blog.massagerepublic.com.co, blog.ae.massagerepublic.com.co, blog.localhost, etc.
            Route::middleware('web')
                ->domain($this->getBlogDomain())
                ->group(base_path('routes/blog.php'));

            // Main domain routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Get the blog subdomain based on environment
     */
    protected function getBlogDomain(): string
    {
        // Get the main domain from config or environment
        $appUrl = config('app.url');
        $parsed = parse_url($appUrl);
        $host = $parsed['host'] ?? 'localhost';
        
        // For local development
        if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
            return 'blog.localhost';
        }
        
        // For production - prepend 'blog.' to the main domain
        // e.g., ae.massagerepublic.com.co -> blog.massagerepublic.com.co
        // Remove any subdomain like 'ae.' and add 'blog.'
        $parts = explode('.', $host);
        
        // If we have a subdomain like 'ae', remove it
        if (count($parts) > 3) {
            array_shift($parts);
        }
        
        return 'blog.' . implode('.', $parts);
    }
}
