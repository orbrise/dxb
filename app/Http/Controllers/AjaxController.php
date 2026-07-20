<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\City;
use App\Models\Currency;
use App\Models\Package;
use App\Models\Country;
use App\Models\ProfileVisit;
use App\Models\Review;
use App\Models\Question;
use App\Models\PhoneClick;
use App\Models\UsersProfile;

class AjaxController extends Controller
{
    /**
     * Async profile-view tracker. Called via fetch()/sendBeacon from the profile detail
     * page so visit tracking still works when the page itself is served from page cache.
     * IP-based dedup happens inside ProfileVisit::recordVisit (one count per IP per 24h).
     */
    public function trackProfileView($id, Request $request)
    {
        try {
            ProfileVisit::recordVisit((int) $id, $request);
        } catch (\Throwable $e) {
            \Log::warning('trackProfileView failed', ['profile_id' => $id, 'error' => $e->getMessage()]);
        }
        // 204 keeps the response cheap and tells the browser there's no body to parse.
        return response()->noContent();
    }

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

    /**
     * Fallback temp image upload used by the new-profile / edit-profile pages
     * when this Livewire build's wire:model file upload pipeline is missing
     * client-side. Saves the file into Livewire's livewire-tmp directory using
     * its filename convention ("<extension>-<random40>.tmp" — what
     * TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded produces),
     * then returns the filename so the Livewire component can hydrate a
     * TemporaryUploadedFile from it via createFromLivewire().
     */
    public function uploadTempImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        ]);

        $file = $request->file('file');
        $original = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        // Livewire's TemporaryUploadedFile::createFromLivewire accepts a bare
        // filename and resolves it relative to the configured upload directory
        // (livewire-tmp/). Random 40 chars is the same convention Livewire uses.
        $filename = Str::random(40) . '.' . $extension;

        $file->storeAs('livewire-tmp', $filename, 'local');

        return response()->json([
            'filename' => $filename,
            'original' => $original,
        ]);
    }

    public function postReview($profileId, Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'You must be logged in to post a review.'], 401);
        }

        $validated = $request->validate([
            'star' => 'required|numeric|min:1|max:5',
            'review' => 'required|string|min:10',
        ], [
            'star.required' => 'Please pick a star rating before posting.',
            'star.min' => 'Please pick a star rating before posting.',
            'review.required' => 'Please write a short review.',
            'review.min' => 'Review must be at least 10 characters.',
        ]);

        Review::create([
            'user_id' => auth()->id(),
            'profile_id' => (int) $profileId,
            'review' => $validated['review'],
            'star' => (int) $validated['star'],
            'status' => 'pending',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Review posted successfully. It will be visible after moderation.',
        ]);
    }

    public function trackPhoneClick($profileId, Request $request)
    {
        try {
            PhoneClick::recordClick((int) $profileId, $request);
        } catch (\Throwable $e) {
            // Fall back to the legacy counter if the click table isn't there.
            try {
                UsersProfile::where('id', (int) $profileId)->increment('phone_clicks');
            } catch (\Throwable $e2) {
                \Log::warning('trackPhoneClick failed', ['profile_id' => $profileId, 'error' => $e2->getMessage()]);
            }
        }
        return response()->noContent();
    }

    public function postQuestion($profileId, Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'You must be logged in to ask a question.'], 401);
        }

        $validated = $request->validate([
            'question' => 'required|string|min:10|max:240',
        ], [
            'question.required' => 'Please type a question before submitting.',
            'question.min' => 'Your question needs to be at least 10 characters long.',
            'question.max' => 'Your question can be at most 240 characters long.',
        ]);

        $q = new Question;
        $q->user_id = auth()->id();
        $q->profile_id = (int) $profileId;
        $q->question = $validated['question'];
        $q->status = 0;
        $q->save();

        return response()->json([
            'ok' => true,
            'message' => 'We will send an email when/if it is answered.',
        ]);
    }
}
