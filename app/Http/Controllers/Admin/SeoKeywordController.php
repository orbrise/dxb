<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoKeyword;
use App\Models\Setting;
use App\Models\Gender;
use App\Models\City;
use App\Models\Country;

class SeoKeywordController extends Controller
{
    public function index()
    {
        $seoData = SeoKeyword::with(['gender', 'city', 'country'])->latest()->get();
        return view('admin.seo.index', ['seoKeywords' => $seoData]);
    }

    public function create()
    {
        $genders = Gender::all();
        $cities = City::all();
        $countries = Country::all();
        
        return view('admin.seo.create', compact('genders', 'cities', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'keywords' => 'nullable',
            'description' => 'nullable',
            'content' => 'nullable',
            'gender_id' => 'nullable|exists:genders,id',
            'city_id' => 'nullable|exists:cities,id',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        // Check if combination already exists
        $existing = SeoKeyword::where('gender_id', $request->gender_id)
                             ->where('city_id', $request->city_id)
                             ->where('country_id', $request->country_id)
                             ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'SEO data for this combination already exists.']);
        }

        SeoKeyword::create($request->only([
            'page', 'gender_id', 'city_id', 'country_id', 
            'title', 'keywords', 'description', 'content'
        ]));
        return redirect()->route('seo.index')->with('success', 'SEO data added successfully!');
    }

    public function edit(SeoKeyword $seoKeyword)
    {
        $genders = Gender::all();
        $cities = City::all();
        $countries = Country::all();
        
        return view('admin.seo.edit', compact('seoKeyword', 'genders', 'cities', 'countries'));
    }

    public function update(Request $request, SeoKeyword $seoKeyword)
    {
        $request->validate([
            'title' => 'required',
            'keywords' => 'nullable',
            'description' => 'nullable',
            'content' => 'nullable',
            'gender_id' => 'nullable|exists:genders,id',
            'city_id' => 'nullable|exists:cities,id',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        // Check if combination already exists (excluding current record)
        $existing = SeoKeyword::where('gender_id', $request->gender_id)
                             ->where('city_id', $request->city_id)
                             ->where('country_id', $request->country_id)
                             ->where('id', '!=', $seoKeyword->id)
                             ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'SEO data for this combination already exists.']);
        }

        $seoKeyword->update($request->only([
            'page', 'gender_id', 'city_id', 'country_id', 
            'title', 'keywords', 'description', 'content'
        ]));
        return redirect()->route('seo.index')->with('success', 'SEO data updated successfully!');
    }

    public function destroy(SeoKeyword $seoKeyword)
    {
        $seoKeyword->delete();
        return redirect()->route('seo.index')->with('success', 'SEO data deleted successfully!');
    }

    // API method to get SEO tags by parameters
    public function getSeoTags($requestOrPage = null, $genderId = null, $cityId = null, $countryId = null)
    {
        // Handle both Request object (for API) and string parameter (for legacy calls)
        if ($requestOrPage instanceof \Illuminate\Http\Request) {
            $request = $requestOrPage;
            $page = $request->get('page');
            $genderId = $request->get('gender_id');
            $cityId = $request->get('city_id');
            $countryId = $request->get('country_id');
            $returnJson = true;
        } else {
            // Legacy call with string parameter
            $page = $requestOrPage;
            $returnJson = false;
        }
        
        // First try to find specific combination
        $seoTags = SeoKeyword::byParameters($genderId, $cityId, $countryId)->first();
        
        // If not found, try less specific combinations
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters($genderId, $cityId, null)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters($genderId, null, $countryId)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters(null, $cityId, $countryId)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters($genderId, null, null)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters(null, $cityId, null)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters(null, null, $countryId)->first();
        }
        
        // If still not found and page is provided, try traditional page-based SEO
        if (!$seoTags && $page) {
            $seoTags = SeoKeyword::where('page', $page)->first();
        }
        
        // If still not found, use default
        if (!$seoTags) {
            $defaultTags = Setting::first();
            $result = [
                'title' => $defaultTags->title ?? 'Default Title',
                'keywords' => $defaultTags->keywords ?? 'Default Keywords',
                'description' => $defaultTags->description ?? 'Default Description',
                'content' => $defaultTags->content ?? ''
            ];
        } else {
            $result = [
                'title' => $seoTags->title,
                'keywords' => $seoTags->keywords,
                'description' => $seoTags->description,
                'content' => $seoTags->content ?? ''
            ];
        }
        
        // Return JSON response for API calls, array for legacy calls
        return $returnJson ? response()->json($result) : $result;
    }

    // Helper method for internal use (backward compatibility)
    public function getSeoTagsForPage($page = null, $genderId = null, $cityId = null, $countryId = null)
    {
        // First try to find specific combination
        $seoTags = SeoKeyword::byParameters($genderId, $cityId, $countryId)->first();
        
        // If not found, try less specific combinations
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters($genderId, $cityId, null)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters($genderId, null, $countryId)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters(null, $cityId, $countryId)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters($genderId, null, null)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters(null, $cityId, null)->first();
        }
        
        if (!$seoTags) {
            $seoTags = SeoKeyword::byParameters(null, null, $countryId)->first();
        }
        
        // If still not found and page is provided, try traditional page-based SEO
        if (!$seoTags && $page) {
            $seoTags = SeoKeyword::where('page', $page)->first();
        }
        
        // If still not found, use default
        if (!$seoTags) {
            $defaultTags = Setting::first();
            return [
                'title' => $defaultTags->title ?? 'Default Title',
                'keywords' => $defaultTags->keywords ?? 'Default Keywords',
                'description' => $defaultTags->description ?? 'Default Description',
                'content' => $defaultTags->content ?? ''
            ];
        }
        
        return [
            'title' => $seoTags->title,
            'keywords' => $seoTags->keywords,
            'description' => $seoTags->description,
            'content' => $seoTags->content ?? ''
        ];
    }
}
