<?php
namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Favorite;

class FavoriteProfile extends Component
{
    public $profileId;
    public $isFavorited = false;

    public function mount($profileId)
    {
        $this->profileId = $profileId;
        $this->isFavorited = auth()->user()?->favorites()->where('profile_id', $profileId)->exists() ?? false;
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) {
            return redirect()->route('sign-in');
        }

        if ($this->isFavorited) {
            auth()->user()->favorites()->where('profile_id', $this->profileId)->delete();
        } else {
            auth()->user()->favorites()->create([
                'profile_id' => $this->profileId
            ]);
        }

        $this->isFavorited = !$this->isFavorited;

        // Broadcast the new state so the sibling desktop/mobile
        // FavoriteProfile instance (same profile, different viewport)
        // updates its icon without a full page refresh. Without this,
        // clicking the desktop button leaves the mobile-only button
        // showing stale state and vice versa.
        $this->dispatch('favorite-toggled',
            profileId: $this->profileId,
            isFavorited: $this->isFavorited,
        );
    }

    #[On('favorite-toggled')]
    public function syncFromSibling($profileId, $isFavorited)
    {
        if ((int) $profileId !== (int) $this->profileId) {
            return;
        }
        $this->isFavorited = (bool) $isFavorited;
    }

    public function render()
    {
        return view('livewire.favorite-profile');
    }
}