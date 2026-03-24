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
.bid-form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.bid-form-header h4 { color: #fff; font-weight: 600; margin: 0; }
.bid-form-gender { color: #ccc; font-size: 14px; }
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
.bid-form-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-top: 10px;
}
.bid-form-meta {
    text-align: right;
}
.bid-form-meta .current-price {
    display: block;
    color: #C1F11D;
    font-weight: 600;
    font-size: 15px;
}
.bid-form-meta .auction-timer {
    display: block;
    color: #ccc;
    font-size: 13px;
    margin-top: 4px;
}
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

/* Highest Bidder Row Layout */
.highest-bidder-row {
    display: flex;
    align-items: center;
    gap: 15px;
}
.highest-bidder-img {
    width: 70px;
    height: 70px;
    border-radius: 8px;
    object-fit: cover;
}
.highest-bidder-info h5 { margin: 0 0 4px 0; }
.highest-bidder-info p { margin: 0; color: #ccc; }

/* Bottom Info Sections */
.bottom-info-sections {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #2a2a2a;
}
.bottom-info-sections h4 {
    color: #fff;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 18px;
}
.bottom-info-sections ul,
.bottom-info-sections ol {
    padding-left: 20px;
}
.bottom-info-sections ul li,
.bottom-info-sections ol li {
    color: #ccc;
    margin-bottom: 8px;
}

/* Custom Select2 - Dark Theme */
.custom-select2 {
    position: relative;
    width: 100%;
    display: inline-block;
}
.custom-select2-selection {
    background: #111 !important;
    border: 1px solid #333 !important;
    color: #fff !important;
    border-radius: 6px;
    padding: 8px 12px;
    cursor: pointer;
    position: relative;
    min-height: 38px;
    display: flex;
    align-items: center;
}
.custom-select2-selection::after {
    content: '\25BC';
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #888;
    transition: transform 0.2s;
}
.custom-select2-selection.open::after {
    transform: translateY(-50%) rotate(180deg);
}
.custom-select2-selection:hover {
    border-color: #555 !important;
}
.custom-select2-selection:focus,
.custom-select2-selection.open {
    border-color: #C1F11D !important;
    box-shadow: 0 0 0 2px rgba(193, 241, 29, 0.15) !important;
}
.custom-select2-placeholder {
    color: #666 !important;
}
.custom-select2-dropdown {
    display: none;
    position: absolute;
    width: 100%;
    background: #1a1a1a !important;
    border: 1px solid #333 !important;
    border-radius: 6px;
    margin-top: 4px;
    z-index: 1050;
    box-shadow: 0 6px 20px rgba(0,0,0,0.5) !important;
    max-height: 300px;
    overflow: hidden;
}
.custom-select2-dropdown.open {
    display: block;
}
.custom-select2-search {
    padding: 8px;
    border-bottom: 1px solid #333 !important;
}
.custom-select2-search input {
    width: 100%;
    background: #111 !important;
    border: 1px solid #333 !important;
    color: #fff !important;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 14px;
    outline: none;
}
.custom-select2-search input:focus {
    border-color: #C1F11D !important;
    box-shadow: 0 0 0 2px rgba(193, 241, 29, 0.15) !important;
}
.custom-select2-results {
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 240px;
    overflow-y: auto;
}
.custom-select2-results::-webkit-scrollbar { width: 6px; }
.custom-select2-results::-webkit-scrollbar-track { background: #111; }
.custom-select2-results::-webkit-scrollbar-thumb { background: #444; border-radius: 3px; }
.custom-select2-option {
    padding: 8px 12px;
    cursor: pointer;
    color: #ccc;
    border-bottom: 1px solid #222;
    transition: background 0.15s;
}
.custom-select2-option:hover {
    background: #2e3033 !important;
    color: #fff !important;
}
.custom-select2-option.selected {
    background: #C1F11D !important;
    color: #000 !important;
}
.custom-select2-option.selected:hover {
    background: #b5e600 !important;
}
.custom-select2-option.hidden {
    display: none;
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
        {{-- LEFT COLUMN --}}
        <div class="col-md-8 mt-4">
            {{-- Bid Form --}}
            @if($canBid && count($userProfiles) > 0)
            <div class="bid-form">
                <div class="bid-form-header">
                    <h4>Place Your Bid Spot #{{ $auction->spot_number }}</h4>
                    <span class="bid-form-gender">Gender: {{ ucfirst($auction->gender) }}</span>
                </div>

                <form wire:submit.prevent="placeBid">
                    <div class="form-group">
                        <label for="selectedProfile">Select Profile</label>
                        <select wire:model="selectedProfile" id="selectedProfile" class="form-control apply-custom-select2 @error('selectedProfile') is-invalid @enderror">
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

                    <div class="bid-form-footer">
                        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove>Place Bid</span>
                            <span wire:loading>Processing...</span>
                        </button>
                        <div class="bid-form-meta">
                            <span class="current-price">Current Price: ${{ number_format($auction->current_price, 2) }}</span>
                            <span class="auction-timer">
                                <img src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/timer-5fc5fc1474905d451c5cb2d9ad472d17fea1e9059c0baf436d0aaf6df2b2aeed.svg"
                                     class="timer-icon" width="16" height="16">
                                Auction ends in: {{ $timeRemaining }}
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            {{-- Bid History --}}
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

            {{-- Bottom info sections --}}
            <div class="row bottom-info-sections">
                <div class="col-md-6">
                    <h4>Why bid on this spot?</h4>
                    <ul>
                        <li>Premium visibility at the top of search results</li>
                        <li>More profile views and contacts</li>
                        <li>Stand out from other advertisers</li>
                        <li>Increase your booking rate</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4>How Auctions Work</h4>
                    <ol>
                        <li>Place a bid higher than the current price</li>
                        <li>If you're the highest bidder when the auction ends, your profile will be featured in this spot for 7 days</li>
                        <li>You'll only be charged if you win</li>
                        <li>The spot will be automatically renewed at the end of the 7-day period</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-md-4" style="margin-top: 2rem;">
            {{-- Note Alert --}}
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <strong>Note:</strong> You need to have spent at least 300 credit to participate in auctions.
                    </div>
                </div>
            </div>

            {{-- Current Highest Bidder --}}
            @if($auction->bids->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Current Highest Bidder</h4>
                </div>
                <div class="card-body">
                    <div class="highest-bidder-row">
                        @php
                            $highestBid = $auction->bids->sortByDesc('amount')->first();
                            $highestBidderProfile = $highestBid ? $highestBid->profile : null;
                        @endphp

                        @if($highestBidderProfile && $highestBidderProfile->singleimg)
                        <img src="{{ asset('storage/userimages/'.$highestBidderProfile->user_id.'/'.$highestBidderProfile->id.'/'.$highestBidderProfile->singleimg->image) }}"
                             class="highest-bidder-img">
                        @endif
                        <div class="highest-bidder-info">
                            <h5>{{ $highestBidderProfile->name ?? 'No bids yet' }}</h5>
                            <p>Current bid: ${{ number_format($auction->current_price, 2) }}</p>
                        </div>
                    </div>
                    @if($highestBidderProfile)
                    <div class="text-center mt-3">
                        <a href="{{ url($gender.'-escorts-in-'.$city.'/'.$highestBidderProfile->id.'/'.$highestBidderProfile->slug) }}"
                           class="btn btn-outline-primary btn-block">View Profile</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Auction Rules --}}
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

<script>
// Custom Select2 Implementation
class CustomSelect2 {
    constructor(selectElement, options = {}) {
        this.select = selectElement;
        this.options = {
            placeholder: options.placeholder || 'Select an option',
            searchable: options.searchable !== false,
            onChange: options.onChange || null
        };
        this.isOpen = false;
        this.selectedValue = this.select.value;
        this.init();
    }

    init() {
        this.select.style.display = 'none';
        this.createCustomSelect();
        this.attachEventListeners();
        if (this.selectedValue) {
            this.selectOption(this.selectedValue);
        }
    }

    createCustomSelect() {
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'custom-select2';
        this.wrapper.style.width = '100%';
        this.wrapper.style.position = 'relative';

        this.selectionBox = document.createElement('div');
        this.selectionBox.className = 'custom-select2-selection custom-select2-placeholder';
        this.selectionBox.textContent = this.options.placeholder;

        this.dropdown = document.createElement('div');
        this.dropdown.className = 'custom-select2-dropdown';

        if (this.options.searchable) {
            const searchContainer = document.createElement('div');
            searchContainer.className = 'custom-select2-search';
            this.searchInput = document.createElement('input');
            this.searchInput.type = 'text';
            this.searchInput.placeholder = 'Search...';
            searchContainer.appendChild(this.searchInput);
            this.dropdown.appendChild(searchContainer);
        }

        this.resultsList = document.createElement('ul');
        this.resultsList.className = 'custom-select2-results';

        Array.from(this.select.options).forEach(option => {
            if (option.value === '') return;
            const li = document.createElement('li');
            li.className = 'custom-select2-option';
            li.textContent = option.textContent;
            li.dataset.value = option.value;
            if (option.value === this.selectedValue) li.classList.add('selected');
            this.resultsList.appendChild(li);
        });

        this.dropdown.appendChild(this.resultsList);
        this.wrapper.appendChild(this.selectionBox);
        this.wrapper.appendChild(this.dropdown);
        this.select.parentNode.insertBefore(this.wrapper, this.select.nextSibling);
    }

    attachEventListeners() {
        this.selectionBox.addEventListener('click', (e) => { e.stopPropagation(); this.toggleDropdown(); });

        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => { e.stopPropagation(); this.filterOptions(e.target.value); });
            this.searchInput.addEventListener('click', (e) => { e.stopPropagation(); });
        }

        const handleOptionSelect = (e) => {
            e.preventDefault();
            e.stopPropagation();
            let target = e.target;
            while (target && !target.classList.contains('custom-select2-option')) {
                target = target.parentElement;
                if (target === this.resultsList) { target = null; break; }
            }
            if (target && target.classList.contains('custom-select2-option')) {
                this.selectOption(target.dataset.value);
                this.closeDropdown();
            }
        };

        this.resultsList.addEventListener('click', handleOptionSelect);
        this.resultsList.addEventListener('touchend', handleOptionSelect);
        document.addEventListener('click', (e) => { if (!this.wrapper.contains(e.target)) this.closeDropdown(); });
        this.dropdown.addEventListener('click', (e) => { e.stopPropagation(); });
    }

    toggleDropdown() { this.isOpen ? this.closeDropdown() : this.openDropdown(); }

    openDropdown() {
        this.dropdown.classList.add('open');
        this.selectionBox.classList.add('open');
        this.isOpen = true;
        if (this.searchInput) setTimeout(() => this.searchInput.focus(), 100);
    }

    closeDropdown() {
        this.dropdown.classList.remove('open');
        this.selectionBox.classList.remove('open');
        this.isOpen = false;
        if (this.searchInput) { this.searchInput.value = ''; this.filterOptions(''); }
    }

    filterOptions(searchTerm) {
        const term = searchTerm.toLowerCase();
        this.resultsList.querySelectorAll('.custom-select2-option').forEach(option => {
            option.classList.toggle('hidden', !option.textContent.toLowerCase().includes(term));
        });
    }

    selectOption(value) {
        this.select.value = value;
        this.selectedValue = value;
        const event = new Event('change', { bubbles: true });
        this.select.dispatchEvent(event);

        const selectedOption = Array.from(this.select.options).find(opt => opt.value === value);
        if (selectedOption) {
            this.selectionBox.textContent = selectedOption.textContent;
            this.selectionBox.classList.remove('custom-select2-placeholder');
        }

        this.resultsList.querySelectorAll('.custom-select2-option').forEach(li => {
            li.classList.toggle('selected', li.dataset.value === value);
        });

        if (this.options.onChange) this.options.onChange(value);
    }

    destroy() {
        if (this.wrapper && this.wrapper.parentNode) this.wrapper.parentNode.removeChild(this.wrapper);
        this.select.style.display = '';
        this.wrapper = null;
    }
}

function initializeSpotBiddingSelect2() {
    const selects = document.querySelectorAll('select.apply-custom-select2');
    selects.forEach(select => {
        if (select.nextElementSibling && select.nextElementSibling.classList.contains('custom-select2')) return;
        if (select.customSelect2Instance) { select.customSelect2Instance.destroy(); select.customSelect2Instance = null; }

        const placeholder = select.options[0]?.textContent || 'Select an option';
        const wireModel = select.getAttribute('wire:model');

        select.customSelect2Instance = new CustomSelect2(select, {
            placeholder: placeholder,
            searchable: true,
            onChange: (value) => {
                select.value = value;
                select.dispatchEvent(new Event('input', { bubbles: true }));
                select.dispatchEvent(new Event('change', { bubbles: true }));
                if (wireModel && typeof Livewire !== 'undefined') {
                    const componentId = select.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId && window.Livewire) {
                        const component = window.Livewire.find(componentId);
                        if (component && component.set) component.set(wireModel, value);
                    }
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeSpotBiddingSelect2, 100);
});

if (typeof Livewire !== 'undefined') {
    Livewire.hook('message.processed', () => { setTimeout(initializeSpotBiddingSelect2, 100); });
}
document.addEventListener('livewire:navigated', () => { setTimeout(initializeSpotBiddingSelect2, 200); });
document.addEventListener('livewire:load', () => { setTimeout(initializeSpotBiddingSelect2, 200); });
</script>