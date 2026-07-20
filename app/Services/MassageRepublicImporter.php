<?php

namespace App\Services;

use App\Models\City;
use App\Models\MassageRepublicProfile;
use App\Models\ProfileImage;
use App\Models\User;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class MassageRepublicImporter
{
    public const SOURCE = 'massage_republic';
    protected const DEFAULT_GENDER_ID = 1; // Female
    protected const DEFAULT_USER_TYPE = 1;
    protected const IMPORT_DOMAIN = 'imported.evoory.local';

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
     * @return array{user_id:int, profile_id:int, images:int, phone_error:?string}|null  null when skipped
     */
    public function import(MassageRepublicProfile $row, string $citySlug, ?int $cityId, ?MassageRepublicPhoneWorker $phoneWorker = null): ?array
    {
        if ($row->imported_user_id) {
            return null;
        }

        $cityId = $cityId ?: $this->resolveCityId($row->city, $citySlug);

        // Prefer any scraped email when creating the user
        $scrapedEmail = $row->email ?? ($row->attributes['email'] ?? null);
        $user = $this->createSyntheticUser($row, $scrapedEmail);

        // Try to reveal the phone via the Playwright worker. Done before
        // createProfile() so the phone lands in the same INSERT.
        $phone = null;
        $phoneError = null;
        $apps = ['whatsapp' => false, 'telegram' => false, 'signal' => false, 'wechat' => false];
        if ($phoneWorker) {
            if (! $phoneWorker->isAvailable()) {
                $phoneError = 'worker script missing (tools/mr-phone-worker/worker.js)';
            } else {
                $listingPath = '/female-escorts-in-' . ltrim($citySlug, '/');
                try {
                    $phone = $phoneWorker->revealOne($row->external_id, $listingPath);
                    if (method_exists($phoneWorker, 'getLastApps')) {
                        $apps = $phoneWorker->getLastApps($row->external_id);
                    }
                    if ($phone) {
                        $row->forceFill(['phone' => $phone])->save();
                    } elseif (method_exists($phoneWorker, 'getLastError')) {
                        $phoneError = $phoneWorker->getLastError($row->external_id) ?: 'no phone returned';
                    } else {
                        $phoneError = 'no phone returned';
                    }
                } catch (\Throwable $e) {
                    $phoneError = 'exception: ' . $e->getMessage();
                    Log::warning('MR importer: phone reveal threw', ['slug' => $row->external_id, 'error' => $e->getMessage()]);
                }
            }
        } else {
            $phoneError = 'phone reveal disabled (--no-phone)';
        }

        $profile = $this->createProfile($user, $row, $cityId, $phone, $apps);
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
            'phone_error' => $phoneError,
        ];
    }

    protected function createSyntheticUser(MassageRepublicProfile $row, ?string $scrapedEmail = null): User
    {
        $scrapedEmail = $scrapedEmail ? trim($scrapedEmail) : null;

        // Only reuse a scraped email if it's a real-looking address that
        // (a) we don't already have in the users table, OR
        // (b) we already have but it was previously imported by this same source.
        // Never link a freshly-scraped profile to an unrelated, pre-existing user.
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
            // Else: collision with an unrelated existing user — fall through to
            // the synthetic-email path so we don't pollute that account.
        }

        $syntheticEmail = 'mr-' . Str::lower(Str::slug($row->external_id, '-')) . '@' . self::IMPORT_DOMAIN;

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

    /**
     * @param array{whatsapp:bool,telegram:bool,signal:bool,wechat:bool} $apps
     */
    protected function createProfile(User $user, MassageRepublicProfile $row, ?int $cityId, ?string $revealedPhone = null, array $apps = ['whatsapp'=>false,'telegram'=>false,'signal'=>false,'wechat'=>false]): UsersProfile
    {
        // MR names look like "Jenny New Real Independent 😊 – Filipino escort in Dubai".
        // The URL uses {id}/{slug} (id is the route key — slug has no unique index),
        // so we only need a clean SEO slug. Drop everything after the first en/em dash
        // or hyphen-with-spaces and slug the leading "real name" portion only.
        $primary = preg_split('/\s+[\x{2013}\x{2014}\-]\s+/u', (string) $row->name, 2)[0] ?? '';
        $slug = Str::slug($primary, '-') ?: Str::slug($row->external_id, '-');
        // users_profiles.slug is varchar(50).
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
            'phone' => $this->normalizePhone($revealedPhone ?? $row->phone ?? $attrs['phone'] ?? null),
            'iswhatsapp' => $apps['whatsapp'] ? 1 : 0,
            'istelegram' => $apps['telegram'] ? 1 : 0,
            'issignal'   => $apps['signal']   ? 1 : 0,
            // WeChat intentionally always 0 — no wechat_id column exists,
            // so the UI can't render a meaningful contact link. See the
            // matching skip in profile-details.blade.php phone modal.
            'iswechat'   => 0,
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
            'listing' => 1,
            'is_active' => 1,
            'is_featured' => 1,
            'is_verified' => $row->is_verified ? 1 : 0,
            'package_id' => 37,
            'package_days' => 10,
            'promoted_until' => now()->addDays(10),
            'package_expires_at' => now()->addDays(10),
            'slug' => $slug,
            'imported_from' => self::SOURCE,
            'imported_external_id' => $row->external_id,
        ]);
    }

    /**
     * Case-insensitive lookup of a value in a lookup table.
     * Returns the row's id, or null when no match.
     */
    protected function lookupId(string $table, ?string $value): ?int
    {
        if ($value === null || trim($value) === '') return null;

        $needle = trim($value);
        $id = DB::table($table)->whereRaw('LOWER(name) = ?', [strtolower($needle)])->value('id');
        return $id ? (int) $id : null;
    }

    /**
     * MR exposes height as "170 cm / 5′7″" — we only store the cm number.
     */
    protected function extractHeightCm(?string $value): ?string
    {
        if (! $value) return null;
        if (preg_match('/(\d{2,3})\s*cm/i', $value, $m)) {
            return $m[1];
        }
        return null;
    }

    protected function normalizeYesNo(?string $value): ?int
    {
        if ($value === null) return null;
        $v = strtolower(trim($value));
        if ($v === 'yes' || $v === 'true' || $v === '1') return 1;
        if ($v === 'no' || $v === 'false' || $v === '0') return 0;
        return null;
    }

    protected function normalizePhone(?string $phone): ?string
    {
        if (! $phone) return null;
        $p = trim($phone);
        // keep digits and leading +
        $p = preg_replace('/[^0-9\+]/', '', $p);
        return $p !== '' ? $p : null;
    }

    /**
     * MR shows nationality as a demonym ("Belarusian", "Indian", "British", ...).
     * Map common ones to the `countries.name` (uppercased) and look up the id.
     */
    protected function lookupNationalityId(?string $demonym): ?int
    {
        if (! $demonym) return null;

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
            'australian' => 'AUSTRALIA', 'south african' => 'SOUTH AFRICA', 'kenyan' => 'KENYA',
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

    protected function downloadAndStoreImages(MassageRepublicProfile $row, User $user, UsersProfile $profile): int
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
                    Log::warning('MR importer: image download failed', ['url' => $url, 'status' => $response->getStatusCode()]);
                    continue;
                }

                $tempSrc = $tempDir . DIRECTORY_SEPARATOR . uniqid('mrimg_', true) . '.bin';
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
                Log::warning('MR importer: image processing error', ['url' => $url, 'error' => $e->getMessage()]);
                continue;
            }
        }

        return $stored;
    }

    protected function normalizeAge(?string $age): ?string
    {
        if ($age === null) {
            return null;
        }
        $digits = preg_replace('/[^0-9]/', '', $age);
        return $digits !== '' ? $digits : null;
    }

    /**
     * Resolve a cities.id from the scraped city string. Falls back to the
     * city slug from the MR URL ("dubai" → cities.name="Dubai") when the
     * scraped string is missing or ambiguous.
     */
    public function resolveCityId(?string $scrapedCity, string $citySlug): ?int
    {
        $candidates = [];

        if ($scrapedCity) {
            $candidates[] = $scrapedCity;
            $candidates[] = preg_replace('/[A-Z]{2,4}$/', '', $scrapedCity); // "DubaiUAE" → "Dubai"
        }

        $candidates[] = Str::title(str_replace('-', ' ', $citySlug));

        foreach ($candidates as $name) {
            $name = trim($name ?? '');
            if ($name === '') continue;

            $id = City::where('name', $name)->value('id');
            if ($id) {
                return $id;
            }
        }

        return null;
    }
}
