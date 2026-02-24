<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateServiceSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:update-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all service slugs to be URL-friendly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $services = \App\Models\Service::all();
        $count = 0;
        
        foreach ($services as $service) {
            $oldSlug = $service->slug;
            $service->slug = \Illuminate\Support\Str::slug($service->name);
            $service->save();
            
            if ($oldSlug !== $service->slug) {
                $this->info("Updated: {$service->name} -> {$service->slug}");
                $count++;
            }
        }
        
        $this->info("Updated {$count} service slugs successfully!");
        return 0;
    }
}
