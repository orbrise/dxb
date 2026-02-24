<?php

namespace App\Livewire\Profile\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
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

    public function mount($id)
    {
        $this->id = $id;
        $this->filter = request()->get('filter');
        
        $user = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg'])
            ->where('id', $this->id)
            ->first();
        
        if (!$user) {
            return redirect()->route('new.profile');
        }
        
        $this->check = (int)$user->is_active;
        $this->profileLink = "{$user->ggender->name}-escorts-in-{$user->getcity->name}/{$user->id}/{$user->slug}";
        $this->currentUser = $user;
    }

    public function render()
    {   
        // Get current user profile
        $user = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg'])
            ->where('id', $this->id)
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