<?php

namespace App\Http\Controllers;

use App\Models\UsersProfile;
use App\Models\City;
use App\Models\Service;
use App\Models\ProfileImage;
use App\Models\Review;
use App\Models\Country;
use Illuminate\Http\Request;

class AmpController extends Controller
{
    /**
     * AMP Home/Main page
     */
    public function home()
    {
        // Get featured cities
        $featuredCities = City::orderBy('name')
            ->take(20)
            ->get();
        
        // Get total counts
        $totalProfiles = UsersProfile::where('is_active', 1)->count();
        
        return response()
            ->view('amp.home', compact('featuredCities', 'totalProfiles'))
            ->header('AMP-Access-Control-Allow-Source-Origin', url('/'))
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * AMP Listings page (escorts in city)
     */
    public function listings($gender, $city)
    {
        $cityData = City::where('slug', $city)->orWhere('name', $city)->first();
        
        if (!$cityData) {
            abort(404, 'City not found');
        }
        
        $profiles = UsersProfile::where('city', $cityData->id)
            ->where('is_active', 1)
            ->orderByRaw("CASE 
                WHEN package_id = 21 THEN 1 
                WHEN package_id = 20 THEN 2 
                WHEN package_id = 19 THEN 3 
                ELSE 4 
            END")
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        // Get profile images
        foreach ($profiles as $profile) {
            $profile->main_image = ProfileImage::where('profile_id', $profile->id)
                ->orderBy('id', 'asc')
                ->first();
        }
        
        $canonicalUrl = url("/{$gender}-escorts-in-{$cityData->slug}");
        
        return response()
            ->view('amp.listings', compact('profiles', 'cityData', 'gender', 'canonicalUrl'))
            ->header('AMP-Access-Control-Allow-Source-Origin', url('/'))
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * AMP Profile detail page
     */
    public function profile($gender, $city, $id, $slug)
    {
        $profile = UsersProfile::findOrFail($id);
        
        $cityData = City::where('slug', $city)->orWhere('name', $city)->first();
        
        // Get profile images
        $images = ProfileImage::where('profile_id', $id)
            ->orderBy('id', 'asc')
            ->get();
        
        // Get reviews
        $reviews = Review::where('profile_id', $id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get services
        $services = Service::whereIn('id', function($query) use ($id) {
            $query->select('service_id')
                ->from('user_services')
                ->where('profile_id', $id);
        })->get();
        
        // Get nationality
        $nationality = Country::find($profile->nationality);
        
        $canonicalUrl = url("/{$gender}-escorts-in-{$city}/{$id}/{$slug}");
        
        return response()
            ->view('amp.profile', compact('profile', 'cityData', 'gender', 'images', 'reviews', 'services', 'nationality', 'canonicalUrl'))
            ->header('AMP-Access-Control-Allow-Source-Origin', url('/'))
            ->header('Access-Control-Allow-Origin', '*');
    }
}
