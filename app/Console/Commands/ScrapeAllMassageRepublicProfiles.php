<?php

namespace App\Console\Commands;

use App\Models\ScraperAutoCity;
use Illuminate\Console\Command;

class ScrapeAllMassageRepublicProfiles extends Command
{
    protected $signature = 'scrape:massagerepublic:all
                            {--limit=20 : Profiles per city}
                            {--cities= : Comma-separated city slugs (default: istanbul,london,antalya,batumi,beijing)}
                            {--no-phone : Skip the Playwright phone-reveal step}
                            {--require-phone : Only import profiles whose phone reveal succeeded}
                            {--bd-api-key= : Bright Data Web Unlocker API key}
                            {--bd-zone= : Bright Data zone (web_unlocker1)}
                            {--bd-proxy-url= : Bright Data proxy URL (superproxy) }';

    protected $description = 'Run scrape:massagerepublic for a list of cities sequentially — the next city starts only when the previous one finishes.';

    protected array $defaultCities = [
        // Existing rotation
        'istanbul', 'london', 'antalya', 'batumi', 'beijing',
        // Asia
        'bangkok', 'new-delhi', 'mumbai', 'manila', 'singapore',
        'pune', 'chennai', 'kolkata', 'gurgaon', 'noida',
        'hyderabad', 'bangalore', 'pattaya', 'kuala-lumpur',
        'jakarta', 'hong-kong',
        // Europe
        'paris',
        // Middle East (MR slugs are hyphenated lowercase; drop "Al " prefix
        // on Manama since MR indexes it as just "manama")
        'dubai', 'abu-dhabi', 'al-manama', 'doha', 'kuwait', 'muscat',
        // Caucasus
        'baku',
        // Africa
        'nairobi',
        // Oceania
        'sydney',
        // North America
        'toronto',
    ];

    public function handle(): int
    {
        // Apply Bright Data options to environment so child command sees them
        $bdApiKey = trim((string) $this->option('bd-api-key'));
        $bdZone = trim((string) $this->option('bd-zone'));
        $bdProxy = trim((string) $this->option('bd-proxy-url'));

        if ($bdApiKey !== '') {
            putenv('MASSAGE_REPUBLIC_BRIGHTDATA_API_KEY=' . $bdApiKey);
            $_ENV['MASSAGE_REPUBLIC_BRIGHTDATA_API_KEY'] = $bdApiKey;
            $_SERVER['MASSAGE_REPUBLIC_BRIGHTDATA_API_KEY'] = $bdApiKey;
            $this->info('Using Bright Data API key from CLI option.');
        }
        if ($bdZone !== '') {
            putenv('MASSAGE_REPUBLIC_BRIGHTDATA_ZONE=' . $bdZone);
            $_ENV['MASSAGE_REPUBLIC_BRIGHTDATA_ZONE'] = $bdZone;
            $_SERVER['MASSAGE_REPUBLIC_BRIGHTDATA_ZONE'] = $bdZone;
            $this->info('Using Bright Data zone: ' . $bdZone);
        }
        if ($bdProxy !== '') {
            putenv('MASSAGE_REPUBLIC_PROXY_URL=' . $bdProxy);
            $_ENV['MASSAGE_REPUBLIC_PROXY_URL'] = $bdProxy;
            $_SERVER['MASSAGE_REPUBLIC_PROXY_URL'] = $bdProxy;
            $this->info('Using Bright Data proxy URL from CLI option.');
        }
        $citiesOption = trim((string) $this->option('cities'));
        $limit = (int) $this->option('limit') ?: 10;
        $noPhone = (bool) $this->option('no-phone');
        $requirePhone = (bool) $this->option('require-phone');

        // Build the (city, per-city limit) list.
        //   1. --cities= override always wins (comma-separated slugs, uses --limit for all).
        //   2. Otherwise pull from scraper_auto_cities (admin-managed via /admin/scrapers/auto).
        //   3. Fallback to the hardcoded defaults so the cron never runs empty
        //      even before an admin has set anything up.
        if ($citiesOption !== '') {
            $cityList = collect(array_filter(array_map('trim', explode(',', $citiesOption))))
                ->map(fn($slug) => ['slug' => $slug, 'limit' => $limit])
                ->all();
        } else {
            $rows = ScraperAutoCity::where('source', 'massagerepublic')
                ->where('is_active', true)
                ->orderBy('city_slug')
                ->get(['city_slug', 'limit_per_run']);
            if ($rows->isNotEmpty()) {
                $cityList = $rows->map(fn($r) => ['slug' => $r->city_slug, 'limit' => (int) $r->limit_per_run])->all();
            } else {
                $cityList = collect($this->defaultCities)
                    ->map(fn($slug) => ['slug' => $slug, 'limit' => $limit])
                    ->all();
            }
        }

        $this->info(sprintf('Sequential MR scrape starting at %s for %d city(ies): %s',
            now()->toDateTimeString(),
            count($cityList),
            implode(', ', array_column($cityList, 'slug'))
        ));

        $anyFailed = false;
        foreach ($cityList as $entry) {
            $city = $entry['slug'];
            $cityLimit = $entry['limit'];

            $this->newLine();
            $this->info(str_repeat('=', 60));
            $this->info(sprintf('[%s] Scraping city: %s (limit=%d)', now()->toDateTimeString(), $city, $cityLimit));
            $this->info(str_repeat('=', 60));

            $args = ['--city' => $city, '--limit' => $cityLimit];
            if ($noPhone) $args['--no-phone'] = true;
            if ($requirePhone) $args['--require-phone'] = true;
            if ($bdApiKey !== '') $args['--bd-api-key'] = $bdApiKey;
            if ($bdZone !== '') $args['--bd-zone'] = $bdZone;
            if ($bdProxy !== '') $args['--bd-proxy-url'] = $bdProxy;

            try {
                $exit = $this->call('scrape:massagerepublic', $args);
            } catch (\Throwable $e) {
                $this->error(sprintf('[%s] %s: exception — %s', now()->toDateTimeString(), $city, $e->getMessage()));
                $anyFailed = true;
                continue;
            }

            if ($exit !== 0) {
                $this->warn(sprintf('[%s] %s: exited with code %d', now()->toDateTimeString(), $city, $exit));
                $anyFailed = true;
            } else {
                $this->info(sprintf('[%s] %s: done', now()->toDateTimeString(), $city));
            }
        }

        $this->newLine();
        $this->info(sprintf('Sequential MR scrape finished at %s (some failures: %s)', now()->toDateTimeString(), $anyFailed ? 'yes' : 'no'));

        return $anyFailed ? self::FAILURE : self::SUCCESS;
    }
}
