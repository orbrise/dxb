@extends('amp.layout')

@section('title', $profile->name . ' - ' . ucfirst($gender) . ' Escort in ' . ucfirst($cityData->name ?? 'Dubai'))
@section('canonical', $canonicalUrl)
@section('description', Str::limit(strip_tags($profile->about ?? $profile->name . ' escort in ' . ($cityData->name ?? 'Dubai')), 160))

@section('head')
<!-- Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Person",
    "name": "{{ $profile->name }}",
    "description": "{{ Str::limit(strip_tags($profile->about ?? ''), 200) }}",
    "url": "{{ $canonicalUrl }}"
}
</script>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div style="margin-bottom: 15px; color: #999; font-size: 13px;">
        <a href="/amp">Home</a> &raquo; 
        <a href="/amp/{{ $gender }}-escorts-in-{{ $cityData->slug ?? 'dubai' }}">{{ ucfirst($cityData->name ?? 'Dubai') }}</a> &raquo; 
        <span style="color: #fff;">{{ $profile->name }}</span>
    </div>
    
    <!-- Profile Header -->
    <div class="amp-card">
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <!-- Main Image -->
            <div style="flex: 0 0 200px;">
                @if($images->count() > 0 && $images->first()->image)
                <amp-img 
                    src="{{ asset('storage/' . $images->first()->image) }}" 
                    width="200" 
                    height="250" 
                    layout="responsive"
                    alt="{{ $profile->name }}">
                </amp-img>
                @else
                <amp-img 
                    src="https://via.placeholder.com/200x250/333/999?text=No+Photo" 
                    width="200" 
                    height="250" 
                    layout="responsive"
                    alt="{{ $profile->name }}">
                </amp-img>
                @endif
            </div>
            
            <!-- Profile Info -->
            <div style="flex: 1; min-width: 200px;">
                <!-- Badges -->
                <div style="margin-bottom: 10px;">
                    @if($profile->package_id == 21)
                    <span class="amp-badge amp-badge-premium">PREMIUM</span>
                    @elseif($profile->package_id == 20)
                    <span class="amp-badge amp-badge-featured">FEATURED</span>
                    @endif
                    
                    @if($profile->is_verified)
                    <span class="amp-badge amp-badge-verified">✓ VERIFIED PHOTOS</span>
                    @endif
                </div>
                
                <!-- Name -->
                <h1 style="color: #f4b827; font-size: 28px; margin: 0 0 10px 0;">{{ $profile->name }}</h1>
                
                <!-- Location -->
                <div style="color: #999; margin-bottom: 15px;">
                    📍 {{ ucfirst($cityData->name ?? 'Dubai') }}@if($cityData && $cityData->country), {{ $cityData->country }}@endif
                </div>
                
                <!-- Quick Stats -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                    @if($profile->age)
                    <div>
                        <span style="color: #999;">Age:</span>
                        <span style="color: #fff;">{{ $profile->age }} years</span>
                    </div>
                    @endif
                    
                    @if($profile->height)
                    <div>
                        <span style="color: #999;">Height:</span>
                        <span style="color: #fff;">{{ $profile->height }} cm</span>
                    </div>
                    @endif
                    
                    @if($profile->weight)
                    <div>
                        <span style="color: #999;">Weight:</span>
                        <span style="color: #fff;">{{ $profile->weight }} kg</span>
                    </div>
                    @endif
                    
                    @if($nationality)
                    <div>
                        <span style="color: #999;">Nationality:</span>
                        <span style="color: #fff;">{{ $nationality->nicename }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Prices -->
                <div style="margin-top: 20px; padding: 15px; background: #1a1a1a; border-radius: 8px;">
                    <div style="color: #f4b827; font-weight: bold; margin-bottom: 10px;">💰 Rates</div>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        @if($profile->incall_price)
                        <div>
                            <span style="color: #999;">Incall:</span>
                            <span style="color: #4CAF50; font-weight: bold;">{{ number_format($profile->incall_price, 0) }} {{ $profile->currency_code ?? 'AED' }}</span>
                        </div>
                        @endif
                        
                        @if($profile->outcall_price)
                        <div>
                            <span style="color: #999;">Outcall:</span>
                            <span style="color: #4CAF50; font-weight: bold;">{{ number_format($profile->outcall_price, 0) }} {{ $profile->currency_code ?? 'AED' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Photo Gallery -->
    @if($images->count() > 1)
    <h2 class="amp-page-title">Photos ({{ $images->count() }})</h2>
    <amp-carousel width="400" height="300" layout="responsive" type="slides" autoplay delay="3000" loop>
        @foreach($images as $image)
        @if($image->image)
        <amp-img 
            src="{{ asset('storage/' . $image->image) }}" 
            width="400" 
            height="300" 
            layout="responsive"
            alt="{{ $profile->name }} - Photo {{ $loop->iteration }}">
        </amp-img>
        @endif
        @endforeach
    </amp-carousel>
    @endif
    
    <!-- About -->
    @if($profile->about)
    <h2 class="amp-page-title">About {{ $profile->name }}</h2>
    <div class="amp-card">
        <div style="color: #ccc; line-height: 1.8;">
            {!! nl2br(e($profile->about)) !!}
        </div>
    </div>
    @endif
    
    <!-- Services -->
    @if($services->count() > 0)
    <h2 class="amp-page-title">Services Offered</h2>
    <div class="amp-card">
        <div class="amp-services">
            @foreach($services as $service)
            <span class="amp-service-tag">{{ $service->name }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Contact Info -->
    <h2 class="amp-page-title">Contact</h2>
    <div class="amp-card">
        @if($profile->phone)
        <div style="margin-bottom: 15px;">
            <span style="color: #999;">📞 Phone:</span>
            <a href="tel:{{ $profile->phone }}" style="color: #4CAF50; font-size: 18px; font-weight: bold; margin-left: 10px;">
                {{ $profile->phone }}
            </a>
        </div>
        @endif
        
        @if($profile->whatsapp)
        <div style="margin-bottom: 15px;">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $profile->whatsapp) }}" 
               class="amp-btn" 
               style="background: #25D366; display: inline-flex; align-items: center; gap: 10px;">
                <span>💬 WhatsApp</span>
            </a>
        </div>
        @endif
        
        <div class="amp-notice" style="margin-top: 20px; margin-bottom: 0;">
            <p style="color: #999; margin: 0; font-size: 12px;">
                ⚠️ For full contact options and messaging, please visit the <a href="{{ $canonicalUrl }}">full website</a>.
            </p>
        </div>
    </div>
    
    <!-- Reviews -->
    @if($reviews->count() > 0)
    <h2 class="amp-page-title">Reviews ({{ $reviews->count() }})</h2>
    @foreach($reviews as $review)
    <div class="amp-review">
        <div class="amp-review-author">
            {{ $review->name ?? 'Anonymous' }}
            <span class="amp-stars">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= ($review->rating ?? 5))★@else☆@endif
                @endfor
            </span>
        </div>
        <div class="amp-review-text">{{ $review->review }}</div>
        <div style="color: #666; font-size: 11px; margin-top: 5px;">
            {{ $review->created_at ? $review->created_at->diffForHumans() : '' }}
        </div>
    </div>
    @endforeach
    @endif
    
    <!-- Back to Listings -->
    <div style="display: flex; gap: 10px; margin-top: 30px;">
        <a href="/amp/{{ $gender }}-escorts-in-{{ $cityData->slug ?? 'dubai' }}" class="amp-btn amp-btn-outline" style="flex: 1; text-align: center;">
            ← Back to Listings
        </a>
        <a href="{{ $canonicalUrl }}" class="amp-btn" style="flex: 1; text-align: center;">
            View Full Profile
        </a>
    </div>
    
    <!-- Alternative Domains -->
    <div class="amp-notice amp-mt-20">
        <div class="amp-notice-title">Can't access the full site?</div>
        <p style="color: #ccc; margin: 0;">
            Try: 
            <a href="https://ae.massagerepublic.com.co{{ request()->getPathInfo() }}">ae.massagerepublic.com.co</a> | 
            <a href="https://pk.massagerepublic.com.co{{ request()->getPathInfo() }}">pk.massagerepublic.com.co</a>
        </p>
    </div>
@endsection
