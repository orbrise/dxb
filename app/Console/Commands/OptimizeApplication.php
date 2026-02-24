<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OptimizeApplication extends Command
{
    protected $signature = 'app:optimize {--production : Run production optimizations}';
    protected $description = 'Optimize the application for better performance';

    public function handle()
    {
        $this->info('Starting application optimization...');

        // Clear all caches first
        $this->clearCaches();

        // Optimize configuration
        $this->optimizeConfig();

        // Optimize routes
        $this->optimizeRoutes();

        // Optimize views
        $this->optimizeViews();

        // Database optimizations
        $this->optimizeDatabase();

        // Production-specific optimizations
        if ($this->option('production')) {
            $this->productionOptimizations();
        }

        $this->info('Application optimization completed!');
    }

    private function clearCaches()
    {
        $this->info('Clearing caches...');

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        $this->info('✓ Caches cleared');
    }

    private function optimizeConfig()
    {
        $this->info('Optimizing configuration...');
        
        Artisan::call('config:cache');
        
        $this->info('✓ Configuration cached');
    }

    private function optimizeRoutes()
    {
        $this->info('Optimizing routes...');
        
        Artisan::call('route:cache');
        
        $this->info('✓ Routes cached');
    }

    private function optimizeViews()
    {
        $this->info('Optimizing views...');
        
        Artisan::call('view:cache');
        
        $this->info('✓ Views cached');
    }

    private function optimizeDatabase()
    {
        $this->info('Optimizing database...');

        try {
            // Create cache table if it doesn't exist
            if (!DB::getSchemaBuilder()->hasTable('cache')) {
                Artisan::call('cache:table');
                $this->info('✓ Cache table created');
            }

            // Create sessions table if it doesn't exist
            if (!DB::getSchemaBuilder()->hasTable('sessions')) {
                Artisan::call('session:table');
                $this->info('✓ Sessions table created');
            }

            // Analyze tables for better performance
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                DB::statement("ANALYZE TABLE `$tableName`");
            }
            
            $this->info('✓ Database tables analyzed');

        } catch (\Exception $e) {
            $this->warn('Database optimization partially failed: ' . $e->getMessage());
        }
    }

    private function productionOptimizations()
    {
        $this->info('Running production optimizations...');

        // Optimize autoloader
        Artisan::call('optimize');
        
        // Create symlink for storage
        if (!File::exists(public_path('storage'))) {
            Artisan::call('storage:link');
            $this->info('✓ Storage link created');
        }

        // Compile assets if Vite is available
        if (File::exists(base_path('vite.config.js'))) {
            $this->info('Building assets...');
            exec('npm run build', $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->info('✓ Assets built');
            } else {
                $this->warn('Asset build failed. Make sure Node.js and npm are installed.');
            }
        }

        $this->info('✓ Production optimizations completed');
    }
}
