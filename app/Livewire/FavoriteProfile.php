<?php
namespace App\Livewire;

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
            return redirect()->route('login');
        }

        if ($this->isFavorited) {
            auth()->user()->favorites()->where('profile_id', $this->profileId)->delete();
        } else {
            auth()->user()->favorites()->create([
                'profile_id' => $this->profileId
            ]);
        }

        $this->isFavorited = !$this->isFavorited;
    }

    public function render()
    {
        return view('livewire.favorite-profile');
    }
}