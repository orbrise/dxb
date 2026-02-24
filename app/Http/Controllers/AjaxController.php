<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Currency;
use App\Models\Package;
use App\Models\Country;

class AjaxController extends Controller
{
    public function citySearch(Request $req){
        $val = $req->val;
        $cities = City::where('name', 'like', "%$val%")->take(5)->get();
        
        // Add currency code for each city based on country
        $result = $cities->map(function($city) {
            $currency = Currency::where('country', $city->country)->first();
            return [
                'id' => $city->id,
                'name' => $city->name,
                'country' => $city->country,
                'iso' => $city->iso,
                'currency_code' => $currency ? $currency->code : 'USD'
            ];
        });
        
        return $result->toArray();
    }
    
    /**
     * Get package data for upgrade page (public route for authenticated users)
     */
    public function getPackage($id)
    {
        // Get current country from domain with fallback
        $currentCountry = null;
        
        if (function_exists('getCurrentCountry')) {
            $currentCountry = getCurrentCountry();
        }
        
        // Load package first to check if it's global
        $package = Package::findOrFail($id);
        
        // If package is global, return with global price_tiers
        if ($package->is_global) {
            return response()->json($package);
        }
        
        // For country-specific packages, load country prices
        // First try current country, if not found try to get any available
        if ($currentCountry) {
            $package->load(['countryPrices' => function($query) use ($currentCountry) {
                $query->where('country_id', $currentCountry->id)->with('country');
            }]);
        }
        
        // If no country prices found for current country, load all country prices
        if ($package->countryPrices->isEmpty()) {
            $package->load(['countryPrices' => function($query) {
                $query->with('country');
            }]);
        }
        
        return response()->json($package);
    }
}
