<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneClick extends Model
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
     * Get the profile that was clicked
     */
    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id');
    }

    /**
     * Get the user who clicked (if logged in)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if IP has clicked phone within time window
     */
    public static function hasClickedRecently($profileId, $ipAddress, $hours = 1)
    {
        return self::where('profile_id', $profileId)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subHours($hours))
            ->exists();
    }

    /**
     * Record a new phone click
     */
    public static function recordClick($profileId, $request = null)
    {
        $ipAddress = $request ? $request->ip() : request()->ip();
        
        // Check if already clicked in last hour (prevent spam)
        if (self::hasClickedRecently($profileId, $ipAddress, 1)) {
            return false; // Don't count duplicate click
        }

        // Record the click
        self::create([
            'profile_id' => $profileId,
            'ip_address' => $ipAddress,
            'user_agent' => substr($request ? $request->userAgent() : request()->userAgent() ?? '', 0, 255),
            'country' => self::getCountryFromIp($ipAddress),
            'user_id' => auth()->id(),
        ]);

        // Also increment the cumulative counter for backward compatibility
        \DB::table('users_profiles')
            ->where('id', $profileId)
            ->update([
                'phone_clicks' => \DB::raw('COALESCE(phone_clicks, 0) + 1')
            ]);

        return true;
    }

    /**
     * Get country from IP (simple implementation)
     */
    protected static function getCountryFromIp($ip)
    {
        try {
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
     * Get phone clicks count for a profile within date range
     */
    public static function getClicksCount($profileId, $days = 30)
    {
        return self::where('profile_id', $profileId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Get clicks for a specific date
     */
    public static function getClicksForDate($profileId, $date)
    {
        return self::where('profile_id', $profileId)
            ->whereDate('created_at', $date)
            ->count();
    }

    /**
     * Get clicks today for a profile
     */
    public static function getClicksToday($profileId)
    {
        return self::where('profile_id', $profileId)
            ->whereDate('created_at', today())
            ->count();
    }
}
