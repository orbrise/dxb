<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\City;

class NewHomepage extends Component
{
    // Flag to use new Evoory theme
    public $useEvooryTheme = true;
    
    public function render()
    {
        // Get current country based on domain (ae.domain.com -> UAE, pk.domain.com -> Pakistan, etc.)
        $currentCountry = function_exists('getCurrentCountry') ? getCurrentCountry() : null;
        
        // Get featured cities filtered by current country
        // Order by feature_priority (1 = main city, then higher numbers)
        $featuredCities = City::where('is_featured', true)
            ->when($currentCountry, function($query) use ($currentCountry) {
                // Filter by country nicename (e.g., "United Arab Emirates", "Pakistan")
                $query->where('country', $currentCountry->nicename);
            })
            ->orderBy('feature_priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        
        // Use new Evoory theme or legacy template
        if ($this->useEvooryTheme) {
            return view('livewire.homepage-evoory', compact('featuredCities'))
                ->layout('components.layouts.app-evoory');
        }
            
        return view('livewire.new-homepage', compact('featuredCities'));
    }
}
