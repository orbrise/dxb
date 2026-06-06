<?php

namespace App\Console\Commands;

use App\Models\MassageRepublicProfile;
use App\Services\MassageRepublicImporter;
use App\Services\MassageRepublicScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScrapeMassageRepublicProfiles extends Command
{
    protected $signature = 'scrape:massagerepublic
                            {--city= : MR city slug, e.g. dubai, delhi, lahore}
                            {--limit=50 : Maximum number of profiles to fetch}
                            {--no-import : Scrape only; skip writing to live tables}';

    protected $description = 'Scrape massagerepublic.com profiles for a given city and import them into the live users_profiles tables.';

    public function handle(MassageRepublicImporter $importer)
    {
        $username = config('services.massagrerepublic.username') ?: env('MASSAGE_REPUBLIC_USERNAME');
        $password = config('services.massagrerepublic.password') ?: env('MASSAGE_REPUBLIC_PASSWORD');

        if (! $username || ! $password) {
            $this->error('Missing MASSAGE_REPUBLIC_USERNAME or MASSAGE_REPUBLIC_PASSWORD in .env');
            return Command::FAILURE;
        }

        $citySlug = Str::lower(trim((string) $this->option('city')));
        if ($citySlug === '') {
            $this->error('Pass --city=<slug>, e.g. --city=dubai');
            return Command::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $importEnabled = ! $this->option('no-import');

        $this->info("Starting massage republic scraper for {$citySlug}...");
        $this->line("Logging in as {$username}");

        $scraper = new MassageRepublicScraper($username, $password);

        try {
            $profiles = $scraper->scrape($limit, $citySlug);
        } catch (\Throwable $exception) {
            $this->error('Scraper error: ' . $exception->getMessage());
            return Command::FAILURE;
        }

        if (empty($profiles)) {
            $this->warn('No profiles were scraped. Check the city slug and login credentials.');
            return Command::SUCCESS;
        }

        $saved = 0;
        $cityId = null;

        foreach ($profiles as $profileData) {
            $row = MassageRepublicProfile::updateOrCreate(
                ['external_id' => $profileData['external_id']],
                array_merge($profileData, [
                    'scraped_at' => now(),
                    'source_city' => $citySlug,
                ])
            );

            if (! $importEnabled) {
                $saved++;
                continue;
            }

            if ($row->imported_user_id) {
                $this->line("  - {$row->external_id}: already imported (user #{$row->imported_user_id}), skipping");
                continue;
            }

            if ($cityId === null) {
                $cityId = $importer->resolveCityId($profileData['city'] ?? null, $citySlug);
                if (! $cityId) {
                    $this->warn("Could not resolve cities.id for '{$citySlug}' — profiles will be imported with city=NULL. Add the city to the `cities` table or check the slug.");
                }
            }

            try {
                $result = $importer->import($row, $citySlug, $cityId);
                if ($result === null) {
                    continue;
                }
                $saved++;
                $this->line("  + imported {$row->external_id} → user #{$result['user_id']}, profile #{$result['profile_id']}, {$result['images']} image(s)");
            } catch (\Throwable $e) {
                $this->error("  ! import failed for {$row->external_id}: " . $e->getMessage());
            }
        }

        if ($importEnabled) {
            $this->info("Imported {$saved} new profile(s) into users_profiles for city '{$citySlug}'.");
        } else {
            $this->info("Scraped {$saved} profile(s) into massage_republic_profiles only (import skipped).");
        }

        return Command::SUCCESS;
    }
}
