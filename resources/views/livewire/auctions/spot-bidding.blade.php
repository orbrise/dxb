@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid" style="background:transparent !important;">
        <a class="back-link" href="{{ url('/auctions/'.$gender.'-escorts-in-'.$city) }}">
            <i class="fa fa-angle-left fa-fw"></i>
            <span class="hidden-xs" style="color: #C1F11D !important;">Back to Auctions</span>
        </a>
        <div class="title">
            <h1>Bid on Spot #{{ $spotNumber }}</h1>
        </div>
    </div>
</div>
@endsection

<style>
/* === Evoory Dark Theme === */

/* Page background */
body { background: #000 !important; }
#header .nav-bar { background: #131616 !important; }
#header { margin-bottom: 0px !important; }
#header .nav-bar .back-link { color: #C1F11D !important; text-decoration: none; }
#header .nav-bar .title h1 { color: #fff !important; }
#footer { background: #0D1011 !important; border-top: 0px !important; }
#footer .list-inline li { margin-bottom: 0px !important; }

/* Header - Evoory style buttons */
.navbar.navbar-inverse { background: #0D1011 !important; border: none !important; }
.logo.navbar-brand, .logo2.navbar-brand { display: none !important; }
.navbar-header::before {
    content: "evoory";
    font-size: 24px;
    font-weight: 700;
    color: #C1F11D;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    display: flex;
    align-items: center;
    margin-right: auto;
    padding: 10px 0;
}
.auth-button-group { gap: 10px !important; }
.auth-button-group .btn-navbar-header,
.auth-button-group .button_to .btn-navbar-header {
    border-radius: 8px !important;
    border: 1px solid #2a2a2a !important;
    border-right: 1px solid #2a2a2a !important;
    background: transparent !important;
    color: #ccc !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    padding: 10px 20px !important;
    transition: all 0.2s ease;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}
.auth-button-group .btn-navbar-header:hover,
.auth-button-group .button_to .btn-navbar-header:hover {
    color: #fff !important;
    background: #1a1a1a !important;
    border-color: #ccc !important;
}
.auth-button-group .btn-navbar-header:first-child {
    border-radius: 8px !important;
}
#main-nav { display: none !important; }

/* Auction Details Section */
.auction-details {
    color: #fff;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.auction-details h3 { color: #fff; font-weight: 600; }
.auction-details h4 { color: #C1F11D; font-weight: 600; margin-bottom: 15px; }
.auction-details p { color: #ccc; }
.auction-details ul { padding-left: 20px; }
.auction-details ul li { color: #ccc; margin-bottom: 8px; }

/* Bid Form */
.bid-form {
    background: #1a1a1a;
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.bid-form h3 { color: #fff; font-weight: 600; margin-bottom: 20px; }
.bid-form label { color: #ccc; }
.bid-form .form-control {
    background: #111 !important;
    border: 1px solid #333 !important;
    color: #fff !important;
    border-radius: 6px;
}
.bid-form .form-control:focus {
    border-color: #C1F11D !important;
    box-shadow: 0 0 0 2px rgba(193, 241, 29, 0.15) !important;
}
.bid-form .form-text { color: #888 !important; }
.bid-form .btn-primary {
    background: #C1F11D !important;
    color: #000 !important;
    border: none !important;
    border-radius: 22px !important;
    font-weight: 600 !important;
    padding: 12px 35px !important;
    font-size: 16px !important;
}
.bid-form .btn-primary:hover {
    background: #d4f84d !important;
    color: #000 !important;
}

/* Bid History */
.bid-history { margin-top: 30px; }
.bid-history h3 { color: #fff; font-weight: 600; margin-bottom: 15px; }
.bid-history-item {
    padding: 15px;
    border-bottom: 1px solid #2a2a2a;
    color: #ccc;
}
.bid-history-item:last-child { border-bottom: none; }
.bid-history-item strong { color: #fff; }
.winning-bid {
    background: rgba(56, 182, 86, 0.15) !important;
    border-left: 3px solid #38b656;
}

/* Bid Status Badges */
.bid-status-badge {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.bid-status-review {
    background: transparent;
    border: 1px solid #C1F11D;
    color: #C1F11D;
}
.bid-status-approved {
    background: transparent;
    border: 1px solid #38b656;
    color: #38b656;
}
.bid-status-rejected {
    background: transparent;
    border: 1px solid #dc3545;
    color: #dc3545;
}

/* Cards (How Auctions Work, Highest Bidder, Rules) */
.card {
    border-radius: 5px !important;
}
.card-header {
margin-bottom: 10px !important;

}
.card-header h4 { color: #fff; margin: 0; font-weight: 600; }
.card-body { color: #ccc; }
.card-body ol, .card-body ul { padding-left: 18px; }
.card-body ol li, .card-body ul li { margin-bottom: 8px; }

/* Alert info in card */
.card-body .alert-info {
    background: #d1ecf1 !important;
    border: 1px solid #bee5eb !important;
    color: #0c5460 !important;
    border-radius: 6px;
}
.card-body .alert-info strong { color: #0c5460; }

/* Highest Bidder card */
.card-body h5 { color: #fff; }
.card-body .btn-outline-primary {
    border-color: #C1F11D !important;
    color: #C1F11D !important;
    border-radius: 22px !important;
    padding: 8px 25px !important;
    background: transparent !important;
}
.card-body .btn-outline-primary:hover {
    background: #C1F11D !important;
    color: #000 !important;
}

/* Badge */
.badge-success {
    background: #C1F11D !important;
    color: #000 !important;
    border-radius: 12px;
    padding: 4px 10px;
    font-weight: 600;
}

/* Alert warning (auction not found) */
.alert-warning {
    background: #1a1a1a !important;
    border: 1px solid #2a2a2a !important;
    color: #ccc !important;
    border-radius: 8px;
}
.alert-warning h4 { color: #fff; }
.alert-warning .btn-primary {
    background: #C1F11D !important;
    color: #000 !important;
    border: none !important;
    border-radius: 22px !important;
    font-weight: 600 !important;
}

/* Alert danger/success */
.alert-danger {
    background: rgba(220, 53, 69, 0.15) !important;
    border: 1px solid rgba(220, 53, 69, 0.3) !important;
    color: #ff6b6b !important;
    border-radius: 6px;
}
.alert-success {
    background: rgba(56, 182, 86, 0.15) !important;
    border: 1px solid rgba(56, 182, 86, 0.3) !important;
    color: #5cb85c !important;
    border-radius: 6px;
}

/* Timer icon */
.timer-icon { margin-right: 10px; filter: invert(1); }

/* Btn hover */
.btn.focus, .btn:focus, .btn:hover { color: #fafafa; }

ul.list-inline > li > a {
    color: #C1F11D !important;
}
</style>

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
                            auction ends in: {{ $timeRemaining }}
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
                <h4 class="mb-4">Place Your Bid</h4>
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
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <strong>{{ $bid->profile->name ?? 'Unknown Profile' }}</strong>
                            </div>
                            <div class="col-md-3">
                                ${{ number_format($bid->amount, 2) }}
                            </div>
                            <div class="col-md-3">
                                {{ $bid->created_at->format('M d,Y H:i') }}
                            </div>
                            <div class="col-md-3 text-right">
                                @if($bid->status === 'won')
                                <span class="bid-status-badge bid-status-approved">Approved</span>
                                @elseif($bid->status === 'lost')
                                <span class="bid-status-badge bid-status-rejected">Rejected</span>
                                @else
                                <span class="bid-status-badge bid-status-review">In Review</span>
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

        <div class="col-md-4" style="margin-top: 2rem;">
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
                    <h4>Auctions Rules</h4>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Minimum bid increment: $10</li>
                        <li>All bids are final and cannot be retracted</li>
                        <li>Type highest bidder when the auction ends</li>
                        <li>will be the winner</li>
                        <li>Payment will be processed automatically if you win</li>
                        <li>Auction end at: {{ $auction->end_date->format('M d, Y H:i') }}</li>
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