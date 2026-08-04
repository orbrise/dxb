<?php

namespace App\Console\Commands;

use App\Models\MassageRepublicProfile;
use App\Services\MassageRepublicImporter;
use App\Services\MassageRepublicPhoneWorker;
use App\Services\MassageRepublicScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScrapeMassageRepublicProfiles extends Command
{
    protected $signature = 'scrape:massagerepublic
                            {--city= : MR city slug, e.g. dubai, delhi, lahore}
                            {--limit=50 : Maximum number of profiles to fetch}
                            {--no-import : Scrape only; skip writing to live tables}
                            {--no-phone : Skip the Playwright phone-reveal step}
                            {--require-phone : Only import profiles whose phone reveal succeeded — skip the rest}';

    protected $description = 'Scrape massagerepublic.com profiles for a given city and import them into the live users_profiles tables.';

    public function handle(MassageRepublicImporter $importer, MassageRepublicPhoneWorker $phoneWorker)
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

        $this->info("Starting Evoory scraper for {$citySlug}...");
        $this->line("Logging in as {$username}");

        $scraper = new MassageRepublicScraper($username, $password);

        // Skip cards whose external_id is already imported so re-runs walk
        // further down the listing to find fresh profiles instead of hitting
        // the same top-N cards every time. Only applies to the live import
        // path — with --no-import we want to re-scrape everything.
        $skipChecker = null;
        if ($importEnabled) {
            $skipChecker = function (string $externalId): bool {
                return MassageRepublicProfile::where('external_id', $externalId)
                    ->whereNotNull('imported_user_id')
                    ->exists();
            };
        }

        try {
            $profiles = $scraper->scrape($limit, $citySlug, $skipChecker);
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

        // Batch-reveal all phones in one Chromium session before we start
        // importing. Without this the importer spawns a fresh Node/browser/
        // login per profile, which MR/Cloudflare throttles within a few
        // requests and every phone comes back NULL.
        if ($importEnabled && ! $this->option('no-phone')) {
            $slugsToReveal = [];
            foreach ($profiles as $p) {
                $ext = $p['external_id'] ?? null;
                if (! $ext) continue;
                $existing = \App\Models\MassageRepublicProfile::where('external_id', $ext)->first();
                if ($existing && $existing->imported_user_id) continue;
                $slugsToReveal[] = $ext;
            }
            if (! empty($slugsToReveal)) {
                $this->info('Batch-revealing phones for ' . count($slugsToReveal) . ' profile(s) in one session...');
                $phoneWorker->preheat($slugsToReveal, '/female-escorts-in-' . ltrim($citySlug, '/'));
            }
        }

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

            // --require-phone: bail before touching users_profiles if the
            // batch preheat failed to reveal a phone for this slug. Prevents
            // ever inserting a row whose phone column would be NULL.
            if ($this->option('require-phone') && ! $this->option('no-phone')) {
                $cachedPhone = $phoneWorker->revealOne($row->external_id, '/female-escorts-in-' . ltrim($citySlug, '/'));
                if (empty($cachedPhone)) {
                    $reason = $phoneWorker->getLastError($row->external_id) ?: 'no phone returned';
                    $this->line("  - {$row->external_id}: skipped (--require-phone; reveal failed: {$reason})");
                    continue;
                }
            }

            if ($cityId === null) {
                $cityId = $importer->resolveCityId($profileData['city'] ?? null, $citySlug);
                if (! $cityId) {
                    $this->warn("Could not resolve cities.id for '{$citySlug}' — profiles will be imported with city=NULL. Add the city to the `cities` table or check the slug.");
                }
            }

            $worker = $this->option('no-phone') ? null : $phoneWorker;

            try {
                $result = $importer->import($row, $citySlug, $cityId, $worker);
                if ($result === null) {
                    continue;
                }
                $saved++;
                $finalPhone = \App\Models\UsersProfile::find($result['profile_id'])?->phone;
                if ($finalPhone) {
                    $phoneNote = ", phone={$finalPhone}";
                } else {
                    $reason = $result['phone_error'] ?? 'unknown';
                    $phoneNote = ", phone=NULL ({$reason})";
                }
                $this->line("  + imported {$row->external_id} → user #{$result['user_id']}, profile #{$result['profile_id']}, {$result['images']} image(s){$phoneNote}");
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
