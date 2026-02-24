<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class PerformanceServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Prevent lazy loading violations in production
        if (app()->environment('production')) {
            Model::preventLazyLoading();
        }

        // Enable strict mode for better error handling
        Model::preventSilentlyDiscardingAttributes();

        // Optimize pagination
        Paginator::defaultView('pagination::bootstrap-4');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-4');

        // Add global view composer for common data
        View::composer('*', function ($view) {
            // Cache common data here if needed
        });

        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Log slow queries
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 1000) { // Log queries taking more than 1 second
                    logger()->warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time
                    ]);
                }
            });
        }
    }
}
