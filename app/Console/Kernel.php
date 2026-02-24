<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('auctions:end')->hourly();
        
        // Check for expired promo packages and unfeatured profiles
        $schedule->command('promos:check-expired')->hourly();
        
        // Check for expired packages and reset to free
        $schedule->command('packages:check-expired --fix-missing')->hourly();
        
        // Archive system commands
        $schedule->command('profiles:send-archive-warnings')->daily()->at('09:00');
        $schedule->command('profiles:auto-archive')->daily()->at('02:00');
        // Auto-delete commands (runs daily at 03:00)
        $schedule->command('profiles:auto-delete')->daily()->at('03:00');
        
        // Send weekly newsletter every Monday at 10:00 AM
        $schedule->command('newsletter:send-weekly')->weekly()->mondays()->at('10:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
