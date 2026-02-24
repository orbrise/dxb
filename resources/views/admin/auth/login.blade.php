<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ smart_asset($setting->favicon) }}">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Login</title>
    
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
    <style>
        .form-material .form-group > label
 {
    position: absolute;
    top: 50%;
    left: 8px;
 }
 
  .btn-color-scheme {
    color: #fff;
    background-color: #d3980b;
    border-color: #d3980b;
}


 .btn-color-scheme:hover {
    color: #fff;
    background-color: #d3980b;
    border-color: #d3980b;
}

.profile-page .login-center
 {
    border: 1px solid;
 }
    
    </style>
</head>

<body class="body-bg-full profile-page" style="background-color: #333">
    <div id="wrapper" class="row wrapper">
        <div class="col-10 ml-sm-auto col-sm-6 col-md-4 ml-md-auto login-center mx-auto" style="background-color: #302f2f">
            <div class="navbar-header text-center">
                <a href="index.html">
                    <img alt="" src="{{smart_asset($setting->app_logo)}}">
                </a>
            </div>
            <!-- /.navbar-header -->
            <form class="form-material mt-4" action="{{route('admin.loginpost')}}"   method="post">
                {{ csrf_field() }}
                <div class="form-group">
                     <p style="margin-bottom: 0px;
    color: #b1b1b1;">Email</p>
                    <input type="email" placeholder="johndoe@site.com" class="form-control form-control-line mt-2" name="email" id="example-email" style="background: white;
    border-radius: 6px;padding-inline: 10px;">
                   
                </div>
                <div class="form-group">
                    <p style="margin-bottom: 0px;
    color: #b1b1b1;">Password</p>
                    <input type="password" placeholder="password" class="form-control form-control-line mt-2" name="password" style="background: white;
    border-radius: 6px;padding-inline: 10px;">
                   
                </div>
               
                <div class="form-group no-gutters mb-3 mt-2">
                    <div class="col-md-12 d-flex">
                        <div class="checkbox checkbox-info mr-auto">
                            <label class="d-flex">
                                <input type="checkbox"> <span class="label-text">Remember me</span>
                            </label>
                        </div><a href="javascript:void(0)" id="to-recover" class="my-auto pb-2 text-right" style="color: #d3980b;"><i class="fa fa-lock mr-1"></i>Forgot Password?</a>
                    </div>
                    <!-- /.col-md-12 -->
                </div>
                
                 <div class="form-group">
                    <button class="btn btn-block btn-lg btn-color-scheme ripple" type="submit" style="color:#333">Sign in <i class="list-icon material-icons">keyboard_arrow_right</i></button>
                </div>
                
                <!-- /.form-group -->
            </form>
            <!-- /.form-material -->
            
            <!-- /.btn-list -->
          
        </div>
        <!-- /.login-center -->
    </div>
    <!-- /.body-container -->
    <!-- Scripts -->
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 
</body>

</html>
