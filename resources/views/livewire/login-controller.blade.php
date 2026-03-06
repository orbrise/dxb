<div>
<div class="ev-auth-topbar">
    <div class="ev-auth-topbar-inner">
        <a class="ev-auth-back-link" href="{{ url('/') }}" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span>Back</span>
        </a>
        <div class="ev-auth-topbar-title">Sign in</div>
    </div>
</div>
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

    <div class="ev-login-page">
        <div class="ev-login-card">
            <h1 class="ev-login-title">Sign in</h1>

            <form class="simple_form" id="new_account" wire:submit="loginNow">
                <a href="{{ route('google.redirect') }}" class="ev-google-btn">
                    <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                        <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.184L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9.003 18z" fill="#34A853"/>
                        <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                        <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.428 0 9.002 0 5.48 0 2.438 2.017.957 4.958L3.964 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <div class="ev-login-divider"><span>or sign in with email</span></div>

                <div class="ev-form-group">
                    <label for="account_email">Email</label>
                    <input wire:model="email" class="ev-auth-input" placeholder="Email" type="email" id="account_email"/>
                    @error('email')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-form-group">
                    <label for="account_password">Password</label>
                    <input wire:model="password" class="ev-auth-input" placeholder="Password" type="password" id="account_password"/>
                    @error('password')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-login-row">
                    <label class="ev-remember" for="account_remember_me">
                        <input wire:model="remember" type="checkbox" id="account_remember_me"/>
                        <span>Remember me</span>
                    </label>
                    <a class="ev-forgot" href="{{ url('forget-password') }}" wire:navigate>Forgot your password?</a>
                </div>

                <div class="ev-login-actions">
                    <button class="ev-btn-signin" type="submit" wire:loading.attr="disabled" wire:target="loginNow">
                        <span class="ev-btn-signin-label" wire:loading.remove wire:target="loginNow">
                            <span>Sign in</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </span>
                        <span wire:loading wire:target="loginNow">
                            <svg class="ev-spinner" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                            Signing in...
                        </span>
                    </button>
                    <a class="ev-btn-register" href="{{ route('register') }}" wire:navigate>Register</a>
                </div>

                <a class="ev-confirm-link" href="/accounts/confirmation/new">Didn&#39;t receive confirmation email?</a>
            </form>
        </div>
    </div>
</div>

@push('css')
<style>
.ev-auth-topbar{background:#12191c;border-top:1px solid #1f262f;border-bottom:1px solid #1f262f}
.ev-auth-topbar-inner{max-width:1180px;margin:0 auto;padding:10px 16px;display:flex;align-items:center;justify-content:center;position:relative}
.ev-auth-back-link{display:inline-flex;align-items:center;gap:4px;color:#b5df19;text-decoration:none;font-size:15px;position:absolute;left:16px}
.ev-auth-topbar-title{color:#d6dbe6;font-size:22px;font-weight:400;line-height:1}

.ev-login-page .alert{max-width:420px;margin:16px auto;padding:12px 16px;border-radius:8px;font-size:15px}
.ev-login-page .alert-success{background:rgba(193,241,29,.12);border:1px solid rgba(193,241,29,.3);color:#c1f11d}
.ev-login-page .alert-danger{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#f87171}
.validation-error{color:#f87171;font-size:13px;margin-top:4px;display:block}

.ev-login-page{max-width:1180px;margin:0 auto;padding:36px 16px 60px}
.ev-login-card{max-width:420px;margin:0 auto}
.ev-login-title{margin:0 0 18px;color:#fff;font-size:30px;font-weight:400}

.ev-google-btn{height:46px;border:1px solid #31384a;border-radius:8px;color:#d8deea;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;font-size:14px}
.ev-login-divider{margin:16px 0 14px;display:flex;align-items:center;color:#8f97a6;font-size:16px}
.ev-login-divider::before,.ev-login-divider::after{content:'';height:1px;background:#2a3241;flex:1}
.ev-login-divider span{padding:0 10px}

.ev-form-group{margin-bottom:14px}
.ev-form-group label{display:block;color:#dce1ec;font-size:16px;margin:0 0 6px;font-weight:400}
.ev-auth-input{width:100%;height:46px;border-radius:8px;border:1px solid #2e3646;background:#0e121a;color:#fff;padding:0 12px;font-size:18px}
.ev-auth-input:focus{outline:none;border-color:#3d475d}

.ev-login-row{display:flex;align-items:center;justify-content:space-between;margin:10px 0 18px;gap:12px}
.ev-remember{display:inline-flex;align-items:center;gap:8px;color:#dce1ec;font-size:16px;font-weight:400;margin:0}
.ev-remember input{
    width:16px;
    height:16px;
    appearance:none;
    -webkit-appearance:none;
    border:1px solid #3a4250;
    background:#000;
    border-radius:3px;
    cursor:pointer;
    display:inline-block;
    position:relative;
}
.ev-remember input:checked{
    border-color:#b5df19;
    background:#0f1400;
}
.ev-remember input:checked::after{
    content:'';
    position:absolute;
    left:4px;
    top:1px;
    width:4px;
    height:8px;
    border:solid #b5df19;
    border-width:0 2px 2px 0;
    transform:rotate(45deg);
}
.ev-forgot{color:#b5df19;text-decoration:none;font-size:14px}

.ev-login-actions{display:flex;gap:12px;align-items:center;margin-top:2rem}
.ev-btn-signin{height:34px;min-width:126px;border-radius:999px;border:none;background:#c1f11d;color:#111;font-size:20px;font-weight:500;padding:0 20px;cursor:pointer}
.ev-btn-signin-label{display:inline-flex;align-items:center;gap:2px}
.ev-btn-signin[disabled]{opacity:.7;cursor:not-allowed}
.ev-btn-register{height:34px;min-width:126px;border-radius:999px;border:1px solid #31384a;color:#b5df19;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-size:14px;padding:0 18px}

.ev-confirm-link{display:inline-block;margin-top:3rem;color:#b5df19;text-decoration:none;font-size:14px}

.ev-spinner{animation:ev-spin 1s linear infinite;vertical-align:middle}
@keyframes ev-spin{to{transform:rotate(360deg)}}

@media(max-width:768px){
  .ev-auth-topbar-title{font-size:26px}
  .ev-login-title{font-size:38px}
  .ev-btn-signin{font-size:26px}
  .ev-btn-register{font-size:20px}
}
</style>
@endpush