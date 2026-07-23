@section('headerform')
@include('components.layouts.headerform')
@endsection
 
@push('css')

{{-- evoory-homepage.css and listing-page-inline.css are now loaded by the
     layout (components/layouts/app-evoory.blade.php) with media="print" on
     non-listing pages. Loading them there — instead of via @push — means the
     <link> elements survive wire:navigate, so navigating away and back never
     re-creates them, never re-parses 369 KB of CSS, and never re-flickers.
     The media attribute is toggled by a tiny script in the layout on every
     livewire:navigated event. --}}

{{-- site-inline is non-critical, keep async --}}
<link rel="preload" href="{{ asset('assets/css/site-inline.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{ asset('assets/css/site-inline.min.css') }}" /></noscript>

<!-- Preload first profile images for faster LCP -->
@if(isset($profiles) && $profiles->count() > 0)
    @foreach($profiles->take(3) as $preloadProfile)
        @if(!empty($preloadProfile->coverimg->image))
            <link rel="preload" as="image" href="{{webp_asset('userimages/'.$preloadProfile->user_id.'/'.$preloadProfile->id.'/'.$preloadProfile->coverimg->image)}}" fetchpriority="high">
        @elseif(!empty($preloadProfile->singleimg->image))
            <link rel="preload" as="image" href="{{webp_asset('userimages/'.$preloadProfile->user_id.'/'.$preloadProfile->id.'/'.$preloadProfile->singleimg->image)}}" fetchpriority="high">
        @endif
    @endforeach
@endif

<!-- Critical CSS for select components (load immediately) -->
<link rel="preload" href="{{smart_asset('chosen/chosen.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{smart_asset('chosen/chosen.css')}}"></noscript>

<!-- Non-critical CSS (defer loading) -->
<link rel="preload" href="{{smart_asset('chosen/docsupport/prism.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{smart_asset('chosen/docsupport/prism.css')}}"></noscript>

<!-- Select2 CSS - load deferred -->
<link rel="preload" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"></noscript>
    <style>
        /* Critical CSS - inline for fastest rendering */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
        }

        .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid rgb(68 68 68);
            border-top: 4px solid rgb(68 68 68);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Critical layout CSS */
        .img-responsive { max-width: 100%; height: auto; }
        .hand:hover { cursor: pointer; }
        
        /* Defer non-critical animations and effects */
        .premium-effects { opacity: 0; transition: opacity 0.3s ease; }
        .premium-effects.loaded { opacity: 1; }
        
        /* Image optimization with shimmer preloader */
        img[loading="lazy"] {
            background: linear-gradient(90deg, #1a1a1a 0%, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%, #1a1a1a 100%) !important;
            background-size: 200% 100% !important;
            animation: shimmer 1.5s ease-in-out infinite !important;
            min-height: 60px;
            position: relative;
        }
        
        @keyframes shimmer {
            0% { 
                background-position: -200% 0; 
            }
            100% { 
                background-position: 200% 0; 
            }
        }
        
        img[loading="lazy"].loaded {
            animation: none !important;
            background: transparent !important;
        }
        
        /* Add shimmer to image wrappers */
        .img-wrapper img:not(.loaded),
        .image-wrapper img:not(.loaded) {
            background: linear-gradient(90deg, #1a1a1a 0%, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%, #1a1a1a 100%) !important;
            background-size: 200% 100% !important;
            animation: shimmer 1.5s ease-in-out infinite !important;
        }

        /* === Themed city search dropdown (overrides .citys/.opt from listing-page-inline.css) === */
        #cityappend.citys {
            width: 100% !important;
            height: auto !important;
            max-height: 320px !important;
            overflow-y: auto !important;
            top: calc(100% + 4px) !important;
            left: 0 !important;
            right: 0 !important;
            background: #1D2224 !important;
            border: 1px solid #2a2a2a !important;
            border-radius: 8px !important;
            padding: 4px 0 !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
            z-index: 9999 !important;
        }
        #cityappend .opt {
            display: flex !important;
            align-items: center !important;
            padding: 10px 16px !important;
            margin: 0 4px !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            color: #fff !important;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
        }
        #cityappend .opt:hover {
            background: #262C2F !important;
            color: #C1F11D !important;
        }
        #cityappend::-webkit-scrollbar { width: 6px; }
        #cityappend::-webkit-scrollbar-track { background: transparent; }
        #cityappend::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 3px; }
        #cityappend::-webkit-scrollbar-thumb:hover { background: #3a3a3a; }
        #cityappend { scrollbar-width: thin; scrollbar-color: #2a2a2a transparent; }

        /* Empty-state Subscribe button (shown when a city has no profiles).
           Targets both the new `.ev-empty-subscribe-btn` class AND the legacy
           `.subscribe-btn-wrapper .btn-primary` selector so the override works
           against page-cached HTML that still has the old class names, and to
           beat evoory-homepage.css's `.btn-primary { background:#C1F11D
           linear-gradient(#C1F11D,#d3980b) repeat-x }` lime→orange gradient. */
        .ev-empty-subscribe-btn,
        .subscribe-btn-wrapper .btn,
        .subscribe-btn-wrapper .btn-primary {
            display: inline-block !important;
            background: #C1F11D !important;
            background-image: none !important;
            color: #000 !important;
            border: none !important;
            padding: 5px 18px !important;
            border-radius: 24px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: background 0.15s ease !important;
        }
        .ev-empty-subscribe-btn:hover,
        .ev-empty-subscribe-btn:focus,
        .subscribe-btn-wrapper .btn:hover,
        .subscribe-btn-wrapper .btn:focus,
        .subscribe-btn-wrapper .btn-primary:hover,
        .subscribe-btn-wrapper .btn-primary:focus {
            background: #d4f84d !important;
            background-image: none !important;
            color: #000 !important;
            text-decoration: none !important;
        }

        /* === Empty-city listing fallback (no profiles in selected city) === */
        .ev-empty-listings-wrap,
        .ev-empty-listings-wrap *,
        .ev-fallback-section,
        .ev-fallback-section * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }
        .ev-empty-listings-wrap.col-md-12,
        .ev-empty-listings-wrap { width: 100%; padding-left: 0 !important; padding-right: 0 !important; }
        .ev-empty-listings-card {
            padding: 0;
            margin: 0 0 8px;
        }
        .ev-empty-listings-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #C77DFF !important;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px;
        }
        .ev-empty-listings-title a,
        .ev-empty-listings-title span { color: #C77DFF !important; }
        .ev-empty-listings-ban {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #C77DFF !important;
        }
        .ev-empty-listings-ban svg { display: block; }
        .ev-empty-listings-sub {
            color: #C77DFF;
            opacity: 0.85;
            font-size: 14px;
            margin: 0 0 8px;
        }
        .ev-empty-listings-cta {
            color: #C1F11D;
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 12px;
            max-width: 520px;
        }
        .ev-empty-listings-wrap .subscribe-btn-wrapper { margin: 0 0 4px; }
        .ev-empty-listings-wrap .ev-empty-subscribe-btn {
            background: transparent !important;
            color: #fff !important;
            border: 1px solid #3a3a3a !important;
            border-radius: 999px !important;
            padding: 8px 28px !important;
        }
        .ev-empty-listings-wrap .ev-empty-subscribe-btn:hover,
        .ev-empty-listings-wrap .ev-empty-subscribe-btn:focus {
            background: #1f1f1f !important;
            color: #fff !important;
            border-color: #4a4a4a !important;
        }

        /* Fallback (nearby/global) section header */
        .ev-fallback-section {
            margin-top: 10px;
            padding-top: 12px;
            border-top: 1px solid #2a2a2a;
        }
        .ev-fallback-title {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 10px;
        }
        .ev-fallback-section .ev-fallback-title .ev-fallback-accent,
        .ev-fallback-accent {
            color: #C1F11D !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-style: normal !important;
            font-weight: inherit !important;
            font-size: inherit !important;
        }
        .ev-empty-listings-wrap .ev-fallback-sub,
        .ev-fallback-sub {
            color: #8a8a8a !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            line-height: 1.5 !important;
            text-transform: none !important;
            letter-spacing: normal !important;
            margin: 0 0 18px !important;
        }
        .listings .ev-fallback-card,
        .ev-empty-listings-wrap .ev-fallback-card {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: 12px !important;
            padding: 14px 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        /* The mobile-only title is hidden on desktop. */
        .ev-empty-listings-wrap .ev-fallback-name--mobile { display: none !important; }
        .ev-empty-listings-wrap .ev-fallback-card + .ev-fallback-card {
            border-top: 1px solid #1f1f1f !important;
        }
        /* When the profile has no thumbnails, hide the empty thumbs column slot so the
           text expands naturally without a 65px gap. */
        .ev-empty-listings-wrap .ev-fallback-card--no-thumbs .ev-fallback-thumbs { display: none !important; }
        .listings .ev-fallback-main,
        .ev-empty-listings-wrap .ev-fallback-main {
            flex: 0 0 200px !important;
            width: 200px !important;
            max-width: 200px !important;
            height: 210px !important;
            display: block !important;
            border-radius: 6px !important;
            overflow: hidden !important;
            background: #111 !important;
            line-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .listings .ev-fallback-main img,
        .ev-empty-listings-wrap .ev-fallback-main img {
            width: 200px !important;
            height: 210px !important;
            max-width: 200px !important;
            object-fit: cover !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }
        .listings .ev-fallback-thumbs,
        .ev-empty-listings-wrap .ev-fallback-thumbs {
            flex: 0 0 65px !important;
            width: 65px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 5px !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .listings .ev-fallback-thumb,
        .ev-empty-listings-wrap .ev-fallback-thumb {
            display: block !important;
            width: 65px !important;
            height: 65px !important;
            max-width: 65px !important;
            border-radius: 4px !important;
            overflow: hidden !important;
            background: #111 !important;
            line-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .listings .ev-fallback-thumb img,
        .ev-empty-listings-wrap .ev-fallback-thumb img {
            width: 65px !important;
            height: 65px !important;
            max-width: 65px !important;
            object-fit: cover !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .listings .ev-fallback-info,
        .ev-empty-listings-wrap .ev-fallback-info {
            flex: 1 1 0 !important;
            min-width: 0 !important;
            max-width: 100% !important;
            padding-left: 8px !important;
            overflow: hidden !important;
        }
        .ev-empty-listings-wrap .ev-fallback-name {
            color: #fff !important;
            font-size: 22px !important;
            font-weight: 700 !important;
            margin: 0 0 10px !important;
            line-height: 1.2 !important;
            /* Single-line truncation with ellipsis. Some imported profiles
               from MR have $profile->name containing the entire "Name –
               Nationality escort in City" string (see
               MassageRepublicImporter.php:172), which wraps to two lines
               and pushes the description down. Do NOT set display here —
               .ev-fallback-name is shared by --mobile and --desktop
               variants and each one relies on responsive display:none
               rules to hide the wrong variant per breakpoint. Forcing
               display:block here made BOTH titles render at once. */
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            overflow-wrap: normal !important;
            word-break: normal !important;
            max-width: 100% !important;
        }
        .ev-empty-listings-wrap .ev-fallback-name a {
            color: #fff !important;
            text-decoration: none !important;
            display: inline-block !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            vertical-align: bottom !important;
        }

        /* Regular listing card titles — same problem as fallback cards:
           imported MR profiles have the full "Name – Nationality escort
           in City" string in $profile->name, so the H2 wraps to a
           second line and pushes the description down. Clamp to one line
           with ellipsis. The badge is inside the <a>, so it gets
           truncated together with the name if the row overflows —
           acceptable trade-off vs. a wrapped title. Applied to premium /
           featured / basic / free listing variants (all share
           .listing-info h2). */
        .listings .listing-li .listing-info h2,
        .listings .listing-li .listing-info h2 a.nostyle-link {
            display: block !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            overflow-wrap: normal !important;
            word-break: normal !important;
            max-width: 100% !important;
        }
        .ev-empty-listings-wrap .ev-fallback-desc,
        .ev-empty-listings-wrap .ev-fallback-info a p,
        .ev-empty-listings-wrap .ev-fallback-info p {
            color: #c9c9c9 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            line-height: 1.5 !important;
            margin: 0 0 14px !important;
            overflow-wrap: anywhere !important;
            word-break: break-word !important;
            white-space: normal !important;
            max-width: 100% !important;
        }
        .ev-empty-listings-wrap .ev-fallback-see-more {
            display: inline-block !important;
            background: #1a1a1a !important;
            color: #C1F11D !important;
            padding: 8px 18px !important;
            border-radius: 999px !important;
            font-size: 13px !important;
            text-decoration: none !important;
            transition: background 0.15s ease !important;
        }
        .ev-empty-listings-wrap .ev-fallback-see-more:hover {
            background: #2a2a2a !important;
            color: #C1F11D !important;
        }
        @media (max-width: 640px) {
            /* Mobile layout: title spans full width above image row, description+button
               below it (also full width). Image row is a single connected unit — main
               on the left with rounded left corners, 3 narrower thumbs stacked tightly
               on the right with only the outer-right corners rounded (top-right of
               first thumb, bottom-right of last thumb). */
            .listings .ev-empty-listings-wrap .ev-fallback-card,
            .ev-empty-listings-wrap .ev-fallback-card { gap: 0 !important; flex-wrap: wrap !important; }
            .ev-empty-listings-wrap .ev-fallback-name--mobile {
                display: block !important;
                width: 100% !important;
                flex: 0 0 100% !important;
                order: -1 !important;
                margin: 0 0 10px !important;
                font-size: 18px !important;
            }
            .ev-empty-listings-wrap .ev-fallback-name--desktop { display: none !important; }
            .listings .ev-empty-listings-wrap .ev-fallback-main,
            .ev-empty-listings-wrap .ev-fallback-main {
                flex: 0 0 78% !important;
                width: 78% !important;
                max-width: none !important;
                height: auto !important;
                aspect-ratio: 1 / 1 !important;
                border-radius: 10px 0 0 10px !important;
            }
            .listings .ev-empty-listings-wrap .ev-fallback-main img,
            .ev-empty-listings-wrap .ev-fallback-main img {
                width: 98% !important;
                height: 100% !important;
                max-width: none !important;
                border-radius: 0 !important;
            }
            .listings .ev-empty-listings-wrap .ev-fallback-thumbs,
            .ev-empty-listings-wrap .ev-fallback-thumbs {
                flex: 0 0 22% !important;
                width: 22% !important;
                gap: 0 !important;
            }
            .listings .ev-empty-listings-wrap .ev-fallback-thumb,
            .ev-empty-listings-wrap .ev-fallback-thumb {
                      width: 100% !important;
        max-width: none !important;
        height: 103px !important;
        aspect-ratio: 1 / 1 !important;
        border-radius: 0 !important;
        margin-bottom: 10px !important;
            }
            /* No-thumb cards: main image expands full width on mobile too. */
            .ev-empty-listings-wrap .ev-fallback-card--no-thumbs .ev-fallback-main {
                flex: 0 0 100% !important;
                width: 100% !important;
                border-radius: 10px !important;
            }
            .ev-empty-listings-wrap .ev-fallback-card--no-thumbs .ev-fallback-main img {
                width: 100% !important;
            }
            .ev-empty-listings-wrap .ev-fallback-thumb img {
                width: 100% !important;
                height: 100% !important;
                max-width: none !important;
                border-radius: 0 !important;
            }
            .ev-empty-listings-wrap .ev-fallback-thumb:first-child { border-radius: 0 10px 0 0 !important; }
            .ev-empty-listings-wrap .ev-fallback-thumb:last-child  { border-radius: 0 0 10px 0 !important; }
            .ev-empty-listings-wrap .ev-fallback-info {
                flex: 0 0 100% !important;
                width: 100% !important;
                order: 99 !important;
                padding-left: 0 !important;
                margin-top: 12px !important;
            }
            .ev-empty-listings-wrap .ev-fallback-desc { text-align: left !important; }
            .ev-empty-listings-wrap .ev-fallback-see-more { display: block !important; text-align: center !important; margin: 0 auto !important; }
        }

        /* VIP Positioning promo (shown under the fallback profiles) */
        .ev-vip-promo {
            margin-top: 22px;
            padding: 22px 26px 24px;
            background: #0a0a0a;
            border: 1px solid #2a2a2a;
            border-radius: 14px;
            position: relative;
        }
        .ev-vip-promo-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .ev-vip-badge {
            display: inline-block;
            padding: 6px 14px;
            border: 1px solid #C1F11D;
            color: #C1F11D;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .ev-vip-crown {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid #2a2a2a;
            color: #C1F11D;
        }
        .ev-vip-title {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 6px;
        }
        .ev-vip-sub {
            color: #c9c9c9;
            font-size: 14px;
            margin: 0 0 14px;
        }
        .ev-vip-perks {
            list-style: none;
            padding: 0;
            margin: 0 0 18px;
        }
        .ev-vip-perks li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #b3b3b3;
            font-size: 13px;
            margin-bottom: 6px;
        }
        .ev-vip-perks li:last-child { margin-bottom: 0; }
        .ev-vip-perk-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
        }
        .ev-vip-cta {
            display: inline-block;
            background: #C1F11D;
            color: #000 !important;
            padding: 9px 24px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none !important;
            transition: background 0.15s ease;
        }
        .ev-vip-cta:hover,
        .ev-vip-cta:focus {
            background: #d4f84d;
            color: #000 !important;
            text-decoration: none !important;
        }

        /* === Subscribe newsletter modal (mirrors user-account modal styles) === */
        [x-cloak] { display: none !important; }
        .ev-empty-listings-wrap .ev-modal-overlay {
            background: rgba(0,0,0,0.85);
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 99999;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 3rem 1rem;
        }
        .ev-empty-listings-wrap .ev-modal {
            background: #1a1a1a;
            color: #fff;
            border-radius: 12px;
            border: 1px solid #2a2a2a;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 500px;
        }
        .ev-empty-listings-wrap .ev-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #2a2a2a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ev-empty-listings-wrap .ev-modal-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .ev-empty-listings-wrap .ev-modal-close {
            background: #222;
            border: 1px solid #2a2a2a;
            color: #fff;
            font-size: 1.25rem;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ev-empty-listings-wrap .ev-modal-close:hover { background: #2a2a2a; }
        .ev-empty-listings-wrap .ev-modal-body { padding: 1.5rem; color: #fff; }
        .ev-empty-listings-wrap .ev-modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #2a2a2a;
            text-align: right;
        }
        .ev-empty-listings-wrap .ev-search-input {
            display: flex;
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            overflow: hidden;
            background: #111;
        }
        .ev-empty-listings-wrap .ev-search-input span {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            color: #fff;
        }
        .ev-empty-listings-wrap .ev-search-input input {
            flex: 1;
            padding: 0.75rem;
            background: transparent;
            border: none;
            color: #fff;
            outline: none;
            font-size: 0.95rem;
        }
        .ev-empty-listings-wrap .ev-search-input input::placeholder { color: #666; }
        .ev-empty-listings-wrap .ev-search-input button {
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            color: #C1F11D;
            cursor: pointer;
        }
        .ev-empty-listings-wrap .ev-city-tag {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1rem;
            background: #111;
            border-radius: 8px;
            border: 1px solid #2a2a2a;
            color: #fff;
        }
        .ev-empty-listings-wrap .ev-city-tag button { color: #C1F11D; }
        .ev-empty-listings-wrap .ev-dropdown-results {
            position: absolute;
            width: 100%;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            background: #111;
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            margin-top: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .ev-empty-listings-wrap .ev-dropdown-results button {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            background: transparent;
            color: #fff;
            border: none;
            border-bottom: 1px solid #2a2a2a;
            text-align: left;
            cursor: pointer;
            font-size: 14px;
        }
        .ev-empty-listings-wrap .ev-dropdown-results button:hover { background: #222; }
        .ev-empty-listings-wrap .ev-buy-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #C1F11D;
            color: #000;
            border: none;
            border-radius: 21.5px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .ev-empty-listings-wrap .ev-buy-btn:hover { background: #d4f84d; }
    </style>

    <!-- Load non-critical styles asynchronously -->
    <script>
        // Load non-critical CSS after page load
        window.addEventListener('load', function() {
            // Add loading class for smoother transitions
            document.body.classList.add('page-loaded');
            
            // Initialize premium effects
            setTimeout(function() {
                document.querySelectorAll('.premium-effects').forEach(function(el) {
                    el.classList.add('loaded');
                });
            }, 100);
        });
        
        // Handle image loading animations immediately (don't wait for window load)
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🖼️ Setting up image shimmer effect...');
            
            const images = document.querySelectorAll('img[loading="lazy"], .img-wrapper img, .image-wrapper img');
            console.log('Found ' + images.length + ' images');
            
            images.forEach(function(img) {
                // If image already loaded
                if (img.complete && img.naturalHeight !== 0) {
                    img.classList.add('loaded');
                } else {
                    // Add loaded class when image loads
                    img.addEventListener('load', function() {
                        console.log('✅ Image loaded:', this.alt || this.src);
                        this.classList.add('loaded');
                    });
                    // Handle error case
                    img.addEventListener('error', function() {
                        console.log('❌ Image error:', this.src);
                        this.classList.add('loaded');
                    });
                }
            });
        });
    </script>

@endpush

<div class="ev-listing-page">

{{-- Include common search header (includes ESCORTS/WHAT'S NEW tabs) --}}
@include('components.search-header')

@if($showMobileSearch)
<div class="mobile-search-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Search for Escorts</h4>
            <button type="button" class="close text-white" wire:click="toggleMobileSearch">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <form class="mobile-search-form" wire:submit.prevent="search">
                <!-- Basic search fields -->
                <div class="form-group mb-3">
                    <label for="mobile_gender">I'm looking for</label>
                    <select class="form-control form-control-lg" id="mobile_gender" wire:model="gender">
                        <option value="female" selected>Female escorts</option>
                        <option value="male">Male escorts</option>
                        <option value="shemale">Shemale escorts</option>
                    </select>
                </div>
                
                <!-- City search field -->
                <div class="form-group mb-3">
                  <label for="mobile_city_search">City</label>
                  <div class="position-relative">
                      <input type="text" id="mobile_city_search" class="form-control form-control-lg" 
                             value="{{ $selectedcity }}" placeholder="Type city..." autocomplete="off">
                      <div id="mobile_city_results" class="dropdown-menu w-100" style="display:none; max-height:250px; overflow-y:auto;"></div>
                  </div>
                  <small id="mobile_selected_city_name" class="form-text text-muted">
                      {{ $selectedcity ? 'Selected: ' . $selectedcity : 'No city selected' }}
                  </small>
              </div>
            
            <!-- Currency field -->
            <div class="form-group mb-3">
                <label for="currency">Currency</label>
                <select
                onfocus="if(jQuery && jQuery.fn.select2 && !jQuery(this).hasClass('select2-hidden-accessible')){jQuery(this).select2({theme:'default',width:'100%',dropdownParent:jQuery('.mobile-search-modal')});jQuery(this).select2('open');}"
                 class="form-control form-control-lg select2-single" id="currency" wire:model="currency">
                    @foreach($currencies as $cur)
                    <option value="{{$cur->id}}" @if($cur->id == $currency) selected @endif>{{$cur->code}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mb-3">
                <label for="rate">Price / hour</label>
                <input type="number" class="form-control form-control-lg" id="rate" wire:model="rate" 
                       placeholder="Price" min="0" step="50">
            </div>
            
            <div class="form-group mb-3">
                <label for="mobile_services">Services</label>
                <div class="services-input-wrapper" wire:ignore>
                    <input type="text" 
                           id="mobile_services_input_box" 
                           class="form-control mobile-services-input" 
                           placeholder="Click to select services..." 
                           readonly
                           style="background-color: #333; color: white; border: 1px solid #555; cursor: pointer;">
                    
                    <div id="mobile_services_dropdown" class="services-dropdown" style="display: none;">
                        <div class="services-list">
                            @foreach($services as $service)
                                <div class="service-option" data-value="{{ $service->id }}">
                                    <input type="checkbox" id="mobile_service_{{ $service->id }}" class="service-checkbox">
                                    <label for="mobile_service_{{ $service->id }}" class="service-label">{{ $service->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <input type="hidden" id="mobile_services_hidden" wire:model="sservices">
                </div>
            </div>
            <!-- Bust size -->
            <div class="form-group mb-3">
                <label for="bust">Bust size</label>
                <select 
                class="form-control form-control-lg select2-single" id="bust" wire:model="buts">
                    <option value="">Any</option>
                    @foreach($busts as $bust)
                    <option value="{{$bust->id}}">{{$bust->name}}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Orientation -->
            <div class="form-group mb-3">
                <label for="orientation">Orientation</label>
                <select class="form-control form-control-lg" id="orientation" wire:model="ori">
                    <option value="">Any</option>
                    <option value="1">Heterosexual</option>
                    <option value="2">Bisexual</option>
                    <option value="3">Lesbian or Gay</option>
                </select>
            </div>
            
            <!-- Checkboxes -->
            <div class="form-group mb-3">
                <div class="row">
                    <div class="col-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="mobile_verified" wire:model="verified">
                            <label class="form-check-label" for="mobile_verified">
                                 Verified
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="incall" wire:model="incall">
                            <label class="form-check-label" for="incall">
                                Incalls
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="outcall" wire:model="outcall">
                            <label class="form-check-label" for="outcall">
                                Outcalls
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="nonsmoker" wire:model="nonsmoker">
                            <label class="form-check-label" for="nonsmoker">
                                Non-smoker
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="withreviews" wire:model="withreviews">
                            <label class="form-check-label" for="withreviews">
                                With reviews
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ethnicity -->
            <div class="form-group mb-3">
                <label for="ethnicity">Ethnicity</label>
                <select 
                onfocus="if(jQuery && jQuery.fn.select2 && !jQuery(this).hasClass('select2-hidden-accessible')){jQuery(this).select2({theme:'default',width:'100%',dropdownParent:jQuery('.mobile-search-modal')});jQuery(this).select2('open');}" class="form-control form-control-lg select2-single" id="ethnicity" wire:model="ethnicity">
                    <option value="">Any</option>
                    @foreach($ethnicities as $eth)
                    <option value="{{$eth->id}}">{{$eth->name}}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Nationality -->
            <div class="form-group mb-3">
                <label for="nationality">Nationality</label>
                <select 
                onfocus="if(jQuery && jQuery.fn.select2 && !jQuery(this).hasClass('select2-hidden-accessible')){jQuery(this).select2({theme:'default',width:'100%',dropdownParent:jQuery('.mobile-search-modal')});jQuery(this).select2('open');}" class="form-control form-control-lg select2-single" id="nationality" wire:model="nationality">
                    <option value="">Any</option>
                    @foreach($countries as $country)
                    <option value="{{$country->id}}">{{$country->nicename}}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Age range -->
            <div class="form-group mb-3">
                <label>Age range</label>
                    
                        <select class="form-control form-control-lg" id="agefrom" wire:model="agefrom">
                            <option value="">From</option>
                            <option value="18">18</option>
                            <option value="21">21</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                            <option value="60">60</option>
                        </select>
                    
                    
                        <select class="form-control form-control-lg" id="ageto" wire:model="ageto">
                            <option value="">To</option>
                            <option value="18">18</option>
                            <option value="21">21</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                            <option value="60">60</option>
                        </select>

            </div>
            
            <!-- Height range -->
            <div class="form-group mb-3">
                <label>Height range (cm)</label>
                
                  
                        <select class="form-control form-control-lg" id="heightfrom" wire:model="heightfrom">
                            <option value="">From</option>
                            <option value="140">140</option>
                            <option value="150">150</option>
                            <option value="160">160</option>
                            <option value="170">170</option>
                            <option value="180">180</option>
                            <option value="190">190</option>
                            <option value="200">200</option>
                            <option value="210">210</option>
                            <option value="220">220</option>
                        </select>
             
                        <select class="form-control form-control-lg" id="heightto" wire:model="heightto">
                            <option value="">To</option>
                            <option value="140">140</option>
                            <option value="150">150</option>
                            <option value="160">160</option>
                            <option value="170">170</option>
                            <option value="180">180</option>
                            <option value="190">190</option>
                            <option value="200">200</option>
                            <option value="210">210</option>
                            <option value="220">220</option>
                        </select>
               
            </div>
            
            <!-- Name -->
            <div class="form-group mb-3">
                <label for="name">Name</label>
                <input type="text" class="form-control form-control-lg" id="name" wire:model="name" placeholder="Search by name">
            </div>
            
            <!-- Language -->
            <div class="form-group mb-3">
                <label for="language">Language</label>
                <select
                onfocus="if(jQuery && jQuery.fn.select2 && !jQuery(this).hasClass('select2-hidden-accessible')){jQuery(this).select2({theme:'default',width:'100%',dropdownParent:jQuery('.mobile-search-modal')});jQuery(this).select2('open');}" class="form-control form-control-lg select2-single" id="language" wire:model="language">
                    <option value="">Any</option>
                    @foreach($languages as $lang)
                    <option value="{{$lang->id}}">{{$lang->name}}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Shaved -->
            <div class="form-group mb-3">
                <label for="isshaved">Shaved</label>
                <select class="form-control form-control-lg" id="isshaved" wire:model="isshaved">
                    <option value="">Any</option>
                    <option value="no">No</option>
                    <option value="partially">Partially</option>
                    <option value="yes">Yes</option>
                </select>
            </div>
            
            <!-- Hair color -->
            <div class="form-group mb-3">
                <label for="haircolor">Hair color</label>
                <select onfocus="if(jQuery && jQuery.fn.select2 && !jQuery(this).hasClass('select2-hidden-accessible')){jQuery(this).select2({theme:'default',width:'100%',dropdownParent:jQuery('.mobile-search-modal')});jQuery(this).select2('open');}"
                   class="form-control form-control-lg select2-single" id="haircolor" wire:model="haircolor">
                    <option value="">Any</option>
                    @foreach($hairs as $hair)
                    <option value="{{$hair->id}}">{{$hair->name}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg btn-block">
                  <i class="fa fa-search"></i> Search
              </button>
          </div>
      </form>
  </div>
</div>
</div>



@endif

{{-- Mobile Location Bar --}}
<div class="ev-mobile-location-bar visible-xs">
    <div class="ev-location-pill">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 10c0 6-9 13-9 13s-9-7-9-13a9 9 0 0 1 18 0z"></path>
            <circle cx="12" cy="10" r="3"></circle>
        </svg>
        <span>{{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}</span>
    </div>
    <div class="ev-location-actions">
        <a href="{{ route('mobile.search', ['gender' => $gender ?? 'female', 'city' => $selectedcity ?? 'Dubai']) }}" class="ev-location-action-btn" aria-label="Advanced Search">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </a>
        <button type="button" class="ev-location-action-btn" onclick="document.getElementById('evQuickFilters').style.display='flex'" aria-label="Filters">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>
                <circle cx="8" cy="6" r="1.5" fill="currentColor"></circle>
                <circle cx="16" cy="12" r="1.5" fill="currentColor"></circle>
                <circle cx="10" cy="18" r="1.5" fill="currentColor"></circle>
            </svg>
        </button>
    </div>
</div>

{{-- Mobile Quick Filters Popup --}}
<div id="evQuickFilters" class="ev-qf-overlay visible-xs" style="display:none;" onclick="if(event.target===this)this.style.display='none';" wire:ignore>
    <div class="ev-qf-panel" role="dialog" aria-label="Filters">
        <div class="ev-qf-head">
            <span class="ev-qf-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="18" x2="20" y2="18"></line><circle cx="8" cy="6" r="1.5" fill="#C1F11D"></circle><circle cx="16" cy="12" r="1.5" fill="#C1F11D"></circle><circle cx="10" cy="18" r="1.5" fill="#C1F11D"></circle></svg>
                Filters
            </span>
            <button type="button" class="ev-qf-close" onclick="document.getElementById('evQuickFilters').style.display='none'" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="ev-qf-body">
            <form id="evQuickFiltersForm" onsubmit="return false;">
                {{-- Category --}}
                <div class="ev-qf-group">
                    <label>Category</label>
                    <div class="ev-qf-custom-dd" data-qf-dd data-qf-name="gender">
                        <button type="button" class="ev-qf-dd-trigger">
                            <span class="ev-qf-dd-label" data-dd-label>{{ ucfirst($gender ?? 'female') }} escorts</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="ev-qf-dd-menu" role="listbox">
                            <ul class="ev-qf-dd-options">
                                <li data-val="female">Female escorts</li>
                                <li data-val="male">Male escorts</li>
                                <li data-val="shemale">Shemale escorts</li>
                            </ul>
                        </div>
                        <input type="hidden" id="ev-qf-gender" value="{{ $gender ?? 'female' }}">
                    </div>
                </div>

                {{-- Location --}}
                <div class="ev-qf-group">
                    <label>Location</label>
                    <div class="ev-qf-city-wrap">
                        <input type="text" id="ev-qf-city" class="ev-qf-input" placeholder="Enter city or area" value="{{ $selectedcity ?? '' }}" autocomplete="off">
                        <input type="hidden" id="ev-qf-city-id" value="{{ $city ?? '' }}">
                        <ul id="ev-qf-city-results" class="ev-qf-city-results" style="display:none;"></ul>
                    </div>
                </div>

                {{-- Price Range --}}
                <div class="ev-qf-group">
                    <label>Price Range</label>
                    <div class="ev-qf-price-row">
                        <div class="ev-qf-custom-dd ev-qf-currency" data-qf-dd data-qf-name="currency" data-qf-searchable="1">
                            <button type="button" class="ev-qf-dd-trigger">
                                <span class="ev-qf-dd-label" data-dd-label>
                                    @php
                                        $curCode = optional($currencies->firstWhere('id', $currency))->code ?? ($currencies->first()->code ?? '');
                                    @endphp
                                    {{ $curCode }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div class="ev-qf-dd-menu" role="listbox">
                                <div class="ev-qf-dd-search-wrap">
                                    <input type="text" class="ev-qf-dd-search" placeholder="Search currency..." autocomplete="off">
                                </div>
                                <ul class="ev-qf-dd-options">
                                    @foreach($currencies as $cur)
                                        <li data-val="{{ $cur->id }}">{{ $cur->code }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <input type="hidden" id="ev-qf-currency" value="{{ $currency }}">
                        </div>
                        <input type="number" id="ev-qf-min" class="ev-qf-input" placeholder="Min">
                        <input type="number" id="ev-qf-max" class="ev-qf-input" placeholder="Max" value="{{ $rate }}">
                    </div>
                </div>

                {{-- Services --}}
                <div class="ev-qf-group">
                    <label>Services</label>
                    <div class="ev-qf-services">
                        @foreach($services as $service)
                            <label class="ev-qf-service-chip">
                                <input type="checkbox"
                                       class="ev-qf-service-cb"
                                       value="{{ $service->id }}"
                                       @if(is_array($sservices) && in_array($service->id, $sservices)) checked @elseif(is_string($sservices) && in_array((string)$service->id, explode(',', (string)$sservices))) checked @endif>
                                <span>{{ $service->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
        <div class="ev-qf-footer">
            <button type="button" id="ev-qf-apply-btn" class="ev-qf-apply">Apply Filters</button>
            <button type="button" id="ev-qf-reset-btn" class="ev-qf-reset">Reset All</button>
        </div>
    </div>
</div>

@push('js')
{{-- Static listing-page bundle: hover-prefetch, dropdown/modal handler, what's-new
     scroll, country-code map. Cached by Cloudflare independently — keeps inline
     payload small. Loaded with defer so it doesn't block parsing. --}}
<script src="{{ asset('assets/js/listing-page.js') }}?v={{ @filemtime(public_path('assets/js/listing-page.js')) ?: time() }}" defer></script>
{{-- Listing CSS strip/restore script removed — now handled by the layout
     via media-attribute toggling. See components/layouts/app-evoory.blade.php. --}}
<script>
(function(){
    // Wait for DOM ready
    function initQuickFilters(){
        var overlay = document.getElementById('evQuickFilters');
        if(!overlay) return;

        // Custom dropdowns
        document.querySelectorAll('#evQuickFilters [data-qf-dd]').forEach(function(dd){
            var trigger = dd.querySelector('.ev-qf-dd-trigger');
            var menu = dd.querySelector('.ev-qf-dd-menu');
            var hidden = dd.querySelector('input[type="hidden"]');
            var labelSpan = dd.querySelector('[data-dd-label]');
            var searchInput = dd.querySelector('.ev-qf-dd-search');
            var options = dd.querySelectorAll('.ev-qf-dd-options li');

            trigger.addEventListener('click', function(e){
                e.stopPropagation();
                document.querySelectorAll('#evQuickFilters [data-qf-dd].open').forEach(function(o){
                    if(o !== dd) o.classList.remove('open');
                });
                dd.classList.toggle('open');
                if(dd.classList.contains('open') && searchInput){
                    setTimeout(function(){ searchInput.focus(); searchInput.select(); }, 50);
                }
            });
            // Click inside menu should not close it
            if(menu){
                menu.addEventListener('click', function(e){ e.stopPropagation(); });
            }
            // Search filter for searchable dropdowns (currency)
            if(searchInput){
                searchInput.addEventListener('input', function(){
                    var q = this.value.toLowerCase().trim();
                    options.forEach(function(li){
                        var text = li.textContent.toLowerCase();
                        li.style.display = (q === '' || text.indexOf(q) !== -1) ? '' : 'none';
                    });
                });
            }
            options.forEach(function(li){
                li.addEventListener('click', function(e){
                    e.stopPropagation();
                    hidden.value = li.getAttribute('data-val');
                    labelSpan.textContent = li.textContent.trim();
                    dd.classList.remove('open');
                    if(searchInput){ searchInput.value = ''; options.forEach(function(o){ o.style.display = ''; }); }
                });
            });
        });

        // Close dropdowns on outside click
        document.addEventListener('click', function(){
            document.querySelectorAll('#evQuickFilters [data-qf-dd].open').forEach(function(o){ o.classList.remove('open'); });
        });

        // City autocomplete
        var cityInput = document.getElementById('ev-qf-city');
        var cityIdInput = document.getElementById('ev-qf-city-id');
        var cityResults = document.getElementById('ev-qf-city-results');
        var cityDebounce;
        if(cityInput && cityResults){
            function searchCitiesQF(query){
                if(!query || query.length < 2){ cityResults.style.display = 'none'; cityResults.innerHTML = ''; return; }
                fetch('{{ route("cities.search") }}', {
                    method: 'POST',
                    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
                    body: 'query='+encodeURIComponent(query)+'&_token={{ csrf_token() }}'
                }).then(function(r){ return r.json(); }).then(function(data){
                    cityResults.innerHTML = '';
                    if(!data || data.length === 0){
                        cityResults.innerHTML = '<li class="ev-qf-city-empty">No cities found</li>';
                    }else{
                        data.forEach(function(city){
                            var li = document.createElement('li');
                            li.textContent = city.name + (city.country ? ' ('+city.country+')' : '');
                            li.setAttribute('data-id', city.id);
                            li.setAttribute('data-name', city.name);
                            li.addEventListener('click', function(){
                                cityInput.value = city.name;
                                if(cityIdInput) cityIdInput.value = city.id;
                                cityResults.style.display = 'none';
                            });
                            cityResults.appendChild(li);
                        });
                    }
                    cityResults.style.display = 'block';
                }).catch(function(){
                    cityResults.innerHTML = '<li class="ev-qf-city-empty">Error loading cities</li>';
                    cityResults.style.display = 'block';
                });
            }
            cityInput.addEventListener('input', function(){
                clearTimeout(cityDebounce);
                var val = this.value.trim();
                if(cityIdInput) cityIdInput.value = '';
                cityDebounce = setTimeout(function(){ searchCitiesQF(val); }, 250);
            });
            cityInput.addEventListener('focus', function(){
                if(this.value.trim().length >= 2) searchCitiesQF(this.value.trim());
            });
            document.addEventListener('click', function(e){
                if(cityInput && !cityResults.contains(e.target) && e.target !== cityInput){
                    cityResults.style.display = 'none';
                }
            });
        }

        // Apply button — navigate to /{gender}-escorts-in-{citySlug}?filters...
        var applyBtn = document.getElementById('ev-qf-apply-btn');
        if(applyBtn){
            applyBtn.addEventListener('click', function(){
                var genderVal = document.getElementById('ev-qf-gender').value || 'female';
                var cityVal   = (document.getElementById('ev-qf-city').value || '').trim();
                var cityIdVal = (document.getElementById('ev-qf-city-id') || {}).value || '';
                var currencyVal = document.getElementById('ev-qf-currency').value;
                var rateVal   = document.getElementById('ev-qf-max').value;
                var serviceIds = Array.from(document.querySelectorAll('#evQuickFilters .ev-qf-service-cb:checked')).map(function(cb){ return cb.value; });

                // Build city slug from text
                var citySlug = (cityVal || 'dubai')
                    .toLowerCase()
                    .replace(/['.]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                if(!citySlug) citySlug = 'dubai';

                // Build query params for filters
                var params = new URLSearchParams();
                if(cityVal) params.set('selectedcity', cityVal);
                if(cityIdVal) params.set('city', cityIdVal);
                if(currencyVal) params.set('currency', currencyVal);
                if(rateVal) params.set('rate', rateVal);
                serviceIds.forEach(function(id){ params.append('services[]', id); });

                var url = '/' + genderVal + '-escorts-in-' + citySlug;
                var qs = params.toString();
                if(qs) url += '?' + qs;

                overlay.style.display = 'none';
                window.location.href = url;
            });
        }

        // Reset button
        var resetBtn = document.getElementById('ev-qf-reset-btn');
        if(resetBtn){
            resetBtn.addEventListener('click', function(){
                document.getElementById('ev-qf-city').value = '';
                document.getElementById('ev-qf-min').value = '';
                document.getElementById('ev-qf-max').value = '';
                document.querySelectorAll('#evQuickFilters .ev-qf-service-cb').forEach(function(cb){ cb.checked = false; });
            });
        }
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', initQuickFilters);
    }else{
        initQuickFilters();
    }
})();
</script>
@endpush

{{-- Mobile "What's New" Section --}}
@if(isset($reviews) && $reviews->count() > 0)
<div class="ev-mobile-whatsnew visible-xs">
    <div class="ev-whatsnew-header">
        <div class="ev-whatsnew-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
            What's New
        </div>
        <a href="/{{ $gender ?? 'female' }}-escort-news-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}" class="ev-whatsnew-seemore">See more</a>
    </div>
    <div class="ev-whatsnew-scroll">
        @foreach($reviews->take(6) as $rev)
        <a href="/{{ $gender ?? 'female' }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $rev->profile_id }}/{{ $rev->getuser->slug ?? '' }}" class="ev-whatsnew-card">
            <div class="ev-whatsnew-card-img">
                @if($rev->getpic)
                <img src="{{ webp_asset('userimages/'.$rev->getpic->user_id.'/'.$rev->getpic->profile_id.'/'.$rev->getpic->image) }}" alt="{{ $rev->getuser->name ?? '' }}" loading="lazy" />
                @endif
            </div>
            <div class="ev-whatsnew-card-text">
                <strong>Feedback for {{ $rev->getuser->name ?? '' }} <span style="color:#C1F11D">&#10084;</span></strong>
                <small>{{ str()->of($rev->review)->limit(60) }}</small>
            </div>
        </a>
        @endforeach
    </div>
    {{-- Scroll indicators --}}
    <div class="ev-whatsnew-indicators">
        <button class="ev-scroll-arrow ev-scroll-left" aria-label="Scroll left">&#9664;</button>
        <div class="ev-scroll-track"><div class="ev-scroll-thumb"></div></div>
        <button class="ev-scroll-arrow ev-scroll-right" aria-label="Scroll right">&#9654;</button>
    </div>
</div>
@endif

  <div class="container-fluid">
    <div class="content-wrapper no-sidebar">
      <div id="content" class="mt-3">

    <div class="col-md-9 col-xs-12" style="padding:0px">
      <a class="page-title" href="/{{ $gender ?? 'female' }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}">
        <h1 class="ev-city-heading">Escorts in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}@if($currentCity && $currentCity->country), {{ $currentCity->country }}@endif</h1>
      </a>
      
      {{-- Sort rotation indicator --}}
      {{-- <div class="text-muted small mb-2" style="padding: 5px 10px; background: rgba(255,255,255,0.05); border-radius: 4px; display: inline-block;">
        <i class="fa fa-sync-alt"></i> 
        <strong>Sorting:</strong> {{ $sortInfo['sorting_order'] }} 
        <span class="text-white-50">• Next rotation in {{ $sortInfo['next_rotation_minutes'] }} min</span>
      </div> --}}
      
      <p class="page-desc margin-bottom hidden-xs">
        We have {{ number_format($profiles->total()) }} {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }} escorts on Massage Republic, {{ number_format($profiles->where('is_verified', 1)->count()) }} profiles have verified photos. 
        <span class="services">The most popular services offered are: 
          @php
            $popularServices = ['Massage', 'Oral sex - blowjob', 'COB - Come On Body', 'French kissing', 'OWO - Oral without condom', 'GFE', 'Deep throat', 'Foot fetish'];
            $citySlug = $currentCity ? $currentCity->slug : 'dubai';
            $genderName = $gender ?? 'female';
          @endphp
          @foreach($popularServices as $index => $serviceName)
            <a href="/{{ strtolower(str_replace([' ', '-'], ['-', '-'], $serviceName)) }}-{{ $genderName }}-escorts-in-{{ $citySlug }}" 
               title="{{ $serviceName }} Escorts in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}">{{ $serviceName }}</a>@if($index < count($popularServices) - 1), @else. @endif
          @endforeach
        </span>
        @if($profiles->count() > 0)
          @php
            $prices = $profiles->pluck('incallprice')->filter()->values();
            $minPrice = $prices->min();
            $maxPrice = $prices->max();
            $avgPrice = $prices->avg();
          @endphp
          @if($prices->count() > 0)
          Prices range from {{ number_format($minPrice) }} {{ $cityCurrency['code'] }} to {{ number_format($maxPrice) }} {{ $cityCurrency['code'] }}, 
          the average cost advertised is {{ number_format($avgPrice) }} {{ $cityCurrency['code'] }}.
          @endif
        @endif
        @if($nearbyCities->count() > 0)
          We also have listings nearby in 
          @foreach($nearbyCities as $index => $nearbyCity)
            <a title="{{ ucfirst($gender ?? 'Female') }} Escorts in {{ ucfirst($nearbyCity->name) }}" 
               href="/{{ $gender ?? 'female' }}-escorts-in-{{ $nearbyCity->slug }}">{{ ucfirst($nearbyCity->name) }}</a>@if($index < $nearbyCities->count() - 2), @elseif($index == $nearbyCities->count() - 2), and @else. @endif
          @endforeach
        @endif
      </p>
      <div class="listings listings-spots listing-spots--minimal border-top padding-top mx-n2 mx-sm-0">
        
     
       @if($auctions->count() > 0)
<div class="listings listings-spots listing-spots--minimal border-bottom mx-n2 mx-sm-0">
  @foreach($auctions as $auction)
  <div class="spot">
    {{-- Only show the auction bidding overlay for active auctions AND logged in users --}}
    @if($auction->status == 'active' && Auth::check())
    <div class="auction-cover d-flex align-items-center flex-wrap" style="z-index:1;">
      <div class="d-flex flex-column align-items-center align-self-start justify-content-between flex-sidebar">
        <div class="spot-id pt-3 px-3 pb-2 text-uppercase small">Spot&nbsp;#{{ $auction->spot_number }}</div>
        <details class="ml-3 w-100 d-none d-sm-block" data-popover="up">
          <summary class="d-flex align-items-center pb-2">
            <img alt="Question mark icon" class="mr-2" height="18" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/query-9f855724e9abf46e4a04ed35fbe5d2b97780f89950dd709df860db1f6b3c04e3.svg" width="18">
            <span class="text-uppercase">Why is this the best choice?</span>
          </summary>
          <div class="popover-content p-2 mb-0">Spots stay always at the top! These profiles are first to be seen.</div>
        </details>
      </div>
      <div class="position-relative flex-not-sidebar">
        <div class="d-flex flex-column align-items-center">
          <h3 class="mb-4">
            <span>Current price for {{ $auction->duration_days ?? 7 }} days:</span>
            <strong class="ml-2">${{ number_format($auction->current_price, 0) }}</strong>
          </h3>
          <a class="btn btn-primary px-5 mb-3 font-weight-bold" href="/auctions/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}" style="font-size:1rem">Make offer</a>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <img alt="Timer icon" class="mr-2" height="28" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/timer-5fc5fc1474905d451c5cb2d9ad472d17fea1e9059c0baf436d0aaf6df2b2aeed.svg" width="28">
          <p class="mb-0">auction ends in: {{ $auction->daysLeft }} {{ Str::plural('day', $auction->daysLeft) }}</p>
        </div>
      </div>
    </div>
    @endif
            
            <div class="listing-li listing-li--spot premium thumbs-3 thumbs-mini p-3">
              <h2 class="visible-xxs">
                @if($auction->status == 'ended' && $auction->winnerProfile)
                  {{-- For ended auctions with winners, show the winner profile --}}
                  <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                    {{ $auction->winnerProfile->name }}
                    @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews_count > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews_count }} approved reviews">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        <span>{{ $auction->winnerProfile->reviews_count }}</span>
                      </span>
                    @endif
                    @if($auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                        <i class="fa fa-question-circle"></i>
                        <span>{{ $auction->winnerProfile->questions->count() }}</span>
                      </span>
                    @endif
                  </a>
                @elseif($auction->status == 'active')
                  {{-- For active auctions, show the current profile or "Available Spot" --}}
                  <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                    {{ $auction->winnerProfile->name ?? 'Available Spot' }}
                    @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews_count > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews_count }} approved reviews">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        <span>{{ $auction->winnerProfile->reviews_count }}</span>
                      </span>
                    @endif
                    @if(isset($auction->winnerProfile) && $auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                        <i class="fa fa-question-circle"></i>
                        <span>{{ $auction->winnerProfile->questions->count() }}</span>
                      </span>
                    @endif
                  </a>
                @endif
              </h2>
              
              <div class="thumbs">
                <div class="main-thumbs">
                  @if($auction->status == 'ended' && $auction->winnerProfile)
                    {{-- For ended auctions with winners, link to the winner profile --}}
                    <a class="img pb-photo-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                      <span class="img-wrapper premium">
                        @if($auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                          <span class="verified-image text-left small" title="Photos Verified">
                            <i class="fa fa-check"></i>
                            <span>Verified photos</span>
                          </span>
                        @endif
                        <div class="image-wrapper">
                          @if(!empty($auction->winnerProfile->coverimg))
                            @php $auctionImgPath = 'userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->coverimg->image; @endphp
                            <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ webp_asset($auctionImgPath) }}"
                                 >
                          @elseif(!empty($auction->winnerProfile->singleimg))
                            @php $auctionImgPath = 'userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->singleimg->image; @endphp
                            <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ webp_asset($auctionImgPath) }}"
                                 >
                          @endif
                        </div>
                      </span>
                    </a>
                  @else
                    {{-- For active auctions, show current profile or placeholder --}}
                    <a class="img pb-photo-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                      <span class="img-wrapper premium">
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                          <span class="verified-image text-left small" title="Photos Verified">
                            <i class="fa fa-check"></i>
                            <span>Verified photos</span>
                          </span>
                        @endif
                        <div class="image-wrapper">
                          @if(isset($auction->winnerProfile) && !empty($auction->winnerProfile->coverimg))
                            @php $activeAuctionImgPath = 'userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->coverimg->image; @endphp
                            <img alt="{{ $auction->winnerProfile->name ?? 'Escort' }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ webp_asset($activeAuctionImgPath) }}"
                                 >
                          @elseif(isset($auction->winnerProfile) && !empty($auction->winnerProfile->singleimg))
                            @php $activeAuctionImgPath = 'userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->singleimg->image; @endphp
                            <img alt="{{ $auction->winnerProfile->name ?? 'Escort' }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ webp_asset($activeAuctionImgPath) }}"
                                 >
                          @else
                            <img alt="Available Spot" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="https://via.placeholder.com/200x208">
                          @endif
                        </div>
                      </span>
                    </a>
                  @endif
                </div>
                
                <div class="other-thumbs pull-left">
                  @if($auction->status == 'ended' && $auction->winnerProfile && $auction->winnerProfile->multipleimgs)
                    {{-- For ended auctions with winners, show the winner's images --}}
                    @foreach($auction->winnerProfile->multipleimgs->take(3) as $key => $img)
                      <div class="thumb thumb-{{ $key }}">
                        <a class="img img-responsive pb-photo-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                          <span class="img-wrapper mini">
                            @if($auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                              <span class="verified-image text-left small" title="Photos Verified">
                                <i class="fa fa-check"></i>
                                <span>Verified photos</span>
                              </span>
                            @endif
                            <div class="image-wrapper">
                              @php $auctionThumbPath = 'userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image; @endphp
                              <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }} Photo {{ $key+1 }}" class="img-responsive" height="60" 
                                src="{{ webp_asset($auctionThumbPath) }}" width="60">
                            </div>
                          </span>
                        </a>
                      </div>
                    @endforeach
                  @elseif(isset($auction->winnerProfile) && $auction->winnerProfile->multipleimgs)
                    {{-- For active auctions with a profile, show the profile's images --}}
                    @foreach($auction->winnerProfile->multipleimgs->take(3) as $key => $img)
                      <div class="thumb thumb-{{ $key }}">
                        <a class="img img-responsive pb-photo-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                          <span class="img-wrapper mini">
                            @if($auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                              <span class="verified-image text-left small" title="Photos Verified">
                                <i class="fa fa-check"></i>
                                <span>Verified photos</span>
                              </span>
                            @endif
                            <div class="image-wrapper">
                              @php $auctionThumbPath = 'userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image; @endphp
                              <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }} Photo {{ $key+1 }}" class="img-responsive" height="60" 
                                src="{{ webp_asset($auctionThumbPath) }}" width="60">
                            </div>
                          </span>
                        </a>
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
      
              <div class="listing-info-wrapper">
                <div class="listing-info">
                  <h2>
                    @if($auction->status == 'ended' && $auction->winnerProfile)
                      {{-- For ended auctions with winners, link to the winner profile --}}
                      <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                        {{ $auction->winnerProfile->name }}
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews_count > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews_count }} approved reviews">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            <span>{{ $auction->winnerProfile->reviews_count }}</span>
                          </span>
                        @endif
                        @if($auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                            <i class="fa fa-question-circle"></i>
                            <span>{{ $auction->winnerProfile->questions->count() }}</span>
                          </span>
                        @endif
                      </a>
                    @else
                      {{-- For active auctions, show current profile or "Available Spot" --}}
                      <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                        {{ $auction->winnerProfile->name ?? 'Available Spot' }}
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews_count > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews_count }} approved reviews">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            <span>{{ $auction->winnerProfile->reviews_count }}</span>
                          </span>
                        @endif
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                        <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                          <i class="fa fa-question-circle"></i>
                          <span>{{ $auction->winnerProfile->questions->count() }}</span>
                        </span>
                      @endif
                    </a>
                  @endif
                </h2>
                
                @if($auction->status == 'ended' && $auction->winnerProfile)
                  {{-- For ended auctions with winners, show the winner's profile content --}}
                  <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                    <p>{{ str()->of($auction->winnerProfile->about)->stripTags()->squish()->limit(290) }}</p>
                  </a>
                  <p class="no-margin see-more">
                    <a class="btn btn-dark" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                      See more & contact
                    </a>
                  </p>
                @else
                  {{-- For active auctions, show auction information --}}
                  <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                    <p>This spot is available for auction. Place your bid to feature your profile here and get maximum visibility!</p>
                  </a>
                  <p class="no-margin see-more">
                    <a class="btn btn-dark" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                      {{ Auth::check() ? 'Make a bid' : 'Sign in to bid' }}
                    </a>
                  </p>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @endif
        
        {{-- <div class="spot">
          <div class="listing-li listing-li--spot premium thumbs-3 thumbs-mini p-3">
            <h2 class="visible-xxs">
              <a class="nostyle-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198" title="Anastasia, Belarusian escort agency in Dubai (14)">Anastasia</a>
            </h2>
            <div class="thumbs">
              <div class="main-thumbs">
                <a class="img pb-photo-link" href="">
                  <span class="img-wrapper premium">
                    <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                      <i class="fa fa-check"></i>
                      <span>Verified photos</span>
                    </span>
                    <div class="image-wrapper">
                      <img alt="Anastasia - escort agency in Dubai Photo 10 of 10" class="img-responsive" height="208" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728120_premium.jpg" width="200" />
                    </div>
                  </span>
                </a>
              </div>
              <div class="other-thumbs pull-left">
                <div class="thumb thumb-0">
                  <a class="img img-responsive pb-photo-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="Anastasia - escort agency in Dubai Photo 1 of 10" class="img-responsive" height="60" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728102_mini.jpg" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
                <div class="thumb thumb-1">
                  <a class="img img-responsive pb-photo-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="Anastasia - escort agency in Dubai Photo 2 of 10" class="img-responsive" height="60" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728104_mini.jpg" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
                <div class="thumb thumb-2">
                  <a class="img img-responsive pb-photo-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="Anastasia - escort agency in Dubai Photo 3 of 10" class="img-responsive" height="60" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728106_mini.jpg" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
              </div>
            </div>
            <div class="listing-info-wrapper">
              <div class="listing-info">
                <h2>
                  <a class="nostyle-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198" title="Anastasia, Belarusian escort agency in Dubai (14)">Anastasia</a>
                </h2>
                <a class="nostyle-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                  <p>Juicy and colorful Anastasia is already in Dubai! Her forms will definitely give you a sensation if you know what😉She knows the true desires of men and fulfills them with all the love you want. Also with her it is pleasant to communicate and share all your feelings, she will listen and will not judge, Come to her for peace of mind and thrill in bed🔥🔥🔥</p>
                </a>
                <p class="no-margin see-more">
                  <a class="btn btn-dark" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">See more &amp; contact</a>
                </p>
              </div>
            </div>
          </div>
        </div> --}}
      </div>
      <div class="listings  @if($auctions->count() > 0 and Auth::check()) padding-top @endif">
        @forelse($profiles as $profile)
        @php
            $packageName = $profile->package ? strtolower($profile->package->name) : '';
            $isVip = str_contains($packageName, 'vip') || str_contains($packageName, 'premium');
            $isFeatured = str_contains($packageName, 'featured');
            $isBasic = str_contains($packageName, 'basic');
            // First 6 profiles should load immediately (above the fold)
            $isAboveFold = $loop->index < 6;
            $loadingAttr = $isAboveFold ? 'eager' : 'lazy';
            $fetchPriority = $isAboveFold ? 'high' : 'auto';
            $isFree = !$profile->package_id || str_contains($packageName, 'free');
        @endphp
        @if($isVip)
        <div class="listing-li premium thumbs-3 thumbs-mini">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="/female-escorts-in-dubai/lea-ukrainian" title="Lea, Ukrainian escort in Dubai (3)">{{$profile->name}} <span class="badge" data-placement="top" data-toggle="tooltip" title="One review. Rating: ❤❤❤❤❤">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span>1</span>
              </span>
            </a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <span class="img-wrapper premium">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
                    @if(!empty($profile->coverimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                         class="img-responsive" 
                         height="208" 
                         width="200"
                         loading="{{ $loadingAttr }}"
                         fetchpriority="{{ $fetchPriority }}"
                         decoding="async"
                         src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}"
                          />
                    @elseif(!empty($profile->singleimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                         class="img-responsive" 
                         height="208" 
                         width="200"
                         loading="{{ $loadingAttr }}"
                         fetchpriority="{{ $fetchPriority }}"
                         decoding="async"
                         src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}"
                          />
                    @else
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                         class="img-responsive" 
                         height="208" 
                         width="200"
                         loading="{{ $loadingAttr }}"
                         src="{{smart_asset('admin/assets/img/flat-icons/user.png')}}" style="opacity:0.5; padding:20px;" />
                    @endif
                  </div>
                </span>
              </a>
            </div>

            
            <div class="other-thumbs pull-left">
            
              @forelse($profile->multipleimgs->take(3) as $imgs)
              <div class="thumb thumb-{{ $loop->index }}">
                <a class="img img-responsive pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                  <span class="img-wrapper mini">
                 
                    @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                    <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                      <i class="fa fa-check"></i>
                      <span>Verified photos</span>
                    </span>
                    @endif
                    <div class="image-wrapper">
                      <img alt="{{ $profile->name }} - Photo {{ $loop->iteration }}" 
                           class="img-responsive" 
                           height="60" 
                           width="60"
                           loading="lazy"
                           src="{{webp_asset('userimages/'.$imgs->user_id.'/'.$imgs->profile_id.'/'.$imgs->image)}}"
                            />
                    </div>
                  </span>
                </a>
              </div>
            
              @empty
                 
              @endforelse
              
              
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}" title="Lea, Ukrainian escort in Dubai (3)">{{$profile->name}} 
                  @if($profile->reviews_count > 0)
                  <span class="badge" data-placement="top" data-toggle="tooltip" title="{{$profile->reviews_count}} Reviews">
                  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span>{{ $profile->reviews_count }}</span>
                  </span>
                  @endif

                  {{-- <a href="#" wire:click.prevent="toggleFavorite({{ $profile->id }})" class="btn btn-sm {{ $this->checkIfFavorited($profile->id) ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fa {{ $this->checkIfFavorited($profile->id) ? 'fa-heart' : 'fa-heart-o' }}"></i>
                </a> --}}

                </a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <p>{{str()->of($profile->about)->stripTags()->squish()->limit(400)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>

        @elseif($isFeatured)
        <div class="listing-li pb-3 featured thumbs-2 thumbs-mini">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}</a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <span class="img-wrapper featured">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
               @if(!empty($profile->coverimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="135" width="115" loading="{{ $loadingAttr }}" fetchpriority="{{ $fetchPriority }}" decoding="async" src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" >
               @elseif(!empty($profile->singleimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="135" width="115" loading="{{ $loadingAttr }}" fetchpriority="{{ $fetchPriority }}" decoding="async" src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" >
               @else
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="135" src="{{smart_asset('admin/assets/img/flat-icons/user.png')}}" width="115" style="opacity:0.5; padding:15px;">
               @endif
                  </div>
                </span>
              </a>
            </div>
            <div class="other-thumbs pull-left">
          @forelse($profile->multipleimgs->take(2) as $k => $imgs)
              <div class="thumb thumb-{{ $k }}">
                <a class="img img-responsive pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                  <span class="img-wrapper mini">
                    @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                    <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                      <i class="fa fa-check"></i>
                      <span>Verified photos</span>
                    </span>
                    @endif
                    <div class="image-wrapper">
                      <img alt="{{ $profile->name }} - Photo {{ $k + 1 }}" 
                           class="img-responsive" 
                           height="60" 
                           width="60"
                           loading="lazy"
                           src="{{webp_asset('userimages/'.$imgs->user_id.'/'.$imgs->profile_id.'/'.$imgs->image)}}"
                            />
                    </div>
                  </span>
                </a>
              </div>
            @empty
            @endforelse
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}} </a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <p>{{str()->of($profile->about)->stripTags()->squish()->limit(150)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>

        @elseif($isBasic)
        <div class="listing-li pb-3 basic thumbs-0 thumbs-basic" style="padding-left:0px">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}  <span class="badge" data-placement="top" data-toggle="tooltip" title="" data-original-title="Curve Amber has answered 3 questions">
                <i class="fa fa-question-circle"></i>
                <span>3</span>
              </span>
            </a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <span class="img-wrapper basic">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
               @if(!empty($profile->coverimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="95" width="89" loading="{{ $loadingAttr }}" fetchpriority="{{ $fetchPriority }}" decoding="async" src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" >
               @elseif(!empty($profile->singleimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="95" width="89" loading="{{ $loadingAttr }}" fetchpriority="{{ $fetchPriority }}" decoding="async" src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" >
               @else
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="95" src="{{smart_asset('admin/assets/img/flat-icons/user.png')}}" width="89" style="opacity:0.5; padding:10px;">
               @endif
                  </div>
                </span>
              </a>
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}  <span class="badge" data-placement="top" data-toggle="tooltip" title="" data-original-title="Curve Amber has answered 3 questions">
                    <i class="fa fa-question-circle"></i>
                    <span>3</span>
                  </span>
                </a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <p>{{str()->of($profile->about)->stripTags()->squish()->limit(70)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>

        @elseif($isFree)
        {{-- Free profiles - same layout as basic --}}
        <div class="listing-li pb-3 basic thumbs-0 thumbs-basic" style="padding-left:0px">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}</a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <span class="img-wrapper basic">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
               @if(!empty($profile->coverimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="95" width="89" loading="{{ $loadingAttr }}" fetchpriority="{{ $fetchPriority }}" decoding="async" src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" >
               @elseif(!empty($profile->singleimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="95" width="89" loading="{{ $loadingAttr }}" fetchpriority="{{ $fetchPriority }}" decoding="async" src="{{webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" >
               @else
                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}" class="img-responsive" height="95" src="{{smart_asset('admin/assets/img/flat-icons/user.png')}}" width="89" style="opacity:0.5; padding:10px;">
               @endif
                  </div>
                </span>
              </a>
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}</a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <p>{{str()->of($profile->about)->stripTags()->squish()->limit(70)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>
        @endif

        @empty
        @php
            $emptyCityLabel = ucwords(str_replace('-', ' ', $selectedcity ?: 'this location'));
        @endphp
        <div class="col-md-12 mb-2 ev-empty-listings-wrap">
            <div class="ev-empty-listings-card">
                <h2 class="ev-empty-listings-title">
                    <span class="ev-empty-listings-ban" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    </span>
                    No Listings Available in {{ $emptyCityLabel }} Yet
                </h2>
                <p class="ev-empty-listings-sub">There are currently no active listings in this location. New profiles are added regularly.</p>
                <p class="ev-empty-listings-cta">Subscribe now and be the first to know when new profiles become available in {{ $emptyCityLabel }}.</p>
                <div class="subscribe-btn-wrapper" x-data="{ show: @entangle('showSubscribeModal') }">
                    @auth
                        <a class="ev-empty-subscribe-btn" href="#" @click.prevent="show = true; $wire.prefillSubscribeCity()">Subscribe</a>
                    @else
                        <a class="ev-empty-subscribe-btn" href="/register">Subscribe</a>
                    @endauth

                    @auth
                    {{-- Newsletter subscribe modal (mirrors the My Account page modal) --}}
                    <div x-show="show" x-cloak class="ev-modal-overlay" @click.self="show = false" @keydown.escape.window="show = false" style="display:none;">
                        <div class="ev-modal">
                            <div class="ev-modal-header">
                                <h2>
                                    <i class="fa fa-newspaper"></i>
                                    <span>Subscribe</span>
                                </h2>
                                <button type="button" class="ev-modal-close" @click="show = false">&times;</button>
                            </div>
                            <div class="ev-modal-body">
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="display: flex; align-items: center; cursor: pointer; font-size: 1rem;">
                                        <input type="checkbox" wire:model.live="subReceiveNewsletter" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                                        <span style="margin-left:0.75rem;font-weight:500;">Send me newsletter for:</span>
                                    </label>
                                </div>

                                @if($subReceiveNewsletter)
                                <div style="margin-bottom: 1rem; position: relative;">
                                    <div class="ev-search-input">
                                        <span><i class="fa fa-map-marker-alt"></i></span>
                                        <input type="text" placeholder="Find city..." wire:model.live="subCitySearch" autocomplete="off">
                                        @if($subCitySearch)
                                            <button type="button" wire:click="$set('subCitySearch', '')"><i class="fa fa-times"></i></button>
                                        @endif
                                    </div>
                                    @if(count($subSearchResults) > 0)
                                        <div class="ev-dropdown-results">
                                            @foreach($subSearchResults as $resultCity)
                                                <button type="button" wire:click="subAddCity({{ $resultCity['id'] }})">{{ $resultCity['name'] }}@if(!empty($resultCity['country'])) <span style="color:#666;">({{ $resultCity['country'] }})</span>@endif</button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if(count($subSelectedCities) > 0)
                                    <div style="margin-bottom: 1rem; max-height: 150px; overflow-y: auto;">
                                        @foreach($subSelectedCities as $index => $subCity)
                                            <div class="ev-city-tag">
                                                <span><i class="fa fa-map-marker-alt" style="margin-right:0.5rem;"></i>{{ $subCity['name'] }}@if(!empty($subCity['country'])) <span style="color:#666;">({{ $subCity['country'] }})</span>@endif</span>
                                                <button type="button" style="background:none;border:none;cursor:pointer;font-size:1.2rem;padding:0;" wire:click="subRemoveCity({{ $index }})"><i class="fa fa-times"></i></button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div>
                                    <label style="display:block;margin-bottom:1rem;font-weight:600;font-size:1rem;">Include</label>
                                    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;">
                                        <label style="display:flex;align-items:center;cursor:pointer;">
                                            <input type="checkbox" value="female" wire:model="subSelectedGenders" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                                            <span style="margin-left:0.5rem;">Escorts</span>
                                        </label>
                                        <label style="display:flex;align-items:center;cursor:pointer;">
                                            <input type="checkbox" value="male" wire:model="subSelectedGenders" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                                            <span style="margin-left:0.5rem;">Male Escorts</span>
                                        </label>
                                        <label style="display:flex;align-items:center;cursor:pointer;">
                                            <input type="checkbox" value="shemale" wire:model="subSelectedGenders" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                                            <span style="margin-left:0.5rem;">Shemale Escorts</span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="ev-modal-footer">
                                <button type="button" class="ev-buy-btn" wire:click="subSaveNewsletter" @click="show = false">
                                    <span>Save</span> <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>

            @if(!empty($fallback) && $fallback['profiles']->count() > 0)
            <div class="ev-fallback-section">
                <h3 class="ev-fallback-title">
                    @if($fallback['type'] === 'nearby')
                        Profiles Available Nearby <span class="ev-fallback-accent">{{ $fallback['cityName'] }}</span>
                    @else
                        What&#39;s Happening Across <span class="ev-fallback-accent">{{ $fallback['cityName'] }}</span> Right Now
                    @endif
                </h3>
                <p class="ev-fallback-sub">
                    @if($fallback['type'] === 'nearby')
                        Explore verified profiles from nearby locations.
                    @else
                        Explore the newest Escorts Models and trending profiles currently active on Evoory.
                    @endif
                </p>

                @foreach($fallback['profiles'] as $profile)
                @php
                    $fallbackCityModel = $profile->getcity;
                    $fallbackCitySlug = $fallbackCityModel
                        ? ($fallbackCityModel->slug ?: strtolower(str_replace([' ', "'", '.'], ['-', '', ''], $fallbackCityModel->name)))
                        : ($cityname ?? 'dubai');
                    $fallbackCitySlug = preg_replace('/[^a-z0-9\-]/', '', $fallbackCitySlug);
                    $fallbackUrl = url($gender.'-escorts-in-'.$fallbackCitySlug.'/'.$profile->id.'/'.$profile->slug);
                    if (!empty($profile->coverimg->image)) {
                        $fallbackMainImg = webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image);
                    } elseif (!empty($profile->singleimg->image)) {
                        $fallbackMainImg = webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image);
                    } else {
                        $fallbackMainImg = smart_asset('admin/assets/img/flat-icons/user.png');
                    }
                @endphp
                <div class="ev-fallback-card{{ $profile->multipleimgs->isEmpty() ? ' ev-fallback-card--no-thumbs' : '' }}">
                    {{-- Mobile-only title (appears above the image row on small screens) --}}
                    <h2 class="ev-fallback-name ev-fallback-name--mobile">
                        <a class="nostyle-link" href="{{ $fallbackUrl }}">{{ $profile->name }}</a>
                    </h2>
                    <a class="ev-fallback-main" href="{{ $fallbackUrl }}" style="flex:0 0 200px;width:200px;max-width:200px;height:210px;display:block;border-radius:6px;overflow:hidden;background:#111;">
                        <img alt="{{ $profile->name }} - escort in {{ optional($fallbackCityModel)->name ?? '' }}" width="200" height="210" loading="lazy" decoding="async" src="{{ $fallbackMainImg }}" style="width:200px;height:210px;max-width:200px;object-fit:cover;display:block;">
                    </a>
                    @if($profile->multipleimgs->isNotEmpty())
                    <div class="ev-fallback-thumbs" style="flex:0 0 65px;width:65px;display:flex;flex-direction:column;gap:5px;">
                        @foreach($profile->multipleimgs->take(3) as $k => $imgs)
                            <a class="ev-fallback-thumb" href="{{ $fallbackUrl }}" style="display:block;width:65px;height:65px;border-radius:4px;overflow:hidden;background:#111;">
                                <img alt="{{ $profile->name }} - Photo {{ $k + 1 }}" width="65" height="65" loading="lazy" src="{{ webp_asset('userimages/'.$imgs->user_id.'/'.$imgs->profile_id.'/'.$imgs->image) }}" style="width:65px;height:65px;max-width:65px;object-fit:cover;display:block;">
                            </a>
                        @endforeach
                    </div>
                    @endif
                    <div class="ev-fallback-info">
                        <h2 class="ev-fallback-name ev-fallback-name--desktop">
                            <a class="nostyle-link" href="{{ $fallbackUrl }}">{{ $profile->name }}</a>
                        </h2>
                        <a class="nostyle-link" href="{{ $fallbackUrl }}">
                            <p class="ev-fallback-desc">{{ str()->of($profile->about)->stripTags()->squish()->limit(280) }}</p>
                        </a>
                        <a class="ev-fallback-see-more" href="{{ $fallbackUrl }}">See more contact</a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- VIP Positioning promo: encourage first-mover profile creation in empty cities --}}
            <div class="ev-vip-promo">
                <div class="ev-vip-promo-head">
                    <span class="ev-vip-badge">VIP Positioning</span>
                    <span class="ev-vip-crown" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7l4 10h12l4-10-6 4-4-7-4 7-6-4z"></path></svg>
                    </span>
                </div>
                <h3 class="ev-vip-title">Be the First in {{ $emptyCityLabel }}</h3>
                <p class="ev-vip-sub">Start receiving visibility before everyone else by creating the first listing in this area.</p>
                <ul class="ev-vip-perks">
                    <li>
                        <span class="ev-vip-perk-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#FFB627" stroke="none"><path d="M12 2l2.39 7.36H22l-6.18 4.49L18.21 22 12 17.27 5.79 22l2.39-8.15L2 9.36h7.61z"></path></svg>
                        </span>
                        Maximum regional search exposure with top-tier ranking
                    </li>
                    <li>
                        <span class="ev-vip-perk-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFB627" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </span>
                        Comprehensive profile verification priority and verification badge
                    </li>
                </ul>
                <a class="ev-vip-cta" href="{{ auth()->check() ? route('new.profile') : '/register' }}">Add your Profile</a>
            </div>
        </div>
        @endforelse
        
        
    
      </div>

      {{ $profiles->links('vendor.livewire.custom') }}
      
<!-- SEO Content Section -->
@if(!empty($seoContent))
<div class="container-fluid mt-2 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="seo-content">
                <div class="content-wrapper">
                    {!! $seoContent !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
    </div>
    <div class="col-md-3 hidden-sm hidden-xs">
      <div class="stream-sidebar">
        <div class="subscribe-btn-wrapper subscribe-btn-wrapper--small-right">
          <a class="btn btn-primary btn-lg" data-btn-link="" href="/register">
            Subscribe </a>
        </div>
        <h3>
          <a href="/female-escort-news-in-dubai">What&#39;s new?</a>
          <a href="/female-escort-news-in-dubai" style="font-size:12px;color:#C1F11D !important">See more</a>
        </h3>
        @if($reviews->count() > 0)
        <ul class="activity-stream activity-records-mini">

      @foreach($reviews as $rev)
          <li>
            <div class="activity-record new-review mini">
              <div class="activity-row">
                <div class="headline h3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>New review for <a title="{{ $rev->getuser->name ?? '' }}" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity ?? 'dubai') }}/{{ $rev->profile_id }}/{{ $rev->getuser->slug ?? '' }}">{{ $rev->getuser->name ?? '' }}</a>
                </div>
                @if($rev->getpic)
                <div class="photo">
                  <a class="pb-photo-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity ?? 'dubai') }}/{{ $rev->profile_id }}/{{ $rev->getuser->slug ?? '' }}">
                    <span class="img-wrapper mini">
                      <div class="image-wrapper">
                        <img alt="{{ $rev->getuser->name ?? '' }}" class="img-responsive" height="60" width="60" loading="lazy" src="{{ webp_asset('userimages/'.$rev->getpic->user_id.'/'.$rev->getpic->profile_id.'/'.$rev->getpic->image) }}" />
                      </div>
                    </span>
                  </a>
                </div>
                @endif
                <div class="activity-content">
                  <div class="review-description">
                    <p>{{ Str::limit($rev->review, 150) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </li>
          @endforeach
        </ul>
        @endif
        
      </div>
    </div>
  </div>

</div>
</div>
</div>


  @push('js')
  {{-- jQuery is loaded once in the layout (app-evoory.blade.php). Do NOT re-load it here:
       a second copy was previously imported from cdnjs which downloaded ~90 KB twice and
       could overwrite plugins registered against the first instance. chosen/select2 are
       deferred so they still execute after the layout's jQuery is parsed. --}}
  <script src="{{ smart_asset('chosen/chosen.jquery.js')}}" defer></script>
  <script src="{{ smart_asset('chosen/docsupport/init.js')}}" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
  
  {{-- Gender dropdown + advanced search modal handlers moved to /assets/js/listing-page.js --}}
  
  <!-- Remove prism.js as it's not needed for functionality -->
  
  <script>
console.log('🔧 Services dropdown - waiting for Livewire...');

// Multiple initialization strategies
function initServicesDropdown() {
    console.log('🚀 Attempting services initialization...');
    
    const displayBox = document.getElementById('services-display-box');
    const dropdown = document.getElementById('services-dropdown-list');
    const displayText = document.getElementById('services-display-text');
    
    console.log('Elements check:', {
        displayBox: !!displayBox,
        dropdown: !!dropdown,
        displayText: !!displayText,
        servicesCount: dropdown ? dropdown.querySelectorAll('.service-option').length : 0
    });
    
    if (!displayBox || !dropdown || !displayText) {
        console.log('❌ Elements not ready, retrying...');
        return false;
    }
    
    console.log('✅ All elements found - setting up dropdown');
    
    // Visual confirmation with theme colors
    displayBox.style.borderColor = 'rgb(68 68 68)';
    setTimeout(() => {
        displayBox.style.borderColor = 'rgb(68 68 68)';
    }, 1000);
    
    let isOpen = false;
    
    // Remove any existing listeners (avoid duplicates)
    const newDisplayBox = displayBox.cloneNode(true);
    displayBox.parentNode.replaceChild(newDisplayBox, displayBox);
    
    // Click to toggle dropdown
    newDisplayBox.addEventListener('click', function(e) {
        console.log('🎯 Display box clicked!');
        e.preventDefault();
        e.stopPropagation();
        
        const currentDropdown = document.getElementById('services-dropdown-list');
        if (!currentDropdown) return;
        
        if (isOpen) {
            currentDropdown.style.display = 'none';
            isOpen = false;
            console.log('✅ Dropdown closed');
        } else {
            currentDropdown.style.display = 'block';
            isOpen = true;
            console.log('✅ Dropdown opened');
        }
    });
    
    // Handle service selection
    const serviceOptions = dropdown.querySelectorAll('.service-option');
    console.log('📋 Found service options:', serviceOptions.length);
    
    serviceOptions.forEach(function(option, index) {
        const checkbox = option.querySelector('input[type="checkbox"]');
        const serviceName = option.getAttribute('data-name');
        const serviceId = option.getAttribute('data-id');
        
        console.log(`Setting up service ${index + 1}: ${serviceName}`);
        
        if (checkbox) {
            // Remove existing listeners
            const newOption = option.cloneNode(true);
            option.parentNode.replaceChild(newOption, option);
            
            const newCheckbox = newOption.querySelector('input[type="checkbox"]');
            
            newOption.addEventListener('click', function(e) {
                console.log('🎯 Service clicked:', serviceName);
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle checkbox
                newCheckbox.checked = !newCheckbox.checked;
                
                if (newCheckbox.checked) {
                    newOption.classList.add('is-selected');
                    // Add checkmark indicator
                    if (!newOption.querySelector('.checkmark')) {
                        const checkmark = document.createElement('span');
                        checkmark.className = 'checkmark';
                        checkmark.innerHTML = ' ✓';
                        checkmark.style.fontWeight = 'bold';
                        checkmark.style.marginLeft = 'auto';
                        newOption.appendChild(checkmark);
                    }
                    console.log('✅ Selected:', serviceName);
                } else {
                    newOption.classList.remove('is-selected');
                    // Remove checkmark indicator
                    const checkmark = newOption.querySelector('.checkmark');
                    if (checkmark) {
                        checkmark.remove();
                    }
                    console.log('❌ Unselected:', serviceName);
                }
                
                updateDisplay();
            });
        }
    });
    
    function updateDisplay() {
        const currentDropdown = document.getElementById('services-dropdown-list');
        const currentDisplayText = document.getElementById('services-display-text');
        if (!currentDropdown || !currentDisplayText) return;
        
        const checkedBoxes = currentDropdown.querySelectorAll('input[type="checkbox"]:checked');
        const selectedNames = [];
        const selectedIds = [];
        
        checkedBoxes.forEach(function(cb) {
            const option = cb.closest('.service-option');
            if (option) {
                selectedNames.push(option.getAttribute('data-name'));
                selectedIds.push(option.getAttribute('data-id'));
            }
        });
        
        if (selectedNames.length > 0) {
            // Limit display to prevent overflow
            if (selectedNames.length <= 3) {
                currentDisplayText.textContent = selectedNames.join(', ');
            } else {
                currentDisplayText.textContent = selectedNames.slice(0, 2).join(', ') + ` +${selectedNames.length - 2} more`;
            }
            currentDisplayText.style.color = 'white';
        } else {
            currentDisplayText.textContent = 'All Services';
            currentDisplayText.style.color = '#999';
        }
        
        console.log('📝 Updated display:', selectedNames);
        
        // Update Livewire
        const hiddenInput = document.getElementById('services-hidden-input');
        if (hiddenInput && window.Livewire) {
            try {
                hiddenInput.value = selectedIds.join(',');
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('📤 Updated Livewire:', selectedIds);
            } catch(e) {
                console.error('❌ Livewire update failed:', e);
            }
        }
    }
    
    // Close when clicking outside
    document.addEventListener('click', function(e) {
        const container = document.querySelector('.services-dropdown-container');
        if (container && !container.contains(e.target) && isOpen) {
            const currentDropdown = document.getElementById('services-dropdown-list');
            if (currentDropdown) {
                currentDropdown.style.display = 'none';
                isOpen = false;
                console.log('✅ Dropdown closed by outside click');
            }
        }
    });
    
    // Initialize display
    updateDisplay();
    
    console.log('🎉 Services dropdown ready!');
    return true;
}

// Try multiple initialization methods
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded');
    setTimeout(() => initServicesDropdown(), 1000);
});

window.addEventListener('load', function() {
    console.log('🌐 Window loaded');
    setTimeout(() => initServicesDropdown(), 2000);
});

// Livewire ready
document.addEventListener('livewire:load', function() {
    console.log('⚡ Livewire loaded');
    setTimeout(() => initServicesDropdown(), 500);
});

// Fallback attempts
setTimeout(() => {
    if (!initServicesDropdown()) {
        console.log('🔄 Fallback attempt 1');
        setTimeout(() => initServicesDropdown(), 2000);
    }
}, 3000);

// Re-bind after every Livewire morph. wire:model.live="rate" triggers a
// morph on every keystroke. Even though the services dropdown is wrapped
// in wire:ignore, morphdom can still detach/reattach the subtree in some
// edge cases, leaving the display-box click handler dead. initServicesDropdown
// always clones+replaces, so calling it again is safe — it just rebinds
// onto whatever DOM is currently in place.
document.addEventListener('livewire:init', function () {
    if (window.Livewire && typeof window.Livewire.hook === 'function') {
        window.Livewire.hook('morphed', function () {
            setTimeout(initServicesDropdown, 50);
        });
    }
});

console.log('✅ Services dropdown script loaded');

// City Search Functionality - Database search with dropdown
function initCitySearch() {
    console.log('🏙️ Initializing database city search...');
    
    const cityInput = document.getElementById('citysearch');
    const cityAppend = document.getElementById('cityappend');
    
    if (!cityInput) {
        console.log('❌ City input not found');
        return false;
    }
    
    console.log('✅ City input found');
    
    let searchTimeout = null;

    // Country-name → ISO-2 code lookup. Backed by window.EvooryGetCountryCode (loaded
    // from /assets/js/listing-page.js). Inline shim keeps this script working even if
    // the external file hasn't loaded yet.
    function getCountryCode(countryName) {
        if (!countryName) return null;
        if (typeof window.EvooryGetCountryCode === 'function') return window.EvooryGetCountryCode(countryName);
        return null;
    }

    // Helper function to convert country code to flag emoji
    function getFlagEmoji(countryCode) {
        if (!countryCode || countryCode.length !== 2) return '';
        
        const codePoints = countryCode
            .toUpperCase()
            .split('')
            .map(char => 127397 + char.charCodeAt());
        
        return String.fromCodePoint(...codePoints);
    }
    
    // Function to search cities from database
    function searchCities(query) {
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            cityAppend.style.display = 'none';
            cityAppend.innerHTML = '';
            return;
        }
        
        searchTimeout = setTimeout(function() {
            console.log('🔍 Searching for city:', query);
            
            // Use SessionRecovery.fetch for automatic token refresh on session expiry
            var fetchFn = window.SessionRecovery ? window.SessionRecovery.fetch.bind(window.SessionRecovery) : fetch;
            
            fetchFn('/cities/search?_=' + Date.now(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.SessionRecovery ? window.SessionRecovery.getToken() : '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                body: JSON.stringify({
                    query: query
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ City search results:', data);
                console.log('First city data:', data[0]);
                
                // Clear previous results
                cityAppend.innerHTML = '';
                
                if (data.length === 0) {
                    cityAppend.innerHTML = '<div class="opt" style="color: #999; cursor: default;">No cities found</div>';
                    cityAppend.style.display = 'block';
                } else {
                    // Add each city to the results
                    data.forEach(function(city) {
                        console.log('Processing city:', city.name, 'Count:', city.profile_count);
                        const citySlug = city.name.toLowerCase().replace(/\s+/g, '-');

                        // Prefer the server-provided ISO (batched DB lookup),
                        // fall back to the JS hardcoded table only if missing.
                        // This unblocks countries not in EvooryCountryCodes,
                        // e.g. Algeria → DZ.
                        const countryCode = city.iso || getCountryCode(city.country);

                        const opt = document.createElement('div');
                        opt.className = 'opt';
                        
                        // Add flag if country code exists
                        if (countryCode) {
                            const flagSpan = document.createElement('span');
                            flagSpan.className = 'flg';
                            flagSpan.style.marginRight = '10px';
                            flagSpan.style.display = 'inline-block';
                            flagSpan.style.width = '16px';
                            flagSpan.style.height = '10px';
                            flagSpan.style.backgroundSize = 'cover';
                            flagSpan.style.backgroundPosition = 'center';
                            flagSpan.style.backgroundImage = `url(https://flagcdn.com/w40/${countryCode.toLowerCase()}.png)`;
                            opt.appendChild(flagSpan);
                        }
                        
                        // Add city name
                        const nameSpan = document.createElement('span');
                        nameSpan.textContent = city.name;
                        nameSpan.style.flex = '1';
                        opt.appendChild(nameSpan);
                        
                        // Add profile count
                        if (city.profile_count !== undefined) {
                            const countSpan = document.createElement('span');
                            countSpan.textContent = city.profile_count;
                            countSpan.style.color = '#999';
                            countSpan.style.fontSize = '12px';
                            countSpan.style.marginLeft = 'auto';
                            opt.appendChild(countSpan);
                        }
                        
                        
                        opt.addEventListener('click', function() {
                            console.log('🎯 City selected:', city.name);
                            cityInput.value = city.name;
                            cityAppend.style.display = 'none';
                            
                            // Show loading state
                            cityInput.disabled = true;
                            cityInput.value = `Loading ${city.name}...`;
                            
                            // Redirect to the city page
                            const currentGender = '{{ $gender ?? "female" }}';
                            const urlPath = `/${currentGender}-escorts-in-${citySlug}`;
                            console.log('🚀 Redirecting to:', urlPath);
                            window.location.href = urlPath;
                        });
                        
                        cityAppend.appendChild(opt);
                    });
                    
                    cityAppend.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('❌ City search error:', error);
                // Show user-friendly error with retry hint
                cityAppend.innerHTML = '<div class="opt" style="color: #dc3545; cursor: default;">Connection error. Please try again.</div>';
                cityAppend.style.display = 'block';
                
                // Auto-retry after a short delay
                setTimeout(function() {
                    if (cityInput.value.trim().length >= 2) {
                        console.log('🔄 Auto-retrying city search...');
                        searchCities(cityInput.value.trim());
                    }
                }, 2000);
            });
        }, 300);
    }
    
    // Input event listener
    cityInput.addEventListener('input', function(e) {
        const value = e.target.value.trim();
        console.log('📝 City input changed:', value);
        searchCities(value);
    });
    
    // Click outside to close dropdown
    document.addEventListener('click', function(e) {
        if (!cityInput.contains(e.target) && !cityAppend.contains(e.target)) {
            cityAppend.style.display = 'none';
        }
    });
    
    // Focus event
    cityInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            searchCities(this.value.trim());
        }
    });
    
    console.log('🏙️ Database city search initialized successfully');
    console.log('💡 Type at least 2 characters to search all cities from database');
    return true;
}

// Initialize city search
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => initCitySearch(), 1000);
});

window.addEventListener('load', function() {
    setTimeout(() => initCitySearch(), 1500);
});

// Livewire ready
document.addEventListener('livewire:load', function() {
    setTimeout(() => initCitySearch(), 500);
});

// Fallback
setTimeout(() => {
    if (!initCitySearch()) {
        setTimeout(() => initCitySearch(), 2000);
    }
}, 3000);

// Mobile City Search Functionality
function initMobileCitySearch() {
    console.log('📱 Initializing mobile city search...');
    
    const mobileCityInput = document.getElementById('mobile_city_search');
    const mobileCityResults = document.getElementById('mobile_city_results');
    const mobileSelectedCityName = document.getElementById('mobile_selected_city_name');
    
    if (!mobileCityInput) {
        console.log('❌ Mobile city input not found');
        return false;
    }
    
    console.log('✅ Mobile city input found');
    
    let searchTimeout = null;

    // Country-name → ISO-2 code lookup. See window.EvooryGetCountryCode in /assets/js/listing-page.js.
    function getCountryCode(countryName) {
        if (!countryName) return null;
        if (typeof window.EvooryGetCountryCode === 'function') return window.EvooryGetCountryCode(countryName);
        return null;
    }

    // Function to search cities from database
    function searchCities(query) {
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            mobileCityResults.style.display = 'none';
            mobileCityResults.innerHTML = '';
            return;
        }
        
        searchTimeout = setTimeout(function() {
            console.log('🔍 Mobile searching for city:', query);
            
            // Use SessionRecovery.fetch for automatic token refresh on session expiry
            var fetchFn = window.SessionRecovery ? window.SessionRecovery.fetch.bind(window.SessionRecovery) : fetch;
            
            fetchFn('/cities/search?_=' + Date.now(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.SessionRecovery ? window.SessionRecovery.getToken() : '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                body: JSON.stringify({
                    query: query
                })
            })
            .then(response => {
                console.log('Mobile response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Mobile city search results:', data);
                
                // Clear previous results
                mobileCityResults.innerHTML = '';
                
                if (data.length === 0) {
                    mobileCityResults.innerHTML = '<a class="dropdown-item" style="color: #999;">No cities found</a>';
                    mobileCityResults.style.display = 'block';
                } else {
                    // Add each city to the results
                    data.forEach(function(city) {
                        const citySlug = city.name.toLowerCase().replace(/\s+/g, '-');
                        const countryCode = getCountryCode(city.country);
                        
                        const item = document.createElement('a');
                        item.className = 'dropdown-item';
                        item.href = '#';
                        item.style.display = 'flex';
                        item.style.alignItems = 'center';
                        item.style.padding = '10px 15px';
                        item.style.color = '#fff';
                        
                        // Add flag if country code exists
                        if (countryCode) {
                            const flagSpan = document.createElement('span');
                            flagSpan.style.marginRight = '10px';
                            flagSpan.style.display = 'inline-block';
                            flagSpan.style.width = '24px';
                            flagSpan.style.height = '16px';
                            flagSpan.style.backgroundSize = 'cover';
                            flagSpan.style.backgroundPosition = 'center';
                            flagSpan.style.backgroundImage = `url(https://flagcdn.com/w40/${countryCode.toLowerCase()}.png)`;
                            item.appendChild(flagSpan);
                        }
                        
                        // Add city name
                        const nameSpan = document.createElement('span');
                        nameSpan.textContent = city.name;
                        nameSpan.style.flex = '1';
                        item.appendChild(nameSpan);
                        
                        // Add profile count
                        if (city.profile_count !== undefined) {
                            const countSpan = document.createElement('span');
                            countSpan.textContent = city.profile_count;
                            countSpan.style.color = '#999';
                            countSpan.style.fontSize = '12px';
                            countSpan.style.marginLeft = 'auto';
                            item.appendChild(countSpan);
                        }
                        
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            console.log('🎯 Mobile city selected:', city.name);
                            mobileCityInput.value = city.name;
                            mobileCityResults.style.display = 'none';
                            
                            if (mobileSelectedCityName) {
                                mobileSelectedCityName.textContent = 'Selected: ' + city.name;
                            }
                            
                            // Update Livewire component
                            @this.set('selectedcity', city.name);
                            @this.set('city', city.id);
                            @this.set('cityname', city.name);
                        });
                        
                        mobileCityResults.appendChild(item);
                    });
                    
                    mobileCityResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('❌ Mobile city search error:', error);
                // Show user-friendly error with retry hint
                mobileCityResults.innerHTML = '<a class="dropdown-item" style="color: #dc3545;">Connection error. Please try again.</a>';
                mobileCityResults.style.display = 'block';
                
                // Auto-retry after a short delay
                setTimeout(function() {
                    if (mobileCityInput.value.trim().length >= 2) {
                        console.log('🔄 Auto-retrying mobile city search...');
                        searchCities(mobileCityInput.value.trim());
                    }
                }, 2000);
            });
        }, 300);
    }
    
    // Input event listener
    mobileCityInput.addEventListener('input', function(e) {
        const value = e.target.value.trim();
        console.log('📝 Mobile city input changed:', value);
        searchCities(value);
    });
    
    // Click outside to close dropdown
    document.addEventListener('click', function(e) {
        if (!mobileCityInput.contains(e.target) && !mobileCityResults.contains(e.target)) {
            mobileCityResults.style.display = 'none';
        }
    });
    
    // Focus event
    mobileCityInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            searchCities(this.value.trim());
        }
    });
    
    console.log('📱 Mobile city search initialized successfully');
    return true;
}

// Initialize mobile city search
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => initMobileCitySearch(), 1000);
});

window.addEventListener('load', function() {
    setTimeout(() => initMobileCitySearch(), 1500);
});

// Listen for mobile search modal open event
document.addEventListener('livewire:load', function() {
    setTimeout(() => initMobileCitySearch(), 500);
});

Livewire.on('mobile-search-toggled', function(data) {
    if (data.show) {
        setTimeout(() => initMobileCitySearch(), 300);
    }
});

// Listen for currency updates from Livewire and update the currency dropdown
Livewire.on('currency-updated', function(data) {
    console.log('💱 Currency update received:', data);
    
    // Handle both object with array data (Livewire v3) and direct object
    const eventData = Array.isArray(data) ? data[0] : data;
    const currencyId = eventData.currencyId;
    const currencyCode = eventData.currencyCode;
    
    if (!currencyId) {
        console.log('❌ No currency ID in event data');
        return;
    }
    
    // Update the main currency dropdown (search header)
    const currencySelect = document.querySelector('select[wire\\:model="currency"]');
    const currencyCombobox = document.querySelector('select[data-currency-combobox="true"]');
    
    // Try the main dropdown first
    if (currencySelect) {
        currencySelect.value = currencyId;
        console.log('✅ Main currency dropdown updated to:', currencyId);
        
        // Trigger change event for any listeners
        currencySelect.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    // Also update the combobox version if exists
    if (currencyCombobox) {
        currencyCombobox.value = currencyId;
        console.log('✅ Currency combobox updated to:', currencyId);
        
        // Trigger change event
        currencyCombobox.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    // Update the mobile search currency dropdown too
    const mobileCurrencySelect = document.getElementById('currency');
    if (mobileCurrencySelect && mobileCurrencySelect !== currencySelect) {
        mobileCurrencySelect.value = currencyId;
        console.log('✅ Mobile currency dropdown updated to:', currencyId);
        
        // If Select2 is initialized, update it
        if (typeof jQuery !== 'undefined' && jQuery(mobileCurrencySelect).data('select2')) {
            jQuery(mobileCurrencySelect).val(currencyId).trigger('change.select2');
        }
    }
});

// Fallback
setTimeout(() => {
    if (!initMobileCitySearch()) {
        setTimeout(() => initMobileCitySearch(), 2000);
    }
}, 3000);
  </script>

  {{-- What's New scroll indicator moved to /assets/js/listing-page.js --}}
  @endpush
