<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function search(Request $request)
{
    $query = $request->input('query');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    // Search all cities matching the query (no featured filter)
    $cities = \App\Models\City::where('name', 'like', "%{$query}%")
        ->limit(15)
        ->get();
    
    if ($cities->isEmpty()) {
        return response()->json([]);
    }
    
    $cities = $cities->map(function($city) {
        // Count profiles in this city
        $profileCount = \App\Models\UsersProfile::where('city', $city->id)
            ->whereNull('archived_at')
            ->where('is_active', 1)
            ->count();
        
        // Get currency for the city based on country
        $currency = \App\Models\Currency::where('country', $city->country)->first();
        
        return [
            'id' => $city->id,
            'name' => $city->name,
            'slug' => $city->slug,
            'country' => $city->country ?? 'Unknown',
            'profile_count' => $profileCount,
            'currency_id' => $currency ? $currency->id : null,
            'currency_code' => $currency ? $currency->code : 'USD'
        ];
    });
    
    return response()->json($cities);
}
}
