<?php

namespace App\Services;

use App\Models\City;
use App\Models\IvySocieteProfile;
use App\Models\ProfileImage;
use App\Models\User;
use App\Models\UsersProfile;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * Materializes an IvySocieteProfile row into the live users +
 * users_profiles + profile_images tables. Mirrors the shape and
 * defaults of MassageRepublicImporter so imported profiles from
 * either source behave identically downstream.
 *
 * No phone-reveal worker — ivysociete embeds the contact number in
 * the profile page HTML, so it's already on $row->phone by the time
 * the importer runs.
 */
class IvySocieteImporter
{
    public const SOURCE = 'ivysociete';
    protected const DEFAULT_GENDER_ID = 1; // Female
    protected const DEFAULT_USER_TYPE = 1;
    protected const DEFAULT_PACKAGE_ID = 37;
    protected const DEFAULT_PACKAGE_DAYS = 10;
    protected const IMPORT_DOMAIN = 'imported.ivysociete.evoory.local';

    protected GuzzleClient $http;
    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->http = new GuzzleClient([
            'timeout' => 60,
            'verify' => false,
            'http_errors' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; EvooryImporter/1.0)',
            ],
        ]);

        $this->imageManager = new ImageManager(new GdDriver());
    }

    /**
     * @return array{user_id:int, profile_id:int, images:int}|null  null when skipped
     */
    public function import(IvySocieteProfile $row, string $citySlug, ?int $cityId): ?array
    {
        if ($row->imported_user_id) {
            return null;
        }

        $cityId = $cityId ?: $this->resolveCityId($row->city, $citySlug);

        $user = $this->createSyntheticUser($row);
        $profile = $this->createProfile($user, $row, $cityId);
        $imageCount = $this->downloadAndStoreImages($row, $user, $profile);

        $row->forceFill([
            'imported_user_id' => $user->id,
            'imported_profile_id' => $profile->id,
            'imported_at' => now(),
            'source_city' => $citySlug,
        ])->save();

        return [
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'images' => $imageCount,
        ];
    }

    protected function createSyntheticUser(IvySocieteProfile $row): User
    {
        $scrapedEmail = $row->email ? trim($row->email) : null;

        // Same policy as the MR importer: only reuse a scraped email when
        // (a) it's not in the users table yet, or (b) it's already an
        // ivysociete-imported user. Never link to an unrelated account.
        if ($scrapedEmail && filter_var($scrapedEmail, FILTER_VALIDATE_EMAIL)) {
            $existing = User::where('email', $scrapedEmail)->first();
            if ($existing && $existing->imported_from === self::SOURCE) {
                return $existing;
            }
            if (! $existing) {
                return User::create([
                    'name' => $row->name ?: 'Imported profile',
                    'email' => $scrapedEmail,
                    'password' => Hash::make(Str::random(40)),
                    'email_verified_at' => null,
                    'type' => self::DEFAULT_USER_TYPE,
                    'status' => 'pending',
                    'imported_from' => self::SOURCE,
                ]);
            }
        }

        $syntheticEmail = 'ivy-' . Str::lower(Str::slug($row->external_id, '-')) . '@' . self::IMPORT_DOMAIN;

        $existing = User::where('email', $syntheticEmail)->first();
        if ($existing) {
            return $existing;
        }

        return User::create([
            'name' => $row->name ?: 'Imported profile',
            'email' => $syntheticEmail,
            'password' => Hash::make(Str::random(40)),
            'email_verified_at' => null,
            'type' => self::DEFAULT_USER_TYPE,
            'status' => 'pending',
            'imported_from' => self::SOURCE,
        ]);
    }

    protected function createProfile(User $user, IvySocieteProfile $row, ?int $cityId): UsersProfile
    {
        // ivysociete slugs are already clean ("acadia-allure"), no dash-suffix
        // cleanup needed like MR. Still cap to 50 chars for users_profiles.slug.
        $slug = Str::slug((string) $row->external_id, '-') ?: Str::slug((string) $row->name, '-');
        $slug = trim(Str::limit($slug, 50, ''), '-');

        $attrs = $row->attributes ?? [];

        return UsersProfile::create([
            'user_id' => $user->id,
            'name' => $row->name ?: 'Imported profile',
            'about' => $row->description,
            'city' => $cityId,
            'gender' => self::DEFAULT_GENDER_ID,
            'age' => $this->normalizeAge($row->age),
            'website' => $row->website ?: null,
            'phone' => $this->normalizePhone($row->phone),
            // ivysociete doesn't expose per-app flags in the profile JSON.
            // Default all off; users can toggle after claim.
            'iswhatsapp' => 0,
            'istelegram' => 0,
            'issignal' => 0,
            'iswechat' => 0,
            'orientation' => $this->lookupId('orientations', $attrs['orientation'] ?? null),
            'height' => $this->extractHeightCm($attrs['height'] ?? null),
            'haircolor' => $this->lookupId('hair_colors', $attrs['hair_color'] ?? null),
            'nationality' => $this->lookupNationalityId($attrs['nationality'] ?? null),
            'bust' => $this->lookupId('busts', $attrs['bust'] ?? null),
            'ethnicity' => $this->lookupId('ethnicities', $attrs['ethnicity'] ?? null),
            'smoke' => $this->normalizeYesNo($attrs['smokes'] ?? null),
            'incallprice' => $row->incall_price,
            'outcallprice' => $row->outcall_price,
            'incallcurr' => $row->incall_currency,
            'outcallcurr' => $row->outcall_currency,
            // If a price is present the service is being offered — flip the boolean
            // so the front-end incall/outcall filters actually match this profile.
            'incall' => ($row->incall_price !== null && $row->incall_price > 0) ? 1 : 0,
            'outcall' => ($row->outcall_price !== null && $row->outcall_price > 0) ? 1 : 0,
            'listing' => 1,
            'is_active' => 1,
            'is_featured' => 1,
            'is_verified' => $row->is_verified ? 1 : 0,
            'package_id' => self::DEFAULT_PACKAGE_ID,
            'package_days' => self::DEFAULT_PACKAGE_DAYS,
            'promoted_until' => now()->addDays(self::DEFAULT_PACKAGE_DAYS),
            'package_expires_at' => now()->addDays(self::DEFAULT_PACKAGE_DAYS),
            'slug' => $slug,
            'imported_from' => self::SOURCE,
            'imported_external_id' => $row->external_id,
        ]);
    }

    protected function lookupId(string $table, ?string $value): ?int
    {
        if ($value === null || trim($value) === '') return null;
        $id = DB::table($table)->whereRaw('LOWER(name) = ?', [strtolower(trim($value))])->value('id');
        return $id ? (int) $id : null;
    }

    /**
     * ivysociete height comes as a raw integer in cm (e.g. 165).
     * MR came as text ("170 cm / 5'7""). We accept either shape.
     */
    protected function extractHeightCm(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        if (ctype_digit($value)) return $value;
        if (preg_match('/(\d{2,3})\s*cm/i', $value, $m)) return $m[1];
        return null;
    }

    protected function normalizeYesNo(?string $value): ?int
    {
        if ($value === null) return null;
        $v = strtolower(trim($value));
        if (in_array($v, ['yes', 'true', '1'], true)) return 1;
        if (in_array($v, ['no', 'false', '0', 'never'], true)) return 0;
        return null;
    }

    protected function normalizePhone(?string $phone): ?string
    {
        if (! $phone) return null;
        $p = preg_replace('/[^0-9\+]/', '', trim($phone));
        return $p !== '' ? $p : null;
    }

    protected function lookupNationalityId(?string $demonym): ?int
    {
        if (! $demonym) return null;

        // Reuse the same demonym→country map the MR importer uses. Any
        // demonym not in the map falls through to a UPPER(name) lookup
        // against the countries table.
        $map = [
            'belarusian' => 'BELARUS', 'russian' => 'RUSSIAN FEDERATION', 'ukrainian' => 'UKRAINE',
            'indian' => 'INDIA', 'pakistani' => 'PAKISTAN', 'filipino' => 'PHILIPPINES', 'filipina' => 'PHILIPPINES',
            'thai' => 'THAILAND', 'chinese' => 'CHINA', 'japanese' => 'JAPAN', 'korean' => 'KOREA, REPUBLIC OF',
            'british' => 'UNITED KINGDOM', 'american' => 'UNITED STATES', 'canadian' => 'CANADA',
            'brazilian' => 'BRAZIL', 'argentinian' => 'ARGENTINA', 'argentine' => 'ARGENTINA',
            'colombian' => 'COLOMBIA', 'venezuelan' => 'VENEZUELA', 'mexican' => 'MEXICO',
            'romanian' => 'ROMANIA', 'polish' => 'POLAND', 'hungarian' => 'HUNGARY',
            'german' => 'GERMANY', 'french' => 'FRANCE', 'spanish' => 'SPAIN', 'italian' => 'ITALY',
            'greek' => 'GREECE', 'portuguese' => 'PORTUGAL', 'dutch' => 'NETHERLANDS', 'belgian' => 'BELGIUM',
            'czech' => 'CZECH REPUBLIC', 'slovak' => 'SLOVAKIA', 'bulgarian' => 'BULGARIA',
            'serbian' => 'SERBIA', 'croatian' => 'CROATIA', 'latvian' => 'LATVIA', 'lithuanian' => 'LITHUANIA',
            'estonian' => 'ESTONIA', 'moldovan' => 'MOLDOVA, REPUBLIC OF', 'turkish' => 'TURKEY',
            'australian' => 'AUSTRALIA', 'kiwi' => 'NEW ZEALAND', 'new zealander' => 'NEW ZEALAND',
            'south african' => 'SOUTH AFRICA', 'kenyan' => 'KENYA',
            'nigerian' => 'NIGERIA', 'moroccan' => 'MOROCCO', 'egyptian' => 'EGYPT',
            'lebanese' => 'LEBANON', 'syrian' => 'SYRIAN ARAB REPUBLIC', 'iranian' => 'IRAN, ISLAMIC REPUBLIC OF',
            'iraqi' => 'IRAQ', 'emirati' => 'UNITED ARAB EMIRATES', 'saudi' => 'SAUDI ARABIA',
            'kazakh' => 'KAZAKHSTAN', 'uzbek' => 'UZBEKISTAN', 'malaysian' => 'MALAYSIA',
            'indonesian' => 'INDONESIA', 'vietnamese' => 'VIETNAM', 'singaporean' => 'SINGAPORE',
        ];

        $key = strtolower(trim($demonym));
        $countryName = $map[$key] ?? strtoupper($demonym);

        $id = DB::table('countries')->whereRaw('UPPER(name) = ?', [$countryName])->value('id');
        return $id ? (int) $id : null;
    }

    protected function downloadAndStoreImages(IvySocieteProfile $row, User $user, UsersProfile $profile): int
    {
        $urls = $row->image_urls ?? [];
        if (! is_array($urls) || empty($urls)) {
            return 0;
        }

        $urls = array_values(array_unique(array_filter($urls, fn ($u) => is_string($u) && str_starts_with($u, 'http'))));
        if (empty($urls)) {
            return 0;
        }

        $disk = Storage::disk('assets_external');
        $basePath = "userimages/{$user->id}/{$profile->id}";

        foreach (["userimages", "userimages/{$user->id}", $basePath] as $dir) {
            if (! $disk->exists($dir)) {
                $disk->makeDirectory($dir, 0755, true);
            }
        }

        $stored = 0;
        $tempDir = sys_get_temp_dir();

        foreach ($urls as $index => $url) {
            try {
                $response = $this->http->request('GET', $url);
                if ($response->getStatusCode() !== 200) {
                    Log::warning('IvySociete importer: image download failed', ['url' => $url, 'status' => $response->getStatusCode()]);
                    continue;
                }

                $tempSrc = $tempDir . DIRECTORY_SEPARATOR . uniqid('ivyimg_', true) . '.bin';
                file_put_contents($tempSrc, (string) $response->getBody());

                $img = $this->imageManager->read($tempSrc);
                if ($img->width() > $img->height()) {
                    $img->scaleDown(width: 1600);
                } else {
                    $img->scaleDown(height: 1600);
                }

                $fileName = time() . random_int(100, 999) . '_' . $index;
                $tempJpg = $tempDir . DIRECTORY_SEPARATOR . uniqid() . '.jpg';
                $tempWebp = $tempDir . DIRECTORY_SEPARATOR . uniqid() . '.webp';
                $tempThumb = $tempDir . DIRECTORY_SEPARATOR . uniqid() . '_thumb.webp';

                $img->save($tempJpg, quality: 82);
                $img->encode(new WebpEncoder(quality: 80))->save($tempWebp);

                $thumb = $this->imageManager->read($tempSrc);
                if ($thumb->width() > $thumb->height()) {
                    $thumb->scaleDown(width: 300);
                } else {
                    $thumb->scaleDown(height: 300);
                }
                $thumb->encode(new WebpEncoder(quality: 55))->save($tempThumb);

                $disk->put("{$basePath}/{$fileName}.jpg", file_get_contents($tempJpg));
                $disk->put("{$basePath}/{$fileName}.webp", file_get_contents($tempWebp));
                $disk->put("{$basePath}/{$fileName}_thumb.webp", file_get_contents($tempThumb));

                @unlink($tempSrc);
                @unlink($tempJpg);
                @unlink($tempWebp);
                @unlink($tempThumb);

                ProfileImage::create([
                    'user_id' => $user->id,
                    'profile_id' => $profile->id,
                    'image' => $fileName . '.jpg',
                    'image_webp' => $fileName . '.webp',
                    'is_main' => $stored === 0 ? 1 : 0,
                    'image_order' => $stored,
                ]);

                $stored++;
            } catch (\Throwable $e) {
                Log::warning('IvySociete importer: image processing error', ['url' => $url, 'error' => $e->getMessage()]);
                continue;
            }
        }

        return $stored;
    }

    protected function normalizeAge(?string $age): ?string
    {
        if ($age === null) return null;
        $digits = preg_replace('/[^0-9]/', '', $age);
        return $digits !== '' ? $digits : null;
    }

    public function resolveCityId(?string $scrapedCity, string $citySlug): ?int
    {
        // Priority: the requested --city always wins. ivysociete lists
        // touring profiles on multiple city pages (e.g. a Melbourne-based
        // escort appears on /escorts/sydney while she's advertising there),
        // and each profile carries a single "home city" field. Running with
        // --city=sydney means the operator wants those profiles filed under
        // Sydney regardless of the source's home-city label. The scraped
        // city is retained on ivysociete_profiles.city for reference.
        $candidates = [Str::title(str_replace('-', ' ', $citySlug))];
        if ($scrapedCity) {
            $candidates[] = $scrapedCity;
        }

        foreach ($candidates as $name) {
            $name = trim($name ?? '');
            if ($name === '') continue;
            $id = City::where('name', $name)->value('id');
            if ($id) return $id;
        }
        return null;
    }
}
