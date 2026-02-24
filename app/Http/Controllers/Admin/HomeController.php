<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersProfile;

class HomeController extends Controller
{
    public function index(){
        if(!auth()->check()){
            return redirect()->route('admin.login');
        }

        $stats = [
            'total_users' => User::count(),
            'total_profiles' => UsersProfile::count(),
            'active_profiles' => UsersProfile::where('is_active', 1)->count(),
            'inactive_profiles' => UsersProfile::where('is_active', 0)->count(),
            'active_users' => User::where('verified', 1)->whereNotNull('email_verified_at')->count(),
            'inactive_users' => User::where(function($query) {
                $query->where('verified', 0)->orWhereNull('email_verified_at');
            })->count()
        ];

        $latest_users = User::latest()->take(20)->get();
        $latest_profiles = UsersProfile::with('user')->latest()->take(20)->get();

        // Get active user statistics with Mon DD format (Dec 16) - Last 30 days
    $activeUsers = User::selectRaw('DATE_FORMAT(created_at, "%b %d") as date, COUNT(*) as count')
        ->where('verified', 1)
        ->whereNotNull('email_verified_at')
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    // Get inactive user statistics with Mon DD format (Dec 16) - Last 30 days
    $inactiveUsers = User::selectRaw('DATE_FORMAT(created_at, "%b %d") as date, COUNT(*) as count')
        ->where(function($query) {
            $query->where('verified', 0)->orWhereNull('email_verified_at');
        })
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    // Get active profile statistics with Mon DD format - Last 30 days
    $activeProfiles = UsersProfile::selectRaw('DATE_FORMAT(created_at, "%b %d") as date, COUNT(*) as count')
        ->where('is_active', 1)
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    // Get inactive profile statistics with Mon DD format - Last 30 days   
    $inactiveProfiles = UsersProfile::selectRaw('DATE_FORMAT(created_at, "%b %d") as date, COUNT(*) as count')
        ->where('is_active', 0)
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Convert to arrays
    $activeUserStats = $activeUsers->pluck('count', 'date')->toArray();
    $inactiveUserStats = $inactiveUsers->pluck('count', 'date')->toArray();
    $activeProfileStats = $activeProfiles->pluck('count', 'date')->toArray();
    $inactiveProfileStats = $inactiveProfiles->pluck('count', 'date')->toArray();

    // Generate ALL dates for the last 30 days
    $allDates = [];
    for ($i = 29; $i >= 0; $i--) {
        $allDates[] = now()->subDays($i)->format('M d');
    }
    
    // Initialize clean arrays with all dates set to 0
    $cleanActiveUserStats = [];
    $cleanInactiveUserStats = [];
    $cleanActiveProfileStats = [];
    $cleanInactiveProfileStats = [];
    
    // Fill ALL dates with data (0 if no data exists for that day)
    foreach ($allDates as $date) {
        $cleanActiveUserStats[$date] = $activeUserStats[$date] ?? 0;
        $cleanInactiveUserStats[$date] = $inactiveUserStats[$date] ?? 0;
        $cleanActiveProfileStats[$date] = $activeProfileStats[$date] ?? 0;
        $cleanInactiveProfileStats[$date] = $inactiveProfileStats[$date] ?? 0;
    }
    
    // Use clean arrays
    $activeUserStats = $cleanActiveUserStats;
    $inactiveUserStats = $cleanInactiveUserStats;
    $activeProfileStats = $cleanActiveProfileStats;
    $inactiveProfileStats = $cleanInactiveProfileStats;


        return view('admin.dashboard' , compact('stats','latest_users','latest_profiles', 'activeUserStats', 'inactiveUserStats', 'activeProfileStats', 'inactiveProfileStats'));
    }
}
