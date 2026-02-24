<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ManageAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:manage {action : sync|init|status} {--force : Force overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage external assets directory - sync from local, initialize, or check status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'init':
                return $this->initializeExternalAssets();
            case 'sync':
                return $this->syncAssetsToExternal();
            case 'status':
                return $this->checkAssetsStatus();
            default:
                $this->error("Unknown action: $action");
                $this->info('Available actions: init, sync, status');
                return 1;
        }
    }

    private function initializeExternalAssets()
    {
        $externalPath = config('filesystems.disks.assets_external.root');
        
        if (!$externalPath) {
            $this->error('External assets path not configured in EXTERNAL_ASSETS_PATH');
            return 1;
        }

        $this->info("Initializing external assets directory: $externalPath");

        // Create directory if it doesn't exist
        if (!File::exists($externalPath)) {
            File::makeDirectory($externalPath, 0755, true);
            $this->info("✅ Created directory: $externalPath");
        }

        // Create subdirectories
        $subdirs = ['css', 'js', 'images', 'fonts', 'libs'];
        foreach ($subdirs as $dir) {
            $fullPath = $externalPath . DIRECTORY_SEPARATOR . $dir;
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $this->info("✅ Created subdirectory: $dir");
            }
        }

        // Copy assets from local
        return $this->syncAssetsToExternal();
    }

    private function syncAssetsToExternal()
    {
        $localDisk = Storage::disk('assets_local');
        $externalDisk = Storage::disk('assets_external');
        
        if (!$localDisk->exists('')) {
            $this->error('Local assets directory does not exist');
            return 1;
        }

        $this->info('Syncing assets from local to external directory...');
        
        $files = $localDisk->allFiles();
        $synced = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            try {
                $content = $localDisk->get($file);
                
                if ($this->option('force') || !$externalDisk->exists($file)) {
                    $externalDisk->put($file, $content);
                    $synced++;
                }
                
                $progressBar->advance();
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Failed to sync $file: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Synced $synced files to external assets directory");
        
        if ($errors > 0) {
            $this->warn("⚠️  $errors files failed to sync");
        }

        return 0;
    }

    private function checkAssetsStatus()
    {
        $this->info('🔍 Asset Disks Status:');
        $this->newLine();

        // Current configuration - use config directly instead of helper
        $currentDisk = $this->getCurrentAssetDisk();
        $this->info("Current Asset Disk: $currentDisk");
        $this->newLine();

        // Check each disk
        $disks = ['assets_local', 'assets_external', 'assets_cdn'];
        
        foreach ($disks as $diskName) {
            $this->checkDiskStatus($diskName);
            $this->newLine();
        }

        // Asset URLs - use config directly
        $this->info('🔗 Sample Asset URLs:');
        $this->line('CSS: ' . $this->getAssetUrl('css/app3.css'));
        $this->line('JS:  ' . $this->getAssetUrl('js/app.js'));
        $this->line('IMG: ' . $this->getAssetUrl('images/logo.png'));

        return 0;
    }

    private function getCurrentAssetDisk()
    {
        if (config('assets.auto_detect')) {
            $environment = config('app.env');
            $diskMapping = config('assets.disk_mapping', []);
            
            return $diskMapping[$environment] ?? config('assets.default_disk');
        }
        
        return config('assets.default_disk');
    }

    private function getAssetUrl($path)
    {
        $disk = $this->getCurrentAssetDisk();
        
        try {
            $storage = Storage::disk($disk);
            return $storage->url($path);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    private function checkDiskStatus($diskName)
    {
        try {
            $disk = Storage::disk($diskName);
            $config = config("filesystems.disks.$diskName");
            
            $this->info("📁 $diskName:");
            $this->line("   Root: " . ($config['root'] ?? 'Not configured'));
            $this->line("   URL:  " . ($config['url'] ?? 'Not configured'));
            
            if (isset($config['root']) && File::exists($config['root'])) {
                $fileCount = count($disk->allFiles());
                $this->line("   Files: $fileCount");
                $this->line("   Status: ✅ Available");
            } else {
                $this->line("   Status: ❌ Directory not found");
            }
            
        } catch (\Exception $e) {
            $this->line("   Status: ❌ Error - " . $e->getMessage());
        }
    }
}
