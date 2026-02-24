@section('headerform')
@include('components.layouts.headerform')
@endsection
 
@push('css')

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
    <style>
        .citys {
            width: 99%;
    height: 179px;
    position: absolute;
    background: #474747;
    padding: 0px;
    overflow: hidden;
    display: none;
    z-index: -1;
        }

        .opt {
            font-size:12px;
            margin-bottom: 0px;
            cursor: pointer;
            padding: 3px 8px;
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
    z-index: 1040 !important;
    opacity: 0.7 !important;
    background-color: rgba(0, 0, 0, 0.7) !important;
}

.modal {
    z-index: 1050 !important;
}

/* Advanced Search Modal Styling */
#search-more.modal {
    display: none;
    z-index: 1055 !important;
}

#search-more.modal.show,
#search-more.modal.in {
    display: block !important;
    z-index: 1055 !important;
}

#search-more .modal-dialog {
    z-index: 1056 !important;
    position: relative;
}

#search-more .modal-content {
    background-color: #2d2d2d !important;
    color: white !important;
    border: 1px solid #444;
    border-radius: 4px;
    z-index: 1057 !important;
    position: relative;
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
    z-index: 1 !important;
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

/* Pagination Styling */
.pagination {
    display: inline-flex;
    align-items: stretch;
    background-color: transparent;
    border-radius: 25px;
    padding: 0;
    margin: 20px 0;
    overflow: visible;
    gap: 0;
    border: 1px solid #4c4c4c;
        height: 55px;
}

.pagination li {
    list-style: none;
    margin: 0;
    padding: 0;
}

.pagination li.d-inline {
    display: inline-flex !important;
}

.pagination li a,
.pagination li span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    color: #f4b827;
    background-color: #3a3a3a;
    text-decoration: none;
    border: none;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 16px;
    white-space: nowrap;
    height: 100%;
}

/* Previous button - rounded left */
.pagination li:first-child a,
.pagination li:first-child span {
    border-radius: 8px 0 0 8px;
}

/* Next button - rounded right */
.pagination li:last-child a,
.pagination li:last-child span {
    border-radius: 0 24px 24px 0;
}

/* Middle section - no radius */
.pagination li.active span {
    background-color: #4a4a4a;
    color: #fff;
    border-left: 1px solid #555;
    border-right: 1px solid #555;
    font-weight: 600;
    min-width: 100px;
    border-radius: 0;
}

.pagination li.inactive span {
    color: #f4b827;
    cursor: not-allowed;
    opacity: 0.9;
    background-color: #2a2a2a;
}

.pagination li:not(.inactive):not(.active) a {
    transition: all 0.3s ease;
}

.pagination li:not(.inactive):not(.active):hover a {
    background-color: #4a4a4a !important;
}

.pagination li:not(.inactive):not(.active) a:hover {
    background-color: #4a4a4a !important;
}

.pagination li:not(.inactive):not(.active) a:hover span,
.pagination li:not(.inactive):not(.active) a:hover i {
    color: #f4b827 !important;
    background-color: #4a4a4a !important;
}

.pagination li a span,
.pagination li span span {
    background-color: inherit;
    transition: all 0.3s ease;
}

.pagination .fa-arrow-left,
.pagination .fa-arrow-right {
    color: #f4b827;
}

.pagination li.inactive .fa-arrow-left,
.pagination li.inactive .fa-arrow-right {
    color: #f4b827;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .pagination li a,
    .pagination li span {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .pagination .fa-lg {
        font-size: 1.1em;
    }
    
    .pagination li.active span {
        min-width: 80px;
    }

    .listing-li>.listing-info-wrapper>.listing-info {
        display: block;
    }
    
    /* Hide the title below/after images in mobile - keep only the arrow one above */
    .listing-li .listing-info-wrapper .listing-info h2 {
        display: none !important;
    }
}

/* Package-specific heights for listing-info */
/* Premium package (ID: 21) */
.listing-li.premium .listing-info-wrapper .listing-info {
    height: 225px !important;
    overflow: hidden;
}

/* Featured package (ID: 20) */
.listing-li.featured .listing-info-wrapper .listing-info {
    height: 180px !important;
    overflow: hidden;
}

/* Basic/Free package (ID: 19 or empty) */
.listing-li.basic .listing-info-wrapper .listing-info {
    height: 128px !important;
    overflow: hidden;
}

.pagination>li>a, .pagination>li>button, .pagination>li>span {
    line-height: 0px;
}

@media(max-width:768px){
    
     .listing-li .img-wrapper.basic img {
    width: 140px;
    height: 130px;
    object-fit: cover;
}

.listing-li.premium .listing-info-wrapper .listing-info {
    height: 125px !important;
    overflow: hidden;
}

/* Featured package (ID: 20) */
.listing-li.featured .listing-info-wrapper .listing-info {
    height: 120px !important;
    overflow: hidden;
}

/* Basic/Free package (ID: 19 or empty) */
.listing-li.basic .listing-info-wrapper .listing-info {
    height: 95px !important;
    overflow: hidden;
}


}


@media (max-width: 768px) {
    .listing-li .img-wrapper.premium img {
        width: 200px;
        height: 221px;
        object-fit: cover;
    }

    .listing-li .img-wrapper.mini img {
    width: 50px;
    height: 53px;
    object-fit: cover;
}
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

@media (min-width: 768px) {
    .activity-record.mini .img-wrapper.featured img, .listing-li .img-wrapper.featured img, .listings-grid .img-wrapper.featured img {
        width: 115px;
        height: 135px;
        object-fit: cover;
    }
}


.listing-li.premium .listing-info-wrapper .listing-info {
    height: auto !important;
}

@media (max-width: 768px) {
    .listing-li.featured .listing-info-wrapper .listing-info {
        height: auto !important;
        
    }
}

@media (max-width: 768px) {
    .listing-li.basic .listing-info-wrapper .listing-info {
        height: auto !important;
    }
}
     </style>

@endpush
    
<div class="">

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

<style>
.mobile-search-modal {
position: fixed;
top: 0;
left: 0;
right: 0;
bottom: 0;
width: 100%;
height: 100%;
background-color: rgba(0, 0, 0, 0.9);
z-index: 9999;
overflow-y: auto;
}

.mobile-search-modal .modal-content {
background-color: #222 !important;
color: white !important;
border: none;
border-radius: 0;
box-shadow: none;
margin: 0;
padding: 0;
min-height: 100%;
}

.mobile-search-modal .modal-header {
display: flex;
justify-content: space-between;
align-items: center;
padding: 15px;
border-bottom: 1px solid #444;
background-color: #222 !important;
}

.mobile-search-modal .modal-title {
color: white !important;
margin: 0;
}

.mobile-search-modal .close {
background: none;
border: none;
color: white !important;
font-size: 24px;
opacity: 0.8;
}

.mobile-search-modal .close:hover {
opacity: 1;
}

.mobile-search-modal .modal-body {
padding: 20px;
background-color: #222 !important;
}

.mobile-search-modal .form-control {
background-color: #333 !important;
color: white !important;
border: 1px solid #555 !important;
}

.mobile-search-modal label {
color: white !important;
}

.mobile-search-modal .form-check-label {
color: white !important;
}

.mobile-search-modal .dropdown-menu {
background-color: #333 !important;
border: 1px solid #555 !important;
color: white !important;
}

.mobile-search-modal .dropdown-item {
color: white !important;
}

.mobile-search-modal .dropdown-item:hover {
background-color: #444 !important;
color: white !important;
}

.mobile-search-modal .chosen-container-multi .chosen-choices {
background-color: #333 !important;
border: 1px solid #555 !important;
}

.mobile-search-modal .chosen-container .chosen-drop {
background: #333 !important;
border: 1px solid #555 !important;
}

.mobile-search-modal .chosen-container .chosen-results li {
color: white !important;
}

.mobile-search-modal .chosen-container .chosen-results li.highlighted {
background: #555 !important;
}

.mobile-search-modal .text-success {
color: #28a745 !important;
}

.mobile-search-modal .text-danger {
color: #dc3545 !important;
}

.city-item {
padding: 1px 1px;
cursor: pointer;
}

.city-item:hover {
background-color: #444 !important;
}

/* Force all elements inside modal to maintain styling */
.mobile-search-modal * {
color: white !important;
}

.mobile-search-modal input,
.mobile-search-modal select,
.mobile-search-modal textarea {
background-color: #333 !important;
color: white !important;
border: 1px solid #555 !important;
}

.mobile-search-modal .btn-primary {
background-color: #007bff !important;
border-color: #007bff !important;
}

.mobile-search-modal .btn-secondary {
background-color: #6c757d !important;
border-color: #6c757d !important;
}

/* Prevent body scrolling when modal is open */
body.modal-open {
overflow: hidden;
}

/* Add this to your existing CSS to ensure proper Select2 styling */
.mobile-search-modal .select2-container {
    z-index: 10000;
    width: 100% !important;
}

.mobile-search-modal .select2-container .select2-selection--single,
.mobile-search-modal .select2-container .select2-selection--multiple {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    color: white !important;
    height: auto;
    min-height: 38px;
    border-radius: 4px;
}

.mobile-search-modal .select2-container .select2-selection--single .select2-selection__rendered {
    color: white !important;
    line-height: 36px;
    padding-left: 12px;
}

.mobile-search-modal .select2-dropdown {
    background-color: #333 !important;
    border: 1px solid #555 !important;
    z-index: 10001;
}

.mobile-search-modal .select2-container .select2-results__option {
    color: white !important;
}

.mobile-search-modal .select2-container .select2-results__option--highlighted[aria-selected] {
    background-color: #555 !important;
}


.mobile-search-modal .select2-container .select2-search--inline .select2-search__field {
    color: white !important;
}

.mobile-search-modal .select2-search--dropdown .select2-search__field {
    background-color: #444 !important;
    color: white !important;
    border: 1px solid #666 !important;
}

/* Force styling for mobile Chosen */
.mobile-search-modal .chosen-container {
    width: 100% !important;
    display: block !important;
}





.mobile-search-modal .chosen-container .chosen-drop {
    background: #2c2c2c !important;
    color: #ffffff !important;
    border: 1px solid #444 !important;
    z-index: 10001 !important;
}

.mobile-search-modal .chosen-container .chosen-results li.highlighted {
    background: #444 !important;
    color: #ffffff !important;
}

.mobile-search-modal .chosen-container .chosen-results li {
    color: #ffffff !important;
}

/* Make sure the Chosen dropdown is visible */
.chosen-container.chosen-with-drop .chosen-drop {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    z-index: 10001 !important;
}

.mobile-search-modal .chosen-container {
    width: 100% !important;
}




.mobile-search-modal .chosen-container .chosen-drop {
    background: #2c2c2c !important;
    color: #ffffff !important;
    border: 1px solid #444 !important;
    z-index: 10001 !important;
}

.mobile-search-modal .chosen-container .chosen-results li.highlighted {
    background: #444 !important;
    color: #ffffff !important;
}

.mobile-search-modal .chosen-container .chosen-results li {
    color: #ffffff !important;
}

.mobile-search-modal .chosen-container .chosen-search input[type="text"] {
    background: #2c2c2c !important;
    color: #ffffff !important;
    border: 1px solid #444 !important;
}

.mobile-search-modal .chosen-container .search-choice {
    background: #444 !important;
    color: #ffffff !important;
    border: 1px solid #666 !important;
}

/* Fix for z-index issues */
.chosen-container.chosen-with-drop .chosen-drop {
    z-index: 10001 !important;
}

/* Services Input Dropdown Styling */
.services-input-wrapper {
    position: relative;
    width: 100%;
}

.desktop-services-input-wrapper {
    position: relative;
    width: 40%; /* Set to 40% instead of fixed 200px */
    max-width: 40%;
}

.services-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: #333;
    border: 1px solid #555;
    border-top: none;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 10001;
    max-height: 200px;
    overflow-y: auto;
    display: none; /* Initially hidden */
}

.desktop-services-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%; /* Match wrapper width exactly (40%) */
    background-color: #2c2c2c;
    border: 1px solid #444;
    border-top: none;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 10001;
    max-height: 180px;
    overflow-y: auto;
    display: none; /* Initially hidden */
}

/* Debug styling - temporary */
.services-dropdown.show {
    display: block !important;
    background-color: #333 !important;
}

.desktop-services-dropdown.show {
    display: block !important;
    background-color: #2c2c2c !important;
}

/* Input styling */
.mobile-services-input:hover,
.desktop-services-input:hover {
    border-color: #666 !important;
    background-color: #404040 !important;
}

.mobile-services-input:focus,
.desktop-services-input:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 5px rgba(0,123,255,0.3) !important;
    outline: none !important;
}

/* Make sure dropdowns are visible */
.services-dropdown[style*="display: block"],
.desktop-services-dropdown[style*="display: block"] {
    display: block !important;
}

.service-option {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    border-bottom: 1px solid #444;
    font-size: 12px;
}

.desktop-service-option {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid #404040;
    font-size: 12px; /* Set to 12px as requested */
    position: relative;
}

.service-option:hover {
    background-color: #404040;
}

.desktop-service-option:hover {
    background-color: #404040;
    color: #007bff;
}

.desktop-service-option.selected {
    background-color: #007bff;
    color: white;
}

.desktop-service-option.selected:hover {
    background-color: #0056b3;
}

.service-option:last-child,
.desktop-service-option:last-child {
    border-bottom: none;
}

.service-checkbox,
.desktop-service-checkbox {
    display: none !important; /* Hide checkboxes completely */
    margin-right: 8px;
    accent-color: #007bff;
}

.desktop-service-checkbox {
    margin-right: 6px;
    transform: scale(0.9);
}

.service-label,
.desktop-service-label {
    color: white;
    cursor: pointer;
    margin: 0;
    flex-grow: 1;
    user-select: none;
}

.desktop-service-label {
    font-size: 12px;
}

.service-option.selected,
.desktop-service-option.selected {
    background-color: #007bff;
}

/* Scrollbar styling - Black theme */
.services-dropdown::-webkit-scrollbar,
.desktop-services-dropdown::-webkit-scrollbar {
    width: 6px;
}

.services-dropdown::-webkit-scrollbar-track,
.desktop-services-dropdown::-webkit-scrollbar-track {
    background: #000000; /* Black track */
}

.services-dropdown::-webkit-scrollbar-thumb,
.desktop-services-dropdown::-webkit-scrollbar-thumb {
    background: #1a1a1a; /* Dark black thumb */
    border-radius: 3px;
}

.services-dropdown::-webkit-scrollbar-thumb:hover,
.desktop-services-dropdown::-webkit-scrollbar-thumb:hover {
    background: #333333; /* Lighter black on hover */
}


#header{margin-bottom:0px!important;}

.listings-spots.listing-spots--minimal .spot .auction-cover {
  z-index:1 !important;
}
</style>


@endif


 

  
  <div class="container-fluid">
    <div class="content-wrapper no-sidebar">
      <div id="content" class="mt-3">

    <div class="col-md-9 col-xs-12" style="padding:0px">
      <a class="page-title" href="/{{ $gender ?? 'female' }}-escorts-in-{{ $currentCity ? $currentCity->slug : 'dubai' }}">
        <h1>Escorts in {{ $currentCity ? ucfirst($currentCity->name) : 'Dubai' }}@if($currentCity && $currentCity->country), {{ $currentCity->country }}@endif</h1>
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
                    @if($auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                        <i class="fa fa-heart2"></i>
                        <span>{{ $auction->winnerProfile->reviews->count() }}</span>
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
                    @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                        <i class="fa fa-heart2"></i>
                        <span>{{ $auction->winnerProfile->reviews->count() }}</span>
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
                        @if($auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                            <i class="fa fa-heart2"></i>
                            <span>{{ $auction->winnerProfile->reviews->count() }}</span>
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
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                            <i class="fa fa-heart2"></i>
                            <span>{{ $auction->winnerProfile->reviews->count() }}</span>
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
                    <p class="d-none d-md-block">{{ str()->of($auction->winnerProfile->about)->limit(400) }}</p>
                    <p class="d-md-none">{{ str()->of($auction->winnerProfile->about)->limit(140) }}</p>
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
                <i class="fa fa-heart2"></i>
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
            
              @forelse($profile->multipleimgs as $imgs)
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
                  @if($profile->reviews->count() > 0)
                  <span class="badge" data-placement="top" data-toggle="tooltip" title="{{$profile->reviews->count()}} Reviews">
                  <i class="fa fa-heart2"></i>
                    <span>{{ $profile->reviews->count() }}</span>
                  </span>
                  @endif

                  {{-- <a href="#" wire:click.prevent="toggleFavorite({{ $profile->id }})" class="btn btn-sm {{ $this->checkIfFavorited($profile->id) ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fa {{ $this->checkIfFavorited($profile->id) ? 'fa-heart' : 'fa-heart-o' }}"></i>
                </a> --}}

                </a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <p class="d-none d-md-block">{{str()->of($profile->about)->limit(400)}}</p>
                <p class="d-md-none">{{str()->of($profile->about)->limit(140)}}</p>
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
                <p class="d-none d-md-block">{{str()->of($profile->about)->limit(250)}}</p>
                <p class="d-md-none">{{str()->of($profile->about)->limit(120)}}</p>
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
                <p class="d-none d-md-block">{{str()->of($profile->about)->limit(150)}}</p>
                <p class="d-md-none">{{str()->of($profile->about)->limit(80)}}</p>
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
                <p class="d-none d-md-block">{{str()->of($profile->about)->limit(150)}}</p>
                <p class="d-md-none">{{str()->of($profile->about)->limit(80)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>
        @endif

        @empty
        <div class="col-md-12 mb-2"><h2>No Escorts in {{$selectedcity}} yet</h2><p>Register today and we will send you updates with new listings in {{$selectedcity}}</p><p></p><div class="subscribe-btn-wrapper"><a class="btn btn-primary btn-lg btn-lg" data-btn-link="" href="/register"><i class="fa fa-newspaper"></i> Subscribe</a></div><p></p></div>
        @endforelse
        
        
    
      </div>

      {{ $profiles->links('vendor.livewire.custom') }}
      
<!-- SEO Content Section -->
@php
    $seoContent = $this->getSeoContent();
@endphp
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
            <i class="fa fa-newspaper"></i> Subscribe </a>
        </div>
        <h3>
          <a href="/female-escort-news-in-dubai">What&#39;s new?</a>
        </h3>
        @if($reviews->count() > 0)
        <ul class="activity-stream activity-records-mini">

      @foreach($reviews as $rev)
          <li>
            <div class="activity-record new-review mini">
              <div class="activity-row">
                <div class="headline h3">
                  <i class="fa fa-heart2"></i>New review for <a title="Sweet tanned GFE Big boobs - New in Town, Vietnamese escort in Dubai" href="/female-escorts-in-dubai/sweet-tanned-gfe-big-boobs-new-in-town">{{$rev->getuser->name ?? ''}} </a>
                </div>
                <div class="photo">
                  <a class=" pb-photo-link" href="/female-escorts-in-dubai/sweet-tanned-gfe-big-boobs-new-in-town">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="" class="img-responsive" height="60" src="{{webp_asset("userimages/".$rev->user_id."/".$rev->profile_id."/".$rev->getpic->image)}}" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
                <div class="activity-content">
                  <div class="review-description">
                    <p>{{$rev->review}} </p>
                  </div>
                </div>
              </div>
            </div>
          </li>
          @endforeach
        </ul>
        @endif
        <div class="padding-left">
          <a href="/female-escort-news-in-dubai">See more</a>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
</div>


  @push('js')
  <!-- Optimized JavaScript Loading -->
  <script src="{{ smart_asset('chosen/chosen.jquery.js')}}" defer></script>
  <script src="{{ smart_asset('chosen/docsupport/init.js')}}" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
  
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
                    cityAppend.innerHTML = '<div class="opt" style="color: #999;">No cities found</div>';
                    cityAppend.style.display = 'block';
                } else {
                    // Add each city to the results
                    data.forEach(function(city) {
                        console.log('Processing city:', city.name, 'Count:', city.profile_count);
                        const citySlug = city.name.toLowerCase().replace(/\s+/g, '-');
                        
                        // Get country code for flag
                        const countryCode = getCountryCode(city.country);
                        
                        const opt = document.createElement('div');
                        opt.className = 'opt';
                        opt.style.cursor = 'pointer';
                        opt.style.color = '#fff';
                        opt.style.padding = '1px 1spx';
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
            'Australia': 'AU'
        };
        
        return countryMap[countryName] || null;
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
  @endpush
