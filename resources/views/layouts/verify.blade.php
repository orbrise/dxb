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
    <link href="{{ asset('storage/'.$setting->favicon) }}" rel="shortcut icon" type="images/x-icon" />
    @include('components.layouts.css')
    @stack('css')
  </head>
  <body>
        
    <header id="header">
      @include('components.layouts.header')
      @yield('headerform')
    </header>

    @yield('content')
     
    <div id="fb-root"></div>
    @include('components.layouts.footer')
    @yield('advancesearch')
    @yield('homepopup')
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('js')
    @yield('registertype')
  </body>
</html>