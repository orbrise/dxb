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
    public $cityName;
    public $gender;
    // Auction::categories() key, and its URL segment before "-in-{city}".
    public $category;
    public $prefix;
    public $errorMessage;
    public $successMessage;
    public $timeRemaining;
    public $canBid = false;
    
    protected $rules = [
        'bidAmount' => 'required|numeric|min:0',
        'selectedProfile' => 'required|integer'
    ];
    
    public function mount($spot, $cityname, $gender = null, $category = null)
    {
        $this->spotNumber = $spot;
        $this->city = $cityname; 

        $this->category = Auction::resolveCategory($gender, $category);
        if (!$this->category) abort(404);
        $categoryInfo = Auction::categories()[$this->category];
        $this->prefix = $categoryInfo['prefix'];
        // Profile links use the escort URL family; category profiles too.
        $this->gender = $categoryInfo['gender'] ?? 'female';

        $genderObj = Gender::where('name', $this->gender)->first();
        // Exact slug / name first: a bare LIKE could pick another city that
        // merely contains the name.
        $cityObj = City::where('slug', $cityname)->first()
            ?? City::where('name', str_replace('-', ' ', $cityname))->first()
            ?? City::where('name', 'like', '%' . $cityname . '%')->first();

        if (!$cityObj) {
            $this->errorMessage = 'City not found';
            return;
        }
        $this->cityName = $cityObj->name;
        
     
        $this->auction = Auction::where('spot_number', $this->spotNumber)
            ->where('city_id', $cityObj->id) 
            ->inCategory($this->category)
            ->where('status', 'active')
            ->first();
                
        if (!$this->auction) {
            $this->errorMessage = 'Auction not found or not active';
            return;
        }
        
        $this->auctionId = $this->auction->id;
        $this->refreshRound();
        $this->bidAmount = $this->minBid;

        if (!auth()->check()) {
            $this->errorMessage = 'Please sign in to place a bid.';
            return;
        }
        if (!$this->auction->is_open) {
            // auctions:end picks the winner within the hour.
            $this->errorMessage = 'Bidding on this spot has closed. The winner is being confirmed.';
            return;
        }
        if (!Auction::userQualifies(auth()->user())) {
            $this->errorMessage = 'You need to have spent at least ' . Auction::MIN_SPENT_CREDITS . ' credits to bid in auctions.';
            return;
        }

        // The user's profiles that fit the spot: that category's listings,
        // or that gender's for an escort spot.
        $this->userProfiles = UsersProfile::where('user_id', auth()->id())
            ->when($categoryInfo['listing_id'],
                fn ($q) => $q->where('listing', $categoryInfo['listing_id']),
                fn ($q) => $q->where('gender', $genderObj?->id))
            ->get();

        if ($this->userProfiles->isEmpty()) {
            $this->errorMessage = 'You need a profile in ' . $categoryInfo['label'] . ' before bidding.';
            return;
        }

        $this->selectedProfile = $this->userProfiles->first()->id;
        $this->canBid = true;
    }

    /**
     * Minimum bid and time left for the current round. A round with no bids
     * yet opens at the current price; after that each bid must beat the
     * highest by at least $10.
     */
    protected function refreshRound(): void
    {
        $hasBids = $this->auction->bids()->where('status', 'active')->exists();
        $this->minBid = (float) $this->auction->current_price + ($hasBids ? 10 : 0);
        // "3 days 4 hours"; null once the end date has passed.
        $this->timeRemaining = $this->auction->end_date->isFuture()
            ? $this->auction->end_date->diffForHumans(['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE])
            : null;
    }

    public function placeBid()
    {
        if (!auth()->check()) {
            return redirect()->route('sign-in');
        }
        if (!$this->canBid) return;

        $this->validate();

        $auction = Auction::find($this->auctionId);
        if (!$auction || !$auction->is_open) {
            $this->canBid = false;
            $this->errorMessage = 'Bidding on this spot has closed. The winner is being confirmed.';
            return;
        }
        $this->auction = $auction;
        $this->refreshRound();

        if (!collect($this->userProfiles)->pluck('id')->contains((int) $this->selectedProfile)) {
            $this->addError('selectedProfile', 'Choose one of your profiles.');
            return;
        }
        if ((float) $this->bidAmount < $this->minBid) {
            $this->addError('bidAmount', 'Your bid must be at least $' . number_format($this->minBid, 2) . '.');
            return;
        }
        $balance = (float) (auth()->user()->wallet->balance ?? 0);
        if ((float) $this->bidAmount > $balance) {
            $this->addError('bidAmount', 'You have $' . number_format($balance, 2) . ' in your wallet. Add credits to bid this much.');
            return;
        }

        $bid = AuctionBid::create([
            'auction_id' => $this->auctionId,
            'user_id' => auth()->id(),
            'profile_id' => $this->selectedProfile,
            'amount' => $this->bidAmount,
            'status' => 'active'
        ]);

        // The price only; the winner is picked when the round ends.
        $this->auction->update(['current_price' => $this->bidAmount]);

        AuctionTransaction::create([
            'user_id' => auth()->id(),
            'auction_id' => $this->auctionId,
            'bid_id' => $bid->id,
            'amount' => $this->bidAmount,
            'status' => 'pending'
        ]);

        $this->successMessage = 'Your bid has been placed successfully!';
        $this->refreshRound();
        $this->bidAmount = $this->minBid;
    }

    public function render()
    {
        if ($this->auction) {
            $this->auction = Auction::with(['bids.profile.singleimg', 'bids.profile.coverimg', 'city'])
                ->find($this->auction->id);
        }

        // A spot runs in rounds on the same row: when one ends its bids are
        // marked won / lost, so only 'active' bids belong to this round.
        $bids = $this->auction ? $this->auction->bids : collect();
        $roundBids = $bids->where('status', 'active')->sortByDesc('amount')->values();

        return view('livewire.auctions.spot-bidding', [
            'auction' => $this->auction,
            'userProfiles' => $this->userProfiles,
            'roundBids' => $roundBids,
            'highestBid' => $roundBids->first(),
            'roundBidCount' => $roundBids->count(),
            'myEarlierBids' => auth()->check()
                ? $bids->where('user_id', auth()->id())->whereIn('status', ['won', 'lost'])->sortByDesc('created_at')->values()
                : collect(),
        ]);
    }
}
