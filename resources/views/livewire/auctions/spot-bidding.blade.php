@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
        <a class="back-link" href="{{ url('/auctions/'.$gender.'-escorts-in-'.$city) }}">
            <i class="fa fa-angle-left fa-fw"></i>
            <span class="hidden-xs">Back to Auctions</span>
        </a>
        <div class="title">
            <h1>
                <a href="{{ url('/auctions/'.$gender.'-escorts-in-'.$city.'/spot/'.$spotNumber) }}">
                    Bid on Spot #{{ $spotNumber }}
                </a>
            </h1>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .auction-details {
        background-color: #333;
        color: white;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .bid-form {
        background-color: #2a2b2b;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .bid-history {
        margin-top: 30px;
    }
    .bid-history-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .bid-history-item:last-child {
        border-bottom: none;
    }
    .winning-bid {
        background-color: #38b656;
    }
    .timer-icon {
        margin-right: 10px;
    }

    .btn.focus, .btn:focus, .btn:hover {
    color: #fafafa;
}
</style>
@endpush

<div class="container-fluid">
    @if($errorMessage)
    <div class="alert alert-danger">
        {{ $errorMessage }}
    </div>
    @endif
    
    @if($successMessage)
    <div class="alert alert-success">
        {{ $successMessage }}
    </div>
    @endif
    
    @if($auction)
    <div class="row">
        <div class="col-md-8">
            <div class="auction-details">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Spot #{{ $auction->spot_number }} in {{ $auction->city->name }}</h3>
                        <p>Gender: {{ ucfirst($auction->gender) }}</p>
                        <p>Current Price: ${{ number_format($auction->current_price, 2) }}</p>
                        <p>
                            <img src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/timer-5fc5fc1474905d451c5cb2d9ad472d17fea1e9059c0baf436d0aaf6df2b2aeed.svg" 
                                 class="timer-icon" width="20" height="20">
                            Auction ends in: {{ $timeRemaining }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h4>Why bid on this spot?</h4>
                        <ul>
                            <li>Premium visibility at the top of search results</li>
                            <li>More profile views and contacts</li>
                            <li>Stand out from other advertisers</li>
                            <li>Increase your booking rate</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            @if($canBid && count($userProfiles) > 0)
            <div class="bid-form">
                <h3>Place Your Bid</h3>
                <form wire:submit.prevent="placeBid">
                    <div class="form-group">
                        <label for="selectedProfile">Select Profile</label>
                        <select wire:model="selectedProfile" id="selectedProfile" class="form-control @error('selectedProfile') is-invalid @enderror">
                            <option value="">-- Select Profile --</option>
                            @foreach($userProfiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedProfile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="bidAmount">Your Bid ($)</label>
                        <input type="number" wire:model="bidAmount" id="bidAmount" 
                               class="form-control @error('bidAmount') is-invalid @enderror"
                               min="{{ $minBid }}" step="10">
                        <small class="form-text text-muted">Minimum bid: ${{ number_format($minBid, 2) }}</small>
                        @error('bidAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                        <span wire:loading.remove>Place Bid</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </form>
            </div>
            @endif
            
            <div class="bid-history">
                <h3>Bid History</h3>
                @if($auction->bids->count() > 0)
                    @foreach($auction->bids->sortByDesc('created_at') as $bid)
                    <div class="bid-history-item {{ $auction->winner_profile_id == $bid->profile_id ? 'winning-bid' : '' }}">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>{{ $bid->profile->name ?? 'Unknown Profile' }}</strong>
                            </div>
                            <div class="col-md-4">
                                ${{ number_format($bid->amount, 2) }}
                            </div>
                            <div class="col-md-4">
                                {{ $bid->created_at->format('M d, Y H:i') }}
                                @if($auction->winner_profile_id == $bid->profile_id)
                                <span class="badge badge-success">Winning</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <p>No bids yet. Be the first to bid!</p>
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>How Auctions Work</h4>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Place a bid higher than the current price</li>
                        <li>If you're the highest bidder when the auction ends, your profile will be featured in this spot for 7 days</li>
                        <li>You'll only be charged if you win</li>
                        <li>The spot will be automatically renewed at the end of the 7-day period</li>
                    </ol>
                    
                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> You need to have spent at least 300 credits to participate in auctions.
                    </div>
                </div>
            </div>
            
            @if($auction->bids->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h4>Current Highest Bidder</h4>
    </div>
    <div class="card-body">
        <div class="text-center">
            @php
                $highestBid = $auction->bids->sortByDesc('amount')->first();
                $highestBidderProfile = $highestBid ? $highestBid->profile : null;
            @endphp
            
            @if($highestBidderProfile && $highestBidderProfile->singleimg)
            <img src="{{ asset('storage/userimages/'.$highestBidderProfile->user_id.'/'.$highestBidderProfile->id.'/'.$highestBidderProfile->singleimg->image) }}" 
                 class="img-fluid rounded mb-3" style="max-height: 200px;">
            @endif
            <h5>{{ $highestBidderProfile->name ?? 'No bids yet' }}</h5>
            <p>Current bid: ${{ number_format($auction->current_price, 2) }}</p>
            @if($highestBidderProfile)
            <a href="{{ url($gender.'-escorts-in-'.$city.'/'.$highestBidderProfile->id.'/'.$highestBidderProfile->slug) }}" 
               class="btn btn-outline-primary">View Profile</a>
            @endif
        </div>
    </div>
</div>
@endif
            
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Auction Rules</h4>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Minimum bid increment: $10</li>
                        <li>All bids are final and cannot be retracted</li>
                        <li>The highest bidder when the auction ends will be the winner</li>
                        <li>Payment will be processed automatically if you win</li>
                        <li>Auction ends at: {{ $auction->end_date->format('M d, Y H:i') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <h4>Auction Not Found</h4>
        <p>The auction you're looking for doesn't exist or has ended.</p>
        <a href="{{ url('/auctions/'.$gender.'-escorts-in-'.$city) }}" class="btn btn-primary mt-3">
            View All Auctions
        </a>
    </div>
    @endif
</div>