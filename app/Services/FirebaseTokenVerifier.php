<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verify Firebase Authentication ID tokens issued by the client SDK.
 *
 * The claim modal's SMS channel hands OTP delivery + verification to
 * Firebase Phone Auth in the browser. Once confirmationResult.confirm()
 * succeeds we ask the browser for user.getIdToken() and POST it here.
 * Without server-side verification a malicious user could POST any phone
 * number and hijack a profile — oobben's implementation skipped this
 * step; we do not.
 *
 * Verification:
 *   1. Fetch Google's rotating x509 public keys (cached ~5h).
 *   2. Pick the PEM matching the JWT header's kid, verify RS256.
 *   3. Check iss == https://securetoken.google.com/{project_id},
 *      aud == {project_id}, exp in the future, auth_time in the past.
 *   4. Return the decoded claims (phone_number, uid, ...).
 *
 * Config: services.firebase.project_id (required).
 */
class FirebaseTokenVerifier
{
    private const GOOGLE_CERTS_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
    private const CACHE_KEY = 'firebase_public_keys';
    // Google's certs rotate roughly every 6h. Cache 5h so we never miss a
    // rotation window while avoiding a fetch on every request.
    private const CACHE_TTL_SECONDS = 18000;
    // Small tolerance for clock skew between our server and Google.
    private const LEEWAY_SECONDS = 60;

    /**
     * Verify an ID token and return its claims. Throws on any failure —
     * the caller can convert it into a single 401/403 response.
     *
     * @return array{phone_number?: string, sub: string, iss: string, aud: string, exp: int, iat: int, auth_time?: int, firebase?: array}
     * @throws \RuntimeException
     */
    public function verify(string $idToken): array
    {
        $projectId = (string) config('services.firebase.project_id');
        if ($projectId === '') {
            throw new \RuntimeException('Firebase project id is not configured.');
        }

        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            throw new \RuntimeException('Malformed token.');
        }
        $header = json_decode($this->base64UrlDecode($parts[0]), true);
        if (!is_array($header) || empty($header['kid'])) {
            throw new \RuntimeException('Token header missing kid.');
        }

        $keys = $this->getKeys();
        if (!isset($keys[$header['kid']])) {
            // Force a refetch — kid rotation may have happened between
            // the cache write and this request.
            Cache::forget(self::CACHE_KEY);
            $keys = $this->getKeys();
            if (!isset($keys[$header['kid']])) {
                throw new \RuntimeException('Unknown token key id.');
            }
        }

        JWT::$leeway = self::LEEWAY_SECONDS;
        try {
            $decoded = JWT::decode($idToken, new Key($keys[$header['kid']], 'RS256'));
        } catch (\Throwable $e) {
            throw new \RuntimeException('Invalid token: ' . $e->getMessage(), 0, $e);
        }

        $claims = (array) $decoded;

        $expectedIss = 'https://securetoken.google.com/' . $projectId;
        if (($claims['iss'] ?? null) !== $expectedIss) {
            throw new \RuntimeException('Token issuer mismatch.');
        }
        if (($claims['aud'] ?? null) !== $projectId) {
            throw new \RuntimeException('Token audience mismatch.');
        }
        if (empty($claims['sub']) || !is_string($claims['sub'])) {
            throw new \RuntimeException('Token subject missing.');
        }
        if (isset($claims['auth_time']) && $claims['auth_time'] > (time() + self::LEEWAY_SECONDS)) {
            throw new \RuntimeException('Token auth_time is in the future.');
        }

        return $claims;
    }

    /**
     * @return array<string, string> kid => PEM
     */
    protected function getKeys(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)->get(self::GOOGLE_CERTS_URL);
            if (!$response->successful()) {
                Log::warning('Firebase cert fetch non-2xx', ['status' => $response->status()]);
                return [];
            }
            $keys = (array) $response->json();
            Cache::put(self::CACHE_KEY, $keys, self::CACHE_TTL_SECONDS);
            return $keys;
        } catch (\Throwable $e) {
            Log::error('Firebase cert fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    protected function base64UrlDecode(string $input): string
    {
        $padded = str_pad($input, strlen($input) + (4 - strlen($input) % 4) % 4, '=');
        return (string) base64_decode(strtr($padded, '-_', '+/'));
    }
}
