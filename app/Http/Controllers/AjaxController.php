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
use App\Models\Report;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\PhoneClick;
use App\Models\UsersProfile;
use App\Events\NewChatMessage;

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
        $val = trim((string) $req->val);

        // Empty search → return the top cities for the user's country
        // (simple alphabetical). Used by the profile-creation form to
        // pre-populate the city dropdown before the user types.
        if ($val === '') {
            $countryName = $this->resolveUserCountryName($req);
            $cities = City::query()
                ->when($countryName, fn($q) => $q->where('country', $countryName))
                ->orderBy('name', 'asc')
                ->limit(6)
                ->get();
        } else {
            $cities = City::where('name', 'like', "%$val%")->take(5)->get();
        }

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
     * Best-effort resolution of the "user's country" for city-list scoping.
     * Priority:
     *   1. Cloudflare's CF-IPCountry header — instant, free, always present
     *      when the site is behind Cloudflare (which this one is). 2-letter
     *      ISO code we map to Country.nicename via the DB.
     *   2. Country subdomain on the current request (pk./my./ae./etc.).
     *   3. Authenticated user's stored registration_country.
     *   4. getCurrentCountry() fallback (defaults to UAE for main domain).
     * Returns the country's `nicename` (e.g. "Malaysia") or null.
     */
    private function resolveUserCountryName(Request $req): ?string
    {
        // 1. Cloudflare edge already resolved geo — trust it. Values like
        //    "XX" (unknown) or "T1" (Tor) mean CF couldn't/wouldn't resolve;
        //    skip those and fall through. We ignore the header when it's
        //    absent (dev / non-CF proxy) or clearly a sentinel value.
        $cfIso = strtoupper((string) $req->header('CF-IPCountry'));
        if ($cfIso && strlen($cfIso) === 2 && !in_array($cfIso, ['XX', 'T1'], true)) {
            $cfCountry = Country::where('iso', $cfIso)->first();
            if ($cfCountry) {
                return $cfCountry->nicename;
            }
        }

        // 2. Explicit country subdomain (e.g. pk.evoory.com, my.evoory.com).
        $host = $req->getHost();
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            $prefix = strtolower($parts[0]);
            $subdomainCountry = Country::where('domain_prefix', $prefix)->first();
            if ($subdomainCountry) {
                return $subdomainCountry->nicename;
            }
        }

        // 3. Auth'd user's stored country from signup.
        $user = auth()->user();
        if ($user && !empty($user->registration_country)
            && !in_array($user->registration_country, ['Local', 'Unknown'], true)) {
            return $user->registration_country;
        }

        // 4. Final fallback: whatever the domain thinks (usually UAE default).
        if (function_exists('getCurrentCountry')) {
            $domainCountry = getCurrentCountry();
            return $domainCountry?->nicename;
        }
        return null;
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
            // reviews.status is INT: 0 = pending, 1 = approved (see
            // Admin\ReviewController::approve).
            'status' => 0,
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

    public function postMessage($profileId, Request $request)
    {
        $profile = UsersProfile::find((int) $profileId);
        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        // Auth flow: create conversation-based message + broadcast.
        if (auth()->check()) {
            $senderId = auth()->id();
            $ownerId = $profile->user_id;

            if ($senderId === $ownerId) {
                return response()->json(['error' => 'You cannot message yourself.'], 422);
            }

            $validated = $request->validate([
                'message' => 'required|string|min:1|max:500',
                'code' => 'nullable|string|max:10',
                'phone' => 'nullable|string|max:30',
            ], [
                'message.required' => 'Please write a message.',
                'message.max' => 'Message cannot exceed 500 characters.',
            ]);

            $conversation = Conversation::getOrCreate($senderId, $ownerId);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'message' => $validated['message'],
                'status' => 'sent',
                // Keep legacy fields for backward compatibility.
                'user_email' => auth()->user()->email,
                'profile_id' => (int) $profileId,
                // Legacy columns are NOT NULL — default to empty string, matching
                // what the old Livewire form sent when the field was left blank.
                'code' => $validated['code'] ?? '',
                'phone' => $validated['phone'] ?? '',
            ]);

            $conversation->update(['last_message_at' => now()]);

            try {
                broadcast(new NewChatMessage($message, $ownerId))->toOthers();
            } catch (\Throwable $e) {
                \Log::warning('NewChatMessage broadcast failed', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'ok' => true,
                'message' => 'Your message has been sent to ' . $profile->name . '. You can continue the conversation in your Messages.',
            ]);
        }

        // Guest flow: legacy one-way inquiry — email is required.
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|min:1|max:500',
            'code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:30',
        ], [
            'email.required' => 'Please provide your email so we can reply.',
            'message.required' => 'Please write a message.',
            'message.max' => 'Message cannot exceed 500 characters.',
        ]);

        $m = new Message;
        $m->user_email = $validated['email'];
        $m->profile_id = (int) $profileId;
        $m->message = $validated['message'];
        $m->code = $validated['code'] ?? '';
        $m->phone = $validated['phone'] ?? '';
        $m->save();

        return response()->json([
            'ok' => true,
            'message' => 'Your message has been sent to ' . $profile->name . '. Please login to continue the conversation.',
        ]);
    }

    public function postReport($profileId, Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'You must be logged in to report a profile.'], 401);
        }

        $validated = $request->validate([
            'report_type' => 'required|in:fake,spam,inappropriate,other',
            'description' => 'required|string|min:10|max:1000',
        ], [
            'report_type.required' => 'Please select a reason for reporting.',
            'report_type.in' => 'Invalid report type selected.',
            'description.required' => 'Please provide a description.',
            'description.min' => 'Description must be at least 10 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
        ]);

        $existing = Report::where('user_id', auth()->id())
            ->where('profile_id', (int) $profileId)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'You have already submitted a report for this profile. Please wait for it to be reviewed.',
            ], 409);
        }

        Report::create([
            'user_id' => auth()->id(),
            'profile_id' => (int) $profileId,
            'report_type' => $validated['report_type'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Thank you for your report. Our team will review it shortly.',
        ]);
    }
}
