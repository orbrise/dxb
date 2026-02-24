<?php

namespace App\Livewire\Profile\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class RejectedVerifications extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        // Get the first user profile for navigation links
        $firstProfile = UsersProfile::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->first();
        
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
        
        // Get all profiles with rejected verifications for the logged-in user
        $rejectedProfiles = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg', 'rejectedVerification'])
            ->where('user_id', Auth::id())
            ->whereHas('rejectedVerification')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        
        return view('livewire.profile.users.rejected-verifications', compact(
            'rejectedProfiles', 
            'firstProfile',
            'activeCount',
            'pendingCount',
            'rejectedCount',
            'archivedCount'
        ));
    }
}
