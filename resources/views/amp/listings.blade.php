@extends('amp.layout')

@section('title', ucfirst($gender) . ' Escorts in ' . ucfirst($cityData->name) . ' - Massage Republic')
@section('canonical', $canonicalUrl)
@section('description', 'Browse ' . $profiles->total() . ' ' . $gender . ' escorts in ' . $cityData->name . '. Find verified profiles with photos and reviews.')

@section('content')
    <!-- Breadcrumb -->
    <div style="margin-bottom: 15px; color: #999; font-size: 13px;">
        <a href="/amp">Home</a> &raquo; 
        <span style="color: #fff;">{{ ucfirst($gender) }} Escorts in {{ ucfirst($cityData->name) }}</span>
    </div>
    
    <!-- Page Title -->
    <h1 class="amp-page-title">
        {{ ucfirst($gender) }} Escorts in {{ ucfirst($cityData->name) }}
        @if($cityData->country)
            <span style="color: #999; font-size: 16px;">, {{ $cityData->country }}</span>
        @endif
    </h1>
    
    <!-- Stats -->
    <div class="amp-card" style="display: flex; justify-content: space-around; text-align: center;">
        <div>
            <div style="color: #f4b827; font-size: 24px; font-weight: bold;">{{ $profiles->total() }}</div>
            <div style="color: #999; font-size: 12px;">Total Profiles</div>
        </div>
        <div>
            <div style="color: #4CAF50; font-size: 24px; font-weight: bold;">{{ $profiles->where('is_verified', 1)->count() }}</div>
            <div style="color: #999; font-size: 12px;">Verified</div>
        </div>
        <div>
            <div style="color: #2196F3; font-size: 24px; font-weight: bold;">{{ $profiles->where('package_id', 21)->count() }}</div>
            <div style="color: #999; font-size: 12px;">Premium</div>
        </div>
    </div>
    
    <!-- Gender Filter -->
    <div style="display: flex; gap: 10px; margin: 20px 0;">
        <a href="/amp/female-escorts-in-{{ $cityData->slug }}" 
           class="amp-btn {{ $gender == 'female' ? '' : 'amp-btn-outline' }}" 
           style="flex: 1; text-align: center;">Female</a>
        <a href="/amp/male-escorts-in-{{ $cityData->slug }}" 
           class="amp-btn {{ $gender == 'male' ? '' : 'amp-btn-outline' }}" 
           style="flex: 1; text-align: center;">Male</a>
        <a href="/amp/shemale-escorts-in-{{ $cityData->slug }}" 
           class="amp-btn {{ $gender == 'shemale' ? '' : 'amp-btn-outline' }}" 
           style="flex: 1; text-align: center;">Trans</a>
    </div>
    
    <!-- Profile Listings -->
    <div class="amp-profile-list">
        @forelse($profiles as $profile)
            <a href="/amp/{{ $gender }}-escorts-in-{{ $cityData->slug }}/{{ $profile->id }}/{{ $profile->slug ?? Str::slug($profile->name) }}" 
               class="amp-profile-item" style="text-decoration: none;">
                
                <!-- Profile Image -->
                <div class="amp-profile-image">
                    @if($profile->main_image && $profile->main_image->image)
                        <amp-img 
                            src="{{ asset('storage/' . $profile->main_image->image) }}" 
                            width="120" 
                            height="150" 
                            layout="responsive"
                            alt="{{ $profile->name }}">
                        </amp-img>
                    @else
                        <amp-img 
                            src="https://via.placeholder.com/120x150/333/999?text=No+Photo" 
                            width="120" 
                            height="150" 
                            layout="responsive"
                            alt="{{ $profile->name }}">
                        </amp-img>
                    @endif
                </div>
                
                <!-- Profile Info -->
                <div class="amp-profile-info">
                    <!-- Badges -->
                    <div style="margin-bottom: 8px;">
                        @if($profile->package_id == 21)
                            <span class="amp-badge amp-badge-premium">PREMIUM</span>
                        @elseif($profile->package_id == 20)
                            <span class="amp-badge amp-badge-featured">FEATURED</span>
                        @endif
                        
                        @if($profile->is_verified)
                            <span class="amp-badge amp-badge-verified">VERIFIED</span>
                        @endif
                    </div>
                    
                    <!-- Name -->
                    <div class="amp-profile-name">{{ $profile->name }}</div>
                    
                    <!-- Details -->
                    <div class="amp-profile-details">
                        @if($profile->age)
                            {{ $profile->age }} years
                        @endif
                        @if($profile->height)
                            - {{ $profile->height }} cm
                        @endif
                    </div>
                    
                    @if($profile->about)
                        <div class="amp-profile-details" style="margin-top: 5px;">
                            {{ Str::limit(strip_tags($profile->about), 80) }}
                        </div>
                    @endif
                    
                    <!-- Price -->
                    @if($profile->incallprice || $profile->outcallprice)
                        <div class="amp-profile-price">
                            @if($profile->incallprice)
                                From {{ number_format($profile->incallprice, 0) }} {{ $profile->incallcurr ?? 'AED' }}
                            @elseif($profile->outcallprice)
                                From {{ number_format($profile->outcallprice, 0) }} {{ $profile->outcallcurr ?? 'AED' }}
                            @endif
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="amp-card amp-text-center">
                <p style="color: #999;">No {{ $gender }} escorts found in {{ $cityData->name }} yet.</p>
                <a href="/register" class="amp-btn" style="margin-top: 15px;">Be the first to advertise!</a>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($profiles->hasPages())
        <div class="amp-pagination">
            @if($profiles->onFirstPage())
                <span style="opacity: 0.5;">Prev</span>
            @else
                <a href="{{ $profiles->previousPageUrl() }}">Prev</a>
            @endif
            
            <span class="active">Page {{ $profiles->currentPage() }} of {{ $profiles->lastPage() }}</span>
            
            @if($profiles->hasMorePages())
                <a href="{{ $profiles->nextPageUrl() }}">Next</a>
            @else
                <span style="opacity: 0.5;">Next</span>
            @endif
        </div>
    @endif
    
    <!-- Alternative Domains Notice -->
    <div class="amp-notice amp-mt-20">
        <div class="amp-notice-title">Can't access the full site?</div>
        <p style="color: #ccc; margin: 0;">
            Try our alternative domains:
            <a href="https://ae.massagerepublic.com.co/{{ $gender }}-escorts-in-{{ $cityData->slug }}">ae.massagerepublic.com.co</a> | 
            <a href="https://pk.massagerepublic.com.co/{{ $gender }}-escorts-in-{{ $cityData->slug }}">pk.massagerepublic.com.co</a> | 
            <a href="https://escorts.ninja/{{ $gender }}-escorts-in-{{ $cityData->slug }}">escorts.ninja</a>
        </p>
    </div>
    
    <!-- View Full Site -->
    <div class="amp-text-center amp-mt-20">
        <a href="{{ $canonicalUrl }}" class="amp-btn amp-btn-outline">View Full Website</a>
    </div>
@endsection
