@extends('amp.layout')

@section('title', 'Massage Republic - Escorts Directory')
@section('canonical', url('/'))
@section('description', 'Find escorts and adult services in your city. Browse our directory of verified escorts.')

@section('content')
    <!-- Hero Section -->
    <div class="amp-card amp-text-center" style="padding: 40px 20px;">
        <h1 class="amp-page-title" style="border: none;">Welcome to Massage Republic</h1>
        <p style="color: #ccc; font-size: 16px; margin-bottom: 20px;">
            Find verified escorts and adult services in your city
        </p>
        <p class="amp-text-muted">
            {{ number_format($totalProfiles) }} active profiles available
        </p>
    </div>
    
    <!-- Notice for blocked users -->
    <div class="amp-notice">
        <div class="amp-notice-title">Site Blocked?</div>
        <p style="color: #ccc; margin: 0;">
            If our main domain is blocked in your country, try these alternatives:
            <a href="https://ae.massagerepublic.com.co">ae.massagerepublic.com.co</a>,
            <a href="https://pk.massagerepublic.com.co">pk.massagerepublic.com.co</a>, or
            <a href="https://escorts.ninja">escorts.ninja</a>
        </p>
    </div>
    
    <!-- Browse by City -->
    <h2 class="amp-page-title" style="margin-top: 30px;">Browse by City</h2>
    
    <div class="amp-city-grid">
        @foreach($featuredCities as $city)
        <a href="/amp/female-escorts-in-{{ $city->slug }}" class="amp-city-item">
            <div style="font-size: 24px; margin-bottom: 5px;">
                @if($city->country == 'United Arab Emirates')🇦🇪
                @elseif($city->country == 'Pakistan')🇵🇰
                @elseif($city->country == 'India')🇮🇳
                @elseif($city->country == 'United Kingdom')🇬🇧
                @elseif($city->country == 'Thailand')🇹🇭
                @elseif($city->country == 'Philippines')🇵🇭
                @elseif($city->country == 'Singapore')🇸🇬
                @else🌍
                @endif
            </div>
            <div style="color: #fff;">{{ $city->name }}</div>
            @if($city->country)
            <div style="color: #999; font-size: 11px;">{{ $city->country }}</div>
            @endif
        </a>
        @endforeach
    </div>
    
    <!-- Browse by Gender -->
    <h2 class="amp-page-title" style="margin-top: 40px;">Browse by Category</h2>
    
    <div class="amp-row">
        <div class="amp-col amp-col-md-4">
            <a href="/amp/female-escorts-in-dubai" class="amp-card" style="display: block; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 10px;">👩</div>
                <div style="color: #C1F11D; font-weight: bold;">Female Escorts</div>
                <div style="color: #999; font-size: 12px;">Browse female profiles</div>
            </a>
        </div>
        <div class="amp-col amp-col-md-4">
            <a href="/amp/male-escorts-in-dubai" class="amp-card" style="display: block; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 10px;">👨</div>
                <div style="color: #C1F11D; font-weight: bold;">Male Escorts</div>
                <div style="color: #999; font-size: 12px;">Browse male profiles</div>
            </a>
        </div>
        <div class="amp-col amp-col-md-4">
            <a href="/amp/shemale-escorts-in-dubai" class="amp-card" style="display: block; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 10px;">⚧</div>
                <div style="color: #C1F11D; font-weight: bold;">Trans Escorts</div>
                <div style="color: #999; font-size: 12px;">Browse trans profiles</div>
            </a>
        </div>
    </div>
    
    <!-- CTA Section -->
    <div class="amp-card amp-text-center amp-mt-20" style="padding: 30px;">
        <h3 style="color: #C1F11D; margin-bottom: 15px;">Want to Advertise?</h3>
        <p style="color: #ccc; margin-bottom: 20px;">
            Create your profile and reach thousands of potential clients
        </p>
        <a href="/register" class="amp-btn">Register Now</a>
        <a href="/sign-in" class="amp-btn amp-btn-outline" style="margin-left: 10px;">Sign In</a>
    </div>
@endsection
