<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
    /** @var bool  True when session cookies were successfully loaded from disk */
    protected bool $sessionSeeded = false;
    /** @var bool  True when routing through Bright Data (or similar) HTTP proxy */
    protected bool $proxyEnabled = false;
    /** @var string|null  The full proxy URL currently in use (for diagnostics) */
    protected ?string $proxyUrl = null;
    /** @var string|null  Per-instance session ID used for Bright Data sticky-IP mode */
    protected ?string $proxySessionId = null;
    /** @var bool  True when routing through Bright Data's HTTPS API (port 443, works
     *             on hosts that block Bright Data's proxy ports 22225/33335/44445). */
    protected bool $brightDataApiEnabled = false;
    protected ?string $brightDataApiKey = null;
    protected string $brightDataZone = 'web_unlocker1';
    /** @var string|null  Per-instance session ID used for Bright Data API sticky-session
     *                    mode. Passed as `session_id` on every request so BD keeps the
     *                    same residential IP + cookie jar across the whole scrape. */
    protected ?string $brightDataSessionId = null;

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

        // cf_clearance is bound to (source IP, User-Agent). When loading cookies
        // from a browser session (MASSAGE_REPUBLIC_USE_SESSION_FILE=true), the
        // Guzzle UA MUST match the browser that solved the challenge — otherwise
        // Cloudflare rejects every request. Allow override via env so the
        // operator can paste in the exact UA string from their Chrome
        // (navigator.userAgent in DevTools console, or chrome://version).
        $userAgent = trim((string) env('MASSAGE_REPUBLIC_USER_AGENT', ''))
            ?: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';

        $headers = [
            'User-Agent' => $userAgent,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ];

        $this->cookies = new CookieJar();
        $this->defaultHeaders = $headers;

        // Base Guzzle config — shared by both proxy and direct modes.
        $guzzleConfig = [
            'cookies' => $this->cookies,
            'timeout' => 60, // longer timeout — proxy adds ~2-10s per request
            'verify' => false,
            'allow_redirects' => ['max' => 10, 'referer' => true, 'protocols' => ['http', 'https']],
            'headers' => $headers,
            'expect' => false,
            'http_errors' => false,
        ];

        // Three request-transport modes, in priority order:
        //
        //   1. Bright Data API mode (MASSAGE_REPUBLIC_BRIGHTDATA_API_KEY set)
        //      Every request is wrapped as POST https://api.brightdata.com/request
        //      via a Guzzle middleware. Uses port 443 so it works on hosts
        //      whose outbound firewall blocks Bright Data's proxy ports
        //      (22225 / 33335 / 44445). This is what the cPanel deployment
        //      uses — proxy mode is refused by the host.
        //
        //   2. Bright Data proxy mode (MASSAGE_REPUBLIC_PROXY_URL set, no API key)
        //      Guzzle routes through the proxy directly. Cleaner but requires
        //      outbound access to Bright Data's non-standard proxy ports.
        //
        //   3. Direct mode (neither set)
        //      Straight Guzzle to MR. Cloudflare will 403 it — kept only
        //      because the session-file cookie-priming code still exists,
        //      and for the health-check flow.
        //
        // In both Bright Data modes we sticky-session so the whole scrape
        // runs from ONE residential IP (BD-side cookie jar in API mode,
        // -session-<id> username suffix in proxy mode). Without stickiness
        // MR would issue _session_id on login from IP A and then reject
        // subsequent GETs coming out of IP B.
        $bdApiKey = trim((string) env('MASSAGE_REPUBLIC_BRIGHTDATA_API_KEY', ''));
        $rawProxyUrl = trim((string) env('MASSAGE_REPUBLIC_PROXY_URL', ''));

        if ($bdApiKey !== '') {
            $this->brightDataApiKey = $bdApiKey;
            $this->brightDataZone = trim((string) env('MASSAGE_REPUBLIC_BRIGHTDATA_ZONE', 'web_unlocker1'));
            // Kept for diagnostic display in mr:test-session. Web Unlocker API
            // mode is stateless — this id isn't sent to BD (session_id isn't a
            // valid API param), it's just an instance-scoped identifier.
            $this->brightDataSessionId = 'mr' . substr(md5(uniqid('', true)), 0, 8);
            $this->brightDataApiEnabled = true;

            $guzzleConfig['handler'] = $this->buildBrightDataApiHandler();
        } elseif ($rawProxyUrl !== '') {
            $this->proxySessionId = 'mr' . substr(md5(uniqid('', true)), 0, 8);
            // Splice `-session-<id>` into the proxy username so Bright Data
            // pins all requests to the same residential IP for this instance.
            // Format: http://brd-customer-hl_XXX-zone-YYY:PASS@brd.superproxy.io:PORT
            $stickyProxy = preg_replace(
                '/^(https?:\/\/brd-customer-hl_[a-z0-9]+-zone-[a-z0-9_]+)(?=:)/i',
                '$1-session-' . $this->proxySessionId,
                $rawProxyUrl
            );
            $guzzleConfig['proxy'] = $stickyProxy ?: $rawProxyUrl;
            $this->proxyUrl = $guzzleConfig['proxy'];
            $this->proxyEnabled = true;
        } else {
            // Only apply CURL_RESOLVE and MASSAGE_REPUBLIC_HOST_IP tricks in
            // direct-connection mode. When proxying, the proxy resolves the
            // target host upstream and any local resolve override just breaks
            // things (the CONNECT tunnel would target the wrong IP).
            $guzzleConfig['curl'] = $this->getCurlOptions();
        }

        $this->guzzle = new GuzzleClient($guzzleConfig);

        $httpFactoryOpts = [
            'cookies' => $this->cookies,
            'timeout' => 60,
            'verify' => false,
            'expect' => false,
        ];
        if ($this->brightDataApiEnabled) {
            // Route Laravel HTTP facade calls through the same BD API middleware
            // so fetchUrl() (which uses Http::) is transported identically to
            // login() / probeListing() (which use Guzzle directly).
            $httpFactoryOpts['handler'] = $this->buildBrightDataApiHandler();
        } elseif ($this->proxyEnabled) {
            $httpFactoryOpts['proxy'] = $this->proxyUrl;
        } else {
            $httpFactoryOpts['curl'] = $this->getCurlOptions();
        }
        $this->client = Http::withOptions($httpFactoryOpts)->withHeaders($headers);

        // If MASSAGE_REPUBLIC_USE_SESSION_FILE=true AND we're NOT proxying,
        // seed cookies from storage/app/mr-session.json before the first
        // request. With the proxy on, we let Bright Data handle CF on every
        // request — no cookie priming needed, login runs fresh.
        if (! $this->proxyEnabled
            && filter_var(env('MASSAGE_REPUBLIC_USE_SESSION_FILE', false), FILTER_VALIDATE_BOOLEAN)
        ) {
            $this->sessionSeeded = $this->loadSessionFile();
        }
    }

    protected ?string $lastLoginError = null;

    public function getLastLoginError(): ?string
    {
        return $this->lastLoginError;
    }

    /**
     * Attempt to authenticate — session-file short-circuit first, credential
     * login second. Exposed so the mr:test-session command can trigger auth
     * without running the full scrape().
     */
    public function attemptLogin(): bool
    {
        return $this->login();
    }

    /**
     * Hit the listing path and return the raw HTML body. Used by the health
     * check to prove the session cookies are actually working — if we get
     * Cloudflare's challenge back, the body will contain "Just a moment".
     */
    public function probeListing(): array
    {
        $url = "{$this->baseUrl}{$this->listingPath}";
        try {
            $res = $this->guzzle->request('GET', $url);
        } catch (\Throwable $e) {
            return ['ok' => false, 'status' => 0, 'body' => '', 'error' => $e->getMessage(), 'url' => $url];
        }
        $body = (string) $res->getBody();
        return [
            'ok' => $res->getStatusCode() < 400 && ! str_contains($body, 'Just a moment'),
            'status' => $res->getStatusCode(),
            'body' => $body,
            'error' => null,
            'url' => $url,
        ];
    }

    public function isSessionSeeded(): bool
    {
        return $this->sessionSeeded;
    }

    public function isProxyEnabled(): bool
    {
        return $this->proxyEnabled;
    }

    public function isBrightDataApiEnabled(): bool
    {
        return $this->brightDataApiEnabled;
    }

    /**
     * Return a short human description of the BD API config in use — the zone
     * name, the sticky session id, and a masked key prefix. Used by the
     * mr:test-session health-check output.
     */
    public function getBrightDataApiDescription(): ?string
    {
        if (! $this->brightDataApiEnabled) {
            return null;
        }
        $keyPreview = $this->brightDataApiKey
            ? substr($this->brightDataApiKey, 0, 8) . '…'
            : '(no key)';
        return "zone={$this->brightDataZone}  session_id={$this->brightDataSessionId}  key={$keyPreview}";
    }

    /**
     * Return the proxy URL with password masked, for safe logging.
     */
    public function getProxyDescription(): ?string
    {
        if (! $this->proxyEnabled || ! $this->proxyUrl) {
            return null;
        }
        // Strip password from userinfo so we don't log it. Keeps everything
        // else intact for diagnostic clarity.
        return preg_replace('/(:\/\/[^:]+:)[^@]+(@)/', '$1***$2', $this->proxyUrl);
    }

    /**
     * Build a Guzzle handler stack that rewrites every outbound request as a
     * POST to Bright Data's Web Unlocker API (https://api.brightdata.com/request)
     * with `format=json`, then unpacks the JSON body into a proper PSR-7
     * response carrying MR's actual status / headers / body.
     *
     * Why this shape rather than a proxy: on hosts that block Bright Data's
     * non-standard proxy ports (22225 / 33335 / 44445 refused by the cPanel
     * outbound firewall), API mode over 443 is the only path that works. The
     * middleware lets us keep every callsite in the scraper unchanged — they
     * still `$this->guzzle->request('GET', '/foo')` and get MR's response back.
     *
     * Session state: Web Unlocker API is stateless (unlike proxy mode, no
     * `session_id` param — the API rejects it with a validation error).
     * Instead we rely on Guzzle's built-in cookies middleware (installed by
     * HandlerStack::create()): our wrap middleware sits ABOVE cookies in the
     * stack, so cookies gets first crack at outgoing (attaches Cookie header
     * from the jar to the original mr.com request) and last crack at incoming
     * (extracts Set-Cookie from the unwrapped MR response we build below).
     * This is why `format=json` matters — `format=raw` returns only body,
     * so MR's Set-Cookie would never make it back to the jar and every request
     * would be logged out.
     */
    protected function buildBrightDataApiHandler(): HandlerStack
    {
        // create() pre-loads cookies + prepare_body + allow_redirects + http_errors.
        // We push our BD-wrap AFTER them so cookies runs FIRST on outgoing (sees
        // the original MR URI, attaches Cookie header) and LAST on incoming (sees
        // the unwrapped MR response we synthesize, extracts Set-Cookie).
        $handler = HandlerStack::create();

        $apiKey = $this->brightDataApiKey;
        $zone = $this->brightDataZone;

        $handler->push(function (callable $next) use ($apiKey, $zone) {
            return function (RequestInterface $request, array $options) use ($next, $apiKey, $zone) {
                $targetUrl = (string) $request->getUri();

                // Recursion guard — if a request already targets BD (shouldn't
                // happen, but belt-and-suspenders) pass through unwrapped.
                if (str_starts_with($targetUrl, 'https://api.brightdata.com')) {
                    return $next($request, $options);
                }

                // Rebuild the headers into a flat name=>string map. Skip Host
                // and Content-Length: BD sets its own for the outer POST and
                // the upstream target's Host is implicit from the `url` field.
                // Cookie header (if any) IS forwarded — that's how MR's session
                // survives across our stateless BD API calls.
                $headers = [];
                foreach ($request->getHeaders() as $name => $values) {
                    $lower = strtolower($name);
                    if ($lower === 'host' || $lower === 'content-length') continue;
                    $headers[$name] = implode(', ', $values);
                }

                $body = (string) $request->getBody();

                $payload = [
                    'zone' => $zone,
                    'url' => $targetUrl,
                    'method' => $request->getMethod(),
                    'format' => 'json',
                ];
                if (! empty($headers)) $payload['headers'] = $headers;
                if ($body !== '') $payload['body'] = $body;

                $bdRequest = new Psr7Request(
                    'POST',
                    'https://api.brightdata.com/request',
                    [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    json_encode($payload)
                );

                return $next($bdRequest, $options)->then(function (ResponseInterface $bdResponse) use ($targetUrl) {
                    $bdBody = (string) $bdResponse->getBody();
                    $decoded = json_decode($bdBody, true);

                    // BD-side error (bad auth, insufficient balance, validation,
                    // 502 gateway) — the outer response won't have the
                    // {status_code, headers, body} shape. Log full context so
                    // operators can see what BD is complaining about (empty
                    // body + 502 status alone is uninformative), then pass the
                    // BD response through so the caller sees BD's status.
                    if (! is_array($decoded) || (! isset($decoded['status_code']) && ! isset($decoded['status']) && ! isset($decoded['body']))) {
                        $bdHeadersFlat = [];
                        foreach ($bdResponse->getHeaders() as $name => $values) {
                            $bdHeadersFlat[$name] = implode(', ', $values);
                        }
                        \Log::warning('BD Web Unlocker API returned non-target response', [
                            'target_url' => $targetUrl,
                            'bd_status' => $bdResponse->getStatusCode(),
                            'bd_headers' => $bdHeadersFlat,
                            'bd_body_snippet' => substr($bdBody, 0, 800),
                        ]);
                        return $bdResponse;
                    }

                    $status = (int) ($decoded['status_code'] ?? $decoded['status'] ?? 200);
                    $rawHeaders = $decoded['headers'] ?? [];
                    $rawBody = $decoded['body'] ?? '';

                    // Normalize header values into PSR-7 shape (each header is
                    // an array of strings). BD sometimes returns single-value
                    // headers as bare strings and multi-value (like Set-Cookie)
                    // as arrays — handle both.
                    $psrHeaders = [];
                    foreach ($rawHeaders as $name => $value) {
                        $psrHeaders[$name] = is_array($value) ? array_map('strval', $value) : [(string) $value];
                    }

                    return new Psr7Response($status, $psrHeaders, is_string($rawBody) ? $rawBody : (string) $rawBody);
                });
            };
        }, 'brightdata_api_wrap');

        return $handler;
    }

    /**
     * Return a summary of the cookies currently in the jar — name, domain,
     * expiry (or "session"), and a value-length rather than the raw value
     * so we don't accidentally log secrets. Used by the health check to
     * confirm whether the critical cookies (cf_clearance, _session_id) are
     * actually loaded.
     */
    public function describeCookies(): array
    {
        $out = [];
        foreach ($this->cookies->toArray() as $c) {
            $out[] = [
                'name' => $c['Name'] ?? '?',
                'domain' => $c['Domain'] ?? '?',
                'expires' => $c['Expires'] ?? 0,
                'expires_iso' => (! empty($c['Expires']) && $c['Expires'] > 0)
                    ? gmdate('Y-m-d H:i:s', $c['Expires']) . ' UTC'
                    : 'session',
                'value_len' => strlen((string) ($c['Value'] ?? '')),
                'secure' => (bool) ($c['Secure'] ?? false),
                'http_only' => (bool) ($c['HttpOnly'] ?? false),
            ];
        }
        return $out;
    }

    /**
     * Return the User-Agent Guzzle is currently sending. cf_clearance is
     * bound to (IP, User-Agent) — if the UA that solved the challenge in
     * your browser doesn't match this, Cloudflare will always reject.
     */
    public function getUserAgent(): string
    {
        return $this->defaultHeaders['User-Agent'] ?? '';
    }

    /**
     * Read storage/app/mr-session.json and inject each cookie into Guzzle's
     * CookieJar. The file format matches Cookie-Editor's default export:
     *
     * [{"name": "cf_clearance", "value": "...", "domain": ".massagerepublic.com",
     *   "path": "/", "expirationDate": 1234567890, "secure": true,
     *   "httpOnly": true, "sameSite": "Lax"}, ...]
     *
     * Session cookies (no expirationDate) are accepted with expires=0.
     * Returns true if at least one cookie loaded successfully.
     */
    protected function loadSessionFile(): bool
    {
        $path = storage_path('app/mr-session.json');

        if (! is_file($path)) {
            $this->lastLoginError = "Session file not found at {$path}";
            return false;
        }

        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            $this->lastLoginError = "Session file at {$path} is empty or unreadable";
            return false;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            $this->lastLoginError = "Session file at {$path} is not valid JSON";
            return false;
        }

        $loaded = 0;
        $now = time();
        foreach ($decoded as $cookie) {
            if (! is_array($cookie) || empty($cookie['name']) || ! isset($cookie['value'])) {
                continue;
            }

            $expires = 0;
            if (isset($cookie['expirationDate'])) {
                $expires = (int) $cookie['expirationDate'];
                // Silently skip already-expired cookies rather than injecting
                // them and having Guzzle send stale data.
                if ($expires > 0 && $expires < $now) {
                    continue;
                }
            }

            $setCookie = new SetCookie();
            $setCookie->setName((string) $cookie['name']);
            $setCookie->setValue((string) $cookie['value']);
            $setCookie->setDomain($cookie['domain'] ?? $this->baseHost);
            $setCookie->setPath($cookie['path'] ?? '/');
            $setCookie->setSecure((bool) ($cookie['secure'] ?? false));
            $setCookie->setHttpOnly((bool) ($cookie['httpOnly'] ?? false));
            if ($expires > 0) {
                $setCookie->setExpires($expires);
            }
            $this->cookies->setCookie($setCookie);
            $loaded++;
        }

        if ($loaded === 0) {
            $this->lastLoginError = "Session file at {$path} contained no valid cookies";
            return false;
        }

        return true;
    }

    public function scrape(int $limit = 50, ?string $citySlug = null, ?callable $skipChecker = null): array
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
        $skippedAgencies = 0;
        $skippedAlreadyImported = 0;
        $seenHrefs = [];

        // MR / Cloudflare rate-limits rapid sequential profile fetches; without
        // a pause we get ~3 profiles in, then everything 4xx/5xx silently.
        $delayMs = (int) (env('MASSAGE_REPUBLIC_FETCH_DELAY_MS', 1500));

        while (count($profiles) < $limit) {
            $html = $this->fetchProfileListPage($page);
            $cards = $this->extractProfileCards($html);

            if (empty($cards)) {
                break;
            }

            // VIP spots repeat on every listing page, so drop cards we've
            // already processed and break when a page adds nothing new.
            $newCards = [];
            foreach ($cards as $card) {
                if (isset($seenHrefs[$card['href']])) continue;
                $seenHrefs[$card['href']] = true;
                $newCards[] = $card;
            }
            if (empty($newCards)) {
                break;
            }

            foreach ($newCards as $card) {
                if (count($profiles) >= $limit) {
                    break 2;
                }

                $url = $this->normalizeUrl($card['href']);

                // Skip cards whose external_id is already imported so that
                // repeated --limit=N runs advance further down the listing
                // instead of re-scraping the same top rows. The check runs
                // BEFORE the detail-page fetch to save the network round-trip.
                if ($skipChecker !== null) {
                    $externalId = $this->extractExternalId($url);
                    if ($externalId !== '' && $skipChecker($externalId)) {
                        $skippedAlreadyImported++;
                        continue;
                    }
                }

                $html = $this->fetchUrlWithRetry($url);

                if (! $html) {
                    $fetchFailures[] = $url;
                    if ($delayMs > 0) usleep($delayMs * 1000);
                    continue;
                }

                $profile = $this->parseProfile($url, $html);

                if (! empty($profile['external_id'])) {
                    // Listing-card flags are authoritative — MR puts the
                    // verified badge and "premium" class on the card wrapper,
                    // not on the detail page, so prefer those values here.
                    $profile['is_verified'] = $card['is_verified'] || ($profile['is_verified'] ?? false);
                    $profile['is_premium'] = $card['is_premium'];

                    // Skip paid-sponsored agency listings — MR puts them at the
                    // top of every listing page, but the user wants actual latest
                    // individual ads, not promoted multi-model agencies.
                    if ($this->looksLikeAgency($profile['name'] ?? '')) {
                        $skippedAgencies++;
                        if ($delayMs > 0) usleep($delayMs * 1000);
                        continue;
                    }
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

        if ($skippedAgencies > 0) {
            \Log::info('MR scraper: agency listings skipped', ['count' => $skippedAgencies]);
        }

        if ($skippedAlreadyImported > 0) {
            \Log::info('MR scraper: already-imported cards skipped', ['count' => $skippedAlreadyImported]);
        }

        return $profiles;
    }

    /**
     * MR mixes paid agency promos into the standard listing — they sit at the
     * top of each page regardless of recency. Detect by naming convention so
     * the scraper can skip them and keep paginating for genuine individuals.
     *
     * False-positive risk: a stage name containing "Agency"/"Models"/"Hub" as
     * a real individual would also get filtered. Acceptable trade-off given
     * how rare that is on MR.
     */
    protected function looksLikeAgency(string $name): bool
    {
        if ($name === '') return false;

        $patterns = [
            '/escort\s+agency/i',     // tail: "Russian escort agency in Dubai"
            '/\bagency\b/i',          // "Aura Agency", "Playgirl Agency"
            '/\bmodels\b/i',          // "Freya Models", "100+ Slavic models"
            '/\bhub\b/i',             // "Dream Hub"
            '/best\s+girls/i',        // "Lady for Daddy Best Girls"
            '/search\s*bot/i',        // "Escort Search Bot"
            '/\bstudio\b/i',
            '/\bcollective\b/i',
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $name)) {
                return true;
            }
        }
        return false;
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
        // Session-cookie path: skip the login round-trip entirely when
        // storage/app/mr-session.json was loaded successfully. Cloudflare
        // blocks the Guzzle GET /sign-in with a JS challenge, so this is
        // currently the only working authentication path. If the cookies
        // have expired since the file was written we'll find out on the
        // next real request (403 or "sign-in" page in the HTML body) and
        // the operator re-exports.
        if ($this->sessionSeeded) {
            return true;
        }

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
            $transport = $this->proxyEnabled ? ' through Bright Data proxy' : ($this->brightDataApiEnabled ? ' through Bright Data API' : '');
            $this->lastLoginError = "GET {$loginUrl} returned HTTP {$getStatus}{$transport}: {$snippet}";
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
            $transport = $this->proxyEnabled ? ' through Bright Data proxy' : ($this->brightDataApiEnabled ? ' through Bright Data API' : '');
            $this->lastLoginError = "POST {$postUrl} returned HTTP {$status}{$transport}: {$snippet}";
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

    /**
     * Parse the listing page into structured per-card data — the profile URL,
     * plus the verified badge + premium flag MR exposes only on listing cards
     * (the detail page doesn't repeat them, which is why a detail-page scan
     * for "verified" always returned zero).
     *
     * MR renders two blocks on each listing page:
     *   1. Auction spots — a <div class="listings listings-spots …"> section
     *                holding <div class="listing-li listing-li--spot …"> cards.
     *                These are paid auction slots (agency ads) we deliberately
     *                skip.
     *   2. Ranked profiles — a <div class="listings border-top"> section
     *                holding <div class="listing-li listing-li--flex …"> cards,
     *                already ordered VIP → Featured → Basic by MR.
     * We match only listing-li--flex so imports come from the ranked list and
     * the top-of-page auction slots are ignored.
     *
     * @return array<int, array{href:string, is_verified:bool, is_premium:bool}>
     */
    protected function extractProfileCards(string $html): array
    {
        if (empty($html)) {
            return [];
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $cards = [];
        $seen = [];

        $cardNodes = $xpath->query(
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' listing-li--flex ')]"
        );
        // Match any /female-escorts-in-<city>/<slug> or /male-escorts-in-<city>/<slug>.
        // Not tied to $this->listingPath because MR redirects some slugs
        // (e.g. dubai stays but delhi → new-delhi in the actual href).
        $profileHrefRegex = '#^/(?:fe)?male-escorts-in-[a-z0-9-]+/[a-z0-9][a-z0-9-]*$#i';
        foreach ($cardNodes as $card) {
            $href = null;
            foreach ($xpath->query('.//a/@href', $card) as $hrefAttr) {
                $candidate = trim($hrefAttr->nodeValue);
                if ($candidate === '' || str_starts_with($candidate, '#')) continue;
                if (! preg_match($profileHrefRegex, $candidate)) continue;

                $href = $candidate;
                break;
            }
            if (! $href || isset($seen[$href])) continue;
            $seen[$href] = true;

            $verifiedHit = $xpath->query(
                ".//*[contains(concat(' ', normalize-space(@class), ' '), ' verified-image ')]",
                $card
            );
            $isVerified = $verifiedHit && $verifiedHit->length > 0;

            $cards[] = [
                'href' => $href,
                'is_verified' => $isVerified,
                'is_premium' => true,
            ];
        }

        return $cards;
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
        $isVerified = $this->extractIsVerified($xpath, $html);

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
            'is_verified' => $isVerified,
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
     * MR marks reviewed profiles with a "Verified" badge — typically a
     * <span class="verified-image"> wrapper holding text like "Verified photos"
     * or "Photos Verified by Evoory". Detect via class first, then
     * fall back to title/alt text in case MR rotates the markup.
     */
    protected function extractIsVerified(\DOMXPath $xpath, string $html): bool
    {
        $classQueries = [
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' verified-image ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' verified-badge ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' is-verified ')]",
            "//*[contains(@class, 'mr-verified')]",
        ];
        foreach ($classQueries as $q) {
            if ($xpath->query($q)->length > 0) {
                return true;
            }
        }

        // Text-based fallbacks (titles, alts, badge labels).
        if (preg_match('/(Verified\s+by\s+Massage\s+Republic|Photos?\s+Verified|Identity\s+Verified|Verified\s+photos)/i', $html)) {
            return true;
        }

        return false;
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
