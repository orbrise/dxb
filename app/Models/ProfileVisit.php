<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'ip_address',
        'user_agent',
        'country',
        'user_id',
    ];

    /**
     * Get the profile that was visited
     */
    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id');
    }

    /**
     * Get the user who visited (if logged in)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if IP has visited profile within time window
     */
    public static function hasVisitedRecently($profileId, $ipAddress, $hours = 24)
    {
        return self::where('profile_id', $profileId)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subHours($hours))
            ->exists();
    }

    /**
     * Record a new visit
     */
    public static function recordVisit($profileId, $request)
    {
        $ipAddress = $request->ip();
        
        // Check if already visited in last 24 hours
        if (self::hasVisitedRecently($profileId, $ipAddress, 24)) {
            return false; // Don't count duplicate visit
        }

        // Record the visit
        self::create([
            'profile_id' => $profileId,
            'ip_address' => $ipAddress,
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'country' => self::getCountryFromIp($ipAddress),
            'user_id' => auth()->id(),
        ]);

        // Increment profile view counter
        \DB::table('users_profiles')
            ->where('id', $profileId)
            ->update([
                'profile_views' => \DB::raw('COALESCE(profile_views, 0) + 1')
            ]);

        return true;
    }

    /**
     * Get country from IP (simple implementation)
     */
    protected static function getCountryFromIp($ip)
    {
        try {
            // Use the same GeoIP service as registration if available
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
            if ($response) {
                $data = json_decode($response, true);
                return $data['countryCode'] ?? null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    /**
     * Get unique visitors count for a profile
     */
    public static function getUniqueVisitors($profileId, $days = 30)
    {
        return self::where('profile_id', $profileId)
            ->where('created_at', '>=', now()->subDays($days))
            ->distinct('ip_address')
            ->count('ip_address');
    }

    /**
     * Get visits today for a profile
     */
    public static function getVisitsToday($profileId)
    {
        return self::where('profile_id', $profileId)
            ->whereDate('created_at', today())
            ->count();
    }
}
