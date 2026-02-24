<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestAssetHelpers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'assets:test-helpers';

    /**
     * The console command description.
     */
    protected $description = 'Test if asset helper functions are loaded';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Asset System:');
        $this->newLine();

        // Test Laravel's built-in asset() function first
        $this->info('🔧 Testing asset() function override:');
        
        // Test different asset types
        $testAssets = [
            'assets/css/bootstrap.min.css' => 'CSS file (should use external)',
            'assets/js/app.js' => 'JS file (should use external)', 
            'assets/images/logo.png' => 'Image file (should use external)',
            'storage/profiles/test.jpg' => 'Storage file (should use local)'
        ];
        
        foreach ($testAssets as $assetPath => $description) {
            try {
                $url = asset($assetPath);
                $this->line("✅ asset('$assetPath')");
                $this->line("   $description");
                $this->line("   Result: $url");
                
                // Check if external domain is used for assets
                if (strpos($assetPath, 'assets/') === 0) {
                    if (strpos($url, 'assets.massagerepublic.co') !== false) {
                        $this->line("   ✅ Correctly routed to external domain");
                    } else {
                        $this->line("   ❌ NOT routed to external domain");
                    }
                }
                
                // Test our custom functions for comparison
                if (strpos($assetPath, 'assets/') === 0) {
                    $smartUrl = smart_asset($assetPath);
                    $externalUrl = external_asset($assetPath);
                    $this->line("   smart_asset(): $smartUrl");
                    $this->line("   external_asset(): $externalUrl");
                    
                    if ($smartUrl === $externalUrl && strpos($smartUrl, 'assets.massagerepublic.co') !== false) {
                        $this->line("   ✅ Custom functions working correctly");
                    }
                }
            } catch (\Exception $e) {
                $this->line("❌ Error with asset('$assetPath'): " . $e->getMessage());
            }
            $this->newLine();
        }

        // Test custom helper functions
        $this->info('🛠 Testing Custom Helper Functions:');
        $functions = [
            'asset_disk',
            'asset_url', 
            'versioned_asset_url',
            'cdn_asset',
            'local_or_cdn_asset',
            'versioned_asset'
        ];

        foreach ($functions as $function) {
            if (function_exists($function)) {
                $this->line("✅ $function - Available");
                
                // Test the function
                try {
                    if ($function === 'asset_disk') {
                        $result = $function();
                    } else {
                        $result = $function('css/app.css');
                    }
                    $this->line("   Result: $result");
                } catch (\Exception $e) {
                    $this->line("   Error: " . $e->getMessage());
                }
            } else {
                $this->line("❌ $function - Not found");
            }
            $this->newLine();
        }

        // Test file loading
        $this->info('📁 File System Check:');
        $helperFile = app_path('asset_helpers.php');
        if (file_exists($helperFile)) {
            $this->line("✅ asset_helpers.php exists at: $helperFile");
        } else {
            $this->line("❌ asset_helpers.php not found at: $helperFile");
        }
        
        // Check if file is in composer autoload
        $composerFile = base_path('composer.json');
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            if (isset($composer['autoload']['files']) && 
                in_array('app/asset_helpers.php', $composer['autoload']['files'])) {
                $this->line("✅ asset_helpers.php is in composer autoload");
            } else {
                $this->line("❌ asset_helpers.php NOT in composer autoload");
                $this->line("   Run: composer dump-autoload");
            }
        }
        $this->newLine();

        // Test configuration
        $this->info('📝 Configuration Status:');
        $this->line('Assets config loaded: ' . (config('assets') ? '✅ Yes' : '❌ No'));
        $this->line('Default disk: ' . config('assets.default_disk', 'Not set'));
        $this->line('External path: ' . config('filesystems.disks.assets_external.root', 'Not set'));
        $this->line('External URL: ' . config('filesystems.disks.assets_external.url', 'Not set'));
        $this->newLine();

        // Provide fix suggestions
        $this->info('🔧 Troubleshooting:');
        $this->line('If custom functions not found:');
        $this->line('  1. Run: composer dump-autoload');
        $this->line('  2. Check composer.json has "app/asset_helpers.php" in autoload.files');
        $this->line('  3. Clear config cache: php artisan config:clear');
        $this->newLine();
        
        $this->line('If asset() not using external domain:');
        $this->line('  1. Ensure asset_helpers.php is loaded (step 1 above)');
        $this->line('  2. Check .env has correct EXTERNAL_ASSETS_* settings');
        $this->line('  3. Verify external directory exists and has files');

        return 0;
    }
}
