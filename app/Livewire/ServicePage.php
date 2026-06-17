<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Models\{Listing, Service, UserService, Gender, Currency, Ethnicity,
    Bust, HairColor, Language, UserLanguage, UsersProfile, City, Country, Review, Auction};
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

// NOTE: ServicePage intentionally uses Livewire's default `app` layout. An
// attempt to switch this to `components.layouts.app-evoory` (matching HomePage)
// crashed production with "Allowed memory size exhausted" inside the compiled
// view. The service-page view structure (top-level `@section('headerform')` +
// heavy `@push('css')` blocks + nested `@forelse` over $profiles + the
// search-header component) interacts badly with app-evoory's slot + stack +
// header partial chain in a way the homepage doesn't trigger. Visual parity
// for the search bar / Search button is achieved by the CSS rules embedded
// inside resources/views/components/search-header.blade.php — those apply
// universally regardless of layout, so the user-visible result is the same.
class ServicePage extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $isFavorited = false;
    public $isMobile = false;
    public $loading = true;
    
    // Search filters
    public $city;
    public $selectedcity;
    public $gender;
    public $serviceSlug; // Service slug from URL
    public $serviceName; // Service name for display
    public $serviceId; // Primary service ID (first match) for display/sservices preselect
    public $serviceIds = []; // Every service ID whose slug matches the URL slug
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
    public $cityname;
    public $auctions = [];
    public $showMobileSearch = false;
    public $verified;
    public $profiletype;

    protected $queryString = [
        'gender' => ['except' => ''],
        'rate' => ['except' => ''],
        'currency' => ['except' => 248],
        'ethnicity' => ['except' => ''],
        'nationality' => ['except' => ''],
        'name' => ['except' => ''],
        'verified' => ['except' => ''],
        'profiletype' => ['except' => '']
    ];

    public function mount($service = '', $gender = 'female', $city = 'dubai')
    {
        $this->gender = $gender;
        $this->serviceSlug = $service;

        // Resolve EVERY service row matching this slug, not just the first.
        // The sidebar links pass `strtolower($service->slug)`, but DB slugs
        // may be stored with mixed case or there may be near-duplicate rows
        // sharing the same canonical name. Filtering by a single id meant
        // the sidebar count ("216") and the listing total ("100") could
        // disagree because they were resolving to different services rows.
        // Matching all candidates closes that gap.
        $slugNormalized = strtolower($service);
        $nameLike = '%' . str_replace('-', ' ', $slugNormalized) . '%';

        $serviceModels = Service::whereRaw('LOWER(slug) = ?', [$slugNormalized])
            ->orWhere('name', 'like', $nameLike)
            ->get();

        if ($serviceModels->isNotEmpty()) {
            // Prefer exact slug matches when both exact and LIKE results came
            // back, so the displayed name reflects the actual sidebar entry
            // the user clicked rather than a stray near-name-match.
            $exactSlugMatches = $serviceModels->filter(
                fn ($s) => strtolower($s->slug) === $slugNormalized
            );
            $matches = $exactSlugMatches->isNotEmpty() ? $exactSlugMatches : $serviceModels;

            $this->serviceIds = $matches->pluck('id')->all();
            $primary = $matches->first();
            $this->serviceId = $primary->id;
            $this->serviceName = $primary->name;
            $this->sservices = $this->serviceIds; // Pre-fill advanced search
        }
        
        // Set city
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
    }

    public function updatedSelectedcity($city)
    {
        // Update URL without redirect
        $gender = $this->gender ?? 'female';
        $cityModel = City::find($city);
        $cityName = $cityModel ? $cityModel->name : 'dubai';
        
        // Auto-select currency based on city's country
        if ($cityModel && $cityModel->country) {
            $currencyModel = Currency::where('country', $cityModel->country)->first();
            if ($currencyModel) {
                $this->currency = $currencyModel->id;
                // Dispatch event to update the custom currency combobox
                $this->dispatch('currency-updated', ['currencyId' => $currencyModel->id, 'currencyCode' => $currencyModel->code]);
            }
        }
        
        $path = $this->serviceSlug . '-' . $gender . '-escorts-in-' . strtolower($cityName);
        $this->js("window.history.pushState({}, '', '/{$path}');");
        
        // Reset pagination and trigger search
        $this->resetPage();
    }

    public function search()
    {
        // Close the modal
        $this->dispatch('closeSearchModal');
        // Reset to first page when searching
        $this->resetPage();
    }

    protected function loadAuctions()
    {
        $genderModel = CacheService::getGenderByName($this->gender);
        $genderId = $genderModel ? $genderModel->id : null;
        
        $this->auctions = collect();
        
        if (auth()->check()) {
            $query = Auction::select('id', 'city_id', 'gender', 'status', 'spot_number', 'winner_profile_id', 'end_date', 'current_price')
                ->where('city_id', $this->city)
                ->where('gender', $this->gender)
                ->orderBy('spot_number');
                
            $query->with([
                'winnerProfile:id,name,user_id,city,gender,about,package_id,slug',
                'winnerProfile.singleimg:id,user_id,profile_id,image',
                'winnerProfile.coverimg:id,user_id,profile_id,image',
                'winnerProfile.photoverify:id,profile_id,status',
                'city:id,name'
            ]);
             
            // Use scope to filter only valid spots (active OR ended but still within validity period)
            $auctions = $query->withValidSpot()->get();
            
            $spotGroups = $auctions->groupBy('spot_number');
            
            foreach ($spotGroups as $spotNumber => $spotAuctions) {
                $activeSpot = $spotAuctions->firstWhere('status', 'active');
                if ($activeSpot) {
                    $this->auctions->push($activeSpot);
                } else {
                    // Already filtered by validity via scope
                    $endedWithWinner = $spotAuctions->first();
                    $this->auctions->push($endedWithWinner);
                }
            }
        } else {
            // For non-logged-in users: Only get ended auctions with winners where spot hasn't expired
            $now = Carbon::now();
            $this->auctions = Auction::select('id', 'city_id', 'gender', 'status', 'spot_number', 'winner_profile_id', 'end_date', 'current_price')
                ->where('city_id', $this->city)
                ->where('gender', $this->gender)
                ->where('status', 'ended')
                ->whereNotNull('winner_profile_id')
                // Only show if spot hasn't expired (end_date > now)
                ->where('end_date', '>', $now)
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
        }
        
        $this->auctions = $this->auctions->take(6);
        
        foreach ($this->auctions as $auction) {
            $auction->timeLeft = Carbon::now()->diffForHumans($auction->end_date, ['parts' => 1, 'short' => true]);
            $auction->daysLeft = Carbon::now()->diffInDays($auction->end_date);
            
            if (!$auction->winnerProfile && $auction->status == 'active') {
                $auction->featuredProfile = UsersProfile::select('id', 'name', 'user_id', 'city', 'gender', 'package_id')
                    ->where('gender', $genderId)
                    ->where('city', $this->city)
                    ->where('package_id', 21)
                    ->with([
                        'singleimg:id,user_id,profile_id,image',
                        'multipleimgs:id,user_id,profile_id,image'
                    ])
                    ->inRandomOrder()
                    ->first();
            }
        }
    }

    /**
     * Determine sort direction based on 5-minute intervals
     */
    protected function getSortDirection()
    {
        $now = Carbon::now();
        $minutesSinceMidnight = ($now->hour * 60) + $now->minute;
        $interval = floor($minutesSinceMidnight / 5);
        return ($interval % 2 === 0) ? 'asc' : 'desc';
    }

    public function getSortInfo()
    {
        $now = Carbon::now();
        $minutesSinceMidnight = ($now->hour * 60) + $now->minute;
        $interval = floor($minutesSinceMidnight / 5);
        $sortDirection = ($interval % 2 === 0) ? 'asc' : 'desc';
        $nextRotationMinutes = (($interval + 1) * 5) - $minutesSinceMidnight;
        
        return [
            'direction' => $sortDirection,
            'interval' => $interval,
            'next_rotation_minutes' => $nextRotationMinutes,
            'sorting_order' => $sortDirection === 'asc' ? 'Oldest First' : 'Newest First'
        ];
    }

    protected function getProfiles()
    {
        $auctionProfileIds = $this->auctions
            ->pluck('winner_profile_id')
            ->filter()
            ->toArray();

        $sortDirection = $this->getSortDirection();

        // Only show profiles that are actually live — same filter HomePage
        // applies. Without these, sidebar counts include archived/inactive
        // profiles that the listing would never render, producing the
        // "sidebar 9, listing 2" mismatch.
        $query = UsersProfile::query()
            ->select('id', 'name', 'user_id', 'city', 'gender', 'about', 'package_id', 'slug', 'bust', 'orientation', 'ethnicity', 'nationality', 'age', 'height', 'shaved', 'haircolor', 'incall', 'incallcurr', 'incallprice', 'smoke', 'created_at')
            ->where('is_active', 1)
            ->whereNull('archived_at')
            ->when($this->city, fn($q) => $q->where('city', $this->city))
            ->when($this->gender, function($q) {
                $genderModel = CacheService::getGenderByName($this->gender);
                return $q->where('gender', $genderModel ? $genderModel->id : null);
            })
            // Always filter by the service(s) resolved from the URL slug.
            // Using whereIn matches every services row that shared the slug,
            // so the listing total agrees with the sidebar count for that
            // same slug.
            ->when(!empty($this->serviceIds), function($q) {
                return $q->whereHas('services', function($query) {
                    $query->whereIn('service_id', $this->serviceIds);
                });
            })
            ->when($this->rate, function($q) {
                return $q->where('incallprice', '<=', $this->rate);
            })
            ->when($this->buts, function($q) {
                return $q->where('bust', $this->buts);
            })
            ->when($this->ori, function($q) {
                return $q->where('orientation', $this->ori);
            })
            ->when($this->incall, function($q) {
                return $q->where('incall', 1);
            })
            ->when($this->outcall, function($q) {
                return $q->where('outcall', 1);
            })
            ->when($this->nonsmoker, function($q) {
                return $q->where('smoke', 0);
            })
            ->when($this->withreviews, function($q) {
                return $q->whereHas('reviews');
            })
            ->when($this->verified, function($q) {
                return $q->where('is_verified', 1);
            })
            ->when($this->profiletype, function($q) {
                return $q->whereHas('user', function($query) {
                    $query->where('type', $this->profiletype);
                });
            })
            ->when($this->ethnicity, function($q) {
                return $q->where('ethnicity', $this->ethnicity);
            })
            ->when($this->nationality, function($q) {
                return $q->where('nationality', $this->nationality);
            })
            ->when($this->agefrom, function($q) {
                return $q->where('age', '>=', $this->agefrom);
            })
            ->when($this->ageto, function($q) {
                return $q->where('age', '<=', $this->ageto);
            })
            ->when($this->heightfrom, function($q) {
                return $q->where('height', '>=', $this->heightfrom);
            })
            ->when($this->heightto, function($q) {
                return $q->where('height', '<=', $this->heightto);
            })
            ->when($this->name, function($q) {
                return $q->where('name', 'like', '%' . $this->name . '%');
            })
            ->when($this->language, function($q) {
                return $q->whereHas('languages', function($query) {
                    $query->where('language_id', $this->language);
                });
            })
            ->when($this->isshaved, function($q) {
                return $q->where('shaved', $this->isshaved);
            })
            ->when($this->haircolor, function($q) {
                return $q->where('haircolor', $this->haircolor);
            })
            ->when(!empty($auctionProfileIds), function($q) use ($auctionProfileIds) {
                return $q->whereNotIn('id', $auctionProfileIds);
            })
            ->with([
                'singleimg:id,user_id,profile_id,image',
                'coverimg:id,user_id,profile_id,image',
                // Note: NO ->limit()/->take() on these eager-load constraints.
                // An ->limit(N) inside `with()` applies a single SQL LIMIT
                // across the WHOLE result set, not per parent profile — so on
                // a 25-profile page only N rows total are loaded and the
                // remaining profiles silently render with empty collections.
                // That bug is exactly why side thumbs (multipleimgs) were
                // missing from every profile after the first one on this
                // page. See memory/project_eager_load_limit_footgun.md.
                'multipleimgs:id,user_id,profile_id,image',
                'photoverify:id,profile_id,status',
                'package:id,name',
                'reviews:id,profile_id',
            ])
            ->get();

        // Bucket profiles by package using the same package-id sets HomePage
        // resolves at runtime. Anything that isn't VIP or Featured drops into
        // the "basic" bucket (including NULL and unknown ids) — previously
        // the basic bucket was hardcoded to [19, null] and silently dropped
        // every profile with another package id, which is why "Couples 9"
        // in the sidebar rendered as 2 in the listing.
        $packageIds = CacheService::getPackageIdsByType();
        $vipPackageIds = array_map('intval', $packageIds['vip'] ?? []);
        $featuredPackageIds = array_map('intval', $packageIds['featured'] ?? []);

        $vipProfiles = $query->filter(fn($p) => in_array((int) $p->package_id, $vipPackageIds, true));
        $featuredProfiles = $query->filter(fn($p) => in_array((int) $p->package_id, $featuredPackageIds, true));
        $basicProfiles = $query->reject(fn($p) =>
            in_array((int) $p->package_id, $vipPackageIds, true)
            || in_array((int) $p->package_id, $featuredPackageIds, true)
        );

        $vipProfiles = $vipProfiles->sortBy('created_at', SORT_REGULAR, $sortDirection === 'desc')->values();
        $featuredProfiles = $featuredProfiles->sortBy('created_at', SORT_REGULAR, $sortDirection === 'desc')->values();
        $basicProfiles = $basicProfiles->sortBy('created_at', SORT_REGULAR, $sortDirection === 'desc')->values();

        $sortedProfiles = $vipProfiles->concat($featuredProfiles)->concat($basicProfiles);

        $page = request()->get('page', 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        
        $paginatedItems = $sortedProfiles->slice($offset, $perPage)->values();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $sortedProfiles->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function paginationView()
    {
        return 'vendor.livewire.custom';
    }

    public function render()
    {
        $currentCity = City::where('slug', $this->selectedcity)
            ->orWhere('name', $this->cityname)
            ->first();

        // Get all services for the sidebar
        $allServices = Service::select('id', 'name', 'slug')
            ->orderBy('name', 'asc')
            ->get();

        // Package id sets used by the listing view to pick the VIP /
        // Featured / Basic markup. Must come from the same source as
        // getProfiles() so the view and the controller bucket profiles the
        // same way — otherwise the view's @else branch silently swallows
        // profiles (e.g. "5 escorts" in the header but 0 cards rendered).
        $packageIdsByType = CacheService::getPackageIdsByType();
        $vipPackageIds = array_map('intval', $packageIdsByType['vip'] ?? []);
        $featuredPackageIds = array_map('intval', $packageIdsByType['featured'] ?? []);

        $genderModel = $this->gender ? CacheService::getGenderByName($this->gender) : null;
        $genderId = $genderModel ? $genderModel->id : null;

        $popularServices = Service::query()
            ->join('user_services', 'services.id', '=', 'user_services.service_id')
            ->join('users_profiles', 'user_services.profile_id', '=', 'users_profiles.id')
            ->where('users_profiles.city', $this->city)
            ->where('users_profiles.is_active', 1)
            ->whereNull('users_profiles.archived_at')
            ->when($genderId, fn($q) => $q->where('users_profiles.gender', $genderId))
            ->groupByRaw('LOWER(services.slug)')
            ->selectRaw('MIN(services.id) as id, MIN(services.name) as name, LOWER(services.slug) as slug, COUNT(DISTINCT users_profiles.id) as profile_count')
            ->orderByDesc('profile_count')
            ->limit(15)
            ->get();

        // Fallback: when a city has zero active profiles, the sidebar would
        // otherwise render as a bare "All Services in {City}" heading sitting
        // over an empty list, while a city with data shows a populated list.
        // To keep the layout consistent across cities, fall back to the full
        // services list (alphabetical, no counts) so the sidebar always has
        // something in it.
        if ($popularServices->isEmpty()) {
            $popularServices = Service::select('id', 'name', 'slug')
                ->orderBy('name', 'asc')
                ->limit(15)
                ->get()
                ->map(function ($service) {
                    $service->slug = strtolower($service->slug);
                    $service->profile_count = 0;
                    return $service;
                });
        }
        
        return view('livewire.service-page', [
            'profiles' => $this->getProfiles(),
            'services' => cache()->remember('services_lookup', 3600, function() {
                return Service::select('id', 'name')->get();
            }),
            'currencies' => cache()->remember('currencies_unique_lookup', 3600, function() {
                return Currency::select('id', 'code')
                    ->distinct('code')
                    ->groupBy('code', 'id')
                    ->orderBy('code')
                    ->get()
                    ->unique('code')
                    ->values();
            }), 
            'currentCurrency' => Currency::find($this->currency),
            'ethnicities' => cache()->remember('ethnicities_lookup', 3600, function() {
                return Ethnicity::select('id', 'name')->get();
            }),
            'busts' => cache()->remember('busts_lookup', 3600, function() {
                return Bust::select('id', 'name')->get();
            }),
            'hairs' => cache()->remember('hairs_lookup', 3600, function() {
                return HairColor::select('id', 'name')->get();
            }),
            'countries' => cache()->remember('countries_lookup', 3600, function() {
                return Country::select('id', 'nicename')->get();
            }),
            'languages' => cache()->remember('languages_lookup', 3600, function() {
                return Language::select('id', 'name')->get();
            }),
            'cityname' => $this->cityname ?? 'dubai',
            'gender' => $this->gender,
            'city' => $this->city,
            'selectedcity' => $this->selectedcity,
            'auctions' => $this->auctions,
            'currentCity' => $currentCity,
            'sortInfo' => $this->getSortInfo(),
            'serviceName' => $this->serviceName,
            'serviceSlug' => $this->serviceSlug,
            'allServices' => $allServices,
            'popularServices' => $popularServices,
            'vipPackageIds' => $vipPackageIds,
            'featuredPackageIds' => $featuredPackageIds,
        ]);
    }
}
