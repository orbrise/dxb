    <style>
    .dropdown-card-dark .card {
    background: #f8f8f8;
}

.dropdown-list-group {
    height: auto;
}
    </style>
    <nav class="navbar">
            <!-- Logo Area -->
            <div class="navbar-header">
                <a href="/" class="navbar-brand">
                    @if($setting->app_logo)
                    <img class="logo-expand" alt="" src="{{smart_asset($setting->app_logo)}}">
                    @endif
                    @if($setting->collapse_icon)
                    <img class="logo-collapse" alt="" src="{{smart_asset($setting->collapse_icon)}}">
                    @endif
                    <!-- <p>OSCAR</p> -->
                </a>
            </div>
            <!-- /.navbar-header -->
            <!-- Left Menu & Sidebar Toggle -->
            <ul class="nav navbar-nav">
                <li class="sidebar-toggle"><a href="javascript:void(0)" class="ripple"><i class="material-icons list-icon">menu</i></a>
                </li>
            </ul>
            <!-- /.navbar-left -->
            <!-- Search Form -->
            {{-- <form class="navbar-search d-none d-sm-block" role="search"><i class="material-icons list-icon">search</i> 
                <input type="text" class="search-query" placeholder="Search anything..."> <a href="javascript:void(0);" class="remove-focus"><i class="material-icons">clear</i></a>
            </form> --}}
            <!-- /.navbar-search -->
            <div class="spacer"></div>
            <!-- Button: Create New -->
            {{-- <div class="btn-list dropdown d-none d-md-flex"><a href="javascript:void(0);" class="btn btn-primary dropdown-toggle ripple" data-toggle="dropdown"><i class="material-icons list-icon fs-24">playlist_add</i> Create New</a>
                <div class="dropdown-menu dropdown-left animated flipInY"><span class="dropdown-header">Create new ...</span>  <a class="dropdown-item" href="#">Projects</a>  <a class="dropdown-item" href="#">User Profile</a>  <a class="dropdown-item" href="#"><span class="badge badge-pill badge-primary float-right">7</span> To-do Item</a> 
                    <a
                    class="dropdown-item" href="#"><span class="badge badge-pill badge-color-scheme float-right">23</span> Mail</a>
                        <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="material-icons list-icon icon-muted pull-right">settings</i> <strong>Settings</strong></a>
                </div>
            </div> --}}
            <!-- /.btn-list -->
            <!-- Right Menu -->
            {{-- <ul class="nav navbar-nav d-none d-lg-block">
                <li class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle ripple" data-toggle="dropdown"><i class="material-icons list-icon">mail_outline</i> <span class="badge badge-border bg-primary">3</span></a>
                    <div class="dropdown-menu dropdown-left dropdown-card dropdown-card-dark animated flipInY">
                        <div class="card">
                            <header class="card-header">New messages <span class="mr-l-10 badge badge-border badge-border-inverted bg-primary">3</span>
                            </header>
                            <ul class="list-unstyled dropdown-list-group">
                                <li><a href="#" class="media"><span class="d-flex user--online thumb-xs"><img src="assets/demo/users/user3.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Steve Smith</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">I slowly updated my Behance with some recent projects ...</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex user--offline thumb-xs"><img src="assets/demo/users/user6.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Emily Lee</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">Hi John!</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex user--online thumb-xs"><img src="assets/demo/users/user2.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Christopher Palmer</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">Like the illustration and the indicator style. Best of luck ...</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex user--online thumb-xs"><img src="assets/demo/users/user3.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Steve Smith</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">I slowly updated my Behance with some recent projects ...</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex user--offline thumb-xs"><img src="assets/demo/users/user6.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Emily Lee</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">Hi John!</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex user--offline thumb-xs"><img src="assets/demo/users/user2.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Christopher Palmer</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">Like the illustration and the indicator style. Best of luck ...</span></span></a>
                                </li>
                            </ul>
                            <!-- /.dropdown-list-group -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.dropdown-menu -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown"><a href="#" class="dropdown-toggle ripple" data-toggle="dropdown"><i class="material-icons list-icon">event_available</i> <span class="badge badge-border bg-primary">9</span></a>
                    <div class="dropdown-menu dropdown-left dropdown-card dropdown-card-dark animated flipInY">
                        <div class="card">
                            <header class="card-header">New notifications <span class="mr-l-10 badge badge-border badge-border-inverted bg-primary">4</span>
                            </header>
                            <ul class="list-unstyled dropdown-list-group">
                                <li><a href="#" class="media"><span class="d-flex"><i class="material-icons list-icon">check</i> </span><span class="media-body"><span class="media-heading">Invitation accepted</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">Your invitation for Mars has been accepted ...</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex user--online thumb-xs"><img src="assets/demo/users/user3.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Steve Smith</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">I slowly updated my Behance with some recent projects ...</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex"><i class="material-icons list-icon">event_available</i> </span><span class="media-body"><span class="media-heading">To Do</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">Meeting with Nathan McCullum on Friday 8 AM ...</span></span></a>
                                </li>
                                <li><a href="#" class="media"><span class="d-flex"><i class="material-icons list-icon">check</i> </span><span class="media-body"><span class="media-heading">Invitation accepted</span> <small>10.14.2016 @ 2:30pm</small> <span class="media-content">Your invitation for Mars has been accepted ...</span></span></a>
                                </li>
                            </ul>
                            <!-- /.dropdown-list-group -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.dropdown-menu -->
                </li>
                <!-- /.dropdown -->
                <li><a href="javascript:void(0);" class="right-sidebar-toggle ripple"><i class="material-icons list-icon">comment</i></a>
                </li>
            </ul> --}}
            <!-- /.navbar-right -->
            <!-- User Image with Dropdown -->
            <ul class="nav navbar-nav">
                <li class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle ripple" data-toggle="dropdown" ><span class="avatar thumb-sm"><img src="assets/demo/users/user-image.png" class="rounded-circle" alt=""> <i class="material-icons list-icon">expand_more</i></span></a>
                    <div
                    class="dropdown-menu dropdown-left dropdown-card dropdown-card-wide dropdown-card-dark text-inverse" style="padding:0px;     border: 1px solid #ddd;">
                        <div class="card">
                            <header class="card-heading-extra" style="overflow: hidden;">
                                <div class="row">
                                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                                 
                                  <div class="" style="background: #7460ee; width: 100%;     padding: 7px 25px;">
                                       <a href="javascript:void(0);" class="dropdown-toggle ripple" data-toggle="dropdown"><span class="avatar thumb-sm"><img src="assets/demo/users/user-image.png" class="rounded-circle" alt=""> <span style="font-size: 17px;margin-left: 10px;display: inline-block;    vertical-align: middle;line-height: 20px;color: white;">Admin  <br><span style="font-size: 13px;margin-left: 10px;">{{auth()->user()->name}}</span></span></span></a>
                                   </div>
                                   
                                    <!-- /.col-4 -->
                                </div>
                                <!-- /.row -->
                            </header>
                            
                             <ul class="list-unstyled dropdown-list-group mt-3">
                                <li style="    padding: 8px;    padding-bottom: 20px;">
                                               <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#" class="mr-t-10">
                          
                            <i class="fa-solid fa-right-from-bracket" style="color: red;
    font-size: 19px;
    margin-right: 10px;"></i> <span style="color:black; font-size:18px">Sign Out</span></a>
                            
                                </li>

                            </ul> 
                        </div>
    </div>
    </li>
    </ul>
    <!-- /.navbar-right -->
    </nav>