<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!empty($setting->favicon))
    <link href="{{ smart_asset($setting->favicon) }}" rel="shortcut icon" type="image/x-icon" />
    @endif
    <title>{{ $seoTitle ?? 'Evoory - Premium Escort Directory' }}</title>
    <meta name="description" content="{{ $seoDescription ?? 'Connect with escorts from Dubai and around the world. Premium escort directory with verified listings.' }}">
    @if(!empty($seoKeywords))
    <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    
    {{-- Preconnect for performance --}}
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
        body{margin:0;background:var(--bg-primary) !important;background-image:none !important;color:var(--text-primary);font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-weight:300}
        .ev-header{background:var(--bg-secondary);padding:12px 0;position:sticky;top:0;z-index:1000}
        .ev-container{max-width:1200px;margin:0 auto;padding:0 16px}
        .ev-logo{font-size:24px;font-weight:700;color:var(--accent);text-decoration:none}
        .ev-flex{display:flex}.ev-items-center{align-items:center}.ev-justify-between{justify-content:space-between}
    </style>
    
    {{-- Bootstrap 4 CSS + Font Awesome (required for grid, components, icons).
         Use the minified build (~15 KB smaller, identical content). --}}
    <link rel="stylesheet" href="{{smart_asset('assets/css/app.min.css')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    {{-- Main theme CSS (loaded after Bootstrap to override) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/evoory-theme.css') }}?v=20260416-1">

    {{-- Additional page-specific CSS --}}
    @stack('css')
    {{-- Cross-layout wire:navigate cleanup.
         When the user arrives at an app-evoory page (homepage, listing,
         profile details) via wire:navigate from a legacy-app page (news,
         etc.), Livewire's head morph leaves /assets/css/app.css | app2.css
         | app3.css | app4.css attached in <head>. Those bundles carry
         global rules (`body { background:#333 url(...) }`, `.fa
         { font-family:… !important }`, `a { color:#C1F11D !important }`)
         that fight the evoory-theme rules and break the layout — footer
         wraps wrong, modal positions wrong, advanced search shows a black
         backdrop. They are never needed on app-evoory, so strip them on
         every transition INTO an app-evoory route. The route check is
         essential — this listener stays alive across wire:navigate, so
         without it, leaving for a legacy page (e.g. /female-escort-news-in-X)
         would delete the legacy bundles that page actually loads. --}}
    <script>
    (function () {
        var LEGACY_HREF = /\/assets\/css\/app[2-4]?\.css(\?|$)/;
        function isAppEvooryRoute() {
            var path = location.pathname;
            // Homepage
            if (path === '/' || path === '') return true;
            // News pages render on legacy `app` layout — need the legacy CSS.
            // Match BEFORE the listing check because both paths contain
            // "-escorts" / "escort-" substrings.
            if (/escort-news-in-/.test(path)) return false;
            // Listing pages and profile detail pages render on app-evoory.
            // Patterns: /{gender}-escorts-in-{city}, /{...}/page/{n},
            // /{...}/{id}/{slug}.
            if (/^\/(female|male|shemale)-escorts-in-/.test(path)) return true;
            // Anything else: don't touch — could be on a legacy-layout page
            // that legitimately loads app.css / app2-4.css.
            return false;
        }
        function stripLegacyCss() {
            if (!isAppEvooryRoute()) return;
            document
                .querySelectorAll('link[rel="stylesheet"]')
                .forEach(function (link) {
                    if (LEGACY_HREF.test(link.getAttribute('href') || '')) {
                        link.parentNode.removeChild(link);
                    }
                });
        }
        stripLegacyCss();
        document.addEventListener('livewire:navigated', stripLegacyCss);
    })();
    </script>
</head>
<body>
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
