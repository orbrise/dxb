<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\GeoDomain;

class GeoLocationService
{
    protected array $config;
    protected bool $useDatabase = false;

    public function __construct()
    {
        $this->config = config('geo_domains');
        
        // Check if database table exists
        $this->useDatabase = Cache::remember('geo_use_database', 3600, function () {
            try {
                return Schema::hasTable('geo_domains') && GeoDomain::count() > 0;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Get country code from IP address
     */
    public function getCountryFromIp(string $ip): ?string
    {
        // Skip for local IPs
        if ($this->isLocalIp($ip)) {
            return null;
        }

        $cacheKey = "geo_country_{$ip}";
        $cacheTtl = $this->config['cache_ttl'] ?? 60;

        return Cache::remember($cacheKey, now()->addMinutes($cacheTtl), function () use ($ip) {
            return $this->lookupCountry($ip);
        });
    }

    /**
     * Lookup country using configured driver
     */
    protected function lookupCountry(string $ip): ?string
    {
        $driver = $this->config['geoip_driver'] ?? 'ip-api';

        return match ($driver) {
            'ip-api' => $this->lookupViaIpApi($ip),
            'maxmind' => $this->lookupViaMaxMind($ip),
            'cloudflare' => $this->getFromCloudflareHeader(),
            'header' => $this->getFromCustomHeader(),
            default => $this->lookupViaIpApi($ip),
        };
    }

    /**
     * Lookup using IP-API (free service)
     */
    protected function lookupViaIpApi(string $ip): ?string
    {
        try {
            $endpoint = $this->config['ip_api']['endpoint'] ?? 'http://ip-api.com/json/';
            $response = Http::timeout(5)->get($endpoint . $ip);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'success') {
                    return $data['countryCode'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::warning('GeoIP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Lookup using MaxMind database
     */
    protected function lookupViaMaxMind(string $ip): ?string
    {
        try {
            $databasePath = $this->config['maxmind']['database_path'] ?? null;
            
            if (!$databasePath || !file_exists($databasePath)) {
                Log::warning('MaxMind database not found', ['path' => $databasePath]);
                return null;
            }

            // Requires geoip2/geoip2 package
            if (class_exists('\GeoIp2\Database\Reader')) {
                $reader = new \GeoIp2\Database\Reader($databasePath);
                $record = $reader->country($ip);
                return $record->country->isoCode;
            }
        } catch (\Exception $e) {
            Log::warning('MaxMind lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get country from Cloudflare header
     */
    protected function getFromCloudflareHeader(): ?string
    {
        $header = $this->config['cloudflare']['country_header'] ?? 'CF-IPCountry';
        return request()->header($header);
    }

    /**
     * Get country from custom header
     */
    protected function getFromCustomHeader(): ?string
    {
        $header = $this->config['header']['country_header'] ?? 'X-Country-Code';
        return request()->header($header);
    }

    /**
     * Check if IP is local/private
     */
    protected function isLocalIp(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost']) 
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.');
    }

    /**
     * Get domain for a country code
     */
    public function getDomainForCountry(string $countryCode): ?string
    {
        $countryCode = strtoupper($countryCode);
        
        // Try database first if available
        if ($this->useDatabase) {
            $domain = GeoDomain::getActiveDomainForCountry($countryCode);
            if ($domain) {
                return $domain;
            }
        }
        
        // Fallback to config
        $domains = $this->config['domains'] ?? [];

        if (isset($domains[$countryCode]) && ($domains[$countryCode]['active'] ?? false)) {
            return $domains[$countryCode]['domain'];
        }

        return null;
    }

    /**
     * Get country code from domain
     */
    public function getCountryFromDomain(string $domain): ?string
    {
        // Try database first if available
        if ($this->useDatabase) {
            $country = GeoDomain::getCountryFromDomain($domain);
            if ($country) {
                return $country;
            }
        }
        
        // Fallback to config
        $domains = $this->config['domains'] ?? [];
        
        foreach ($domains as $countryCode => $domainConfig) {
            if (($domainConfig['domain'] ?? '') === $domain) {
                return $countryCode;
            }
        }

        return null;
    }

    /**
     * Check if a domain exists and is active
     */
    public function isDomainActive(string $domain): bool
    {
        // Try database first if available
        if ($this->useDatabase) {
            if (GeoDomain::isDomainActive($domain)) {
                return true;
            }
        }
        
        // Fallback to config
        $domains = $this->config['domains'] ?? [];
        
        foreach ($domains as $domainConfig) {
            if (($domainConfig['domain'] ?? '') === $domain && ($domainConfig['active'] ?? false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get main/fallback domain
     */
    public function getMainDomain(): string
    {
        return $this->config['main_domain'] ?? 'massagerepublic.com.co';
    }

    /**
     * Get all active domains
     */
    public function getActiveDomains(): array
    {
        // Try database first if available
        if ($this->useDatabase) {
            $dbDomains = GeoDomain::getAllActiveDomains();
            if (!empty($dbDomains)) {
                return $dbDomains;
            }
        }
        
        // Fallback to config
        $domains = $this->config['domains'] ?? [];
        $activeDomains = [];

        foreach ($domains as $countryCode => $domainConfig) {
            if ($domainConfig['active'] ?? false) {
                $activeDomains[$countryCode] = $domainConfig['domain'];
            }
        }

        return $activeDomains;
    }

    /**
     * Build redirect URL with same path
     */
    public function buildRedirectUrl(string $targetDomain, ?string $path = null, ?string $query = null): string
    {
        $scheme = ($this->config['use_https'] ?? true) ? 'https' : 'http';
        $url = "{$scheme}://{$targetDomain}";

        if ($path) {
            $url .= '/' . ltrim($path, '/');
        }

        if ($query) {
            $url .= '?' . $query;
        }

        return $url;
    }

    /**
     * Extract subdomain prefix from domain
     * e.g., 'pk.massagerepublic.com.co' => 'pk'
     */
    public function extractSubdomain(string $domain): ?string
    {
        $mainDomain = $this->getMainDomain();
        
        if ($domain === $mainDomain) {
            return null;
        }

        // Check if it's a subdomain of the main domain
        if (str_ends_with($domain, '.' . $mainDomain)) {
            $subdomain = str_replace('.' . $mainDomain, '', $domain);
            return $subdomain ?: null;
        }

        // For domains like ae.massagerepublic.com.co
        $parts = explode('.', $domain);
        if (count($parts) > 3) {
            return $parts[0];
        }

        return null;
    }
}
