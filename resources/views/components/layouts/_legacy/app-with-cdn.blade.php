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
    
    <!-- Preconnect to asset domain -->
    @if(!app()->environment('local'))
        <x-asset-loader type="preconnect" src="" />
    @endif
    
    <!-- Critical CSS inlined -->
    <x-asset-loader type="css" src="assets/css/critical.css" :critical="true" />
    
    <!-- Non-critical CSS loaded asynchronously -->
    <x-asset-loader type="css" src="assets/css/app3.css" :preload="true" />
    <x-asset-loader type="css" src="assets/css/image-fallbacks.css" :preload="true" />
    
    <!-- Favicon and icons from CDN -->
    <x-asset-loader type="icon" src="assets/images/favicon.ico" />
    <link rel="apple-touch-icon" href="{{ local_or_cdn_asset('assets/images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ local_or_cdn_asset('assets/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ local_or_cdn_asset('assets/images/favicon-16x16.png') }}">
    
    <!-- Select2 CSS -->
    <style>
        .select2-container .select2-selection--multiple {
            border: 1px solid #ccc !important;
            padding: 5px !important;
            min-height: 40px !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            color: white !important;
        }
    </style>

    <!-- Preload critical JavaScript -->
    <x-asset-loader type="js" src="assets/js/app.js" :preload="true" />
    
    @stack('css')
</head>
<body>
    <!-- Header -->
    <header id="header">
        @include('components.layouts.header-optimized')
        @yield('headerform')
    </header>
    
    <!-- Main content -->
    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('components.layouts.footer-optimized')
    
    <!-- JavaScript - Deferred loading -->
    <x-asset-loader type="js" src="assets/js/jquery.min.js" :defer="true" />
    <x-asset-loader type="js" src="assets/js/bootstrap.min.js" :defer="true" />
    <x-asset-loader type="js" src="assets/js/image-fallback-handler.js" :defer="true" />
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    @stack('js')
    
    <!-- Service Worker for caching -->
    @if(!app()->environment('local'))
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ local_or_cdn_asset("sw.js") }}')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    @endif
</body>
</html>
