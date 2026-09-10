<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Preloader disabled --}}
    {{-- <link rel="stylesheet" href="{{smart_asset('admin/assets/css/pace.css')}}"> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script> --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($setting?->favicon)
        <link rel="icon" type="image/png" sizes="16x16" href="{{ smart_asset($setting->favicon) }}">
    @endif
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Dashboard</title>
    
    <!-- Preload critical fonts -->
    <link rel="preload" href="{{asset('admin/assets/vendors/material-icons/MaterialIcons-Regular.woff2')}}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{asset('admin/assets/vendors/material-icons/MaterialIcons-Regular.woff')}}" as="font" type="font/woff" crossorigin>
    
    <!-- CSS -->
    <link href="{{asset('admin/assets/vendors/material-icons/material-icons.css')}}" rel="stylesheet" type="text/css">
    <link href="{{smart_asset('admin/assets/vendors/mono-social-icons/monosocialiconsfont.css')}}" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.4/sweetalert2.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.1.3/mediaelementplayer.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.7.0/css/perfect-scrollbar.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,600,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="{{smart_asset('admin/assets/vendors/weather-icons-master/weather-icons.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{smart_asset('admin/assets/vendors/weather-icons-master/weather-icons-wind.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.25/daterangepicker.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css" rel="stylesheet" type="text/css">
    <link href="{{smart_asset('admin/assets/css/style.css')}}" rel="stylesheet" type="text/css">
    <!-- Head Libs -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.2.0/css/all.min.css" integrity="sha512-6c4nX2tn5KbzeBJo9Ywpa0Gkt+mzCzJBrE1RB6fmpcsoN+b/w/euwIMuQKNyUoU/nToKN3a8SgNOtPrbW12fug==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@stack('css')
<style>
/* Fix sidebar rendering on page load */
.site-sidebar {
    transition: none !important;
    -webkit-transition: none !important;
}

.site-sidebar * {
    transition: none !important;
    -webkit-transition: none !important;
}

.sidebar-nav .nav li > a {
    opacity: 1 !important;
    visibility: visible !important;
}

.sidebar-dark .site-sidebar {
    background: #2c2c2c;
    border-color: rgba(255, 255, 255, 0.2);
    opacity: 1;
    visibility: visible;
}

.sidebar-dark .side-menu li:hover, .sidebar-dark .side-menu li.active {
    background: #C08D15;
}

/* Active Menu Highlighting */
.sidebar-nav .nav li.active > a {
background-color: transparent !important;
    color: #ffffff !important;
    border-radius: 4px;
    margin: 2px 6px 0px 0px;
    font-weight: 500;
    padding-left: 4px;
}

.sidebar-nav .nav li.active > a .material-icons {
    color: #ffffff !important;
}

.sidebar-nav .nav li.active > a:hover {
    background-color: transparent !important;
}

/* Menu item hover effect */
.sidebar-nav .nav li > a:hover {
    background-color: transparent;
    border-radius: 0px;
    margin: 2px 0px;
    transition: all 0.3s ease;
}

/* Menu item default styling */
.sidebar-nav .nav li > a {
    margin: 2px 0px;
    border-radius: 0px;
    transition: all 0.3s ease;
}

 .card-title {
    margin-bottom: 0.0rem;
}

.navbar-brand
 {
    height: 68px;
 }
 .header-light .navbar {
    height: 68px;
}
@media (min-width: 961px) {
    .sidebar-expand .content-wrapper, .sidebar-collapse .content-wrapper
     {
        padding-top: 4.225rem;
    }
}

@media (min-width: 961px) {
    .sidebar-collapse .site-sidebar {
        top: 4.225rem;
    }
}

@media (min-width: 961px) {
    .sidebar-expand .site-sidebar {
        height: calc(100vh - 3.625rem);
    }
}

.navbar-nav > li > a {
    line-height: 3.925rem;
}

.dropdown-card.dropdown-card-wide {
    width: 18rem;
}

.card-heading-extra {
     border-bottom: 0px solid #ddd; 
    padding-bottom: 0.72727em;
    margin-bottom: -0.27273em;
    text-transform: uppercase;
}

.header-light .navbar-nav .avatar .list-icon, .header-dark .navbar-nav .avatar .list-icon {
    color: #fff;
    display: none;
}

.header-light .navbar-nav .avatar::before, .header-dark .navbar-nav .avatar::before
 {
    display:none;
}

.navbar-brand {
    background: #2e2e2e;
    border-bottom: 1px solid #5b5b5b;
}
.sidebar-toggle::after {
    background: transparent;
}

 .page-item.active .page-link
 {

    background-color: #186dde;
    border-color: #186dde;
}
.page-link {

    color: #186dde;

}

.page-link:focus, .page-link:hover {
    color: #186dde;
}
.pagination {
    justify-content: end;
}

    .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th {
    vertical-align: middle;
    }
    
    .card-body {

    padding: .55rem;
}

.btn-primary {
    background-color: #7460ee;
    border-color: #7460ee;
}
.btn-primary:hover {
    color: #fff;
    background-color: #5b46e4ff;
    border-color: #5b46e4ff;
}

.btn {

    margin-right: 2px;
}

.color-color-scheme, .text-color-scheme.sidebar-nav .nav li > a:hover

 {
    color: white !important;
}

.sidebar-dark .side-menu li:hover, .sidebar-dark .side-menu li.active {
    color: white;
}
.sidebar-dark .side-menu li a {
    color: white;
}


.sidebar-dark .side-menu li:hover, .sidebar-dark .side-menu li.active {
    background: transparent;
}


.side-menu > li.current-page > a {
    border-color: #c8ff00;
}

.side-menu > li > a .badge, .side-menu > li > a .label {
    position: relative;
     top: 0px;
    }
    
    
 .form-check-input {
    margin-left: 0px;
}

img.logo-expand {
    width: 70%;
}

/* Fresh mobile menu — completely self-contained, no reliance on theme.js */
@media (max-width: 960px) {
    /* Hide theme's broken sidebar entirely on mobile */
    .site-sidebar { display: none !important; }
    /* Hide theme's broken hamburger to avoid confusion */
    .navbar .sidebar-toggle { display: none !important; }
}
#mm-btn {
    display: none;
    position: fixed;
    top: 12px;
    left: 12px;
    width: 44px;
    height: 44px;
    background: #2c2c2c;
    color: #fff;
    border: none;
    border-radius: 6px;
    z-index: 100000;
    cursor: pointer;
    font-size: 22px;
    line-height: 1;
    padding: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}
#mm-btn:focus { outline: none; }
#mm-panel {
    position: fixed;
    top: 0;
    left: 0;
    width: 85%;
    max-width: 320px;
    height: 100vh;
    background: #2c2c2c;
    color: #fff;
    z-index: 100002;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    transform: translateX(-100%);
    transition: transform 0.25s ease;
    box-shadow: 2px 0 12px rgba(0,0,0,0.4);
}
#mm-panel.mm-open { transform: translateX(0); }
#mm-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 100001;
}
#mm-backdrop.mm-open { display: block; }
#mm-panel .mm-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    background: #1f1f1f;
}
#mm-panel .mm-header .mm-title { font-size: 16px; font-weight: 600; }
#mm-panel .mm-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 26px;
    line-height: 1;
    cursor: pointer;
    padding: 4px 8px;
}
#mm-panel ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
#mm-panel a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: #fff;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    font-size: 14px;
}
#mm-panel a:hover, #mm-panel a.mm-active { background: #3a3a3a; }
#mm-panel .mm-icon { width: 22px; text-align: center; opacity: 0.8; }
#mm-panel .mm-badge {
    margin-left: auto;
    background: #dc3545;
    color: #fff;
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 10px;
}
/* Center the theme's caret vertically on expandable items */
#mm-panel li.menu-item-has-children > a {
    position: relative;
    padding-right: 40px;
}
#mm-panel li.menu-item-has-children > a::after,
#mm-panel li.menu-item-has-children > a::before {
    position: absolute !important;
    right: 16px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    margin: 0 !important;
    transition: transform 0.2s ease !important;
}
#mm-panel li.menu-item-has-children.mm-expanded > a::after,
#mm-panel li.menu-item-has-children.mm-expanded > a::before {
    transform: translateY(-50%) rotate(90deg) !important;
}
/* Neutralize theme's fly-out sub-menu positioning inside our panel */
#mm-panel .sub-menu {
    position: static !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    bottom: auto !important;
    width: 100% !important;
    max-width: none !important;
    min-width: 0 !important;
    height: auto !important;
    margin: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
    background: #232323 !important;
    overflow: visible !important;
    transform: none !important;
    display: none;
}
#mm-panel li.mm-expanded > .sub-menu { display: block !important; }
#mm-panel .sub-menu li { background: transparent !important; }
#mm-panel .sub-menu a { padding-left: 48px !important; font-size: 13px; }
@media (max-width: 960px) {
    #mm-btn { display: flex; align-items: center; justify-content: center; }
    body.mm-body-lock { overflow: hidden; }
}

/* Global mobile card spacing + footer breathing room */
@media (max-width: 768px) {
    .card { margin-bottom: 16px !important; }
    .card:last-child { margin-bottom: 24px; }
    .main-wrapper { padding-bottom: 32px !important; padding-top: 64px !important; }
    /* Reserve room so page-title + first content don't hide behind #mm-btn */
    .page-title { padding-left: 60px !important; }
    /* Theme footer is position:absolute bottom:0 with margin-left for sidebar —
       reset all of that on mobile so it flows below content with breathing room. */
    .footer {
        position: static !important;
        margin-left: 0 !important;
        margin-top: 24px !important;
        height: auto !important;
        line-height: 1.4 !important;
        padding: 16px 10px !important;
    }
}


.modal-content .close {
    top: 0.14286em !important;
    right: 0.14286em;
    }
</style>
</head>

<body class="header-light sidebar-dark sidebar-expand">
    <!-- Mobile menu (only visible below 961px) -->
    <button type="button" id="mm-btn" aria-label="Open menu">&#9776;</button>
    <div id="mm-backdrop"></div>
    <aside id="mm-panel" aria-hidden="true">
        <div class="mm-header">
            <span class="mm-title">Menu</span>
            <button type="button" class="mm-close" aria-label="Close menu">&times;</button>
        </div>
        @include('admin.layout.left')
    </aside>

    <div id="wrapper" class="wrapper">
        <!-- HEADER & TOP NAVIGATION -->
        @include('admin.layout.top')
    <!-- /.navbar -->
    <div class="content-wrapper">
        <!-- SIDEBAR -->
        <aside class="site-sidebar scrollbar-enabled clearfix">
            <!-- Sidebar Menu -->
            @include('admin.layout.left')
            <!-- /.sidebar-nav -->
        </aside>
        <!-- /.site-sidebar -->
        <main class="main-wrapper clearfix">

            @yield('content')
            <!-- /.widget-list -->
        </main>
        <!-- /.main-wrappper -->
        <!-- RIGHT SIDEBAR -->
        <aside class="right-sidebar scrollbar-enabled">
            <div class="sidebar-chat" data-plugin="chat-sidebar">
                <div class="sidebar-chat-info">
                    <h3>Chat</h3>
                    <p class="text-muted">You can chat with your family and friends in this space.</p>
                </div>
                <div class="chat-list">
                    <h6 class="sidebar-chat-subtitle">Online</h6>
                    <div class="list-group row">
                        <a href="javascript:void(0)" class="list-group-item user--online thumb-xs" data-chat-user="Julein Renvoye">
                            <img src="assets/demo/users/user1.jpg" class="rounded-circle" alt=""> <span class="name">Julien Renvoye</span>  <span class="username">@jrenvoye</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--online thumb-xs" data-chat-user="Eddie Lebanovkiy">
                            <img src="assets/demo/users/user2.jpg" class="rounded-circle" alt=""> <span class="name">Eddie Lebanovskiy</span>  <span class="username">@elebano</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--away thumb-xs" data-chat-user="Cameron Moll">
                            <img src="assets/demo/users/user3.jpg" class="rounded-circle" alt=""> <span class="name">Cameron Moll</span>  <span class="username">@cammoll</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--busy thumb-xs" data-chat-user="Bill S Kenny">
                            <img src="assets/demo/users/user7.jpg" class="rounded-circle" alt=""> <span class="name">Bill S Kenny</span>  <span class="username">@billsk</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--busy thumb-xs" data-chat-user="Trent Walton">
                            <img src="assets/demo/users/user6.jpg" class="rounded-circle" alt=""> <span class="name">Trent Walton</span>  <span class="username">@trentwalton</span>
                        </a>
                    </div>
                    <!-- /.list-group -->
                </div>
                <!-- /.chat-list -->
                <div class="chat-list">
                    <h6 class="sidebar-chat-subtitle">Offline</h6>
                    <div class="list-group row">
                        <a href="javascript:void(0)" class="list-group-item user--offline thumb-xs" data-chat-user="Julien Renvoye">
                            <img src="assets/demo/users/user1.jpg" class="rounded-circle" alt=""> <span class="name">Julien Renvoye</span>  <span class="username">@jrenvoye</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--offline thumb-xs" data-chat-user="Eddie Lebaovskiy">
                            <img src="assets/demo/users/user2.jpg" class="rounded-circle" alt=""> <span class="name">Eddie Lebanovskiy</span>  <span class="username">@elebano</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--offline thumb-xs" data-chat-user="Cameron Moll">
                            <img src="assets/demo/users/user3.jpg" class="rounded-circle" alt=""> <span class="name">Cameron Moll</span>  <span class="username">@cammoll</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--offline thumb-xs" data-chat-user="Bill S Kenny">
                            <img src="assets/demo/users/user7.jpg" class="rounded-circle" alt=""> <span class="name">Bill S Kenny</span>  <span class="username">@billsk</span> 
                        </a>
                        <a href="javascript:void(0)" class="list-group-item user--offline thumb-xs" data-chat-user="Trent Walton">
                            <img src="assets/demo/users/user6.jpg" class="rounded-circle" alt=""> <span class="name">Trent Walton</span>  <span class="username">@trentwalton</span>
                        </a>
                    </div>
                    <!-- /.list-group -->
                </div>
                <!-- /.chat-list -->
            </div>
            <!-- /.sidebar-chat -->
        </aside>
        <!-- CHAT PANEL -->
        <div class="chat-panel" hidden>
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <button type="button" class="minimize" aria-label="Minimize"><span aria-hidden="true"><i class="material-icons">expand_more</i></span>
                    </button> <span class="user-name">John Doe</span>
                </div>
                <!-- /.card-header -->
                <div class="card-block custom-scroll">
                    <div class="messages custom-scroll-content scrollbar-enabled">
                        <div class="current-user-message">
                            <img class="user-image" width="30" height="30" src="assets/demo/users/user1.jpg" alt="">
                            <div class="message">
                                <p>Lorem ipsum dolor sit amet?</p><small>10:00 am</small>
                            </div>
                            <!-- /.message -->
                        </div>
                        <!-- /.current-user-message -->
                        <div class="other-user-message">
                            <img class="user-image" width="30" height="30" src="assets/demo/users/user2.jpg" alt="">
                            <div class="message">
                                <p>Etiam rhoncus. Maecenas tempus, tellus eget condi mentum rhoncus</p><small>10:00 am</small>
                            </div>
                            <!-- /.message -->
                        </div>
                        <!-- /.other-user-message -->
                        <div class="current-user-message">
                            <img class="user-image" width="30" height="30" src="assets/demo/users/user1.jpg" alt="">
                            <div class="message">
                                <img src="assets/demo/chat-message.jpg" alt=""> <small>10:00 am</small>
                            </div>
                            <!-- .,message -->
                        </div>
                        <!-- /.current-user-message -->
                        <div class="current-user-message">
                            <img class="user-image" width="30" height="30" src="assets/demo/users/user1.jpg" alt="">
                            <div class="message">
                                <p>Maecenas nec odio et ante tincidunt tempus.</p><small>10:00 am</small>
                            </div>
                            <!-- .,message -->
                        </div>
                        <!-- /.current-user-message -->
                        <div class="other-user-message">
                            <img class="user-image" width="30" height="30" src="assets/demo/users/user2.jpg" alt="">
                            <div class="message">
                                <p>Donec sodales :)</p><small>10:00 am</small>
                            </div>
                            <!-- /.message -->
                        </div>
                        <!-- /.other-user-message -->
                    </div>
                    <!-- /.messages -->
                    <form action="javascript:void(0)" method="post">
                        <textarea name="message" style="resize: none" placeholder="Type message and hit enter"></textarea>
                        <ul class="list-unstyled list-inline chat-extra-buttons">
                            <li class="list-inline-item"><a href="javascript:void(0)"><i class="material-icons">insert_emoticon</i></a>
                            </li>
                            <li class="list-inline-item"><a href="javascript:void(0)"><i class="material-icons">attach_file</i></a>
                            </li>
                        </ul>
                        <button class="btn btn-color-scheme btn-circle submit-btn" type="submit"><i class="material-icons">send</i>
                        </button>
                    </form>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.chat-panel -->
    </div>
    <!-- /.content-wrapper -->
    <!-- FOOTER -->
    <footer class="footer text-center clearfix">2017 © Evoory.com</footer>
    </div>
    <!--/ #wrapper -->
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.2/umd/popper.min.js"></script>
    <script src="{{smart_asset('admin/assets/js/bootstrap.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.77/jquery.form-validator.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.1.3/mediaelementplayer.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/2.7.0/metisMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.7.0/js/perfect-scrollbar.jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.4/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>
    <script src="{{smart_asset('admin/assets/vendors/charts/utils.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
    <script src="{{smart_asset('admin/assets/vendors/charts/excanvas.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mithril/1.1.1/mithril.js"></script>
    <script src="{{smart_asset('admin/assets/vendors/theme-widgets/widgets.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clndr/1.4.7/clndr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.25/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js"></script>
    <script src="{{smart_asset('admin/assets/js/theme.js')}}"></script>
    <script src="{{smart_asset('admin/assets/js/custom.js')}}"></script>

    <script>
    // Force sidebar rendering fix on page load
    $(document).ready(function() {
        // Repaint only on desktop; on mobile this would override the theme's
        // display:none and pin the sidebar permanently open.
        if (window.innerWidth >= 961) {
            $('.site-sidebar').hide().show(0);
        }

        // Ensure all sidebar text is visible
        $('.sidebar-nav .nav li > a').css({
            'opacity': '1',
            'visibility': 'visible',
            'color': 'white'
        });

        $('.sidebar-nav .nav li > a .hide-menu').css({
            'opacity': '1',
            'visibility': 'visible',
            'display': 'inline-block'
        });

        // Force material icons to render
        $('.sidebar-nav .material-icons').css({
            'opacity': '1',
            'visibility': 'visible'
        });


        // Cache clear notifications
        @if(session('cache_success'))
        swal({
            title: 'Success!',
            text: '{{ session('cache_success') }}',
            type: 'success',
            confirmButtonColor: '#28a745'
        });
        @endif

        @if(session('cache_error'))
        swal({
            title: 'Error!',
            text: '{{ session('cache_error') }}',
            type: 'error',
            confirmButtonColor: '#dc3545'
        });
        @endif
    });
    </script>

    @stack('js')

    <script>
    (function() {
        function ready(fn) {
            if (document.readyState !== 'loading') fn();
            else document.addEventListener('DOMContentLoaded', fn);
        }

        ready(function() {
            var btn = document.getElementById('mm-btn');
            var panel = document.getElementById('mm-panel');
            var backdrop = document.getElementById('mm-backdrop');
            var closeBtn = panel ? panel.querySelector('.mm-close') : null;
            if (!btn || !panel || !backdrop) return;

            function open() {
                panel.classList.add('mm-open');
                backdrop.classList.add('mm-open');
                document.body.classList.add('mm-body-lock');
                panel.setAttribute('aria-hidden', 'false');
            }
            function close() {
                panel.classList.remove('mm-open');
                backdrop.classList.remove('mm-open');
                document.body.classList.remove('mm-body-lock');
                panel.setAttribute('aria-hidden', 'true');
            }
            function toggle() {
                if (panel.classList.contains('mm-open')) close(); else open();
            }

            btn.addEventListener('click', function(e) {
                e.preventDefault(); e.stopPropagation();
                toggle();
            });
            if (closeBtn) closeBtn.addEventListener('click', function(e) {
                e.preventDefault(); e.stopPropagation();
                close();
            });
            backdrop.addEventListener('click', close);

            // Convert sub-menus inside the panel to a simple expand/collapse.
            panel.querySelectorAll('li.menu-item-has-children').forEach(function(li) {
                li.classList.add('mm-has-sub');
                var link = li.querySelector(':scope > a');
                var sub = li.querySelector(':scope > ul');
                if (!link || !sub) return;
                sub.classList.add('mm-sub');
                if (li.classList.contains('current-page')) li.classList.add('mm-expanded');
                link.addEventListener('click', function(e) {
                    e.preventDefault(); e.stopPropagation();
                    li.classList.toggle('mm-expanded');
                });
            });

            // Close the panel when tapping a real navigation link.
            panel.querySelectorAll('a[href]').forEach(function(a) {
                var href = a.getAttribute('href') || '';
                if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
                a.addEventListener('click', function() { close(); });
            });

            // Close on Esc.
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') close();
            });
        });
    })();
    </script>
</body>

</html>