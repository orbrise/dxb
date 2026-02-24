<?php

namespace App\Livewire\Profile\Users;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\UsersProfile;
use App\Models\ProfileVisit;
use App\Models\PhoneClick;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class UserStatistics extends Component
{
    // All user's profile IDs
    public $userProfileIds = [];
    
    // Stats
    public $last30DaysMessages = 0;
    public $last30DaysPhoneViews = 0;
    public $last30DaysViews = 0;
    
    public $allTimeMessages = 0;
    public $allTimePhoneViews = 0;
    public $allTimeViews = 0;
    
    // Chart data
    public $chartStartDate;
    public $chartEndDate;
    public $contactsChartData = [];
    public $viewsChartData = [];

    public function mount()
    {
        // Get ALL profile IDs belonging to this user
        $this->userProfileIds = UsersProfile::where('user_id', Auth::id())
            ->pluck('id')
            ->toArray();
        
        if (empty($this->userProfileIds)) {
            return redirect()->route('new.profile');
        }
        
        // Set chart date range (last 5 days)
        $this->chartEndDate = Carbon::now();
        $this->chartStartDate = Carbon::now()->subDays(4);
        
        $this->loadStatistics();
    }
    
    public function loadStatistics()
    {
        $profileIds = $this->userProfileIds;
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Last 30 days stats - Messages (across all user's profiles)
        $this->last30DaysMessages = Message::whereIn('profile_id', $profileIds)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();
            
        // Last 30 days - Phone clicks from phone_clicks table (across all profiles)
        try {
            $this->last30DaysPhoneViews = PhoneClick::whereIn('profile_id', $profileIds)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count();
        } catch (\Exception $e) {
            $this->last30DaysPhoneViews = 0;
        }
        
        // Last 30 days - Views from profile_visits table (across all profiles)
        $this->last30DaysViews = ProfileVisit::whereIn('profile_id', $profileIds)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();
        
        // All time stats (across all profiles)
        $this->allTimeMessages = Message::whereIn('profile_id', $profileIds)->count();
        
        // All time phone clicks - sum from new table + cumulative counters
        try {
            $trackedClicks = PhoneClick::whereIn('profile_id', $profileIds)->count();
            $cumulativeClicks = UsersProfile::where('user_id', Auth::id())->sum('phone_clicks');
            // Use the higher value (table might not have historical data)
            $this->allTimePhoneViews = max($trackedClicks, $cumulativeClicks);
        } catch (\Exception $e) {
            $this->allTimePhoneViews = UsersProfile::where('user_id', Auth::id())->sum('phone_clicks');
        }
        
        // All time views - sum from new table + cumulative counters
        $trackedViews = ProfileVisit::whereIn('profile_id', $profileIds)->count();
        $cumulativeViews = UsersProfile::where('user_id', Auth::id())->sum('profile_views');
        $this->allTimeViews = max($trackedViews, $cumulativeViews);
        
        // Generate chart data
        $this->generateChartData();
    }
    
    public function generateChartData()
    {
        $profileIds = $this->userProfileIds;
        $labels = [];
        $messagesData = [];
        $phoneData = [];
        $viewsData = [];
        
        // Generate data for each day in the range
        for ($i = 4; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M Y');
            
            // Messages for this day (across all profiles)
            $messagesData[] = Message::whereIn('profile_id', $profileIds)
                ->whereDate('created_at', $date->toDateString())
                ->count();
            
            // Phone clicks for this day (across all profiles)
            try {
                $phoneData[] = PhoneClick::whereIn('profile_id', $profileIds)
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
            } catch (\Exception $e) {
                $phoneData[] = 0;
            }
            
            // Views from profile_visits (across all profiles)
            $viewsData[] = ProfileVisit::whereIn('profile_id', $profileIds)
                ->whereDate('created_at', $date->toDateString())
                ->count();
        }
        
        // Contacts chart data (messages and phone requests)
        $this->contactsChartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Received messages',
                        'data' => $messagesData,
                        'borderColor' => '#f4b400',
                        'backgroundColor' => '#f4b400',
                    ],
                    [
                        'label' => 'Phone requests',
                        'data' => $phoneData,
                        'borderColor' => '#4285f4',
                        'backgroundColor' => '#4285f4',
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'x' => [
                        'grid' => [
                            'color' => '#333',
                        ],
                        'ticks' => [
                            'color' => '#ccc',
                        ]
                    ],
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => '#333',
                        ],
                        'ticks' => [
                            'color' => '#ccc',
                            'stepSize' => 1,
                        ]
                    ]
                ],
                'plugins' => [
                    'legend' => [
                        'labels' => [
                            'color' => '#ccc'
                        ]
                    ]
                ]
            ]
        ];
        
        // Views chart data
        $this->viewsChartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Views',
                        'data' => $viewsData,
                        'borderColor' => '#f4b400',
                        'backgroundColor' => '#f4b400',
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'x' => [
                        'grid' => [
                            'color' => '#333',
                        ],
                        'ticks' => [
                            'color' => '#ccc',
                        ]
                    ],
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => '#333',
                        ],
                        'ticks' => [
                            'color' => '#ccc',
                            'stepSize' => 1,
                        ]
                    ]
                ],
                'plugins' => [
                    'legend' => [
                        'labels' => [
                            'color' => '#ccc'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.profile.users.user-statistics');
    }
}
