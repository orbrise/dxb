<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class FavoriteProfilesDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function removeFavorite($profileId)
    {
        auth()->user()->favorites()->where('profile_id', $profileId)->delete();
        session()->flash('success', 'Profile removed from favorites.');
    }

    public function render()
    {
        $user = Auth::user()->profiles->first();
        
        $favorites = auth()->user()->favorites()
            ->with(['profile.ggender', 'profile.getcity', 'profile.coverimg', 'profile.singleimg', 'profile.getpackage'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('livewire.favorite-profiles-dashboard', compact('favorites', 'user'));
    }
}