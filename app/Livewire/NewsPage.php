<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{UsersProfile, Review, Question, City, Gender, Service, Currency, Bust, Ethnicity, HairColor, Language, Country};
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
        $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
        $genderId = $genderModel ? $genderModel->id : null;
        
        return UsersProfile::where('city', $this->city)
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
                'multipleimgs:id,user_id,profile_id,image',
                'photoverify:id,profile_id,status',
                'gcity:id,name,slug',
                'gnat:id,nicename'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
    
    public function getNewReviews()
    {
        $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
        $genderId = $genderModel ? $genderModel->id : null;
        
        return Review::whereHas('profile', function($query) use ($genderId) {
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
                        'multipleimgs' => function($q) {
                            $q->select('id', 'user_id', 'profile_id', 'image')->limit(3);
                        },
                        'photoverify:id,profile_id,status',
                        'gcity:id,name,slug',
                        'gnat:id,nicename'
                    ]);
                },
                'user:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
    
    public function getNewQuestions()
    {
        $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
        $genderId = $genderModel ? $genderModel->id : null;
        
        return Question::whereHas('profile', function($query) use ($genderId) {
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
                        'multipleimgs' => function($q) {
                            $q->select('id', 'user_id', 'profile_id', 'image')->limit(3);
                        },
                        'photoverify:id,profile_id,status',
                        'gcity:id,name,slug',
                        'gnat:id,nicename'
                    ]);
                },
                'askedBy:id,name'
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate($this->perPage);
    }
    
    public function getAllNews()
    {
        $genderModel = Gender::whereRaw('LOWER(name) = ?', [strtolower($this->gender)])->first();
        $genderId = $genderModel ? $genderModel->id : null;
        
        // Get escorts with item_type attribute
        $escorts = UsersProfile::where('city', $this->city)
            ->where('gender', $genderId)
            ->where('is_active', 1)
            ->whereNull('archived_at')
            ->with([
                'singleimg', 'coverimg', 'multipleimgs', 'photoverify', 'gcity', 'gnat'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($this->perPage)
            ->get()
            ->map(function($item) {
                $item->item_type = 'escort';
                $item->sort_date = $item->created_at;
                return $item;
            });
            
        // Get questions with item_type attribute
        $questions = Question::whereHas('profile', function($query) use ($genderId) {
                $query->where('city', $this->city)
                      ->where('gender', $genderId)
                      ->where('is_active', 1)
                      ->whereNull('archived_at');
            })
            ->whereNotNull('answer')
            ->where('answer', '!=', '')
            ->with(['profile.singleimg', 'profile.coverimg', 'profile.multipleimgs', 'profile.photoverify', 'profile.gcity', 'profile.gnat', 'askedBy'])
            ->orderBy('updated_at', 'desc')
            ->limit($this->perPage)
            ->get()
            ->map(function($item) {
                $item->item_type = 'question';
                $item->sort_date = $item->updated_at;
                return $item;
            });
        
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
        
        // Add search form data
        $data['genders'] = Gender::all();
        $data['cities'] = City::orderBy('name')->get();
        $data['services'] = Service::orderBy('name')->get();
        $data['currencies'] = Currency::select('id', 'code', 'symbol')
            ->get()
            ->unique('code')
            ->sortBy('code')
            ->values();
        $data['busts'] = Bust::all();
        $data['ethnicities'] = Ethnicity::all(); 
        $data['haircolors'] = HairColor::all();
        $data['hairs'] = HairColor::select('id', 'name')->get(); // Alias for haircolors
        $data['languages'] = Language::all();
        $data['countries'] = Country::orderBy('nicename')->get();
        $data['nationalities'] = Country::orderBy('nicename')->get(); // Same as countries
        
        return view('livewire.news-page', $data);
    }
    
    public function paginationView()
    {
        return 'vendor.livewire.custom';
    }
}
