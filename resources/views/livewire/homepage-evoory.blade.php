{{-- Evoory Theme Homepage - Optimized for <0.6s load time --}}
{{-- Single root element for Livewire compatibility --}}

{{-- Defensive anchor overrides for homepage elements.

     evoory-homepage.css contains a bare `a:hover { color: !important }` rule
     that's meant only for listing pages. The layout disables that stylesheet
     with media="print" outside listing pages, but after a wire:navigate hop
     to a service page and back, the media attribute sometimes ends up at
     "all" again (due to Livewire's head merge keeping duplicate <link>
     tags). Rather than continue fighting Livewire's head merger, we just
     win the cascade with higher-specificity rules here so the leak — if
     it happens — has no visible effect on the homepage's pills, the List-
     now button, and the city / male / shemale text links. Each rule below
     uses a class + pseudo-class selector so it beats `a:hover` even with
     !important on both sides, and adds !important itself for good measure. --}}
<style>
    /* Popular-locations city pills — DESKTOP behaviour kept intact
       (white solid background, wrapped rows on lime card). Only the
       hover-tint leak from evoory-homepage.css is defended here. */
    a.ev-tag,
    a.ev-tag:link,
    a.ev-tag:visited { color: #000 !important; background: #fff !important; }
    a.ev-tag:hover,
    a.ev-tag:focus,
    a.ev-tag:active { color: #000 !important; background: #f0f0f0 !important; }

    /* Constrain the pill row on desktop so it wraps to 3 lines and
       leaves space on the right for the silhouette background image.
       Both selectors are needed because evoory-theme.css sets max-width
       on .ev-popular-tags but the flex container is .ev-popular-row. */
    @media (min-width: 768px) {
        .ev-popular .ev-popular-tags,
        .ev-popular .ev-popular-row {
            max-width: 780px !important;
            width: 100% !important;
        }
    }

    /* Replace the legacy MR silhouette image with the local Evoory one.
       Overrides evoory-theme.css which points at massagerepublic.com.co. */
    .ev-popular {
        background-image: url('{{ asset("assets/newtheme/homebacground.png") }}') !important;
    }

    /* Custom scrollbar UI — hidden on desktop. Only used on mobile. */
    .ev-popular-scroller { display: none; }

    @media (max-width: 767px) {
        /* Mobile: outlined transparent pills in a single horizontal-scroll row. */
        a.ev-tag,
        a.ev-tag:link,
        a.ev-tag:visited {
            color: #fff !important;
            background: transparent !important;
            border: 1px solid #2a3a4a !important;
            border-radius: 999px !important;
            padding: 8px 18px !important;
            font-size: 14px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        a.ev-tag:hover,
        a.ev-tag:focus,
        a.ev-tag:active {
            color: #fff !important;
            background: rgba(193, 241, 29, 0.08) !important;
            border-color: #C1F11D !important;
        }

        /* Native scrollbar hidden — replaced by the custom UI below. */
        .ev-popular-tags {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            gap: 10px !important;
            padding-bottom: 0 !important;
            scrollbar-width: none;
        }
        .ev-popular-row { display: contents !important; }
        .ev-popular-tags::-webkit-scrollbar { display: none; }

        /* Custom scrollbar: [◀] [── track ──── thumb ──] [▶] */
        .ev-popular-scroller {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            user-select: none;
        }
        .ev-popular-scroller__btn {
            flex-shrink: 0;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #6a7886;
            cursor: pointer;
            padding: 0;
            border-radius: 4px;
            transition: color 0.15s;
        }
        .ev-popular-scroller__btn:hover:not(:disabled) { color: #C1F11D; }
        .ev-popular-scroller__btn:disabled { opacity: 0.35; cursor: default; }
        .ev-popular-scroller__btn svg {
            width: 14px !important;
            height: 14px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
            display: block;
        }
        .ev-popular-scroller__track {
            flex: 1;
            position: relative;
            height: 6px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 3px;
            cursor: pointer;
        }
        .ev-popular-scroller__thumb {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            min-width: 32px;
            background: #2a3a4a;
            border-radius: 3px;
            cursor: grab;
            transition: background 0.15s;
        }
        .ev-popular-scroller__thumb:hover,
        .ev-popular-scroller__thumb.is-dragging { background: #3a4a5a; }
        .ev-popular-scroller__thumb.is-dragging { cursor: grabbing; }
    }

    /* "List now" / "Go" lime buttons — text must stay legible (black on lime). */
    a.ev-btn-primary,
    a.ev-btn-primary:link,
    a.ev-btn-primary:visited,
    a.ev-btn-primary:hover,
    a.ev-btn-primary:focus,
    a.ev-btn-primary:active { color: #000 !important; }

    /* "male escorts" / "shemale escorts" inline text links in the search note. */
    .ev-search-note a,
    .ev-search-note a:link,
    .ev-search-note a:visited,
    .ev-search-note a:hover,
    .ev-search-note a:focus,
    .ev-search-note a:active { color: #C1F11D !important; }

    /* Center the logo in the mobile header on the homepage only.
       Desktop is unaffected. Other pages keep the left-aligned logo. */
    @media (max-width: 768px) {
        body:has(.ev-homepage) .ev-header .ev-flex.ev-justify-between {
            justify-content: center !important;
        }
        body:has(.ev-homepage) .ev-header .ev-nav { display: none !important; }
    }

    /* Mobile-only standalone "Individual Escort or Agency" CTA.
       Scoped under .evry-mcta — !important on svg sizing defeats the global
       `svg { transform: scale(2); width: 100%; }` rule in app.min.css. */
    .evry-mcta { display: none; }
    .evry-mcta-hide-desktop-card { display: block; }

    @media (max-width: 767px) {
        .evry-mcta-hide-desktop-card { display: none !important; }

        .evry-mcta {
            display: block;
            margin: 10px 16px 25px;
        }
        .evry-mcta__card {
            background: #0d1011;
            border: 1px solid #2a3a4a;
            border-radius: 18px;
            padding: 22px 20px;
        }
        .evry-mcta__head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }
        .evry-mcta__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
        }
        .evry-mcta__icon svg {
            display: block;
            width: 44px !important;
            height: 44px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .evry-mcta__title {
            color: #fff !important;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        .evry-mcta__desc {
            color: #c7d2dc;
            font-size: 16px;
            margin: 0 0 18px;
            line-height: 1.5;
        }
        .evry-mcta__btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #C1F11D;
            color: #000 !important;
            text-align: center;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 18px;
            text-decoration: none !important;
            width: 100%;
            max-width: 100%;
            white-space: nowrap;
            box-sizing: border-box;
        }
        .evry-mcta__btn svg {
            width: 18px !important;
            height: 18px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .evry-mcta__btn:hover,
        .evry-mcta__btn:focus {
            background: #b8e63d;
            color: #000 !important;
        }
    }
</style>

<div class="ev-homepage">
    {{-- Hero Tagline --}}
    <div class="ev-hero">
        <div class="ev-container">
            <p class="ev-hero-tagline">
                evoory – where escorts from Dubai and the rest of the world await
            </p>
        </div>
    </div>

    {{-- Search Section --}}
    <section class="ev-search-section">
        <div class="ev-container">
            <div class="ev-search-grid">
                {{-- Find an Escort Card --}}
                <div class="ev-card">
                    <h2 class="ev-card-title">Find an escort</h2>
                    <form class="ev-search-form" action="" method="get">
                        <div class="ev-relative">
                            <div class="ev-input-group">
                                <div class="ev-input-wrap">
                                    <svg class="ev-input-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M21 10c0 6-9 13-9 13s-9-7-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <input 
                                        type="text" 
                                        class="ev-input ev-city-search" 
                                        name="location" 
                                        placeholder="Your city" 
                                        autocomplete="off"
                                        data-slug=""
                                    >
                                </div>
                                <button type="submit" class="ev-btn ev-btn-primary">
                                    Go
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 18l6-6-6-6"/>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="city_id" id="city_id" value="">
                            <div class="ev-dropdown"></div>
                        </div>
                    </form>
                    <p class="ev-search-note">
                        Our goal is to help you find the right escort for you, right now! Evoory provides listings of providers of massage and other services. 
                        Not looking for a female escort? Click here for <a href="male-escorts-in-dubai">male escorts</a> or <a href="shemale-escorts-in-dubai">shemale escorts</a>.
                    </p>
                </div>
                
                {{-- List Now Card (desktop only — replaced by .evry-mcta on mobile) --}}
                <div class="ev-card evry-mcta-hide-desktop-card">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                        <svg style="flex-shrink:0;color:var(--accent)" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <h2 class="ev-card-title" style="margin:0;font-size:18px;line-height:1.3">Individual Escort or Agency</h2>
                    </div>
                    <p style="color:var(--text-secondary);margin:0 0 16px;font-size:14px">
                        Join thousands of verified professionals. Create your listing and connect with clients worldwide.
                    </p>
                    <a href="action/listings/new" class="ev-btn ev-btn-primary">
                        List now
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Mobile-only standalone "Individual Escort or Agency" CTA --}}
    <section class="evry-mcta">
        <div class="evry-mcta__card">
            <div class="evry-mcta__head">
                <span class="evry-mcta__icon" aria-hidden="true">
                    <svg width="44" height="44" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="35" height="35" rx="6" fill="#C1F11D" fill-opacity="0.08"/>
                        <path d="M9.67135 17.7609L14.8697 15.8697L16.7609 10.6713C16.8045 10.5526 16.8836 10.4502 16.9873 10.3778C17.0911 10.3054 17.2145 10.2666 17.341 10.2666C17.4675 10.2666 17.591 10.3054 17.6947 10.3778C17.7984 10.4502 17.8775 10.5526 17.9211 10.6713L19.8123 15.8697L25.0107 17.7609C25.1294 17.8045 25.2319 17.8836 25.3042 17.9873C25.3766 18.0911 25.4154 18.2145 25.4154 18.341C25.4154 18.4675 25.3766 18.591 25.3042 18.6947C25.2319 18.7984 25.1294 18.8775 25.0107 18.9211L19.8123 20.8123L17.9211 26.0107C17.8775 26.1294 17.7984 26.2319 17.6947 26.3042C17.591 26.3766 17.4675 26.4154 17.341 26.4154C17.2145 26.4154 17.0911 26.3766 16.9873 26.3042C16.8836 26.2319 16.8045 26.1294 16.7609 26.0107L14.8697 20.8123L9.67135 18.9211C9.55263 18.8775 9.45017 18.7984 9.37779 18.6947C9.30541 18.591 9.2666 18.4675 9.2666 18.341C9.2666 18.2145 9.30541 18.0911 9.37779 17.9873C9.45017 17.8836 9.55263 17.8045 9.67135 17.7609Z" stroke="#C1F11D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M26.9998 11.8952H24.8283V14.0667H24.1045V11.8952H21.9331V11.1714H24.1045V9H24.8283V11.1714H26.9998V11.8952Z" fill="#C1F11D"/>
                        <path d="M11.8 25.105H10.1714V26.7336H9.62857V25.105H8V24.5622H9.62857V22.9336H10.1714V24.5622H11.8V25.105Z" fill="#C1F11D"/>
                    </svg>
                </span>
                <h3 class="evry-mcta__title">Individual Escort or Agency</h3>
            </div>
            <p class="evry-mcta__desc">Join thousands of verified professionals. Create your listing and connect with clients worldwide.</p>
            <a href="action/listings/new" class="evry-mcta__btn">
                <span>List now</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
    </section>

    {{-- Popular Locations --}}
    <section>
        <div class="ev-container">
            <div class="ev-popular">
                <p class="ev-popular-title">Interested in other popular locations?</p>
                <div class="ev-popular-tags">
                    <div class="ev-popular-row">
                        <a href="female-escorts-in-abu-dhabi" class="ev-tag">Abu Dhabi</a>
                        <a href="female-escorts-in-al-manama" class="ev-tag">Al Manama</a>
                        <a href="female-escorts-in-nairobi" class="ev-tag">Nairobi</a>
                        <a href="female-escorts-in-dubai" class="ev-tag">Dubai</a>
                        <a href="female-escorts-in-bangalore" class="ev-tag">Bangalore</a>
                        <a href="female-escorts-in-bangkok" class="ev-tag">Bangkok</a>
                        </div>
                        <div class="ev-popular-row">
                        <a href="female-escorts-in-chennai" class="ev-tag">Chennai</a>
                        <a href="female-escorts-in-doha" class="ev-tag">Doha</a>
                        <a href="female-escorts-in-hyderabad" class="ev-tag">Hyderabad</a>
                        <a href="female-escorts-in-manila" class="ev-tag">Manila</a>
                        <a href="female-escorts-in-mumbai" class="ev-tag">Mumbai</a>
                        <a href="female-escorts-in-muscat" class="ev-tag">Muscat</a>
                        </div>
                        <div class="ev-popular-row">
                        <a href="female-escorts-in-new-delhi" class="ev-tag">New Delhi</a>
                        <a href="female-escorts-in-pune" class="ev-tag">Pune</a>
                        <a href="female-escorts-in-riyadh" class="ev-tag">Riyadh</a>
                    </div>
                </div>
                <div class="ev-popular-scroller" data-popular-scroller>
                    <button type="button" class="ev-popular-scroller__btn" data-scroll-prev aria-label="Scroll left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <div class="ev-popular-scroller__track" data-scroll-track>
                        <div class="ev-popular-scroller__thumb" data-scroll-thumb></div>
                    </div>
                    <button type="button" class="ev-popular-scroller__btn" data-scroll-next aria-label="Scroll right">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Welcome Section --}}
    <section class="ev-welcome">
        <div class="ev-container">
            <div class="ev-welcome-text-inner collapsed" id="ev-welcome-inner">
                <p class="ev-welcome-text" style="margin:0">
                    Welcome to Evoory, the world's premier platform for connecting with professional escort services worldwide. Our platform showcases a diverse array of stunning companions, each offering unique experiences tailored to your desires.
                    Whether you're seeking a charming dinner date, an adventurous travel partner, or an intimate encounter, you'll find the perfect match here.
                    Among our extensive listings, we proudly feature a selection of <strong>Dubai escorts</strong>, renowned for their elegance and sophistication.
                    These captivating companions embody the luxurious lifestyle of this vibrant city, providing unforgettable experiences that blend allure and excitement.
                    Explore profiles, read reviews, and connect with escorts who can make your time in Dubai truly exceptional.
                    Use our search feature to find a companion in Dubai (or any other city) that has all of your favourite physical characteristics or offering the service you would like to enjoy.
                    <br>
                    <strong>Join us at Evoory and discover the world of companionship at your fingertips, with a special emphasis on the enchanting Dubai escorts ready to elevate your experience.</strong>
                </p>
            </div>
            <button class="ev-read-more-btn" id="ev-read-more" aria-expanded="false" onclick="(function(b,w){var open=w.classList.toggle('collapsed');b.textContent=open?'Read more \u2193':'Show less \u2191';b.setAttribute('aria-expanded',!open)})(this,document.getElementById('ev-welcome-inner'))">Read more &#x2193;</button>
        </div>
    </section>

    {{-- Browse by Country Section --}}
    <section class="ev-browse">
        <div class="ev-container">
            <h2 class="ev-section-title">
                <img src="https://assets.massagerepublic.com.co/assets/newtheme/globe.svg" alt="" class="ev-section-icon" aria-hidden="true">
                Browse by Country
            </h2>
            <p class="ev-section-subtitle">Explore our global directory of services across {{ count($countriesWithCities) }}+ countries worldwide</p>
            
            {{-- Alphabet Filter --}}
            <div class="ev-alpha-filter">
                <button class="ev-alpha-btn all active" data-letter="all">All</button>
                @foreach(range('A', 'Z') as $letter)
                    <button class="ev-alpha-btn" data-letter="{{ $letter }}">{{ $letter }}</button>
                @endforeach
            </div>
            
            {{-- Country Grid --}}
            <div class="ev-country-grid">
                @foreach($countriesWithCities as $country)
                    <button type="button" class="ev-country-card" data-country="{{ $country['name'] }}" data-country-code="{{ strtolower($country['code']) }}" data-country-count="{{ $country['cities'] }}">
                        <p class="ev-country-code">{{ $country['code'] }}</p>
                        <p class="ev-country-name">{{ $country['name'] }}</p>
                        <p class="ev-country-cities">{{ $country['cities'] }} {{ $country['cities'] == 1 ? 'City' : 'Cities' }}</p>
                    </button>
                @endforeach
            </div>

            <div class="ev-country-modal" id="ev-country-modal" aria-hidden="true">
                <div class="ev-country-modal-overlay" data-close-country-modal></div>
                <div class="ev-country-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="ev-country-modal-title">
                    <div class="ev-country-modal-head">
                        <div class="ev-country-meta">
                            <div class="ev-country-modal-title" id="ev-country-modal-title">
                                <span class="ev-country-modal-iso">--</span>
                                <span class="ev-country-modal-name">Country</span>
                            </div>
                            <div class="ev-country-modal-count">0 Cities</div>
                        </div>
                        <div class="ev-country-modal-search-wrap">
                            <input type="text" class="ev-country-modal-search" id="ev-country-modal-search" placeholder="Search cities..." autocomplete="off">
                        </div>
                        <button type="button" class="ev-country-modal-close" aria-label="Close" data-close-country-modal>
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="ev-country-modal-body" id="ev-country-modal-body"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Strip leaked legacy stylesheets when this page is mounted. The legacy
         `app` layout's css-optimized bundle loads app.css/app2.css/app3.css/app4.css.
         wire:navigate keeps those <link>s in <head> when arriving here from a
         legacy-layout page (news-page, profile-details, etc.). Their global rules
         (`a:hover { color:#dca623 }`, FA4 @font-face, etc.) leak into this page.
         Detect by `body > main` (only present on app-evoory) and strip; re-attach
         when navigating back to a legacy page. --}}
    <script>
    (function () {
        var LEGACY_CSS = ['app.css', 'app2.css', 'app3.css', 'app4.css'];

        function syncLegacyCss() {
            var onEvooryLayout = !!document.querySelector('body > main');
            window.__evooryStashedLegacyCss = window.__evooryStashedLegacyCss || {};
            var stash = window.__evooryStashedLegacyCss;

            LEGACY_CSS.forEach(function (name) {
                var live = document.querySelector('link[rel="stylesheet"][href*="assets/css/' + name + '"]');

                if (onEvooryLayout && live) {
                    stash[name] = live.getAttribute('href');
                    live.parentNode.removeChild(live);
                } else if (!onEvooryLayout && !live && stash[name]) {
                    var link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = stash[name];
                    document.head.appendChild(link);
                }
            });
        }

        // Only strip on real navigations. On initial script load the body
        // content may not be fully swapped in via wire:navigate yet.
        if (!window.__evooryHomepageLegacyListener) {
            window.__evooryHomepageLegacyListener = true;
            document.addEventListener('livewire:navigated', syncLegacyCss);
        }
    })();
    </script>

    {{-- Custom horizontal scrollbar for the popular-locations row.
         Native scrollbar is hidden (CSS above) and we render our own UI:
         left/right arrow buttons + a draggable thumb. Thumb width and
         position are kept in sync with the underlying scrollLeft. --}}
    <script>
    (function () {
        function initScroller(scroller) {
            if (!scroller || scroller.dataset.bound === '1') return;
            scroller.dataset.bound = '1';

            var tags = scroller.parentElement.querySelector('.ev-popular-tags');
            var prevBtn = scroller.querySelector('[data-scroll-prev]');
            var nextBtn = scroller.querySelector('[data-scroll-next]');
            var track = scroller.querySelector('[data-scroll-track]');
            var thumb = scroller.querySelector('[data-scroll-thumb]');
            if (!tags || !track || !thumb) return;

            function updateThumb() {
                var sw = tags.scrollWidth;
                var cw = tags.clientWidth;
                if (sw <= cw + 1) {
                    // Nothing to scroll — hide the whole scrollbar.
                    scroller.style.display = 'none';
                    return;
                }
                scroller.style.display = '';

                var trackW = track.clientWidth;
                var ratio = cw / sw;
                var thumbW = Math.max(32, Math.floor(trackW * ratio));
                var maxThumbLeft = trackW - thumbW;
                var scrollRatio = sw - cw > 0 ? tags.scrollLeft / (sw - cw) : 0;
                var thumbLeft = Math.round(maxThumbLeft * scrollRatio);

                thumb.style.width = thumbW + 'px';
                thumb.style.left = thumbLeft + 'px';

                prevBtn.disabled = tags.scrollLeft <= 0;
                nextBtn.disabled = tags.scrollLeft >= (sw - cw - 1);
            }

            // Initial paint + on resize.
            updateThumb();
            window.addEventListener('resize', updateThumb);

            // Scroll position changes (finger swipe / wheel) → repaint thumb.
            tags.addEventListener('scroll', updateThumb, { passive: true });

            // Arrow buttons scroll a fixed step.
            function step(delta) {
                tags.scrollBy({ left: delta, behavior: 'smooth' });
            }
            prevBtn.addEventListener('click', function () { step(-200); });
            nextBtn.addEventListener('click', function () { step(200); });

            // Click on empty track → jump to that position.
            track.addEventListener('mousedown', function (e) {
                if (e.target === thumb) return;
                var rect = track.getBoundingClientRect();
                var clickX = e.clientX - rect.left;
                var thumbW = thumb.offsetWidth;
                var targetThumbLeft = Math.max(0, Math.min(track.clientWidth - thumbW, clickX - thumbW / 2));
                var maxThumbLeft = track.clientWidth - thumbW;
                var scrollRatio = maxThumbLeft > 0 ? targetThumbLeft / maxThumbLeft : 0;
                tags.scrollTo({ left: scrollRatio * (tags.scrollWidth - tags.clientWidth), behavior: 'smooth' });
            });

            // Drag the thumb (mouse + touch).
            var dragging = false;
            var dragStartX = 0;
            var dragStartThumbLeft = 0;

            function startDrag(clientX) {
                dragging = true;
                dragStartX = clientX;
                dragStartThumbLeft = parseFloat(thumb.style.left || '0');
                thumb.classList.add('is-dragging');
            }
            function moveDrag(clientX) {
                if (!dragging) return;
                var dx = clientX - dragStartX;
                var thumbW = thumb.offsetWidth;
                var maxThumbLeft = track.clientWidth - thumbW;
                var newThumbLeft = Math.max(0, Math.min(maxThumbLeft, dragStartThumbLeft + dx));
                var scrollRatio = maxThumbLeft > 0 ? newThumbLeft / maxThumbLeft : 0;
                tags.scrollLeft = scrollRatio * (tags.scrollWidth - tags.clientWidth);
            }
            function endDrag() {
                if (!dragging) return;
                dragging = false;
                thumb.classList.remove('is-dragging');
            }

            thumb.addEventListener('mousedown', function (e) {
                e.preventDefault();
                startDrag(e.clientX);
            });
            document.addEventListener('mousemove', function (e) {
                if (dragging) moveDrag(e.clientX);
            });
            document.addEventListener('mouseup', endDrag);

            thumb.addEventListener('touchstart', function (e) {
                if (!e.touches[0]) return;
                startDrag(e.touches[0].clientX);
            }, { passive: true });
            document.addEventListener('touchmove', function (e) {
                if (dragging && e.touches[0]) {
                    e.preventDefault();
                    moveDrag(e.touches[0].clientX);
                }
            }, { passive: false });
            document.addEventListener('touchend', endDrag);
            document.addEventListener('touchcancel', endDrag);
        }

        function initAll() {
            document.querySelectorAll('[data-popular-scroller]').forEach(initScroller);
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
        document.addEventListener('livewire:navigated', initAll);
    })();
    </script>

    {{-- Listing CSS strip/restore script removed — layout now keeps the
         <link> in head permanently and toggles media="all"/"print" on
         wire:navigate. See components/layouts/app-evoory.blade.php. --}}
</div>

