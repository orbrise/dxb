<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoTitle ?? 'Evoory - Premium Escort Directory' }}</title>
    <meta name="description" content="{{ $seoDescription ?? 'Connect with escorts from Dubai and around the world. Premium escort directory with verified listings.' }}">
    @if(!empty($seoKeywords))
    <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    
    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Critical CSS inline for faster FCP --}}
    <style>
        :root{--bg-primary:#0a0a0a;--bg-secondary:#111111;--accent:#C1F11D;--text-primary:#ffffff}
        body{margin:0;background:var(--bg-primary);color:var(--text-primary);font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-weight:300}
        .ev-header{background:var(--bg-secondary);padding:12px 0;position:sticky;top:0;z-index:1000}
        .ev-container{max-width:1200px;margin:0 auto;padding:0 16px}
        .ev-logo{font-size:24px;font-weight:700;color:var(--accent);text-decoration:none}
        .ev-flex{display:flex}.ev-items-center{align-items:center}.ev-justify-between{justify-content:space-between}
    </style>
    
    {{-- Bootstrap 4 CSS + Font Awesome (required for grid, components, icons) --}}
    <link rel="stylesheet" href="{{smart_asset('assets/css/app.css')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    {{-- Main theme CSS (loaded after Bootstrap to override) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/evoory-theme.css') }}?v=20260404-1">

    {{-- Additional page-specific CSS --}}
    @stack('css')
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
