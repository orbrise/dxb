<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MassageRepublicScraper
{
    protected string $username;
    protected string $password;
    protected $client;
    protected GuzzleClient $guzzle;
    protected CookieJar $cookies;
    protected array $defaultHeaders;
    protected ?string $hostIp = null;
    protected string $baseHost;
    protected string $baseUrl;
    protected string $loginPath;
    protected string $emailField;
    protected string $passwordField;
    protected string $listingPath;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->hostIp = env('MASSAGE_REPUBLIC_HOST_IP');

        $preferredHost = env('MASSAGE_REPUBLIC_HOST');
        $resolveOverride = env('MASSAGE_REPUBLIC_CURL_RESOLVE');

        if (empty($preferredHost) && ! empty($resolveOverride)) {
            $resolveEntries = is_array($resolveOverride)
                ? $resolveOverride
                : preg_split('/\s*,\s*/', trim($resolveOverride));
            $firstEntry = trim($resolveEntries[0] ?? '');

            if (preg_match('/^([^:]+):\d+:/', $firstEntry, $matches)) {
                $preferredHost = $matches[1];
            }
        }

        $this->baseHost = $preferredHost ?: 'massagerepublic.tk';
        $this->baseUrl = "https://{$this->baseHost}";
        $this->loginPath = env('MASSAGE_REPUBLIC_LOGIN_PATH', '/sign-in');
        $this->emailField = env('MASSAGE_REPUBLIC_EMAIL_FIELD', 'account[email]');
        $this->passwordField = env('MASSAGE_REPUBLIC_PASSWORD_FIELD', 'account[password]');
        $this->listingPath = env('MASSAGE_REPUBLIC_LISTING_PATH', '/female-escorts-in-dubai');

        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ];

        $this->cookies = new CookieJar();
        $this->defaultHeaders = $headers;

        $this->guzzle = new GuzzleClient([
            'cookies' => $this->cookies,
            'timeout' => 30,
            'verify' => false,
            'allow_redirects' => ['max' => 10, 'referer' => true, 'protocols' => ['http', 'https']],
            'headers' => $headers,
            'curl' => $this->getCurlOptions(),
            'expect' => false,
            'http_errors' => false,
        ]);

        $this->client = Http::withOptions([
            'cookies' => $this->cookies,
            'timeout' => 30,
            'verify' => false,
            'curl' => $this->getCurlOptions(),
            'expect' => false,
        ])->withHeaders($headers);
    }

    protected ?string $lastLoginError = null;

    public function scrape(int $limit = 50, ?string $citySlug = null): array
    {
        if ($citySlug !== null && $citySlug !== '') {
            $this->listingPath = '/' . trim($this->normalizeCitySlug($citySlug), '/');
        }

        if (! $this->login()) {
            $detail = $this->lastLoginError ? " ({$this->lastLoginError})" : '';
            throw new \RuntimeException("Login failed for {$this->baseHost}{$detail}");
        }

        $profiles = [];
        $page = 1;
        $fetchFailures = [];

        // MR / Cloudflare rate-limits rapid sequential profile fetches; without
        // a pause we get ~3 profiles in, then everything 4xx/5xx silently.
        $delayMs = (int) (env('MASSAGE_REPUBLIC_FETCH_DELAY_MS', 1500));

        while (count($profiles) < $limit) {
            $html = $this->fetchProfileListPage($page);
            $links = $this->extractProfileLinks($html);

            if (empty($links)) {
                break;
            }

            foreach ($links as $link) {
                if (count($profiles) >= $limit) {
                    break 2;
                }

                $url = $this->normalizeUrl($link);
                $html = $this->fetchUrlWithRetry($url);

                if (! $html) {
                    $fetchFailures[] = $url;
                    if ($delayMs > 0) usleep($delayMs * 1000);
                    continue;
                }

                $profile = $this->parseProfile($url, $html);

                if (! empty($profile['external_id'])) {
                    $profiles[] = $profile;
                }

                if ($delayMs > 0) usleep($delayMs * 1000);
            }

            $page++;
            if ($page > 20) {
                break;
            }
        }

        if (! empty($fetchFailures)) {
            \Log::info('MR scraper: profile-fetch failures', [
                'count' => count($fetchFailures),
                'sample' => array_slice($fetchFailures, 0, 5),
            ]);
        }

        return $profiles;
    }

    /**
     * fetchUrl + retry on 4xx/5xx, with exponential backoff up to 3 attempts.
     * Returns the body on success, empty string after all retries fail.
     */
    protected function fetchUrlWithRetry(string $url, int $maxAttempts = 3): string
    {
        $attempt = 0;
        $backoffMs = 2000;

        while ($attempt < $maxAttempts) {
            $body = $this->fetchUrl($url);
            if ($body !== '') {
                return $body;
            }
            $attempt++;
            if ($attempt < $maxAttempts) {
                usleep($backoffMs * 1000);
                $backoffMs *= 2;
            }
        }
        return '';
    }

    protected function login(): bool
    {
        $loginUrl = "{$this->baseUrl}{$this->loginPath}";

        try {
            $getResponse = $this->guzzle->request('GET', $loginUrl);
        } catch (\Throwable $e) {
            $this->lastLoginError = "GET {$loginUrl} threw: " . $e->getMessage();
            return false;
        }

        $getStatus = $getResponse->getStatusCode();
        $getBody = (string) $getResponse->getBody();

        if ($getStatus >= 400) {
            $snippet = trim(substr(strip_tags($getBody), 0, 200));
            $this->lastLoginError = "GET {$loginUrl} returned HTTP {$getStatus}: {$snippet}";
            return false;
        }

        $form = $this->extractLoginForm($getBody);

        if (! $form) {
            $this->lastLoginError = "Could not locate a login <form> with a password input on {$loginUrl}";
            return false;
        }

        $postUrl = $form['action']
            ? $this->normalizeUrl($form['action'])
            : $loginUrl;

        $data = $form['fields'];
        $data[$this->emailField] = $this->username;
        $data[$this->passwordField] = $this->password;

        try {
            $response = $this->guzzle->request('POST', $postUrl, [
                'form_params' => $data,
                'headers' => [
                    'Referer' => $loginUrl,
                    'Origin' => $this->baseUrl,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);
        } catch (\Throwable $e) {
            $this->lastLoginError = "POST {$postUrl} threw: " . $e->getMessage();
            return false;
        }

        $status = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($status >= 400) {
            $snippet = trim(substr(strip_tags($body), 0, 200));
            $this->lastLoginError = "POST {$postUrl} returned HTTP {$status}: {$snippet}";
            return false;
        }

        $loggedIn = str_contains($body, '/sign-out')
            || str_contains($body, '/sign_out')
            || stripos($body, 'logout') !== false
            || stripos($body, 'sign out') !== false;

        if ($loggedIn) {
            return true;
        }

        $snippet = trim(substr(strip_tags($body), 0, 200));
        $this->lastLoginError = "POST {$postUrl} returned HTTP {$status} but no signed-in marker — likely bad credentials. Body: {$snippet}";
        return false;
    }

    protected function extractLoginForm(string $html): ?array
    {
        if ($html === '') {
            return null;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $forms = $xpath->query('//form[.//input[@type="password"]]');
        if ($forms->length === 0) {
            return null;
        }

        $form = $forms->item(0);
        $action = $form->getAttribute('action');
        $fields = [];

        foreach ($xpath->query('.//input[@name]', $form) as $input) {
            $name = $input->getAttribute('name');
            $type = strtolower($input->getAttribute('type'));

            if ($name === '' || in_array($type, ['submit', 'button', 'image', 'reset'], true)) {
                continue;
            }

            if ($type === 'checkbox' && ! $input->hasAttribute('checked')) {
                if (! isset($fields[$name])) {
                    $fields[$name] = $input->getAttribute('value');
                }
                continue;
            }

            $fields[$name] = $input->getAttribute('value');
        }

        return [
            'action' => $action,
            'fields' => $fields,
        ];
    }

    protected function normalizeCitySlug(string $citySlug): string
    {
        $citySlug = trim($citySlug, " \t\n\r/");

        if (str_starts_with($citySlug, 'female-escorts-in-') || str_starts_with($citySlug, 'male-escorts-in-')) {
            return $citySlug;
        }

        return 'female-escorts-in-' . $citySlug;
    }

    public function getListingPath(): string
    {
        return $this->listingPath;
    }

    protected function fetchProfileListPage(int $page): string
    {
        $path = rtrim($this->listingPath, '/');
        $url = $page === 1
            ? "{$this->baseUrl}{$path}"
            : "{$this->baseUrl}{$path}/{$page}";
        return $this->fetchUrl($url);
    }

    protected function fetchUrl(string $url): string
    {
        $actualUrl = $this->buildRequestUrl($url);
        $response = $this->client->get($actualUrl);
        return $response->successful() ? $response->body() : '';
    }

    protected function buildRequestUrl(string $url): string
    {
        return $url;
    }

    protected function extractCsrfToken(string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $node = $xpath->query('//input[@name="_token"]')->item(0);

        return $node ? trim($node->getAttribute('value')) : null;
    }

    protected function extractProfileLinks(string $html): array
    {
        if (empty($html)) {
            return [];
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $listingPath = '/' . trim($this->listingPath, '/');
        $listingSegment = ltrim($listingPath, '/');

        $nodes = $xpath->query("//a[starts-with(@href, '{$listingPath}/')]/@href");
        $links = [];

        foreach ($nodes as $node) {
            $href = trim($node->nodeValue);
            if ($href === '' || str_starts_with($href, '#')) {
                continue;
            }

            $tail = substr($href, strlen($listingPath) + 1);

            // Skip pagination links like "/female-escorts-in-dubai/2"
            if ($tail === '' || ctype_digit($tail)) {
                continue;
            }

            // Skip any nested paths (real profile slugs have no further '/')
            if (str_contains($tail, '/')) {
                continue;
            }

            $links[] = $href;
        }

        return array_values(array_unique($links));
    }

    protected function normalizeUrl(string $href): string
    {
        if (Str::startsWith($href, 'http')) {
            return $href;
        }

        return rtrim($this->baseUrl, '/') . '/' . ltrim($href, '/');
    }

    protected function parseProfile(string $url, string $html): array
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $externalId = $this->extractExternalId($url);
        $name = $this->getFirstText($xpath, [
            '//h1',
            '//h2',
            '//*[contains(@class, "profile-name")]',
        ]);
        $age = $this->getFirstText($xpath, [
            '//*[contains(text(), "Age")]/following-sibling::*',
            '//*[contains(@class, "age")]',
        ]);
        $city = $this->getFirstText($xpath, [
            '//*[contains(text(), "City")]/following-sibling::*',
            '//*[contains(@class, "location")]',
        ]);
        $phone = $this->getFirstText($xpath, [
            '//*[contains(@class, "phone")]',
            '//*[contains(text(), "Phone")]/following-sibling::*',
        ]);
        // Try to extract an email address. MR doesn't expose profile emails
        // anywhere, but some descriptions or external sites might. We prefer
        // explicit mailto: links over regex-scans-of-the-page (which would
        // otherwise pick up the logged-in user's own email from the nav bar).
        $email = '';
        $mailto = $this->getFirstAttribute($xpath, [
            '//a[starts-with(@href, "mailto:")]/@href',
        ]);
        if ($mailto) {
            $email = preg_replace('/^mailto:/i', '', trim($mailto));
            $email = preg_replace('/\?.*$/', '', $email);
        }

        // Discard the email if it matches the MR account we're logged into,
        // or any other email tagged as our scraper credentials in env. This
        // guards against false positives from the nav bar / footer.
        $excluded = array_filter([
            strtolower((string) $this->username),
            strtolower((string) env('MASSAGE_REPUBLIC_USERNAME', '')),
        ]);
        if ($email !== '' && in_array(strtolower($email), $excluded, true)) {
            $email = '';
        }
        $website = $this->getFirstAttribute($xpath, [
            '//a[contains(@href, "http") and contains(@href, "website")]',
            '//a[contains(text(), "Website")]/@href',
        ]);
        $gender = $this->getFirstText($xpath, [
            '//*[contains(text(), "Gender")]/following-sibling::*',
            '//*[contains(@class, "gender")]',
        ]);
        $rating = $this->getFirstText($xpath, [
            '//*[contains(@class, "rating")]',
        ]);
        $services = $this->getAllText($xpath, [
            '//*[contains(@class, "services")]//li',
            '//*[contains(text(), "Services")]/following-sibling::*',
        ]);
        $description = $this->getFirstText($xpath, [
            '//*[contains(@class, "description")]',
            '//*[contains(@class, "profile-description")]',
        ]);
        $imageUrls = $this->extractImageUrls($xpath);
        $attributes = $this->extractAttributes($xpath);
        $rates = $this->extractRates($html);

        return [
            'external_id' => $externalId,
            'profile_url' => $url,
            'name' => $name,
            'age' => $attributes['age'] ?? $this->extractNumber($age),
            'city' => $city,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'gender' => $attributes['gender'] ?? $gender,
            'rating' => $rating,
            'services' => implode(', ', array_filter($services)),
            'description' => $description,
            'image_urls' => $imageUrls,
            'attributes' => $attributes,
            'incall_price' => $rates['incall_price'] ?? null,
            'outcall_price' => $rates['outcall_price'] ?? null,
            'incall_currency' => $rates['incall_currency'] ?? null,
            'outcall_currency' => $rates['outcall_currency'] ?? null,
            'raw_html' => $this->cleanHtml($html),
            'scraped_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Extract <dt>label</dt><dd>value</dd> pairs from the profile attributes panel.
     * Returns lower-cased keys (orientation, height, ethnicity, bust, age,
     * smokes, hair_color, nationality, gender, ...) → trimmed text values.
     */
    protected function extractAttributes(\DOMXPath $xpath): array
    {
        $attributes = [];

        foreach ($xpath->query('//dt') as $dt) {
            $label = trim($dt->textContent);
            if ($label === '') continue;

            $dd = $dt->nextSibling;
            while ($dd && $dd->nodeName !== 'dd') {
                $dd = $dd->nextSibling;
            }
            if (! $dd) continue;

            $value = trim(preg_replace('/\s+/', ' ', $dd->textContent));
            if ($value === '') continue;

            $key = strtolower(rtrim($label, "?:"));
            $key = preg_replace('/\s+/', '_', $key);
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    /**
     * Pull "Incalls per hour from 1,500 AED" / "Outcalls per hour from 2,000 AED"
     * out of the raw HTML — they live as bare text nodes next to listing-price-label divs.
     */
    protected function extractRates(string $html): array
    {
        $rates = [];

        if (preg_match('/listing-price-label[^>]*>Incalls[^<]*<\/div>\s*([0-9,.]+)\s*([A-Z]{2,5})/i', $html, $m)) {
            $rates['incall_price'] = (float) str_replace(',', '', $m[1]);
            $rates['incall_currency'] = strtoupper($m[2]);
        }

        if (preg_match('/listing-price-label[^>]*>Outcalls[^<]*<\/div>\s*([0-9,.]+)\s*([A-Z]{2,5})/i', $html, $m)) {
            $rates['outcall_price'] = (float) str_replace(',', '', $m[1]);
            $rates['outcall_currency'] = strtoupper($m[2]);
        }

        return $rates;
    }

    protected function extractExternalId(string $url): string
    {
        $parts = explode('/', trim($url, '/'));
        return end($parts) ?: md5($url);
    }

    protected function getFirstText(\DOMXPath $xpath, array $queries): string
    {
        foreach ($queries as $query) {
            $node = $xpath->query($query)->item(0);
            if ($node) {
                $text = trim($node->textContent);
                if ($text !== '') {
                    return $text;
                }
            }
        }
        return '';
    }

    protected function getFirstAttribute(\DOMXPath $xpath, array $queries): string
    {
        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes->length > 0) {
                return trim($nodes->item(0)->nodeValue);
            }
        }
        return '';
    }

    protected function getAllText(\DOMXPath $xpath, array $queries): array
    {
        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes->length > 0) {
                $values = [];
                foreach ($nodes as $node) {
                    $values[] = trim($node->textContent);
                }
                return array_filter($values);
            }
        }
        return [];
    }

    protected function extractImageUrls(\DOMXPath $xpath): array
    {
        $nodes = $xpath->query('//img[contains(@src, ".jpg") or contains(@src, ".png") or contains(@src, ".jpeg")]/@src');
        $urls = [];

        foreach ($nodes as $node) {
            $src = trim($node->nodeValue);
            if ($src === '') continue;

            $url = $this->upgradeToOriginalSize($this->normalizeUrl($src));
            $urls[] = $url;
        }

        return array_values(array_unique($urls));
    }

    /**
     * MR's CloudFront serves four size variants distinguished by the filename suffix:
     * _basic (3KB), _listing (18KB), _premium (12KB), _original (~130KB).
     * The pages embed _listing thumbnails — rewrite them to _original so we
     * store the full-resolution image.
     */
    protected function upgradeToOriginalSize(string $url): string
    {
        if (! preg_match('#//[^/]*cloudfront\.net/#', $url)) {
            return $url;
        }

        return preg_replace('/_(basic|listing|premium)(\.(?:jpe?g|png|webp))$/i', '_original$2', $url);
    }

    protected function getCurlOptions(): array
    {
        $options = [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 30,
        ];

        if (! empty($this->hostIp)) {
            if (defined('CURLOPT_TLS_HOSTNAME')) {
                $options[CURLOPT_TLS_HOSTNAME] = $this->baseHost;
            }

            $ipAddresses = is_array($this->hostIp)
                ? $this->hostIp
                : preg_split('/\s*,\s*/', trim($this->hostIp));

            $resolve = [];
            foreach ($ipAddresses as $ipAddress) {
                if ($ipAddress !== '') {
                    $resolve[] = "{$this->baseHost}:443:{$ipAddress}";
                }
            }

            if (! empty($resolve)) {
                $options[CURLOPT_RESOLVE] = $resolve;
            }
        }

        $resolve = env('MASSAGE_REPUBLIC_CURL_RESOLVE');
        if (! empty($resolve)) {
            if (! is_array($resolve)) {
                $resolve = explode(',', $resolve);
            }

            $options[CURLOPT_RESOLVE] = array_map('trim', $resolve);
        }

        return $options;
    }

    protected function extractNumber(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value) ?: '';
    }

    protected function cleanHtml(string $html): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($html, '<p><a><br><div><span><img>')));
    }
}
