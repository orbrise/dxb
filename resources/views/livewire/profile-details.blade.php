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
        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 6H11.25M4.5 9H11.25M4.5 12H8.25M15.75 8.25C15.75 12.3922 12.3922 15.75 8.25 15.75H0.75V8.25C0.75 4.10775 4.10775 0.75 8.25 0.75C12.3922 0.75 15.75 4.10775 15.75 8.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
        <svg width="17" height="17" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M16.3125 9C16.3125 10.9394 15.5421 12.7994 14.1707 14.1707C12.7994 15.5421 10.9394 16.3125 9 16.3125C7.0606 16.3125 5.20064 15.5421 3.82928 14.1707C2.45792 12.7994 1.6875 10.9394 1.6875 9C1.6875 7.0606 2.45792 5.20064 3.82928 3.82928C5.20064 2.45792 7.0606 1.6875 9 1.6875C10.9394 1.6875 12.7994 2.45792 14.1707 3.82928C15.5421 5.20064 16.3125 7.0606 16.3125 9ZM18 9C18 11.3869 17.0518 13.6761 15.364 15.364C13.6761 17.0518 11.3869 18 9 18C6.61305 18 4.32387 17.0518 2.63604 15.364C0.948212 13.6761 0 11.3869 0 9C0 6.61305 0.948212 4.32387 2.63604 2.63604C4.32387 0.948212 6.61305 0 9 0C11.3869 0 13.6761 0.948212 15.364 2.63604C17.0518 4.32387 18 6.61305 18 9ZM5.54288 5.61375C5.22263 6.09675 5.0625 6.573 5.0625 7.0425C5.0625 7.27125 5.16375 7.4835 5.36625 7.67925C5.56875 7.875 5.81663 7.9725 6.10988 7.97175C6.60863 7.97175 6.94725 7.692 7.12575 7.1325C7.31475 6.59775 7.54575 6.19275 7.81875 5.9175C8.09175 5.643 8.517 5.50575 9.0945 5.50575C9.588 5.50575 9.99113 5.64187 10.3039 5.91412C10.6159 6.18712 10.7719 6.52163 10.7719 6.91763C10.7732 7.11629 10.7203 7.31156 10.6189 7.48238C10.5146 7.65578 10.3875 7.81433 10.2409 7.95375C10.0043 8.17037 9.76121 8.37972 9.51188 8.5815C9.12937 8.89875 8.82487 9.1725 8.59838 9.40275C8.37337 9.63375 8.19225 9.90112 8.055 10.2049C7.69275 11.6055 9.57375 11.718 10.008 10.7179C10.0605 10.6219 10.1404 10.5154 10.2476 10.3984C10.3556 10.2821 10.4989 10.1471 10.6774 9.99338C11.1326 9.61429 11.5804 9.22646 12.0206 8.83012C12.2696 8.60063 12.4845 8.32687 12.6653 8.00887C12.852 7.67031 12.9459 7.28842 12.9375 6.90188C12.9375 6.36788 12.7785 5.87288 12.4605 5.41687C12.1433 4.96012 11.6933 4.59937 11.1105 4.33462C10.5278 4.06987 9.85575 3.9375 9.0945 3.9375C8.2755 3.9375 7.55888 4.09613 6.94462 4.41337C6.33037 4.73063 5.86312 5.1315 5.54288 5.61375ZM7.95038 13.5787C7.95038 13.8771 8.0689 14.1633 8.27988 14.3742C8.49086 14.5852 8.77701 14.7037 9.07538 14.7037C9.37374 14.7037 9.65989 14.5852 9.87087 14.3742C10.0818 14.1633 10.2004 13.8771 10.2004 13.5787C10.2004 13.2804 10.0818 12.9942 9.87087 12.7833C9.65989 12.5723 9.37374 12.4537 9.07538 12.4537C8.77701 12.4537 8.49086 12.5723 8.27988 12.7833C8.0689 12.9942 7.95038 13.2804 7.95038 13.5787Z" fill="currentColor"/></svg>
        <span>Ask Question</span>
      </a>
      <a class="list-group-item add-review1" href="javascript:void(0)">
        <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.25 10.3192H5.63125C5.74458 10.3192 5.85452 10.2977 5.96105 10.2547C6.06758 10.2117 6.16307 10.1472 6.2475 10.0612L10.2425 6.01952C10.37 5.89053 10.4658 5.74348 10.5298 5.57837C10.5938 5.41327 10.6256 5.25217 10.625 5.09509C10.6244 4.93801 10.589 4.78408 10.5187 4.63331C10.4485 4.48253 10.3564 4.34265 10.2425 4.21366L9.4775 3.39673C9.35 3.26774 9.20833 3.17114 9.0525 3.10693C8.89667 3.04272 8.73375 3.01033 8.56375 3.00976C8.40792 3.00976 8.24868 3.04215 8.08605 3.10693C7.92342 3.17171 7.77807 3.26831 7.65 3.39673L3.655 7.4384C3.57 7.5244 3.50625 7.62128 3.46375 7.72906C3.42125 7.83684 3.4 7.94777 3.4 8.06185V9.45924C3.4 9.70289 3.4816 9.90727 3.6448 10.0724C3.808 10.2375 4.00973 10.3197 4.25 10.3192ZM4.675 9.02928V8.21234L6.82125 6.04102L7.24625 6.42799L7.62875 6.85795L5.4825 9.02928H4.675ZM7.24625 6.42799L7.62875 6.85795L6.82125 6.04102L7.24625 6.42799ZM7.79875 10.3192H12.75C12.9908 10.3192 13.1928 10.2366 13.356 10.0715C13.5192 9.90641 13.6006 9.70232 13.6 9.45924C13.5994 9.21617 13.5178 9.01208 13.3552 8.84697C13.1926 8.68186 12.9908 8.59931 12.75 8.59931H9.49875L7.79875 10.3192ZM3.4 13.7589L1.445 15.7367C1.17583 16.0091 0.867568 16.0701 0.520201 15.9199C0.172835 15.7697 -0.000565282 15.5008 1.38436e-06 15.1133V1.71986C1.38436e-06 1.2469 0.166601 0.842159 0.499801 0.50564C0.833001 0.16912 1.23307 0.000573287 1.7 0H15.3C15.7675 0 16.1678 0.168547 16.501 0.50564C16.8342 0.842733 17.0006 1.24747 17 1.71986V12.039C17 12.512 16.8337 12.917 16.501 13.2541C16.1684 13.5912 15.7681 13.7595 15.3 13.7589H3.4ZM2.6775 12.039H15.3V1.71986H1.7V13.0065L2.6775 12.039Z" fill="currentColor"/></svg>
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

              {{-- Mobile action menu bar (between photos and price section).
                   Re-uses the same handler classes as the old sticky bar
                   that we removed earlier — Phone/Message open the call
                   sheet and message modal, Ask/Review open their modals. --}}
              <div class="ev-profile-actions visible-xs">
                <a href="javascript:void(0)" class="ev-profile-actions__btn ev-profile-actions__btn--filled contact-phone1">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  <span>Phone</span>
                </a>
                <a href="javascript:void(0)" class="ev-profile-actions__btn ev-profile-actions__btn--filled send-message1">
                  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 5.3H9.85M4 7.9H9.85M4 10.5H7.25M13.75 7.25C13.75 10.8399 10.8399 13.75 7.25 13.75H0.75V7.25C0.75 3.66005 3.66005 0.75 7.25 0.75C10.8399 0.75 13.75 3.66005 13.75 7.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  <span>Message</span>
                </a>
                <a href="javascript:void(0)" class="ev-profile-actions__btn ev-profile-actions__btn--ghost ask-question1">
                  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M13.5938 7.5C13.5938 9.11616 12.9517 10.6661 11.8089 11.8089C10.6661 12.9517 9.11616 13.5938 7.5 13.5938C5.88384 13.5938 4.33387 12.9517 3.19107 11.8089C2.04827 10.6661 1.40625 9.11616 1.40625 7.5C1.40625 5.88384 2.04827 4.33387 3.19107 3.19107C4.33387 2.04827 5.88384 1.40625 7.5 1.40625C9.11616 1.40625 10.6661 2.04827 11.8089 3.19107C12.9517 4.33387 13.5938 5.88384 13.5938 7.5ZM15 7.5C15 9.48912 14.2098 11.3968 12.8033 12.8033C11.3968 14.2098 9.48912 15 7.5 15C5.51088 15 3.60322 14.2098 2.1967 12.8033C0.790176 11.3968 0 9.48912 0 7.5C0 5.51088 0.790176 3.60322 2.1967 2.1967C3.60322 0.790176 5.51088 0 7.5 0C9.48912 0 11.3968 0.790176 12.8033 2.1967C14.2098 3.60322 15 5.51088 15 7.5ZM4.61906 4.67812C4.35219 5.08062 4.21875 5.4775 4.21875 5.86875C4.21875 6.05938 4.30312 6.23625 4.47188 6.39937C4.64062 6.5625 4.84719 6.64375 5.09156 6.64313C5.50719 6.64313 5.78938 6.41 5.93813 5.94375C6.09562 5.49813 6.28813 5.16062 6.51562 4.93125C6.74312 4.7025 7.0975 4.58813 7.57875 4.58813C7.99 4.58813 8.32594 4.70156 8.58656 4.92844C8.84656 5.15594 8.97656 5.43469 8.97656 5.76469C8.9777 5.93024 8.93361 6.09297 8.84906 6.23531C8.7622 6.37982 8.65625 6.51195 8.53406 6.62813C8.33694 6.80865 8.13434 6.9831 7.92656 7.15125C7.60781 7.41562 7.35406 7.64375 7.16531 7.83562C6.97781 8.02812 6.82687 8.25094 6.7125 8.50406C6.41062 9.67125 7.97813 9.765 8.34 8.93156C8.38375 8.85156 8.45031 8.76281 8.53969 8.66531C8.62969 8.56844 8.74906 8.45594 8.89781 8.32781C9.27713 8.01191 9.65032 7.68872 10.0172 7.35844C10.2247 7.16719 10.4038 6.93906 10.5544 6.67406C10.71 6.39193 10.7882 6.07368 10.7812 5.75156C10.7812 5.30656 10.6488 4.89406 10.3837 4.51406C10.1194 4.13344 9.74438 3.83281 9.25875 3.61219C8.77313 3.39156 8.21312 3.28125 7.57875 3.28125C6.89625 3.28125 6.29906 3.41344 5.78719 3.67781C5.27531 3.94219 4.88594 4.27625 4.61906 4.67812ZM6.62531 11.3156C6.62531 11.5643 6.72408 11.8027 6.8999 11.9785C7.07572 12.1544 7.31417 12.2531 7.56281 12.2531C7.81145 12.2531 8.04991 12.1544 8.22573 11.9785C8.40154 11.8027 8.50031 11.5643 8.50031 11.3156C8.50031 11.067 8.40154 10.8285 8.22573 10.6527C8.04991 10.4769 7.81145 10.3781 7.56281 10.3781C7.31417 10.3781 7.07572 10.4769 6.8999 10.6527C6.72408 10.8285 6.62531 11.067 6.62531 11.3156Z" fill="currentColor"/></svg>
                  <span>Ask</span>
                </a>
                <a href="javascript:void(0)" class="ev-profile-actions__btn ev-profile-actions__btn--ghost add-review1">
                  <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.75 9.02928H4.96875C5.06875 9.02928 5.16575 9.01047 5.25975 8.97285C5.35375 8.93522 5.438 8.87879 5.5125 8.80355L9.0375 5.26708C9.15 5.15421 9.2345 5.02555 9.291 4.88108C9.3475 4.73661 9.3755 4.59565 9.375 4.45821C9.3745 4.32076 9.34325 4.18607 9.28125 4.05415C9.21925 3.92222 9.138 3.79982 9.0375 3.68695L8.3625 2.97214C8.25 2.85927 8.125 2.77475 7.9875 2.71856C7.85 2.66238 7.70625 2.63404 7.55625 2.63354C7.41875 2.63354 7.27825 2.66188 7.13475 2.71856C6.99125 2.77525 6.863 2.85977 6.75 2.97214L3.225 6.5086C3.15 6.58385 3.09375 6.66862 3.05625 6.76293C3.01875 6.85723 3 6.9543 3 7.05412V8.27684C3 8.49003 3.072 8.66886 3.216 8.81333C3.36 8.9578 3.538 9.02978 3.75 9.02928ZM4.125 7.90062V7.1858L6.01875 5.28589L6.39375 5.62449L6.73125 6.00071L4.8375 7.90062H4.125ZM6.39375 5.62449L6.73125 6.00071L6.01875 5.28589L6.39375 5.62449ZM6.88125 9.02928H11.25C11.4625 9.02928 11.6407 8.95704 11.7847 8.81257C11.9287 8.66811 12.0005 8.48953 12 8.27684C11.9995 8.06415 11.9275 7.88557 11.784 7.7411C11.6405 7.59663 11.4625 7.5244 11.25 7.5244H8.38125L6.88125 9.02928ZM3 12.039L1.275 13.7696C1.0375 14.0079 0.765501 14.0613 0.459001 13.9299C0.152501 13.7985 -0.000498778 13.5632 1.2215e-06 13.2241V1.50488C1.2215e-06 1.09104 0.147001 0.736889 0.441001 0.442435C0.735001 0.14798 1.088 0.000501627 1.5 0H13.5C13.9125 0 14.2657 0.147478 14.5597 0.442435C14.8537 0.737391 15.0005 1.09154 15 1.50488V10.5342C15 10.948 14.8532 11.3024 14.5597 11.5974C14.2662 11.8923 13.913 12.0395 13.5 12.039H3ZM2.3625 10.5342H13.5V1.50488H1.5V11.3807L2.3625 10.5342Z" fill="currentColor"/></svg>
                  <span>Review</span>
                </a>
              </div>
              <style>
                /* `visible-xs` is also on this container, and the legacy
                   Bootstrap bundle forces `.visible-xs { display: block !important }`
                   under 767px. That kills our flex layout, so the buttons size to
                   their text and end up unequal. Override with !important here. */
                /* Desktop: keep the bar completely hidden (it's marked
                   .visible-xs and is for mobile only). */
                .ev-profile-actions { display: none !important; }

                @media (max-width: 767px) {
                  .ev-profile-actions,
                  .ev-profile-actions.visible-xs {
                    display: flex !important;
                    align-items: stretch;
                    gap: 8px;
                    padding: 14px 0px;
                    background: #2a3505;
                    margin-top: 18px;
                    margin-bottom: 4px;
                    box-sizing: border-box;
                    /* box-shadow trick: a massive spread paints the bar's colour
                       out to ~100vmax in every direction, while `clip-path` keeps
                       the paint only on the horizontal axis. */
                    box-shadow: 0 0 0 100vmax #2a3505;
                    -webkit-clip-path: inset(0 -100vmax);
                    clip-path: inset(0 -100vmax);
                  }
                  /* For the box-shadow paint to be visible, no ancestor may have
                     overflow:hidden. The legacy listing bundle sets it on a few
                     ancestors — override on mobile only, scoped to this page. */
                  body:has(.ev-profile-actions) article[data-listing],
                  body:has(.ev-profile-actions) article[data-listing] > .row,
                  body:has(.ev-profile-actions) #listing-content,
                  body:has(.ev-profile-actions) .listing-li,
                  body:has(.ev-profile-actions) .content-wrapper,
                  body:has(.ev-profile-actions) #content,
                  body:has(.ev-profile-actions) .container-fluid,
                  body:has(.ev-profile-actions) main {
                    overflow: visible !important;
                    overflow-x: visible !important;
                  }
                }
                .ev-profile-actions__btn {
                  display: inline-flex !important;
                  align-items: center;
                  justify-content: center;
                  gap: 5px;
                  flex: 1 1 0;
                  min-width: 0;
                  height: 38px;
                  padding: 0 10px;
                  border-radius: 5px;
                  font-size: 15px;
                  font-weight: 500;
                  text-decoration: none !important;
                  white-space: nowrap;
                  box-sizing: border-box;
                  line-height: 1;
                  transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
                }
                .ev-profile-actions__btn svg {
                  width: 20px !important;
                  height: 20px !important;
                  transform: none !important;
                  -webkit-transform: none !important;
                  margin: 0 !important;
                  display: block;
                  flex-shrink: 0;
                }
                .ev-profile-actions__btn span {
                  display: inline-block;
                  overflow: hidden;
                  text-overflow: ellipsis;
                }
                .ev-profile-actions__btn--filled {
                  background: #C1F11D;
                  color: #000 !important;
                  border: 1px solid #C1F11D;
                  font-size: 12px;
                }
                .ev-profile-actions__btn--filled:hover,
                .ev-profile-actions__btn--filled:focus {
                  background: #b5e600;
                  border-color: #b5e600;
                  color: #000 !important;
                }
                .ev-profile-actions__btn--ghost {
                  background: transparent;
                  color: #fff !important;
                  border: 1px solid transparent;
                }
                .ev-profile-actions__btn--ghost:hover,
                .ev-profile-actions__btn--ghost:focus {
                  background: transparent;
                  border-color: transparent;
                  color: #C1F11D !important;
                }
              </style>

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
                   Hidden when: (1) the profile has already been claimed
                   (any verified_at row in profile_claim_attempts), or
                   (2) the viewer is signed in and is the profile owner
                   or an admin. The three-step modal (phone → code →
                   success) lives further down inside #profile-claim-modal. --}}
              @if(!($profileClaimed ?? false) && (!Auth::check() || (Auth::id() !== ($user->id ?? null) && Auth::user()?->type != 1)))
              <div class="ev-claim-card" id="ev-claim-card">
                  <div class="ev-claim-card-inner">
                      <div class="ev-claim-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                      </div>
                      <div class="ev-claim-text">
                          <h4>Is This Your Profile?</h4>
                          <p>If this listing belongs to you but was posted by someone else, you can verify your ownership and claim it. We will transfer it securely to your original account.</p>
                          <button type="button" class="ev-claim-cta" onclick="var m=document.getElementById('profile-claim-modal');m.removeAttribute('hidden');m.classList.add('is-open');document.body.style.overflow='hidden';">Claim Now</button>
                      </div>
                  </div>
              </div>

              {{-- Claim modal — three stacked panels controlled by the
                   data-step attribute set in JS: phone → code → success.
                   wire:ignore is critical: without it a Livewire re-render
                   from any other component on the page (review/askq/msg
                   modals sharing the same view) morphs this DOM subtree
                   and reverts our client-side setStep('code') back to
                   the phone step, making the modal appear stuck. --}}
              <div class="ev-claim-modal" id="profile-claim-modal" data-step="phone" data-profile-id="{{ $user->id ?? '' }}" data-claim-js-version="v3-poll" role="dialog" aria-modal="true" aria-labelledby="ev-claim-title" hidden wire:ignore>
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
                          {{-- Inline color/background overrides the site's
                               global "a { color: … }" rule, which was
                               matching the lime background and hiding the
                               label on both desktop and mobile. --}}
                          <a href="{{ url('my-account') }}" class="ev-claim-primary ev-claim-primary--center"
                             style="color:#000 !important;background:#C1F11D !important;text-decoration:none !important;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 24px;border-radius:24px;font-weight:600;font-size:14px;margin:8px auto 0;">
                              Go To My Dashboard <span aria-hidden="true" style="color:#000 !important;">›</span>
                          </a>
                      </div>

                  </div>
              </div>

              {{-- Invisible reCAPTCHA host for Firebase Phone Auth. Sits
                   at the top-level of the page (not inside the modal) so
                   Firebase's iframe insertion, badge rendering, and any
                   fallback challenge don't fight the modal's overflow /
                   z-index. Zero-size containers cause the SDK to hang on
                   some versions, so it has real dimensions and is hidden
                   via visibility, not display. --}}
              <div id="ev-claim-recaptcha" style="position:fixed;bottom:0;right:0;width:80px;height:80px;visibility:hidden;pointer-events:none;"></div>

              <style>
                  /* Claim card (shown inside About tab content) */
                  .ev-claim-card {
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

              {{-- Firebase Phone Auth SDK (v8 compat build — same as used
                   in the oobben project). Only loaded when the SMS channel
                   is available (project id configured). If the config is
                   missing we skip the SDK entirely and the SMS radio
                   silently falls back to a "not configured" message so the
                   WhatsApp channel keeps working. --}}
              @php
                  $firebaseWebConfig = [
                      'apiKey' => config('services.firebase.api_key'),
                      'authDomain' => config('services.firebase.auth_domain'),
                      'projectId' => config('services.firebase.project_id'),
                      'appId' => config('services.firebase.app_id'),
                  ];
                  $firebaseEnabled = !empty($firebaseWebConfig['apiKey']) && !empty($firebaseWebConfig['projectId']);
              @endphp
              @if($firebaseEnabled)
              <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
              <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-auth.js"></script>
              @endif

              <script>
                  (function () {
                      var modal = document.getElementById('profile-claim-modal');
                      if (!modal || modal.__bound) return;
                      modal.__bound = true;

                      var profileId = modal.getAttribute('data-profile-id');
                      var csrf = document.querySelector('meta[name="csrf-token"]')
                          ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                          : @json(csrf_token());

                      var firebaseConfig = @json($firebaseWebConfig);
                      var firebaseEnabled = @json($firebaseEnabled);
                      var firebaseApp = null;         // firebase.app() handle
                      var firebaseRecaptcha = null;   // RecaptchaVerifier instance
                      var firebaseConfirmation = null; // confirmationResult from signInWithPhoneNumber

                      // Lightweight console-only debug helper so we can
                      // trace flow if something regresses. No DOM output.
                      function debug(msg) {
                          try { console.log('[claim] ' + msg); } catch (e) {}
                      }

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

                      // Lazy-init Firebase. We don't do this on modal open
                      // because the SDK may not be present (config missing)
                      // and the RecaptchaVerifier attaches to a real DOM
                      // node which we only need once the user actually
                      // hits Continue on the SMS channel.
                      function ensureFirebase() {
                          if (!firebaseEnabled) return null;
                          if (typeof firebase === 'undefined' || !firebase.auth) return null;
                          if (!firebaseApp) {
                              try {
                                  firebaseApp = firebase.apps && firebase.apps.length
                                      ? firebase.app()
                                      : firebase.initializeApp(firebaseConfig);
                              } catch (e) {
                                  console.error('Firebase init failed', e);
                                  return null;
                              }
                          }
                          if (!firebaseRecaptcha) {
                              try {
                                  firebaseRecaptcha = new firebase.auth.RecaptchaVerifier('ev-claim-recaptcha', {
                                      size: 'invisible',
                                  });
                              } catch (e) {
                                  console.error('Firebase recaptcha init failed', e);
                                  return null;
                              }
                          }
                          return firebase.auth();
                      }

                      async function sendOtp(btn) {
                          var phone = getFullPhone();
                          var email = getVal('#ev-claim-email');
                          var countryCode = getVal('#ev-claim-dial').replace(/\D/g, '');
                          var channel = getChannel();
                          if (!phone || phone.replace(/\D/g, '').length < 7) {
                              showError('Please enter a valid phone number.');
                              return;
                          }
                          if (!email || email.indexOf('@') === -1) {
                              showError('Please enter a valid email address.');
                              return;
                          }

                          busy(btn, true);

                          if (channel === 'sms') {
                              debug('SMS flow start · phone=' + phone);
                              if (!firebaseEnabled) {
                                  busy(btn, false);
                                  debug('firebase not enabled (config missing)');
                                  showError('SMS is not available right now. Please use WhatsApp.');
                                  return;
                              }
                              debug('calling precheck-sms endpoint');
                              var pre = await api('/profile/' + profileId + '/claim/precheck-sms', {
                                  phone: phone, country_code: countryCode, email: email,
                              });
                              debug('precheck response ok=' + pre.ok + ' status=' + pre.status);
                              if (!pre.ok) {
                                  busy(btn, false);
                                  showError((pre.data && pre.data.message) || 'Could not start SMS verification.');
                                  return;
                              }
                              var auth = ensureFirebase();
                              debug('ensureFirebase auth=' + !!auth + ' recaptcha=' + !!firebaseRecaptcha);
                              if (!auth) {
                                  busy(btn, false);
                                  showError('SMS verification is not configured. Please use WhatsApp.');
                                  return;
                              }
                              // Non-await style: kick off signInWithPhoneNumber
                              // and poll for either the confirmation result
                              // or an error to appear. Polling side-steps
                              // any async/await plumbing issues.
                              debug('calling signInWithPhoneNumber…');
                              window.firebaseClaimConfirmation = null;
                              window.firebaseClaimError = null;
                              try {
                                  auth.signInWithPhoneNumber(phone, firebaseRecaptcha)
                                      .then(function (result) {
                                          debug('signInWithPhoneNumber RESOLVED · sessionInfo present=' + !!(result && result.verificationId));
                                          window.firebaseClaimConfirmation = result;
                                      })
                                      .catch(function (err) {
                                          debug('signInWithPhoneNumber REJECTED · ' + (err && err.code) + ' · ' + (err && err.message));
                                          window.firebaseClaimError = err;
                                      });
                              } catch (err) {
                                  busy(btn, false);
                                  debug('signInWithPhoneNumber THREW sync · ' + (err && err.message));
                                  showError((err && err.message) || 'Could not send the SMS code.');
                                  return;
                              }

                              debug('polling for confirmation…');
                              var pollStart = Date.now();
                              var pollTicks = 0;
                              var poll = setInterval(function () {
                                  pollTicks++;
                                  if (window.firebaseClaimConfirmation) {
                                      clearInterval(poll);
                                      firebaseConfirmation = window.firebaseClaimConfirmation;
                                      busy(btn, false);
                                      var mask1 = modal.querySelector('[data-claim-phone-mask]');
                                      if (mask1) mask1.textContent = maskPhone(phone);
                                      debug('advancing to code step after ' + pollTicks + ' polls');
                                      setStep('code');
                                      var codePanel = modal.querySelector('[data-step-panel="code"]');
                                      var phonePanel = modal.querySelector('[data-step-panel="phone"]');
                                      if (codePanel) { codePanel.removeAttribute('hidden'); codePanel.style.display = ''; }
                                      if (phonePanel) { phonePanel.setAttribute('hidden', ''); phonePanel.style.display = 'none'; }
                                      setTimeout(function () {
                                          var first = modal.querySelector('[data-otp-cell]');
                                          if (first) first.focus();
                                      }, 40);
                                      return;
                                  }
                                  if (window.firebaseClaimError) {
                                      clearInterval(poll);
                                      busy(btn, false);
                                      var err = window.firebaseClaimError;
                                      try {
                                          if (firebaseRecaptcha && firebaseRecaptcha.clear) firebaseRecaptcha.clear();
                                      } catch (e) {}
                                      firebaseRecaptcha = null;
                                      showError((err && err.message) || 'Could not send the SMS code.');
                                      return;
                                  }
                                  if (Date.now() - pollStart > 30000) {
                                      clearInterval(poll);
                                      busy(btn, false);
                                      debug('POLL TIMEOUT after ' + pollTicks + ' polls · ' + (Date.now() - pollStart) + 'ms · fbConfirm=' + !!window.firebaseClaimConfirmation + ' · fbError=' + !!window.firebaseClaimError);
                                      showError('SMS request timed out. Check the debug line above.');
                                      return;
                                  }
                              }, 300);
                              return;
                          }

                          // WhatsApp path: server generates + delivers OTP.
                          var r = await api('/profile/' + profileId + '/claim/send-otp', {
                              phone: phone, country_code: countryCode, email: email, channel: channel,
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
                          var channel = getChannel();
                          busy(btn, true);

                          if (channel === 'sms') {
                              // Confirm the SMS OTP with Firebase, then post
                              // the ID token to the server for verification.
                              // Prefer the window-stashed confirmation over
                              // the closure var — the polling in sendOtp
                              // writes there first (some SDK builds don't
                              // propagate the closure-scoped assignment
                              // reliably).
                              var conf = window.firebaseClaimConfirmation || firebaseConfirmation;
                              if (!conf) {
                                  busy(btn, false);
                                  showError('Please request a new code.');
                                  return;
                              }
                              var idToken = '';
                              try {
                                  var result = await conf.confirm(code);
                                  idToken = await result.user.getIdToken();
                              } catch (err) {
                                  busy(btn, false);
                                  console.error('Firebase confirm failed', err);
                                  showError((err && err.message) || 'The code is incorrect.');
                                  return;
                              }
                              var vs = await api('/profile/' + profileId + '/claim/verify-sms-firebase', {
                                  id_token: idToken,
                                  email: getVal('#ev-claim-email'),
                              });
                              busy(btn, false);
                              if (!vs.ok) {
                                  showError((vs.data && vs.data.message) || 'Verification failed.');
                                  return;
                              }
                              setStep('success');
                              return;
                          }

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

    {{-- Mobile Sticky Bottom Action Bar removed — the global
         mobile-user-bottom-nav (Home/Chats/Add Profile/Favorite/Menu) now
         handles the bottom-of-screen nav. Profile-details actions
         (Phone/Message/Ask/Review) are still accessible via the buttons
         higher up the page (.contact-phone1, .send-message1, etc.). --}}

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
              @if (session()->has('rerror'))
              <div class="alert alert-danger mb-3">
                {{ session('rerror') }}
              </div>
              @endif
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

          // Listen for close review modal event. Bootstrap's own hide()
          // handles the panel; we also explicitly strip any orphaned
          // backdrop and restore scroll on <body> because a Livewire
          // re-render mid-hide can leave those behind.
          window.addEventListener('closeReviewModal', event => {
            $(".reviewmodal").modal("hide");
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            const notification = $(`
              <div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 10000; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); animation: slideInRight 0.3s ease-out;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <strong><i class="fa fa-check-circle"></i> Thanks!</strong><br>
                Your review has been submitted and is awaiting moderation.
              </div>
            `);
            $('body').append(notification);
            setTimeout(() => {
              notification.fadeOut(400, function() { $(this).remove(); });
            }, 5000);
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
