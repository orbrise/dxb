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
    /* Popular-locations city pills — leaked rule was making them lime on hover. */
    a.ev-tag,
    a.ev-tag:link,
    a.ev-tag:visited { color: #000 !important; background: #fff !important; }
    a.ev-tag:hover,
    a.ev-tag:focus,
    a.ev-tag:active { color: #000 !important; background: #f0f0f0 !important; }

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
                
                {{-- List Now Card --}}
                <div class="ev-card">
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
                        <a href="female-escorts-in-chennai" class="ev-tag">Chennai</a>
                        <a href="female-escorts-in-doha" class="ev-tag">Doha</a>
                        <a href="female-escorts-in-hyderabad" class="ev-tag">Hyderabad</a>
                        <a href="female-escorts-in-manila" class="ev-tag">Manila</a>
                        <a href="female-escorts-in-mumbai" class="ev-tag">Mumbai</a>
                        <a href="female-escorts-in-muscat" class="ev-tag">Muscat</a>
                        <a href="female-escorts-in-new-delhi" class="ev-tag">New Delhi</a>
                        <a href="female-escorts-in-pune" class="ev-tag">Pune</a>
                        <a href="female-escorts-in-riyadh" class="ev-tag">Riyadh</a>
                    </div>
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

    {{-- Listing CSS strip/restore script removed — layout now keeps the
         <link> in head permanently and toggles media="all"/"print" on
         wire:navigate. See components/layouts/app-evoory.blade.php. --}}
</div>

