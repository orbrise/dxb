<div>
<div class="ev-auth-topbar hidden-xs-login">
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
        {{-- Mobile Logo & Welcome --}}
        <div class="ev-login-mobile-header">
            <a href="/" class="ev-login-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 100 100">
                    <circle cx="50" cy="30" r="8" fill="#E91E63"/>
                    <circle cx="70" cy="40" r="8" fill="#FF9800"/>
                    <circle cx="65" cy="62" r="8" fill="#4CAF50"/>
                    <circle cx="35" cy="62" r="8" fill="#2196F3"/>
                    <circle cx="30" cy="40" r="8" fill="#9C27B0"/>
                    <circle cx="50" cy="48" r="6" fill="#C1F11D"/>
                </svg>
                <span>evoory</span>
            </a>
            <h1 class="ev-login-welcome">Welcome Back</h1>
            <p class="ev-login-subtitle">Sign in to your account</p>
        </div>

        <div class="ev-login-card">
            {{-- Desktop title --}}
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
                    <div class="ev-input-wrapper">
                        <svg class="ev-input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input wire:model="email" class="ev-auth-input ev-auth-input-icon" placeholder="your@mail.com" type="email" id="account_email"/>
                    </div>
                    @error('email')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-form-group">
                    <label for="account_password">Password</label>
                    <div class="ev-input-wrapper">
                        <svg class="ev-input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input wire:model="password" class="ev-auth-input ev-auth-input-icon" placeholder="*****" type="password" id="account_password"/>
                        <button type="button" class="ev-eye-toggle" onclick="var i=document.getElementById('account_password');i.type=i.type==='password'?'text':'password'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-login-row">
                    <label class="ev-remember" for="account_remember_me">
                        <input wire:model="remember" type="checkbox" id="account_remember_me"/>
                        <span>Remember me</span>
                    </label>
                    <a class="ev-forgot" href="{{ url('forget-password') }}" wire:navigate>Forgot password?</a>
                </div>

                <div class="ev-login-actions">
                    <button class="ev-btn-signin" type="submit" wire:loading.attr="disabled" wire:target="loginNow">
                        <span class="ev-btn-signin-label" wire:loading.remove wire:target="loginNow">
                            <span>Sign in</span>
                        </span>
                        <span wire:loading wire:target="loginNow">
                            <svg class="ev-spinner" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                            Signing in...
                        </span>
                    </button>
                </div>

                <div class="ev-login-divider ev-login-or"><span>or</span></div>

                <p class="ev-signup-link">Don't have an account? <a href="{{ route('register') }}" wire:navigate>Sign Up</a></p>
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

.ev-login-page .alert{max-width:420px;margin:16px auto;padding:12px 16px;border-radius:5px;font-size:15px}
.ev-login-page .alert-success{background:rgba(193,241,29,.12);border:1px solid rgba(193,241,29,.3);color:#c1f11d}
.ev-login-page .alert-danger{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#f87171}
.validation-error{color:#f87171;font-size:13px;margin-top:4px;display:block}

.ev-login-page{max-width:1180px;margin:0 auto;padding:36px 16px 60px}
.ev-login-card{max-width:420px;margin:0 auto}
.ev-login-title{margin:0 0 18px;color:#fff;font-size:30px;font-weight:400}

.ev-google-btn{height:46px;border:1px solid #31384a;border-radius:5px;color:#d8deea;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;font-size:14px}
.ev-login-divider{margin:16px 0 14px;display:flex;align-items:center;color:#8f97a6;font-size:16px}
.ev-login-divider::before,.ev-login-divider::after{content:'';height:1px;background:#2a3241;flex:1}
.ev-login-divider span{padding:0 10px}

.ev-form-group{margin-bottom:14px}
.ev-form-group label{display:block;color:#dce1ec;font-size:16px;margin:0 0 6px;font-weight:400}
.ev-auth-input{width:100%;height:46px;border-radius:5px;border:1px solid #2e3646;background:#0e121a;color:#fff;padding:0 12px;font-size:18px;box-sizing:border-box}
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
.ev-btn-signin{height:34px;min-width:126px;border-radius:5px;border:none;background:#c1f11d;color:#111;font-size:20px;font-weight:500;padding:0 20px;cursor:pointer}
.ev-btn-signin-label{display:inline-flex;align-items:center;gap:2px}
.ev-btn-signin[disabled]{opacity:.7;cursor:not-allowed}
.ev-btn-register{height:34px;min-width:126px;border-radius:5px;border:1px solid #31384a;color:#b5df19;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-size:14px;padding:0 18px}

.ev-confirm-link{display:inline-block;margin-top:3rem;color:#b5df19;text-decoration:none;font-size:14px}

.ev-spinner{animation:ev-spin 1s linear infinite;vertical-align:middle}
@keyframes ev-spin{to{transform:rotate(360deg)}}

/* Input with icon */
.ev-input-wrapper{position:relative;display:flex;align-items:center}
.ev-input-icon{position:absolute;left:12px;color:#666;pointer-events:none;z-index:1}
.ev-auth-input-icon{padding-left:38px !important}
.ev-eye-toggle{position:absolute;right:12px;background:none;border:none;color:#666;cursor:pointer;padding:4px;display:flex}

/* Mobile header (hidden on desktop) */
.ev-login-mobile-header{display:none}

/* Signup link */
.ev-signup-link{text-align:center;color:#999;font-size:14px;margin-top:8px}
.ev-signup-link a{color:#C1F11D;text-decoration:none;font-weight:500}

/* "or" divider */
.ev-login-or{margin:20px 0 8px !important}

@media(max-width:768px){
  .hidden-xs-login{display:none !important}

  .ev-login-mobile-header{
    display:flex;
    flex-direction:column;
    align-items:center;
    padding:30px 0 20px;
  }
  .ev-login-logo{
    display:flex;
    align-items:center;
    gap:6px;
    text-decoration:none;
    margin-bottom:16px;
  }
  .ev-login-logo span{
    color:#C1F11D;
    font-size:24px;
    font-weight:600;
    font-style:italic;
  }
  .ev-login-welcome{
    color:#fff;
    font-size:22px;
    font-weight:500;
    margin:0 0 4px;
  }
  .ev-login-subtitle{
    color:#888;
    font-size:14px;
    margin:0;
  }

  .ev-login-page{padding:0 16px 40px}
  .ev-login-card{
    background:#111;
    border:1px solid #222;
    border-radius:5px;
    padding:24px 20px;
    max-width:100%;
  }
  .ev-login-title{display:none}

  .ev-google-btn{
    background:#1a1a1a;
    border-color:#333;
    border-radius:5px;
    height:44px;
    font-size:14px;
  }
  .ev-login-divider{font-size:13px;color:#666}
  .ev-login-divider::before,.ev-login-divider::after{background:#222}

  .ev-form-group label{font-size:14px;color:#ccc}
  .ev-auth-input{
    height:44px;
    border-radius:5px;
    border-color:#333;
    background:#0a0a0a;
    font-size:15px;
  }

  .ev-login-row{margin:12px 0 0}
  .ev-remember span{font-size:14px;color:#ccc}
  .ev-forgot{color:#C1F11D;font-size:13px}

  .ev-login-actions{margin-top:24px}
  .ev-btn-signin{
    width:100%;
    height:48px;
    border-radius:5px;
    font-size:16px;
    font-weight:600;
  }
  .ev-btn-register{display:none}

  .ev-signup-link{margin-top:0;font-size:14px}
  .ev-confirm-link{display:none}
}
</style>
@endpush