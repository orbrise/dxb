<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertAssetCalls extends Command
{
    protected $signature = 'assets:convert-calls {--dry-run : Show what would be changed without making changes} {--function=smart_asset : Function to replace asset() with}';
    
    protected $description = 'Convert asset() calls to smart_asset() calls in Blade templates';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $replacementFunction = $this->option('function');
        
        $this->info("🔄 Converting asset() calls to {$replacementFunction}()");
        $this->info($dryRun ? '🔍 DRY RUN MODE - No files will be modified' : '✏️ LIVE MODE - Files will be modified');
        $this->newLine();
        
        // Find all Blade files
        $bladeFiles = $this->findBladeFiles();
        $totalChanges = 0;
        
        foreach ($bladeFiles as $file) {
            $changes = $this->processFile($file, $replacementFunction, $dryRun);
            if ($changes > 0) {
                $this->line("📁 {$file}: {$changes} changes" . ($dryRun ? ' (would be made)' : ' (made)'));
                $totalChanges += $changes;
            }
        }
        
        $this->newLine();
        if ($totalChanges > 0) {
            $this->info("✅ Total: {$totalChanges} asset() calls " . ($dryRun ? 'found' : 'converted'));
            
            if ($dryRun) {
                $this->info("Run without --dry-run to make the changes");
            }
        } else {
            $this->info("✅ No asset() calls found that need conversion");
        }
        
        // Show usage examples
        $this->newLine();
        $this->info("📝 Manual conversion examples:");
        $this->line("  Before: {{ asset('assets/css/app.css') }}");
        $this->line("  After:  {{ {$replacementFunction}('assets/css/app.css') }}");
        $this->newLine();
        $this->line("  Before: <link href=\"{{ asset('assets/css/bootstrap.css') }}\" rel=\"stylesheet\">");
        $this->line("  After:  <link href=\"{{ {$replacementFunction}('assets/css/bootstrap.css') }}\" rel=\"stylesheet\">");

        return 0;
    }
    
    private function findBladeFiles()
    {
        $files = [];
        
        // Search in resources/views
        $viewsPath = resource_path('views');
        if (is_dir($viewsPath)) {
            $files = array_merge($files, $this->getBladeFilesInDirectory($viewsPath));
        }
        
        // Search in any other blade files
        $publicFiles = $this->getBladeFilesInDirectory(base_path());
        
        return array_unique($files);
    }
    
    private function getBladeFilesInDirectory($directory)
    {
        $files = [];
        
        if (!is_dir($directory)) {
            return $files;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && 
                (strpos($file->getFilename(), '.blade.php') !== false)) {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }
    
    private function processFile($filePath, $replacementFunction, $dryRun)
    {
        $content = File::get($filePath);
        $originalContent = $content;
        
        // Pattern to match asset() calls in Blade templates
        // Matches: {{ asset('...') }}, {!! asset('...') !!}, @if(asset('...')), etc.
        $patterns = [
            // {{ asset('path') }} or {!! asset('path') !!}
            '/(\{\{\s*|\{\!\!\s*)asset\s*\(\s*([^)]+)\s*\)(\s*\}\}|\s*\!\!\})/i',
            // asset('path') in other contexts like @if, href, src attributes
            '/(\W)asset\s*\(\s*([^)]+)\s*\)/i'
        ];
        
        $changeCount = 0;
        
        foreach ($patterns as $pattern) {
            $content = preg_replace_callback($pattern, function ($matches) use ($replacementFunction, &$changeCount) {
                $changeCount++;
                
                if (isset($matches[3])) {
                    // Blade directive format
                    return $matches[1] . $replacementFunction . '(' . $matches[2] . ')' . $matches[3];
                } else {
                    // Other contexts
                    return $matches[1] . $replacementFunction . '(' . $matches[2] . ')';
                }
            }, $content);
        }
        
        if (!$dryRun && $content !== $originalContent) {
            File::put($filePath, $content);
        }
        
        return $changeCount;
    }
}
