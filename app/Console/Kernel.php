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

        // MassageRepublic scraper — cities run sequentially 30 min apart.
        // Cities added/removed from $scraperCities are automatically slotted
        // in the rotation without hand-editing times. Start time is chosen
        // to skip the 02:00 auto-archive and 03:00 auto-delete windows above.
        // withoutOverlapping guards against a slow run bleeding into the
        // next slot; the scraper itself skips already-imported profiles at
        // the card level (see ScrapeMassageRepublicProfiles +
        // MassageRepublicScraper::scrape), so --limit=20 daily walks 20
        // fresh profiles each run instead of re-hitting the same top rows.
        $scraperStartTime = '03:30';
        $scraperStepMinutes = 30;
        $scraperCities = ['istanbul', 'london', 'antalya', 'batumi', 'beijing'];
        foreach ($scraperCities as $index => $city) {
            $time = date('H:i', strtotime("{$scraperStartTime} +" . ($index * $scraperStepMinutes) . " minutes"));
            $schedule->command("scrape:massagerepublic --city={$city} --limit=20")
                ->dailyAt($time)
                ->withoutOverlapping(60)
                ->runInBackground()
                ->appendOutputTo(storage_path("logs/scraper-{$city}.log"));
        }

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
