<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeAllMassageRepublicProfiles extends Command
{
    protected $signature = 'scrape:massagerepublic:all
                            {--limit=20 : Profiles per city}
                            {--cities= : Comma-separated city slugs (default: istanbul,london,antalya,batumi,beijing)}
                            {--no-phone : Skip the Playwright phone-reveal step}
                            {--require-phone : Only import profiles whose phone reveal succeeded}';

    protected $description = 'Run scrape:massagerepublic for a list of cities sequentially — the next city starts only when the previous one finishes.';

    protected array $defaultCities = ['istanbul', 'london', 'antalya', 'batumi', 'beijing'];

    public function handle(): int
    {
        $citiesOption = trim((string) $this->option('cities'));
        $cities = $citiesOption !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $citiesOption))))
            : $this->defaultCities;

        $limit = (int) $this->option('limit') ?: 20;
        $noPhone = (bool) $this->option('no-phone');
        $requirePhone = (bool) $this->option('require-phone');

        $this->info(sprintf('Sequential MR scrape starting at %s for %d city(ies): %s', now()->toDateTimeString(), count($cities), implode(', ', $cities)));

        $anyFailed = false;
        foreach ($cities as $city) {
            $this->newLine();
            $this->info(str_repeat('=', 60));
            $this->info(sprintf('[%s] Scraping city: %s', now()->toDateTimeString(), $city));
            $this->info(str_repeat('=', 60));

            $args = ['--city' => $city, '--limit' => $limit];
            if ($noPhone) $args['--no-phone'] = true;
            if ($requirePhone) $args['--require-phone'] = true;

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
