<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * The visitor's IP and the country it maps to, as stored for registration
 * (users.registration_*), login (users.last_login_*) and profile create /
 * update (users_profiles.ip_* / updated_*), each also logged to
 * user_location_logs. Country names come from
 * ip-api.com, cached a week per IP: logins repeat the same IPs, and the free
 * tier allows 45 lookups a minute.
 */
class IpLocation
{
    public static function clientIp(): ?string
    {
        // Cloudflare's header first: it is the visitor even when the proxy
        // ranges in TrustProxies fall out of date.
        $cf = request()->header('CF-Connecting-IP');
        if ($cf && filter_var($cf, FILTER_VALIDATE_IP)) {
            return $cf;
        }

        return request()->ip();
    }

    /** Country name, 'Local' for private addresses, 'Unknown' if the lookup fails. */
    public static function country(?string $ip): string
    {
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'Local';
        }

        $cached = Cache::get("ip_country_name.{$ip}");
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,country']);
            if ($response->successful() && ($response->json('status') === 'success')) {
                $country = $response->json('country') ?: 'Unknown';
                Cache::put("ip_country_name.{$ip}", $country, now()->addWeek());
                return $country;
            }
        } catch (\Throwable $e) {
            Log::warning("IP country lookup failed for {$ip}: " . $e->getMessage());
        }

        // Not cached, so the next request for this IP tries again.
        return 'Unknown';
    }

    /**
     * Records the IP now and fills in the country after the response is sent,
     * so a slow ip-api.com never holds up a login or a save. Also adds the
     * row to user_location_logs (admin → User Locations).
     *
     * @param string $event register | login | profile_create | profile_update
     * @param (callable(string $ip, string $country): void)|null $store
     */
    public static function captureAfterResponse(int $userId, string $event, ?int $profileId = null, ?callable $store = null): void
    {
        $ip = self::clientIp();
        $userAgent = request()->userAgent();

        app()->terminating(function () use ($ip, $userAgent, $userId, $event, $profileId, $store) {
            try {
                $country = self::country($ip);
                self::log($userId, $event, $profileId, $ip, $country, $userAgent);
                if ($store) $store($ip, $country);
            } catch (\Throwable $e) {
                Log::warning('Storing IP location failed: ' . $e->getMessage());
            }
        });
    }

    /** Adds one row to user_location_logs; never throws. */
    public static function log(int $userId, string $event, ?int $profileId, ?string $ip, ?string $country, ?string $userAgent = null): void
    {
        try {
            DB::table('user_location_logs')->insert([
                'user_id'    => $userId,
                'profile_id' => $profileId,
                'event'      => $event,
                'ip'         => $ip,
                'country'    => $country,
                'user_agent' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("user_location_logs insert failed ({$event}, user {$userId}): " . $e->getMessage());
        }
    }
}
