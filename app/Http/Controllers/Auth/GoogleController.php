<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
                
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'registration_ip' => $existingUser->registration_ip ?: $ipAddress,
                    'registration_country' => $existingUser->registration_country ?: $ipCountry,
                ]);
                
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
             
            \Log::info('New Google user created', [
                'user_id' => $newUser->id,
                'email' => $newUser->email,
                'password' => $randomPassword,
                'ip_address' => $ipAddress,
                'ip_country' => $ipCountry,
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
