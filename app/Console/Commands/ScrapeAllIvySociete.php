<?php

namespace App\Console\Commands;

use App\Models\ScraperAutoCity;
use Illuminate\Console\Command;

class ScrapeAllIvySociete extends Command
{
    protected $signature = 'scrape:ivysociete:all
                            {--limit=50 : Default per-city limit when no admin override exists}
                            {--cities= : Comma-separated city slugs (overrides the DB list)}';

    protected $description = 'Run scrape:ivysociete for the admin-configured city list sequentially (one city at a time).';

    protected array $defaultCities = ['sydney'];

    public function handle(): int
    {
        $citiesOption = trim((string) $this->option('cities'));
        $limit = (int) $this->option('limit') ?: 50;

        // Same precedence as the MR bulk runner:
        //   1. --cities= override
        //   2. scraper_auto_cities (source=ivysociete, is_active=1)
        //   3. hardcoded fallback so cron never runs empty
        if ($citiesOption !== '') {
            $cityList = collect(array_filter(array_map('trim', explode(',', $citiesOption))))
                ->map(fn($slug) => ['slug' => $slug, 'limit' => $limit])
                ->all();
        } else {
            $rows = ScraperAutoCity::where('source', 'ivysociete')
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

        $this->info(sprintf('Sequential Ivy scrape starting at %s for %d city(ies): %s',
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

            try {
                $exit = $this->call('scrape:ivysociete', ['--city' => $city, '--limit' => $cityLimit]);
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
        $this->info(sprintf('Sequential Ivy scrape finished at %s (some failures: %s)',
            now()->toDateTimeString(),
            $anyFailed ? 'yes' : 'no'
        ));

        return $anyFailed ? self::FAILURE : self::SUCCESS;
    }
}
