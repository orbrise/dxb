<div class="evoory-upgrade-controller">
{{-- Back-link sub-header. Previously this was rendered via
     @section('headerform') so the legacy app.blade.php layout's
     @yield('headerform') would slot it inside <header id="header">,
     but that pattern was triggering Livewire's multiple-root-elements
     check after wire:navigate. Rendering it inline keeps the markup
     inside the component's single root and gives the same visual
     result. --}}
<div class="nav-bar navbar-top-nav ev-upgrade-subheader">
    <div class="container-fluid ev-upgrade-subheader__inner"
         style="background:transparent !important; display:flex; align-items:center; gap:12px; flex-wrap:nowrap; position:relative;">
      {{-- Back link is absolutely positioned on the left so the title
           can be perfectly centered in the row regardless of the back
           link's width. --}}
      <a class="back-link" href="/my-profile/{{$profile->slug}}/{{$profile->id}}"
         style="position:absolute; left:16px; top:50%; transform:translateY(-50%); display:inline-flex; align-items:center; gap:4px; z-index:2;">
        <i class="fa fa-angle-left fa-fw"></i>
        <span style="color: #C1F11D !important;">Back</span>
      </a>
      <div class="title" style="flex:1 1 auto; min-width:0; text-align:center;">
        <h1 style="margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size:18px; padding:0 70px;">Upgrade for {{ $profile->name }}</h1>
      </div>
    </div>
  </div>
  @push('css')
  <style>
    /* Sub-header + select-days layout, pushed to <head> so it survives
       every wire:navigate re-mount. Same rules previously lived in a
       <style> tag inside the component body but morphdom occasionally
       failed to keep them active after a round-trip (Home → back to
       Upgrade), causing the "Back" link and title to jam together. */
    .evoory-upgrade-controller .ev-upgrade-subheader .ev-upgrade-subheader__inner {
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
      flex-wrap: nowrap !important;
      position: relative !important;
    }
    .evoory-upgrade-controller .ev-upgrade-subheader__inner .back-link {
      position: absolute !important;
      left: 16px !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 4px !important;
      z-index: 2 !important;
    }
    .evoory-upgrade-controller .ev-upgrade-subheader__inner .title {
      flex: 1 1 auto !important;
      min-width: 0 !important;
      text-align: center !important;
    }
    .evoory-upgrade-controller .ev-upgrade-subheader__inner .title h1 {
      margin: 0 !important;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      font-size: 18px !important;
      padding: 0 70px !important;
    }
    /* Select-days block flex row: VIP name + Back-to-selection pill. */
    .evoory-upgrade-controller .upgrade-duration .upgrade-title {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      gap: 12px !important;
      flex-wrap: nowrap !important;
    }
    .evoory-upgrade-controller .upgrade-duration .upgrade-title .upgrade-name {
      flex: 1 1 auto !important;
      min-width: 0 !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      white-space: nowrap !important;
      margin: 0 !important;
    }
    .evoory-upgrade-controller .upgrade-duration .upgrade-title [data-listing-upgrade-form-back-to-upgrade-selection-btn] {
      flex-shrink: 0 !important;
    }
  </style>
  @endpush
<style>
/* === Evoory Dark Theme === */

/* Page background — scoped to body when this page is rendered.
   We add `evoory-upgrade-controller-active` to <body> via JS and remove it on
   navigation away, so this rule cannot leak into other pages via wire:navigate. */
body.evoory-upgrade-controller-active { background: #000 !important; }
body.evoory-upgrade-controller-active #header .nav-bar { background: #1f2222 !important; }
body.evoory-upgrade-controller-active #header { margin-bottom: 0px !important; }

/* Mobile: hide only the top nav (My Profile / My Account / Sign Out
   buttons row) — the page's `< Back ... Upgrade for X` sub-header lives
   in the same #header wrapper via @yield('headerform') so we can't hide
   the whole element; target only the .ev-header bar inside it. */
@media (max-width: 768px) {
    body.evoory-upgrade-controller-active #header .ev-header { display: none !important; }
    /* Add side gutter so VIP/Account Balance/Payment Method cards
       don't touch the viewport edges. The Bootstrap .row negative
       margin chain defeats container-level padding, so apply the
       margin directly to the .block cards inside .checkout-fields. */
    body.evoory-upgrade-controller-active .evoory-upgrade-controller .checkout-fields .block {
        margin-left: 16px !important;
        margin-right: 16px !important;
    }
    body.evoory-upgrade-controller-active .evoory-upgrade-controller .ev-upgrade-subheader__inner {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }
}
body.evoory-upgrade-controller-active #header .nav-bar .back-link { color: #C1F11D !important; text-decoration: none; }
body.evoory-upgrade-controller-active #header .nav-bar .title h1 a { color: #fff !important; }
body.evoory-upgrade-controller-active #footer { background: #0D1011 !important; border-top: 0px !important; }
body.evoory-upgrade-controller-active #footer .list-inline li { margin-bottom: 0px !important; }

/* Header - Evoory style buttons */
body.evoory-upgrade-controller-active .navbar.navbar-inverse { background: #0D1011 !important; border: none !important; }
body.evoory-upgrade-controller-active .logo.navbar-brand,
body.evoory-upgrade-controller-active .logo2.navbar-brand { display: none !important; }
body.evoory-upgrade-controller-active .navbar-header::before { display: none !important; }
body.evoory-upgrade-controller-active .auth-button-group { gap: 10px !important; }
body.evoory-upgrade-controller-active .auth-button-group .btn-navbar-header,
body.evoory-upgrade-controller-active .auth-button-group .button_to .btn-navbar-header {
    border-radius: 8px !important;
    border: 1px solid #2a2a2a !important;
    border-right: 1px solid #2a2a2a !important;
    background: transparent !important;
    color: #ccc !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    padding: 10px 20px !important;
    transition: all 0.2s ease;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}
body.evoory-upgrade-controller-active .auth-button-group .btn-navbar-header:hover,
body.evoory-upgrade-controller-active .auth-button-group .button_to .btn-navbar-header:hover {
    color: #fff !important;
    background: #1a1a1a !important;
    border-color: #ccc !important;
}
body.evoory-upgrade-controller-active .auth-button-group .btn-navbar-header:first-child {
    border-top-left-radius: 8px !important;
    border-bottom-left-radius: 8px !important;
    border-top-right-radius: 8px !important;
    border-bottom-right-radius: 8px !important;
}
body.evoory-upgrade-controller-active #main-nav { display: none !important; }

/* Base functional styles — all scoped to .evoory-upgrade-controller so
   they can never leak into the homepage/other pages if this <style>
   tag survives a wire:navigate morph. Same reason as the body-class
   scoping above. */
.evoory-upgrade-controller .upgrade-listing-form-init { visibility: visible; }
.evoory-upgrade-controller #allpackages { display: block; }
.evoory-upgrade-controller .checkout-fields { display: none; margin-top: 0px; padding-top: 7px; }
.evoory-upgrade-controller #paypal-button-container { margin-top: 20px; width: 100%; }
.evoory-upgrade-controller .payment-options li label.selected { border-left: 3px solid #C1F11D; }
.evoory-upgrade-controller .form-group { margin-bottom: 10px; }

/* Package Cards */
.upgrade-type-selector { padding: 20px 0; }

.upgrade-type {
    position: relative;
    min-height: 330px;
    cursor: pointer;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 5px;
    padding: 20px 15px;
    transition: all 0.3s ease;
    text-align: center;
}

.upgrade-type:hover {
    border-color: #C1F11D;
    background: #222222;
}

.upgrade-type.upgrade-type-free {
    min-height: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px 15px;
}

/* Selected Package */
.upgrade-type.active {
    border: 2px solid #C1F11D !important;
    box-shadow: 0 0 20px rgba(193, 241, 29, 0.15) !important;
    position: relative;
}

.upgrade-type.active .choose-package-btn { display: none !important; }
.upgrade-type.active .selected-badge { display: inline-block !important; }

/* Radio buttons in cards */
.upgrade-type .form-group { margin-bottom: 5px; }
.upgrade-type .radio-inline label { font-size: 22px; font-weight: 700; color: #fff; cursor: pointer; }
.upgrade-type .radio-inline input[type="radio"] { display: none; }
.upgrade-type .until { color: #999; font-size: 14px; margin-top: 5px; }
.upgrade-type .profile-preview { border-radius: 8px; background: #000 !important; }

/* Package tagline */
.package-tagline {
    font-weight: 500;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 14px;
}

/* Buttons - Evoory lime green */
.choose-package-btn {
    background: #C1F11D !important;
    color: #000 !important;
    border: none !important;
    border-radius: 22px !important;
    padding: 10px 30px !important;
    font-weight: 600 !important;
    font-size: 15px !important;
    transition: all 0.3s ease !important;
}
.choose-package-btn:hover { background: #d4f84d !important; color: #000 !important; }

.btn-warning {
    background: #C1F11D !important;
    color: #000 !important;
    border-color: #C1F11D !important;
}
.btn-warning:focus, .btn-warning:hover, .btn-warning:active,
.btn-warning.active, .open>.btn-warning.dropdown-toggle,
.btn-warning.active.focus, .btn-warning.active:focus, .btn-warning.active:hover,
.btn-warning:active.focus, .btn-warning:active:focus, .btn-warning:active:hover,
.open>.btn-warning.dropdown-toggle.focus, .open>.btn-warning.dropdown-toggle:focus,
.open>.btn-warning.dropdown-toggle:hover {
    color: #000 !important;
    background-color: #d4f84d !important;
    border-color: #d4f84d !important;
}

.selected-badge {
    margin: 0;
    display: none;
    background-color: #C1F11D;
    color: #000;
    padding: 10px 20px;
    border-radius: 22px;
    font-size: 14px;
    font-weight: 500;
    z-index: 10;
}
.selected-badge i { margin-right: 5px; color: #000 !important; }
/* Badge is a <span>; the scoped `.evoory-upgrade-controller span { color:#fff }`
   rule below would win on specificity and turn the "Selected" text white against
   the lime background. Pin to black here with !important so the badge stays
   legible. */
.evoory-upgrade-controller .selected-badge,
.evoory-upgrade-controller .selected-badge i { color: #000 !important; }

/* Price & text */
.u-price strong { color: #fff; }

/* Checkout / Proceed button */
.checkout-button {
    position: relative !important;
    z-index: 100 !important;
    pointer-events: auto !important;
    background: #C1F11D !important;
    color: #000 !important;
    border: none !important;
    border-radius: 22px !important;
    font-weight: 400 !important;
}
.checkout-button:hover { background: #d4f84d !important; color: #000 !important; }

/* btn-primary override */
/* Force black text on the wallet-payment button. The form-scoped
   .upgrade-listing-form .btn-primary rule below sets color:#000, but
   something in the loaded stylesheets was overriding it with white on
   this specific button, so target it by ID as well. */
#wallet-payment-button,
#wallet-payment-button *,
#wallet-payment-button .button-text,
#wallet-payment-button .button-spinner,
#wallet-payment-button .payment-amount {
    color: #000 !important;
}
/* Width 70% of the section, horizontally centered. .btn-block would
   otherwise stretch this to the full container width. */
#wallet-payment-button {
    width: 50% !important;
    max-width: 55% !important;
    margin-left: auto !important;
    margin-right: auto !important;
    display: flex !important;
}
.upgrade-listing-form .btn-primary {
    background: #C1F11D !important;
    color: #000 !important;
    border: none !important;
    border-radius: 24px;
}
.upgrade-listing-form .btn-primary:hover { background: #d4f84d !important; color: #000 !important; }

/* Payment Method Options */
.payment-method-option {
    padding: 17px;
    border: 2px solid #2a2a2a;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
    background: #1a1a1a;
}
.payment-method-option:hover { border-color: #444; }
.payment-method-option.selected { border-color: #C1F11D !important; background-color: #1a1a1a !important; }
.payment-method-option.disabled { opacity: 0.5; cursor: not-allowed; }
.payment-method-option.disabled:hover { border-color: #2a2a2a; }
.payment-method-option input[type="radio"] { accent-color: #C1F11D; width: 18px; height: 18px; margin-right: 15px; margin-top: -1px; }
.payment-method-option label { cursor: pointer; margin-bottom: 0; }
.payment-method-option .account-balance-amount { margin-left: auto; color: #28a745; font-weight: bold; }

/* Dark theme for sections — scoped. `.block` and `.border-top` are
   Bootstrap-style classes that exist on many other pages (homepage
   search cards, listing rows, etc.), so leaking them would repaint
   half the site dark and rounded. */
.evoory-upgrade-controller .alert-info { background: #1a1a1a; border: 1px solid #2a2a2a; color: #ccc; border-radius: 8px; }
.evoory-upgrade-controller .upgrade-duration { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 20px; }
.evoory-upgrade-controller .block { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; }
.evoory-upgrade-controller .border-top { border-color: #2a2a2a !important; }

/* Text colors — scoped. The bare `p, span, label, strong { color:#fff }`
   and unqualified `h2` were the widest-blast rules in the file; they
   would repaint text across the entire homepage if this <style> tag
   survived a morph. */
.evoory-upgrade-controller .upgrade-name,
.evoory-upgrade-controller .payment-options-box__title,
.evoory-upgrade-controller .lead,
.evoory-upgrade-controller h2 { color: #fff !important; }
.evoory-upgrade-controller p,
.evoory-upgrade-controller span,
.evoory-upgrade-controller label,
.evoory-upgrade-controller strong { color: #fff; }
.evoory-upgrade-controller a.text-warning:focus,
.evoory-upgrade-controller a.text-warning { color: #C1F11D; }

/* Bottom text */
.upgrade-types-bottom-text { margin-top: 40px; text-align: center; }
.upgrade-types-bottom-text a { color: #C1F11D !important; }
.upgrade-types-bottom-text .text-muted { color: #999 !important; }

/* Mobile */
@media (max-width: 767px) {
    /* Selected badge: don't stretch full-width on mobile — inline pill only,
       with tighter padding so it doesn't dominate the card. */
    .upgrade-type.active .selected-badge {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: auto !important;
        max-width: none !important;
        padding: 6px 50px !important;
        font-size: 13px !important;
        border-radius: 999px !important;
        margin-top: 6px !important;
    }
    /* Same treatment for the Choose button so the two states match. */
    .choose-package-btn {
        padding: 6px 20px !important;
        font-size: 13px !important;
        border-radius: 999px !important;
    }
    /* Package preview image — the fixed sizes (45/60/94px) look tiny on a
       full-width mobile card. Scale each tier up proportionally. */
    .upgrade-type .profile-preview { padding: 14px !important; gap: 14px !important; }
    .upgrade-type-free .profile-preview > div,
    .upgrade-type-basic .profile-preview > div:first-child {
        width: 90px !important;
        height: 90px !important;
    }
    .upgrade-type-featured .profile-preview > div:first-child {
        width: 110px !important;
        height: 110px !important;
    }
    .upgrade-type-vip .profile-preview > div:first-child {
        width: 140px !important;
        height: 140px !important;
    }
    .upgrade-type .profile-preview img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .upgrade-button-wrapper.mobile-fixed {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0 !important;
        padding: 15px !important;
        background: #0a0a0a !important;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.3) !important;
        z-index: 99999 !important;
        width: 100% !important;
        border-top: 1px solid #2a2a2a;
    }
    .upgrade-button-wrapper.mobile-fixed .checkout-button { width: 90% !important; margin: 0 !important; position: static !important; }
    body.has-fixed-button { padding-bottom: 90px !important; }
}

@media (max-width: 768px) {
    .upgrade-type { margin: 7px 4px; background: #1a1a1a; }
    .upgrade-modal .upgrade-type.active, .upgrade-type.active:hover { transform: matrix(1, 0, 0, 1, 0, 0); -webkit-transition: auto; transition: auto; }
    .package-text-content { height: auto !important; }
}

.evoory-upgrade-controller a { color: #C1F11D !important; }
</style>

  <div class="container-fluid">
        <div class="content-wrapper no-sidebar">
          <div id="content">
            
            <form class="simple_form upgrade-listing-form upgrade-listing-form-init  free-visible" id="new_upgrade_process" onsubmit="return false;">
              <div class="upgrade-type-selector">
                <div class="row">
                  <div class="col-lg-offset-1 col-lg-10">
                   
                    <div class="row" id="allpackages">
                      <div class="col-sm-3">
                        <div class="free upgrade-type upgrade-type-free current" style="cursor: pointer;" data-package="free">
                          <div class="form-group radio_buttons optional upgrade_process_upgrade_type">
                            <span class="radio-inline">
                              <label><input class="radio_buttons optional" type="radio" value="free" name="upgrade_process[upgrade_type]" />Free </label>
                            </span>
                          </div>
                          <div class='until'>Current</div>
                        </div>
                      </div>
                   
                      @foreach($packages as $package)
                      <div class="col-sm-3">
                          <div class="{{strtolower($package->name)}}  upgrade-type upgrade-type-{{strtolower($package->name)}}" data-package="{{$package->id}}">
                              
                              <div class="form-group radio_buttons optional upgrade_process_upgrade_type">
                                  <span class="radio-inline">
                                      <label for="upgrade_process_upgrade_type_{{strtolower($package->name)}}">
                                          <input class="radio_buttons optional" type="radio" value="{{strtolower($package->name)}}" name="upgrade_process[upgrade_type]" id="upgrade_process_upgrade_type_{{strtolower($package->name)}}" />
                                          {{$package->name}}
                                      </label>
                                  </span>
                              </div>
                              
                              <!-- Profile Preview -->
                              @php
                                  // Set image size based on package
                                  $imageSize = '80px'; // default
                                  $minHeight = '200px';
                                  $textLimit = 120;
                                  $showSideImages = 0; // Number of side images to show
                                  
                                  if($package->name == 'Basic') { // Basic
                                      $imageSize = '45px';
                                      $minHeight = '45px';
                                      $textLimit = 190;
                                      $showSideImages = 0;
                                  } elseif($package->name == 'Featured') { // Featured
                                      $imageSize = '60px';
                                      $minHeight = '60p';
                                      $textLimit = 120;
                                      $showSideImages = 2;
                                  } elseif($package->name == 'VIP') { // Premium/VIP
                                      $imageSize = '94px';
                                      $minHeight = '94px';
                                      $textLimit = 150;
                                      $showSideImages = 3;
                                  }
                                  
                                  // Get profile images for side display (use multipleimgs relationship)
                                  $profileImages = $profile->multipleimgs ?? collect();
                              @endphp
                              
                              @php
                                  $defaultAvatar = smart_asset('assets/images/defaultprofile.png');
                              @endphp
                              <div class="profile-preview" style="background: #000; padding: 10px; margin: 10px 0; min-height: {{ $minHeight }}; display: flex; align-items: flex-start; gap: 10px;">
                                  <!-- Main Image -->
                                  <div style="width: {{ $imageSize }}; height: {{ $imageSize }}; background: #1a1a1a; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                      @if($profile->coverimg && $profile->coverimg->image)
                                          <img src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image) }}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';this.style.opacity='0.5';">
                                      @else
                                          <img src="{{ $defaultAvatar }}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.5;">
                                      @endif
                                  </div>

                                  @if($showSideImages > 0)
                                  <!-- Side Images Column -->
                                  <div style="display: flex; flex-direction: column; gap: 3px; flex-shrink: 0;">
                                      @php
                                          $sideImages = $profileImages->take($showSideImages);
                                          $sideImageSize = $package->name == 'VIP' ? '30px' : '30px';
                                      @endphp
                                      @foreach($sideImages as $sideImage)
                                      <div style="width: {{ $sideImageSize }}; height: {{ $sideImageSize }}; background: #0D1011; overflow: hidden;">
                                          <img src="{{ webp_asset('userimages/'.$sideImage->user_id.'/'.$sideImage->profile_id.'/'.$sideImage->image) }}" alt="Side Image" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';this.style.opacity='0.5';">
                                      </div>
                                      @endforeach
                                      @for($i = $sideImages->count(); $i < $showSideImages; $i++)
                                      <div style="width: {{ $sideImageSize }}; height: {{ $sideImageSize }}; background: #0D1011; overflow: hidden;">
                                          <img src="{{ $defaultAvatar }}" alt="Placeholder" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.5;">
                                      </div>
                                      @endfor
                                  </div>
                                  @endif
                                  
                                  <div class="package-text-content" style="color: #fff; font-size: 10px; overflow: hidden; text-align: left; flex: 1; height: 172px;">
                                      <strong style="display: block; margin-bottom: 5px;">{{ $profile->name }}</strong>
                                      <p style="margin: 0; line-height: 1.4; word-wrap: break-word; max-height: 113px; overflow: hidden;">{{ Str::limit($profile->about, $textLimit) }}</p>
                                  </div>
                              </div>
                              
                              <div class="u-price" style="text-align: center; margin-top: 0px;">
                                  <!-- Tagline -->
                                  <div class="package-tagline" style="font-size: 1em; color: white; ">
                                      {{ $package->tagline ?? 'Best value package' }}
                                  </div>
                                  
                                  @php
                                      // Check if it's a global package first
                                      if ($package->is_global && $package->price_tiers) {
                                          $tiers = is_string($package->price_tiers) ? json_decode($package->price_tiers, true) : $package->price_tiers;
                                          $minPrice = !empty($tiers) ? min(array_column($tiers, 'price')) : 0;
                                      } else {
                                          // Country-specific package
                                          $countryPrice = $package->countryPrices->first();
                                          if ($countryPrice && $countryPrice->price_tiers) {
                                              $tiers = json_decode($countryPrice->price_tiers, true);
                                              $minPrice = !empty($tiers) ? min(array_column($tiers, 'price')) : 0;
                                          } else {
                                              $minPrice = 0;
                                          }
                                      }
                                  @endphp
                                  
                                  <!-- Price -->
                                  <strong style="display: block; font-size: 18px; margin-bottom: 5px;">from ${{$minPrice}}</strong>
                                  
                                  <!-- Choose Button -->
                                  <button type="button" class="btn btn-warning btn-sm choose-package-btn" style="padding: 8px 25px; border-radius:4 px; font-weight: bold;">
                                      Choose <i class="fa fa-arrow-right"></i>
                                  </button>
                                  
                                  <span class="selected-badge" style="display: none; margin-top: 10px;">
                                      <i class="fa fa-check"></i> Selected
                                  </span>
                              </div>
                          </div>
                      </div>
                      @endforeach
                      
                    </div>
                    <div class="clearfix"></div>
                    
                    <!-- Upgrade Button - Shows after package selection -->
                    <div class='upgrade-button-wrapper text-center' style='display: none; margin: 30px 0;'>
                        <button class='btn btn-primary btn-lg checkout-button' type='button' style='padding: 10px 40px; font-size: 16px;'>
                            Proceed Upgrade <i class='fa fa-arrow-right'></i>
                        </button>
                    </div>
                    
                    <!-- Checkout Section - Appears Between Packages and Bottom Text -->
                    <div class="checkout-fields">
                      <div class="row">
                        <div class="col-sm-9 col-md-7 col-md-offset-1 col-lg-6 col-lg-offset-3">
                        <div class="block pb-0 mb-3 upgrade-duration">
                          <div class="upgrade-title d-flex justify-content-between align-items-center mt-0 mb-3"
                               style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:nowrap;">
                            <h2 class="upgrade-name font-weight-bold my-0 mr-2" data-listing-upgrade-form-selected-upgrade-type-display=""
                                style="flex:1 1 auto; min-width:0; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></h2>
                            <a class="btn btn-primary d-flex align-items-center justify-content-center" data-listing-upgrade-form-back-to-upgrade-selection-btn="" href="#"
                               style="flex-shrink:0;">
                              <i class="fa fa-angle-left" style="color:#000"></i>
                              <span class="ml-2" style="color:#000">Back to selection</span>
                            </a>
                          </div>
                          <div class="border-top padding-top">
                            <p class="upgrade-duration__hint" style="margin:0 0 10px;color:#c8ccd0;font-size:14px;font-weight:500;">
                              Select the number of days you want to upgrade for:
                            </p>
                            <div class="upgrade-duration__radios mb-3"></div>
                            <div class="text-right w-100 pb-3">
                              <strong class="lead">Total: $ <span id="famount"></span></strong>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Account Balance Section -->
                        <div class="block px-4 py-3 mb-3 block--payment" id="account-balance-section" style="display: none;">
                          <div class="credits-label p-2">
                            <h2 class="inline-block payment-options-box__title">Account Balance</h2>
                          </div>
                          <ul class="payment-options toggle-li-radio list-unstyled mb-0">
                            <li class="mb-1">
                              <label class="p-3 d-flex flex-wrap align-items-center mb-0 payment-method-option {{ auth()->user()->wallet && auth()->user()->wallet->balance > 0 ? '' : 'disabled' }}" for="payment_method_wallet" data-payment-method="wallet" style="min-height:80px">
                                <input class="payment-option-input" type="radio" value="wallet" name="payment_method" id="payment_method_wallet" {{ auth()->user()->wallet && auth()->user()->wallet->balance > 0 ? '' : 'disabled' }}>
                                <span>Available balance</span>
                                <span class="account-balance" style="margin-left: 10px; color: white; font-weight: bold;">${{ auth()->user()->wallet ? number_format(auth()->user()->wallet->balance, 0) : 0 }}</span>
                                <span style="margin-left: 10px;">
                                  <a href="{{ route('purchase.credits') }}#wallet" class="text-warning" style="font-size:15px">Purchase account balance</a>
                                </span>
                              </label>
                            </li>
                          </ul>
                        </div>


                        <!-- Payment Method Selection -->
                        <div class="block px-0 mb-3" id="payment-methods-section" style="display: none;">
                          <div class="p-3" >
                            <h2 class="payment-options-box__title d-flex align-items-center mb-0"><span style="margin-right:10px">Payment Method</span>  </h2>
                          </div>
                          <div class="p-3">
                            <!-- Primary Gateway Option -->
                            <div class="payment-method-option d-flex align-items-center disabled" data-payment-method="primary">
                              <input type="radio" name="payment_method" id="payment_method_primary" value="primary" disabled>
                              <label for="payment_method_primary" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                                <span style="font-size: 16px;margin-right: 10px;">Primary Gateway</span>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 40px; margin-right: 8px;">
                                <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 40px;">
                              </label>
                            </div>
                            
                            <!-- Secondary Gateway (PayPal) Option -->
                            <div class="payment-method-option d-flex align-items-center disabled mt-2" data-payment-method="paypal">
                              <input type="radio" name="payment_method" id="payment_method_paypal" value="paypal" disabled>
                              <label for="payment_method_paypal" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                                <span style="font-size: 16px;margin-right: 10px;">Secondary Gateway</span>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 40px; margin-right: 8px;">
                                <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 40px;">
                              </label>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Wallet Payment Section -->
                        <div id="wallet-payment-section" class="block p-0 mb-3 payment-section" style="display: none;">
                          <div class="p-3">
                            <p>You will be charged <strong>$<span class="payment-amount"></span></strong> from your account Credits.</p>
                            <button class="d-flex align-items-center justify-content-center mt-4 btn btn-block btn-primary btn-lg btn-xs-block" type="button" id="wallet-payment-button">
                              <span class="button-text">Pay $<span class="payment-amount"></span> from Credits</span>
                              <span class="button-spinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                              </span>
                            </button>
                          </div>
                        </div>
                        
                        <!-- Primary Gateway Payment Section -->
                        <div id="primary-payment-section" class="block p-0 mb-3 payment-section" style="display: none;">
                          <div class="p-3">
                            <p>You will be charged <strong>$<span class="payment-amount"></span></strong> via secure payment gateway.</p>
                            
                            <!-- Iframe Container -->
                            <div id="primary-gateway-container" class="mt-3">
                              <div id="primary-gateway-loading" style="text-align: center; padding: 30px; color: #fff;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Loading secure payment form...</p>
                              </div>
                              <iframe id="primary-gateway-iframe" style="display: none; width: 100%; height: 570px; min-height: 570px; border: none; border-radius: 8px;" allowpaymentrequest></iframe>
                            </div>
                            
                            <p class="small text-muted text-center mt-3">Secure payment processing. Your card details are protected.</p>
                          </div>
                        </div>
                        
                        <!-- PayPal Payment Section -->
                        <div id="paypal-payment-section" class="block p-0 mb-3 payment-section" style="display: none; background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 8px;">
                          <div class="p-3">
                            <p style="color:#fff !important;">You will be charged <strong style="">$<span class="payment-amount"></span></strong> via PayPal.</p>
                            {{-- PayPal renders its own white card inside this container.
                                 The wrapper above is dark to match the rest of the page
                                 so the "You will be charged" line and the footer note
                                 stay legible in the site's dark theme. --}}
                            <div id="paypal-button-container" class="mt-3" style="background:#fff; border-radius:6px; padding:12px;"></div>
                            <p class="small text-center mt-3" style="color:#9aa3b2 !important;">Secure payment processing by PayPal. You can use your PayPal account or credit/debit card.</p>
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                  
                    <div class="upgrade-types-bottom-text">
                      <p class="text-muted lead">To find out more about the different upgrade options <a href="/help-for-advertisers#upgrade-options" target="_blank">click here</a>. </p>
                    </div>
                  </div>
                </div>
              </div>
              
      </form>
    </div>
  </div>
</div>
</div>

@push('js')
<script>
// Two things to do here, both triggered by `wire:navigate` keeping <head>/<body>
// across pages:
//
// 1. Toggle a body class so this page's body/header/footer overrides only apply
//    while the upgrade page is mounted.
// 2. Detach app2.css when leaving the upgrade page. The legacy layout
//    (components.layouts.app) loads app2.css which sets `body { background: #0D1011
//    !important; url(...) }` and beats the evoory layout's body bg when both
//    stylesheets sit in <head> after navigation. Stash it on a data attribute
//    so we can re-attach it if the user navigates back to upgrade.
(function() {
    function syncBodyClass() {
        if (document.querySelector('.evoory-upgrade-controller')) {
            document.body.classList.add('evoory-upgrade-controller-active');
        } else {
            document.body.classList.remove('evoory-upgrade-controller-active');
        }
    }

    // Re-attach app.css / app2.css / app3.css when we're on the upgrade
    // page but they've been stripped by another page's script (namely
    // homepage-evoory's stripLegacyCss). Stash the href on first sight
    // so we can restore even after the browser has completely removed
    // the <link> element.
    function ensureLegacyBundle() {
        var LEGACY = ['app.css', 'app2.css', 'app3.css'];
        window.__evooryStashedLegacyCss = window.__evooryStashedLegacyCss || {};
        var stash = window.__evooryStashedLegacyCss;
        LEGACY.forEach(function (name) {
            var live = document.querySelector('link[rel="stylesheet"][href*="assets/css/' + name + '"]');
            if (live) {
                // Cache the current href so we can rebuild if it gets
                // removed later.
                stash[name] = live.getAttribute('href');
            } else if (stash[name]) {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = stash[name];
                document.head.appendChild(link);
            }
        });
    }

    syncBodyClass();
    ensureLegacyBundle();

    if (!window.evooryUpgradeNavListener) {
        window.evooryUpgradeNavListener = true;
        // Fire on navigate (pre-render): restore legacy CSS before the
        // destination paints. Called unconditionally because
        // ensureLegacyBundle is idempotent — if the sheets are already
        // in <head> it does nothing, so an unnecessary call on an
        // evoory-layout destination is harmless (the destination's own
        // strip script will remove them again a moment later).
        document.addEventListener('livewire:navigate', function () {
            ensureLegacyBundle();
        });
        // Fire on navigated (post-render): keep body class + legacy CSS
        // in sync with the actual page state.
        document.addEventListener('livewire:navigated', function () {
            syncBodyClass();
            if (document.querySelector('.evoory-upgrade-controller')) {
                ensureLegacyBundle();
            }
        });
    }
})();
</script>
<script>
(function() {
    // Prevent multiple initializations
    if (window.upgradePageInitialized) return;
    window.upgradePageInitialized = true;
    
    var selectedPackageId = null;
    var selectedDuration = null;
    var selectedPrice = null;
    
    // Get Livewire component ID from the page
    function getLivewireComponent() {
        var el = document.querySelector('[wire\\:id]');
        if (el) {
            var wireId = el.getAttribute('wire:id');
            if (wireId && typeof Livewire !== 'undefined') {
                var component = Livewire.find(wireId);
                console.log('Found Livewire component:', component);
                return component;
            }
        }
        return null;
    }
    
    // Call Livewire method
    function callLivewire(method, args) {
        var component = getLivewireComponent();
        if (component && typeof component[method] === 'function') {
            return component[method].apply(component, args || []);
        } else if (component && typeof component.call === 'function') {
            return component.call(method, ...(args || []));
        }
        console.error('Cannot call Livewire method:', method);
        return null;
    }
    
    // Set Livewire property. `defer=true` writes the value into the
    // component but skips the server round-trip + re-render — critical
    // for calls that happen while the user is mid-flow, because any
    // re-render wipes JS-driven UI state (active package, checkout
    // fields visibility, etc.) and drops the user back to the initial
    // package-selection screen. iOS Safari specifically: with ITP,
    // Livewire XHRs during a defer commit can fail with 500 and
    // trigger Livewire's error overlay (a "500 Server Error" popup
    // that briefly appears before PayPal renders). Writing to
    // `component.$wire[name]` is a pure local mutation — no network
    // round trip, no error overlay, batched with the next real action.
    function setLivewireProperty(name, value, defer) {
        var component = getLivewireComponent();
        if (!component) return;
        if (defer) {
            try {
                if (component.$wire && (name in component.$wire || typeof component.$wire[name] !== 'undefined')) {
                    component.$wire[name] = value;
                    return;
                }
            } catch (e) { /* fall through */ }
            // Fallback: direct assignment on the component instance.
            // Still local, no XHR.
            try { component[name] = value; return; } catch (e) { /* fall through */ }
        }
        if (typeof component.set === 'function') {
            component.set(name, value);
        } else if (typeof component.$set === 'function') {
            component.$set(name, value);
        } else {
            component[name] = value;
        }
    }
    
    function initUpgradePage() {
        console.log('=== initUpgradePage CALLED ===');
        
        if (typeof jQuery === 'undefined') {
            setTimeout(initUpgradePage, 50);
            return;
        }
        
        // Package card clicks
        $(document).off('click.upgrade', '.upgrade-type').on('click.upgrade', '.upgrade-type', function(e) {
            console.log('Package card clicked');
            
            if ($(this).hasClass('upgrade-type-free')) {
                e.preventDefault();
                alert('This is your current package. Please select a paid package to upgrade.');
                return false;
            }
            if ($(e.target).closest('.upgrade-button-wrapper').length > 0) return;
            e.preventDefault();
            
            $('.upgrade-type').removeClass('active');
            $('.upgrade-button-wrapper, .checkout-fields').hide();
            $(this).addClass('active');
            selectedPackageId = $(this).data('package');
            
            if ($(window).width() <= 767) {
                var $wrapper = $('.upgrade-button-wrapper');
                if (!$wrapper.hasClass('mobile-fixed')) {
                    $wrapper.addClass('mobile-fixed').appendTo('body');
                    $('body').addClass('has-fixed-button');
                }
            }
            $('.upgrade-button-wrapper').show();
            console.log('Package ID:', selectedPackageId);
        });
        
        // Checkout button
        $(document).off('click.upgrade', '.checkout-button').on('click.upgrade', '.checkout-button', function(e) {
            e.preventDefault();
            if (!selectedPackageId) { alert('Please select a package first'); return; }
            
            $('.upgrade-name').text($('.upgrade-type.active label').first().text().trim());
            $('#allpackages, .upgrade-button-wrapper').hide();
            $('body').removeClass('has-fixed-button');
            $('.checkout-fields').show();
            
            setTimeout(function() {
                $('html, body').animate({ scrollTop: $('.checkout-fields').offset().top - 100 }, 500);
            }, 100);
            loadPackagePricing(selectedPackageId);
        });
        
        // Back button
        $(document).off('click.upgrade', '[data-listing-upgrade-form-back-to-upgrade-selection-btn]').on('click.upgrade', '[data-listing-upgrade-form-back-to-upgrade-selection-btn]', function(e) {
            e.preventDefault();
            $('.checkout-fields').hide();
            $('#allpackages, .upgrade-button-wrapper').show();
            
            if ($(window).width() <= 767) {
                var $wrapper = $('.upgrade-button-wrapper');
                if (!$wrapper.hasClass('mobile-fixed')) {
                    $wrapper.addClass('mobile-fixed').appendTo('body');
                    $('body').addClass('has-fixed-button');
                }
            }
            
            selectedPackageId = selectedDuration = selectedPrice = null;
            $('#account-balance-section, #payment-methods-section, .payment-section').hide();
            $('.payment-method-option').removeClass('selected');
            $('[data-payment-method="primary"], [data-payment-method="paypal"]').addClass('disabled');
            $('#payment_method_wallet, #payment_method_primary, #payment_method_paypal').prop('disabled', true).prop('checked', false);
        });
        
        // Duration change
        $(document).off('change.upgrade', 'input[name="duration"]').on('change.upgrade', 'input[name="duration"]', function() {
            selectedPrice = $(this).data('price');
            selectedDuration = $(this).val();
            $('span#famount, .payment-amount').text(selectedPrice);
            
            // Show both payment sections
            $('#account-balance-section, #payment-methods-section').slideDown();
            
            // Enable Primary Gateway option
            $('[data-payment-method="primary"]').removeClass('disabled');
            $('#payment_method_primary').prop('disabled', false);
            
            // Enable PayPal option
            $('[data-payment-method="paypal"]').removeClass('disabled');
            $('#payment_method_paypal').prop('disabled', false);
            
            // Enable wallet option only if balance is sufficient
            var walletBalance = {{ auth()->user()->wallet ? auth()->user()->wallet->balance : 0 }};
            if (walletBalance >= selectedPrice) {
                $('[data-payment-method="wallet"]').removeClass('disabled');
                $('#payment_method_wallet').prop('disabled', false);
            } else {
                $('[data-payment-method="wallet"]').addClass('disabled');
                $('#payment_method_wallet').prop('disabled', true);
            }
        });
        
        // Payment method click
        $(document).off('click.upgrade', '.payment-method-option').on('click.upgrade', '.payment-method-option', function(e) {
            // If the click landed on a real link inside the option (e.g. the
            // "Purchase account balance" link inside the Account Balance row),
            // don't intercept — let the browser navigate normally.
            if ($(e.target).closest('a[href]').length) return;
            // Otherwise prevent the label→radio→form click chain from bubbling
            // into <form id="new_upgrade_process">. Without this, PayPal's
            // async SDK render into #paypal-button-container has previously
            // coincided with an implicit form submission on some browsers,
            // which reloads the page and drops the user back on the
            // package-selection step.
            e.preventDefault();
            e.stopPropagation();
            if ($(this).hasClass('disabled')) return false;
            var method = $(this).data('payment-method');
            // Manually update the radio state since we just prevented the
            // native label→input click chain.
            $('.payment-method-option').removeClass('selected');
            $('.payment-method-option input[type="radio"]').prop('checked', false);
            $(this).addClass('selected').find('input[type="radio"]').prop('checked', true);
            $('.payment-section').hide();
            $('#' + method + '-payment-section').show();
            if (method === 'paypal') initPayPalButtons();
            // Load primary gateway iframe when switching to it
            if (method === 'primary') {
                initPrimaryGatewayIframe();
                // Scroll to iframe on mobile
                setTimeout(function() {
                    var $target = $('#primary-payment-section');
                    if ($target.length && $(window).width() <= 767) {
                        $('html, body').animate({ scrollTop: $target.offset().top - 20 }, 400);
                    }
                }, 150);
            }
        });
        
        // Wallet payment button
        $(document).off('click.upgrade', '#wallet-payment-button').on('click.upgrade', '#wallet-payment-button', function() {
            if (!selectedPackageId || !selectedDuration || !selectedPrice) {
                alert('Please select a package and duration first');
                return;
            }
            var $btn = $(this);
            $btn.prop('disabled', true).find('.button-text').hide();
            $btn.find('.button-spinner').show();
            
            // Pass parameters directly with dispatch
            Livewire.dispatch('processWalletPayment', { 
                packageId: selectedPackageId, 
                duration: parseInt(selectedDuration), 
                amount: parseFloat(selectedPrice) 
            });
        });
         
        // Listen for message from iframe
        window.addEventListener('message', function(event) {
            // Accept messages from external payment site or same origin
            if (event.origin !== 'https://myadsnetwork.com' && event.origin !== window.location.origin) return;
            
            if (event.data && event.data.type === 'payment_success') {
                var ref = event.data.reference_id;
                
                // Prevent duplicate processing
                if (window.primaryPaymentProcessed && window.primaryPaymentProcessed === ref) {
                    console.log('Payment already processed, ignoring duplicate message');
                    return;
                }
                window.primaryPaymentProcessed = ref;
                window.primaryPaymentSuccess = true;
                
                // Show success in iframe container
                $('#primary-gateway-iframe').hide();
                $('#primary-gateway-loading').html('<i class="fa fa-check-circle fa-2x text-success"></i><p class="mt-2">Payment successful! Processing...</p>').show();
                
                // Process the payment in Livewire
                Livewire.dispatch('processPrimaryPayment', { 
                    packageId: window.primaryPaymentReference.packageId, 
                    duration: parseInt(window.primaryPaymentReference.duration), 
                    amount: parseFloat(window.primaryPaymentReference.price),
                    referenceId: ref
                });
            }
        });
        
        // Livewire message handler
        window.removeEventListener('showMessage', window.handleShowMessage);
        window.handleShowMessage = function(e) {
            var d = e.detail[0];
            if (d.type === 'success') {
                sessionStorage.setItem('successMessage', d.message);
                window.location.href = '/my-profile/' + d.name + '/' + d.id;
            } else {
                alert(d.message);
                $('#wallet-payment-button').prop('disabled', false).find('.button-text').show();
                $('#wallet-payment-button').find('.button-spinner').hide();
            }
        };
        window.addEventListener('showMessage', window.handleShowMessage);
    }
    
    function loadPackagePricing(packageId) {
        console.log('Loading package pricing for:', packageId);
        $('.upgrade-duration__radios').html('<p><i class="fa fa-spinner fa-spin"></i> Loading pricing...</p>');
        
        $.ajax({
            url: '/api/package/' + packageId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var tiers = [];
                if (data.country_prices && data.country_prices.length > 0 && data.country_prices[0].price_tiers) {
                    tiers = typeof data.country_prices[0].price_tiers === 'string' ? JSON.parse(data.country_prices[0].price_tiers) : data.country_prices[0].price_tiers;
                } else if (data.price_tiers) {
                    tiers = typeof data.price_tiers === 'string' ? JSON.parse(data.price_tiers) : data.price_tiers;
                }
                var html = '';
                if (tiers.length > 0) {
                    // Use inline styles instead of Bootstrap 4 utility
                    // classes (d-flex/align-items-center/pl-0/my-2/ml-2):
                    // the homepage strips the legacy Bootstrap bundle on
                    // arrival, and if the user navigates Home → Upgrade
                    // the utility classes are gone and the radios collapse
                    // into an unspaced "[○20 days for $33]" mash. Inline
                    // styles don't care whether the bundle is present.
                    tiers.forEach(function(t) {
                        html += '<div style="margin: 8px 0;">'
                             + '<label style="display: flex; align-items: center; gap: 10px; padding: 6px 0; cursor: pointer; color: #fff; font-size: 15px; margin: 0;">'
                             + '<input name="duration" type="radio" data-price="' + t.price + '" value="' + t.days + '" style="accent-color: #C1F11D; width: 18px; height: 18px; cursor: pointer; margin: 0;">'
                             + '<span style="color: #fff;">' + t.days + ' days for $' + t.price + '</span>'
                             + '</label></div>';
                    });
                } else {
                    html = '<p class="text-danger">No pricing available.</p>';
                }
                $('.upgrade-duration__radios').html(html);
            },
            error: function() {
                $('.upgrade-duration__radios').html('<p class="text-danger">Failed to load pricing.</p>');
            }
        });
    }
    
    function initPrimaryGatewayIframe() {
        console.log('=== initPrimaryGatewayIframe CALLED ===');
        
        var $loading = $('#primary-gateway-loading');
        var $iframe = $('#primary-gateway-iframe');
        
        if (!selectedPackageId || !selectedDuration || !selectedPrice) {
            $('#primary-gateway-container').html('<p class="text-danger">Please select a duration first.</p>');
            return;
        }
        
        // Show loading
        $loading.show();
        $iframe.hide();
        
        // Get package name from selected package card
        var packageName = $('.upgrade-type.active label').first().text().trim() || 'Package';
        
        // Generate a unique reference ID
        var referenceId = 'DXB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Store reference for callback
        window.primaryPaymentReference = {
            referenceId: referenceId,
            packageId: selectedPackageId,
            duration: selectedDuration,
            price: selectedPrice
        };
        
        // Build the external payment URL
        var callbackUrl = encodeURIComponent(window.location.origin + '/payment/primary-callback?reference_id=' + referenceId);
        var cancelUrl = encodeURIComponent(window.location.href);
        
        var externalPaymentUrl = 'https://myadsnetwork.com/external-payment/checkout' +
            '?price=' + selectedPrice +
            '&name=' + encodeURIComponent(packageName + ' - ' + selectedDuration + ' days') +
            '&package_name=' + encodeURIComponent(packageName) +
            '&package_id=' + selectedPackageId +
            '&duration=' + selectedDuration +
            '&currency=USD' +
            '&callback_url=' + callbackUrl +
            '&reference_id=' + referenceId +
            '&customer_email=' + encodeURIComponent('{{ auth()->user()->email }}') +
            '&embed=1';
        
        console.log('Loading iframe:', externalPaymentUrl);
        
        // Set iframe source
        $iframe.attr('src', externalPaymentUrl);
        
        // Show iframe when loaded
        $iframe.off('load').on('load', function() {
            $loading.hide();
            $iframe.show();
        });
    }
    
    function initPayPalButtons() {
        var $container = $('#paypal-button-container');
        console.log('=== initPayPalButtons CALLED ===');
        $container.html('<p><i class="fa fa-spinner fa-spin"></i> Loading PayPal...</p>');
        
        if (!selectedPackageId || !selectedDuration || !selectedPrice) {
            $container.html('<p class="text-danger">Please select a duration first.</p>');
            return;
        }
        
        if (typeof paypal !== 'undefined' && paypal.Buttons) {
            console.log('PayPal SDK already loaded');
            renderPayPalButtons($container);
        } else {
            console.log('Loading PayPal SDK...');
            document.querySelectorAll('script[src*="paypal.com/sdk"]').forEach(function(s) { s.remove(); });
            var script = document.createElement('script');
            script.src = 'https://www.paypal.com/sdk/js?client-id=ARoQNQK4S-k3R3g2vLFGtfyGlI7UiA1ZyDkyxrCEtcuDguDoVuSH2_JcWMjowUTwI3jMgjr5ttj8QSrJ&currency=USD';
            script.onload = function() {
                console.log('PayPal SDK loaded');
                setTimeout(function() { renderPayPalButtons($container); }, 300);
            };
            script.onerror = function() {
                $container.html('<p class="text-danger">Failed to load payment system.</p>');
            };
            document.head.appendChild(script);
        }
    }
    
    function renderPayPalButtons($container) {
        console.log('=== renderPayPalButtons ===');
        
        if (typeof paypal === 'undefined' || !paypal.Buttons) {
            $container.html('<p class="text-danger">Payment system unavailable.</p>');
            return;
        }
        
        $container.empty();
        
        // Set Livewire properties in "defer" mode — we only need the
        // backend to know these values at the moment onApprove dispatches
        // handlePayPalApproval, so batching the writes prevents an
        // immediate re-render that would wipe the current UI state.
        setLivewireProperty('selectedPackage', selectedPackageId, true);
        setLivewireProperty('selectedDuration', selectedDuration, true);
        setLivewireProperty('selectedAmount', selectedPrice, true);
        
        try {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{ amount: { value: selectedPrice, currency_code: 'USD' } }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function() {
                        console.log('PayPal approved, calling handlePayPalApproval');
                        // Use Livewire dispatch with data
                        Livewire.dispatch('handlePayPalApproval', { orderId: data.orderID });
                    });
                },
                onError: function(err) {
                    console.error('PayPal Error:', err);
                    alert('Payment failed. Please try again.');
                }
            }).render('#paypal-button-container');
            console.log('PayPal buttons rendered!');
        } catch (err) {
            console.error('Error rendering PayPal:', err);
            $container.html('<p class="text-danger">Failed to initialize payment.</p>');
        }
    }
    
    // Initialize when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUpgradePage);
    } else {
        initUpgradePage();
    }
    
    // Re-init on Livewire navigation. Detect the upgrade page by DOM
    // presence rather than URL substring — the router path may be
    // /my-profile/{slug}/{id}/upgrade or a rewrite, so checking for the
    // component root is the reliable signal. Also reset all the JS state
    // that would otherwise leak from the previous visit and confuse the
    // duration/payment flows.
    document.addEventListener('livewire:navigated', function() {
        if (document.querySelector('.evoory-upgrade-controller')) {
            window.upgradePageInitialized = false;
            selectedPackageId = null;
            selectedDuration = null;
            selectedPrice = null;
            setTimeout(initUpgradePage, 100);
        }
    });
})();
</script>
@endpush
