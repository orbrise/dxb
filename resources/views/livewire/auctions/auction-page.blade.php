@push('css')
<style>
/* Sub-header bar.
   Uses a 3-column grid (back-link | title | spacer) so the title
   sits at the true visual center of the container regardless of
   how wide the back-link is. Prior version used
   `position: absolute; left: 50%` which is mathematically centered
   in the container but reads as off-center to the eye because only
   the left side has content — the title looks pulled toward the
   right of the whitespace, not the middle of the bar. */
/* Sub-header. Flex container centered on both axes so the
   title is horizontally + vertically centered inside the
   container. Back link is absolutely positioned at left:0
   with its OWN flex centering so its content baseline sits
   at the same Y as the title. Keeps `.ev-container`'s
   natural 1300px centering (same as top nav) so "Back"
   aligns with the top-nav logo horizontally.

   Prior version had the title on the inline-flow baseline
   (below the flex-centered back-link) which read as a
   vertical offset — both need the same vertical alignment
   technique. */
.auction-subheader {
    background: #1f2222;
    padding: 12px 0;
}
.auction-subheader .ev-container {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    min-height: 24px;
}
.auction-subheader .back-link {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
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
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    line-height: 1;
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
        grid-template-columns: 1fr;
        gap: 20px;
        max-width: 320px;
        margin: 0 auto;
    }
}

/* ═══ MOBILE VIEW ═══ */
@media (max-width: 768px) {
    #header { margin-bottom: 0 !important; }
    .auction-subheader { padding: 10px 0 !important; }
    .auction-subheader .back-link { font-size: 13px !important; }
    .auction-subheader .back-link .ev-desktop-back { display: none !important; }
    .auction-subheader .back-link .ev-mobile-back { display: inline-flex !important; align-items: center; gap: 4px; }
    .auction-subheader .page-title { font-size: 14px !important; }
    .auction-page { padding: 16px 0 40px !important; }
    .auction-page .section-title { font-size: 16px !important; }
    .auction-page .section-subtitle { font-size: 12px !important; }
    .auction-warning { border-radius: 5px !important; font-size: 13px !important; padding: 10px 14px !important; }
    .auction-grid { grid-template-columns: 1fr; gap: 20px; max-width: 320px; margin: 0 auto; }
    .auction-card-img { height: 280px !important; border-radius: 5px !important; }
    .auction-overlay .price { font-size: 28px !important; }
    .auction-overlay .btn-offer { border-radius: 5px !important; padding: 10px 32px !important; font-size: 15px !important; }
    .auction-overlay .duration { font-size: 14px !important; color: #ccc !important; }
    .auction-overlay .timer { flex-direction: column !important; gap: 2px !important; margin-top: 16px !important; }
    .auction-overlay .timer-icon { width: 22px !important; height: 22px !important; opacity: 0.6 !important; }
    .auction-overlay .timer-text { text-align: center !important; }
    .auction-overlay .timer-text span { font-size: 13px !important; color: #ccc !important; }
    .auction-overlay .timer-text .countdown { font-size: 16px !important; font-weight: 700 !important; color: #fff !important; }
    .auction-spot-label { font-size: 14px !important; padding: 8px 0 !important; text-transform: uppercase !important; letter-spacing: 1px !important; }
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
/* price size handled in mobile section below */
.auction-overlay .duration {
    color: #ccc;
    font-size: 13px;
    margin: 2px 0 14px;
}

/* Make Offer button.
   `!important` on color is required because evoory-homepage-base.css
   sets `a { color: #C1F11D !important; }` globally, which would render
   the button text lime-on-lime (invisible). */
.auction-overlay .btn-offer,
.auction-overlay a.btn-offer {
    background: #C1F11D;
    color: #000 !important;
    border: none;
    padding: 5px 24px;
    border-radius: 22px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.3s ease;
    display: inline-block;
}
.auction-overlay .btn-offer:hover,
.auction-overlay a.btn-offer:hover {
    background: #d4f84d;
    color: #000 !important;
    text-decoration: none !important;
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

.page-title{
        margin-top: 0px !important
}

/* City chooser: auctions run per city, so the spots show once one is picked. */
.auction-picker { margin-bottom: 28px; padding: 22px; border: 1px solid #23292B; border-radius: 14px; background: #15191B; }
.auction-picker-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px; }
.auction-picker h2 { margin: 0 0 4px; color: #fff; font-size: 20px; font-weight: 600; }
.auction-picker p { margin: 0; color: #9aa6ac; font-size: 14px; }
.auction-empty-title { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
.auction-empty-title svg { flex: none; color: #8e99a0; }
.auction-empty-title h2 { margin: 0; }
.auction-picker-city { color: #C1F11D; }
/* Link colors need !important: evoory-homepage-base.css forces
   a { color: lime !important; text-decoration: none !important } globally. */
.auction-change-city { margin-left: 10px; color: #9aa6ac !important; font-size: 13px; text-decoration: underline !important; cursor: pointer; }
.auction-change-city:hover { color: #fff !important; }
.auction-genders { display: flex; flex-wrap: wrap; gap: 8px; }
.auction-genders a { padding: 7px 14px; border: 1px solid #2a3133; border-radius: 999px; background: transparent; color: #c9d0d3 !important; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none !important; display: inline-block; }
.auction-genders a:hover { border-color: #3a4245; color: #fff !important; }
.auction-genders a.active { border-color: #C1F11D; background: rgba(193, 241, 29, 0.08); color: #C1F11D !important; }
.auction-search { position: relative; max-width: 520px; margin-top: 18px; }
.auction-search-box { display: flex; align-items: center; gap: 10px; height: 48px; padding: 0 14px; border: 1px solid #2a3133; border-radius: 10px; background: #0D1011; }
.auction-search-box:focus-within { border-color: #C1F11D; }
.auction-search-box svg { flex: none; color: #8e99a0; }
.auction-search-box input { flex: 1; min-width: 0; height: 100%; border: 0; outline: 0; background: transparent; color: #fff; font-size: 16px; }
.auction-search-box input::placeholder { color: #6b767b; }
.auction-results { position: absolute; top: calc(100% + 6px); right: 0; left: 0; z-index: 20; max-height: 300px; overflow-y: auto; padding: 6px; border: 1px solid #2a3133; border-radius: 10px; background: #15191B; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45); }
.auction-results[hidden] { display: none; }
.auction-results[data-loading="1"] { opacity: .65; }
.auction-results a { display: block; width: 100%; padding: 10px 12px; border-radius: 8px; color: #fff !important; font-size: 14px; text-align: left; cursor: pointer; text-decoration: none !important; }
.auction-results a:hover { background: #1f2527; }
.auction-results small, .auction-suggest small { color: #8e99a0; }
.auction-results .none { padding: 10px 12px; color: #8e99a0; font-size: 13px; }
.auction-suggest-label { margin: 18px 0 10px; color: #9aa6ac; font-size: 12px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
.auction-suggest { display: flex; flex-wrap: wrap; gap: 8px; }
.auction-suggest a { display: inline-flex; align-items: center; gap: 7px; padding: 8px 14px; border: 1px solid #2a3133; border-radius: 999px; background: #1b2022; color: #fff !important; font-size: 13.5px; cursor: pointer; text-decoration: none !important; }
.auction-suggest a:hover { border-color: #C1F11D; }
.auction-suggest .open-dot { width: 7px; height: 7px; border-radius: 50%; background: #C1F11D; }
.auction-notice { margin-top: 14px; color: #ffb44d; font-size: 13.5px; }
.auction-none { grid-column: 1 / -1; padding: 50px 20px; border: 1px dashed #2a3133; border-radius: 14px; text-align: center; }
.auction-none h3 { margin: 0 0 6px; color: #fff; font-size: 18px; font-weight: 600; }
.auction-none p { margin: 0; color: #8e99a0; font-size: 14px; }
@media (max-width: 768px) {
    .auction-picker { padding: 16px; border-radius: 12px; }
    .auction-picker h2 { font-size: 17px; }
}
</style>
@endpush

<div>
{{-- Sub-header --}}
<div class="auction-subheader">
    <div class="ev-container">
        <a class="back-link" href="{{ $citySlug ? '/' . $gender . '-escorts-in-' . $citySlug : '/' }}">
            <span class="ev-desktop-back"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg> Back</span>
            <span class="ev-mobile-back" style="display:none;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg> Home</span>
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

        {{-- City chooser: spots are per city, so they show once one is picked.
             Deliberately server-rendered (plain links + a GET search form) with
             NO Alpine/Livewire JS dependency: on production the JS bundle can
             fail to boot, which previously left the x-cloak'd chooser hidden
             and the controls dead. Plain navigation works everywhere. --}}
        <div class="auction-picker">
            <div class="auction-picker-head">
                <div>
                    @if($cityId)
                        <h2>Top Spots in <span class="auction-picker-city">{{ $cityName }}</span></h2>
                        <p>
                            {{ $auctions->count() ? $auctions->count() . ' ' . \Illuminate\Support\Str::plural('spot', $auctions->count()) . ' open for offers' : 'No spots open right now' }}
                            <a class="auction-change-city" href="{{ url('/auctions') . '?gender=' . $gender }}">Change city</a>
                        </p>
                    @elseif(!$hasOpenSpots)
                        <div class="auction-empty-title">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="10" y1="14" x2="14" y2="18"/><line x1="14" y1="14" x2="10" y2="18"/></svg>
                            <h2>No auction slots available</h2>
                        </div>
                        <p>There are no Top Spots open for offers in the {{ \App\Livewire\Auctions\AuctionPage::GENDERS[$gender] }} category right now. Check back later, or try another category.</p>
                    @else
                        <h2>Choose a city</h2>
                        <p>Top Spot auctions run per city. Pick yours to see the spots open for offers.</p>
                    @endif
                </div>
                <div class="auction-genders" role="group" aria-label="Category">
                    @foreach(\App\Livewire\Auctions\AuctionPage::GENDERS as $value => $label)
                        <a class="{{ $gender === $value ? 'active' : '' }}"
                                href="{{ $citySlug ? url('/auctions/' . $value . '-escorts-in-' . $citySlug) : url('/auctions') . '?gender=' . $value }}"
                                aria-pressed="{{ $gender === $value ? 'true' : 'false' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            @if($cityNotFound)
                <div class="auction-notice">We couldn't find that city. Search for it below.</div>
            @endif

            @if(!$cityId && $hasOpenSpots)
                {{-- Typeahead: typing fetches /auctions/cities and shows selectable
                     cities (inline vanilla JS below). The GET form is the no-JS /
                     Enter fallback: it reloads with ?q= and results render server-side. --}}
                <form class="auction-search" method="get" action="{{ url('/auctions') }}">
                    <input type="hidden" name="gender" value="{{ $gender }}">
                    <div class="auction-search-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <input type="text" name="q" id="auctionCitySearch" value="{{ $citySearch }}" placeholder="Search for a city…" autocomplete="off" aria-label="Search for a city">
                    </div>
                    <div class="auction-results" id="auctionCityResults" data-gender="{{ $gender }}" @if(mb_strlen(trim($citySearch)) < 2) hidden @endif>
                        @if(mb_strlen(trim($citySearch)) >= 2)
                            @forelse($searchResults as $result)
                                <a href="{{ url('/auctions/' . $gender . '-escorts-in-' . $result['slug']) }}">
                                    {{ $result['name'] }}@if($result['country']) <small>· {{ $result['country'] }}</small>@endif
                                </a>
                            @empty
                                <div class="none">No cities match “{{ $citySearch }}”.</div>
                            @endforelse
                        @endif
                    </div>
                </form>
                @if($suggestedCities->isNotEmpty())
                    <div class="auction-suggest-label">Popular cities</div>
                    <div class="auction-suggest">
                        @foreach($suggestedCities as $suggested)
                            <a href="{{ url('/auctions/' . $gender . '-escorts-in-' . $suggested->slug) }}"
                                    @if(!empty($suggested->hasOpenSpots)) title="Spots open for offers" @endif>
                                @if(!empty($suggested->hasOpenSpots))<span class="open-dot" aria-hidden="true"></span>@endif
                                {{ $suggested->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        {{-- Live city typeahead. Plain vanilla JS on purpose: production has
             shown the Livewire/Alpine bundle failing to boot, and this must
             keep working regardless. Typing (2+ chars) fetches matching
             cities; clicking one opens its auctions. No submit arrow. --}}
        <script>
        (function () {
            var input = document.getElementById('auctionCitySearch');
            var results = document.getElementById('auctionCityResults');
            if (!input || !results) return;

            var gender = results.getAttribute('data-gender') || 'female';
            var endpoint = @json(url('/auctions/cities'));
            var timer = null;
            var controller = null;
            var lastQuery = '';

            function hide() { results.hidden = true; }

            function render(rows, term) {
                results.textContent = '';
                if (!rows.length) {
                    var none = document.createElement('div');
                    none.className = 'none';
                    none.textContent = 'No cities match \u201C' + term + '\u201D.';
                    results.appendChild(none);
                } else {
                    rows.forEach(function (row) {
                        var a = document.createElement('a');
                        a.href = '/auctions/' + gender + '-escorts-in-' + encodeURIComponent(row.slug);
                        a.textContent = row.name;
                        if (row.country) {
                            var small = document.createElement('small');
                            small.textContent = ' \u00B7 ' + row.country;
                            a.appendChild(small);
                        }
                        results.appendChild(a);
                    });
                }
                results.hidden = false;
            }

            function fetchCities(term) {
                if (controller) controller.abort();
                controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
                results.setAttribute('data-loading', '1');
                fetch(endpoint + '?q=' + encodeURIComponent(term), {
                    headers: { 'Accept': 'application/json' },
                    signal: controller ? controller.signal : undefined
                }).then(function (res) { return res.json(); }).then(function (data) {
                    results.removeAttribute('data-loading');
                    if (input.value.trim() !== term) return; // a newer keystroke won
                    lastQuery = term;
                    render(data.results || [], term);
                }).catch(function () {
                    results.removeAttribute('data-loading');
                });
            }

            input.addEventListener('input', function () {
                var term = input.value.trim();
                if (timer) clearTimeout(timer);
                if (term.length < 2) { hide(); lastQuery = ''; return; }
                if (term === lastQuery && !results.hidden) return;
                timer = setTimeout(function () { fetchCities(term); }, 200);
            });

            input.addEventListener('focus', function () {
                if (input.value.trim().length >= 2 && results.childNodes.length) results.hidden = false;
            });

            // Enter opens the first match; with the dropdown closed it falls
            // through to the GET form (server-rendered ?q= results).
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') { hide(); return; }
                if (e.key === 'Enter') {
                    var first = results.querySelector('a');
                    if (!results.hidden && first) {
                        e.preventDefault();
                        window.location.href = first.getAttribute('href');
                    }
                }
            });

            document.addEventListener('click', function (e) {
                if (!results.hidden && e.target !== input && !results.contains(e.target)) hide();
            });

            input.addEventListener('blur', function () {
                setTimeout(function () {
                    if (document.activeElement !== input && !results.contains(document.activeElement)) hide();
                }, 150);
            });
        })();
        </script>

        @if($cityId)
        <div style="margin-bottom: 24px;">
            <h2 class="section-title">This week's deluxe profiles</h2>
            <span class="section-subtitle">(view for logged-in advertisers)</span>
        </div>

        <div class="auction-grid">
            @forelse($auctions as $auction)
            <div class="auction-card">
                <div class="auction-card-img">
                    {{-- The slot's image: the admin's upload, else the default
                         (Auction::background_image_url). If an upload ever fails
                         to load, fall back to the default instead of a broken icon. --}}
                    <img loading="lazy" src="{{ $auction->background_image_url }}" alt="Spot #{{ $auction->spot_number }}"
                         onerror="this.onerror=null;this.src='{{ \App\Models\Auction::defaultImageUrl() }}'" />

                    {{-- Overlay with price, button, timer --}}
                    <div class="auction-overlay">
                        <p class="price">${{ $auction->current_price }}</p>
                        <p class="duration">for {{ $auction->duration_days ?? 7 }} days</p>

                        <a class="btn-offer" href="{{ url('/auctions/' . $gender . '-escorts-in-' . $citySlug . '/spot/' . $auction->spot_number) }}">Make Offer</a>

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
            <div class="auction-none">
                <h3>No slots available in {{ $cityName }}</h3>
                <p>No spots are open for offers in {{ $cityName }} in the {{ \App\Livewire\Auctions\AuctionPage::GENDERS[$gender] }} category right now. Check back later, or try another city or category.</p>
            </div>
            @endforelse
        </div>
        @endif

       
    </div>
</div>
</div>
