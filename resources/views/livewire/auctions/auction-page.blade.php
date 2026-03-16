@push('css')
<style>
/* Sub-header bar */
.auction-subheader {
    background: #131616;
    padding: 12px 0;
}
.auction-subheader .ev-container {
    display: flex;
    align-items: center;
    position: relative;
}
.auction-subheader .back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.auction-subheader .back-link:hover {
    color: #d4f84d;
}
.auction-subheader .page-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    white-space: nowrap;
}

/* Auction page */
.auction-page {
    background: #0a0a0a;
    min-height: 100vh;
    padding: 30px 0 60px;
}
.auction-page .section-title {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 6px 0;
}
.auction-page .section-subtitle {
    color: #999;
    font-size: 13px;
    font-weight: 400;
}

/* Warning banner */
.auction-warning {
    background: rgba(193, 241, 29, 0.1);
    border: 1px solid rgba(193, 241, 29, 0.3);
    border-radius: 8px;
    padding: 12px 16px;
    color: #C1F11D;
    font-size: 14px;
    margin-bottom: 24px;
}

/* Auction grid */
.auction-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
}
@media (max-width: 1024px) {
    .auction-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .auction-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 480px) {
    .auction-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
}

/* Auction card */
.auction-card {
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
}
.auction-card:hover {
    border-color: rgba(193, 241, 29, 0.3);
    transform: translateY(-2px);
}

/* Card image wrapper */
.auction-card-img {
    position: relative;
    width: 100%;
    height: 280px;
    border-radius: 12px;
    overflow: hidden;
}
@media (max-width: 480px) {
    .auction-card-img {
        height: 220px;
    }
}
.auction-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.auction-card-img .placeholder-img {
    width: 100%;
    height: 100%;
    background: #222;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
}

/* Overlay */
.auction-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.65);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 16px;
    z-index: 2;
}
.auction-overlay .price {
    color: #C1F11D;
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}
@media (max-width: 480px) {
    .auction-overlay .price {
        font-size: 22px;
    }
}
.auction-overlay .duration {
    color: #ccc;
    font-size: 13px;
    margin: 2px 0 14px;
}

/* Make Offer button */
.auction-overlay .btn-offer {
    background: #C1F11D;
    color: #000;
    border: none;
    padding: 8px 24px;
    border-radius: 22px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}
.auction-overlay .btn-offer:hover {
    background: #d4f84d;
    color: #000;
    text-decoration: none;
}

/* Timer */
.auction-overlay .timer {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ccc;
    font-size: 13px;
    margin-top: 14px;
}
.auction-overlay .timer-icon {
    width: 18px;
    height: 18px;
    opacity: 0.8;
}
.auction-overlay .timer-text {
    text-align: center;
}
.auction-overlay .timer-text span {
    display: block;
    font-size: 12px;
}
.auction-overlay .timer-text .countdown {
    font-weight: 600;
    color: #fff;
    font-size: 14px;
}

/* Spot label */
.auction-spot-label {
    text-align: center;
    padding: 10px 0;
    color: #ffffff;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}
</style>
@endpush

<div>
{{-- Sub-header --}}
<div class="auction-subheader">
    <div class="ev-container">
        <a class="back-link" href="/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Back
        </a>
        <h1 class="page-title">Auctions for Top Spots</h1>
    </div>
</div>

{{-- Main content --}}
<div class="auction-page">
    <div class="ev-container">
        @if(!$canBid)
        <div class="auction-warning">
            To access auctions, you must purchase or spend at least 300 credits that you purchased.
        </div>
        @endif

        <div style="margin-bottom: 24px;">
            <h2 class="section-title">This week's deluxe profiles</h2>
            <span class="section-subtitle">(view for logged-in advertisers)</span>
        </div>

        <div class="auction-grid">
            @forelse($auctions as $auction)
            <div class="auction-card">
                <div class="auction-card-img">
                    @if($auction->highestBidderProfile && !empty($auction->highestBidderProfile->singleimg))
                        <img loading="lazy" src="{{ asset('storage/userimages/'.$auction->highestBidderProfile->user_id.'/'.$auction->highestBidderProfile->id.'/'.$auction->highestBidderProfile->singleimg->image) }}" alt="" />
                    @elseif($auction->featuredProfile && !empty($auction->featuredProfile->singleimg))
                        <img loading="lazy" src="{{ smart_asset('storage/userimages/'.$auction->featuredProfile->user_id.'/'.$auction->featuredProfile->id.'/'.$auction->featuredProfile->singleimg->image) }}" alt="" />
                    @else
                        <div class="placeholder-img">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" opacity="0.3"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        </div>
                    @endif

                    {{-- Overlay with price, button, timer --}}
                    <div class="auction-overlay">
                        <p class="price">${{ $auction->current_price }}</p>
                        <p class="duration">for {{ $auction->duration_days ?? 7 }} days</p>

                        <a class="btn-offer" href="/auctions/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}/spot/{{ $auction->spot_number }}">Make Offer</a>

                        <div class="timer">
                            <svg class="timer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <div class="timer-text">
                                <span>auction ends in:</span>
                                <span class="countdown">{{ $auction->daysLeft }} {{ Str::plural('day', $auction->daysLeft) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="auction-spot-label">Spot #{{ $auction->spot_number }}</div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0;">
                <p style="color: #999; font-size: 16px;">No active auctions found for {{ $gender }} escorts in {{ $cityName }}</p>
                <p style="color: #666; font-size: 14px;">Check back later or contact support for more information.</p>
            </div>
            @endforelse
        </div>

        <hr style="border-color: #2a2a2a; margin: 40px 0;" />
    </div>
</div>
</div>
