<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\GoogleSignup;
use App\Models\MailSettings;

class GoogleController extends Controller
{
    /**
     * Get the client IP address
     */
    private function getClientIp()
    {
        $ip = request()->ip();
        
        // Check for proxy/load balancer headers
        if (request()->header('CF-Connecting-IP')) {
            $ip = request()->header('CF-Connecting-IP');
        } elseif (request()->header('X-Forwarded-For')) {
            $ips = explode(',', request()->header('X-Forwarded-For'));
            $ip = trim($ips[0]);
        } elseif (request()->header('X-Real-IP')) {
            $ip = request()->header('X-Real-IP');
        }
        
        return $ip;
    }
    
    /**
     * Get country from IP address
     */
    private function getCountryFromIp($ip)
    {
        try {
            // Skip for local/private IPs
            if (in_array($ip, ['127.0.0.1', '::1']) || 
                preg_match('/^(10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/', $ip)) {
                return 'Local';
            }
            
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return $data['country'] ?? null;
                }
            }
        } catch (Exception $e) {
            \Log::warning('Failed to get country from IP: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Returns true when the user has an avatar value AND the underlying file
     * actually exists on the public disk. The Google sign-in flow uses this to
     * decide whether to refetch the picture — a stale DB value pointing to a
     * deleted file (e.g. uploaded long ago and lost in a server migration) is
     * treated as "no avatar" so we can backfill from Google.
     */
    private function hasUsableAvatar($user): bool
    {
        if (empty($user->avatar)) {
            return false;
        }

        return Storage::disk('public')->exists($user->avatar);
    }

    /**
     * Download a Google profile picture and store it on the public disk under
     * `avatars/` so it works the same way as user-uploaded avatars (which are
     * stored via $request->file->store('avatars', 'public') in UserAccountEdit).
     *
     * Returns the relative path (e.g. `avatars/google-42-1718...jpg`) on success,
     * or null on any failure (timeout, non-200, IO error, etc.) — failures are
     * never fatal to the sign-in flow.
     */
    private function fetchAndStoreGoogleAvatar(?string $url, int $userId): ?string
    {
        if (!$url) {
            return null;
        }

        try {
            // Google avatar URLs end in `=s96-c` (96px square crop). Bump to
            // 400px so the stored copy is high enough resolution for the UI;
            // anything bigger gets downsized when displayed anyway.
            $sized = preg_replace('/=s\d+(-c)?$/', '=s400-c', $url);
            $fetchUrl = $sized ?: $url;

            $response = Http::timeout(10)->get($fetchUrl);
            if (!$response->successful()) {
                \Log::info('Google avatar fetch returned ' . $response->status(), [
                    'user_id' => $userId,
                    'url'     => $fetchUrl,
                ]);
                return null;
            }

            // Sniff extension from Content-Type since Google URLs omit it.
            $contentType = strtolower((string) $response->header('Content-Type'));
            $extension = match (true) {
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif')  => 'gif',
                default                            => 'jpg',
            };

            $filename = 'avatars/google-' . $userId . '-' . time() . '.' . $extension;
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (Exception $e) {
            \Log::warning('Google avatar fetch failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'url'     => $url,
            ]);
            return null;
        }
    }

    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        // Store the current domain in session so we can redirect back after OAuth
        session(['oauth_redirect_domain' => request()->getSchemeAndHttpHost()]);
        
        // Build dynamic redirect URL based on current domain
        $redirectUrl = request()->getSchemeAndHttpHost() . '/auth/google/callback';
        
        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Get the redirect URL that was used (from current domain)
            $redirectUrl = request()->getSchemeAndHttpHost() . '/auth/google/callback';
            
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->user();
            
            // Check if user already exists by Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // User exists, log them in
                // Update IP/country if not set
                if (empty($user->registration_ip) || empty($user->registration_country)) {
                    $ipAddress = $this->getClientIp();
                    $ipCountry = $this->getCountryFromIp($ipAddress);
                    $user->update([
                        'registration_ip' => $user->registration_ip ?: $ipAddress,
                        'registration_country' => $user->registration_country ?: $ipCountry,
                    ]);
                }

                // Backfill avatar from Google for accounts that signed up
                // before we started downloading the picture, AND for accounts
                // whose stored avatar file has gone missing (DB value points to
                // a file that no longer exists on disk — e.g. a stale upload
                // from a previous server). Never overwrites a working custom
                // upload because hasUsableAvatar() short-circuits when the
                // file is actually present.
                if (!$this->hasUsableAvatar($user)) {
                    $avatarPath = $this->fetchAndStoreGoogleAvatar($googleUser->getAvatar(), $user->id);
                    if ($avatarPath) {
                        $user->update(['avatar' => $avatarPath]);
                    }
                }

                Auth::login($user);
                
                if ($user->type == 1) {
                    if ($user->getprofile->count() > 0) {
                        return redirect()->to("my-profile/" . $user->name . "/" . $user->id)
                            ->with('success', 'Welcome back!');
                    }
                    return redirect()->route('new.profile')
                        ->with('info', 'Please complete your profile');
                }
                
                return redirect()->route('user.account')
                    ->with('success', 'Welcome back!');
            }
            
            // Check if email already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // Link Google account to existing user
                // Also update IP/country if not set
                $ipAddress = $this->getClientIp();
                $ipCountry = $this->getCountryFromIp($ipAddress);

                // If the existing account has no working avatar, take this
                // opportunity to grab the Google one — they're linking the
                // accounts so it's reasonable to pull the picture they signed
                // in with. Same hasUsableAvatar() check as the returning-user
                // branch so we also rescue stale paths to deleted files.
                $avatarPath = null;
                if (!$this->hasUsableAvatar($existingUser)) {
                    $avatarPath = $this->fetchAndStoreGoogleAvatar($googleUser->getAvatar(), $existingUser->id);
                }

                $existingUser->update(array_filter([
                    'google_id'             => $googleUser->getId(),
                    'registration_ip'       => $existingUser->registration_ip ?: $ipAddress,
                    'registration_country'  => $existingUser->registration_country ?: $ipCountry,
                    'avatar'                => $avatarPath ?: null,
                ], fn ($v) => !is_null($v)));
                
                \Log::info('Google account linked to existing user', [
                    'user_id' => $existingUser->id,
                    'email' => $existingUser->email,
                    'ip_address' => $ipAddress,
                    'ip_country' => $ipCountry,
                ]);
                
                Auth::login($existingUser);
                
                if ($existingUser->type == 1) {
                    if ($existingUser->getprofile->count() > 0) {
                        return redirect()->to("my-profile/" . $existingUser->name . "/" . $existingUser->id)
                            ->with('success', 'Google account linked successfully!');
                    }
                    return redirect()->route('new.profile')
                        ->with('info', 'Please complete your profile');
                }
                
                return redirect()->route('user.account')
                    ->with('success', 'Google account linked successfully!');
            }
            
            // Create new user
            $randomPassword = Str::random(8) . rand(10, 99); // e.g., "E$OLdn21"
            
            // Get IP and country
            $ipAddress = $this->getClientIp();
            $ipCountry = $this->getCountryFromIp($ipAddress);
            
            $newUser = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt($randomPassword), // Store the generated password
                'verified' => 1, // Google users are auto-verified
                'email_verified_at' => now(),
                'type' => 2, // Individual advertiser (default for Google sign-in)
                'status' => 'active',
                'registration_ip' => $ipAddress,
                'registration_country' => $ipCountry,
            ]);

            // Pull the Google profile picture and use it as the default avatar.
            // Done after User::create so we can use the real user id in the
            // filename. Failure here is non-fatal — the user just won't have
            // an avatar set, same as if they'd signed up with email only.
            $avatarPath = $this->fetchAndStoreGoogleAvatar($googleUser->getAvatar(), $newUser->id);
            if ($avatarPath) {
                $newUser->update(['avatar' => $avatarPath]);
            }

            \Log::info('New Google user created', [
                'user_id' => $newUser->id,
                'email' => $newUser->email,
                'password' => $randomPassword,
                'ip_address' => $ipAddress,
                'ip_country' => $ipCountry,
                'avatar' => $avatarPath,
            ]);
            
            // Send welcome email with password - ALWAYS send for new users
            try {
                Mail::to($newUser->email)->send(new GoogleSignup([
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'password' => $randomPassword,
                ]));
                \Log::info('Google signup email sent successfully', ['email' => $newUser->email]);
            } catch (Exception $e) {
                \Log::error('Google Signup Email Failed: ' . $e->getMessage(), [
                    'email' => $newUser->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the signup if email fails
            }
            
            Auth::login($newUser);
            
            return redirect()->route('user.account')
                ->with('success', 'Account created successfully! Welcome! Check your email for login credentials.');
                
        } catch (Exception $e) {
            \Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('sign-in')
                ->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}
