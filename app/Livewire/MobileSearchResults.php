<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UsersProfile;
use App\Models\Gender;
use App\Models\Review;
use App\Models\Auction;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MobileSearchResults extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'gender' => ['except' => 'female'],
        'selectedcity' => ['except' => 'Dubai'],
        'city' => ['except' => 229],
        'currency' => ['except' => ''],
        'rate' => ['except' => ''],
        'ethnicity' => ['except' => ''],
        'nationality' => ['except' => ''],
        'name' => ['except' => ''],
        'currentPage' => ['except' => 1, 'as' => 'page']

    ];
    
    // Search parameters
    public $gender = 'female';
    public $selectedcity = 'Dubai';
    public $city = 229;
    public $currency;
    public $rate;
    public $sservices = [];
    public $buts;
    public $ori;
    public $incall;
    public $outcall;
    public $nonsmoker;
    public $withreviews;
    public $verified;
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
    public $cityname = 'dubai';
    public $auctions = [];

    public $currentPage = 1;
    public $perPage = 25;
    public $totalPages = 1;
    
    public function mount($gender, Request $request)
    {
        $this->gender = $gender;
        
        // Get search parameters from request
        $this->selectedcity = $request->input('selectedcity', 'Dubai');
        $this->city = $request->input('city', 229);
        $this->currency = $request->input('currency');
        $this->rate = $request->input('rate');
        $this->sservices = $request->input('sservices', []);
        $this->buts = $request->input('buts');
        $this->ori = $request->input('ori');
        $this->incall = $request->input('incall');
        $this->outcall = $request->input('outcall');
        $this->nonsmoker = $request->input('nonsmoker');
        $this->withreviews = $request->input('withreviews');
        $this->verified = $request->input('verified');
        $this->ethnicity = $request->input('ethnicity');
        $this->nationality = $request->input('nationality');
        $this->agefrom = $request->input('agefrom');
        $this->ageto = $request->input('ageto');
        $this->heightfrom = $request->input('heightfrom');
        $this->heightto = $request->input('heightto');
        $this->name = $request->input('name');
        $this->language = $request->input('language');
        $this->isshaved = $request->input('isshaved');
        $this->haircolor = $request->input('haircolor');
        $this->cityname = strtolower($this->selectedcity);
        
        $this->currentPage = (int)$request->input('page', 1);
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }

        // Debug: Log mount parameters
        \Log::info('MobileSearchResults mount()', [
            'gender' => $this->gender,
            'city' => $this->city,
            'selectedcity' => $this->selectedcity,
            'url' => $request->fullUrl(),
            'all_input' => $request->all(),
        ]);

        // Load auctions
        $this->loadAuctions();
    }

    public function goToPage($page)
    {
        // Redirect to the same URL but with a different page parameter
        return redirect()->to(request()->fullUrlWithQuery(['page' => $page]));
    }
    
    public function previousPage()
    {
        if ($this->currentPage > 1) {
            return $this->goToPage($this->currentPage - 1);
        }
    }
    
    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            return $this->goToPage($this->currentPage + 1);
        }
    }


    public function paginationView()
    {
        return 'vendor.livewire.custom';
    }
    
    protected function loadAuctions()
    {
        // Get gender ID if needed
        $genderModel = Gender::where('name', $this->gender)->first();
        $genderId = $genderModel ? $genderModel->id : null;
        
        // Initialize auctions collection
        $this->auctions = collect();
          
        // Use a more efficient query approach
        if (auth()->check()) {
            // Get only the necessary columns and limit the related data
            $query = Auction::select('id', 'city_id', 'gender', 'status', 'spot_number', 'winner_profile_id', 'end_date', 'current_price')
                ->where('city_id', $this->city)
                ->where('gender', $this->gender)
                ->orderBy('spot_number');
                
            // Use a more targeted approach for relationships
            $query->with([
                'winnerProfile:id,name,user_id,city,gender,about,package_id,slug',
                'winnerProfile.singleimg:id,user_id,profile_id,image',
                'winnerProfile.coverimg:id,user_id,profile_id,image',
                'winnerProfile.photoverify:id,profile_id,status',
                'city:id,name'
            ]);
            
            // Use scope to filter only valid spots (active OR ended but still within validity period)
            $auctions = $query->withValidSpot()->get();
            
            // Group by spot number and take the first of each group
            $spotGroups = $auctions->groupBy('spot_number');
            
            foreach ($spotGroups as $spotNumber => $spotAuctions) {
                // If there's an active auction for this spot, use it
                $activeSpot = $spotAuctions->firstWhere('status', 'active');
                if ($activeSpot) {
                    $this->auctions->push($activeSpot);
                } else {
                    // Otherwise use the ended auction with a winner (already filtered by validity)
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
        
        // Limit to 6 auctions
        $this->auctions = $this->auctions->take(6);
        
        foreach ($this->auctions as $auction) {
            $auction->timeLeft = Carbon::now()->diffForHumans($auction->end_date, ['parts' => 1, 'short' => true]);
            $auction->daysLeft = Carbon::now()->diffInDays($auction->end_date);
            
            // If no winner profile, get a featured profile for display (only for active auctions)
            if (!$auction->winnerProfile && $auction->status == 'active') {
                $auction->featuredProfile = UsersProfile::select('id', 'name', 'user_id', 'city', 'gender', 'package_id')
                    ->where('gender', $genderId)
                    ->where('city', $this->city)
                    ->where('package_id', 21) // Premium package
                    ->with([
                        'singleimg:id,user_id,profile_id,image',
                        'multipleimgs:id,user_id,profile_id,image'
                    ])
                    ->inRandomOrder()
                    ->first();
            }
        }
    }
    
    protected function getProfiles()
    {

        $auctionProfileIds = $this->auctions
        ->pluck('winner_profile_id')
        ->filter()
        ->toArray();

        // Debug: Log the search parameters
        \Log::info('MobileSearchResults Query', [
            'city' => $this->city,
            'city_type' => gettype($this->city),
            'gender' => $this->gender,
        ]);

         $query = UsersProfile::query()
            ->select('id', 'name', 'user_id', 'city', 'gender', 'about', 'package_id', 'slug', 'bust', 'orientation', 'ethnicity', 'nationality', 'age', 'height', 'shaved', 'haircolor', 'incall', 'incallcurr', 'incallprice', 'smoke')
            ->where('is_active', 1)
            ->whereNull('archived_at')
            ->when($this->city, fn($q) => $q->where('city', (int)$this->city))
            ->when($this->gender, function($q) {
                $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
                \Log::info('Gender lookup', ['gender_input' => $this->gender, 'gender_id' => $genderModel?->id]);
                return $q->where('gender', $genderModel ? $genderModel->id : null);
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
                return $q->whereHas('photoverify', function($query) {
                    $query->where('status', 'approved');
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
            ->when($this->sservices && count(array_filter($this->sservices)) > 0, function($q) {
                return $q->whereHas('services', function($query) {
                    $query->whereIn('service_id', array_filter($this->sservices));
                });
            })
            ->with([
                'singleimg:id,user_id,profile_id,image',
                'coverimg:id,user_id,profile_id,image',
                'multipleimgs:id,user_id,profile_id,image',
                'multipleimgss:id,user_id,profile_id,image',
                'photoverify:id,profile_id,status',
                'reviews:id,profile_id'
            ])->when(!empty($auctionProfileIds), function($q) use ($auctionProfileIds) {
            return $q->whereNotIn('id', $auctionProfileIds);
        })->orderBy('package_id', 'desc');

        // Debug: Log the SQL query
        \Log::info('MobileSearchResults SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        $totalCount = $query->count();
        
        \Log::info('MobileSearchResults Results', [
            'totalCount' => $totalCount,
            'auctionProfileIds' => $auctionProfileIds,
        ]);
        
        $this->totalPages = ceil($totalCount / $this->perPage);
        
        // Get paginated results
        $profiles = $query->skip(($this->currentPage - 1) * $this->perPage)
                         ->take($this->perPage)
                         ->get();
                         
        // Create a custom paginator
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $profiles,
            $totalCount,
            $this->perPage,
            $this->currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return $paginator;
    }
    
    public function render()
    {
        $profiles = $this->getProfiles();
        
        // TEMP DEBUG - Remove after testing
        \Log::info('MobileSearchResults Render', [
            'profiles_count' => $profiles->count(),
            'profiles_total' => $profiles->total(),
            'currentPage' => $this->currentPage,
        ]);
        
        // Get recent reviews
        $reviews = Review::select('id', 'review', 'user_id', 'profile_id')
            ->with(['getuser:id,name', 'getpic:id,user_id,profile_id,image'])
            ->take(5)
            ->get();
            
        return view('livewire.mobile-search-results', [
            'profiles' => $profiles,
            'reviews' => $reviews,
            'cityname' => $this->cityname,
            'auctions' => $this->auctions,
            'currentPage' => $this->currentPage,
            'totalPages' => $this->totalPages
        ])
        ->layout('components.layouts.app');
    }
}