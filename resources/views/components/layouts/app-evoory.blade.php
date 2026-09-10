<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="NHKPt4-TOMI7fTCn2xKG6Mkeray0v9b-B7Bk9EEKHok" />
    {{-- Edge-cache CSRF workaround.
         Cloudflare caches listing HTML, so the inline meta csrf-token above may
         be a stale token from another visitor's session. Fetch a fresh token
         from /csrf-refresh (no-store) and overwrite the meta tag before any
         Livewire request fires. We also intercept Livewire 'request' events to
         inject the latest token into the X-CSRF-TOKEN / X-XSRF-TOKEN headers,
         and retry once on 419 after re-fetching. --}}
    <script>
    (function () {
        var REFRESH_URL = '/csrf-refresh';
        var latestToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        var refreshing = null;

        function setToken(token) {
            if (!token) return;
            latestToken = token;
            var m = document.querySelector('meta[name="csrf-token"]');
            if (m) m.setAttribute('content', token);
            if (window.jQuery) {
                window.jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
            }
        }

        function refresh() {
            if (refreshing) return refreshing;
            // Cache-busting query param: Cloudflare's "Cache Everything" rule
            // is what caused this whole problem in the first place. A unique
            // URL per request guarantees we always hit the origin, regardless
            // of whether the Cache-Control headers are honored.
            var url = REFRESH_URL + '?t=' + Date.now() + '_' + Math.random().toString(36).slice(2);
            refreshing = fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store'
            })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (data && data.token) setToken(data.token);
                    return data && data.token;
                })
                .catch(function () { return null; })
                .finally(function () { refreshing = null; });
            return refreshing;
        }

        // Refresh once on initial load. Runs in parallel with Livewire init —
        // most users won't reach the form before the fetch resolves.
        refresh();

        // Refresh again after Livewire is ready, and hook every outgoing
        // Livewire request to use the freshest token + retry once on 419.
        document.addEventListener('livewire:init', function () {
            if (!window.Livewire || typeof window.Livewire.hook !== 'function') return;

            window.Livewire.hook('request', function (payload) {
                var options = payload && payload.options;
                if (!options) return;
                options.headers = options.headers || {};
                if (latestToken) {
                    options.headers['X-CSRF-TOKEN'] = latestToken;
                    options.headers['X-XSRF-TOKEN'] = latestToken;
                }

                if (typeof payload.respond === 'function') {
                    payload.respond(function (resp) {
                        if (resp && resp.status === 419) {
                            // Token went stale mid-session — refresh and let
                            // Livewire's built-in retry/refresh handle the user
                            // flow. The next request will pick up the new token.
                            refresh();
                        }
                    });
                }
            });
        });
    })();
    </script>
    @if(!empty($setting->favicon))
    <link href="{{ smart_asset($setting->favicon) }}" rel="shortcut icon" type="image/x-icon" />
    @endif
    {{-- Per-page title override wins over the SeoComposer's $seoTitle so
         profile detail pages can show the profile name in the browser tab
         while listing pages keep their SEO-driven title. --}}
    <title>{{ $pageTitle ?? $seoTitle ?? 'Evoory - Premium Escort Directory' }}</title>
    <meta name="description" content="{{ $pageDescription ?? $seoDescription ?? 'Connect with escorts from Dubai and around the world. Premium escort directory with verified listings.' }}">
    @if(!empty($seoKeywords))
    <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    
    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://assets.evoory.com" crossorigin>
    <link rel="dns-prefetch" href="//assets.evoory.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    {{-- display=optional avoids the layout shift caused by a late font swap.
         Browsers use the system fallback if Inter doesn't load within ~100ms;
         on subsequent visits the font is cached and used immediately. Trade-off
         is intentional: zero CLS at the cost of a one-time fallback render for
         slow-connection visitors. --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=optional" rel="stylesheet">
    
    {{-- Critical CSS inline for faster FCP --}}
    <style>
        :root{--bg-primary:#0D1011;--bg-secondary:#111111;--accent:#C1F11D;--text-primary:#ffffff}
        html{min-height:100%}
        body{margin:0;background:var(--bg-primary) !important;background-image:none !important;color:var(--text-primary);font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-weight:300;display:flex;flex-direction:column;min-height:100vh}
        body > main{flex:1 0 auto}
        .ev-header{background:var(--bg-secondary);padding:12px 0;position:sticky;top:0;z-index:1000}
        .ev-container{max-width:1200px;margin:0 auto;padding:0 16px}
        .ev-logo{font-size:24px;font-weight:700;color:var(--accent);text-decoration:none}
        .ev-flex{display:flex}.ev-items-center{align-items:center}.ev-justify-between{justify-content:space-between}

        /* Anti-FOUC: mirrors the Bootstrap 3 visibility/dropdown/modal rules and
           the global link-color rule that live inside the 1.3 MB
           evoory-homepage.css bundle. Without these, a cold-cache load briefly
           shows the modal contents, dropdown items as a flat list, mobile-only
           controls on desktop, and every link as default-blue underlined text
           until the big bundle finishes downloading. */
        .modal{display:none}
        .dropdown-menu{display:none;position:absolute}
        .visible-xs,.visible-xs-block,.visible-xs-inline,.visible-xs-inline-block{display:none !important}
        @media (max-width:767px){
            .visible-xs,.visible-xs-block{display:block !important}
            .visible-xs-inline{display:inline !important}
            .visible-xs-inline-block{display:inline-block !important}
            .hidden-xs{display:none !important}
        }
        @media (min-width:768px){
            .form-inline .form-group{display:inline-block;margin-bottom:0;vertical-align:middle}
        }
        a{color:var(--accent);text-decoration:none}
        .nostyle-link,.nostyle-link *{color:inherit;text-decoration:none}
    </style>
    
    {{-- Bootstrap 4 CSS + Font Awesome (required for grid, components, icons).
         Use the minified build (~15 KB smaller, identical content). --}}
    <link rel="stylesheet" href="{{smart_asset('assets/css/app.min.css')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    {{-- Main theme CSS (loaded after Bootstrap to override) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/evoory-theme.css') }}?v=20260416-1">

    {{-- Listing-page CSS lives in the layout (not in @push) so the <link>
         elements survive wire:navigate. Pushing them caused Livewire's head
         merger to destroy + recreate the element on every back-to-listings
         navigation, forcing a fresh CSS parse and a visible reflicker.
         media="print" initially keeps the rules from applying on non-listing
         pages (preventing the legacy bundle's body/a/.fa rules from leaking
         onto homepage etc.). PHP picks the initial value from the current
         route so server-rendered listing pages are styled immediately; the
         small JS at the bottom of <head> flips it on wire:navigate. --}}
    @php
        $__isListingPage = request()->is('*-escorts-in-*');
    @endphp
    <link rel="stylesheet" id="evoory-listing-css"
          href="{{ asset('assets/css/evoory-homepage.css') }}?v={{ @filemtime(public_path('assets/css/evoory-homepage.css')) ?: 1 }}"
          media="{{ $__isListingPage ? 'all' : 'print' }}">
    <link rel="stylesheet" id="evoory-listing-inline-css"
          href="{{ asset('assets/css/listing-page-inline.css') }}?v={{ @filemtime(public_path('assets/css/listing-page-inline.css')) ?: 1 }}"
          media="{{ $__isListingPage ? 'all' : 'print' }}">
    <script>
    (function(){
        // Toggle listing-only CSS between media="all" and media="print" so its
        // rules only apply on listing/service pages. Matches BY HREF (not by
        // id), because Livewire's head merge can leave duplicate <link> tags
        // around when navigating between pages that push their own copy of
        // these stylesheets (service-page does); getElementById would only
        // touch the first match and leave the rest stuck.
        function isListingPath(path){
            return /-escorts-in-/.test(path);
        }
        function syncListingCss(){
            var on = !!document.querySelector('.ev-listing-page')
                  || isListingPath(window.location.pathname);
            var links = document.querySelectorAll(
                'link[href*="evoory-homepage.css"], link[href*="listing-page-inline.css"]'
            );
            links.forEach(function(el){ el.media = on ? 'all' : 'print'; });
        }
        document.addEventListener('livewire:navigated', syncListingCss);
        document.addEventListener('livewire:navigating', syncListingCss);
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncListingCss);
        } else {
            syncListingCss();
        }
    })();
    </script>

    {{-- Additional page-specific CSS --}}
    @stack('css')
</head>
<body>
    {{-- Admin Impersonation Banner. Rendered here (not inside the header
         partial) so it sits above every page that uses this layout,
         including /my-listings which is where the admin-as-user flow
         lands. --}}
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
    @if(session('impersonation_started'))
    (function () {
      var startTime = new Date('{{ session('impersonation_started') }}');
      function updateTimer() {
        var el = document.getElementById('impersonation-timer');
        if (!el) return;
        var diff = Math.floor((new Date() - startTime) / 1000);
        var minutes = Math.floor(diff / 60);
        var seconds = diff % 60;
        el.innerHTML = '(Active for ' + minutes + 'm ' + seconds + 's)';
      }
      setInterval(updateTimer, 1000);
      updateTimer();
    })();
    @endif
    </script>
    @endif

    {{-- Header --}}
    @include('components.layouts.header-evoory')

    {{-- Main Content --}}
    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.layouts.footer-evoory')

    {{-- Mobile bottom nav for authenticated users --}}
    @include('components.mobile-user-bottom-nav')

    {{-- jQuery + Bootstrap JS (required for components and page scripts) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

    {{-- Theme JS - Deferred for performance --}}
    <script src="{{ asset('assets/js/evoory-theme.js') }}?v=20260318-4" defer></script>

    {{-- Additional page-specific JS --}}
    @stack('js')
</body>
</html>
