<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $currentUrl = request()->path();
    $seo = app(App\Http\Controllers\Admin\SeoKeywordController::class)->getSeoTags($currentUrl);
    @endphp
    <meta charset="UTF-8" />
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <title>{{ $seo['title'] }}</title>
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="//code.jquery.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//d257pz9kz95xf4.cloudfront.net">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://code.jquery.com/jquery-3.7.1.min.js" as="script" crossorigin="anonymous">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" as="script" crossorigin="anonymous">
    
    <!-- Favicon -->
    <link href="{{ asset('storage/'.$setting->favicon) }}" rel="shortcut icon" type="images/x-icon" />
    
    <!-- Critical CSS (inline for faster loading) -->
    <style>
        /* Critical above-the-fold CSS */
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .navbar { background-color: #343a40; }
        .container-fluid { max-width: 100%; padding: 0 15px; }
        
        /* Select2 optimizations */
        .select2-container .select2-selection--multiple {
            border: 1px solid #ccc !important;
            padding: 5px !important;
            min-height: 40px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            color: white !important;
        }
        
        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <!-- Load non-critical CSS asynchronously -->
    @include('components.layouts.css-async')
    @stack('css')
</head>
<body>
    <header id="header">
        @include('components.layouts.header-optimized')
        @yield('headerform')
    </header>

    <!-- Main content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Footer moved before scripts for better perceived performance -->
    @include('components.layouts.footer-optimized')
    @yield('advancesearch')
    @yield('homepopup')
    
    <!-- Scripts loaded at bottom for better performance -->
    <script>
        // Inline critical JavaScript for immediate functionality
        window.addEventListener('DOMContentLoaded', function() {
            // Initialize critical functionality immediately
            document.body.classList.add('loaded');
        });
    </script>
    
    <!-- Load scripts asynchronously -->
    <script async src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script async src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <!-- Lazy load non-critical scripts -->
    <script>
        // Load non-critical scripts after page load
        window.addEventListener('load', function() {
            @stack('js')
        });
    </script>
    
    @yield('registertype')
    
    <!-- Service Worker for caching -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function() {
                    // Service worker registration failed
                });
            });
        }
    </script>
</body>
</html>
