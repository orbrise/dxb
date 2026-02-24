<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\GeoLocationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class GeoRedirectMiddleware
{ 
    protected GeoLocationService $geoService;
    protected array $config;

    public function __construct(GeoLocationService $geoService)
    {
        $this->geoService = $geoService;
        $this->config = config('geo_domains');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if geo redirection is enabled (database setting takes priority)
        if (!$this->isGeoRedirectEnabled()) {
            return $next($request);
        }

        // Skip excluded paths
        if ($this->isExcludedPath($request)) {
            return $next($request);
        }

        // Skip bots unless configured otherwise
        if (!($this->config['redirect_bots'] ?? false) && $this->isBot($request)) {
            return $next($request);
        }

        // Get current domain
        $currentDomain = $request->getHost();
        $mainDomain = $this->geoService->getMainDomain();

        // Get user's country from IP
        $userCountry = $this->getUserCountry($request);

        if (!$userCountry) {
            return $next($request);
        }

        // Get the appropriate domain for user's country
        $targetDomain = $this->geoService->getDomainForCountry($userCountry);

        // If no specific domain exists for this country, use main domain
        if (!$targetDomain) {
            $targetDomain = $mainDomain;
        }

        // Check if user is already on the correct domain - no redirect needed
        if ($this->isSameDomain($currentDomain, $targetDomain)) {
            return $next($request);
        }

        // User is on wrong domain - check if we should redirect
        // Skip redirect only if cookie exists (user chose to stay on this domain)
        if ($this->hasRedirectCookie($request)) {
            return $next($request);
        }

        // Check if current domain is for a different country
        // This handles the case where a pk link is indexed but user is from UAE
        $currentDomainCountry = $this->geoService->getCountryFromDomain($currentDomain);
        
        if ($currentDomainCountry && $currentDomainCountry !== $userCountry) {
            // User is on wrong country domain, redirect to their country domain
            return $this->redirectToDomain($request, $targetDomain);
        }

        // If user is on main domain but should be on country domain
        if ($this->isMainDomain($currentDomain) && $targetDomain !== $mainDomain) {
            return $this->redirectToDomain($request, $targetDomain);
        }

        return $next($request);
    }

    /**
     * Get user's country from various sources
     */
    protected function getUserCountry(Request $request): ?string
    {
        // First check for Cloudflare header (fastest)
        $cfCountry = $request->header('CF-IPCountry');
        if ($cfCountry && $cfCountry !== 'XX') {
            return strtoupper($cfCountry);
        }

        // Check custom header (for testing or proxy setups)
        $customHeader = $this->config['header']['country_header'] ?? 'X-Country-Code';
        $headerCountry = $request->header($customHeader);
        if ($headerCountry) {
            return strtoupper($headerCountry);
        }

        // Fallback to IP lookup
        $ip = $request->ip();
        return $this->geoService->getCountryFromIp($ip);
    }

    /**
     * Redirect to target domain preserving path and query
     */
    protected function redirectToDomain(Request $request, string $targetDomain): Response
    {
        $path = $request->getPathInfo();
        $query = $request->getQueryString();

        $redirectUrl = $this->geoService->buildRedirectUrl($targetDomain, $path, $query);

        Log::info('Geo redirect', [
            'from' => $request->getHost(),
            'to' => $targetDomain,
            'country' => $this->getUserCountry($request),
            'path' => $path,
        ]);

        // Use 302 for geo redirects (not permanent)
        // Don't set cookie here - always redirect when on wrong domain
        return redirect($redirectUrl, 302);
    }

    /**
     * Check if request has redirect cookie
     */
    protected function hasRedirectCookie(Request $request): bool
    {
        $cookieName = $this->config['cookie_name'] ?? 'geo_redirect_checked';
        return $request->cookie($cookieName) !== null;
    }

    /**
     * Set checked cookie on response
     */
    protected function setCheckedCookie(Response $response): Response
    {
        $cookieName = $this->config['cookie_name'] ?? 'geo_redirect_checked';
        $cookieExpiry = $this->config['cookie_expiry'] ?? 1440;

        $cookie = cookie($cookieName, '1', $cookieExpiry, '/', null, true, true, false, 'Lax');
        
        return $response->withCookie($cookie);
    }

    /**
     * Check if path is excluded from redirection
     */
    protected function isExcludedPath(Request $request): bool
    {
        $excludedPaths = $this->config['excluded_paths'] ?? [];
        
        foreach ($excludedPaths as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request is from a bot/crawler
     */
    protected function isBot(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        $excludedAgents = $this->config['excluded_user_agents'] ?? [];

        foreach ($excludedAgents as $bot) {
            if (str_contains($userAgent, strtolower($bot))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if two domains are the same
     */
    protected function isSameDomain(string $domain1, string $domain2): bool
    {
        return strtolower($domain1) === strtolower($domain2);
    }

    /**
     * Check if domain is the main domain
     */
    protected function isMainDomain(string $domain): bool
    {
        $mainDomain = $this->geoService->getMainDomain();
        return $this->isSameDomain($domain, $mainDomain);
    }

    /**
     * Check if geo redirection is enabled
     * Database setting takes priority over config file
     */
    protected function isGeoRedirectEnabled(): bool
    {
        // Cache the setting for 5 minutes to reduce database queries
        return Cache::remember('geo_redirect_enabled', 300, function () {
            try {
                $setting = Setting::first();
                if ($setting && isset($setting->geo_redirect_enabled)) {
                    return (bool) $setting->geo_redirect_enabled;
                }
            } catch (\Exception $e) {
                // If database is not available, fall back to config
                Log::warning('Could not check geo_redirect_enabled from database: ' . $e->getMessage());
            }
            
            // Fall back to config setting
            return $this->config['enabled'] ?? false;
        });
    }
}
