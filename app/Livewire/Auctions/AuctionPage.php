<?php

namespace App\Livewire\Auctions;

use Livewire\Component;
use App\Models\Auction;
use App\Models\City;
use App\Models\UsersProfile;
use Carbon\Carbon;

class AuctionPage extends Component
{
    public $city;
    public $gender = 'female';
    public $auctions = [];
    public $cityName;
    public $cityId;
    public $canBid = false;
    
    public function mount($city = 'dubai', $gender = 'female')
    {
        $this->city = $city;
        $this->gender = $gender;
        
        // Get city ID from name
        $cityObj = City::where('name', 'like', '%' . $city . '%')->first();
        if ($cityObj) {
            $this->cityId = $cityObj->id;
            $this->cityName = $cityObj->name;
        } else {
            // Default to Dubai if city not found
            $defaultCity = City::where('name', 'like', '%dubai%')->first();
            $this->cityId = $defaultCity->id;
            $this->cityName = $defaultCity->name;
        }
        
        // Check if user can bid (has spent at least 300 credits)
        if (auth()->check()) {
            $this->canBid = auth()->user()->spent_credits >= 300;
        }
    }
    
    public function render()
    {
    $this->auctions = Auction::where('city_id', $this->cityId)
        ->where('gender', $this->gender)
        ->where('status', 'active')
        ->orderBy('spot_number')
        ->with(['bids.profile', 'winnerProfile', 'city'])
        ->get();
        
    foreach ($this->auctions as $auction) {
        $auction->timeLeft = Carbon::now()->diffForHumans($auction->end_date, ['parts' => 1, 'short' => true]);
        $auction->daysLeft = Carbon::now()->diffInDays($auction->end_date);
        
        // Get the highest bidder's profile for display
        $highestBid = $auction->bids->sortByDesc('amount')->first();
        $auction->highestBidderProfile = $highestBid ? $highestBid->profile : null;
        
        // If no highest bidder, get a featured profile for display
        if (!$auction->highestBidderProfile && $auction->status == 'active') {
            $auction->featuredProfile = UsersProfile::where('gender', $this->gender)
                ->where('city', $this->cityId)
                ->where('package_id', 21) // Premium package
                ->with(['singleimg', 'multipleimgs'])
                ->inRandomOrder()
                ->first();
        }
    }
    
    return view('livewire.auctions.auction-page', [
        'auctions' => $this->auctions,
        'canBid' => $this->canBid
    ]);
 }
}
