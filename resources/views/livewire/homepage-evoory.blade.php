{{-- Evoory Theme Homepage - Optimized for <0.6s load time --}}
{{-- Single root element for Livewire compatibility --}}
<div class="ev-homepage">
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
                                <input 
                                    type="text" 
                                    class="ev-input ev-city-search" 
                                    name="location" 
                                    placeholder="Your City" 
                                    autocomplete="off"
                                    data-slug=""
                                >
                                <button type="submit" class="ev-btn ev-btn-primary">Go</button>
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
                    <h2 class="ev-card-title">Individual escort or agency?</h2>
                    <p style="color:var(--text-secondary);margin:0 0 16px;font-size:14px">
                        A basic listing on the website is free! Don't worry if you don't see your city on the left or below – it will appear when you list!
                    </p>
                    <a href="action/listings/new" class="ev-btn ev-btn-outline">
                        List now
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
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
                    @if($featuredCities && $featuredCities->count() > 0)
                        @foreach($featuredCities as $city)
                            <a href="female-escorts-in-{{ $city->slug }}" class="ev-tag">{{ $city->name }}</a>
                        @endforeach
                    @else
                        <a href="female-escorts-in-abu-dhabi" class="ev-tag">Abu Dhabi</a>
                        <a href="female-escorts-in-al-fujayrah" class="ev-tag">Al Fujayrah</a>
                        <a href="female-escorts-in-dubai" class="ev-tag">Dubai</a>
                        <a href="female-escorts-in-sharjah" class="ev-tag">Sharjah</a>
                        <a href="female-escorts-in-ajman" class="ev-tag">Ajman</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Welcome Section --}}
    <section class="ev-welcome">
        <div class="ev-container">
            <h1 class="ev-welcome-title">Welcome to Evoory.com, your premier destination for connecting with escorts from all corners of the globe.</h1>
            <p class="ev-welcome-text">
                Our platform showcases a diverse array of stunning companions, each offering unique experiences tailored to your desires.
                Whether you're seeking a charming dinner date, an adventurous travel partner, or an intimate encounter, you'll find the perfect match here.
                Among our extensive listings, we proudly feature a selection of <strong>Dubai escorts</strong>, renowned for their elegance and sophistication.
                These captivating companions embody the luxurious lifestyle of this vibrant city, providing unforgettable experiences that blend allure and excitement.
                Explore profiles, read reviews, and connect with escorts who can make your time in Dubai truly exceptional.
                Use our search feature to find a companion in Dubai (or any other city) that has all of your favourite physical characteristics or offering the service you would like to enjoy.
                <br><br>
                <strong>Join us at Evoory and discover the world of companionship at your fingertips, with a special emphasis on the enchanting Dubai escorts ready to elevate your experience.</strong>
            </p>
        </div>
    </section>

    {{-- Browse by Country Section --}}
    <section class="ev-browse">
        <div class="ev-container">
            <h2 class="ev-section-title">Browse by Country</h2>
            <p class="ev-section-subtitle">Explore our global directory of services across 50+ countries worldwide</p>
            
            {{-- Alphabet Filter --}}
            <div class="ev-alpha-filter">
                <button class="ev-alpha-btn all active" data-letter="all">All</button>
                @foreach(range('A', 'Z') as $letter)
                    <button class="ev-alpha-btn" data-letter="{{ $letter }}">{{ $letter }}</button>
                @endforeach
            </div>
            
            {{-- Country Grid --}}
            <div class="ev-country-grid">
                @php
                $countries = [
                    ['code' => 'AR', 'name' => 'Argentina', 'cities' => 12],
                    ['code' => 'AU', 'name' => 'Australia', 'cities' => 10],
                    ['code' => 'AT', 'name' => 'Austria', 'cities' => 7],
                    ['code' => 'BH', 'name' => 'Bahrain', 'cities' => 7],
                    ['code' => 'BE', 'name' => 'Belgium', 'cities' => 7],
                    ['code' => 'BR', 'name' => 'Brazil', 'cities' => 14],
                    ['code' => 'CA', 'name' => 'Canada', 'cities' => 71],
                    ['code' => 'CL', 'name' => 'Chile', 'cities' => 5],
                    ['code' => 'CN', 'name' => 'China', 'cities' => 19],
                    ['code' => 'CO', 'name' => 'Colombia', 'cities' => 11],
                    ['code' => 'HR', 'name' => 'Croatia', 'cities' => 6],
                    ['code' => 'CY', 'name' => 'Cyprus', 'cities' => 5],
                    ['code' => 'CZ', 'name' => 'Czechia', 'cities' => 3],
                    ['code' => 'DK', 'name' => 'Denmark', 'cities' => 3],
                    ['code' => 'EG', 'name' => 'Egypt', 'cities' => 4],
                    ['code' => 'FI', 'name' => 'Finland', 'cities' => 4],
                    ['code' => 'FR', 'name' => 'France', 'cities' => 56],
                    ['code' => 'DE', 'name' => 'Germany', 'cities' => 32],
                    ['code' => 'GR', 'name' => 'Greece', 'cities' => 6],
                    ['code' => 'HK', 'name' => 'Hong Kong', 'cities' => 1],
                    ['code' => 'HU', 'name' => 'Hungary', 'cities' => 3],
                    ['code' => 'IN', 'name' => 'India', 'cities' => 108],
                    ['code' => 'ID', 'name' => 'Indonesia', 'cities' => 6],
                    ['code' => 'IE', 'name' => 'Ireland', 'cities' => 2],
                    ['code' => 'IL', 'name' => 'Israel', 'cities' => 5],
                    ['code' => 'IT', 'name' => 'Italy', 'cities' => 9],
                    ['code' => 'JP', 'name' => 'Japan', 'cities' => 27],
                    ['code' => 'KE', 'name' => 'Kenya', 'cities' => 15],
                    ['code' => 'KW', 'name' => 'Kuwait', 'cities' => 1],
                    ['code' => 'MY', 'name' => 'Malaysia', 'cities' => 5],
                    ['code' => 'MX', 'name' => 'Mexico', 'cities' => 10],
                    ['code' => 'NL', 'name' => 'Netherlands', 'cities' => 10],
                    ['code' => 'NZ', 'name' => 'New Zealand', 'cities' => 4],
                    ['code' => 'NG', 'name' => 'Nigeria', 'cities' => 15],
                    ['code' => 'NO', 'name' => 'Norway', 'cities' => 5],
                    ['code' => 'OM', 'name' => 'Oman', 'cities' => 9],
                    ['code' => 'PK', 'name' => 'Pakistan', 'cities' => 6],
                    ['code' => 'PH', 'name' => 'Philippines', 'cities' => 16],
                    ['code' => 'PL', 'name' => 'Poland', 'cities' => 8],
                    ['code' => 'PT', 'name' => 'Portugal', 'cities' => 17],
                    ['code' => 'QA', 'name' => 'Qatar', 'cities' => 6],
                    ['code' => 'RO', 'name' => 'Romania', 'cities' => 2],
                    ['code' => 'SA', 'name' => 'Saudi Arabia', 'cities' => 20],
                    ['code' => 'SG', 'name' => 'Singapore', 'cities' => 1],
                    ['code' => 'ZA', 'name' => 'South Africa', 'cities' => 24],
                    ['code' => 'KR', 'name' => 'South Korea', 'cities' => 7],
                    ['code' => 'ES', 'name' => 'Spain', 'cities' => 18],
                    ['code' => 'LK', 'name' => 'Sri Lanka', 'cities' => 4],
                    ['code' => 'SE', 'name' => 'Sweden', 'cities' => 9],
                    ['code' => 'CH', 'name' => 'Switzerland', 'cities' => 11],
                    ['code' => 'TW', 'name' => 'Taiwan', 'cities' => 5],
                    ['code' => 'TH', 'name' => 'Thailand', 'cities' => 18],
                    ['code' => 'TR', 'name' => 'Turkey', 'cities' => 9],
                    ['code' => 'AE', 'name' => 'UAE', 'cities' => 9],
                    ['code' => 'GB', 'name' => 'United Kingdom', 'cities' => 82],
                    ['code' => 'VN', 'name' => 'Vietnam', 'cities' => 5],
                ];
                @endphp
                
                @foreach($countries as $country)
                    <a href="country/{{ strtolower($country['code']) }}" class="ev-country-card" data-country="{{ $country['name'] }}">
                        <p class="ev-country-code">{{ $country['code'] }}</p>
                        <p class="ev-country-name">{{ $country['name'] }}</p>
                        <p class="ev-country-cities">{{ $country['cities'] }} Cities</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/evoory-theme.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/js/evoory-theme.js') }}" defer></script>
@endpush
