{{-- Evoory Theme Homepage - Optimized for <0.6s load time --}}
{{-- Single root element for Livewire compatibility --}}
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
</div>

