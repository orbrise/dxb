@push('css')
@if(isset($images) && $images->count() > 0)
    <link rel="preload" as="image" href="{{ webp_asset('userimages/'.$images->first()->user_id.'/'.$images->first()->profile_id.'/'.$images->first()->image) }}" fetchpriority="high">
@endif
<link rel="preload" href="{{ asset('assets/css/evoory-profile.css') }}?v={{ filemtime(public_path('assets/css/evoory-profile.css')) }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{ asset('assets/css/evoory-profile.css') }}?v={{ filemtime(public_path('assets/css/evoory-profile.css')) }}"></noscript>

{{-- Profile-page-specific styles, previously inlined as a 22k-line <style> block.
     Extracted to a static file so the HTML payload is small and the CSS gets its
     own long-lived edge cache entry. Loaded sync (not async) to match prior render
     behavior — these styles are not safe to defer without testing for FOUC. --}}
<link rel="stylesheet" href="{{ asset('assets/css/profile-details-inline.css') }}?v={{ @filemtime(public_path('assets/css/profile-details-inline.css')) ?: time() }}">
@endpush

<div class="profile-details-page">

  @php
    // Some imported profiles (MR scraper) stored the full international number
    // in users_profiles.phone with an empty country_code, while regular profiles
    // store cc + national digits separately. Normalize both into the same
    // display/link form so we never emit "+ +971..." or "tel:++971...".
    $rawCc = trim((string) ($user->country_code ?? ''));
    $rawPhone = trim((string) ($user->phone ?? ''));
    if ($rawCc === '' && str_starts_with($rawPhone, '+')) {
        $digits = preg_replace('/\D/', '', $rawPhone);
        $phoneDisplay = $digits !== '' ? '+' . $digits : '';
        $phoneE164 = $phoneDisplay;
        $phoneForLink = $digits;
    } else {
        $ccDigits = preg_replace('/\D/', '', $rawCc);
        $nat = preg_replace('/\D/', '', $rawPhone);
        $phoneDisplay = ($ccDigits || $nat) ? '+' . $ccDigits . ' ' . $nat : '';
        $phoneE164 = ($ccDigits || $nat) ? '+' . $ccDigits . $nat : '';
        $phoneForLink = $ccDigits . $nat;
    }

    // Safe city/country fallbacks. Some imported or archived profiles have a
    // null user OR a missing gcity/getcountry relation; raw `$user->gcity->name`
    // throws "Attempt to read property on null" the moment either is absent
    // (the `??` operator can't rescue a null receiver). Resolve once here so
    // every reference below survives orphaned data.
    $cityName = optional(optional($user)->gcity)->name ?? 'Dubai';
    $cityCountry = optional(optional($user)->gcity)->country ?? 'UAE';
    $citySlug = Str::slug($cityName);
    $nationality = optional(optional($user)->getcountry)->nationality ?? '';
    $userName = optional($user)->name ?? optional($profile)->name ?? '';
    $userCityField = optional($user)->city ?? $cityName;
  @endphp

  <div class="nav-bar navbar-top-nav" itemscope="" itemtype="https://schema.org/BreadcrumbList">
    <div class="container-fluid" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
      <a class="back-link btn btn-dark" href="/{{ $gender }}-escorts-in-{{ $citySlug }}" itemprop="item" title="Back">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        <span itemprop="name">{{ ucfirst($gender) }} Escorts in {{ $cityName }}</span>
      </a>

      <meta content="1" itemprop="position" />
      <div class="listing-title title hidden-xs">

        @livewire('favorite-profile', ['profileId' => $profile->id])

        <a href="/{{ $gender }}-escorts-in-{{ $citySlug }}/{{ $profile->slug }}">
          <h1 style="font-size: 18px; margin-top: 7px; font-weight: 500;">{{ ucfirst($profile->name)}} – {{ $nationality }} escort in {{ $cityName }}</h1>
        </a>
      </div>
      <a wire:click='nextescort' class="next btn btn-dark" href="javascript:void(0)">
        <span class="hidden-xs">Next Escort</span>
        <span class="visible-xs-inline">Next</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
  </div>

  <div class="listing-user-actions" navbar-affix="">
    <a class="pull-left btn btn-dark btn-lg" href="/{{ $gender }}-escorts-in-{{ $citySlug }}" itemprop="url" title="Back">
      <i class="fa fa-angle-left fa-fw"></i>All </a>
    <a wire:click='nextescort' class="pull-right btn btn-dark btn-lg" href="#">Next <i
        class="fa fa-angle-right fa-fw"></i>
    </a>
    <div class="list-group list-group-dark">
      <a class="list-group-item contact-phone1" href="javascript:void(0)" style="background-color: #131616 !important;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span>Phone</span>
      </a>
      <a class="list-group-item send-message1" href="javascript:void(0)" style="background-color: #131616 !important;">
        <img src="https://assets.massagerepublic.com.co/assets/newtheme/msg.svg" width="16" height="16" alt="Message">
        <span>Message</span>
      </a>
      @if(!empty($user->website))
      <a class="list-group-item contact-website track-event" 
        href="{{ $user->website }}" rel="external noopener nofollow" target="_blank">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        <span>Website</span>
      </a>
      @endif
      <a class="list-group-item ask-question1" href="javascript:void(0)">
        <img src="https://assets.massagerepublic.com.co/assets/newtheme/question.svg" width="16" height="16" alt="Message">
        <span>Ask Question</span>
      </a>
      <a class="list-group-item add-review1" href="javascript:void(0)">
        <img src="https://assets.massagerepublic.com.co/assets/newtheme/review.svg" width="16" height="16" alt="Message">
        <span>Add Review</span>
      </a>
    </div>
  </div>

  <div class="container-fluid">
    <div class="content-wrapper no-sidebar">
      <div id="content" style="margin-top:20px">
        <div class="listing-title title visible-xs">
          @livewire('favorite-profile', ['profileId' => $profile->id])
          <a href="javascript:void(0);">
            <h1>{{ ucfirst($profile->name)}} – {{ $nationality }} escort in {{ $cityName }}</h1>
          </a>
          <div class="clearfix"></div>
        </div>
        <article data-listing="">
          <div class="row">
            <div class="col-xs-12 col-sm-9 col-sm-push-3 col-md-9 col-lg-7 col-lg-push-5" id="listing-content">
              <div class="visible-xs pb-thumbnails">
                <div class="listing-photos-xs">
                  @foreach($images as $img)
                  <a class=" pb-photo-link" href="{{webp_asset("userimages/".$img->user_id."/".$img->profile_id."/".$img->image)}}">
                    <span class="img-wrapper listing">
                      @if($user->photoverify && $user->photoverify->status == 'approved')
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      @endif
                      <div class="image-wrapper">
                        <img alt="{{ $userName }} - escort in {{ $userCityField }}" class="img-responsive" data-original-height="1499" data-original-width="1000" data-thumb-url="{{webp_asset("userimages/".$img->user_id."/".$img->profile_id."/".$img->image)}}" height="327" loading="{{ $loop->first ? 'eager' : 'lazy' }}" fetchpriority="{{ $loop->first ? 'high' : 'auto' }}" decoding="async" src="{{webp_asset("userimages/".$img->user_id."/".$img->profile_id."/".$img->image)}}" width="238">
                      </div>
                    </span>
                  </a>
                  @endforeach
                 
                </div>
              </div>
              
              {{-- YouTube Video Display - Top of Content --}}
              @if(!empty($user->video))
              @php
                $videoUrl = $user->video;
                $videoId = '';
                $embedUrl = '';
                $isValidVideo = false;
                
                // Check if it's a Vimeo URL
                if (str_contains($videoUrl, 'vimeo.com')) {
                    // Extract Vimeo video ID: https://vimeo.com/VIDEO_ID or https://vimeo.com/VIDEO_ID?params
                    preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches);
                    $videoId = $matches[1] ?? '';
                    if ($videoId) {
                        $embedUrl = 'https://player.vimeo.com/video/' . $videoId;
                        $isValidVideo = true;
                    }
                }
                // Extract video ID from different YouTube URL formats
                elseif (str_contains($videoUrl, 'youtube.com/shorts/')) {
                    // YouTube Shorts: https://www.youtube.com/shorts/VIDEO_ID
                    preg_match('/shorts\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches);
                    $videoId = $matches[1] ?? '';
                    if ($videoId) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                        $isValidVideo = true;
                    }
                } elseif (str_contains($videoUrl, 'youtu.be/')) {
                    // Short URL: https://youtu.be/VIDEO_ID
                    $videoId = last(explode('/', parse_url($videoUrl, PHP_URL_PATH)));
                    if ($videoId) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                        $isValidVideo = true;
                    }
                } elseif (str_contains($videoUrl, 'youtube.com/watch')) {
                    // Regular URL: https://www.youtube.com/watch?v=VIDEO_ID
                    parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);
                    $videoId = $params['v'] ?? '';
                    if ($videoId) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                        $isValidVideo = true;
                    }
                }
                // Invalid video URL - don't show iframe
              @endphp
              @if($isValidVideo && $embedUrl)
              <div class="video-container" style="margin-bottom: 20px; max-width: 600px; margin-left: auto; margin-right: auto; background: transparent;">
                <div class="video-wrapper" style="position: relative; padding-bottom: 75%; height: 0; overflow: hidden; max-width: 100%; border-radius: 4px; background: transparent;">
                  <iframe 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 4px; background: transparent;" 
                    src="{{ $embedUrl }}" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                  </iframe>
                </div>
              </div>
              @endif
              @endif
              
              {{-- Mobile Tabs: About / Reviews / Questions --}}
              <div class="ev-mobile-tabs visible-xs">
                  <button class="ev-mobile-tab active" data-tab="about">About</button>
                  <button class="ev-mobile-tab" data-tab="reviews">Reviews</button>
                  <button class="ev-mobile-tab" data-tab="questions">Questions</button>
              </div>

              <div class="ev-mobile-tab-content" data-tab-content="about">
              <div class="description">
                <p>{!! nl2br($user->about) !!} </p>
              </div>
              @if(!empty($user->incall) or !empty($user->outcall))
              @php
                // Currency to USD exchange rates (approximate)
                $exchangeRates = [
                    'USD' => 1,
                    'AED' => 0.27,
                    'PKR' => 0.0036,
                    'EUR' => 1.08,
                    'GBP' => 1.27,
                    'INR' => 0.012,
                    'SAR' => 0.27,
                    'QAR' => 0.27,
                    'KWD' => 3.25,
                    'BHD' => 2.65,
                    'OMR' => 2.60,
                ];
                
                $incallUsd = null;
                $outcallUsd = null;
                
                if (!empty($user->incallprice) && !empty($user->incallcurr)) {
                    $rate = $exchangeRates[strtoupper($user->incallcurr)] ?? 0.27;
                    $incallUsd = round($user->incallprice * $rate);
                }
                
                if (!empty($user->outcallprice) && !empty($user->outcallcurr)) {
                    $rate = $exchangeRates[strtoupper($user->outcallcurr)] ?? 0.27;
                    $outcallUsd = round($user->outcallprice * $rate);
                }
              @endphp
              <div class="margin-bottom" id="listing-price">
                @if(!empty($user->incall))
                <div class="pull-left" data-placement="bottom" data-toggle="tooltip" title="You can come to my place">
                  <div class="pull-left text-muted">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #0D1011; border-radius: 50%; margin-right: 5px;padding: 7px;">
                      <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.833333 0C0.61232 0 0.400358 0.0877973 0.244078 0.244078C0.0877973 0.400358 0 0.61232 0 0.833333C0 4.59057 1.49256 8.19391 4.14932 10.8507C6.80609 13.5074 10.4094 15 14.1667 15C14.3877 15 14.5996 14.9122 14.7559 14.7559C14.9122 14.5996 15 14.3877 15 14.1667V11.25C15 11.029 14.9122 10.817 14.7559 10.6607C14.5996 10.5045 14.3877 10.4167 14.1667 10.4167C13.125 10.4167 12.125 10.25 11.1917 9.94167C11.0451 9.895 10.8887 9.88895 10.739 9.92417C10.5893 9.95939 10.452 10.0346 10.3417 10.1417L8.50833 11.975C6.14283 10.7716 4.22005 8.84884 3.01667 6.48333L4.85 4.64167C4.96037 4.53695 5.0386 4.40293 5.0755 4.25533C5.1124 4.10773 5.10644 3.95267 5.05833 3.80833C4.74277 2.84824 4.58242 1.84395 4.58333 0.833333C4.58333 0.61232 4.49554 0.400358 4.33926 0.244078C4.18298 0.0877973 3.97101 0 3.75 0H0.833333ZM13.3333 6.66667V5.41667H10.4167L15 0.833333L14.1667 0L9.58333 4.58333V1.66667H8.33333V6.66667H13.3333Z" fill="white"/>
</svg>

                    </span>
                  </div>
                  <div class="pull-left margin-right">
                    <div class="listing-price-label">Incalls per hour from</div>{{ number_format($user->incallprice) }}
                    {{$user->incallcurr}} @if($incallUsd && strtoupper($user->incallcurr) != 'USD')<span class='usd-price text-muted'>(US${{ number_format($incallUsd) }})</span>@endif
                  </div>
                </div>
                @endif
                @if(!empty($user->outcall))
                <div class="pull-left" data-placement="bottom" data-toggle="tooltip" title="I can come to your place">
                  <div class="pull-left text-muted">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #0D1011; border-radius: 50%; margin-right: 5px;padding: 7px;">
                      <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.833333 0C0.61232 0 0.400358 0.0877973 0.244078 0.244078C0.0877973 0.400358 0 0.61232 0 0.833333C0 4.59057 1.49256 8.19391 4.14932 10.8507C6.80609 13.5074 10.4094 15 14.1667 15C14.3877 15 14.5996 14.9122 14.7559 14.7559C14.9122 14.5996 15 14.3877 15 14.1667V11.25C15 11.029 14.9122 10.817 14.7559 10.6607C14.5996 10.5045 14.3877 10.4167 14.1667 10.4167C13.125 10.4167 12.125 10.25 11.1917 9.94167C11.0451 9.895 10.8887 9.88895 10.739 9.92417C10.5893 9.95939 10.452 10.0346 10.3417 10.1417L8.50833 11.975C6.14283 10.7716 4.22005 8.84884 3.01667 6.48333L4.85 4.64167C4.96037 4.53695 5.0386 4.40293 5.0755 4.25533C5.1124 4.10773 5.10644 3.95267 5.05833 3.80833C4.74277 2.84824 4.58242 1.84395 4.58333 0.833333C4.58333 0.61232 4.49554 0.400358 4.33926 0.244078C4.18298 0.0877973 3.97101 0 3.75 0H0.833333ZM10 0V1.25H12.9167L8.33333 5.83333L9.16667 6.66667L13.75 2.08333V5H15V0H10Z" fill="white"/>
</svg>

                    </span>
                  </div>
                  <div class="pull-left margin-right">
                    <div class="listing-price-label">Outcalls per hour from</div>{{ number_format($user->outcallprice) }}
                    {{$user->outcallcurr}} @if($outcallUsd && strtoupper($user->outcallcurr) != 'USD')<span class='usd-price text-muted'>(US${{ number_format($outcallUsd) }})</span>@endif
                  </div>
                </div>
                @endif
                <div class="clearfix"></div>
              </div>
              @endif
              {{-- Details table styles moved to evoory-profile.css --}}
              <div class="ev-details-card">
              <table class="profile-details-table">
                <tr>
                  <td class="label-cell">Orientation</td>
                  <td class="value-cell">{{$user->ori->name ?? "none"}}</td>
                  <td class="label-cell">Height</td>
                  <td class="value-cell">
                    @if($user->height)
                      @php
                        $totalInches = $user->height / 2.54;
                        $feet = floor($totalInches / 12);
                        $inches = round($totalInches % 12);
                      @endphp
                      {{$user->height}}cm / {{$feet}}'{{$inches}}"
                    @else
                      none
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="label-cell">Ethnicity</td>
                  <td class="value-cell">{{$user->ethi->name ?? 'none'}}</td>
                  <td class="label-cell">Bust</td>
                  <td class="value-cell">{{$user->gbust->name ?? 'none'}}</td>
                </tr>
                <tr>
                  <td class="label-cell">Age</td>
                  <td class="value-cell">{{$user->age ?? 'none'}}</td>
                  <td class="label-cell">Shaved</td>
                  <td class="value-cell">{{$user->shaved ? ucfirst($user->shaved) : 'none'}}</td>
                </tr>
                <tr>
                  <td class="label-cell">Smokes?</td>
                  <td class="value-cell">{{($user->smoke == 1)? "Yes":"No"}}</td>
                  <td class="label-cell">Hair color</td>
                  <td class="value-cell">{{$user->ghair->name ?? 'none'}}</td>
                </tr>
                <tr>
                  <td class="label-cell">Nationality</td>
                  <td class="value-cell">{{$user->gnat->nicename ?? ($user->gnat->name ?? 'none')}}</td>
                  <td class="label-cell">Gender</td>
                  <td class="value-cell">{{$user->ggender->name ?? 'none'}}</td>
                </tr>
                <tr>
                  <td class="label-cell">City</td>
                  <td class="value-cell" colspan="3">
                    <a href="/{{ $gender }}-escorts-in-{{ $citySlug }}">{{ $cityName }}@if($cityCountry) - {{ $cityCountry }}@endif</a>
                  </td>
                </tr>
              </table>
              <div class="ev-languages-section">
                <h3 class="ev-section-label">Languages</h3>
                <div class="ev-language-pills">
                    @foreach($user->languages as $lang)
                    <span class="ev-lang-pill">{{$lang->getlangname->name}}</span>
                    @endforeach
                </div>
              </div>
              </div>{{-- /ev-details-card --}}

              {{-- "Is This Your Profile?" claim card.
                   Hidden once the viewer is signed in and already owns the
                   profile so a logged-in profile owner doesn't see their own
                   claim CTA. The actual three-step modal (phone → code →
                   success) lives further down inside #profile-claim-modal. --}}
              @if(!Auth::check() || (Auth::id() !== ($user->id ?? null) && Auth::user()?->type != 1))
              <div class="ev-claim-card" id="ev-claim-card">
                  <div class="ev-claim-card-inner">
                      <div class="ev-claim-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                      </div>
                      <div class="ev-claim-text">
                          <h4>Is This Your Profile?</h4>
                          <p>If this listing belongs to you but was posted by someone else, you can verify your ownership and claim it. We will transfer it securely to your original account.</p>
                          <button type="button" class="ev-claim-cta" onclick="document.getElementById('profile-claim-modal').classList.add('is-open');document.body.style.overflow='hidden';">Claim Now</button>
                      </div>
                  </div>
              </div>

              {{-- Claim modal — three stacked panels controlled by the
                   data-step attribute set in JS: phone → code → success. --}}
              <div class="ev-claim-modal" id="profile-claim-modal" data-step="phone" data-profile-id="{{ $user->id ?? '' }}" role="dialog" aria-modal="true" aria-labelledby="ev-claim-title" hidden>
                  <div class="ev-claim-modal-overlay" onclick="window.profileClaimClose && window.profileClaimClose()"></div>
                  <div class="ev-claim-modal-panel">
                      <button type="button" class="ev-claim-close" aria-label="Close" onclick="window.profileClaimClose && window.profileClaimClose()">&times;</button>

                      {{-- Step 1: phone + email + channel --}}
                      <div class="ev-claim-step" data-step-panel="phone">
                          <div class="ev-claim-modal-head">
                              <h2 id="ev-claim-title">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                  Claim Your Profile
                              </h2>
                              <p class="ev-claim-sub">Verify ownership &amp; request transfer</p>
                          </div>
                          <p class="ev-claim-blurb">Verify that this profile belongs to you, and we'll securely help transfer it to your account after verification is complete.</p>

                          <label class="ev-claim-label" for="ev-claim-phone-number">Mobile Number</label>
                          @php
                              // Default selection: the profile's own city country if we know it,
                              // otherwise UAE. Falls back gracefully if either lookup is empty.
                              $claimDefaultIso = strtoupper(optional(optional($user)->getcountry)->iso ?: 'AE');
                              $claimDefaultCountry = ($countries ?? collect())->firstWhere('iso', $claimDefaultIso)
                                  ?? ($countries ?? collect())->firstWhere('iso', 'AE')
                                  ?? ($countries ?? collect())->first();
                          @endphp
                          <div class="ev-claim-phone-group">
                              <div class="ev-claim-cc" id="ev-claim-cc">
                                  <button type="button" class="ev-claim-cc-trigger" data-cc-trigger
                                          aria-haspopup="listbox" aria-expanded="false">
                                      <img class="ev-claim-cc-flag" data-cc-flag
                                           src="https://flagcdn.com/w40/{{ strtolower($claimDefaultCountry->iso ?? 'ae') }}.png"
                                           alt="" width="22" height="16">
                                      <span class="ev-claim-cc-code" data-cc-code>+{{ $claimDefaultCountry->phonecode ?? '971' }}</span>
                                      <span class="ev-claim-cc-caret" aria-hidden="true">▾</span>
                                      <input type="hidden" id="ev-claim-dial" value="{{ $claimDefaultCountry->phonecode ?? '971' }}">
                                      <input type="hidden" id="ev-claim-iso" value="{{ strtoupper($claimDefaultCountry->iso ?? 'AE') }}">
                                  </button>
                                  <div class="ev-claim-cc-panel" data-cc-panel hidden role="listbox">
                                      <input type="text" class="ev-claim-cc-search" placeholder="Search country…" data-cc-search>
                                      <ul class="ev-claim-cc-list" data-cc-list>
                                          @foreach($countries as $c)
                                              @if(!empty($c->phonecode))
                                              <li role="option"
                                                  data-cc-iso="{{ strtoupper($c->iso) }}"
                                                  data-cc-dial="{{ $c->phonecode }}"
                                                  data-cc-name="{{ strtolower($c->nicename) }}">
                                                  <img src="https://flagcdn.com/w40/{{ strtolower($c->iso) }}.png" alt="" width="22" height="16">
                                                  <span class="ev-claim-cc-list-name">{{ $c->nicename }}</span>
                                                  <span class="ev-claim-cc-list-dial">+{{ $c->phonecode }}</span>
                                              </li>
                                              @endif
                                          @endforeach
                                      </ul>
                                  </div>
                              </div>
                              <input id="ev-claim-phone-number" name="phone_number" type="tel"
                                     class="ev-claim-input ev-claim-phone-input"
                                     placeholder="Enter your mobile number"
                                     autocomplete="tel-national" inputmode="numeric">
                          </div>

                          <label class="ev-claim-label" for="ev-claim-email">Email Address</label>
                          <input id="ev-claim-email" name="email" type="email" class="ev-claim-input" placeholder="Enter your email" autocomplete="email">

                          <p class="ev-claim-label" style="margin-top:18px;">Choose how you'd like to receive your verification code.</p>
                          <div class="ev-claim-channels">
                              <label class="ev-claim-channel">
                                  <input type="radio" name="claim-channel" value="whatsapp" checked>
                                  <span class="ev-claim-channel-icon" aria-hidden="true">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#25D366"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 0 1 8.413 3.488 11.82 11.82 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.711.306 1.265.489 1.697.625.713.227 1.362.195 1.875.118.572-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/></svg>
                                  </span>
                                  <span>WhatsApp</span>
                              </label>
                              <label class="ev-claim-channel">
                                  <input type="radio" name="claim-channel" value="sms">
                                  <span class="ev-claim-channel-icon" aria-hidden="true">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                  </span>
                                  <span>SMS</span>
                              </label>
                          </div>

                          <div class="ev-claim-info">
                              <strong>Why claim this profile?</strong>
                              <ul>
                                  <li>Protect your identity on evoory.</li>
                                  <li>Recover your original listing securely.</li>
                                  <li>Prevent duplicate or fake profiles using your imagery.</li>
                                  <li>Manage reviews and updates directly from your own dashboard.</li>
                              </ul>
                          </div>

                          <p class="ev-claim-error" data-claim-error hidden></p>
                          <button type="button" class="ev-claim-primary" data-claim-action="send-otp">Continue <span aria-hidden="true">›</span></button>
                      </div>

                      {{-- Step 2: OTP entry --}}
                      <div class="ev-claim-step" data-step-panel="code" hidden>
                          <div class="ev-claim-modal-head">
                              <h2>
                                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                                  Enter Security Code
                              </h2>
                              <p class="ev-claim-sub">Verification code sent to phone</p>
                          </div>
                          <p class="ev-claim-blurb">We've dispatched a secure code to <strong data-claim-phone-mask>your phone</strong>.</p>
                          <div class="ev-claim-otp" data-claim-otp>
                              <input type="text" inputmode="numeric" maxlength="1" data-otp-cell>
                              <input type="text" inputmode="numeric" maxlength="1" data-otp-cell>
                              <input type="text" inputmode="numeric" maxlength="1" data-otp-cell>
                              <input type="text" inputmode="numeric" maxlength="1" data-otp-cell>
                              <input type="text" inputmode="numeric" maxlength="1" data-otp-cell>
                              <input type="text" inputmode="numeric" maxlength="1" data-otp-cell>
                          </div>
                          <div class="ev-claim-otp-actions">
                              <button type="button" class="ev-claim-link" data-claim-action="back-to-phone">‹ Change Number</button>
                              <button type="button" class="ev-claim-link ev-claim-link--accent" data-claim-action="resend">RESEND CODE</button>
                          </div>
                          <p class="ev-claim-error" data-claim-error hidden></p>
                          <button type="button" class="ev-claim-primary" data-claim-action="verify-otp">Verify OTP <span aria-hidden="true">›</span></button>
                      </div>

                      {{-- Step 3: success --}}
                      <div class="ev-claim-step" data-step-panel="success" hidden>
                          <div class="ev-claim-success-icon">
                              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="9 12 12 15 17 9"></polyline></svg>
                          </div>
                          <h2 class="ev-claim-success-title">Verification Successful</h2>
                          <p class="ev-claim-blurb">Your profile ownership verification has been logged successfully. The profile is now linked to your account, and we've sent your login credentials to the email you provided.</p>
                          <a href="{{ url('my-account') }}" class="ev-claim-primary ev-claim-primary--center">Go To My Dashboard <span aria-hidden="true">›</span></a>
                      </div>
                  </div>
              </div>

              <style>
                  /* Claim card (shown inside About tab content) */
                  .ev-claim-card {
                      display: none !important;
                      margin: 16px 0 0;
                      padding: 18px 18px;
                      background: #15191B;
                      border: 1px solid #23292B;
                      border-radius: 12px;
                  }
                  .ev-claim-card-inner { display: flex; gap: 14px; align-items: flex-start; }
                  .ev-claim-icon {
                      flex: 0 0 38px; width: 38px; height: 38px; border-radius: 50%;
                      background: rgba(193, 241, 29, 0.12); display: flex; align-items: center; justify-content: center;
                  }
                  .ev-claim-text h4 { margin: 0 0 6px; color: #fff; font-size: 16px; font-weight: 600; }
                  .ev-claim-text p { margin: 0 0 12px; color: #aab0b4; font-size: 13.5px; line-height: 1.55; }
                  .ev-claim-cta {
                      background: #C1F11D; color: #000; border: none; border-radius: 24px;
                      padding: 7px 22px; font-weight: 600; font-size: 14px; cursor: pointer;
                      transition: background 0.15s ease;
                  }
                  .ev-claim-cta:hover { background: #d4f84d; }

                  /* Modal */
                  .ev-claim-modal { position: fixed; inset: 0; z-index: 99999; display: none; align-items: flex-start; justify-content: center; padding: 4vh 16px; }
                  .ev-claim-modal.is-open { display: flex; }
                  .ev-claim-modal[hidden] { display: none !important; }
                  .ev-claim-modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.78); }
                  .ev-claim-modal-panel {
                      position: relative; width: 100%; max-width: 520px;
                      background: #0e1214; color: #fff; border: 1px solid #23292B;
                      border-radius: 14px; padding: 28px 26px;
                      box-shadow: 0 24px 60px rgba(0,0,0,0.55);
                      max-height: 92vh; overflow-y: auto;
                  }
                  .ev-claim-close {
                      position: absolute; top: 14px; right: 14px;
                      width: 32px; height: 32px; border-radius: 50%;
                      background: #1a1f22; border: 1px solid #23292B; color: #fff;
                      font-size: 18px; line-height: 1; cursor: pointer;
                  }
                  .ev-claim-close:hover { background: #23292b; }
                  .ev-claim-modal-head { padding-right: 36px; }
                  .ev-claim-modal-head h2 { margin: 0 0 4px; font-size: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
                  .ev-claim-sub { margin: 0 0 18px; color: #8b9298; font-size: 13px; }
                  .ev-claim-blurb { margin: 0 0 18px; color: #cfd3d6; font-size: 14px; line-height: 1.55; }
                  .ev-claim-label { display: block; margin: 14px 0 6px; color: #cfd3d6; font-size: 13px; }
                  .ev-claim-input {
                      width: 100%; padding: 12px 14px; background: #0a0d0f;
                      border: 1px solid #23292B; border-radius: 8px; color: #fff; font-size: 14px;
                  }
                  .ev-claim-input:focus { outline: none; border-color: #C1F11D; }
                  /* Country-code + phone composite field */
                  .ev-claim-phone-group {
                      display: flex; gap: 8px; align-items: stretch; position: relative;
                  }
                  .ev-claim-cc { position: relative; flex: 0 0 auto; }
                  .ev-claim-cc-trigger {
                      display: inline-flex; align-items: center; gap: 8px;
                      height: 100%; min-height: 44px; padding: 0 10px;
                      background: #0a0d0f; border: 1px solid #23292B;
                      border-radius: 8px; color: #fff; cursor: pointer;
                      font: inherit; font-size: 14px;
                  }
                  .ev-claim-cc-trigger:hover { border-color: #3a4147; }
                  .ev-claim-cc-trigger[aria-expanded="true"] { border-color: #C1F11D; }
                  .ev-claim-cc-flag {
                      display: block; width: 22px; height: 16px; border-radius: 2px;
                      object-fit: cover; flex-shrink: 0;
                  }
                  .ev-claim-cc-code { font-weight: 500; }
                  .ev-claim-cc-caret { color: #8b9298; font-size: 11px; }
                  .ev-claim-phone-input { flex: 1; }
                  .ev-claim-cc-panel {
                      position: absolute; top: calc(100% + 6px); left: 0;
                      width: 320px; max-width: calc(100vw - 32px);
                      background: #0a0d0f; border: 1px solid #23292B;
                      border-radius: 10px; box-shadow: 0 12px 32px rgba(0,0,0,0.5);
                      z-index: 10; padding: 8px;
                  }
                  .ev-claim-cc-search {
                      width: 100%; padding: 8px 10px;
                      background: #0e1214; border: 1px solid #23292B;
                      border-radius: 6px; color: #fff; font-size: 13px;
                      margin-bottom: 6px;
                  }
                  .ev-claim-cc-search:focus { outline: none; border-color: #C1F11D; }
                  .ev-claim-cc-list {
                      list-style: none; margin: 0; padding: 0;
                      max-height: 240px; overflow-y: auto;
                  }
                  .ev-claim-cc-list li {
                      display: flex; align-items: center; gap: 10px;
                      padding: 8px 8px; border-radius: 6px; cursor: pointer;
                      color: #cfd3d6; font-size: 13.5px;
                  }
                  .ev-claim-cc-list li:hover,
                  .ev-claim-cc-list li[aria-selected="true"] { background: #15191B; color: #fff; }
                  .ev-claim-cc-list li.is-hidden { display: none; }
                  .ev-claim-cc-list-name { flex: 1; }
                  .ev-claim-cc-list-dial { color: #8b9298; font-variant-numeric: tabular-nums; }

                  /* Channel chips: content-width pills, not full-row.
                     Each chip = icon + label + radio dot, snug padding so the
                     row hugs the controls rather than stretching across the
                     modal body. Match the target reference. */
                  .ev-claim-channels {
                      display: flex; gap: 12px; flex-wrap: wrap;
                      margin: 6px 0 18px;
                  }
                  .ev-claim-channel {
                      display: inline-flex; align-items: center; gap: 10px;
                      padding: 10px 16px; background: #0a0d0f;
                      border: 1px solid #23292B; border-radius: 10px;
                      cursor: pointer; font-size: 14px; line-height: 1;
                      transition: border-color 0.15s ease, background 0.15s ease;
                  }
                  .ev-claim-channel:hover { border-color: #3a4147; }
                  .ev-claim-channel:has(input:checked) {
                      border-color: #C1F11D; background: rgba(193, 241, 29, 0.06);
                      color: #C1F11D;
                  }
                  .ev-claim-channel input { accent-color: #C1F11D; margin: 0; }
                  .ev-claim-channel-icon { display: inline-flex; }
                  .ev-claim-info {
                      margin: 4px 0 18px; padding: 14px 16px;
                      background: rgba(193, 241, 29, 0.05);
                      border: 1px solid rgba(193, 241, 29, 0.25);
                      border-radius: 10px; font-size: 13px; color: #cfd3d6;
                  }
                  .ev-claim-info strong { display: block; color: #C1F11D; margin-bottom: 6px; }
                  .ev-claim-info ul { margin: 0; padding-left: 18px; line-height: 1.6; }
                  .ev-claim-info li { margin-bottom: 2px; }
                  .ev-claim-error {
                      margin: 0 0 12px; padding: 10px 12px;
                      background: rgba(220, 53, 69, 0.12); border: 1px solid rgba(220, 53, 69, 0.4);
                      border-radius: 8px; color: #ff8c95; font-size: 13px;
                  }
                  .ev-claim-primary {
                      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
                      width: auto; min-width: 140px; padding: 10px 24px;
                      background: #C1F11D; color: #000; border: none; border-radius: 24px;
                      font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none;
                  }
                  .ev-claim-primary:hover { background: #d4f84d; }
                  .ev-claim-primary[disabled] { opacity: 0.55; cursor: progress; }
                  .ev-claim-primary--center { display: flex; margin: 8px auto 0; }
                  .ev-claim-otp { display: flex; gap: 8px; justify-content: center; margin: 10px 0 6px; }
                  .ev-claim-otp input {
                      width: 44px; height: 50px; text-align: center; font-size: 18px;
                      background: #0a0d0f; border: 1px solid #23292B; border-radius: 8px; color: #fff;
                  }
                  .ev-claim-otp input:focus { outline: none; border-color: #C1F11D; }
                  .ev-claim-otp-actions { display: flex; justify-content: space-between; padding: 12px 0 18px; border-bottom: 1px solid #1f262f; margin-bottom: 18px; }
                  .ev-claim-link { background: none; border: none; color: #cfd3d6; font-size: 13px; cursor: pointer; padding: 0; }
                  .ev-claim-link--accent { color: #C1F11D; font-weight: 600; letter-spacing: 0.05em; }
                  .ev-claim-success-icon { display: flex; justify-content: center; margin: 8px 0 14px; }
                  .ev-claim-success-title { margin: 0 0 10px; text-align: center; font-size: 20px; font-weight: 600; }
              </style>

              <script>
                  (function () {
                      var modal = document.getElementById('profile-claim-modal');
                      if (!modal || modal.__bound) return;
                      modal.__bound = true;

                      var profileId = modal.getAttribute('data-profile-id');
                      var csrf = document.querySelector('meta[name="csrf-token"]')
                          ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                          : @json(csrf_token());

                      function panels() { return modal.querySelectorAll('[data-step-panel]'); }
                      function setStep(name) {
                          modal.setAttribute('data-step', name);
                          panels().forEach(function (el) {
                              if (el.getAttribute('data-step-panel') === name) el.removeAttribute('hidden');
                              else el.setAttribute('hidden', '');
                          });
                          clearError();
                      }
                      function clearError() {
                          modal.querySelectorAll('[data-claim-error]').forEach(function (el) {
                              el.textContent = '';
                              el.setAttribute('hidden', '');
                          });
                      }
                      function showError(msg) {
                          var panel = modal.querySelector('[data-step-panel="' + modal.getAttribute('data-step') + '"]');
                          if (!panel) return;
                          var err = panel.querySelector('[data-claim-error]');
                          if (!err) return;
                          err.textContent = msg;
                          err.removeAttribute('hidden');
                      }
                      function getVal(sel) {
                          var el = modal.querySelector(sel);
                          return el ? (el.value || '').trim() : '';
                      }
                      function getFullPhone() {
                          // Concatenate selected dial code + the local-number
                          // input. Strips everything non-numeric from both so
                          // we always send E.164 (digits-only after the
                          // leading +). Server side normalises identically.
                          var dial = getVal('#ev-claim-dial').replace(/\D/g, '');
                          var local = getVal('#ev-claim-phone-number').replace(/\D/g, '');
                          if (!dial || !local) return '';
                          return '+' + dial + local;
                      }
                      function getChannel() {
                          var checked = modal.querySelector('input[name="claim-channel"]:checked');
                          return checked ? checked.value : 'whatsapp';
                      }
                      function getOtp() {
                          var out = '';
                          modal.querySelectorAll('[data-otp-cell]').forEach(function (el) {
                              out += (el.value || '').replace(/\D/g, '').slice(0, 1);
                          });
                          return out;
                      }
                      function maskPhone(phone) {
                          var digits = (phone || '').replace(/\D/g, '');
                          if (digits.length < 4) return digits;
                          var prefix = digits.length > 4 ? digits.slice(0, digits.length - 4) : '';
                          return (prefix ? '+' + prefix : '') + ' ' + '*'.repeat(Math.max(0, digits.length - 4)) + digits.slice(-4);
                      }
                      function busy(button, on) {
                          if (!button) return;
                          if (on) {
                              button.dataset.originalText = button.dataset.originalText || button.innerHTML;
                              button.setAttribute('disabled', 'disabled');
                              button.innerHTML = 'Please wait…';
                          } else {
                              button.removeAttribute('disabled');
                              if (button.dataset.originalText) button.innerHTML = button.dataset.originalText;
                          }
                      }

                      async function api(path, payload) {
                          var res = await fetch(path, {
                              method: 'POST',
                              headers: {
                                  'Content-Type': 'application/json',
                                  'Accept': 'application/json',
                                  'X-CSRF-TOKEN': csrf,
                                  'X-Requested-With': 'XMLHttpRequest',
                              },
                              body: JSON.stringify(payload),
                          });
                          var data = {};
                          try { data = await res.json(); } catch (e) { data = {}; }
                          return { ok: res.ok && data.ok, status: res.status, data: data };
                      }

                      async function sendOtp(btn) {
                          var phone = getFullPhone();
                          var email = getVal('#ev-claim-email');
                          if (!phone || phone.replace(/\D/g, '').length < 7) {
                              showError('Please enter a valid phone number.');
                              return;
                          }
                          if (!email || email.indexOf('@') === -1) {
                              showError('Please enter a valid email address.');
                              return;
                          }
                          busy(btn, true);
                          var r = await api('/profile/' + profileId + '/claim/send-otp', {
                              phone: phone, email: email, channel: getChannel(),
                          });
                          busy(btn, false);
                          if (!r.ok) { showError((r.data && r.data.message) || 'Could not send the code.'); return; }
                          var mask = modal.querySelector('[data-claim-phone-mask]');
                          if (mask) mask.textContent = maskPhone(phone);
                          setStep('code');
                          setTimeout(function () {
                              var first = modal.querySelector('[data-otp-cell]');
                              if (first) first.focus();
                          }, 40);
                      }

                      async function verifyOtp(btn) {
                          var code = getOtp();
                          if (code.length < 4) { showError('Please enter the full code.'); return; }
                          busy(btn, true);
                          var r = await api('/profile/' + profileId + '/claim/verify-otp', {
                              phone: getFullPhone(),
                              email: getVal('#ev-claim-email'),
                              code: code,
                          });
                          busy(btn, false);
                          if (!r.ok) {
                              showError((r.data && r.data.message) || 'Verification failed.');
                              return;
                          }
                          setStep('success');
                      }

                      // Country-code dropdown wiring. Self-contained — no
                      // external lib. Open/close, type-to-filter, click to
                      // select, escape to close. Updates the visible flag +
                      // dial-code and the hidden inputs the OTP send reads.
                      (function () {
                          var trigger = modal.querySelector('[data-cc-trigger]');
                          var panel = modal.querySelector('[data-cc-panel]');
                          var search = modal.querySelector('[data-cc-search]');
                          var list = modal.querySelector('[data-cc-list]');
                          var flagEl = modal.querySelector('[data-cc-flag]');
                          var codeEl = modal.querySelector('[data-cc-code]');
                          var dialInput = modal.querySelector('#ev-claim-dial');
                          var isoInput = modal.querySelector('#ev-claim-iso');
                          if (!trigger || !panel || !list) return;

                          function open() {
                              panel.removeAttribute('hidden');
                              trigger.setAttribute('aria-expanded', 'true');
                              if (search) { search.value = ''; filter(''); setTimeout(function () { search.focus(); }, 10); }
                          }
                          function close() {
                              panel.setAttribute('hidden', '');
                              trigger.setAttribute('aria-expanded', 'false');
                          }
                          function filter(term) {
                              term = (term || '').toLowerCase().trim();
                              Array.prototype.forEach.call(list.children, function (li) {
                                  var name = li.getAttribute('data-cc-name') || '';
                                  var dial = li.getAttribute('data-cc-dial') || '';
                                  var match = !term || name.indexOf(term) !== -1 || ('+' + dial).indexOf(term) !== -1 || dial.indexOf(term) !== -1;
                                  li.classList.toggle('is-hidden', !match);
                              });
                          }
                          trigger.addEventListener('click', function (e) {
                              e.preventDefault();
                              if (panel.hasAttribute('hidden')) open(); else close();
                          });
                          search && search.addEventListener('input', function () { filter(search.value); });
                          search && search.addEventListener('keydown', function (e) {
                              if (e.key === 'Escape') { close(); trigger.focus(); }
                          });
                          list.addEventListener('click', function (e) {
                              var li = e.target.closest('li');
                              if (!li || li.classList.contains('is-hidden')) return;
                              var iso = li.getAttribute('data-cc-iso');
                              var dial = li.getAttribute('data-cc-dial');
                              if (flagEl) flagEl.src = 'https://flagcdn.com/w40/' + (iso || '').toLowerCase() + '.png';
                              if (codeEl) codeEl.textContent = '+' + dial;
                              if (dialInput) dialInput.value = dial;
                              if (isoInput) isoInput.value = iso;
                              close();
                              var phoneInput = modal.querySelector('#ev-claim-phone-number');
                              if (phoneInput) phoneInput.focus();
                          });
                          document.addEventListener('click', function (e) {
                              if (!panel.contains(e.target) && e.target !== trigger && !trigger.contains(e.target)) close();
                          });
                      })();

                      modal.addEventListener('click', function (e) {
                          var btn = e.target.closest('[data-claim-action]');
                          if (!btn) return;
                          e.preventDefault();
                          var action = btn.getAttribute('data-claim-action');
                          if (action === 'send-otp') sendOtp(btn);
                          else if (action === 'resend') sendOtp(btn);
                          else if (action === 'back-to-phone') setStep('phone');
                          else if (action === 'verify-otp') verifyOtp(btn);
                      });

                      // OTP UX: auto-advance + backspace-to-previous + paste handling.
                      var cells = modal.querySelectorAll('[data-otp-cell]');
                      cells.forEach(function (cell, idx) {
                          cell.addEventListener('input', function (e) {
                              cell.value = (cell.value || '').replace(/\D/g, '').slice(0, 1);
                              if (cell.value && cells[idx + 1]) cells[idx + 1].focus();
                          });
                          cell.addEventListener('keydown', function (e) {
                              if (e.key === 'Backspace' && !cell.value && cells[idx - 1]) cells[idx - 1].focus();
                          });
                          cell.addEventListener('paste', function (e) {
                              var data = (e.clipboardData || window.clipboardData).getData('text') || '';
                              var digits = data.replace(/\D/g, '').slice(0, cells.length);
                              if (!digits) return;
                              e.preventDefault();
                              for (var i = 0; i < cells.length; i++) cells[i].value = digits[i] || '';
                              var next = digits.length >= cells.length ? cells[cells.length - 1] : cells[digits.length];
                              if (next) next.focus();
                          });
                      });

                      window.profileClaimClose = function () {
                          modal.classList.remove('is-open');
                          document.body.style.overflow = '';
                          setStep('phone');
                          cells.forEach(function (c) { c.value = ''; });
                      };
                      // Show the modal when the trigger was clicked while
                      // it was still hidden=true (initial render quirk).
                      modal.removeAttribute('hidden');
                      modal.style.display = 'none';
                      var observer = new MutationObserver(function () {
                          modal.style.display = modal.classList.contains('is-open') ? 'flex' : 'none';
                      });
                      observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
                  })();
              </script>
              @endif

              {{-- Mobile: Report link inside About tab --}}
              <p class="ev-mobile-report visible-xs" style="text-align:center; padding: 16px 0; border-top: 1px solid #1a1a1a;">
                  <a class="report-link" href="javascript:void(0);">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                      Report this Profile
                  </a>
              </p>
              </div>{{-- /ev-mobile-tab-content[about] --}}

              {{-- Mobile Reviews tab content --}}
              <div class="ev-mobile-tab-content" data-tab-content="reviews" style="display:none;">
                  <div class="reviews styled-questions" id="mobile-listing-reviews">
                      @if(count($reviews) > 0)
                      <ul class="list-unstyled list-separated revs">
                          @foreach($reviews as $review)
                          <li>
                              <div class="review">
                                  <span class="star-rating" data-val="{{$review->star}}"></span>
                                  <span class="reviewer">by <a href="#">{{$review->getuser->name}}</a></span>
                                  <div class="review-description">
                                      <p class="review-text">{{ $review->review }}</p>
                                  </div>
                              </div>
                          </li>
                          @endforeach
                      </ul>
                      @else
                      <div class="no-reviews">There are no reviews yet. <a class="add-review1" href="javascript:void(0)"><span>Add a review</span></a></div>
                      @endif
                  </div>
              </div>

              {{-- Mobile Questions tab content --}}
              <div class="ev-mobile-tab-content" data-tab-content="questions" style="display:none;">
                  <div class="styled-questions" id="mobile-listing-questions">
                      @if($questions->count() > 0)
                      <ul class="list-unstyled list-separated que">
                          @foreach($questions as $question)
                          <li>
                              <div class="question-block">
                                  <p class="question">{{$question->question}}</p>
                              </div>
                              <span class="questioner">by <a href="#">{{$question->getuser->name}}</a></span>
                              @if($question->answer)
                              <div class="answer-wrapper">
                                  <div class="answer-block">
                                      <p class="answer">{{$question->answer}}</p>
                                  </div>
                              </div>
                              @else
                              <div class="pending-answer text-muted"><i>Awaiting answer</i></div>
                              @endif
                          </li>
                          @endforeach
                      </ul>
                      @else
                      <p>No questions asked yet.</p>
                      @endif
                  </div>
              </div>

              <div id="revsAndQs">
                <ul class="nav nav-tabs mb-2" role="tablist">
                  <li class="active" role="presentation">
                    <a aria-controls="listingReviews" data-toggle="tab" href="#listingReviews" role="tab">
                      <h3 class="my-0">Reviews</h3>
                    </a>
                  </li>
                  <li role="presentation">
                    <a aria-controls="listingQuestions" data-toggle="tab" href="#listingQuestions" role="tab">
                      <h3 class="my-0">Questions</h3>
                    </a>
                  </li>
                  <li role="presentation">
                    <a aria-controls="twitterPosts" data-toggle="tab" href="#twitterPosts" role="tab">
                      <h3 class="my-0">X (Twitter)</h3>
                    </a>
                  </li>
                </ul>
                <div class="tab-content" style="min-height: 100px;">

                  <div class="px-0 px-sm-1 tab-pane fade in active" id="listingReviews" role="tabpanel" style="display: block;">

                    <div class="reviews styled-questions" id="listing-reviews">
                      @if(count($reviews)> 0)
                      <ul class="list-unstyled list-separated revs">
                        <li itemscope="" itemtype="https://schema.org/Review">

                          @foreach($reviews as $review)
                          <div class="review">
                            <div class="hidden" itemprop="itemReviewed" itemscope=""
                              itemtype="https://schema.org/AdultEntertainment">
                              @php
                                $firstImage = (isset($images) && $images && $images->count() > 0) ? $images->first() : null;
                              @endphp
                              <meta
                                content="{{ $firstImage ? webp_asset('userimages/'.$firstImage->user_id.'/'.$firstImage->profile_id.'/'.$firstImage->image) : '' }}"
                                itemprop="image" />
                              <meta content="{{ $profile->name }} - escort in {{ $cityName }}" itemprop="name" />
                              <meta content="/{{ $gender }}-escorts-in-{{ $citySlug }}/{{ $profile->slug }}" itemprop="url" />
                              <meta content="{{ $cityName }}, {{ $cityCountry }}" itemprop="areaServed" />
                            </div>
                            <span class="star-rating" data-val="{{$review->star}}" itemprop="reviewRating" itemscope=""
                              itemtype="https://schema.org/Rating" title="Rating: 5 / 5">
                              <span class="sr-only" content="{{$review->star}}" itemprop="ratingValue">Rating: 5 /
                                5</span>
                            </span>
                            <span class="reviewer">by <span itemprop="author" itemscope=""
                                itemtype="https://schema.org/Person">
                                <a href="/u/deesseanna7">
                                  <span itemprop="name">{{$review->getuser->name}}</span>
                                </a>
                              </span>
                            </span>
                            <span class="review-date" content="2023-12-01" itemprop="datePublished">&nbsp;&ndash; 1 Dec
                              2023</span>
                            <div class="review-description">
                              <p class="review-text" itemprop="reviewBody ">« {{$review->review}}</p>
                            </div>
                            <div class="answer-wrapper">
                              <div class="answer-block">
                                <p class="review-comment-text">Thank you dear for your feedback</p>
                              </div>
                            </div>
                            
                            
                            @endforeach
                          </div>

                        </li>
                      </ul>
                      @else
                      <div class="no-reviews">There are no reviews yet. <a class="ml-2 ajax-overlay add-review1" data-modal-dialog-class="modal-md" data-overlay-class="user-action-modal" href="javascript:void(0)"><span class="ml-1">Add a review</span></a></div>
                      @endif
                    </div>

                  </div>
                  <div class="px-0 px-sm-1 tab-pane fade" id="listingQuestions" role="tabpanel">
                    <div class="styled-questions" id="listing-questions" style="margin-top: 0; padding-top: 0;">
                      @if($questions->count() > 0)
                      <ul class="list-unstyled list-separated que">
                        @foreach($questions as $question)
                        <li>
                          <div class="question-block">
                            <p class="question">{{$question->question}}</p>
                          </div>
                          <span class="questioner">by <a href="#">{{$question->getuser->name}}</a></span>
                          <span class="question-date">&nbsp;&ndash; {{$question->created_at->format('d M Y')}}</span>

                          @if($question->answer)
                          <div class="answer-wrapper">
                            <div class="answer-block">
                              <p class="answer">{{$question->answer}}</p>
                              <span class="answer-date text-muted">&nbsp;&ndash; Answered
                                {{$question->updated_at->format('d M Y')}}</span>
                            </div>
                          </div>
                          @else
                          <div class="pending-answer text-muted">
                            <i>Awaiting answer</i>
                          </div>
                          @endif
                        </li>
                        @endforeach
                      </ul>
                      @else
                      <p>No questions asked yet.</p>
                      @endif
                    </div>
                  </div>
                  <div class="px-0 px-sm-1 tab-pane fade" id="twitterPosts" role="tabpanel" style="padding-top: 0;">
                    <div style="margin: 0; padding: 0;">
                      <p style="margin: 0; padding: 0;">The profile has no linked X account</p>
                    </div>
                  </div>

                  
                </div>
                <div class="clearfix"></div>
                <p class="margin-top padding-top border-top">
                  <a class="report-link">
                    <span class="fa-stack">
                      <i class="fa fa-circle fa-stack-2x"></i>
                      <i class="fa fa-flag fa-stack-1x fa-inverse pdtop" style=""></i>
                    </span>Report this profile </a>
                </p>
              </div>

            </div>

            
            <div class="hidden-xs pb-thumbnails col-sm-3 col-sm-pull-9 col-lg-5 col-lg-pull-7">
              <div class="row listing-photos-sm-plus">
                @foreach($images as $img)
                <div class="col-lg-6">
                  <a class="pb-photo-link"
                    href="{{webp_asset("userimages/".$img->user_id."/".$img->profile_id."/".$img->image)}}">
                    <span class="img-wrapper listing">
                      <div class="image-wrapper">
                        <img alt="{{ $userName }} - escort in {{ $userCityField }}" class="img-responsive uniform-image"
                          loading="lazy" decoding="async"
                          src="{{webp_asset("userimages/".$img->user_id."/".$img->profile_id."/".$img->image)}}" />
                      </div>
                    </span>
                  </a>
                </div>
                @endforeach
              </div>
            </div>

          </div>
        </article>


      </div>
      </div>

    {{-- Mobile Sticky Bottom Action Bar --}}
    <div class="ev-mobile-action-bar visible-xs">
        <a class="ev-action-item contact-phone1" href="javascript:void(0)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <span>Phone</span>
        </a>
        <a class="ev-action-item ev-action-primary send-message1" href="javascript:void(0)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span>Message</span>
        </a>
        <a class="ev-action-item ask-question1" href="javascript:void(0)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span>Ask</span>
        </a>
        <a class="ev-action-item add-review1" href="javascript:void(0)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            <span>Review</span>
        </a>
    </div>

    {{-- models --}}
    <div class="user-action-modal modal reviewmodal" wire:ignore.self data-backdrop="static">
      <div class="modal-md  modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            @if(auth()->check())
            @if($rev)

            Thank you for adding review

            @else
            <h2 class="modal-title">
              <i class="fa fa-heart2 fa-fw"></i> Add a review
            </h2>

            @endif

            @else
            @endif

          </div>
          @if(!auth()->check())
          <div style="padding:24px 24px 20px;background:#111213;">
            <p style="color:#fff;font-size:20px;font-weight:400;margin:0 0 24px;padding-right:40px;line-height:1.4;">To add a review you need to <br> be a member.</p>
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
              <input type="text" placeholder="Username" wire:model='reg_username'
                style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:5px;color:#fff;padding:5px 14px;font-size:14px;outline:none;box-sizing:border-box;">
              <input type="email" placeholder="Email" wire:model='reg_email'
                style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:5px;color:#fff;padding:5px 14px;font-size:14px;outline:none;box-sizing:border-box;">
              <input type="password" placeholder="Password" wire:model='reg_password'
                style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:5px;color:#fff;padding:5px 14px;font-size:14px;outline:none;box-sizing:border-box;">
            </div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
              <input type="checkbox" id="rev_terms" wire:model='reg_terms'
                style="width:20px;height:20px;accent-color:#c8ff00;cursor:pointer;flex-shrink:0;border-radius:50%;-webkit-appearance:none;appearance:none;border:2px solid #5E6365;background:transparent;position:relative;"
                onclick="this.style.background=this.checked?'#c8ff00':'transparent';this.style.borderColor=this.checked?'#c8ff00':'#5E6365';">
              <label for="rev_terms" style="color:#ccc;font-size:14px;cursor:pointer;margin:0;margin-top:4px;">
                I accept the <a href="/terms" style="color:#c8ff00;text-decoration:none;">Terms and Conditions</a> of use
              </label>
            </div>
            <button type="button"
              style="background:#c8ff00;color:#000;font-weight:400;font-size:15px;border:none;border-radius:50px;padding:4px 30px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
              Send <span style="font-family:'Font Awesome 5 Free',sans-serif;font-weight:900;font-size:12px;display:inline-block;">&#xf054;</span>
            </button>
            <p style="margin-top:20px;color:#888;font-size:14px;margin-bottom:0;">Already have an account? <a href="/login" style="color:#c8ff00;text-decoration:none;">Sign in</a></p>
          </div>
          @endif
          @if(auth()->check())
          @if ($rev)
          <div class="alert alert-success">
            It will be published when it has been reviewed by a moderator.
          </div>
          <p><button class="btn btn-lg btn-primary" data-dismiss="modal"
              data-modal-notice-response-btn="true">OK</button></p>
          @endif
          @if(!$rev)
          <div class="modal-body">
            <ul class="list-unstyled">
              <li>Only review if you had direct contact with this escort</li>
              <li>Do not write fake or abusive reviews, they will not be published</li>
              <li>To contact this escort click on <a class="ajax-overlay" data-modal-dialog-class="modal-md"
                  data-overlay-class="user-action-modal" data-dismiss="modal"
                  href="/action/listings/alisha-gorgeous-hottie-tecom/listing_messages/new">
                  <i class="fa fa-envelope fa-inline"></i>Send Message </a>
              </li>
            </ul>
            <form wire:submit.prevent="postreview">
              <div class="form-group">
                <textarea wire:model="review" class="form-control" rows="6"
                  placeholder="Your review (minimum 10 characters)"></textarea>
                @error('review')
                <div class="validation-error" style="display: block;">
                  <span class="tooltip"></span>{{ $message }}
                </div>
                @enderror
              </div>
              
              <div class="form-group">
                <label class="control-label">Rating <span class="text-danger">*</span></label>
                <div class="star-rating editable" id="reviewStarRating" style="margin-top: 10px;" wire:ignore>
                  <span class="stars">
                    <span class="star" data-rating="1"></span>
                    <span class="star" data-rating="2"></span>
                    <span class="star" data-rating="3"></span>
                    <span class="star" data-rating="4"></span>
                    <span class="star" data-rating="5"></span>
                  </span>
                </div>
                <input type="hidden" wire:model="star" id="hiddenStarInput">
              </div>
              @error('star')
              <div class="text-danger small mb-2">{{ $message }}</div>
              @enderror
              
              @if (session()->has('rmessage'))
              <div class="alert alert-success mb-3">
                {{ session('rmessage') }}
              </div>
              @endif
              
              <div style="text-align:center;">
                <button type="submit" wire:loading.attr="disabled" wire:target="postreview"
                  style="background:#c8ff00;color:#000;font-weight:500;font-size:16px;border:none;border-radius:50px;padding:5px 40px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
                  <span wire:loading.remove wire:target="postreview">Post review <span style="font-family:'Font Awesome 5 Free',sans-serif;font-weight:900;font-size:12px;display:inline-block;">&#xf054;</span></span>
                  <span wire:loading wire:target="postreview">Posting...</span>
                </button>
              </div>
            </form>
          </div>
          @endif
          @endif
        </div>
      </div>
    </div>

    <div class="user-action-modal modal askq" wire:ignore.self data-backdrop="static">
      <div class="modal-md modal-dialog" style="max-width:580px;">
        <div class="modal-content" style="background:#111213;border:1px solid #2a2a2a;border-radius:12px;color:#fff;">

          @if(auth()->check())
          {{-- LOGGED IN: Ask question --}}
          <div style="padding:10px 24px 0;display:flex;align-items:center;gap:10px;border-bottom:1px solid #2a2a2a;padding-bottom:16px;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;background:#1c1c1e;border:1px solid #3a3a3a;border-radius:50%;color:#fff;font-weight:700;font-size:16px;">?</span>
            @if(session()->has('questionmsg'))
            <p style="margin:0;color:#fff;font-size:16px;flex:1;">Your question has been sent</p>
            @else
            <h2 style="margin:0;font-size:17px;font-weight:600;color:#fff;flex:1;">Ask question</h2>
            @endif
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"
              style="background:transparent;border:1px solid #3a3a3a;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#aaa;opacity:1;padding:0;flex-shrink:0;">
              <i class="fa fa-times" style="font-size:13px;" aria-hidden="true"></i>
            </button>
          </div>
          <div style="padding:24px;">
            @if(session()->has('questionmsg'))
            <div style="background:#1a2e1a;border:1px solid #2d5a2d;color:#7ecb7e;border-radius:8px;padding:12px 16px;margin-bottom:16px;">{{ session('questionmsg') }}</div>
            <button data-dismiss="modal" data-modal-notice-response-btn="true"
              style="background:#c8ff00;color:#000;font-weight:700;font-size:15px;border:none;border-radius:50px;padding:12px 32px;cursor:pointer;">OK</button>
            @else
            <ul style="list-style:none;padding:0;margin:0 0 20px;color:#aaa;font-size:14px;display:flex;flex-direction:column;gap:8px;">
              <li>If your question is private, send a
                <a class="ajax-overlay" href="/action/listings/alisha-gorgeous-hottie-tecom/listing_messages/new"
                  data-dismiss="modal" data-modal-dialog-class="modal-md" data-overlay-class="user-action-modal"
                  style="color:#c8ff00;text-decoration:none;font-weight:500;">
                  <i class="fa fa-envelope"></i> Message</a>.
              </li>
              <li>If it's a review,
                <a class="ajax-overlay" href="/action/listings/alisha-gorgeous-hottie-tecom/reviews/new"
                  data-dismiss="modal" data-modal-dialog-class="modal-md" data-overlay-class="user-action-modal"
                  style="color:#c8ff00;text-decoration:none;font-weight:500;">
                  <i class="fa fa-pencil-alt"></i> write it here</a>, otherwise it will be deleted.
              </li>
              <li>Your question will be visible if the advertiser replies publicly.</li>
            </ul>
            <form wire:submit.prevent='askquestion' id="new_listing_question" novalidate="novalidate" accept-charset="UTF-8">
              <div style="margin-bottom:20px;">
                <textarea wire:model='question' rows="5"
                  data-validations-wait-for-submit="true" data-validations="presence length(10,240)"
                  data-validations-minlength-message="Your question needs to be at least 10 characters long"
                  maxlength="240" name="listing_question[question]" id="listing_question_question"
                  placeholder="Type your question here..."
                  style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:5px;color:#fff;padding:12px 14px;font-size:14px;outline:none;resize:vertical;box-sizing:border-box;"></textarea>
              </div>
              <div style="text-align:center;">
                <button data-btn-submit="" type="submit"
                  style="background:#c8ff00;color:#000;font-weight:500;font-size:16px;border:none;border-radius:50px;padding:5px 40px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
                  Ask <span style="font-family:'Font Awesome 5 Free',sans-serif;font-weight:900;font-size:12px;display:inline-block;">&#xf054;</span>
                </button>
              </div>
            </form>
            @endif
          </div>

          @else
          {{-- NOT LOGGED IN: registration form --}}
          <div style="padding:24px;position:relative;">
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"
              style="position:absolute;top:16px;right:16px;background:transparent;border:1px solid #3a3a3a;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#aaa;opacity:1;padding:0;">
              <i class="fa fa-times" style="font-size:13px;" aria-hidden="true"></i>
            </button>
            <p style="color:#fff;font-size:20px;font-weight:400;margin:0 0 24px;padding-right:40px;line-height:1.4;">To ask a question you need to <br> be a member.</p>

            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
              <input type="text" placeholder="Username" wire:model='reg_username'
                style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:5px;color:#fff;padding:5px 14px;font-size:14px;outline:none;box-sizing:border-box;">
              <input type="email" placeholder="Email" wire:model='reg_email'
                style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:5px;color:#fff;padding:5px 14px;font-size:14px;outline:none;box-sizing:border-box;">
              <input type="password" placeholder="Password" wire:model='reg_password'
                style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:5px;color:#fff;padding:5px 14px;font-size:14px;outline:none;box-sizing:border-box;">
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
              <input type="checkbox" id="askq_terms" wire:model='reg_terms'
                style="width:20px;height:20px;accent-color:#c8ff00;cursor:pointer;flex-shrink:0;border-radius:50%;-webkit-appearance:none;appearance:none;border:2px solid #5E6365;background:transparent;position:relative;"
                onclick="this.style.background=this.checked?'#c8ff00':'transparent';this.style.borderColor=this.checked?'#c8ff00':'#5E6365';">
              <label for="askq_terms" style="color:#ccc;font-size:14px;cursor:pointer;margin:0; margin-top:4px">
                I accept the <a href="/terms" style="color:#c8ff00;text-decoration:none;">Terms and Conditions</a> of use
              </label>
            </div>

            <button type="button"
              style="background:#c8ff00;color:#000;font-weight:400;font-size:15px;border:none;border-radius:50px;padding:4px 30px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
              Send <span style="font-family:'Font Awesome 5 Free',sans-serif;font-weight:900;font-size:12px;display:inline-block;">&#xf054;</span>
            </button>
            <p style="margin-top:20px;color:#888;font-size:14px;margin-bottom:0;">Already have an account? <a href="/login" style="color:#c8ff00;text-decoration:none;">Sign in</a></p>
          </div>
          @endif

        </div>
      </div>
    </div>

    <div class="user-action-modal modal msgmodal" data-backdrop="static">
      <div class="modal-md modal-dialog" style="max-width:580px;">
        <div class="modal-content" style="background:#111213;border:1px solid #2a2a2a;border-radius:12px;color:#fff;">

          @if(auth()->check())
          {{-- LOGGED IN: Send message form --}}
          <div class="modal-header" style="border-bottom:1px solid #2a2a2a;padding:20px 24px 16px;display:flex;align-items:center;gap:10px;">
            <span style="display:inline-flex;align-items:center;justify-content:center;">
               <img src="https://assets.massagerepublic.com.co/assets/newtheme/msg.svg" width="18" height="18" alt="Message">
            </span>
            @if (session()->has('sendmsg'))
            <p style="margin:0;color:#fff;font-size:16px;">Your message has been sent</p>
            @else
            <h1 class="modal-title" style="margin:0;font-size:17px;font-weight:600;color:#fff;flex:1;">
              Message for {{ $userName }}
            </h1>
            @endif
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"
              style="margin-left:auto;background:transparent;border:1px solid #3a3a3a;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;color:#aaa;opacity:1;padding:0;">
              <i class="fa fa-times" style="font-size:13px;" aria-hidden="true"></i>
            </button>
          </div>
          <div class="modal-body" style="padding:24px;">

            @if (session()->has('sendmsg'))
            <div class="alert alert-success" style="background:#1a2e1a;border:1px solid #2d5a2d;color:#7ecb7e;border-radius:8px;">{{session('sendmsg')}}</div>
            <br>
            <button class="btn btn-lg" data-dismiss="modal" data-modal-notice-response-btn="true"
              style="background:#c8ff00;color:#000;font-weight:700;border-radius:50px;border:none;padding:10px 32px;">OK</button>
            @else
            <form wire:submit.prevent='sendmsg' class="simple_form validate track-event" id="new_listing_message"
              data-track="contact/message/UAE premium-listing-contact/message/UAE" novalidate="novalidate"
              action="/action/listings/alisha-gorgeous-hottie-tecom/listing_messages" accept-charset="UTF-8"
              method="post">
              <input name="utf8" type="hidden" value="✓">
              <input type="hidden" name="authenticity_token"
                value="+LM7/8YR7Sn0qCawhfPlPNDAoom5Dtq32T3SvdRjJ+XUPyA8gl9HLrxn8XNmjkslyCYvio627JOJvCRYqOCj3w==">

              <div class="form-group" style="margin-bottom:18px;">
                <label style="display:block;color:#fff;font-size:14px;font-weight:500;margin-bottom:8px;" for="listing_message_sender_email_address">Email</label>
                <input wire:model='email' type="email"
                  data-validations="presence emailFormat"
                  name="listing_message[sender_email_address]" id="listing_message_sender_email_address"
                  style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:4px;color:#fff;padding:4px 14px;font-size:14px;outline:none;box-sizing:border-box;">
                <small style="color:#888;font-size:10px;margin-top:4px;display:block;">Please enter a valid email address</small>
              </div>

              <div class="form-group" style="margin-bottom:18px;">
                <label style="display:block;color:#fff;font-size:14px;font-weight:500;margin-bottom:8px;" for="listing_message_content">Message</label>
                <textarea wire:model='msg' rows="5"
                  data-validations="presence minlength(10)" data-validations-presence-message="Add a message"
                  maxlength="500" name="listing_message[content]" id="listing_message_content"
                  style="width:100%;background:transparent;border:1px solid #5E6365;border-radius:4px;color:#fff;padding:4px 14px;font-size:14px;outline:none;resize:vertical;box-sizing:border-box;"></textarea>
              </div>

              <div class="form-group" style="margin-bottom:24px;">
                <label style="display:block;color:#fff;font-size:14px;font-weight:500;margin-bottom:8px;" for="listing_message_phone_number_attributes_phone_digits">Telephone</label>
                <div style="display:flex;gap:8px;">
                  <select wire:model='code'
                    name="listing_message[phone_number_attributes][calling_code]"
                    id="message_phone_code"
                    style="background:transparent;border:1px solid #5E6365;border-radius:4px;color:#fff;padding:4px 12px;font-size:14px;outline:none;width:110px;appearance:none;-webkit-appearance:none;cursor:pointer;">
                    <option value="">+971 ▾</option>
                    @foreach($countries as $country)
                    <option value="{{$country->phonecode}}">+{{$country->phonecode}} - {{$country->nicename}}</option>
                    @endforeach
                  </select>
                  <input wire:model='phone' type="text"
                    placeholder="Phone number"
                    name="listing_message[phone_number_attributes][phone_digits]"
                    id="listing_message_phone_number_attributes_phone_digits"
                    style="flex:1;background:transparent;border:1px solid #5E6365;border-radius:4px;color:#fff;padding:4px 14px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
              </div>

              <div style="text-align:center;">
                <button data-btn-submit="" type="submit"
                  style="background:#c8ff00;color:#000;font-weight:500;font-size:15px;border:none;border-radius:50px;padding:5px 30px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
                  Send <i class="fa fa-chevron-right" style="font-size:12px;"></i>
                </button>
              </div>
            </form>
            @endif
          </div>

          @else
          {{-- NOT LOGGED IN: registration form --}}
          <div style="padding:24px;position:relative;">
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"
              style="position:absolute;top:16px;right:16px;background:transparent;border:1px solid #3a3a3a;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#aaa;opacity:1;padding:0;">
              <i class="fa fa-times" style="font-size:13px;" aria-hidden="true"></i>
            </button>
            <p style="color:#fff;font-size:20px;font-weight:600;margin:0 0 24px;padding-right:40px;line-height:1.4;">To send a message you need to be a member.</p>

            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
              <input type="text" placeholder="Username" wire:model='msg_reg_username'
                style="width:100%;background:#1c1c1e;border:1px solid #2e2e30;border-radius:8px;color:#fff;padding:12px 14px;font-size:14px;outline:none;box-sizing:border-box;">
              <input type="email" placeholder="Email" wire:model='msg_reg_email'
                style="width:100%;background:#1c1c1e;border:1px solid #2e2e30;border-radius:8px;color:#fff;padding:12px 14px;font-size:14px;outline:none;box-sizing:border-box;">
              <input type="password" placeholder="Password" wire:model='msg_reg_password'
                style="width:100%;background:#1c1c1e;border:1px solid #2e2e30;border-radius:8px;color:#fff;padding:12px 14px;font-size:14px;outline:none;box-sizing:border-box;">
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
              <input type="checkbox" id="msg_terms" wire:model='msg_reg_terms'
                style="width:18px;height:18px;accent-color:#c8ff00;cursor:pointer;flex-shrink:0;">
              <label for="msg_terms" style="color:#ccc;font-size:14px;cursor:pointer;margin:0;">
                I accept the <a href="/terms" style="color:#c8ff00;text-decoration:none;">Terms and Conditions</a> of use
              </label>
            </div>

            <button type="button"
              style="background:#c8ff00;color:#000;font-weight:700;font-size:15px;border:none;border-radius:50px;padding:12px 32px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
              Send <i class="fa fa-chevron-right" style="font-size:12px;"></i>
            </button>
            <p style="margin-top:20px;color:#888;font-size:14px;margin-bottom:0;">Already have an account? <a href="/login" style="color:#c8ff00;text-decoration:none;">Sign in</a></p>
          </div>
          @endif

        </div>
      </div>
    </div>

    {{-- Mobile Call Modal - Bottom Sheet --}}
    <div class="ev-mobile-modal-overlay" id="evMobileCallOverlay" style="display:none;">
        <div class="ev-mobile-modal">
            <div class="ev-mobile-modal-header">
                <h3>Call me now</h3>
                <button type="button" class="ev-mobile-modal-close" id="evMobileCallClose" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="ev-mobile-modal-body">
                @if($user->iswhatsapp)
                <a class="ev-call-row whatsapp-rotation-link"
                   href="https://wa.me/{{ $phoneForLink }}?text=Hi, I found your profile on evoory: {{ url()->current() }}"
                   target="_blank"
                   data-profile-id="{{ $user->id }}"
                   data-phone="{{ $phoneForLink }}"
                   data-profile-url="{{ url()->current() }}"
                   onclick="handleWhatsAppClick(event, this)">
                    <span class="ev-call-icon ev-whatsapp-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </span>
                    <span class="ev-call-number">{{ $phoneDisplay }}</span>
                </a>
                @endif
                <a class="ev-call-row" href="tel:{{ $phoneE164 }}">
                    <span class="ev-call-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <span class="ev-call-number">{{ $phoneDisplay }}</span>
                </a>
                <p class="ev-call-note">Please tell me you found me on evoory.</p>
            </div>
            <div class="ev-mobile-modal-footer">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <p>Do not pay anyone in advance, as this is often used by scammers. Please report any suspicious profiles to us. For your safety, we recommend booking verified escorts.</p>
            </div>
        </div>
    </div>

    <div class="modal callnow" wire:ignore.self data-backdrop="static">
      <div class="modal-md  modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <h2 class="modal-title">Call me now</h2>
          </div>
          <div class="modal-body modal-call">
            <ul class="list-unstyled pull-left no-margin">
              <li> 
                <span class="lead phone-number">
                  @if($user->iswhatsapp)
                  <a class="icon-whatsapp phone-icon whatsapp-rotation-link"
   data-toggle="tooltip"
   data-profile-id="{{ $user->id }}"
   data-phone="{{ $phoneForLink }}"
   data-profile-url="{{ url()->current() }}"
   href="https://wa.me/{{ $phoneForLink }}?text=Hi, I found your profile on MassageRepublic: {{ url()->current() }}"
   title="WhatsApp"
   target="_blank"
   onclick="handleWhatsAppClick(event, this)">
</a>
                  @endif

                  @if($user->istelegram)
                  <a class="icon-telegram phone-icon" data-toggle="tooltip"
                    href="https://t.me/{{ $phoneForLink }}" title="Telegram" target="_blank">
                  </a>
                  @endif

                  @if($user->issignal)
                  <a class="icon-signal phone-icon" data-toggle="tooltip"
                    href="https://signal.me/#p/{{ $phoneE164 }}" title=""
                    data-original-title="Signal"></a>
                  @endif

                  @if($user->iswechat)
                  <span class="icon-wechat phone-icon" data-toggle="tooltip" title=""
                    data-original-title="WeChat"></span>
                  @endif

                  @if(!$user->iswhatsapp && !$user->istelegram && !$user->issignal && !$user->iswechat)
                  <i class="phone-icon fa fa-phone"></i>
                  @endif
                  <span>
                    <a class="tel" href="tel:{{ $phoneE164 }}">{{ $phoneDisplay }}</a>
                  </span>
                </span>
              </li>
            </ul>
            <p style="clear:both;font-size:12px" class="text-muted pull-left">Please tell me you found me on evoory.</p>
            <div class="clearfix"></div>
          </div>
          <div class="modal-footer modal-footer__disclaimer">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <p class="mb-0 text-left">Do not pay anyone in advance, as this is often used by scammers. Please report any suspicious profiles to us. For your safety, we recommend booking verified escorts.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="user-action-modal modal reportModal" wire:ignore.self>
      <div class="modal-md modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button class="close" type="button" data-dismiss="modal">
              <i class="fa fa-times"></i>
            </button>
            <h1 class="modal-title">Report {{ $userName }}</h1>
          </div>
          <div class="modal-body">
            @if(!auth()->check())
              <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i>
                You must be logged in to report a profile.
              </div>
              <p class="text-center">
                <a href="/sign-in" class="btn btn-primary btn-lg">Sign In</a>
                <button class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
              </p>
            @else
              @if(session()->has('report_message'))
                <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i>
                  {{ session('report_message') }}
                </div>
                <p class="text-center">
                  <button class="btn btn-lg btn-primary" data-dismiss="modal">OK</button>
                </p>
              @elseif(session()->has('report_error'))
                <div class="alert alert-danger">
                  <i class="fa fa-exclamation-circle"></i>
                  {{ session('report_error') }}
                </div>
                <p class="text-center">
                  <button class="btn btn-lg btn-primary" data-dismiss="modal">OK</button>
                </p>
              @else
                <form wire:submit.prevent="submitReport">
                  <div class="form-group">
                    <label>Report Type <span class="text-danger">*</span></label>
                    <select wire:model="reportType" class="form-control" required>
                      <option value="">Select reason</option>
                      <option value="fake">Fake Profile</option>
                      <option value="spam">Spam</option>
                      <option value="inappropriate">Inappropriate Content</option>
                      <option value="other">Other</option>
                    </select>
                    @error('reportType')
                      <span class="text-danger small">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label>Description <span class="text-danger">*</span></label>
                    <textarea wire:model="reportDescription" class="form-control" rows="4" 
                      placeholder="Please provide details about why you are reporting this profile (minimum 10 characters)" 
                      required></textarea>
                    <small class="text-muted">
                      {{ strlen($reportDescription ?? '') }}/1000 characters
                    </small>
                    @error('reportDescription')
                      <span class="text-danger small d-block">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                      <span wire:loading.remove>
                        <i class="fa fa-flag"></i> Submit Report
                      </span>
                      <span wire:loading>
                        <i class="fa fa-spinner fa-spin"></i> Submitting...
                      </span>
                    </button>
                    <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
                  </div>
                </form>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>

</div>

@push('js')
<script>
/* Async profile-view tracker — fires once per page load, survives page caching.
   sendBeacon is preferred (won't block page unload); fetch is a fallback. */
(function() {
    var pid = {{ (int) ($profile->id ?? 0) }};
    if (!pid) return;
    var url = '/profile/' + pid + '/track-view';
    try {
        if (navigator.sendBeacon) {
            navigator.sendBeacon(url, new Blob([''], { type: 'application/x-www-form-urlencoded' }));
        } else {
            fetch(url, { method: 'POST', credentials: 'same-origin', keepalive: true });
        }
    } catch (e) { /* swallow — tracking is best-effort */ }
})();

/* Mobile Tab Switching */
(function() {
    var tabs = document.querySelectorAll('.ev-mobile-tab');
    var contents = document.querySelectorAll('.ev-mobile-tab-content');
    if (!tabs.length) return;
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            contents.forEach(function(c) {
                c.style.display = c.getAttribute('data-tab-content') === target ? 'block' : 'none';
            });
        });
    });
})();

/* Mobile Bottom Sheet Modal */
(function() {
    function closeModal(id) {
        var overlay = document.getElementById(id);
        if (overlay) {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    // Close button
    var closeBtn = document.getElementById('evMobileCallClose');
    if (closeBtn) closeBtn.addEventListener('click', function() { closeModal('evMobileCallOverlay'); });
    // Close on overlay click
    var overlay = document.getElementById('evMobileCallOverlay');
    if (overlay) overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal('evMobileCallOverlay');
    });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" defer></script>
<script>
/* Inject SVG close icons into all modal close buttons */
document.querySelectorAll('.modal .close').forEach(function(btn) {
    if (!btn.querySelector('svg')) {
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
    }
});
/* Inject report flag SVG */
document.querySelectorAll('.report-link').forEach(function(link) {
    if (!link.querySelector('svg')) {
        link.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg> Report this Profile';
    }
});
</script>
<script>
  // Flag to prevent multiple initializations
  window.profileDetailsInitialized = false;
  
  function initProfileDetails() {
    // Prevent multiple initializations
    if (window.profileDetailsInitialized) {
      return;
    }
    
    // Wait for jQuery to be fully loaded
    const checkJQuery = setInterval(() => {
      if (window.jQuery) {
        clearInterval(checkJQuery);
        window.profileDetailsInitialized = true;
        const $ = window.jQuery;
        
        // Star rating click handler - uses existing CSS classes
        $(document).on('click', '#reviewStarRating .star', function(e) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          
          const rating = parseInt($(this).attr('data-rating'));
          console.log('Star clicked:', rating);
          
          // Update visual state FIRST using existing CSS classes
          $('#reviewStarRating .star').removeClass('selected');
          $('#reviewStarRating .star').each(function(index) {
            if (index < rating) {
              $(this).addClass('selected');
            }
          });
          
          // Update hidden input
          $('#hiddenStarInput').val(rating);
          
          // Update Livewire model using defer to prevent immediate re-render
          @this.set('star', rating, false);
          
          return false;
        });
        
        // Prevent star container clicks from bubbling
        $(document).on('click', '#reviewStarRating', function(e) {
          e.stopPropagation();
        });
        
        $(document).on('click', '#reviewStarRating .stars', function(e) {
          e.stopPropagation();
        });
        
        // Hover effects using existing CSS
        $(document).on('mouseenter', '#reviewStarRating .star', function() {
          const rating = parseInt($(this).attr('data-rating'));
          $('#reviewStarRating .star').each(function(index) {
            if (index < rating) {
              $(this).addClass('hover');
            } else {
              $(this).removeClass('hover');
            }
          });
        });
        
        $(document).on('mouseleave', '#reviewStarRating', function() {
          $('#reviewStarRating .star').removeClass('hover');
        });
          
          $("a.add-review1").off('click').on('click', function() {
            $("div.reviewmodal").modal("show");
          });
          $("a.ask-question1").off('click').on('click', function() {
            $("div.askq").modal({
              backdrop: 'static',
              keyboard: false
            });
          });
          $("a.send-message1").off('click').on('click', function() {
            $("div.msgmodal").modal("show");
          });
          $("a.contact-phone1").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Use mobile bottom sheet on small screens
            if (window.innerWidth < 768) {
                var overlay = document.getElementById('evMobileCallOverlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
                return false;
            }

            setTimeout(function() {
              $("div.callnow").modal({
                backdrop: 'static',
                keyboard: false,
                show: true
              });
            }, 100);

            return false;
          });
          
          // Track phone click when user clicks the actual tel: link
          $("a.tel").off('click').on('click', function() {
            @this.call('trackPhoneClick');
          });
          
          $(".report-link").off('click').on('click', function() {
            $(".reportModal").modal("show");
          });

          // Listen for close report modal event
          window.addEventListener('closeReportModal', event => {
            $(".reportModal").modal("hide");
          });
          
          // Listen for close message modal event
          window.addEventListener('closeMessageModal', event => {
            $(".msgmodal").modal("hide");
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            
            // Show success notification
            const notification = $(`
              <div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 10000; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); animation: slideInRight 0.3s ease-out;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <strong><i class="fa fa-check-circle"></i> Success!</strong><br>
                Your message has been sent successfully.
              </div>
            `);
            
            $('body').append(notification);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
              notification.fadeOut(400, function() {
                $(this).remove();
              });
            }, 5000);
          });
          
          // Initialize custom lightbox gallery since photobox isn't working properly
          function initCustomGallery() {
            console.log('Initializing custom gallery...');
            
            // Remove any existing lightbox
            $('#customLightbox').remove();
            
            // Add mobile styles for lightbox
            if (!$('#lightboxMobileStyles').length) {
              $('head').append(`
                <style id="lightboxMobileStyles">
                  @media (max-width: 768px) {
                    #customLightbox {
                      background: rgba(0,0,0,0.9) !important;
                    }
                    #lightboxImageContainer {
                      top: 56px !important;
                      left: 0px !important;
                      right: 0px !important;
                      bottom: 90px !important;
                      background: #000 !important;
                      overflow: hidden !important;
                      height: auto !important;
                      padding: 0 !important;
                    }
                    #lightboxImageContainer::before,
                    #lightboxImageContainer::after {
                      display: none !important;
                    }
                    #lightboxImage {
                      width: 100% !important;
                      height: 100% !important;
                      object-fit: cover !important;
                      max-width: 100% !important;
                      max-height: 100% !important;
                      -webkit-mask-image: none !important;
                      mask-image: none !important;
                      border-radius: 12px !important;
                    }
                    #lightboxImageContainer {
                      position: relative !important;
                    }
                    /* Dark gradient overlay at bottom of image */
                    #lightboxImageContainer::after {
                      content: '' !important;
                      display: block !important;
                      position: absolute !important;
                      bottom: 0 !important;
                      left: 0 !important;
                      right: 0 !important;
                      height: 45% !important;
                      background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.85) 100%) !important;
                      pointer-events: none !important;
                      z-index: 2 !important;
                      border-radius: 0 0 12px 12px !important;
                    }
                    /* Profile text overlay */
                    #lightboxProfileText {
                      display: block !important;
                      position: absolute !important;
                      bottom: 12px !important;
                      left: 16px !important;
                      right: 16px !important;
                      z-index: 3 !important;
                      color: #fff !important;
                      font-size: 14px !important;
                      line-height: 1.4 !important;
                      text-shadow: 0 1px 4px rgba(0,0,0,0.6) !important;
                      white-space: normal !important;
                      overflow: hidden !important;
                      text-overflow: ellipsis !important;
                      display: -webkit-box !important;
                      -webkit-line-clamp: 2 !important;
                      -webkit-box-orient: vertical !important;
                    }
                    #lightboxPrev, #lightboxNext {
                      display: none !important;
                    }
                    #lightboxThumbnails {
                      bottom: 10px !important;
                      max-height: 70px !important;
                      padding: 8px !important;
                      left: 10px !important;
                      right: 10px !important;
                      transform: none !important;
                      max-width: none !important;
                      background: rgba(0,0,0,0.5) !important;
                      border-radius: 8px !important;
                    }
                    #lightboxThumbnails img {
                      width: 50px !important;
                      height: 50px !important;
                    }
                    #lightboxCounter {
                      bottom: 95px !important;
                      background: rgba(0,0,0,0.5) !important;
                      padding: 4px 12px !important;
                      border-radius: 12px !important;
                      font-size: 13px !important;
                    }
                    #lightboxPlay {
                      display: flex !important;
                      top: 10px !important;
                      left: 10px !important;
                      width: 36px !important;
                      height: 36px !important;
                      border-width: 1.5px !important;
                    }
                    #lightboxPlay svg {
                      width: 16px !important;
                      height: 16px !important;
                    }
                    #lightboxClose {
                      top: 10px !important;
                      right: 10px !important;
                    }
                    #lightboxImage {
                      transition: opacity 0.4s ease-in-out !important;
                    }
                    #lightboxImage.fade-out {
                      opacity: 0 !important;
                    }
                  }
                </style>
              `);
            }
            
            // Create enhanced lightbox HTML with thumbnails
            const lightboxHTML = `
              <div id="customLightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999;">
                <div id="lightboxImageContainer" style="position: absolute; top: 10%; left: 0; right: 0; bottom: 140px; display: flex; align-items: center; justify-content: center; padding: 0px; width: 100%; height: 36rem;">
                  <img id="lightboxImage" style="max-width: 100%; max-height: 100%; width: auto; height: auto; display: block; box-shadow: 0 4px 20px rgba(0,0,0,0.5); object-fit: cover; opacity: 1; transition: opacity 0.4s ease-in-out;">
                  <div id="lightboxProfileText" style="display: none; position: absolute; bottom: 12px; left: 16px; right: 16px; z-index: 3; color: #fff; font-size: 14px; line-height: 1.4;">{{ ucfirst($profile->name) }}, {{ $nationality }} escort in {{ $cityName }}</div>
                </div>
                
                <!-- Play/Stop button -->
                <div id="lightboxPlay" class="play" style="position: absolute; top: 12px; left: 12px; z-index: 999; text-align: center; cursor: pointer; color: #fff; width: 44px; height: 44px; padding: 0; transition: all 0.2s ease; background: rgba(0,0,0,0.6); border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="Click to toggle slideshow">
                  <svg id="playIconSvg" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white" stroke="none">
                    <polygon points="5,3 19,12 5,21"/>
                  </svg>
                  <svg id="stopIconSvg" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white" stroke="none" style="display:none;">
                    <rect x="4" y="4" width="16" height="16" rx="2"/>
                  </svg>
                </div>
                <!-- Close button -->
                <div id="lightboxClose"></div>
                
                <!-- Navigation arrows -->
                <div id="lightboxPrev" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #ffffff59; font-size: 14rem; cursor: pointer; z-index: 10000; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); border-radius: 50%; transition: all 0.3s ease;">&#8249;</div>
                <div id="lightboxNext" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: #ffffff59; font-size: 14rem; cursor: pointer; z-index: 10000; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); border-radius: 50%; transition: all 0.3s ease;">&#8250;</div>
                
                <!-- Thumbnails at bottom -->
                <div id="lightboxThumbnails" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; max-width: 90%; overflow-x: auto; padding: 10px; background: rgba(0,0,0,0.3); border-radius: 10px; max-height: 80px;">
                </div>
                
                <!-- Image counter -->
                <div id="lightboxCounter" style="position: absolute; bottom: 120px; left: 50%; transform: translateX(-50%); color: white; font-size: 14px; background: rgba(0,0,0,0.5); padding: 5px 15px; border-radius: 15px;"></div>
              </div>
            `;
            
            $('body').append(lightboxHTML);
            
            // Get all gallery images - use Set to avoid duplicates
            const galleryImagesSet = new Set();
            $('.pb-photo-link').each(function() {
              galleryImagesSet.add($(this).attr('href'));
            });
            const galleryImages = Array.from(galleryImagesSet);
            
            console.log('Found', galleryImages.length, 'unique gallery images');
            
            let currentImageIndex = 0;
            let slideshowInterval = null;
            let isPlaying = false;
            
            // Create thumbnail navigation
            function createThumbnails() {
              const thumbnailsContainer = $('#lightboxThumbnails');
              thumbnailsContainer.empty();
              
              galleryImages.forEach((imageSrc, index) => {
                const thumbnail = $(`
                  <img src="${imageSrc}" 
                       style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; border: 2px solid transparent; border-radius: 5px; transition: all 0.3s ease;" 
                       data-index="${index}">
                `);
                
                thumbnail.on('click', function() {
                  showImage(parseInt($(this).attr('data-index')));
                });
                
                thumbnailsContainer.append(thumbnail);
              });
            }
            
            // Update thumbnail selection
            function updateThumbnailSelection() {
              $('#lightboxThumbnails img').each(function(index) {
                if (index === currentImageIndex) {
                  $(this).css('border', '2px solid #C1F11D');
                  $(this).css('opacity', '1');
                } else {
                  $(this).css('border', '2px solid transparent');
                  $(this).css('opacity', '0.6');
                }
              });
              
              // Update counter
              $('#lightboxCounter').text(`${currentImageIndex + 1} / ${galleryImages.length}`);
            }
            
            // Function to start slideshow
            function startSlideshow() {
              if (slideshowInterval) return;
              isPlaying = true;
              $('#playIconSvg').hide();
              $('#stopIconSvg').show();
              $('#lightboxPlay').removeClass('play').css('background', 'rgba(193,241,29,0.3)');
              slideshowInterval = setInterval(() => {
                showImage(currentImageIndex + 1);
              }, 3000); // Change image every 3 seconds
            }

            // Function to stop slideshow
            function stopSlideshow() {
              if (slideshowInterval) {
                clearInterval(slideshowInterval);
                slideshowInterval = null;
              }
              isPlaying = false;
              $('#stopIconSvg').hide();
              $('#playIconSvg').show();
              $('#lightboxPlay').addClass('play').css('background', 'rgba(0,0,0,0.6)');
            }
            
            // Function to toggle slideshow
            function toggleSlideshow() {
              if (isPlaying) {
                stopSlideshow();
              } else {
                startSlideshow();
              }
            }
            
            // Function to show image with fade effect
            function showImage(index) {
              if (index < 0) index = galleryImages.length - 1;
              if (index >= galleryImages.length) index = 0;

              const isFirstOpen = !$('#customLightbox').is(':visible');
              const isSameImage = (index === currentImageIndex && !isFirstOpen);
              currentImageIndex = index;

              if (isFirstOpen || isSameImage) {
                // First open or same image - no fade needed
                $('#lightboxImage').attr('src', galleryImages[index]);
                updateThumbnailSelection();
                if (isFirstOpen) {
                  $('#customLightbox').fadeIn(300);
                  $('body').css('overflow', 'hidden');
                }
              } else {
                // Transition between images - fade out, swap, fade in
                var $img = $('#lightboxImage');
                $img.addClass('fade-out');
                setTimeout(function() {
                  var imgEl = $img[0];
                  imgEl.onload = function() {
                    // Small delay to ensure browser has painted the new image
                    setTimeout(function() { $img.removeClass('fade-out'); }, 50);
                  };
                  $img.attr('src', galleryImages[index]);
                  updateThumbnailSelection();
                  // Fallback: if image was cached, onload may fire synchronously or not at all
                  setTimeout(function() {
                    if ($img.hasClass('fade-out')) {
                      $img.removeClass('fade-out');
                    }
                  }, 800);
                }, 400);
              }
            }
            
            // Function to hide lightbox
            function hideLightbox() {
              stopSlideshow(); // Stop slideshow when closing
              $('#customLightbox').fadeOut(300);
              $('body').css('overflow', 'auto');
            }
            
            // Create thumbnails
            createThumbnails();

            // Force apply mask-image directly on the img element via JS (mobile only)
            (function() {
              var lbImg = document.getElementById('lightboxImage');
              if (lbImg && window.matchMedia('(max-width: 767px)').matches) {
                var fadeGradient = 'linear-gradient(to bottom, transparent 0%, black 6%, black 88%, transparent 100%)';
                lbImg.style.cssText += '; -webkit-mask-image: ' + fadeGradient + ' !important; mask-image: ' + fadeGradient + ' !important;';
              }
            })();

            // Bind click events to gallery links
            $('.pb-photo-link').off('click.customGallery').on('click.customGallery', function(e) {
              e.preventDefault();
              const clickedIndex = galleryImages.indexOf($(this).attr('href'));
              console.log('Opening gallery at index:', clickedIndex);
              showImage(clickedIndex);
              return false;
            });
            
            // Lightbox controls
            $('#customLightbox').off('click.customGallery').on('click.customGallery', function(e) {
              if (e.target === this) {
                hideLightbox();
              }
            });
            
            $('#lightboxPlay').off('click.customGallery').on('click.customGallery', function(e) {
              e.stopPropagation();
              toggleSlideshow();
            });
            
            $('#lightboxClose').off('click.customGallery').on('click.customGallery', function(e) {
              e.stopPropagation();
              hideLightbox();
            });
            
            $('#lightboxPrev').off('click.customGallery').on('click.customGallery', function(e) {
              e.stopPropagation();
              showImage(currentImageIndex - 1);
            });
            
            $('#lightboxNext').off('click.customGallery').on('click.customGallery', function(e) {
              e.stopPropagation();
              showImage(currentImageIndex + 1);
            });
            
            // Touch/Swipe support for mobile
            let touchStartX = 0;
            let touchStartY = 0;
            let touchEndX = 0;
            let touchEndY = 0;
            
            $('#customLightbox').on('touchstart', function(e) {
              touchStartX = e.originalEvent.touches[0].clientX;
              touchStartY = e.originalEvent.touches[0].clientY;
            });
            
            $('#customLightbox').on('touchend', function(e) {
              touchEndX = e.originalEvent.changedTouches[0].clientX;
              touchEndY = e.originalEvent.changedTouches[0].clientY;
              handleSwipe();
            });
            
            function handleSwipe() {
              const swipeThreshold = 50;
              const diffX = touchStartX - touchEndX;
              const diffY = touchStartY - touchEndY;
              
              // Only handle horizontal swipes (ignore vertical)
              if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > swipeThreshold) {
                if (diffX > 0) {
                  // Swipe left - next image
                  showImage(currentImageIndex + 1);
                } else {
                  // Swipe right - previous image
                  showImage(currentImageIndex - 1);
                }
              }
            }
            
            // Hover effects for navigation buttons
            {{-- $('#lightboxPrev, #lightboxNext').hover(
              function() {
                $(this).css('background', 'rgba(255,255,255,0.2)');
                $(this).css('transform', 'translateY(-50%) scale(1.1)');
              },
              function() {
                $(this).css('background', 'rgba(0,0,0,0.5)');
                $(this).css('transform', 'translateY(-50%) scale(1)');
              }
            ); --}}
            
            $('#lightboxClose').hover(
              function() {
                $(this).css('background', 'rgba(255,0,0,0.3)');
                $(this).css('transform', 'scale(1.1)');
              },
              function() {
                $(this).css('background', 'rgba(0,0,0,0.3)');
                $(this).css('transform', 'scale(1)');
              }
            );
            
            // Keyboard controls
            $(document).off('keydown.customGallery').on('keydown.customGallery', function(e) {
              if ($('#customLightbox').is(':visible')) {
                if (e.keyCode === 27) hideLightbox(); // ESC
                if (e.keyCode === 37) showImage(currentImageIndex - 1); // Left arrow
                if (e.keyCode === 39) showImage(currentImageIndex + 1); // Right arrow
                if (e.keyCode === 32) { // Spacebar
                  e.preventDefault();
                  toggleSlideshow();
                }
              }
            });
            
            console.log('Enhanced custom gallery initialized successfully');
          }
          
          // Initialize custom gallery
          initCustomGallery();
      }
    }, 100);
  }
  
  // Initialize on all possible events but remove Livewire dependency
  document.addEventListener('livewire:init', initProfileDetails);
  document.addEventListener('livewire:navigated', initProfileDetails);
  document.addEventListener('DOMContentLoaded', initProfileDetails);
  
  // Try Livewire events only if Livewire is available
  if (typeof window.Livewire !== 'undefined') {
    window.Livewire.on('profile-loaded', initProfileDetails);
  }
  
  // Additional fallback initialization
  window.addEventListener('load', function() {
    console.log('Window load event - initializing profile details');
    setTimeout(initProfileDetails, 1000);
  });
  
  // Also try with jQuery ready
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event - initializing profile details');
    setTimeout(initProfileDetails, 500);
  });
</script>

<script>
// Custom Select2 Implementation
class CustomSelect2 {
    constructor(selectElement, options = {}) {
        this.selectElement = selectElement;
        this.options = {
            placeholder: options.placeholder || selectElement.getAttribute('data-placeholder') || 'Select...',
            searchable: options.searchable !== false,
            width: options.width || '100%'
        };
        
        this.init();
    }
    
    init() {
        // Hide original select
        this.selectElement.style.display = 'none';
        
        // Create custom select container
        this.container = document.createElement('div');
        this.container.className = 'custom-select2';
        this.container.style.width = this.options.width;
        
        // Create selection box
        this.selectionBox = document.createElement('div');
        this.selectionBox.className = 'custom-select2-selection';
        this.selectionBox.innerHTML = `<span class="custom-select2-placeholder">${this.options.placeholder}</span>`;
        
        // Create dropdown
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'custom-select2-dropdown';
        
        // Create search if enabled
        if (this.options.searchable) {
            const searchWrapper = document.createElement('div');
            searchWrapper.className = 'custom-select2-search';
            this.searchInput = document.createElement('input');
            this.searchInput.type = 'text';
            this.searchInput.placeholder = 'Search...';
            searchWrapper.appendChild(this.searchInput);
            this.dropdown.appendChild(searchWrapper);
        }
        
        // Create results list
        this.resultsList = document.createElement('ul');
        this.resultsList.className = 'custom-select2-results';
        this.dropdown.appendChild(this.resultsList);
        
        // Append elements
        this.container.appendChild(this.selectionBox);
        this.container.appendChild(this.dropdown);
        this.selectElement.parentNode.insertBefore(this.container, this.selectElement.nextSibling);
        
        // Populate options
        this.populateOptions();
        
        // Bind events
        this.bindEvents();
    }
    
    populateOptions() {
        this.resultsList.innerHTML = '';
        const options = this.selectElement.querySelectorAll('option');
        
        options.forEach((option, index) => {
            if (index === 0 && option.value === '') return; // Skip placeholder option
            
            const li = document.createElement('li');
            li.className = 'custom-select2-option';
            li.textContent = option.textContent;
            li.dataset.value = option.value;
            
            if (option.selected) {
                li.classList.add('selected');
                const codeMatch = option.textContent.trim().match(/^(\\+\\d+)/);
                const displayText = codeMatch ? codeMatch[1] : option.textContent.split('-')[0].trim();
                this.selectionBox.innerHTML = displayText;
            }
            
            this.resultsList.appendChild(li);
        });
    }
    
    bindEvents() {
        // Toggle dropdown
        this.selectionBox.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleDropdown();
        });
        
        // Search functionality
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                this.filterOptions(e.target.value);
            });
            this.searchInput.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
        
        // Option selection
        this.resultsList.addEventListener('click', (e) => {
            if (e.target.classList.contains('custom-select2-option')) {
                this.selectOption(e.target);
                this.closeDropdown();
            }
        });
        
        // Close on outside click
        const outsideClickHandler = (e) => {
            if (!this.container.contains(e.target)) {
                this.closeDropdown();
            }
        };
        
        document.addEventListener('click', outsideClickHandler);
        
        // Store the handler so we can remove it later if needed
        this.outsideClickHandler = outsideClickHandler;
    }
    
    toggleDropdown() {
        const isOpen = this.dropdown.classList.contains('open');
        if (isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        // Close all other open dropdowns first
        document.querySelectorAll('.custom-select2.open').forEach(openContainer => {
            openContainer.querySelector('.custom-select2-dropdown').classList.remove('open');
            openContainer.querySelector('.custom-select2-selection').classList.remove('open');
            openContainer.classList.remove('open');
        });
        
        this.dropdown.classList.add('open');
        this.selectionBox.classList.add('open');
        this.container.classList.add('open');
        if (this.searchInput) {
            this.searchInput.focus();
            this.searchInput.value = '';
            this.filterOptions('');
        }
    }
    
    closeDropdown() {
        this.dropdown.classList.remove('open');
        this.selectionBox.classList.remove('open');
        this.container.classList.remove('open');
    }
    
    filterOptions(searchTerm) {
        const options = this.resultsList.querySelectorAll('.custom-select2-option');
        const term = searchTerm.toLowerCase();
        
        options.forEach(option => {
            const text = option.textContent.toLowerCase();
            if (text.includes(term)) {
                option.classList.remove('hidden');
            } else {
                option.classList.add('hidden');
            }
        });
    }
    
    selectOption(optionElement, triggerChange = true) {
        // Remove previous selection
        const previousSelected = this.resultsList.querySelector('.custom-select2-option.selected');
        if (previousSelected) {
            previousSelected.classList.remove('selected');
        }
        
        // Add new selection
        optionElement.classList.add('selected');
        
        // Update selection box - extract only the code part (e.g., "+93" from "+93 - Afghanistan")
        const fullText = optionElement.textContent.trim();
        const codeMatch = fullText.match(/^(\\+\\d+)/);
        const displayText = codeMatch ? codeMatch[1] : fullText.split('-')[0].trim();
        this.selectionBox.innerHTML = displayText;
        
        // Update original select
        this.selectElement.value = optionElement.dataset.value;
        
        // Trigger change event
        if (triggerChange) {
            const event = new Event('change', { bubbles: true });
            this.selectElement.dispatchEvent(event);
            
            // Also trigger Livewire update if available
            if (this.selectElement.hasAttribute('wire:model')) {
                this.selectElement.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    }
    
    destroy() {
        if (this.container && this.container.parentNode) {
            this.container.parentNode.removeChild(this.container);
        }
        this.selectElement.style.display = '';
    }
}

// Initialize custom select2 for message modal
function initializeCustomSelect2() {
    console.log('Initializing Custom Select2 for message modal...');
    
    // Find all selects with the class .apply-custom-select2
    const customSelects = document.querySelectorAll('select.apply-custom-select2');
    
    customSelects.forEach(select => {
        if (!select.customSelect2Instance) {
            console.log('Creating Custom Select2 for:', select.id);
            
            const options = {
                placeholder: select.getAttribute('data-placeholder') || 'Select...',
                searchable: select.getAttribute('data-searchable') !== 'false',
                width: select.style.width || '120px'
            };
            
            select.customSelect2Instance = new CustomSelect2(select, options);
        }
    });
    
    console.log('Custom Select2 initialized for', customSelects.length, 'dropdowns');
}

// Initialize when message modal is shown
$(document).on('click', '.send-message1', function() {
    setTimeout(function() {
        initializeCustomSelect2();
    }, 300);
});

// Also initialize on modal shown event
$('.msgmodal').on('shown.bs.modal', function() {
    setTimeout(function() {
        initializeCustomSelect2();
    }, 100);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeCustomSelect2, 500);
});

// Initialize Bootstrap tabs explicitly for mobile compatibility
$(document).ready(function() {
    // Ensure tab functionality works on all devices
    $('#revsAndQs .nav-tabs a[data-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        
        // Hide all tab panes with !important
        $('#revsAndQs .tab-content .tab-pane').removeClass('in active').attr('style', 'display: none !important');
        
        // Show the clicked tab pane
        var target = $(this).attr('href');
        $(target).addClass('in active').attr('style', 'display: block !important');
        
        // Update active state on tabs
        $('#revsAndQs .nav-tabs li').removeClass('active');
        $(this).parent('li').addClass('active');
    });
    
    // Ensure only the active tab content is visible on page load
    $('#revsAndQs .tab-content .tab-pane').removeClass('in active').attr('style', 'display: none !important');
    $('#listingReviews').addClass('in active').attr('style', 'display: block !important');
    $('#revsAndQs .nav-tabs li').removeClass('active');
    $('#revsAndQs .nav-tabs li:first-child').addClass('active');
});

// WhatsApp Rotation Message Handler
function handleWhatsAppClick(event, element) {
    event.preventDefault();
    
    var profileId = element.getAttribute('data-profile-id');
    var phone = element.getAttribute('data-phone');
    var profileUrl = element.getAttribute('data-profile-url');
    var fallbackMessage = 'Hi, I found your profile on MassageRepublic: ' + profileUrl;
    
    // Fetch the next rotation message from API
    fetch('/api/whatsapp-message/' + profileId + '?url=' + encodeURIComponent(profileUrl))
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            var message = data.message || fallbackMessage;
            var whatsappUrl = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
            window.open(whatsappUrl, '_blank');
        })
        .catch(function(error) {
            console.error('Error fetching rotation message:', error);
            // Fallback to default message if API fails
            var whatsappUrl = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(fallbackMessage);
            window.open(whatsappUrl, '_blank');
        });
}
</script>
@endpush
