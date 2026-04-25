<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NewHomepage extends Component
{
    // Flag to use new Evoory theme
    public $useEvooryTheme = true;

    public function render()
    {
        $currentCountry = function_exists('getCurrentCountry') ? getCurrentCountry() : null;
        $countryKey = $currentCountry->nicename ?? 'all';

        $featuredCities = Cache::remember("homepage:featured_cities:{$countryKey}", 3600, function () use ($currentCountry) {
            return City::where('is_featured', true)
                ->when($currentCountry, fn($q) => $q->where('country', $currentCountry->nicename))
                ->orderBy('feature_priority', 'asc')
                ->orderBy('name', 'asc')
                ->get();
        });

        $countriesWithCities = Cache::remember('homepage:countries_with_cities', 3600, function () {
            return Country::select('countries.id', 'countries.iso', 'countries.nicename')
                ->join('cities', 'countries.iso', '=', 'cities.iso')
                ->groupBy('countries.id', 'countries.iso', 'countries.nicename')
                ->selectRaw('COUNT(cities.id) as cities_count')
                ->having('cities_count', '>', 0)
                ->orderBy('countries.nicename', 'asc')
                ->get()
                ->map(fn($country) => [
                    'code' => $country->iso,
                    'name' => $country->nicename,
                    'cities' => $country->cities_count,
                ]);
        });

        if ($this->useEvooryTheme) {
            return view('livewire.homepage-evoory', compact('featuredCities', 'countriesWithCities'))
                ->layout('components.layouts.app-evoory');
        }

        return view('livewire.new-homepage', compact('featuredCities'));
    }
}
