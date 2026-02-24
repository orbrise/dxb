
<div class="startbar d-print-none">
    <!--start brand-->
    <div class="brand">
        <a href="index.html" class="logo">
            <span>
                <img src="{{ smart_asset($setting->app_logo) }}" alt="logo-small" class="logo-sm">
            </span>
            <span class="">
                <img src="{{ smart_asset($setting->app_logo) }}" alt="logo-large" class="logo-lg logo-light">
                <img src="{{ smart_asset($setting->app_logo) }}" alt="logo-large" class="logo-lg logo-dark">
            </span>
        </a>
    </div>
    <!--end brand-->
    <!--start startbar-menu-->
    <div class="startbar-menu" >
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <!-- Navigation -->
                <ul class="navbar-nav mb-auto w-100">
                    <li class="menu-label mt-2">
                        <span>Navigation</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.dashboard')}}">
                            <i class="iconoir-report-columns menu-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </li><!--end nav-item-->
                             
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.auctions.index')}}">
                            <i class="iconoir-trophy menu-icon"></i>
                            <span>Auctions</span>
                        </a>
                    </li><!--end nav-item-->
                 
                    </li><!--end nav-item-->
                
                    <li class="menu-label mt-2">
                        <small class="label-border">
                            <div class="border_left hidden-xs"></div>
                            <div class="border_right"></div>
                        </small>
                        <span>Regional Settings</span>
                    </li>
                
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.cities')}}">
                            <i class="iconoir-city menu-icon"></i>
                            <span>Cities</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.countries')}}">
                            <i class="iconoir-globe menu-icon"></i>
                            <span>Countries</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.currencies')}}">
                            <i class="iconoir-cash menu-icon"></i>
                            <span>Currencies</span>
                        </a>
                    </li>

                    <li class="menu-label mt-2">
                        <small class="label-border">
                            <div class="border_left hidden-xs"></div>
                            <div class="border_right"></div>
                        </small>
                        <span>Profile Settings</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.profiles.index')}}">
                            <i class="iconoir-community menu-icon"></i>
                            <span>All Profiles</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.genders')}}">
                            <i class="iconoir-male menu-icon"></i>
                            <span>Gender</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.busts')}}">
                            <i class="iconoir-female menu-icon"></i>
                            <span>Busts</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.haircolors')}}">
                            <i class="iconoir-female menu-icon"></i>
                            <span>Hair Colors</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.ethnicities')}}">
                            <i class="iconoir-female menu-icon"></i>
                            <span>Ethnicities</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.languages')}}">
                            <i class="iconoir-female menu-icon"></i>
                            <span>Languages</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.orientations')}}">
                            <i class="iconoir-female menu-icon"></i>
                            <span>Orientation</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.listings.index')}}">
                            <i class="iconoir-keyframes-solid menu-icon"></i>
                            <span>Categories</span>
                        </a>
                    </li>



                    <li class="menu-label mt-2">
                        <small class="label-border">
                            <div class="border_left hidden-xs"></div>
                            <div class="border_right"></div>
                        </small>
                        <span>App Settings</span>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.appsetting')}}">
                            <i class="iconoir-settings menu-icon"></i>
                            <span>App Setup</span>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.users')}}">
                            <i class="iconoir-user menu-icon"></i>
                            <span> Users & Profile</span>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link" href="{{route('seo.index')}}">
                            <i class="iconoir-candlestick-chart menu-icon"></i>
                            <span>Seo keywords</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{url('admin/pages')}}">
                            <i class="iconoir-multiple-pages menu-icon"></i>
                            <span>Pages</span>
                        </a>
                    </li>



                    <li class="menu-label mt-2">
                        <small class="label-border">
                            <div class="border_left hidden-xs"></div>
                            <div class="border_right"></div>
                        </small>
                        <span>Users Ads</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('questions.index')}}">
                            <i class="iconoir-chat-bubble-question menu-icon"></i>
                            <span>Questions</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('reviews.index')}}">
                            <i class="iconoir-bookmark-solid menu-icon"></i>
                            <span>Reviews</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.packages')}}">
                            <i class="iconoir-box-iso menu-icon"></i>
                            <span>Packages</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.wallet')}}">
                            <i class="iconoir-wallet menu-icon"></i>
                            <span>Wallet</span>
                        </a>
                    </li>

                    
                    
                </ul><!--end navbar-nav--->
               
            </div>
        </div><!--end startbar-collapse-->
    </div><!--end startbar-menu-->    
</div><!--end startbar-->


