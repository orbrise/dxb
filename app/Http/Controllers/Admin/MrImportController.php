<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MassageRepublicProfile;
use App\Services\MassageRepublicImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives JSON scrapes uploaded from a locally-run
 * `php artisan scrape:massagerepublic:local --upload`.
 *
 * The local scraper does the CF-sensitive work (login, listing fetch,
 * profile fetch, phone reveal) from a residential IP where Cloudflare
 * doesn't challenge it. This endpoint just persists the resulting rows +
 * hands them off to MassageRepublicImporter — no MR interaction happens
 * on the server side.
 *
 * Auth is a plain bearer token from the MR_IMPORT_TOKEN env var. Not
 * routed through Sanctum/session auth because the caller is a headless
 * artisan command with no user context.
 */
class MrImportController extends Controller
{
    public function import(Request $request, MassageRepublicImporter $importer): JsonResponse
    {
        $configuredToken = trim((string) env('MR_IMPORT_TOKEN', ''));
        if ($configuredToken === '') {
            // Fail-closed: no token env → nobody can import. Prevents accidental
            // exposure if the route gets deployed without configuration.
            return response()->json(['error' => 'import endpoint not configured (set MR_IMPORT_TOKEN)'], 503);
        }

        $bearer = $request->bearerToken();
        if (! $bearer || ! hash_equals($configuredToken, $bearer)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $citySlug = trim((string) $request->input('city_slug'));
        $profiles = $request->input('profiles');

        if ($citySlug === '' || ! is_array($profiles)) {
            return response()->json(['error' => 'expected {city_slug: string, profiles: array}'], 422);
        }

        // Resolve cities.id once for the whole batch — same optimization the
        // local scrape command uses. Falls back to null if the slug doesn't
        // map cleanly; the importer will accept that but the resulting rows
        // won't be filterable by city.
        $cityId = $importer->resolveCityId(null, $citySlug);

        $created = 0;
        $updated = 0;
        $imported = 0;
        $skippedAlreadyImported = 0;
        $errors = [];

        foreach ($profiles as $i => $profileData) {
            if (! is_array($profileData) || empty($profileData['external_id'])) {
                $errors[] = "profile #{$i}: missing external_id";
                continue;
            }

            $externalId = (string) $profileData['external_id'];

            // Only whitelist the columns the MassageRepublicProfile fillable
            // accepts — don't let arbitrary keys from the payload land in the
            // DB. The `apps` field the local scraper attaches is intentionally
            // discarded here (it's used for phone-metadata display elsewhere,
            // not stored on this table).
            $writable = collect($profileData)
                ->only((new MassageRepublicProfile)->getFillable())
                ->put('scraped_at', now())
                ->put('source_city', $citySlug)
                ->toArray();

            $existed = MassageRepublicProfile::where('external_id', $externalId)->exists();

            try {
                $row = MassageRepublicProfile::updateOrCreate(
                    ['external_id' => $externalId],
                    $writable
                );
            } catch (\Throwable $e) {
                $errors[] = "profile {$externalId}: DB write failed — " . $e->getMessage();
                continue;
            }

            $existed ? $updated++ : $created++;

            if ($row->imported_user_id) {
                $skippedAlreadyImported++;
                continue;
            }

            try {
                // Pass null for the phone worker — the phone (if any) rode
                // along in the JSON and is already persisted on $row->phone
                // via the updateOrCreate above. Importer will pick it up.
                $result = $importer->import($row, $citySlug, $cityId, null);
                if ($result !== null) {
                    $imported++;
                }
            } catch (\Throwable $e) {
                $errors[] = "profile {$externalId}: import failed — " . $e->getMessage();
                Log::warning('MR remote import: import() threw', [
                    'external_id' => $externalId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'city_slug' => $citySlug,
            'received' => count($profiles),
            'created' => $created,
            'updated' => $updated,
            'imported' => $imported,
            'skipped_already_imported' => $skippedAlreadyImported,
            'errors' => $errors,
        ]);
    }
}
