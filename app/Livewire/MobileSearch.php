<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Currency;
use App\Models\Service;
use App\Models\Bust;
use App\Models\Ethnicity;
use App\Models\Country;
use App\Models\Language;
use App\Models\HairColor;
use App\Models\City;

class MobileSearch extends Component
{
    // Search form properties
    public $gender = 'female';
    public $selectedcity = 'Dubai';
    public $city = 229; // Default to Dubai
    public $currency = 248; // Default to AED
    public $rate;
    public $sservices = [];
    public $buts;
    public $ori;
    public $incall;
    public $outcall;
    public $nonsmoker;
    public $withreviews;
    public $verified;
    public $ethnicity;
    public $nationality;
    public $agefrom;
    public $ageto;
    public $heightfrom;
    public $heightto;
    public $name;
    public $language;
    public $isshaved;
    public $haircolor;
    
    public function mount($gender = 'female')
    {
        $this->gender = $gender;
        
        // Check if city is passed as query parameter
        $cityParam = request()->query('city');
        
        if ($cityParam) {
            // Try to find city by name or ID
            $city = City::where('name', $cityParam)
                       ->orWhere('id', $cityParam)
                       ->first();
            
            if ($city) {
                $this->selectedcity = $city->name;
                $this->city = $city->id;
                return;
            }
        }
        
        // Default to Dubai if no city parameter or city not found
        $dubai = City::where('name', 'Dubai')->first();
        if ($dubai) {
            $this->selectedcity = 'Dubai';
            $this->city = $dubai->id;
        }
    }
    
    public function citySelected($cityId, $cityName)
    {
        $this->city = $cityId;
        $this->selectedcity = $cityName;
    }
    
    public function render()
    {
        return view('livewire.mobile-search', [
            'currencies' => Currency::select('id', 'code', 'symbol')
                ->get()
                ->unique('code')
                ->sortBy('code')
                ->values(),
            'services' => Service::all(),
            'busts' => Bust::all(), 
            'ethnicities' => Ethnicity::all(),
            'countries' => Country::all(),
            'languages' => Language::all(),
            'hairs' => HairColor::all(),
        ])
        ->layout('layouts.mobile-search-layout');
    }
}