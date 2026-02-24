<?php

namespace App\Livewire\Auctions;

use Livewire\Component;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\AuctionTransaction;
use App\Models\UsersProfile;
use App\Models\City;
use App\Models\Gender;
use Carbon\Carbon;

class SpotBidding extends Component
{
    public $auction;
    public $auctionId;
    public $spotNumber;
    public $bidAmount;
    public $minBid;
    public $selectedProfile;
    public $userProfiles = [];
    public $city;
    public $gender;
    public $errorMessage;
    public $successMessage;
    public $timeRemaining;
    public $canBid = false;
    
    protected $rules = [
        'bidAmount' => 'required|numeric|min:0',
        'selectedProfile' => 'required|exists:users_profiles,id'
    ];
    
    public function mount($spot, $cityname, $gender = 'female')
    {
        $this->spotNumber = $spot;
        $this->city = $cityname; 
        $this->gender = $gender; 
        
        $genderObj = Gender::where('name', $gender)->first();
        $cityObj = City::where('name', 'like', '%' . $cityname . '%')->first();
        
        if (!$cityObj) {
            $this->errorMessage = 'City not found';
            return;
        }
        
     
        $this->auction = Auction::where('spot_number', $this->spotNumber)
            ->where('city_id', $cityObj->id) 
            ->where('gender', $gender) 
            ->where('status', 'active')
            ->first();
                
        if (!$this->auction) {
            $this->errorMessage = 'Auction not found or not active';
            return;
        }
        
        $this->auctionId = $this->auction->id;
        $this->minBid = $this->auction->current_price + 10; 
        $this->bidAmount = $this->minBid;
        $this->timeRemaining = Carbon::now()->diffForHumans($this->auction->end_date, ['parts' => 2]);
        
        // Check if user can bid
        if (auth()->check()) {
            $user = auth()->user();
            $wallet = $user->wallet;
            
            if ($wallet) {
                // Calculate total spent credits from wallet transactions
                $spentCredits = $wallet->transactions()
                    ->where('status', 'completed')
                    ->sum('amount');
                
                // Check if user has spent at least 300 credits
                $hasSpentEnough = $spentCredits >= 300;
                
                // Check if user has enough balance for the minimum bid
                $hasEnoughBalance = $wallet->balance >= $this->minBid;
                
                if (!$hasSpentEnough) {
                    $this->errorMessage = 'You need to spend at least 300 credits to participate in auctions';
                    $this->canBid = false;
                } elseif (!$hasEnoughBalance) {
                    $this->errorMessage = 'You don\'t have enough credits in your wallet to place this bid. Please add more credits.';
                    $this->canBid = false;
                } else {
                    $this->canBid = true;
                    
                    // Get user's profiles for the specific gender
                    $this->userProfiles = UsersProfile::where('user_id', auth()->id())
                        ->where('gender', $genderObj->id) // Using gender directly
                        ->get();
                        
                    if ($this->userProfiles->count() > 0) {
                        $this->selectedProfile = $this->userProfiles->first()->id;
                    } else {
                        $this->errorMessage = 'You need to create a profile before bidding';
                        $this->canBid = false;
                    }
                }
            } else {
                $this->errorMessage = 'You don\'t have a wallet. Please contact support.';
                $this->canBid = false;
            }
        } else {
            $this->canBid = false;
            $this->errorMessage = 'Please login to place a bid';
        }
    }
    
    public function placeBid()
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    if (!$this->canBid) {
        $this->errorMessage = 'You need to spend at least 300 credits to participate in auctions';
        return;
    }
    
    $this->validate();
    
    // Check if bid amount is higher than current price
    if ($this->bidAmount <= $this->auction->current_price) {
        $this->addError('bidAmount', 'Bid amount must be higher than the current price');
        return;
    }
    
    // Create the bid
    $bid = AuctionBid::create([
        'auction_id' => $this->auctionId,
        'user_id' => auth()->id(),
        'profile_id' => $this->selectedProfile,
        'amount' => $this->bidAmount,
        'status' => 'active'
    ]);
    
    // Update auction current price ONLY, not the winner
    $this->auction->update([
        'current_price' => $this->bidAmount
        // Removed winner_profile_id assignment
    ]);
    
    // Create transaction record
    AuctionTransaction::create([
        'user_id' => auth()->id(),
        'auction_id' => $this->auctionId,
        'bid_id' => $bid->id,
        'amount' => $this->bidAmount,
        'status' => 'pending'
    ]);
    
    $this->successMessage = 'Your bid has been placed successfully!';
    $this->minBid = $this->bidAmount + 10;
    $this->bidAmount = $this->minBid;
}
    
    public function render()
    {
        // Refresh auction data
        if ($this->auction) {
            $this->auction = Auction::with(['bids.profile', 'winnerProfile', 'city'])
                ->find($this->auction->id);
        }
        
        return view('livewire.auctions.spot-bidding', [
            'auction' => $this->auction,
            'userProfiles' => $this->userProfiles
        ]);
    }
}
