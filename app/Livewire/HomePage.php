<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Models\{Listing, Service, UserService, Gender, Currency, Ethnicity, 
    Bust, HairColor, Language, UserLanguage, UsersProfile, City, Country, Review, Auction};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
 
class HomePage extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $isFavorited = false;
    public $isMobile = false;
    public $loading = true;
    // Search filters
    public $city = 229;
    public $selectedcity = 'dubai';
    public $gender = 'female';
    public $sservices = [];
    public $rate = null;
    public $currency = 248;
    public $buts;
    public $ori;
    public $nonsmoker;
    public $incall;
    public $outcall;
    public $incallprice;
    public $outcallprice;
    public $withreviews;
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
    public $cityname = 'Dubai';
    public $verified;
    public $profiletype;
    public $auctions = [];
    public $showMobileSearch = false;
    public $tempCity;
    public $tempCityName;

    protected $queryString = [
        'gender' => ['except' => ''],
        'city' => ['except' => ''],
        'selectedcity' => ['except' => ''],
        'cityname' => ['except' => ''],
        'rate' => ['except' => ''],
        'currency' => ['except' => 248],
        'buts' => ['except' => '', 'as' => 'bust'],
        'ori' => ['except' => '', 'as' => 'orientation'],
        'incall' => ['except' => ''],
        'outcall' => ['except' => ''],
        'nonsmoker' => ['except' => ''],
        'withreviews' => ['except' => ''],
        'ethnicity' => ['except' => ''],
        'nationality' => ['except' => ''],
        'agefrom' => ['except' => ''],
        'ageto' => ['except' => ''],
        'heightfrom' => ['except' => ''],
        'heightto' => ['except' => ''],
        'name' => ['except' => ''],
        'language' => ['except' => ''],
        'isshaved' => ['except' => '', 'as' => 'shaved'],
        'haircolor' => ['except' => ''],
        'verified' => ['except' => ''],
        'profiletype' => ['except' => ''],
        'sservices' => ['except' => [], 'as' => 'services']
    ];

    public function mount($city = '', $gender = '', $page = 1, $isMobile = false, $showMobileSearch = false)
{
    $this->gender = $gender;
    $this->isMobile = $isMobile;
    $this->showMobileSearch = $showMobileSearch;
    
    // Set the page from URL parameter
    if ($page > 1) {
        $this->setPage($page);
    }
    
    // Ensure sservices is always an array
    if (!is_array($this->sservices)) {
        $this->sservices = [];
    }
    
    if ($city) {
        $cityModel = City::where('slug', $city)->first();
    
        $this->selectedcity = $city;
        $this->city = $cityModel?->id ?? 229;
        $this->cityname = $cityModel?->name ?? 'Dubai';
        
        // Set initial currency based on city's country
        if ($cityModel && $cityModel->country) {
            $currencyModel = Currency::where('country', $cityModel->country)->first();
            if ($currencyModel) {
                $this->currency = $currencyModel->id;
            }
        }
    } else {
        $this->city = 229;
        $this->selectedcity = "Dubai";
        
        // Set default currency for Dubai (AED)
        $defaultCity = City::find(229);
        if ($defaultCity && $defaultCity->country) {
            $currencyModel = Currency::where('country', $defaultCity->country)->first();
            if ($currencyModel) {
                $this->currency = $currencyModel->id;
            }
        }
    }
    $this->loadAuctions();
    $this->loading = false;

    if (session('search_performed')) {
        $searchParams = session('search_params', []);
        
        // Apply search parameters to component properties
        $this->gender = $searchParams['gender'] ?? 'female';
        $this->selectedcity = $searchParams['selectedcity'] ?? '';
        $this->city = $searchParams['city'] ?? '';
        $this->currency = $searchParams['currency'] ?? null;
        $this->rate = $searchParams['rate'] ?? null;
        $this->sservices = is_array($searchParams['sservices'] ?? []) ? $searchParams['sservices'] : [];
        $this->buts = $searchParams['buts'] ?? null;
        $this->ori = $searchParams['ori'] ?? null;
        $this->incall = $searchParams['incall'] ?? null;
        $this->outcall = $searchParams['outcall'] ?? null;
        $this->nonsmoker = $searchParams['nonsmoker'] ?? null;
        $this->withreviews = $searchParams['withreviews'] ?? null;
        $this->ethnicity = $searchParams['ethnicity'] ?? null;
        $this->nationality = $searchParams['nationality'] ?? null;
        $this->agefrom = $searchParams['agefrom'] ?? null;
        $this->ageto = $searchParams['ageto'] ?? null;
        $this->heightfrom = $searchParams['heightfrom'] ?? null;
        $this->heightto = $searchParams['heightto'] ?? null;
        $this->name = $searchParams['name'] ?? null;
        $this->language = $searchParams['language'] ?? null;
        $this->isshaved = $searchParams['isshaved'] ?? null;
        $this->haircolor = $searchParams['haircolor'] ?? null;
        $this->verified = $searchParams['verified'] ?? null;
        $this->profiletype = $searchParams['profiletype'] ?? null;
        
        // Perform search automatically
        $this->search();
        
        // Clear the session flag
        session()->forget('search_performed');
    }
}

/**
 * Generate pagination URL for SEO-friendly format
 */
public function getPaginationUrl($page)
{
    $gender = $this->gender ?: 'female';
    $city = $this->selectedcity ?: 'dubai';
    
    if ($page <= 1) {
        return route('home', ['gender' => $gender, 'city' => $city]);
    }
    
    return route('home.paginated', ['gender' => $gender, 'city' => $city, 'page' => $page]);
}

/**
 * Navigate to a specific page using URL redirect
 */
public function goToPage($page)
{
    return redirect($this->getPaginationUrl($page));
}

public function toggleMobileSearch()
{
    $this->showMobileSearch = !$this->showMobileSearch;
    // Use dispatch instead of dispatchBrowserEvent for Livewire v3
    $this->dispatch('mobile-search-toggled', ['show' => $this->showMobileSearch]);
    
    // Add a small delay to ensure the modal is rendered before initializing Select2
    if ($this->showMobileSearch) {
        $this->dispatch('initSelect2InModal');
    }
}

public function submitMobileSearch()
{
    // Build the URL with search parameters
    $gender = $this->gender ?? 'female';
    $city = strtolower($this->selectedcity);
    
    $url = "/{$gender}-escorts-in-{$city}";
    
    // Add query parameters
    $params = [];
    if ($this->rate) $params[] = "rate={$this->rate}";
    if ($this->currency) $params[] = "currency={$this->currency}";
    if (!empty($this->sservices)) $params[] = "services=" . implode(',', $this->sservices);
    // Add other parameters as needed
    
    if (!empty($params)) {
        $url .= '?' . implode('&', $params);
    }
    
    return redirect($url);
}

    protected function loadAuctions()
    {
        // Cache key based on city, gender, and auth status
        $isLoggedIn = auth()->check();
        $cacheKey = "auctions:{$this->city}:{$this->gender}:" . ($isLoggedIn ? 'auth' : 'guest');
        
        // Cache auctions for 60 seconds
        $this->auctions = Cache::remember($cacheKey, 60, function() use ($isLoggedIn) {
            $genderModel = CacheService::getGenderByName($this->gender);
            $genderId = $genderModel ? $genderModel->id : null;
            
            $auctionsCollection = collect();
            
            if ($isLoggedIn) {
                // Logged in users can see active auctions (to bid)
                $auctionsCollection = Auction::select('id', 'city_id', 'gender', 'status', 'spot_number', 'winner_profile_id', 'end_date', 'current_price')
                    ->where('city_id', $this->city)
                    ->where('gender', $this->gender)
                    ->where('status', 'active')
                    ->orderBy('spot_number')
                    ->with([
                        'winnerProfile:id,name,user_id,city,gender,about,package_id,slug',
                        'winnerProfile.singleimg:id,user_id,profile_id,image',
                        'winnerProfile.coverimg:id,user_id,profile_id,image',
                        'winnerProfile.photoverify:id,profile_id,status',
                        'city:id,name'
                    ])
                    ->take(6)
                    ->get();
            } else {
                // Guests don't see auctions (only active ones would show, but they can't bid)
                $auctionsCollection = collect();
            }
            
            return $auctionsCollection->take(6);
        });
        
        // Get gender ID for featured profile queries
        $genderModel = CacheService::getGenderByName($this->gender);
        $genderId = $genderModel ? $genderModel->id : null;
        
        // Calculate time-related fields (not cached as they change)
        foreach ($this->auctions as $auction) {
            $auction->timeLeft = Carbon::now()->diffForHumans($auction->end_date, ['parts' => 1, 'short' => true]);
            $auction->daysLeft = Carbon::now()->diffInDays($auction->end_date);
            
            // If no winner profile, get a featured profile for display (only for active auctions)
            if (!$auction->winnerProfile && $auction->status == 'active') {
                // Get VIP/Premium package IDs from cache
                $packageIds = CacheService::getPackageIdsByType();
                $premiumPackageIds = $packageIds['vip'];
                    
                $auction->featuredProfile = UsersProfile::select('id', 'name', 'user_id', 'city', 'gender', 'package_id')
                    ->where('gender', $genderId)
                    ->where('city', $this->city)
                    ->where('is_active', 1)
                    ->whereNull('archived_at')
                    ->when(!empty($premiumPackageIds), fn($q) => $q->whereIn('package_id', $premiumPackageIds))
                    ->with([
                        'singleimg:id,user_id,profile_id,image',
                        'multipleimgs:id,user_id,profile_id,image'
                    ])
                    ->inRandomOrder()
                    ->first();
            }
        }
    }


    public function updatedSservices($value)
    {
        // Handle different input types from the services dropdown
        if (is_string($value) && $value) {
            // If it's a comma-separated string, split it into array
            if (strpos($value, ',') !== false) {
                $this->sservices = array_filter(explode(',', $value), function($id) {
                    return is_numeric(trim($id)) && trim($id) !== '';
                });
                $this->sservices = array_map('intval', $this->sservices); // Convert to integers
            } else {
                // Single value as string
                $this->sservices = [intval($value)];
            }
        } elseif (is_array($value)) {
            // Already an array, ensure all values are integers
            $this->sservices = array_map('intval', array_filter($value, 'is_numeric'));
        } elseif (empty($value)) {
            // Empty value, reset to empty array
            $this->sservices = [];
        }
        
        // Ensure sservices is always an array
        if (!is_array($this->sservices)) {
            $this->sservices = [];
        }
        
        // Auto-search when services change
        $this->resetPage();
    }

    // Auto-search when key filters change
    public function updatedGender()
    {
        $this->resetPage();
    }

    public function updatedRate()
    {
        $this->resetPage();
    }

    public function updatedCurrency()
    {
        $this->resetPage();
    }

    public function toggleFavorite($profileId)
{
    if (!auth()->check()) {
        return redirect()->route('sign-in');
    }

    $favorite = auth()->user()->favorites()->where('profile_id', $profileId)->first();
    
    if ($favorite) {
        $favorite->delete();
        $this->isFavorited = false;
    } else {
        auth()->user()->favorites()->create([
            'profile_id' => $profileId
        ]);
        $this->isFavorited = true;
    }
}

public function checkIfFavorited($profileId)
{
    if (auth()->check()) {
        return auth()->user()->favorites()->where('profile_id', $profileId)->exists();
    }
    return false;
}


    public function paginationView()
    {
        return 'vendor.livewire.custom';
    }

    // Getter method to ensure sservices is always an array
    public function getSservicesProperty()
    {
        return is_array($this->sservices) ? $this->sservices : [];
    }

    // Additional safety method for templates
    public function getSafeServices()
    {
        return is_array($this->sservices) ? $this->sservices : [];
    }

    /**
     * Get SEO content for current city and gender
     * Called from blade template to ensure fresh data on every render
     */
    public function getSeoContent()
    {
        // Get current city
        $currentCity = null;
        if ($this->city && is_numeric($this->city)) {
            $currentCity = Cache::remember("cache:city:id:{$this->city}", 3600, function() {
                return City::find($this->city);
            });
        }
        
        if (!$currentCity) {
            $currentCity = Cache::remember("cache:city:id:229", 3600, function() {
                return City::find(229);
            });
        }
        
        $genderModel = CacheService::getGenderByName($this->gender ?: 'female');
        
        if (!$currentCity || !$genderModel) {
            return null;
        }
        
        $cityId = (int) $currentCity->id;
        $genderId = (int) $genderModel->id;
        
        $seoModel = Cache::remember(
            "cache:seo:{$genderId}:{$cityId}", 
            CacheService::TTL_LOOKUP, 
            function() use ($genderId, $cityId) {
                return \App\Models\SeoKeyword::where('gender_id', $genderId)
                    ->where('city_id', $cityId)
                    ->first();
            }
        );
        
        return $seoModel ? $seoModel->content : null;
    }

    public function updatedSelectedcity($city)
    {
        // Update URL without redirect
        $gender = $this->gender ?? 'female';
        $cityModel = \App\Models\City::find($city);
        $cityName = $cityModel ? $cityModel->name : 'dubai';
        
        // Auto-select currency based on city's country
        if ($cityModel && $cityModel->country) {
            $currencyModel = \App\Models\Currency::where('country', $cityModel->country)->first();
            if ($currencyModel) {
                $this->currency = $currencyModel->id;
                // Dispatch event to update the custom currency combobox
                $this->dispatch('currency-updated', ['currencyId' => $currencyModel->id, 'currencyCode' => $currencyModel->code]);
            }
        }
        
        $path = $gender . '-escorts-in-' . strtolower($cityName);
        $this->js("window.history.pushState({}, '', '/{$path}');");
        
        // Reset pagination and trigger search
        $this->resetPage();
    }

    public function updateCityByName($cityName)
    {
        // Find city by name (case insensitive)
        $cityModel = \App\Models\City::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($cityName) . '%'])->first();
        
        if ($cityModel) {
            // Update component properties
            $this->city = $cityModel->id;
            $this->selectedcity = $cityModel->name;
            $this->cityname = $cityModel->name;
            
            // Auto-select currency based on city's country
            if ($cityModel->country) {
                $currencyModel = \App\Models\Currency::where('country', $cityModel->country)->first();
                if ($currencyModel) {
                    $this->currency = $currencyModel->id;
                    // Dispatch event to update the custom currency combobox
                    $this->dispatch('currency-updated', ['currencyId' => $currencyModel->id, 'currencyCode' => $currencyModel->code]);
                }
            }
            
            // Create proper URL slug
            $gender = $this->gender ?? 'female';
            $citySlug = strtolower(str_replace([' ', "'", '.'], ['-', '', ''], $cityModel->name));
            $citySlug = preg_replace('/[^a-z0-9\-]/', '', $citySlug); // Remove any remaining special chars
            $citySlug = preg_replace('/\-+/', '-', $citySlug); // Replace multiple dashes with single
            $citySlug = trim($citySlug, '-'); // Remove leading/trailing dashes
            
            $path = $gender . '-escorts-in-' . $citySlug;
            
            // Update the URL using Livewire's JS method (this will override the query parameter approach)
            $this->js("
                window.history.replaceState({}, '', '/{$path}');
                console.log('✅ URL updated to: /{$path}');
            ");
            
            // Reset pagination and trigger content update
            $this->resetPage();
            
            // Emit event for any JavaScript listeners
            $this->dispatch('city-updated', ['city' => $cityModel->name, 'slug' => $citySlug, 'path' => $path]);
        } else {
            // Default to Dubai if city not found
            $defaultCity = \App\Models\City::where('name', 'like', '%dubai%')->first();
            if ($defaultCity) {
                $this->city = $defaultCity->id;
                $this->selectedcity = $defaultCity->name;
                $this->cityname = $defaultCity->name;
                
                // Auto-select AED currency for Dubai
                if ($defaultCity->country) {
                    $currencyModel = \App\Models\Currency::where('country', $defaultCity->country)->first();
                    if ($currencyModel) {
                        $this->currency = $currencyModel->id;
                    }
                }
                
                // Update URL to Dubai
                $gender = $this->gender ?? 'female';
                $path = $gender . '-escorts-in-dubai';
                $this->js("window.history.replaceState({}, '', '/{$path}');");
                
                $this->resetPage();
            }
        }
    }

    public function search()
    {
        // Reset to page 1 when searching
        $this->resetPage();
        
        // Dispatch event to close modal
        $this->dispatch('closeSearchModal');
    }
    

    /**
     * Determine sort direction based on 5-minute intervals (for testing)
     * Alternates between 'asc' and 'desc' every 5 minutes
     */
    protected function getSortDirection()
    {
        // Get current time in minutes since midnight
        $now = Carbon::now();
        $minutesSinceMidnight = ($now->hour * 60) + $now->minute;

        // Calculate 30-minute interval (0-47 intervals per day)
        $interval = floor($minutesSinceMidnight / 30);
        
        // Alternate: even intervals = ASC, odd intervals = DESC
        return ($interval % 2 === 0) ? 'asc' : 'desc';
    }

    /**
     * Get information about current sort status (for debugging/display)
     */
    public function getSortInfo()
    {
        $now = Carbon::now();
        $minutesSinceMidnight = ($now->hour * 60) + $now->minute;
        $interval = floor($minutesSinceMidnight / 30);
        $sortDirection = ($interval % 2 === 0) ? 'asc' : 'desc';
        
        // Calculate when the next rotation will happen
        $nextRotationMinutes = (($interval + 1) * 30) - $minutesSinceMidnight;

        return [
            'direction' => $sortDirection,
            'interval' => $interval,
            'next_rotation_minutes' => $nextRotationMinutes,
            'sorting_order' => $sortDirection === 'asc' ? 'Oldest First' : 'Newest First'
        ];
    }

    /**
     * Sort profiles with rotation within each date bracket
     * Groups profiles by creation date, then sorts within each date group
     * with alternating order every 30 minutes
     * Date brackets are ordered newest first (descending)
     */
    protected function sortProfilesWithDateBracketRotation($profiles, $sortDirection)
    {
        if ($profiles->isEmpty()) {
            return $profiles;
        }

        // Group profiles by their creation date (Y-m-d format)
        $groupedByDate = $profiles->groupBy(function($profile) {
            return Carbon::parse($profile->created_at)->format('Y-m-d');
        });

        // Sort date groups in descending order (newest dates first)
        $sortedDateGroups = $groupedByDate->sortKeysDesc();

        // Process each date group and sort profiles within it
        $result = collect();
        foreach ($sortedDateGroups as $date => $dateProfiles) {
            // Sort profiles within this date bracket by ID with the current sort direction
            if ($sortDirection === 'desc') {
                $sortedDateProfiles = $dateProfiles->sortByDesc('id')->values();
            } else {
                $sortedDateProfiles = $dateProfiles->sortBy('id')->values();
            }
            
            $result = $result->concat($sortedDateProfiles);
        }

        return $result->values();
    }

    protected function getProfiles()
{
    $auctionProfileIds = $this->auctions
        ->pluck('winner_profile_id')
        ->filter()
        ->toArray();

    // Get the dynamic sort direction
    $sortDirection = $this->getSortDirection();
    
    // Get package IDs from cache
    $packageIds = CacheService::getPackageIdsByType();
    $vipPackageIds = $packageIds['vip'];
    $featuredPackageIds = $packageIds['featured'];
    $basicPackageIds = $packageIds['basic'];
    
    // Build cache key based on filters (excluding pagination and sort which we handle separately)
    $filterHash = md5(json_encode([
        'city' => $this->city,
        'gender' => $this->gender,
        'rate' => $this->rate,
        'buts' => $this->buts,
        'ori' => $this->ori,
        'incall' => $this->incall,
        'outcall' => $this->outcall,
        'nonsmoker' => $this->nonsmoker,
        'withreviews' => $this->withreviews,
        'ethnicity' => $this->ethnicity,
        'nationality' => $this->nationality,
        'agefrom' => $this->agefrom,
        'ageto' => $this->ageto,
        'heightfrom' => $this->heightfrom,
        'heightto' => $this->heightto,
        'name' => $this->name,
        'language' => $this->language,
        'isshaved' => $this->isshaved,
        'haircolor' => $this->haircolor,
        'verified' => $this->verified,
        'profiletype' => $this->profiletype,
        'sservices' => $this->sservices,
        'auction_excluded' => $auctionProfileIds,
    ]));
    
    $cacheKey = "profiles:list:{$filterHash}";
    
    // Cache the query results for 2 minutes (120 seconds)
    $allProfiles = Cache::remember($cacheKey, 120, function() use ($auctionProfileIds, $vipPackageIds, $featuredPackageIds, $basicPackageIds) {
        // Get all profiles with filters applied
        $query = UsersProfile::query()
            ->select('id', 'name', 'user_id', 'city', 'gender', 'about', 'package_id', 'slug', 'bust', 'orientation', 'ethnicity', 'nationality', 'age', 'height', 'shaved', 'haircolor', 'incall', 'incallcurr', 'incallprice', 'smoke', 'is_verified', 'created_at')
            ->where('is_active', 1)
            ->whereNull('archived_at')
            ->when($this->city, fn($q) => $q->where('city', $this->city))
            ->when($this->gender, function($q) {
                $genderModel = CacheService::getGenderByName($this->gender);
                return $q->where('gender', $genderModel ? $genderModel->id : null);
            })
            ->when($this->rate, fn($q) => $q->where('incallprice', '<=', $this->rate))
            ->when($this->buts, fn($q) => $q->where('bust', $this->buts))
            ->when($this->ori, fn($q) => $q->where('orientation', $this->ori))
            ->when($this->incall, fn($q) => $q->where('incall', 1))
            ->when($this->outcall, fn($q) => $q->where('outcall', 1))
            ->when($this->nonsmoker, fn($q) => $q->where('smoke', 0))
            ->when($this->withreviews, fn($q) => $q->whereHas('reviews'))
            ->when($this->ethnicity, fn($q) => $q->where('ethnicity', $this->ethnicity))
            ->when($this->nationality, fn($q) => $q->where('nationality', $this->nationality))
            ->when($this->agefrom, fn($q) => $q->where('age', '>=', $this->agefrom))
            ->when($this->ageto, fn($q) => $q->where('age', '<=', $this->ageto))
            ->when($this->heightfrom, fn($q) => $q->where('height', '>=', $this->heightfrom))
            ->when($this->heightto, fn($q) => $q->where('height', '<=', $this->heightto))
            ->when($this->name, fn($q) => $q->where('name', 'like', '%' . $this->name . '%'))
            ->when($this->language, function($q) {
                return $q->whereHas('languages', fn($query) => $query->where('language_id', $this->language));
            })
            ->when($this->isshaved, fn($q) => $q->where('shaved', $this->isshaved))
            ->when($this->haircolor, fn($q) => $q->where('haircolor', $this->haircolor))
            ->when($this->verified, fn($q) => $q->where('is_verified', 1))
            ->when($this->profiletype, function($q) {
                return $q->whereHas('user', fn($query) => $query->where('type', $this->profiletype));
            })
            ->when($this->sservices && count($this->sservices) > 0, function($q) {
                return $q->whereHas('services', fn($query) => $query->whereIn('service_id', $this->sservices));
            })
            ->when(!empty($auctionProfileIds), fn($q) => $q->whereNotIn('id', $auctionProfileIds))
            // Optimized eager loading
            ->with([
                'singleimg:id,user_id,profile_id,image',
                'coverimg:id,user_id,profile_id,image',
                'multipleimgss' => fn($query) => $query->select('id', 'user_id', 'profile_id', 'image')->limit(6),
                'photoverify:id,profile_id,status',
                'package:id,name'
            ])
            ->get();

        // Separate profiles by package type
        return [
            'vip' => $query->filter(fn($p) => in_array($p->package_id, $vipPackageIds))->values(),
            'featured' => $query->filter(fn($p) => in_array($p->package_id, $featuredPackageIds))->values(),
            'basic' => $query->filter(fn($p) => in_array($p->package_id, $basicPackageIds))->values(),
            'free' => $query->filter(fn($p) => $p->package_id === null || (!in_array($p->package_id, $vipPackageIds) && !in_array($p->package_id, $featuredPackageIds) && !in_array($p->package_id, $basicPackageIds)))->values(),
            'total' => $query->count(),
        ];
    });

    // Sort each group with rotation (done outside cache as it changes every 30 min)
    $vipProfiles = $this->sortProfilesWithDateBracketRotation(collect($allProfiles['vip']), $sortDirection);
    $featuredProfiles = $this->sortProfilesWithDateBracketRotation(collect($allProfiles['featured']), $sortDirection);
    $basicProfiles = $this->sortProfilesWithDateBracketRotation(collect($allProfiles['basic']), $sortDirection);
    $freeProfiles = $this->sortProfilesWithDateBracketRotation(collect($allProfiles['free']), $sortDirection);

    // Merge in order: VIP -> Featured -> Basic -> Free
    $sortedProfiles = $vipProfiles->concat($featuredProfiles)->concat($basicProfiles)->concat($freeProfiles);

    // Manual pagination using Livewire's page tracking
    $page = $this->getPage();
    $perPage = 36;
    $offset = ($page - 1) * $perPage;
    
    $paginatedItems = $sortedProfiles->slice($offset, $perPage)->values();
    
    return new \Illuminate\Pagination\LengthAwarePaginator(
        $paginatedItems,
        $sortedProfiles->count(),
        $perPage,
        $page,
        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'page']
    );
}


    /**
     * Get currency information for the current city
     */
    private function getCityCurrency($currentCity)
    {
        // Default to AED (Dubai) with conversion rate to USD
        $defaultCurrency = [
            'code' => 'AED',
            'symbol' => 'AED',
            'to_usd_rate' => 0.27
        ];
        
        if (!$currentCity || !$currentCity->country) {
            return $defaultCurrency;
        }
        
        // Map countries to their currencies with USD conversion rates
        $currencyMap = [
            'United Arab Emirates' => ['code' => 'AED', 'symbol' => 'AED', 'to_usd_rate' => 0.27],
            'UAE' => ['code' => 'AED', 'symbol' => 'AED', 'to_usd_rate' => 0.27],
            'Saudi Arabia' => ['code' => 'SAR', 'symbol' => 'SAR', 'to_usd_rate' => 0.27],
            'Qatar' => ['code' => 'QAR', 'symbol' => 'QAR', 'to_usd_rate' => 0.27],
            'Kuwait' => ['code' => 'KWD', 'symbol' => 'KWD', 'to_usd_rate' => 3.25],
            'Bahrain' => ['code' => 'BHD', 'symbol' => 'BHD', 'to_usd_rate' => 2.65],
            'Oman' => ['code' => 'OMR', 'symbol' => 'OMR', 'to_usd_rate' => 2.60],
            'United States' => ['code' => 'USD', 'symbol' => '$', 'to_usd_rate' => 1.00],
            'United Kingdom' => ['code' => 'GBP', 'symbol' => '£', 'to_usd_rate' => 1.27],
            'India' => ['code' => 'INR', 'symbol' => '₹', 'to_usd_rate' => 0.012],
            'Pakistan' => ['code' => 'PKR', 'symbol' => 'Rs', 'to_usd_rate' => 0.0036],
            'Thailand' => ['code' => 'THB', 'symbol' => '฿', 'to_usd_rate' => 0.029],
            'Philippines' => ['code' => 'PHP', 'symbol' => '₱', 'to_usd_rate' => 0.018],
            'Singapore' => ['code' => 'SGD', 'symbol' => 'S$', 'to_usd_rate' => 0.74],
            'Malaysia' => ['code' => 'MYR', 'symbol' => 'RM', 'to_usd_rate' => 0.22],
            'Indonesia' => ['code' => 'IDR', 'symbol' => 'Rp', 'to_usd_rate' => 0.000063],
            'Australia' => ['code' => 'AUD', 'symbol' => 'A$', 'to_usd_rate' => 0.66],
            'Canada' => ['code' => 'CAD', 'symbol' => 'C$', 'to_usd_rate' => 0.74],
            'Euro' => ['code' => 'EUR', 'symbol' => '€', 'to_usd_rate' => 1.09],
            'Switzerland' => ['code' => 'CHF', 'symbol' => 'CHF', 'to_usd_rate' => 1.13],
            'Japan' => ['code' => 'JPY', 'symbol' => '¥', 'to_usd_rate' => 0.0069],
            'China' => ['code' => 'CNY', 'symbol' => '¥', 'to_usd_rate' => 0.14],
            'Russia' => ['code' => 'RUB', 'symbol' => '₽', 'to_usd_rate' => 0.010],
            'Brazil' => ['code' => 'BRL', 'symbol' => 'R$', 'to_usd_rate' => 0.20],
            'Mexico' => ['code' => 'MXN', 'symbol' => 'Mex$', 'to_usd_rate' => 0.059],
            'South Africa' => ['code' => 'ZAR', 'symbol' => 'R', 'to_usd_rate' => 0.055],
            'Turkey' => ['code' => 'TRY', 'symbol' => '₺', 'to_usd_rate' => 0.031],
            'Egypt' => ['code' => 'EGP', 'symbol' => 'E£', 'to_usd_rate' => 0.020],
        ];
        
        // Check if country has a currency mapping
        return $currencyMap[$currentCity->country] ?? $defaultCurrency;
    }

    public function render()
    {
        // Load auctions on every render to ensure they're fresh for pagination
        $this->loadAuctions();
        
        // Get current city details - try city ID first as it's most reliable
        $currentCity = null;
        
        // Priority 1: Use city ID if available (most reliable)
        if ($this->city && is_numeric($this->city)) {
            $currentCity = Cache::remember("cache:city:id:{$this->city}", 3600, function() {
                return City::find($this->city);
            });
        }
        
        // Priority 2: Try selectedcity as slug
        if (!$currentCity && $this->selectedcity) {
            $slug = strtolower(trim($this->selectedcity));
            $currentCity = CacheService::getCityBySlug($slug);
            
            // Also try finding by name if slug doesn't work
            if (!$currentCity) {
                $currentCity = Cache::remember("cache:city:name:" . strtolower($this->selectedcity), 3600, function() {
                    return City::whereRaw('LOWER(name) = ?', [strtolower($this->selectedcity)])->first();
                });
            }
        }
        
        // Priority 3: Try cityname
        if (!$currentCity && $this->cityname) {
            $currentCity = Cache::remember("cache:city:name:" . strtolower($this->cityname), 3600, function() {
                return City::whereRaw('LOWER(name) = ?', [strtolower($this->cityname)])->first();
            });
        }
        
        // Default fallback to Dubai
        if (!$currentCity) {
            $currentCity = Cache::remember("cache:city:id:229", 3600, function() {
                return City::find(229);
            });
        }
        
        // Get nearby cities (cached)
        $nearbyCities = collect();
        if ($currentCity && $currentCity->country) {
            $nearbyCities = CacheService::getFeaturedCities($currentCity->country, $currentCity->id);
        }
        
        return view('livewire.home-page', [
            'profiles' => $this->getProfiles(),
            // Use CacheService for all lookup data
            'listings' => Cache::remember('cache:listings', CacheService::TTL_LOOKUP, function() {
                return Listing::select('id', 'name')->get();
            }),
            'services' => CacheService::getServices(),
            'currencies' => CacheService::getCurrencies(),
            'ethnicities' => CacheService::getEthnicities(), 
            'busts' => CacheService::getBusts(),
            'hairs' => CacheService::getHairColors(),
            'countries' => CacheService::getCountries(),
            'languages' => CacheService::getLanguages(),
            'reviews' => CacheService::getRecentReviews(10),
            'cityname' => $this->cityname ?? 'dubai',
            'gender' => $this->gender,
            'city' => $this->city,
            'selectedcity' => $this->selectedcity,
            'auctions' => $this->auctions,
            'currentCity' => $currentCity,
            'nearbyCities' => $nearbyCities,
            'sortInfo' => $this->getSortInfo(),
            'cityCurrency' => $this->getCityCurrency($currentCity)
        ]);
    }

    
}