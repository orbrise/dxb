<?php

namespace App\Livewire\Profile\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class ArchivedProfiles extends Component
{
    use WithPagination;
    
    public $page = 1;

    protected $paginationTheme = 'bootstrap';
    
    protected $queryString = [
        'page' => ['except' => 1]
    ];

    public function render()
    {   
        // Get archived profiles for the current user
        $archivedProfiles = UsersProfile::with(['ggender', 'getcity', 'coverimg', 'singleimg', 'getpackage'])
            ->where('user_id', Auth::id())
            ->whereNotNull('archived_at')
            ->orderBy('archived_at', 'desc')
            ->paginate(10);
        
        // Get current user's first profile for header info
        $user = UsersProfile::where('user_id', Auth::id())->first();
        
        return view('livewire.profile.users.archived-profiles', compact('archivedProfiles', 'user'));
    }

    public function repostProfile($profileId)
    {
        try {
            $profile = UsersProfile::where('id', $profileId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $profile->repost();
            
            session()->flash('success', 'Profile reposted successfully!');
            
            // Redirect to main dashboard
            return redirect()->route('user.dashboard', [
                'name' => $profile->slug,
                'id' => $profile->id
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error reposting profile', [
                'error' => $e->getMessage(),
                'profile_id' => $profileId
            ]);
            session()->flash('error', 'Failed to repost profile');
        }
    }
}
