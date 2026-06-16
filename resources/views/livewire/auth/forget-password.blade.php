<div>
<div class="ev-auth-topbar hidden-xs-login">
    <div class="ev-auth-topbar-inner">
        <a class="ev-auth-back-link" href="{{ url('/') }}" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span>Back</span>
        </a>
        <div class="ev-auth-topbar-title">Forgot password</div>
    </div>
</div>

    <div class="ev-login-page">
        @if (session()->has('success'))
            <div class="ev-flash ev-flash-success">
                <svg class="ev-flash-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="ev-flash ev-flash-error">
                <svg class="ev-flash-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        {{-- Mobile Logo & Welcome --}}
        <div class="ev-login-mobile-header">
            <a href="/" class="ev-login-logo">
                @if(isset($setting) && $setting->app_logo)
                <img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}" style="height:36px;width:auto;">
                @else
                <span>{{ $setting->app_name ?? 'evoory' }}</span>
                @endif
            </a>
            <h1 class="ev-login-welcome">Forgot your password?</h1>
            <p class="ev-login-subtitle">We'll email you a reset link</p>
        </div>

        <div class="ev-login-card">
            {{-- Desktop title --}}
            <h1 class="ev-login-title">Forgot your password?</h1>
            <p class="ev-forget-lead">Enter the email associated with your account and we'll send you a link to reset your password.</p>

            <form class="simple_form" id="new_account" wire:submit.prevent="forgetPassword">
                <div class="ev-form-group">
                    <label for="account_email">Email</label>
                    <div class="ev-input-wrapper">
                        <svg class="ev-input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input wire:model="email" class="ev-auth-input ev-auth-input-icon" placeholder="your@mail.com" type="email" id="account_email" required/>
                    </div>
                    @error('email')
                        <span class="validation-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ev-login-actions">
                    <button class="ev-btn-signin" type="submit" wire:loading.attr="disabled" wire:target="forgetPassword">
                        <span wire:loading.remove wire:target="forgetPassword">Send</span>
                        <span wire:loading wire:target="forgetPassword">
                            <svg class="ev-spinner" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                            Sending...
                        </span>
                    </button>
                </div>

                <div class="ev-login-divider ev-login-or"><span>or</span></div>

                <p class="ev-signup-link">Remembered it? <a href="{{ url('sign-in') }}" wire:navigate>Sign in</a></p>
                <p class="ev-signup-link">Don't have an account? <a href="{{ route('register') }}" wire:navigate>Register</a></p>
            </form>
        </div>
    </div>
</div>

@push('css')
<style>
@media (max-width: 768px){
  body:has(.ev-login-page) .ev-header{display:none!important}
  .ev-auth-topbar{background:transparent!important;border:0!important;padding-top:18px}
  .ev-auth-topbar-inner{justify-content:flex-start!important;padding:4px 16px!important}
  .ev-auth-back-link{position:static!important;left:auto!important;font-size:18px!important}
  .ev-auth-topbar-title{display:none!important}
}
.ev-auth-topbar{background:#12191c;border-top:1px solid #1f262f;border-bottom:1px solid #1f262f}
.ev-auth-topbar-inner{max-width:1300px;margin:0 auto;padding:10px 16px;display:flex;align-items:center;justify-content:center;position:relative}
.ev-auth-back-link{display:inline-flex;align-items:center;gap:4px;color:#b5df19;text-decoration:none;font-size:15px;position:absolute;left:16px}
.ev-auth-topbar-title{color:#d6dbe6;font-size:22px;font-weight:400;line-height:1}

.ev-flash{max-width:420px;margin:0 auto 18px;padding:14px 18px;border-radius:8px;font-size:15px;display:flex;align-items:center;gap:10px;text-align:left;line-height:1.4}
.ev-flash-icon{flex-shrink:0}
.ev-flash-success{background:rgba(193,241,29,.1);border:1px solid rgba(193,241,29,.35);color:#c1f11d;box-shadow:0 4px 18px rgba(193,241,29,.08)}
.ev-flash-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.35);color:#f87171;box-shadow:0 4px 18px rgba(239,68,68,.08)}
.validation-error{color:#f87171;font-size:13px;margin-top:4px;display:block}

.ev-login-page{max-width:1180px;margin:0 auto;padding:36px 16px 60px}
.ev-login-card{max-width:420px;margin:0 auto}
.ev-login-title{margin:0 0 10px;color:#fff;font-size:30px;font-weight:400}
.ev-forget-lead{color:#8f97a6;font-size:14px;margin:0 0 22px;line-height:1.5}

.ev-login-divider{margin:16px 0 14px;display:flex;align-items:center;color:#8f97a6;font-size:16px}
.ev-login-divider::before,.ev-login-divider::after{content:'';height:1px;background:#2a3241;flex:1}
.ev-login-divider span{padding:0 10px}

.ev-form-group{margin-bottom:14px}
.ev-form-group label{display:block;color:#dce1ec;font-size:16px;margin:0 0 6px;font-weight:400}
.ev-auth-input{width:100%;height:46px;border-radius:5px;border:1px solid #2e3646;background:#0e121a;color:#fff;padding:0 12px;font-size:18px;box-sizing:border-box}
.ev-auth-input:focus{outline:none;border-color:#3d475d}

.ev-login-actions{display:flex;gap:12px;align-items:center;margin-top:1.5rem}
.ev-btn-signin{height:34px;min-width:126px;border-radius:5px;border:none;background:#c1f11d;color:#111;font-size:20px;font-weight:500;padding:0 20px;cursor:pointer}
.ev-btn-signin[disabled]{opacity:.7;cursor:not-allowed}

.ev-spinner{animation:ev-spin 1s linear infinite;vertical-align:middle}
@keyframes ev-spin{to{transform:rotate(360deg)}}

/* Input with icon */
.ev-input-wrapper{position:relative;display:flex;align-items:center}
.ev-input-icon{position:absolute;left:12px;color:#666;pointer-events:none;z-index:1}
.ev-auth-input-icon{padding-left:38px !important}

/* Mobile header (hidden on desktop) */
.ev-login-mobile-header{display:none}

/* Signup link */
.ev-signup-link{text-align:center;color:#999;font-size:14px;margin:6px 0 0}
.ev-signup-link a{color:#C1F11D;text-decoration:none;font-weight:500}

/* "or" divider */
.ev-login-or{margin:20px 0 8px !important}

@media(max-width:768px){
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
    text-align:center;
  }
  .ev-login-subtitle{
    color:#888;
    font-size:14px;
    margin:0;
    text-align:center;
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
  .ev-forget-lead{display:none}

  .ev-form-group label{font-size:14px;color:#ccc}
  .ev-auth-input{
    height:44px;
    border-radius:5px;
    border-color:#333;
    background:#0a0a0a;
    font-size:15px;
  }

  .ev-login-actions{margin-top:20px}
  .ev-btn-signin{
    width:100%;
    height:48px;
    border-radius:5px;
    font-size:16px;
    font-weight:600;
  }

  .ev-signup-link{margin-top:0;font-size:14px}
}
</style>
@endpush
