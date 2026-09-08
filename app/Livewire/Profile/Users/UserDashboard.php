<?php

namespace App\Livewire\Profile\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app-evoory')]
class UserDashboard extends Component
{
    use WithPagination;
    
    public $id, $check;
    public $profileLink;
    public $currentUser;
    public $filter = null;
    public $page = 1;

    protected $paginationTheme = 'bootstrap';
    
    protected $queryString = [
        'page' => ['except' => 1],
        'filter' => ['except' => null]
    ];

    public function mount($id = null)
    {
        $this->filter = request()->get('filter');

        // Route no longer carries a profile id — default to the auth user's
        // latest profile so the dashboard has a "featured" one to show in
        // sidebar tiles (Verify Photos, Upgrade). Individual profile cards
        // in the list still link to their own per-profile actions.
        $user = null;
        if ($id) {
            $user = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg'])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
        }

        if (!$user) {
            $user = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg'])
                ->where('user_id', Auth::id())
                ->whereNull('archived_at')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$user) {
            return redirect()->route('new.profile');
        }

        $this->id = $user->id;
        $this->check = (int) $user->is_active;
        $this->profileLink = "{$user->ggender->name}-escorts-in-{$user->getcity->name}/{$user->id}/{$user->slug}";
        $this->currentUser = $user;
    }

    public function render()
    {
        // Get current user profile — mount() guarantees $this->id points at
        // one of the auth user's profiles (or the request already redirected
        // to new.profile), so a firstOrFail is safe here.
        $user = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg'])
            ->where('id', $this->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $this->check = $user->is_active;
        
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
         
        // Build query for all profiles (exclude archived)
        $query = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg', 'rejectedVerification', 'getpackage', 'activeAuction'])
            ->where('user_id', Auth::id())
            ->whereNull('archived_at'); // Exclude archived profiles
         
        // Apply filters
        if ($this->filter === 'pending') {
            // Pending approval: profiles not verified yet (you can adjust this condition based on your verification logic)
            // Assuming there's a 'verified' or 'approval_status' column
            $query->where('is_verified', 0); // Adjust this condition as needed
        } elseif (!$this->filter) {
            // Active profiles: no filter or default view shows active profiles
            // You can add specific conditions here if needed
        }
        
        $allProfiles = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate total views and phone clicks across all user profiles (exclude archived)
        $totalViews = UsersProfile::where('user_id', Auth::id())
            ->whereNull('archived_at')
            ->sum('profile_views');
        
        $totalPhoneClicks = UsersProfile::where('user_id', Auth::id())
            ->whereNull('archived_at')
            ->sum('phone_clicks');
        
        return view('livewire.profile.users.user-dashboard', compact(
            'user', 
            'allProfiles', 
            'totalViews', 
            'totalPhoneClicks',
            'activeCount',
            'pendingCount',
            'rejectedCount',
            'archivedCount'
        ));
    }

    public function deleteProfile($profileId)
    {
        try {
            $profile = UsersProfile::where('id', $profileId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $profile->archive();

            session()->flash('success', 'Profile deleted successfully.');

            if ((int) $profileId === (int) $this->id) {
                return redirect()->route('user.account');
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting profile', [
                'error' => $e->getMessage(),
                'profile_id' => $profileId,
            ]);
            session()->flash('error', 'Failed to delete profile');
        }
    }

    public function activestatus($id, $action)
    {
        try {
            // Update the profile status
            $updated = UsersProfile::where('id', $id)->update(['is_active' => $action]);
            
            // Log for debugging
            \Log::info('Profile status updated', [
                'profile_id' => $id,
                'action' => $action,
                'updated' => $updated
            ]);
            
            // Update check status if it's the current profile
            if ($id == $this->id) {
                $this->check = $action;
            }
            
            // Flash success message
            $message = $action == 1 ? 'Profile resumed successfully!' : 'Profile paused successfully!';
            session()->flash('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Error updating profile status', [
                'error' => $e->getMessage(),
                'profile_id' => $id
            ]);
            session()->flash('error', 'Failed to update profile status');
        }
    }
}