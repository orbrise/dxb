<!DOCTYPE html>
<html lang="en">
  <head>
    @php
    $currentUrl = request()->getPathInfo();
    $seoService = app(App\Services\SeoService::class);
    $seo = $seoService->getSeoFromUrl($currentUrl);
    
    // Check if there's a canonical URL set (for URL aliases)
    $canonicalUrl = isset($canonicalUrl) ? $canonicalUrl : url($currentUrl);
    @endphp 
     
    <!-- DNS Prefetch and Preconnect for faster resource loading -->
    <link rel="dns-prefetch" href="//assets.massagerepublic.com.co">
    <link rel="preconnect" href="https://assets.massagerepublic.com.co" crossorigin>
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="//code.jquery.com">
    <link rel="preconnect" href="https://code.jquery.com" crossorigin>
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    
    <meta name="google-site-verification" content="xYsnVyiR2Xg8SvEIB65bLjyxsIcBKWNSIPz2_jh-1Ww" />
    <meta charset="UTF-8" />
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    
    <!-- Disable Turbolinks caching completely -->
    <meta name="turbolinks-cache-control" content="no-cache">
    
    <!-- Dynamic SEO Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    <meta name="description" content="{{ $seo['description'] }}">
    
    <!-- Canonical URL for SEO -->
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    
    <!-- AMP Version Link (helps bypass blocked domains via Google) -->
    @php
        $ampUrl = null;
        if (preg_match('/^\/(female|male|shemale)-escorts-in-([a-z0-9\-]+)(?:\/(\d+)\/([a-z0-9\-]+))?$/', $currentUrl, $matches)) {
            if (isset($matches[3]) && isset($matches[4])) {
                // Profile page
                $ampUrl = url('/amp/' . $matches[1] . '-escorts-in-' . $matches[2] . '/' . $matches[3] . '/' . $matches[4]);
            } else {
                // Listings page
                $ampUrl = url('/amp/' . $matches[1] . '-escorts-in-' . $matches[2]);
            }
        } elseif ($currentUrl === '/' || $currentUrl === '') {
            $ampUrl = url('/amp');
        }
    @endphp
    @if($ampUrl)
    <link rel="amphtml" href="{{ $ampUrl }}" />
    @endif
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="{{ $seo['title'] }}" />
    <meta property="og:description" content="{{ $seo['description'] }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:type" content="website" />
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="{{ $seo['title'] }}" />
    <meta name="twitter:description" content="{{ $seo['description'] }}" />
    
    <!-- Additional Meta Tags -->
    <meta name="robots" content="index, follow" />
    <meta name="author" content="{{ config('app.name') }}" />
    
    <!-- SEO Debug Information (visible in source) -->
    <!-- 
    SEO Debug Info:
    - Current URL: {{ $currentUrl }}
    - Resolved URL: {{ $seo['resolved_url'] ?? 'N/A' }}
    - Is Alias: {{ ($seo['is_alias'] ?? false) ? 'Yes' : 'No' }}
    - Gender: {{ $seo['gender'] ?? 'Not detected' }}
    - City: {{ $seo['city'] ?? 'Not detected' }}
    - Country: {{ $seo['country'] ?? 'Not detected' }}
    - Canonical URL: {{ $canonicalUrl }}
    -->
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ smart_asset($setting->favicon) }}" rel="shortcut icon" type="images/x-icon" />
    
    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "{{ config('app.name') }}",
      "url": "{{ url('/') }}",
      "description": "{{ $seo['description'] }}",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/') }}/search?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    
    @if(isset($seo['city']) && $seo['city'])
    <!-- Local Business Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "{{ $seo['title'] }}",
      "description": "{{ $seo['description'] }}",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "{{ $seo['city'] }}",
        "addressCountry": "{{ $seo['country'] ?? 'UAE' }}"
      },
      "url": "{{ $canonicalUrl }}"
    }
    </script>
    @endif
    
    {{-- Use optimized CSS (62KB external file instead of inline) --}}
    @include('components.layouts.css-optimized')
    
    <!-- Font Awesome font preload for faster icon rendering -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    
    @stack('css')
    @livewireStyles
    
    <style>
/* Font Awesome Global Fix - Ensure all icons render properly */
@font-face {
    font-family: "Font Awesome 5 Free";
    font-style: normal;
    font-weight: 900;
    font-display: swap;
    src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/webfonts/fa-solid-900.woff2) format("woff2");
}

i[class*="fa"], .fa, .fas, .far, .fab, .fal {
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
    -moz-osx-font-smoothing: grayscale !important;
    -webkit-font-smoothing: antialiased !important;
    display: inline-block !important;
    font-style: normal !important;
    font-variant: normal !important;
    text-rendering: auto !important;
    line-height: 1 !important;
}

/* Force specific icon classes */
i.fa::before, i.fas::before, i.far::before, i.fab::before {
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
}

/* Common icon unicode content - FA5 compatible */
.fa-plus:before { content: "\f067" !important; }
.fa-minus:before { content: "\f068" !important; }
.fa-user-circle:before { content: "\f2bd" !important; }
.fa-venus:before { content: "\f221" !important; }
.fa-phone:before { content: "\f095" !important; }
.fa-user:before { content: "\f007" !important; }
.fa-sign-out-alt:before { content: "\f2f5" !important; }
.fa-caret-down:before { content: "\f0d7" !important; }
.fa-search:before { content: "\f002" !important; }
.fa-arrow-alt-circle-down:before { content: "\f359" !important; }
.fa-arrow-alt-circle-up:before { content: "\f35b" !important; }
.fa-envelope:before { content: "\f0e0" !important; }
.fa-certificate:before { content: "\f0a3" !important; }
.fa-heart:before { content: "\f004" !important; }
.fa-question-circle:before { content: "\f059" !important; }
.fa-pencil-alt:before { content: "\f303" !important; }
.fa-globe-asia:before { content: "\f57e" !important; }
.fa-map-marker-alt:before { content: "\f3c5" !important; }
.fa-bookmark:before { content: "\f02e" !important; }
.fa-check:before { content: "\f00c" !important; }
.fa-times:before { content: "\f00d" !important; }
.fa-angle-left:before { content: "\f104" !important; }
.fa-angle-right:before { content: "\f105" !important; }
.fa-flag:before { content: "\f024" !important; }
.fa-circle:before { content: "\f111" !important; }
.fa-spinner:before { content: "\f110" !important; }
.fa-arrow-left:before { content: "\f060" !important; }
.fa-arrow-right:before { content: "\f061" !important; }
.fa-sync-alt:before { content: "\f2f1" !important; }
.fa-edit:before { content: "\f044" !important; }
.fa-pen:before { content: "\f304" !important; }
.fa-key:before { content: "\f084" !important; }
.fa-lock:before { content: "\f023" !important; }
.fa-comments:before { content: "\f086" !important; }
.fa-star:before { content: "\f005" !important; }
.fa-lg { font-size: 1.33333em !important; line-height: 0.75em !important; vertical-align: -0.0667em !important; }

/* FA4 to FA5 icon name compatibility */
.fa-envelope2:before { content: "\f0e0" !important; }
.fa-heart2:before { content: "\f004" !important; }
.fa-arrow-circle-down:before { content: "\f359" !important; }
.fa-arrow-circle-up:before { content: "\f35b" !important; }

.select2-container .select2-selection--multiple {
    border: 1px solid #ccc !important;
    padding: 5px !important;
    min-height: 40px !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff !important;
    color: white !important;
}

/* Impersonation Banner Styles */
#impersonation-banner {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 14px;
    border-bottom: 2px solid rgba(255,255,255,0.3);
}

#impersonation-banner .btn-light {
    background: rgba(255,255,255,0.9);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

#impersonation-banner .btn-light:hover {
    background: white;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    #impersonation-banner .col-md-4 {
        margin-top: 10px;
    }
}

/* Hide NProgress spinner on mobile */
@media (max-width: 768px) {
    #nprogress .bar {
        background: #000 !important;
    }
    #nprogress .peg {
        box-shadow: 0 0 10px #000, 0 0 5px #000 !important;
    }
    #nprogress .np-spinner {
        display: none !important;
    }
}

/* Custom mobile loading overlay */
#mobile-loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(78, 78, 78, 0.15);
    z-index: 99998;
}

#mobile-loading-overlay.show {
    display: block;
}

#mobile-loading-box {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #454545cc;
    padding: 18px 22px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    z-index: 99999;
}

#mobile-loading-spinner {
    border: 3px solid rgba(244, 184, 39, 0.2);
    border-top: 3px solid #f4b827;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    animation: mobile-spin 0.8s linear infinite;
}

@keyframes mobile-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

label {color:white !important;}

.divider {
    display: flex
;
    align-items: center;
    margin: 24px 0;
    position: relative;
}

.divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background-color: #ddd;
}
.divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background-color: #ddd;
}


@media (min-width: 568px) {
    .listing-li .listing-info {
        height: 228px;
    }
}


@media (min-width: 768px) {
    .activity-record.mini .img-wrapper.premium img, .listing-li .img-wrapper.premium img, .listings-grid .img-wrapper.premium img {
        height: 222px;
    }

    .btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
    color: #fff;
    background-color: #d3980b;
    border-color: #d3980b;
}


}

.pagination li a, .pagination li span {
    justify-content: center;
    padding: 14px 7px;
}
.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
    z-index: 3;
    color: #fff;
    background-color: #6262627a;
    border-color: rgba(255, 255, 255, .1);
    cursor: default;
}
.pagination>li>a:focus, .pagination>li>a:hover, .pagination>li>span:focus, .pagination>li>span:hover
 {
    z-index: 2;
    color: #dca623;
    background-color: #474747;
    border-color: rgba(255, 255, 255, .1);
}

.container-fluid {
    padding-top: 3px;
    padding-bottom: 3px;
}

#footer .checkout-fields ul li, #footer .list-inline li, #footer .upgrade-type ul li, #footer .upgrade-type-selector ul li, .checkout-fields #footer ul li, .upgrade-type #footer ul li, .upgrade-type-selector #footer ul li {
    border-right: 1px solid rgb(255 255 255 / 21%);
}

 body {
        background-image: url('{{ smart_asset('assets/images/background.png') }}');
        background-position: center;
        background-repeat: repeat;
        background-attachment: fixed;
    }


.btn-primary.active, .btn-primary:active, .btn-primary:hover, .my-listings-nav.open>.dropdown-toggle.my-listing-new-link, .my-listings-nav>.active.my-listing-new-link, .my-listings-nav>.my-listing-new-link:active, .my-listings-nav>.my-listing-new-link:hover, .open>.btn-primary.dropdown-toggle {
    color: #333;
    background-color: transparent;
    border-color: #747474ff;
}

    </style>

  </head>
  <body>
    <!-- Custom mobile loading overlay -->
    <div id="mobile-loading-overlay">
        <div id="mobile-loading-box">
            <div id="mobile-loading-spinner"></div>
        </div>
    </div>
        
  <header id="header">
    @include('components.layouts.header')
  @yield('headerform')
  </header>
    
  <!-- Admin Impersonation Banner -->
  @if(session('admin_impersonating'))
  <div id="impersonation-banner" style="background: linear-gradient(45deg, #ff6b6b, #ffa500); color: white; padding: 10px 0; text-align: center; position: sticky; top: 0; z-index: 9999; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-8">
          <strong><i class="fa fa-user-secret"></i> ADMIN IMPERSONATION MODE</strong>
          @if(session('impersonating'))
          - You are viewing as: <strong>{{ session('impersonating.user_name') }}</strong> ({{ session('impersonating.user_email') }})
          @if(session('impersonating.via_profile'))
          <br><small><i class="fa fa-id-card"></i> Via Profile: <strong>{{ session('impersonating.via_profile') }}</strong></small>
          @endif
          @endif
          <span id="impersonation-timer" style="margin-left: 15px; font-size: 12px;"></span>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('exit.impersonation') }}" class="btn btn-sm btn-light" onclick="return confirm('Are you sure you want to exit impersonation mode?')">
            <i class="fa fa-sign-out"></i> Exit Impersonation
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <script>
  // Impersonation timer
  @if(session('impersonation_started'))
  var startTime = new Date('{{ session('impersonation_started') }}');
  function updateTimer() {
    var now = new Date();
    var diff = Math.floor((now - startTime) / 1000);
    var minutes = Math.floor(diff / 60);
    var seconds = diff % 60;
    document.getElementById('impersonation-timer').innerHTML = 
      '(Active for ' + minutes + 'm ' + seconds + 's)';
  }
  setInterval(updateTimer, 1000);
  updateTimer();
  @endif
  </script>
  @endif

        {{ $slot ?? '' }}
        @yield('content')
     
        <div id="fb-root"></div>
        @include('components.layouts.footer')
        @yield('advancesearch')
        @yield('homepopup')
        
        <!-- Optimized JavaScript Loading - defer to prevent render-blocking -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
                integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
                crossorigin="anonymous"
                defer></script>
        
        <!-- Popper.js required for Bootstrap tooltips -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" 
                integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" 
                crossorigin="anonymous"
                defer></script>
        
        {{-- CRITICAL: Polyfills MUST run before app2.js loads --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Apply jQuery polyfills AFTER jQuery loads
                if (typeof jQuery !== 'undefined') {
                    var $ = jQuery;
                    var noopPlugin = function() { return this; };
                    
                    // Lock these with Object.defineProperty to prevent overwriting
                    Object.defineProperty($.fn, 'select2', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'typeahead', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'typeaheadCity', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'combobox', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'photobox', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'validate', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'ajaxForm', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'ajaxOverlay', { value: noopPlugin, writable: false, configurable: false });
                    Object.defineProperty($.fn, 'videoEmbedder', { value: noopPlugin, writable: false, configurable: false });
                }
            });
            
            // Suppress console errors (can run immediately)
            (function() {
                var originalError = console.error;
                console.error = function() {
                    var msg = arguments[0] || '';
                    if (msg.includes && (msg.includes('rails-ujs') || msg.includes('already been loaded') || msg.includes('Component') || msg.includes('already registered'))) {
                        return;
                    }
                    originalError.apply(console, arguments);
                };
            })();
        </script>
        
        {{-- Font Awesome icon fix - ULTRA AGGRESSIVE --}}
        <script>
            (function() {
                
                // ULTRA aggressive icon fix with LOCAL webfont
                window.fixAllIcons = function() {
                    // Add @font-face via JavaScript using LOCAL font file
                    var styleId = 'fa-icon-fix';
                    if (!document.getElementById(styleId)) {
                        var style = document.createElement('style');
                        style.id = styleId;
                        style.textContent = `
                            @font-face {
                                font-family: "Font Awesome 5 Free";
                                font-style: normal;
                                font-weight: 900;
                                font-display: block;
                                src: url('{{smart_asset("assets/fonts/fa-solid-900.woff2")}}') format("woff2");
                            }
                            i[class*="fa"], .fa, .fas, .far, .fab, .fal { 
                                font-family: "Font Awesome 5 Free" !important; 
                                font-weight: 900 !important; 
                                display: inline-block !important; 
                                font-style: normal !important;
                                -webkit-font-smoothing: antialiased !important;
                                -moz-osx-font-smoothing: grayscale !important;
                            }
                        `;
                        document.head.appendChild(style);
                    }
                    
                    // Force each icon to re-render
                    var icons = document.querySelectorAll('i[class*="fa"], .fa, .fas, .far, .fab, .fal');
                    icons.forEach(function(icon) {
                        // Completely reset and reapply styles
                        var className = icon.className;
                        icon.className = '';
                        void icon.offsetWidth; // Force reflow
                        icon.className = className;
                        icon.style.cssText = 'font-family: "Font Awesome 5 Free" !important; font-weight: 900 !important; display: inline-block !important; font-style: normal !important;';
                    });
                };
                
                // Run immediately
                window.fixAllIcons();
                
                // Run on DOMContentLoaded
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', window.fixAllIcons);
                } else {
                    window.fixAllIcons();
                }
                
                // Run on ALL Livewire events - NO CSS reload
                ['livewire:navigating', 'livewire:navigated', 'livewire:load', 'livewire:update'].forEach(function(event) {
                    document.addEventListener(event, function() {
                        // Fix icons multiple times with different delays
                        window.fixAllIcons();
                        setTimeout(window.fixAllIcons, 10);
                        setTimeout(window.fixAllIcons, 50);
                        setTimeout(window.fixAllIcons, 100);
                        setTimeout(window.fixAllIcons, 200);
                        setTimeout(window.fixAllIcons, 500);
                    });
                });
                
                // Also on Turbolinks events
                ['turbolinks:load', 'turbolinks:render'].forEach(function(event) {
                    document.addEventListener(event, function() {
                        window.fixAllIcons();
                        setTimeout(window.fixAllIcons, 50);
                        setTimeout(window.fixAllIcons, 150);
                    });
                });
                
                // MutationObserver - start IMMEDIATELY
                var observer = new MutationObserver(function(mutations) {
                    window.fixAllIcons();
                });
                observer.observe(document.documentElement, {
                    childList: true,
                    subtree: true
                });
                
            })();
        </script>
        
        {{-- Load app2.js with error suppression --}}
        <script>
            // Suppress Rails UJS and Livewire component errors
            (function() {
                var originalError = console.error;
                console.error = function() {
                    var msg = arguments[0] ? arguments[0].toString() : '';
                    if (msg.indexOf('rails-ujs') === -1 && 
                        msg.indexOf('Component already registered') === -1) {
                        originalError.apply(console, arguments);
                    }
                };
                
                // Catch thrown errors too
                window.addEventListener('error', function(e) {
                    if (e.message && (
                        e.message.indexOf('rails-ujs') > -1 ||
                        e.message.indexOf('Component already registered') > -1
                    )) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            })();
        </script>
        <script src="{{asset('assets/js/app2.js')}}" defer></script>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" 
                integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" 
                crossorigin="anonymous"
                defer></script>

        {{-- Laravel Echo for real-time WebSocket connections (Reverb) --}}
        @vite(['resources/js/app.js'])

        {{-- Livewire Scripts - Required for Livewire to work --}}
        @livewireScripts
        @livewireScriptConfig

        {{-- Session Recovery Helper - Auto-reconnect after idle/session expiry --}}
        <script>
        (function() {
            'use strict';
            
            // Session Recovery Manager
            window.SessionRecovery = {
                isRefreshing: false,
                refreshPromise: null,
                lastActivity: Date.now(),
                
                // Get current CSRF token from meta tag
                getToken: function() {
                    var meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') : null;
                },
                
                // Update CSRF token in meta tag
                setToken: function(token) {
                    var meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) {
                        meta.setAttribute('content', token);
                    }
                    // Also update any hidden CSRF inputs in forms
                    document.querySelectorAll('input[name="_token"]').forEach(function(input) {
                        input.value = token;
                    });
                    // Update Livewire if available
                    if (window.Livewire) {
                        try {
                            // Livewire 3 uses a different approach
                            if (window.Livewire.all) {
                                window.Livewire.all().forEach(function(component) {
                                    if (component.fingerprint) {
                                        component.fingerprint.csrf = token;
                                    }
                                });
                            }
                        } catch (e) {
                            console.log('Livewire token update skipped:', e.message);
                        }
                    }
                    return token;
                },
                
                // Refresh CSRF token from server
                refreshToken: function() {
                    var self = this;
                    
                    // If already refreshing, return existing promise
                    if (this.isRefreshing && this.refreshPromise) {
                        return this.refreshPromise;
                    }
                    
                    this.isRefreshing = true;
                    console.log('🔄 Refreshing session token...');
                    
                    this.refreshPromise = fetch('/csrf-refresh?_=' + Date.now(), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache, no-store, must-revalidate'
                        },
                        credentials: 'same-origin'
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Token refresh failed: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.token) {
                            self.setToken(data.token);
                            console.log('✅ Session token refreshed successfully');
                            self.lastActivity = Date.now();
                            return data.token;
                        }
                        throw new Error('No token in response');
                    })
                    .catch(function(error) {
                        console.error('❌ Token refresh error:', error);
                        throw error;
                    })
                    .finally(function() {
                        self.isRefreshing = false;
                        self.refreshPromise = null;
                    });
                    
                    return this.refreshPromise;
                },
                
                // Enhanced fetch with automatic retry on 419 (CSRF token mismatch) or network errors
                fetch: function(url, options, retryCount) {
                    var self = this;
                    retryCount = retryCount || 0;
                    options = options || {};
                    
                    // Always use fresh token from meta tag
                    if (!options.headers) {
                        options.headers = {};
                    }
                    
                    // Update CSRF token if present in headers
                    if (options.headers['X-CSRF-TOKEN']) {
                        options.headers['X-CSRF-TOKEN'] = this.getToken();
                    }
                    
                    return fetch(url, options)
                        .then(function(response) {
                            // If CSRF token mismatch (419) or session expired, refresh and retry
                            if (response.status === 419 || response.status === 401) {
                                if (retryCount < 2) {
                                    console.log('⚠️ Session expired (status: ' + response.status + '), refreshing...');
                                    return self.refreshToken().then(function(newToken) {
                                        // Update the token in headers for retry
                                        if (options.headers['X-CSRF-TOKEN']) {
                                            options.headers['X-CSRF-TOKEN'] = newToken;
                                        }
                                        return self.fetch(url, options, retryCount + 1);
                                    });
                                }
                            }
                            return response;
                        })
                        .catch(function(error) {
                            // On network error, try refreshing token and retry once
                            if (retryCount < 1 && (error.name === 'TypeError' || error.message.includes('network'))) {
                                console.log('⚠️ Network error, attempting recovery...');
                                return self.refreshToken()
                                    .then(function(newToken) {
                                        if (options.headers['X-CSRF-TOKEN']) {
                                            options.headers['X-CSRF-TOKEN'] = newToken;
                                        }
                                        return self.fetch(url, options, retryCount + 1);
                                    })
                                    .catch(function() {
                                        // If refresh also fails, throw original error
                                        throw error;
                                    });
                            }
                            throw error;
                        });
                },
                
                // Track user activity to keep session alive
                trackActivity: function() {
                    var self = this;
                    var events = ['mousedown', 'keydown', 'touchstart', 'scroll'];
                    
                    events.forEach(function(event) {
                        document.addEventListener(event, function() {
                            self.lastActivity = Date.now();
                        }, { passive: true });
                    });
                },
                
                // Proactively refresh token before it expires (optional keepalive)
                startKeepalive: function(intervalMinutes) {
                    var self = this;
                    intervalMinutes = intervalMinutes || 30; // Default 30 minutes
                    
                    setInterval(function() {
                        var idleTime = Date.now() - self.lastActivity;
                        // Only refresh if user was active in last hour
                        if (idleTime < 60 * 60 * 1000) {
                            self.refreshToken().catch(function() {});
                        }
                    }, intervalMinutes * 60 * 1000);
                },
                
                // Initialize
                init: function() {
                    this.trackActivity();
                    // Start keepalive every 30 minutes
                    this.startKeepalive(30);
                    console.log('🔒 Session recovery initialized');
                }
            };
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    window.SessionRecovery.init();
                });
            } else {
                window.SessionRecovery.init();
            }
        })();
        </script>

        @stack('js')
        
        <script>
        // Custom mobile loading overlay - Show on all navigation
        (function() {
            if (window.innerWidth <= 768) {
                const overlay = document.getElementById('mobile-loading-overlay');
                
                if (overlay) {
                    // Capture all link clicks in capture phase
                    document.addEventListener('click', function(e) {
                        // Check if it's a link
                        const link = e.target.closest('a');
                        if (link && !link.hasAttribute('target')) {
                            // Show loader for any internal navigation
                            overlay.classList.add('show');
                            
                            // Hide after page loads or after timeout
                            setTimeout(function() {
                                overlay.classList.remove('show');
                            }, 3000);
                        }
                    }, true);
                    
                    // Hide on page show (back/forward navigation)
                    window.addEventListener('pageshow', function() {
                        overlay.classList.remove('show');
                    });
                    
                    // Livewire specific events (if they fire)
                    window.addEventListener('livewire:navigating', function() {
                        overlay.classList.add('show');
                    });
                    
                    window.addEventListener('livewire:navigated', function() {
                        overlay.classList.remove('show');
                    });
                }
            }
        })();
        </script>
  
        @yield('registertype')
    </body>
</html>
