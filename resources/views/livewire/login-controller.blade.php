@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="{{url('/')}}" wire:navigate>
            <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Back</span></a>
        <div class="title">
            <h1><a href="/sign-in">Sign in</a></h1></div>
    </div>
</div>
@endsection

@section("registertype")
@include('components.layouts.registertype')
@endsection

<div>
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="alert alert-danger">
            {{ $errorMessage }}
        </div>
    @endif

    <div class="row container mt-3">
        <div class="col-sm-6 col-lg-5 col-lg-offset-1">
            <div class="auth-form sign-in-form">
                <h1 class="margin-bottom hidden-xs">Sign in</h1>
                <form class="simple_form" id="new_account" wire:submit="loginNow">

                 <!-- Google Login Button -->
                    <div class="form-group text-center">
                
                        <a href="{{ route('google.redirect') }}" class="btn btn-lg btn-xs-block" style="color: #e9ad1e;
    display: flex
;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 10px 20px;
    border: 2px solid white;">
                            <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                                <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.184L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9.003 18z" fill="#34A853"/>
                                <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.428 0 9.002 0 5.48 0 2.438 2.017.957 4.958L3.964 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                            </svg>
                            <span>Continue with Google</span>
                        </a>
                    </div>

                    <div class="divider"> <span style="margin-inline: 9px;font-size: 15px;color: #e9ad1e;">or sign in with email</span> </div>


                    <div class="form-group email optional account_email">
                        <label class="email optional control-label" for="account_email">Email</label>
                        <input wire:model="email" class="string email optional form-control input-lg validate form-control" placeholder="Email" type="email" id="account_email"/>
                        @error('email')
                            <span class="validation-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group password optional account_password">
                        <label class="password optional control-label" for="account_password">Password</label>
                        <input wire:model="password" class="password optional form-control input-lg validate" placeholder="Password" type="password" id="account_password"/>
                        @error('password')
                            <span class="validation-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group boolean optional account_remember_me">
                        <label class="boolean optional control-label col-form-offset checkbox" for="account_remember_me">
                            <input wire:model="remember" class="boolean optional" type="checkbox" id="account_remember_me"/>Remember me
                        </label>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary btn-lg btn-xs-block" type="submit" wire:loading.attr="disabled" wire:target="loginNow">
                            <span wire:loading.remove wire:target="loginNow">Sign in</span>
                            <span wire:loading wire:target="loginNow">
                                <i class="fa fa-spinner fa-spin"></i> Signing in...
                            </span>
                        </button>
                    </div>

                   

                </form>

                <div class="auth-other-links padding-bottom">
                    <div class="list-group list-group-dark">
                        <a class="list-group-item" id="registerpopup" href="#">Register</a>
                        <a class="list-group-item" href="{{url('forget-password')}}" wire:navigate>Forgot your password?</a>
                        <a class="list-group-item" href="/accounts/confirmation/new">Didn&#39;t receive confirmation email?</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-5">
            <div class="text-center" id="sign-in-right">
                <h2>Don&rsquo;t have an account yet? <br> Register now - it&rsquo;s free!</h2>
                <div class="modal-header">
                    <h2 class="modal-title">Register now</h2>
                </div>
                <div class="modal-body">
                    <div class="row" id="register-account-type-selection">
                        <div class="col-xs-6 text-center">
                            <div class="account-type" id="account-type-user">
                                <h1>User</h1>
                                <a href="/register"><span class="register-graphic user-graphic"></span></a>
                                <p>Keep updated on<br/> activity in your area!</p>
                                <a class="btn btn-lg btn-primary btn-xs-block" href="{{route('register')}}" wire:navigate>Register</a>
                            </div>
                        </div>
                        <div class="col-xs-6 text-center">
                            <div class="account-type border-left" id="account-type-escort">
                                <h1>Advertiser</h1>
                                <a href="/escort-sign-up"><span class="register-graphic escort-graphic"></span></a>
                                <p>Get listed<br/> for free today!</p>
                                <a class="btn btn-lg btn-primary btn-xs-block" href="{{route('escort.signup')}}" wire:navigate>Register</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>