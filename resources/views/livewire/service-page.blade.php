@section('headerform')
@include('components.layouts.headerform')
@endsection

@push('css')

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
        
        /* Image optimization */
        img[loading="lazy"] {
            background: #f0f0f0;
            min-height: 60px;
        }
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
    </script>
    <style>
        .citys {
            width: 99%;
    height: 179px;
    position: absolute;
    background: #474747;
    padding: 2px;
    overflow: hidden;
    display: none;
        }

        .opt {
            font-size:12px;
            margin-bottom: 3px;
            cursor: pointer;
        }
        .flg {
            margin-right:10px;
        }

        form.listing .big-one-line .typeahead-city-wrapper input {
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 37px;

}

#header {
    background: rgba(0, 0, 0, .3);
    margin-bottom: 0px;
}

.listinga {
  background: #565656;
    color: white;
    border: none;
    border-radius: 0px;
    height: 52px;
    font-size: 21px;
    border-bottom: 2px dashed #aaa;
}

.mi-new-image-input {display:none;}
.modal-backdrop {
    z-index: 1;
}

.typeahead-city-wrapper .twitter-typeahead {
    z-index: 0;
}
.adinput {background: white !important;
    color: black !important;
    width: 100% !important}

    .num-range-to {
    float: left;
    }
    .advanced-search-checkboxes {
    padding-top: 30px;
}

/* Service Sidebar Styles - Matching Design */
.service-sidebar {
    background: #3a3a3a;
    padding: 0;
    border-radius: 4px;
    margin-bottom: 20px;
    overflow: hidden;
}

.service-sidebar h3 {
    color: #f5a623;
    font-size: 18px;
    margin: 0;
    padding: 20px;
    background: #2d2d2d;
    border-bottom: none;
    font-weight: 600;
}

.service-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.service-list li {
    margin: 0;
    border-bottom: 1px solid #2d2d2d;
}

.service-list li:last-child {
    border-bottom: none;
}

.service-list a {
    color: #f5a623;
    text-decoration: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    transition: all 0.2s ease;
    font-size: 16px;
    background: #3a3a3a;
}

.service-list a:hover {
    background: #444;
    color: #f4b827;
}

.service-list a.active {
    background: #4a4a4a;
    color: #f4b827;
    font-weight: 500;
}

.service-count {
    background: #5a5a5a;
    color: #fff;
    padding: 4px 12px;
    border-radius: 14px;
    font-size: 13px;
    min-width: 40px;
    text-align: center;
    font-weight: 500;
}

.service-list a.active .service-count {
    background: #666;
}

.select2.select2-container {
  width: 100% !important;
}

.select2.select2-container .select2-selection {
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  height: 34px;
  margin-bottom: 15px;
  outline: none !important;
  transition: all .15s ease-in-out;
}

.select2.select2-container .select2-selection .select2-selection__rendered {
  color: #333;
  line-height: 32px;
  padding-right: 33px;
}

.select2.select2-container .select2-selection .select2-selection__arrow {
  background: #f8f8f8;
  border-left: 1px solid #ccc;
  -webkit-border-radius: 0 3px 3px 0;
  -moz-border-radius: 0 3px 3px 0;
  border-radius: 0 3px 3px 0;
  height: 32px;
  width: 33px;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
  background: #f8f8f8;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
  -webkit-border-radius: 0 3px 0 0;
  -moz-border-radius: 0 3px 0 0;
  border-radius: 0 3px 0 0;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
  border: 1px solid #34495e;
}

.select2.select2-container .select2-selection--multiple {
  height: auto;
  min-height: 34px;
}

.select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
  margin-top: 0;
  height: 32px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__rendered {
  display: block;
  padding: 0 4px;
  line-height: 29px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice {
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  margin: 4px 4px 0 0;
  padding: 0 6px 0 22px;
  height: 24px;
  line-height: 24px;
  font-size: 12px;
  position: relative;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
  position: absolute;
  top: 0;
  left: 0;
  height: 22px;
  width: 22px;
  margin: 0;
  text-align: center;
  color: #e74c3c;
  font-weight: bold;
  font-size: 16px;
}

.select2-container .select2-dropdown {
  background: transparent;
  border: none;
  margin-top: -5px;
}

.select2-container .select2-dropdown .select2-search {
  padding: 0;
}

.select2-container .select2-dropdown .select2-search input {
  outline: none !important;
  border: 1px solid #34495e !important;
  border-bottom: none !important;
  padding: 4px 6px !important;
}

.select2-container .select2-dropdown .select2-results {
  padding: 0;
}

.select2-container .select2-dropdown .select2-results ul {
  background: #fff;
  border: 1px solid #34495e;
}

.select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
  background-color: #3498db;
}

@media (min-width: 768px) {
    .activity-record.mini .img-wrapper.premium img, .listing-li .img-wrapper.premium img, .listings-grid .img-wrapper.premium img {
        object-fit: cover;
    }
}
@media (min-width: 768px) {
    .activity-record.mini .img-wrapper.mini img, .listing-li .img-wrapper.mini img, .listings-grid .img-wrapper.mini img {
        object-fit: cover;
    }
}

.chosen-container {
width:auto !important;
min-width: 222px;
}

.chosen-container .chosen-drop {
    background: #2c2c2c; /* Dark background */
    color: #ffffff;     /* White text */
    border: 1px solid #444; /* Border for dropdown */
}

/* Highlighted item in the dropdown */
.chosen-container .chosen-results li.highlighted {
    background: #444; /* Darker highlight */
    color: #ffffff;
}

/* Dropdown items */
.chosen-container .chosen-results li {
    color: #ffffff;
}

/* Search field inside Chosen dropdown */
.chosen-container .chosen-search input[type="text"] {
    background: #2c2c2c;
    color: #ffffff;
    border: 1px solid #444;
}

/* Selected items in multiple select */
.chosen-container .search-choice {
    background: #444;
    color: #ffffff;
    border: 1px solid #666;
}

/* Placeholder text */
.chosen-container-multi .chosen-choices li.search-field input {
    color: #bbbbbb;
  
}

.chosen-container-multi .chosen-choices {
  background-color: #333 !important;
    background: -webkit-gradient(linear, left top, left bottom, from(#222), to(#333))!important;
    background: linear-gradient(#222, #333)!important;
        border: 1px solid #5a5a5a;
            border-radius: 5px;
            height: 35px;
    }

.chosen-container-multi .chosen-choices li.search-field input[type="text"] {
    height: 33px;
    color: #ffffff;
    font-size: 14px;

}
  .hidden {
            display: none;
        }

        .typeahead-city-wrapper {
    position: relative;
}

/* City search styling - simplified */
.typeahead-city-wrapper {
    position: relative;
}

.typeahead-city-wrapper input.search-bar--city {
    padding-left: 35px; /* Make space for the icon */
}

.city-search-icon {
    position: absolute !important;
    left: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #fff !important;
    z-index: 10 !important;
    pointer-events: none !important;
    font-size: 14px !important;
}

@media (min-width: 768px) {
    .activity-record.mini .img-wrapper.basic img, .listing-li .img-wrapper.basic img, .listings-grid .img-wrapper.basic img {
      object-fit: cover; 
    }
}


      @media (max-width: 567px) {
    .listings>.listing-li.basic, .listings>.listing-li.free {
        width: 100%;
        display: inline-block;
    }
    
    .city-search-icon {
        font-size: 12px !important;
        left: 8px !important;
    }
}

.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    color: white !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: white !important;
}

.select2-dropdown {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    color: white !important;
}

.select2-container--default .select2-results__option {
    color: white !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #555 !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #555 !important;
    border: 1px solid #666 !important;
    color: white !important;
}

.select2-search--dropdown .select2-search__field {
    background-color: #444 !important;
    color: white !important;
    border: 1px solid #666 !important;
}

/* Fix for mobile modal z-index */
.select2-container--open {
    z-index: 9999 !important;
}

.select2-container .select2-choice, .select2-container .select2-choices, .select2-container .select2-choices .select2-search-field input, .select2-search input {
    border-color: #3d3d3d;
    border-radius: 4px;
    background-color: #333 ;
}

.select2-container .select2-choice, .select2-container .select2-choices, .select2-container .select2-choices .select2-search-field input, .select2-search input {
    color: #dadada;
}

/* Advanced Search Modal Styling */
.modal-backdrop.show {
    opacity: 0.3 !important;
}

#search-more .modal-content {
    background-color: #2d2d2d !important;
    color: white !important;
    border: 1px solid #444;
    border-radius: 4px;
}

#search-more .modal-header {
    background-color: #2d2d2d !important;
    border-bottom: 1px solid #444;
    padding: 20px;
}

#search-more .modal-title {
    color: #f5a623 !important;
    font-weight: 600;
}

#search-more .close {
    color: white !important;
    opacity: 0.8;
    text-shadow: none;
}

#search-more .close:hover {
    opacity: 1;
}

#search-more .modal-body {
    background-color: #2d2d2d !important;
    padding: 30px 20px;
}

#search-more label {
    color: #ddd !important;
    font-weight: normal;
}

#search-more .modal-footer {
    background-color: #2d2d2d !important;
    border-top: 1px solid #444;
    padding: 20px;
}

#search-more .btn-primary {
    background-color: #f5a623 !important;
    border-color: #f5a623 !important;
    color: #000 !important;
}

#search-more .btn-primary:hover {
    background-color: #f4b827 !important;
    border-color: #f4b827 !important;
}

/* Main Navigation Tabs - ESCORTS / WHAT'S NEW */
.nav-tabs-wrapper {
    margin-top: 0;
    display: block !important;
    width: 100%;
    position: relative;
    z-index: 100;
}

.nav-tabs-wrapper .btn-group {
    display: flex !important;
    width: 100%;
}

.nav-tabs-wrapper .btn {
    transition: all 0.2s ease;
}

.nav-tabs-wrapper .btn:hover {
    opacity: 0.9;
}

/* Mobile specific styles for nav tabs */
@media (max-width: 768px) {
    .nav-tabs-wrapper {
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .nav-tabs-wrapper .btn-group {
        display: flex !important;
        flex-direction: row !important;
        width: 100% !important;
    }
    
    .nav-tabs-wrapper .btn {
        flex: 1 !important;
        font-size: 11px !important;
        padding: 10px 8px !important;
        letter-spacing: 0.3px !important;
    }
}

     </style>

@endpush
    
<div class="">
  <div wire:loading class="page-loader">
    <div class="spinner"></div>
  </div>

{{-- Include common search header --}}
@include('components.search-header')

<div class="container-fluid">
    <div class="content-wrapper no-sidebar">
      <div id="content" class="mt-3">

    <div class="col-md-9 col-xs-12" style="padding:0px">
      <a class="page-title" href="#">
        <h1>{{ $serviceName }} Escorts in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}@if($currentCity && $currentCity->country), {{ $currentCity->country }}@endif</h1>
      </a>
      
      
      <p class="page-desc margin-bottom hidden-xs">
        We have {{ $profiles->total() }} {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }} escorts offering <strong>{{ $serviceName }}</strong> on Massage Republic. 
        <span class="services">
          @if($profiles->count() > 0)
            @php
              $prices = $profiles->pluck('incallprice')->filter();
              $minPrice = $prices->min();
              $maxPrice = $prices->max();
              $avgPrice = $prices->avg();
              $currencyCode = $currentCurrency->code ?? 'AED';
              // Approximate USD conversion rates
              $usdRates = [
                  'AED' => 0.27,
                  'PKR' => 0.0036,
                  'INR' => 0.012,
                  'SAR' => 0.27,
                  'GBP' => 1.27,
                  'EUR' => 1.10,
              ];
              $usdRate = $usdRates[$currencyCode] ?? 0.27;
            @endphp
            Prices range from {{ number_format($minPrice) }} {{ $currencyCode }} to {{ number_format($maxPrice) }} {{ $currencyCode }} 
            <span class="usd-price text-muted">(US$ {{ number_format($minPrice * $usdRate, 0) }} to US$ {{ number_format($maxPrice * $usdRate, 0) }})</span>, 
            the average cost advertised is {{ number_format($avgPrice) }} {{ $currencyCode }} 
            <span class="usd-price text-muted">(US$ {{ number_format($avgPrice * $usdRate, 0) }})</span>.
          @endif
        </span>
      </p>
      
      <div class="listings listings-spots listing-spots--minimal border-top padding-top mx-n2 mx-sm-0">
        
     
       @if($auctions->count() > 0)
<div class="listings listings-spots listing-spots--minimal border-bottom mx-n2 mx-sm-0">
  @foreach($auctions as $auction)
  <div class="spot">
    {{-- Only show the auction bidding overlay for active auctions AND logged in users --}}
    @if($auction->status == 'active' && Auth::check())
    <div class="auction-cover d-flex align-items-center flex-wrap">
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
            <strong class="ml-2">€{{ $auction->current_price }}</strong>
          </h3>
          <a class="btn btn-primary px-5 mb-3 font-weight-bold" href="/auctions/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/spot/{{ $auction->spot_number }}" style="font-size:1rem">Make offer</a>
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
                  <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                    {{ $auction->winnerProfile->name }}
                  </a>
                @elseif($auction->status == 'active')
                  <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                    {{ $auction->winnerProfile->name ?? 'Available Spot' }}
                  </a>
                @endif
              </h2>
              
              <div class="thumbs">
                <div class="main-thumbs">
                  @if($auction->status == 'ended' && $auction->winnerProfile)
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
                            <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ smart_asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->coverimg->image) }}">
                          @elseif(!empty($auction->winnerProfile->singleimg))
                            <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ smart_asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->singleimg->image) }}">
                          @endif
                        </div>
                      </span>
                    </a>
                  @else
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
                            <img alt="{{ $auction->winnerProfile->name ?? 'Escort' }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ smart_asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->coverimg->image) }}">
                          @elseif(isset($auction->winnerProfile) && !empty($auction->winnerProfile->singleimg))
                            <img alt="{{ $auction->winnerProfile->name ?? 'Escort' }} - escort in {{ $selectedcity }}" 
                                 class="img-responsive" 
                                 height="208" 
                                 width="200"
                                 loading="lazy"
                                 src="{{ smart_asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->singleimg->image) }}">
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
                    @foreach($auction->winnerProfile->multipleimgss->take(3) as $key => $img)
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
                              <img alt="{{ $auction->winnerProfile->name }} - Photo {{ $key+1 }}" class="img-responsive" height="60" 
                                src="{{ smart_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}" width="60">
                            </div>
                          </span>
                        </a>
                      </div>
                    @endforeach
                  @elseif(isset($auction->winnerProfile) && $auction->winnerProfile->multipleimgs)
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
                              <img alt="{{ $auction->winnerProfile->name }} - Photo {{ $key+1 }}" class="img-responsive" height="60" 
                                src="{{ smart_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}" width="60">
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
                      <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                        {{ $auction->winnerProfile->name }}
                      </a>
                    @else
                      <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                        {{ $auction->winnerProfile->name ?? 'Available Spot' }}
                      </a>
                    @endif
                  </h2>
                
                  @if($auction->status == 'ended' && $auction->winnerProfile)
                    <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                      <p>{{ str()->of($auction->winnerProfile->about)->limit(400) }}</p>
                    </a>
                    <p class="no-margin see-more">
                      <a class="btn btn-dark" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                        See more & contact
                      </a>
                    </p>
                  @else
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
        
      </div>
      <div class="listings  @if($auctions->count() > 0 and Auth::check()) padding-top @endif">
        @forelse($profiles as $profile)
        @if($profile->package_id == 21)
        <div class="listing-li premium thumbs-3 thumbs-mini">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
              {{ $profile->name }}
              @if($profile->reviews->count() > 0)
              <span class="badge" data-placement="top" data-toggle="tooltip" title="{{$profile->reviews->count()}} Reviews">
                <i class="fa fa-heart2"></i>
                <span>{{ $profile->reviews->count() }}</span>
              </span>
              @endif
            </a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                <span class="img-wrapper premium">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
                    @if(!empty($profile->coverimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}" 
                         class="img-responsive" 
                         height="208" 
                         width="200"
                         loading="lazy"
                         src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" />
                    @else
                    <img alt="{{ $profile->name }} - escort in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}" 
                         class="img-responsive" 
                         height="208" 
                         width="200"
                         loading="lazy"
                         src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" />
                    @endif
                  </div>
                </span>
              </a>
            </div>

            
            <div class="other-thumbs pull-left">
              @forelse($profile->multipleimgss as $imgs)
              <div class="thumb thumb-0">
                <a class="img img-responsive pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
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
                           src="{{smart_asset('userimages/'.$imgs->user_id.'/'.$imgs->profile_id.'/'.$imgs->image)}}" />
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
                <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                  {{ $profile->name }}
                  @if($profile->reviews->count() > 0)
                  <span class="badge" data-placement="top" data-toggle="tooltip" title="{{$profile->reviews->count()}} Reviews">
                  <i class="fa fa-heart2"></i>
                    <span>{{ $profile->reviews->count() }}</span>
                  </span>
                  @endif
                </a>
              </h2>
              <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                <p>{{str()->of($profile->about)->limit(400)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>

        @elseif($profile->package_id == 20)
        <div class="listing-li pb-3 featured thumbs-2 thumbs-mini">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">{{$profile->name}}</a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                <span class="img-wrapper featured">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
               @if(!empty($profile->coverimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}" class="img-responsive" height="135" 
                         src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" width="115">
                    @else
                    <img alt="{{ $profile->name }} - escort in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}" class="img-responsive" height="135" 
                         src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" width="115">
              @endif
                  </div>
                </span>
              </a>
            </div>
            <div class="other-thumbs pull-left">
          @forelse($profile->multipleimgss->take(2) as $k => $imgs)
                      @if($k < 2)
              <div class="thumb thumb-0">
                <a class="img img-responsive pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                  <span class="img-wrapper mini">
                    @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                    <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                      <i class="fa fa-check"></i>
                      <span>Verified photos</span>
                    </span>
                    @endif
                    <div class="image-wrapper">
                      <img alt="{{ $profile->name }} - Photo {{ $k+1 }}" class="img-responsive" height="60" 
                           src="{{smart_asset('userimages/'.$imgs->user_id.'/'.$imgs->profile_id.'/'.$imgs->image)}}" width="60">
                    </div>
                  </span>
                </a>
              </div>
            @endif
            @empty
            @endforelse
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">{{$profile->name}} </a>
              </h2>
              <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                <p>{{str()->of($profile->about)->limit(400)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>

        @elseif($profile->package_id == 19 or empty($profile->package_id))
        <div class="listing-li pb-3 basic thumbs-0 thumbs-basic" style="padding-left:0px">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">{{$profile->name}}</a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                <span class="img-wrapper basic">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
               @if(!empty($profile->coverimg->image))
                    <img alt="{{ $profile->name }} - escort in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}" class="img-responsive" height="95" 
                         src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" width="89">
             @else
             @if(!empty($profile->singleimg->image))
             <img alt="{{ $profile->name }} - escort in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}" class="img-responsive" height="95" 
                  src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" width="89">
              @endif
                    @endif
                  </div>
                </span>
              </a>
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">{{$profile->name}}</a>
              </h2>
              <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">
                <p>{{str()->of($profile->about)->limit(400)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="/{{ $gender }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}/{{ $profile->id }}/{{ $profile->slug }}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>
        @else

        @endif

        @empty
        <div class="col-md-6">
          <h2>No Escorts offering {{ $serviceName }} in {{ $selectedcity }} yet</h2>
          <p>Register today and we will send you updates with new listings offering {{ $serviceName }} in {{ $selectedcity }}</p>
          <p></p>
          <div class="subscribe-btn-wrapper">
            <a class="btn btn-primary btn-lg btn-lg" href="/register">
              <i class="fa fa-newspaper"></i> Subscribe
            </a>
          </div>
          <p></p>
        </div>
        @endforelse
      </div>

      {{ $profiles->links('vendor.livewire.custom') }}
      
    </div>
    
    {{-- Sidebar with services list --}}
    <div class="col-md-3 hidden-sm hidden-xs">
     <h3>
          <i class="fa fa-list"></i> All Services in {{ ucfirst($currentCity->name ?? 'Dubai') }}
        </h3>
      <div class="service-sidebar">
       
        <ul class="service-list">
          @foreach($popularServices as $service)
          <li>
            <a href="/{{ strtolower($service->slug) }}-{{ $gender }}-escorts-in-{{ $selectedcity }}" 
               class="{{ $service->slug === $serviceSlug ? 'active' : '' }}">
              <span>{{ $service->name }}</span>
              <span class="service-count">{{ $service->profile_count }}</span>
            </a>
          </li>
          @endforeach
        </ul>
        
      
      </div>
    </div>
  </div>

</div>
</div>
</div>

@push('js')
<script src="{{ smart_asset('chosen/chosen.jquery.js')}}" defer></script>
<script>
// City search functionality - matching homepage implementation
function initCitySearch() {
    console.log('🏙️ Initializing city search...');
    
    const cityInput = document.getElementById('citysearch');
    const cityAppend = document.getElementById('cityappend');
    
    if (!cityInput || !cityAppend) {
        console.log('❌ City elements not found');
        return false;
    }
    
    console.log('✅ City elements found');
    
    let searchTimeout = null;
    
    // Helper function to get country code from country name
    function getCountryCode(countryName) {
        if (!countryName) return null;
        
        const countryMap = {
            'United Arab Emirates': 'AE',
            'Pakistan': 'PK',
            'India': 'IN',
            'United Kingdom': 'GB',
            'United States': 'US',
            'Brazil': 'BR',
            'Philippines': 'PH',
            'Thailand': 'TH',
            'Singapore': 'SG',
            'China': 'CN',
            'Japan': 'JP',
            'France': 'FR',
            'Germany': 'DE',
            'Italy': 'IT',
            'Spain': 'ES',
            'Canada': 'CA',
            'Australia': 'AU',
            'Netherlands': 'NL',
            'Belgium': 'BE',
            'Switzerland': 'CH',
            'Austria': 'AT',
            'Sweden': 'SE',
            'Norway': 'NO',
            'Denmark': 'DK',
            'Finland': 'FI',
            'Poland': 'PL',
            'Czech Republic': 'CZ',
            'Czechia': 'CZ',
            'Hungary': 'HU',
            'Turkey': 'TR',
            'Egypt': 'EG',
            'South Africa': 'ZA',
            'Saudi Arabia': 'SA',
            'Qatar': 'QA',
            'Kuwait': 'KW',
            'Bahrain': 'BH',
            'Oman': 'OM',
            'Lebanon': 'LB',
            'Jordan': 'JO',
            'Ireland': 'IE',
            'Portugal': 'PT',
            'Greece': 'GR',
            'Russia': 'RU',
            'Ukraine': 'UA',
            'Romania': 'RO',
            'Bulgaria': 'BG',
            'Croatia': 'HR',
            'Serbia': 'RS',
            'Malaysia': 'MY',
            'Indonesia': 'ID',
            'Vietnam': 'VN',
            'South Korea': 'KR',
            'Hong Kong': 'HK',
            'Taiwan': 'TW',
            'New Zealand': 'NZ',
            'Argentina': 'AR',
            'Mexico': 'MX',
            'Colombia': 'CO',
            'Chile': 'CL',
            'Peru': 'PE',
            'Venezuela': 'VE',
            'Honduras': 'HN',
            'Morocco': 'MA',
            'Tunisia': 'TN',
            'Kenya': 'KE',
            'Nigeria': 'NG',
            'Ethiopia': 'ET',
            'Sudan': 'SD',
            'Israel': 'IL',
            'Cyprus': 'CY',
            'Malta': 'MT',
            'Luxembourg': 'LU',
            'Monaco': 'MC',
            'Iceland': 'IS',
            'Estonia': 'EE',
            'Latvia': 'LV',
            'Lithuania': 'LT',
            'Slovenia': 'SI',
            'Slovakia': 'SK',
            'Bosnia and Herzegovina': 'BA',
            'Albania': 'AL',
            'North Macedonia': 'MK',
            'Montenegro': 'ME',
            'Armenia': 'AM',
            'Georgia': 'GE',
            'Azerbaijan': 'AZ',
            'Kazakhstan': 'KZ',
            'Uzbekistan': 'UZ',
            'Bangladesh': 'BD',
            'Sri Lanka': 'LK',
            'Nepal': 'NP',
            'Myanmar': 'MM',
            'Cambodia': 'KH',
            'Laos': 'LA',
            'Brunei': 'BN',
            'Maldives': 'MV',
            'Afghanistan': 'AF',
            'Iran': 'IR',
            'Iraq': 'IQ'
        };
        
        return countryMap[countryName] || null;
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
                
                // Clear previous results
                cityAppend.innerHTML = '';
                
                if (data.length === 0) {
                    cityAppend.innerHTML = '<div class="opt" style="color: #999;">No cities found</div>';
                    cityAppend.style.display = 'block';
                } else {
                    // Add each city to the results
                    data.forEach(function(city) {
                        const citySlug = city.slug || city.name.toLowerCase().replace(/\s+/g, '-');
                        
                        // Get country code - use city.iso if available, otherwise map from country name
                        const countryCode = city.iso || getCountryCode(city.country);
                        
                        const opt = document.createElement('div');
                        opt.className = 'opt';
                        opt.style.cursor = 'pointer';
                        opt.style.color = '#fff';
                        opt.style.padding = '2px 10px';
                        opt.style.transition = 'background-color 0.2s';
                        opt.style.display = 'flex';
                        opt.style.alignItems = 'center';
                        
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
                        
                        opt.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#5a5a5a';
                        });
                        
                        opt.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = 'transparent';
                        });
                        
                        opt.addEventListener('click', function() {
                            console.log('🎯 City selected:', city.name);
                            cityInput.value = city.name;
                            cityAppend.style.display = 'none';
                            
                            // Show loading state
                            cityInput.disabled = true;
                            cityInput.value = `Loading ${city.name}...`;
                            
                            // Keep the current service and redirect
                            const currentGender = '{{ $gender ?? "female" }}';
                            const currentService = '{{ $serviceSlug ?? "" }}';
                            
                            let urlPath;
                            if (currentService) {
                                urlPath = `/${currentService}-${currentGender}-escorts-in-${citySlug}`;
                            } else {
                                urlPath = `/${currentGender}-escorts-in-${citySlug}`;
                            }
                            
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
                cityAppend.innerHTML = '<div class="opt" style="color: #dc3545;">Connection error. Please try again.</div>';
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
    
    console.log('🏙️ City search initialized successfully');
    return true;
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing city search');
    setTimeout(initCitySearch, 500);
});

// Also initialize on Livewire ready (fallback)
if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:initialized', () => {
        console.log('Livewire initialized - initializing city search');
        setTimeout(initCitySearch, 500);
    });
}

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
                    newOption.style.backgroundColor = '#464646';
                    // Add checkmark indicator
                    if (!newOption.querySelector('.checkmark')) {
                        const checkmark = document.createElement('span');
                        checkmark.className = 'checkmark';
                        checkmark.innerHTML = ' ✓';
                        checkmark.style.color = '#4CAF50';
                        checkmark.style.fontWeight = 'bold';
                        checkmark.style.marginLeft = 'auto';
                        newOption.appendChild(checkmark);
                    }
                    console.log('✅ Selected:', serviceName);
                } else {
                    newOption.style.backgroundColor = '';
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
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const currentDropdown = document.getElementById('services-dropdown-list');
        const currentDisplayBox = document.getElementById('services-display-box');
        if (currentDropdown && currentDisplayBox && !currentDisplayBox.contains(e.target) && !currentDropdown.contains(e.target)) {
            currentDropdown.style.display = 'none';
            isOpen = false;
        }
    });
    
    console.log('✅ Services dropdown fully initialized!');
    return true;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM loaded, initializing services dropdown...');
    let attempts = 0;
    const maxAttempts = 10;
    
    function tryInit() {
        attempts++;
        console.log(`Attempt ${attempts}/${maxAttempts}`);
        
        if (initServicesDropdown()) {
            console.log('✅ Initialization successful!');
        } else if (attempts < maxAttempts) {
            setTimeout(tryInit, 500);
        } else {
            console.log('❌ Failed to initialize after', maxAttempts, 'attempts');
        }
    }
    
    tryInit();
});

// Also try on Livewire load
document.addEventListener('livewire:init', function() {
    console.log('🔄 Livewire initialized, setting up services dropdown...');
    setTimeout(initServicesDropdown, 500);
});
</script>
@endpush
