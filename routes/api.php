<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// City search API for typeahead autocomplete
Route::get('/search_city.json', function (Request $request) {
    $query = $request->input('q');
    $excludeCountries = $request->input('xc', ''); // Excluded countries (e.g., 'us,pl,ru,by')
    
    if (empty($query) || strlen($query) < 2) {
        return response()->json([]);
    }
    
    $citiesQuery = App\Models\City::where('name', 'like', $query . '%')
        ->orWhere('name', 'like', '% ' . $query . '%');
    
    // Filter out excluded countries if provided
    if (!empty($excludeCountries)) {
        $excludedCodes = array_map('strtoupper', array_map('trim', explode(',', $excludeCountries)));
        $citiesQuery->whereNotIn('iso', $excludedCodes);
    }
    
    $cities = $citiesQuery->limit(8)
        ->get()
        ->map(function ($city) {
            // Get profile counts for this city (using 'city' column, not 'city_id')
            $femaleCount = App\Models\UsersProfile::where('city', $city->id)->where('gender', 1)->count();
            $maleCount = App\Models\UsersProfile::where('city', $city->id)->where('gender', 2)->count();
            $shemaleCount = App\Models\UsersProfile::where('city', $city->id)->where('gender', 3)->count();
            
            return [
                'name' => $city->name,
                'url' => $city->slug,
                'co' => strtolower($city->iso ?? ''),
                'country_code' => strtolower($city->iso ?? ''),
                'listings_count' => [
                    'female' => $femaleCount,
                    'male' => $maleCount,
                    'shemale' => $shemaleCount,
                ]
            ];
        });
    
    return response()->json($cities);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    Route::get('/cities/search', function (Request $request) {
        $query = $request->input('query');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $cities = App\Models\City::where('name', 'like', '%' . $query . '%')
            ->with('country')
            ->limit(10)
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'country_name' => $city->country->nicename ?? 'Unknown',
                    'country_code' => $city->country->iso ?? ''
                ];
            });
        
        return response()->json($cities);
    });
});

// Profile Status Update Route
Route::post('/profile-status/{id}', function (Request $request, $id) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $profile = App\Models\UsersProfile::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();
    
    $action = $request->input('action');
    $profile->is_active = $action;
    $profile->save();
    
    return response()->json([
        'success' => true,
        'message' => $action == 1 ? 'Profile resumed successfully!' : 'Profile paused successfully!',
        'profile_id' => $id,
        'is_active' => $action
    ]);
})->middleware('auth'); 

// WhatsApp Rotation Message API - Get next message for a profile
Route::get('/whatsapp-message/{profileId}', function (Request $request, $profileId) {
    $profileUrl = $request->input('url', url()->current());
    
    $message = App\Models\WhatsAppRotationMessage::getNextMessageForProfile(
        (int) $profileId,
        $profileUrl
    );
    
    return response()->json([
        'success' => true,
        'message' => $message
    ]);
});
