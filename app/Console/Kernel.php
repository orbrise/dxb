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

        // MassageRepublic scraper — one daily run that iterates cities
        // SEQUENTIALLY (next city starts only when the previous one
        // finishes). Runs after the 02:00 auto-archive and 03:00
        // auto-delete windows above. withoutOverlapping's 240-min lock is
        // generous because 5 cities × Playwright phone reveal can easily
        // take an hour or more. The scraper itself skips already-imported
        // profiles at the card level (see ScrapeMassageRepublicProfiles +
        // MassageRepublicScraper::scrape), so --limit=20 walks 20 fresh
        // profiles per city each day.
        //
        // Cities live in ScrapeAllMassageRepublicProfiles::$defaultCities.
        // Override at run time with --cities=istanbul,london,batumi.
        $schedule->command('scrape:massagerepublic:all --limit=20 --require-phone')
            ->everyTwoHours()
            ->withoutOverlapping(240)
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/scraper-all.log'));

        // ivysociete scraper — Sydney only for now. Add more cities by
        // registering additional daily commands (e.g. --city=london) once
        // the Sydney pipeline is proven stable in production. 60-minute
        // withoutOverlapping lock is generous; a --limit=50 run typically
        // finishes in under 15 minutes (no Playwright, no phone reveal).
        $schedule->command('scrape:ivysociete --city=sydney --limit=50')
            ->dailyAt('03:00')
            ->withoutOverlapping(60)
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/scrape-ivysociete.log'));

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
