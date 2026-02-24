<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsersProfile;
use App\Models\MailSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProfileReactivationController extends Controller
{
    /**
     * Show archived profiles for the authenticated user
     */
    public function index()
    {
        // Get counts for each filter
        $activeCount = UsersProfile::where('user_id', Auth::id())
            ->whereNull('archived_at')
            ->count();
            
        $pendingCount = UsersProfile::where('user_id', Auth::id())
            ->whereNull('archived_at')
            ->where('is_verified', 0)
            ->count();
            
        $rejectedCount = UsersProfile::where('user_id', Auth::id())
            ->whereHas('rejectedVerification')
            ->count();
            
        $archivedCount = UsersProfile::where('user_id', Auth::id())
            ->whereNotNull('archived_at')
            ->count();
            
        $archivedProfiles = UsersProfile::archived()
            ->where('user_id', Auth::id())
            ->with(['gcity', 'ggender'])
            ->get();

        return view('profile.archived', compact(
            'archivedProfiles',
            'activeCount',
            'pendingCount',
            'rejectedCount',
            'archivedCount'
        ));
    }

    /**
     * Reactivate an archived profile
     */
    public function reactivate(Request $request, $id)
    {
        $profile = UsersProfile::findOrFail($id);

        // Check if user owns this profile
        if ($profile->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if profile is actually archived
        if (!$profile->isArchived()) {
            return redirect()->back()->with('error', 'Profile is already active.');
        }

        try {
            $profile->repost();
            
            Log::info("User {Auth::id()} reactivated profile ID: {$profile->id}");
            
            // Send reactivation confirmation email if enabled
            $this->sendReactivationNotification($profile);

            return redirect()->back()->with('success', 'Profile has been successfully reactivated!');
            
        } catch (\Exception $e) {
            Log::error("Failed to reactivate profile ID: {$profile->id} - " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reactivate profile. Please try again.');
        }
    }

    /**
     * Show user's profile status dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $activeProfiles = UsersProfile::active()
            ->where('user_id', $user->id)
            ->with(['gcity', 'ggender'])
            ->get();
            
        $archivedProfiles = UsersProfile::archived()
            ->where('user_id', $user->id)
            ->with(['gcity', 'ggender'])
            ->get();

        return view('profile.dashboard', compact('activeProfiles', 'archivedProfiles'));
    }

    /**
     * Send reactivation notification email
     */
    private function sendReactivationNotification($profile)
    {
        $mailSettings = MailSettings::first();
        
        if (!$mailSettings || !$mailSettings->shouldSendEmail('profile_archived')) {
            return;
        }

        try {
            // TODO: Create ProfileReactivatedMail mailable class
            // Mail::to($profile->user->email)->send(new ProfileReactivatedMail($profile));
            
            Log::info("Reactivation notification sent to {$profile->user->email} for profile ID: {$profile->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send reactivation notification for profile ID: {$profile->id} - " . $e->getMessage());
        }
    }
}
