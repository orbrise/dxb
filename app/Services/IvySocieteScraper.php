<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

/**
 * Scrapes ivysociete.com listings and profile pages.
 *
 * Unlike the MR scraper, ivysociete has no Cloudflare/JS challenge —
 * profile data is embedded in the Next.js RSC hydration stream inside
 * the raw HTML. A plain Guzzle GET with a browser User-Agent is enough
 * to receive the fully-populated markup.
 *
 * Extraction strategy: for each profile URL we locate the JSON block
 * containing `"slug":"<target>"` in the escaped RSC stream, take a
 * window around it, unescape the JS string quoting, then regex-match
 * individual fields. Walking full brace-balanced JSON is unnecessary
 * for the shallow scalar fields we care about (firstName, contactNumber,
 * height, etc.) — a windowed per-field match is faster and more forgiving
 * of Next.js chunk boundaries.
 */
class IvySocieteScraper
{
    protected const BASE_URL = 'https://ivysociete.com';
    protected const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    protected GuzzleClient $http;

    public function __construct()
    {
        $this->http = new GuzzleClient([
            'timeout' => 60,
            'verify' => false,
            'http_errors' => false,
            'headers' => [
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
            ],
        ]);
    }

    /**
     * Scrape up to $limit profiles for a city.
     *
     * @param  callable|null  $skipChecker  fn(string $externalId): bool
     * @return array<int, array>            payloads ready for updateOrCreate
     */
    public function scrape(int $limit, string $citySlug, ?callable $skipChecker = null): array
    {
        $slugs = $this->fetchListingSlugs($citySlug);
        if (empty($slugs)) {
            return [];
        }

        $out = [];
        foreach ($slugs as $slug) {
            if (count($out) >= $limit) {
                break;
            }
            if ($skipChecker && $skipChecker($slug)) {
                continue;
            }
            $data = $this->fetchProfile($slug, $citySlug);
            if ($data !== null) {
                $out[] = $data;
            }
        }

        return $out;
    }

    /**
     * @return string[]  profile slugs in listing order (deduplicated)
     */
    public function fetchListingSlugs(string $citySlug): array
    {
        $url = self::BASE_URL . '/escorts/' . rawurlencode($citySlug);
        $res = $this->http->request('GET', $url);
        if ($res->getStatusCode() !== 200) {
            Log::warning('IvySociete: listing fetch failed', ['url' => $url, 'status' => $res->getStatusCode()]);
            return [];
        }

        $body = (string) $res->getBody();
        preg_match_all('#/escorts/profile/([a-z0-9-]+)#i', $body, $m);

        return array_values(array_unique($m[1] ?? []));
    }

    /**
     * Fetch a single profile page and return a payload matching
     * IvySocieteProfile fillable — or null if the profile HTML doesn't
     * yield a usable primary object.
     */
    public function fetchProfile(string $slug, string $citySlug): ?array
    {
        $url = self::BASE_URL . '/escorts/profile/' . $slug;
        $res = $this->http->request('GET', $url);
        if ($res->getStatusCode() !== 200) {
            Log::warning('IvySociete: profile fetch failed', ['url' => $url, 'status' => $res->getStatusCode()]);
            return null;
        }

        $html = (string) $res->getBody();

        $window = $this->extractWindow($html, $slug);
        if ($window === null) {
            return null;
        }

        $attributes = $this->extractAttributes($window);
        $userId = $attributes['user_id'] ?? null;

        return [
            'external_id' => $slug,
            'profile_url' => $url,
            'name' => $this->buildName($window),
            'age' => $this->matchScalar('/"age":(\d+)/', $window),
            'city' => $this->matchScalar('/"city":\{[^}]*?"name":"([^"]+)"/', $window)
                      ?? $this->titleize($citySlug),
            'phone' => $this->extractContactNumber($window),
            'email' => $this->matchScalar('/"displayEmail":"([^"]+)"/', $window),
            'website' => $this->matchScalar('/"website":"(https?:[^"]+)"/', $window),
            'gender' => 'female',
            'is_verified' => (bool) preg_match('/"isVerified":true|"verified":true(?![^}]*"mediaType")/', $window),
            'is_premium' => (bool) preg_match('/"isPremium":true|"premium":true/', $window),
            'description' => $this->extractMetaDescription($html),
            // Images live in a large JSON array that often extends past our
            // 8KB window, and the page also embeds nearby profiles' images
            // in a sidebar. Filter by the profile's own user_id (which
            // prefixes the CDN path) so we grab this profile's photos and
            // only this profile's.
            'image_urls' => $userId ? $this->extractImageUrlsForUser($html, $userId) : [],
            'attributes' => $attributes,
            'incall_price' => null,
            'outcall_price' => null,
            'incall_currency' => null,
            'outcall_currency' => null,
        ];
    }

    /**
     * Locate the RSC block that describes this profile and return a
     * decoded/unescaped window of text around it. Returns null if the
     * slug can't be found (e.g. profile removed between listing and detail).
     */
    protected function extractWindow(string $html, string $slug): ?string
    {
        // In the RSC hydration stream, quotes are escaped as \". Look for
        // `\"slug\":\"<slug>\"` — that's the anchor for the primary profile
        // object on this page.
        $needle = '\\"slug\\":\\"' . $slug . '\\"';
        $pos = strpos($html, $needle);
        if ($pos === false) {
            return null;
        }

        // 8KB window centered on the slug covers the primary object in
        // every profile I sampled (biggest was ~5.5KB including nested
        // images array). If a profile is truly gigantic and clips, we
        // fall back to per-field nulls — safer than an unbounded scan.
        $start = max(0, $pos - 4000);
        $raw = substr($html, $start, 8000);

        // Undo the RSC string escaping so downstream regex can use "key":"val".
        return str_replace(['\\"', '\\\\'], ['"', '\\'], $raw);
    }

    /**
     * `contactNumber` lives inside the `contacts` sub-object of the
     * profile. Because our window is anchored on the profile slug, the
     * first "contactNumber":"..." after the anchor belongs to this
     * profile — matching non-null.
     */
    protected function extractContactNumber(string $window): ?string
    {
        if (preg_match('/"contactNumber":"([^"]+)"/', $window, $m)) {
            $v = trim($m[1]);
            return $v !== '' ? $v : null;
        }
        return null;
    }

    /**
     * Find image URLs that belong to this profile only. The CDN path is
     * `/assets.ivysociete.com/{userId}/{albumId}/{file}`, so filtering by
     * the profile's own user_id excludes the sidebar's related-profile
     * images that also appear in the same HTML.
     *
     * We match against the raw (escaped) HTML — the CDN URLs are plain
     * (no backslashes to unescape) even inside the RSC stream.
     *
     * @return string[]
     */
    protected function extractImageUrlsForUser(string $html, string $userId): array
    {
        $userId = preg_quote($userId, '/');
        preg_match_all(
            '#https://assets\.ivysociete\.com/' . $userId . '/[0-9]+/[^"\\\\<>\s]+?\.(?:jpg|jpeg|png|webp)#i',
            $html,
            $m
        );

        $urls = [];
        foreach ($m[0] as $url) {
            // Skip the CDN's thumbnail variants — the importer generates
            // its own thumbnails during downloadAndStoreImages.
            if (str_contains($url, '_thumbnail_')) continue;
            // Skip video screenshots (uploaded as .mp4 with a .jpg preview);
            // Intervention can't process them cleanly and they look like
            // placeholder art anyway.
            if (str_contains($url, '.mp4_screenshot')) continue;
            $urls[] = $url;
        }

        return array_values(array_unique($urls));
    }

    /**
     * Pull the free-form attributes we mirror onto UsersProfile
     * (ethnicity, hair color, height, bust, etc.). Kept in the JSON
     * `attributes` column so the importer can lookup each one against
     * the corresponding cities/ethnicities/etc. table.
     */
    protected function extractAttributes(string $window): array
    {
        $attrs = [];
        $fields = [
            'ethnicity' => '/"ethnicity":"([^"]+)"/',
            'hair_color' => '/"hairColor":"([^"]+)"/',
            'eye_color' => '/"eyeColor":"([^"]+)"/',
            'bust' => '/"bust":"([^"]+)"/',
            'height' => '/"height":(\d{2,3})(?!\.)/',
            'dress_size' => '/"dressSize":(\d+)/',
            'orientation' => '/"(?:sexualOrientation|orientation)":"([^"]+)"/',
            'nationality' => '/"nationality":"([^"]+)"/',
            'smokes' => '/"smokes":"([^"]+)"/',
            'instagram' => '/"instagram":"([^"]+)"/',
            'user_id' => '/"userId":(\d+)/',
        ];
        foreach ($fields as $key => $pattern) {
            $v = $this->matchScalar($pattern, $window);
            if ($v !== null && $v !== '') {
                $attrs[$key] = $v;
            }
        }
        return $attrs;
    }

    protected function buildName(string $window): ?string
    {
        $first = $this->matchScalar('/"firstName":"([^"]+)"/', $window);
        $last = $this->matchScalar('/"lastName":"([^"]+)"/', $window);
        $name = trim(($first ?? '') . ' ' . ($last ?? ''));
        return $name !== '' ? $name : null;
    }

    /**
     * Fallback description: ivysociete stores the long bio as an RSC
     * reference (e.g. "bio":"$2d") so we can't read it out of the
     * primary object. The `<meta name="description">` tag holds a
     * summary that's good enough for our About block.
     */
    protected function extractMetaDescription(string $html): ?string
    {
        if (preg_match('/<meta\s+name="description"\s+content="([^"]+)"/i', $html, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return null;
    }

    protected function matchScalar(string $pattern, string $subject): ?string
    {
        if (preg_match($pattern, $subject, $m)) {
            return $m[1];
        }
        return null;
    }

    protected function titleize(string $slug): string
    {
        return ucwords(str_replace('-', ' ', $slug));
    }
}
