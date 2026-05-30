<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{UsersProfile, Review, Question, City, Gender, Service, Currency, Bust, Ethnicity, HairColor, Language, Country};
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NewsPage extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $type = 'new-escorts'; // new-escorts, new-reviews, new-questions
    public $gender = 'female';
    public $city = 'dubai';
    public $cityname = 'Dubai';
    public $selectedcity = 'dubai';
    public $perPage = 4;
    
    // Search filters
    public $sservices = [];
    public $rate = null;
    public $currency = 248;
    public $buts;
    public $ori;
    public $nonsmoker;
    public $incall;
    public $outcall;
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
    
    protected $queryString = [
        'gender' => ['except' => 'female'],
        'rate' => ['except' => ''],
        'currency' => ['except' => 248],
        'ethnicity' => ['except' => ''],
        'nationality' => ['except' => ''],
        'name' => ['except' => '']
    ];
    
    public function loadMore()
    {
        $this->perPage += 4;
    }

    /**
     * True when no user-specific filter is set, so the query result depends
     * only on (type, gender, city, perPage, page) and is safe to share across
     * visitors via a short-TTL cache. Filtered queries skip the cache so each
     * search/refinement still hits the DB and returns fresh data.
     */
    protected function isCacheable(): bool
    {
        return empty($this->sservices)
            && empty($this->rate)
            && empty($this->buts)
            && empty($this->ori)
            && empty($this->nonsmoker)
            && empty($this->incall)
            && empty($this->outcall)
            && empty($this->withreviews)
            && empty($this->ethnicity)
            && empty($this->nationality)
            && empty($this->agefrom)
            && empty($this->ageto)
            && empty($this->heightfrom)
            && empty($this->heightto)
            && empty($this->name)
            && empty($this->language)
            && empty($this->isshaved)
            && empty($this->haircolor);
    }

    protected function cacheKey(string $bucket): string
    {
        $page = (int) request()->query('page', 1);
        $type = $this->type ?: 'all';
        return "news:{$bucket}:{$this->gender}:{$this->city}:{$type}:{$this->perPage}:p{$page}";
    }

    /**
     * 5 min — short enough that newly created profiles/reviews/questions show
     * up promptly, long enough that consecutive visitors share a cached payload
     * on the hot default views (e.g. /female-escort-news-in-dubai).
     */
    const NEWS_CACHE_TTL = 300;
    
    public function updatedSservices($value)
    {
        if (is_string($value) && $value) {
            $this->sservices = array_map('intval', explode(',', $value));
        } elseif (is_array($value)) {
            $this->sservices = array_map('intval', array_filter($value, 'is_numeric'));
        } elseif (empty($value)) {
            $this->sservices = [];
        }
        
        if (!is_array($this->sservices)) {
            $this->sservices = [];
        }
        
        $this->resetPage();
    }
    
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
    
    public function search()
    {
        $gender = $this->gender ?: 'female';
        $cityModel = null;
        
        if ($this->selectedcity) {
            $cityModel = City::where('slug', $this->selectedcity)->first();
        }
        
        $city = $cityModel ? $cityModel->slug : 'dubai';
        $typeUrl = $this->type ? '/' . $this->type : '';
        
        // Update URL in browser without refresh
        $this->js("window.history.pushState({}, '', '/{$gender}-escort-news-in-{$city}{$typeUrl}')");
        
        // Reset pagination to first page
        $this->resetPage();
    }
    
    public function mount($gender = 'female', $city = 'dubai', $type = null)
    {
        $this->type = $type ?? 'all';
        $this->gender = $gender;
        $this->selectedcity = $city;
        
        // Get city details
        $cityModel = City::where('slug', $city)->first();
        if ($cityModel) {
            $this->city = $cityModel->id;
            $this->cityname = $cityModel->name;
        } else {
            $this->city = 229; // Dubai default
            $this->cityname = 'Dubai';
        }
    }
    
    public function getNewEscorts()
    {
        // Caching the LengthAwarePaginator was rolled back — it caused 500s on
        // prod (Redis) when Livewire's loadMore() tried to deserialize the
        // cached Eloquent paginator with eager-loaded relations. Local file
        // cache survived the roundtrip; Redis didn't. The HTML fragment cache
        // in render() still gives us a fast path on prod for repeat visitors.
        return $this->fetchNewEscorts();
    }

    protected function fetchNewEscorts()
    {
        $genderModel = CacheService::getGenderByName($this->gender);
        $genderId = $genderModel ? $genderModel->id : null;

        $paginator = UsersProfile::where('city', $this->city)
            ->where('gender', $genderId)
            ->where('is_active', 1)
            ->whereNull('archived_at')
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
            ->when($this->sservices && count($this->sservices) > 0, function($q) {
                return $q->whereHas('services', function($query) {
                    $query->whereIn('service_id', $this->sservices);
                });
            })
            ->with([
                'singleimg:id,user_id,profile_id,image',
                'coverimg:id,user_id,profile_id,image',
                // multipleimgs intentionally omitted — attached manually below
                // (see attachThumbnailImages for why).
                'photoverify:id,profile_id,status',
                'gcity:id,name,slug',
                'gnat:id,nicename'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $this->attachThumbnailImages($paginator->getCollection());
        return $paginator;
    }

    public function getNewReviews()
    {
        return $this->fetchNewReviews();
    }

    protected function fetchNewReviews()
    {
        $genderModel = CacheService::getGenderByName($this->gender);
        $genderId = $genderModel ? $genderModel->id : null;

        $paginator = Review::whereHas('profile', function($query) use ($genderId) {
                $query->where('city', $this->city)
                      ->where('gender', $genderId)
                      ->where('is_active', 1)
                      ->whereNull('archived_at');
            })
            ->with([
                'profile' => function($query) {
                    $query->with([
                        'singleimg:id,user_id,profile_id,image',
                        'coverimg:id,user_id,profile_id,image',
                        // multipleimgs attached manually below — see attachThumbnailImages.
                        'photoverify:id,profile_id,status',
                        'gcity:id,name,slug',
                        'gnat:id,nicename'
                    ]);
                },
                'user:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $this->attachThumbnailImages($paginator->getCollection()->pluck('profile')->filter());
        return $paginator;
    }
    
    public function getNewQuestions()
    {
        return $this->fetchNewQuestions();
    }

    protected function fetchNewQuestions()
    {
        $genderModel = CacheService::getGenderByName($this->gender);
        $genderId = $genderModel ? $genderModel->id : null;

        $paginator = Question::whereHas('profile', function($query) use ($genderId) {
                $query->where('city', $this->city)
                      ->where('gender', $genderId)
                      ->where('is_active', 1)
                      ->whereNull('archived_at');
            })
            ->whereNotNull('answer')
            ->where('answer', '!=', '')
            ->with([
                'profile' => function($query) {
                    $query->with([
                        'singleimg:id,user_id,profile_id,image',
                        'coverimg:id,user_id,profile_id,image',
                        // multipleimgs attached manually below — see attachThumbnailImages.
                        'photoverify:id,profile_id,status',
                        'gcity:id,name,slug',
                        'gnat:id,nicename'
                    ]);
                },
                'askedBy:id,name'
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate($this->perPage);

        $this->attachThumbnailImages($paginator->getCollection()->pluck('profile')->filter());
        return $paginator;
    }
    
    public function getAllNews()
    {
        return $this->fetchAllNews();
    }

    protected function fetchAllNews()
    {
        $genderModel = CacheService::getGenderByName($this->gender);
        $genderId = $genderModel ? $genderModel->id : null;

        // Get escorts — multipleimgs is attached manually below via
        // attachThumbnailImages() to dodge Laravel's eager-load LIMIT footgun
        // (same fix HomePage::attachThumbnailImages uses, see HomePage.php:723).
        $escorts = UsersProfile::where('city', $this->city)
            ->where('gender', $genderId)
            ->where('is_active', 1)
            ->whereNull('archived_at')
            ->with([
                'singleimg', 'coverimg', 'photoverify', 'gcity', 'gnat'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($this->perPage)
            ->get()
            ->map(function($item) {
                $item->item_type = 'escort';
                $item->sort_date = $item->created_at;
                return $item;
            });

        $this->attachThumbnailImages($escorts);

        // Get questions with item_type attribute
        $questions = Question::whereHas('profile', function($query) use ($genderId) {
                $query->where('city', $this->city)
                      ->where('gender', $genderId)
                      ->where('is_active', 1)
                      ->whereNull('archived_at');
            })
            ->whereNotNull('answer')
            ->where('answer', '!=', '')
            ->with(['profile.singleimg', 'profile.coverimg', 'profile.photoverify', 'profile.gcity', 'profile.gnat', 'askedBy'])
            ->orderBy('updated_at', 'desc')
            ->limit($this->perPage)
            ->get()
            ->map(function($item) {
                $item->item_type = 'question';
                $item->sort_date = $item->updated_at;
                return $item;
            });

        $this->attachThumbnailImages($questions->pluck('profile')->filter());

        // Merge and sort all items by date
        $allItems = $escorts->concat($questions)->sortByDesc('sort_date')->take($this->perPage)->values();
        
        // Create a fake paginator for consistency
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $allItems,
            1000, // Fake total - we'll just keep loading
            $this->perPage,
            1,
            ['path' => request()->url()]
        );
    }

    /**
     * Manually attach up to 3 thumbnail images per profile.
     *
     * Copied from HomePage::attachThumbnailImages. Eloquent's eager-load with
     * any LIMIT (either in the relation method itself, or via a closure
     * constraint) applies the LIMIT to the whole IN(...) result, not per
     * parent — so on the news page, profiles like Daraline (4243) randomly
     * end up with zero thumbnails depending on cache state and worker race.
     * Running one grouped query and setRelation()'ing the result manually
     * makes the count per profile deterministic.
     */
    protected function attachThumbnailImages($profiles): void
    {
        if ($profiles->isEmpty()) {
            return;
        }
        $profileIds = $profiles->pluck('id')->filter()->values()->all();
        if (empty($profileIds)) {
            return;
        }
        $imagesByProfile = \App\Models\ProfileImage::query()
            ->select('id', 'user_id', 'profile_id', 'image', 'is_main')
            ->whereIn('profile_id', $profileIds)
            ->where(function ($q) {
                $q->whereNull('is_main')->orWhere('is_main', '!=', 1);
            })
            ->orderBy('profile_id')
            ->orderBy('id')
            ->get()
            ->groupBy('profile_id');
        foreach ($profiles as $profile) {
            $thumbs = $imagesByProfile->get($profile->id, collect())->take(3)->values();
            $profile->setRelation('multipleimgs', $thumbs);
            $profile->setRelation('multipleimgss', $thumbs);
        }
    }

    public function render()
    {
        $data = [];
        
        switch($this->type) {
            case 'all':
                $data['items'] = $this->getAllNews();
                $data['title'] = "{$this->cityname} Escort News";
                break;
            case 'new-escorts':
                $data['items'] = $this->getNewEscorts();
                $data['title'] = "Escort News {$this->cityname}: new escorts";
                break;
            case 'new-reviews':
                $data['items'] = $this->getNewReviews();
                $data['title'] = "Escort News {$this->cityname}: new reviews";
                break;
            case 'new-questions':
                $data['items'] = $this->getNewQuestions();
                $data['title'] = "Escort News {$this->cityname}: new answers to questions";
                break;
            default:
                $data['items'] = $this->getAllNews();
                $data['title'] = "{$this->cityname} Escort News";
        }
        
        // Reference tables — cached so logged-in users (who bypass both CF edge
        // cache and the origin page.cache via the evoory_auth cookie) don't pay
        // for ~12 lookup queries on every render. These rarely change; admin
        // edits to lookups can wait up to TTL_LOOKUP for the cache to refresh.
        $haircolors = Cache::remember('news:haircolors', CacheService::TTL_LOOKUP, fn() => HairColor::all());
        $countries = Cache::remember('news:countries', CacheService::TTL_LOOKUP, fn() => Country::orderBy('nicename')->get());

        $data['genders'] = Cache::remember('news:genders', CacheService::TTL_STATIC, fn() => Gender::all());
        $data['cities'] = Cache::remember('news:cities', CacheService::TTL_LOOKUP, fn() => City::orderBy('name')->get());
        $data['services'] = Cache::remember('news:services', CacheService::TTL_LOOKUP, fn() => Service::orderBy('name')->get());
        $data['currencies'] = Cache::remember('news:currencies', CacheService::TTL_LOOKUP, fn() =>
            Currency::select('id', 'code', 'symbol')
                ->get()
                ->unique('code')
                ->sortBy('code')
                ->values()
        );
        $data['busts'] = Cache::remember('news:busts', CacheService::TTL_LOOKUP, fn() => Bust::all());
        $data['ethnicities'] = Cache::remember('news:ethnicities', CacheService::TTL_LOOKUP, fn() => Ethnicity::all());
        $data['haircolors'] = $haircolors;
        $data['hairs'] = $haircolors;
        $data['languages'] = Cache::remember('news:languages', CacheService::TTL_LOOKUP, fn() => Language::all());
        $data['countries'] = $countries;
        $data['nationalities'] = $countries;

        // Pre-render the heavy activity-items foreach into a string. On Redis
        // (prod) we cache the rendered HTML so subsequent hits skip both Blade
        // compilation and template rendering — a clear win because Redis reads
        // are ~sub-ms. On the file cache driver (local dev) we render fresh
        // every request: a 50-100KB cache file read can cost more disk I/O than
        // the Blade render saves, and the eloquent collection cache further up
        // already eliminates the slow DB queries.
        $itemsViewData = [
            'items' => $data['items'],
            'type' => $this->type,
            'gender' => $this->gender,
            'selectedcity' => $this->selectedcity,
            'cityname' => $this->cityname,
        ];
        $useHtmlCache = $this->isCacheable() && config('cache.default') === 'redis';
        if ($useHtmlCache) {
            $data['activityItemsHtml'] = Cache::remember(
                $this->cacheKey('itemshtml'),
                self::NEWS_CACHE_TTL,
                fn() => view('livewire.partials.news-activity-items', $itemsViewData)->render()
            );
        } else {
            $data['activityItemsHtml'] = view('livewire.partials.news-activity-items', $itemsViewData)->render();
        }

        return view('livewire.news-page', $data);
    }
    
    public function paginationView()
    {
        return 'vendor.livewire.custom';
    }
}
