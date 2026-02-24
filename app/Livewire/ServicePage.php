<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Models\{Listing, Service, UserService, Gender, Currency, Ethnicity, 
    Bust, HairColor, Language, UserLanguage, UsersProfile, City, Country, Review, Auction};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
    public $serviceId; // Service ID for filtering
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
        
        // Find the service by slug
        $serviceModel = Service::where('slug', $service)
            ->orWhere('name', 'like', '%' . str_replace('-', ' ', $service) . '%')
            ->first();
        
        if ($serviceModel) {
            $this->serviceId = $serviceModel->id;
            $this->serviceName = $serviceModel->name;
            $this->sservices = [$serviceModel->id]; // Pre-filter by this service
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
        $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
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
        
        $query = UsersProfile::query()
            ->select('id', 'name', 'user_id', 'city', 'gender', 'about', 'package_id', 'slug', 'bust', 'orientation', 'ethnicity', 'nationality', 'age', 'height', 'shaved', 'haircolor', 'incall', 'incallcurr', 'incallprice', 'smoke', 'created_at')
            ->when($this->city, fn($q) => $q->where('city', $this->city))
            ->when($this->gender, function($q) {
                $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
                return $q->where('gender', $genderModel ? $genderModel->id : null);
            })
            // Always filter by the service from URL
            ->when($this->serviceId, function($q) {
                return $q->whereHas('services', function($query) {
                    $query->where('service_id', $this->serviceId);
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
                'multipleimgs' => function($query) {
                    $query->select('id', 'user_id', 'profile_id', 'image')->limit(6);
                },
                'photoverify:id,profile_id,status',
                'reviews' => function($query) {
                    $query->select('id', 'profile_id')->limit(1);
                }
            ])
            ->get();

        $vipProfiles = $query->where('package_id', 21);
        $featuredProfiles = $query->where('package_id', 20);
        $basicProfiles = $query->whereIn('package_id', [19, null]);

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
        
        // Get popular services in this city with counts
        $popularServices = Service::select('services.id', 'services.name', 'services.slug')
            ->join('user_services', 'services.id', '=', 'user_services.service_id')
            ->join('users_profiles', 'user_services.profile_id', '=', 'users_profiles.id')
            ->where('users_profiles.city', $this->city)
            ->when($this->gender, function($q) {
                $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
                return $q->where('users_profiles.gender', $genderModel ? $genderModel->id : null);
            })
            ->groupBy('services.id', 'services.name', 'services.slug')
            ->selectRaw('COUNT(DISTINCT users_profiles.id) as profile_count')
            ->orderBy('profile_count', 'desc')
            ->limit(15)
            ->get();
        
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
            'popularServices' => $popularServices
        ]);
    }
}
