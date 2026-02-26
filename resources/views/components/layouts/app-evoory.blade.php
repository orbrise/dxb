<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Evoory - Premium Escort Directory')</title>
    <meta name="description" content="@yield('description', 'Connect with escorts from Dubai and around the world. Premium escort directory with verified listings.')">
    
    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    
    {{-- Critical CSS inline for faster FCP --}}
    <style>
        :root{--bg-primary:#0a0a0a;--bg-secondary:#111111;--accent:#C1F11D;--text-primary:#ffffff}
        body{margin:0;background:var(--bg-primary);color:var(--text-primary);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
        .ev-header{background:var(--bg-secondary);padding:12px 0;position:sticky;top:0;z-index:1000}
        .ev-container{max-width:1200px;margin:0 auto;padding:0 16px}
        .ev-logo{font-size:24px;font-weight:700;color:var(--accent);text-decoration:none}
        .ev-flex{display:flex}.ev-items-center{align-items:center}.ev-justify-between{justify-content:space-between}
    </style>
    
    {{-- Main theme CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/evoory-theme.css') }}">
    
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
    
    {{-- Theme JS - Deferred for performance --}}
    <script src="{{ asset('assets/js/evoory-theme.js') }}" defer></script>
    
    {{-- Additional page-specific JS --}}
    @stack('js')
</body>
</html>
