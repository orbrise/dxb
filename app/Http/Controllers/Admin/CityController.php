<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 25);
        if (!in_array($perPage, $allowed)) {
            $perPage = 25;
        }

        $countries = City::groupBy('country')->get();
        $cities = "";

        if(!empty(request()->input('country'))){
            $query = City::where("country", request()->input('country'));
            
            // Add search filter if provided
            if(!empty(request()->input('search'))){
                $searchTerm = request()->input('search');
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');
            }
            
            $cities = $query->orderBy('name', 'asc')
                ->paginate($perPage)
                ->withQueryString();
        }

    

        return view('admin.cities.cities', compact('countries', 'cities'));
    }

    public function addCity(Request $req){

        $req->validate([
        'cityname' => 'required|unique:cities,name',
        ]);

        $city = new City;
        $city->name = $req->cityname;
        $city->country = $req->countryname;
        $city->iso = $req->iso;

        if($city->save()){
            return back()->with("success", "City added successfully");
        }
    }


    public function updateCity(Request $req){

        $city = City::findOrFail($req->rid);
        $city->name = $req->name;
        
        // Update slug if provided
        if($req->has('slug') && !empty($req->slug)){
            $city->slug = $req->slug;
        }
        
        $city->iso = $req->iso;

        if($city->save()){
            return "success";
        }
    }

    public function delCity(Request $req)
    {
        if(City::where('id',$req->rid)->delete()){
            return 'success';
        }
    }

    public function toggleCitySitemap(Request $req)
    {
        $city = City::findOrFail($req->city_id);
        $city->include_in_sitemap = $req->include_in_sitemap;
        
        if($city->save()){
            return 'success';
        }
    }

    public function toggleCityFeatured(Request $req)
    {
        $city = City::findOrFail($req->city_id);
        $city->is_featured = $req->is_featured;
        
        if($city->save()){
            return 'success';
        }
    }

    public function updateFeaturePriority(Request $req)
    {
        try {
            $req->validate([
                'city_id' => 'required|exists:cities,id',
                'feature_priority' => 'required|integer|min:1|max:999'
            ]);
            
            $city = City::findOrFail($req->city_id);
            $city->feature_priority = $req->feature_priority;
            
            if($city->save()){
                return response()->json([
                    'success' => true, 
                    'message' => 'Priority updated successfully',
                    'priority' => $city->feature_priority
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'Failed to save'], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
