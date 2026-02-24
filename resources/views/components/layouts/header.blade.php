<style>
  /* Font Awesome icon fix */
  .fa, .fas, .far, .fab, .fal {
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    display: inline-block !important;
    font-style: normal !important;
    font-variant: normal !important;
    text-rendering: auto !important;
    line-height: 1;
  }

  /* Specific icon content for FA5 */
  .fa-user-circle:before {
    content: "\f2bd" !important;
  }

  /* Custom girl icon for profile button */
  .auth-button-group .btn-navbar-header:first-child i.fa-venus:before {
    content: "" !important;
    background-image: url('{{ asset("assets/images/girl-icon.png") }}') !important;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    display: inline-block !important;
    width: 18px;
    height: 18px;
    vertical-align: middle;
  }

  .fa-venus:before {
    content: "\f221" !important;
  }

  .fa-phone:before {
    content: "\f095" !important;
  }

  .fa-user:before {
    content: "\f007" !important;
  }

  .fa-sign-out-alt:before {
    content: "\f2f5" !important;
  }

  /* Ensure icons display in buttons */
  .btn-navbar-header i {
    display: inline-block !important;
    font-style: normal !important;
    margin-right: 5px;
  }

  /* Navbar layout - logo left, buttons right */
  .navbar-header {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    width: 100%;
  }
  
  .navbar-header .logo.navbar-brand {
    margin: 0;
    padding: 10px 0;
    margin-right: 10px;
  }
  
  #main-nav {
    margin: 0 !important;
  }
  
  .header-nav-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    margin-left: auto;
  }
  
  /* Hide main-nav on mobile */
  @media (max-width: 768px) {
    #main-nav {
      display: none !important;
    }

      .btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
    color: #fff;
    background-color: #d3980b;
    border-color: #d3980b;
}
.btn-primary.disabled, .btn-primary:disabled {
    color: #fff;
    background-color: #d3980b;
    border-color: #d3980b;
}

.btn-primary:not(:disabled):not(.disabled).active:focus, .btn-primary:not(:disabled):not(.disabled):active:focus, .show>.btn-primary.dropdown-toggle:focus {
    box-shadow: 0 0 0 .2rem #333;
}


.btn-primary.focus, .btn-primary:focus {
    box-shadow: 0 0 0 .2rem #333;
}


  }
  
  /* Add textured background to navbar */
  .navbar.navbar-inverse {
    background: black;
  }
  
  /* Collapsed button group for Profile, Account, Logout */
  .auth-button-group {
    display: flex;
    align-items: stretch;
  }
  
  .auth-button-group .btn-navbar-header,
  .auth-button-group .button_to {
    margin: 0 !important;
    border-radius: 0 !important;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .auth-button-group .btn-navbar-header:first-child {
    border-top-left-radius: 4px !important;
    border-bottom-left-radius: 4px !important;
  }
  
  .auth-button-group .button_to {
    display: inline-flex;
  }
  
  .auth-button-group .button_to .btn-navbar-header {
    border-radius: 0 !important;
    border-right: none;
    border-top-right-radius: 4px !important;
    border-bottom-right-radius: 4px !important;
  }
  
  .auth-button-group .btn-navbar-header + .btn-navbar-header {
    margin-left: -1px !important;
  }
  
  /* Separated language dropdown */
  .dropdown.dropdown--lang {
    margin: 0;
  }
  
  .dropdown--lang .btn-navbar-header {
    background-color: #474747 !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 4px !important;
  }
  
  .dropdown--lang .btn-navbar-header:hover {
    background-color: #474747 !important;
  }
  
  /* Separated Sign in button */
  .separated-btn {
    border-radius: 4px !important;
  }

  .btn-navbar-header {
    margin-top: 3px;
  }

  .header-nav-buttons .btn-navbar-header
 {
    border-radius: 3px !important;
    border-left-width: 1px !important;
    color: #fff;
}

.logo2 {
    margin: 5px 0;
    text-indent: -9999px;
    display: block;
    width: 165px;
    height: 55px;
    background: url(https://assets.massagerepublic.com.co/assets/images/dashboardlogo.svg) 0 0 no-repeat;
}


.btn-primary:focus, .btn-primary:hover, .my-listings-nav>.my-listing-new-link:focus, .my-listings-nav>.my-listing-new-link:hover
 {
    background-position: 0 0px;
}

</style>

<div class="navbar navbar-inverse" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
    
      @php
        $currentPath = request()->path();
        $showDashboardLogo = Auth::check() && (
          str_contains($currentPath, 'my-profile/') ||
          str_contains($currentPath, 'my-account') ||
          str_contains($currentPath, 'purchase-credits') ||
          str_contains($currentPath, 'action/listings/new')
        );
      @endphp
      
      @if($showDashboardLogo)
        <a class="logo2 navbar-brand" href="/">Dubai Escorts at Massage Republic</a>
      @else
        <a class="logo navbar-brand" href="/">Dubai Escorts at Massage Republic</a>
      @endif
      
      <div class="btn-group" id="main-nav">
        @php
          $citySlug = getFeaturedCitySlug();
          $currentRoute = request()->route()->getName();
          $currentPath = request()->path();
          $isNewsPage = in_array($currentRoute, ['news.all', 'news.page']);
          $isEscortsPage = $currentRoute === 'home';
          
          // Show buttons only on specific pages
          $showButtons = $isEscortsPage || 
                        $isNewsPage || 
                        str_contains($currentPath, 'female-escort-news-in-') && 
                        (str_contains($currentPath, '/new-escorts') || 
                         str_contains($currentPath, '/new-reviews') || 
                         str_contains($currentPath, '/new-questions'));
        @endphp
        @if($showButtons)
        <a class="btn btn-dark lead {{ $isEscortsPage ? 'selected' : '' }}" href="/female-escorts-in-{{ $citySlug }}" wire:navigate>Escorts</a>
        <a class="btn btn-dark lead {{ $isNewsPage ? 'selected' : '' }}" href="/female-escort-news-in-{{ $citySlug }}" wire:navigate>What's new</a>
        @endif
      </div>
      <div class="header-nav-buttons" >
        @if(Auth::check())
          <div class="auth-button-group">
            @if(Auth::user()->type != 1)
              <a style="border-top-right-radius: 0px !important;
    border-bottom-right-radius: 0px !important;" class="btn btn-navbar-header {{ request()->is('my-profile/*') || request()->is('new-profile') ? 'active' : '' }}" 
                 href="{{ auth()->user()->profiles->first() 
                   ? route('user.dashboard', ['name' => auth()->user()->profiles->first()->name, 'id' => auth()->user()->profiles->first()->id]) 
                   : route('new.profile') }}">
                <i class="fas fa-venus"></i> 
                <span class="sr-only-xs">My Profile</span>
              </a>
            @endif
            
            <a style="border-radius:0px !important;" class="btn btn-navbar-header {{ request()->is('my-account') || request()->is('my-account/*') ? 'active' : '' }}" 
               href="/my-account">
              <i class="fas fa-user"></i> 
              <span class="sr-only-xs">Account</span>
              <strong class="sr-only-xs"> ${{auth()->user()->wallet->balance ?? 0}}</strong>
            </a>
            
            <form class="button_to" method="post" action="{{url('sign_out')}}">
             {{csrf_field()}}
              <button style="" class="btn btn-navbar-header" type="submit">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sr-only-xs">Log out</span>
              </button>
            </form>
          </div>
        @else 
          <div class="header-nav-buttons--separated position-absolute d-flex align-items-center gap-2 gap-sm-3"><div class="dropdown dropdown--lang"><button  aria-expanded="false" aria-haspopup="true" class="btn btn-dark btn-navbar-header d-flex align-items-center px-2 px-sm-3" data-toggle="dropdown" id="langSwitch" type="button">EN<svg class="ml-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path fill="#FFFFFF" d="M480-100.001q-78.154 0-147.499-29.962-69.346-29.961-120.962-81.576-51.615-51.616-81.576-120.962Q100.001-401.846 100.001-480q0-78.769 29.962-147.807 29.961-69.038 81.576-120.654 51.616-51.615 120.962-81.576Q401.846-859.999 480-859.999q78.769 0 147.807 29.962 69.038 29.961 120.654 81.576 51.615 51.616 81.576 120.654Q859.999-558.769 859.999-480q0 78.154-29.962 147.499-29.961 69.346-81.576 120.962-51.616 51.615-120.654 81.576Q558.769-100.001 480-100.001Zm0-60.845q30.616-40.616 51.539-81.924 20.923-41.308 34.077-90.308H394.384q13.923 50.539 34.462 91.847 20.538 41.308 51.154 80.385Zm-77.46-11q-23-33-41.308-75.039t-28.462-86.193H197.076q31.693 62.309 85.001 104.694 53.309 42.385 120.463 56.538Zm154.92 0q67.154-14.153 120.463-56.538 53.308-42.385 85.001-104.694H627.23q-12.077 44.539-30.385 86.578t-39.385 74.654Zm-385.537-221.23h148.693q-3.769-22.308-5.461-43.731-1.692-21.424-1.692-43.193t1.692-43.193q1.692-21.423 5.461-43.731H171.923q-5.769 20.385-8.846 42.385Q160-502.539 160-480t3.077 44.539q3.077 22 8.846 42.385Zm208.692 0h198.77q3.769-22.308 5.462-43.347 1.692-21.038 1.692-43.577 0-22.539-1.692-43.577-1.693-21.039-5.462-43.347h-198.77q-3.769 22.308-5.462 43.347-1.692 21.038-1.692 43.577 0 22.539 1.692 43.577 1.693 21.039 5.462 43.347Zm258.769 0h148.693q5.769-20.385 8.846-42.385Q800-457.461 800-480t-3.077-44.539q-3.077-22-8.846-42.385H639.384q3.769 22.308 5.461 43.731 1.692 21.424 1.692 43.193t-1.692 43.193q-1.692 21.423-5.461 43.731ZM627.23-626.922h135.694Q730.846-690 678.5-731.616q-52.347-41.615-121.04-56.923 23 34.923 40.923 76.385 17.924 41.462 28.847 85.232Zm-232.846 0h171.232q-13.923-50.154-35.039-92.424-21.115-42.269-50.577-79.808-29.462 37.539-50.577 79.808-21.116 42.27-35.039 92.424Zm-197.308 0H332.77q10.923-43.77 28.847-85.232 17.923-41.462 40.923-76.385-69.077 15.308-121.232 57.116-52.154 41.808-84.232 104.501Z"></path></svg></button><ul aria-labelledby="langSwitch" class="dropdown-menu nav nav-dark w-100"><li><a href="https://massagerepublic.com/es/female-escorts-in-cairo?f=1" rel="alternate" hreflang="es">ES</a></li></ul></div><a class="btn btn-navbar-header" href="/sign-in"><i class="fa fa-user"></i> <span class="sr-only-xs">Sign in</span></a></div>
          @endif
        </div>
      </div>

      
    </div>
  </div>
</div>