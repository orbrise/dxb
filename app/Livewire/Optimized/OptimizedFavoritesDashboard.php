<?php

namespace App\Livewire\Optimized;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class OptimizedFavoritesDashboard extends Component
{
    use WithPagination;

    public function removeFavorite($profileId)
    {
        auth()->user()->favorites()->where('profile_id', $profileId)->delete();
        
        // Clear related cache
        Cache::forget('user_favorites_' . auth()->id());
    }

    public function render()
    {
        $cacheKey = 'user_favorites_' . auth()->id() . '_page_' . $this->getPage();
        
        $favorites = Cache::remember($cacheKey, 300, function () { // 5 minutes cache
            return auth()->user()->favorites()
                ->with(['profile:id,name,listing_id', 'profile.profileImages:id,listing_id,image_name']) // Only select needed fields
                ->select(['id', 'profile_id', 'created_at']) // Limit columns
                ->latest()
                ->paginate(12);
        });

        return view('livewire.optimized.favorite-profiles-dashboard', [
            'favorites' => $favorites
        ]);
    }

    protected function getPage()
    {
        return request()->get('page', 1);
    }
}
