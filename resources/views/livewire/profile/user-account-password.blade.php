@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* ===============================================================
       Change Password — matches the My Account - Edit redesign:
       dark back-bar, 3 tabs, same input styling (translucent dark fill
       + grey border), lime pill Update button, body goes pure black.
       =============================================================== */
    body { background: #000 !important; }

    /* Unstick + unpaint the top site header so it scrolls with the
       content and blends into the black body. */
    header.ev-header {
        background: transparent !important;
        position: static !important;
    }

    /* Dark back-bar strip at the top of the content. */
    .acctpw-header {
        background: #131616;
        padding: 7px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 24px;
    }
    .acctpw-header-inner {
        max-width: 1300px;
        margin: 0 auto;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        min-height: 24px;
    }
    .acctpw-header-back {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #C1F11D;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }
    .acctpw-header-back:hover { color: #d4ff3a; text-decoration: none; }
    .acctpw-header-title {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .acctpw-wrap {
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 0 48px;
        color: #fff;
    }

    .acctpw-flash {
        background: rgba(193,241,29,0.1);
        border: 1px solid rgba(193,241,29,0.3);
        color: #C1F11D;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 14px;
        margin-bottom: 20px;
        max-width: 620px;
    }

    /* 3 equal-width tab pills (same style as the Edit page). */
    .acctpw-tabs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
        max-width: 620px;
    }
    .acctpw-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 5px 16px;
        background: #131616;
        border: 1px solid #242B2D;
        border-radius: 5px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 120ms, border-color 120ms, background 120ms;
    }
    .acctpw-tab:hover { color: #fff; border-color: rgba(255,255,255,0.14); text-decoration: none; }
    .acctpw-tab.active {
        color: #C1F11D;
        
    }
    .acctpw-tab i { font-size: 13px; }
    .acctpw-tab svg { display: block; width: 16px; height: 16px; }

    /* Form fields — same visual tokens as the Edit page. */
    .acctpw-field { margin-bottom: 18px; max-width: 620px; }
    .acctpw-field label {
        display: block;
        color: rgba(255,255,255,0.75);
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .acctpw-input {
        width: 100%;
        padding: 12px 14px;
        background: #14141A2E;
        border: 1px solid #5E6365;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
        transition: border-color 120ms;
    }
    .acctpw-input:focus { border-color: rgba(193,241,29,0.5); }
    .acctpw-input::placeholder { color: rgba(255,255,255,0.3); }
    .acctpw-error {
        color: #ef4444;
        font-size: 12px;
        margin-top: 6px;
        display: block;
    }

    /* Password input + show/hide eye toggle. */
    .acctpw-password-wrap {
        position: relative;
        max-width: 620px;
    }
    .acctpw-password-wrap .acctpw-input { padding-right: 44px; }
    .acctpw-password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        padding: 4px;
        color: rgba(255,255,255,0.5);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
    }
    .acctpw-password-toggle:hover { color: #C1F11D; }
    .acctpw-password-toggle:focus { outline: none; color: #C1F11D; }
    .acctpw-password-toggle svg {
        width: 20px !important;
        height: 20px !important;
    }

    /* Lime pill Update button — matches the Save button on Edit. */
    .acctpw-save-wrap { margin-top: 24px; }
    .acctpw-save-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 42px;
        background: #C1F11D;
        color: #0a0a0a;
        border: none;
        border-radius: 999px;
        font-size: 16px;
        font-weight: 500;
        font-family: inherit;
        cursor: pointer;
        transition: background 120ms, transform 120ms;
    }
    .acctpw-save-btn:hover { background: #d4ff3a; transform: translateY(-1px); }
    .acctpw-save-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* Match .ev-container's 12px inset under 768px so the content
       doesn't collide with the screen edge on phones. */
    @media (max-width: 768px) {
        .acctpw-header-inner { padding: 0 12px; }
        .acctpw-header-back { left: 12px; }
        .acctpw-wrap { padding: 12px 12px 48px; }
    }

    @media (max-width: 900px) {
        .acctpw-tabs { gap: 8px; }
        .acctpw-tab { padding: 7px 8px; font-size: 12px; border: 1px solid #364153; border-radius: 12px; }
        .acctpw-tab i { display: none; }
        .acctpw-header-title { font-size: 15px; }
        .acctpw-header-back span { display: none; }
        /* Hide the page's own "Change Password" sub-header on mobile — the
           layout's parent "Back / Password" strip above already serves as
           the page title; stacking two sub-headers is redundant. */
        .acctpw-header { display: none; }
    }
</style>
@endpush

<div>
    {{-- Dark back-bar header matching the Edit page. --}}
    <div class="acctpw-header">
        <div class="acctpw-header-inner">
            <a href="{{ url('/my-account/info') }}" wire:navigate class="acctpw-header-back" aria-label="My account">
                <i class="fa fa-chevron-left"></i>
                <span>My account</span>
            </a>
            <h1 class="acctpw-header-title">Change Password</h1>
        </div>
    </div>

    <div class="acctpw-wrap">
        @if (session()->has('message'))
            <div class="acctpw-flash">
                <i class="fa fa-check-circle"></i> {{ session('message') }}
            </div>
        @endif

        {{-- Tabs: Account / Edit / Password (active) — custom SVG glyphs,
             currentColor-stroked so they inherit the tab's text color
             (grey when idle, lime when .active). --}}
        <div class="acctpw-tabs">
            {{-- /my-account is the mobile dashboard grid; the Account content
                 lives at /my-account/info. Route there so clicking Account
                 from Edit/Password lands on the account view, not the grid. --}}
            <a href="/my-account/info" wire:navigate class="acctpw-tab">
                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M1 15C1 13.9391 1.42143 12.9217 2.17157 12.1716C2.92172 11.4214 3.93913 11 5 11H13C14.0609 11 15.0783 11.4214 15.8284 12.1716C16.5786 12.9217 17 13.9391 17 15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17H3C2.46957 17 1.96086 16.7893 1.58579 16.4142C1.21071 16.0391 1 15.5304 1 15Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M9 7C10.6569 7 12 5.65685 12 4C12 2.34315 10.6569 1 9 1C7.34315 1 6 2.34315 6 4C6 5.65685 7.34315 7 9 7Z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Account
            </a>
            <a href="/my-account/edit" wire:navigate class="acctpw-tab">
                <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M4 5.00012H3C2.46957 5.00012 1.96086 5.21084 1.58579 5.58591C1.21071 5.96098 1 6.46969 1 7.00012V16.0001C1 16.5306 1.21071 17.0393 1.58579 17.4143C1.96086 17.7894 2.46957 18.0001 3 18.0001H12C12.5304 18.0001 13.0391 17.7894 13.4142 17.4143C13.7893 17.0393 14 16.5306 14 16.0001V15.0001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13 3.00011L16 6.00011M17.385 4.58511C17.7788 4.19126 18.0001 3.65709 18.0001 3.10011C18.0001 2.54312 17.7788 2.00895 17.385 1.61511C16.9912 1.22126 16.457 1 15.9 1C15.343 1 14.8088 1.22126 14.415 1.61511L6 10.0001V13.0001H9L17.385 4.58511Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Edit
            </a>
            <a href="/my-password/edit" wire:navigate class="acctpw-tab active">
                <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M1.49715 14.1093C1.17892 14.4275 1.0001 14.859 1 15.3089V17.1516C1 17.3766 1.08938 17.5924 1.24848 17.7515C1.40759 17.9106 1.62337 18 1.84838 18H4.39351C4.61851 18 4.8343 17.9106 4.9934 17.7515C5.15251 17.5924 5.24189 17.3766 5.24189 17.1516V16.3032C5.24189 16.0782 5.33127 15.8625 5.49037 15.7034C5.64947 15.5442 5.86526 15.4549 6.09027 15.4549H6.93864C7.16365 15.4549 7.37944 15.3655 7.53854 15.2064C7.69764 15.0473 7.78702 14.8315 7.78702 14.6065V13.7581C7.78702 13.5331 7.8764 13.3173 8.03551 13.1582C8.19461 12.9991 8.4104 12.9097 8.6354 12.9097H8.78132C9.23129 12.9096 9.6628 12.7308 9.98093 12.4126L10.6715 11.722C11.8506 12.1327 13.1342 12.1312 14.3123 11.7176C15.4904 11.3039 16.4933 10.5027 17.1568 9.44507C17.8204 8.38738 18.1053 7.13582 17.9651 5.89513C17.8249 4.65443 17.2677 3.49805 16.3848 2.61516C15.502 1.73226 14.3456 1.17513 13.1049 1.0349C11.8642 0.894666 10.6126 1.17964 9.55493 1.84319C8.49725 2.50675 7.69607 3.5096 7.28245 4.6877C6.86883 5.8658 6.86726 7.14939 7.27799 8.3285L1.49715 14.1093Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.3011 6.12267C13.5354 6.12267 13.7253 5.93275 13.7253 5.69848C13.7253 5.46421 13.5354 5.27429 13.3011 5.27429C13.0669 5.27429 12.877 5.46421 12.877 5.69848C12.877 5.93275 13.0669 6.12267 13.3011 6.12267Z" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Password
            </a>
        </div>

        <form wire:submit.prevent="updatePassword">
            <div class="acctpw-field">
                <label for="current_password">Current password</label>
                <div class="acctpw-password-wrap">
                    <input wire:model="current_password" type="password" class="acctpw-input" id="current_password" autocomplete="current-password">
                    <button type="button" class="acctpw-password-toggle" aria-label="Show password" data-target="current_password" tabindex="-1">
                        <svg class="acctpw-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="acctpw-eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('current_password') <span class="acctpw-error">{{ $message }}</span> @enderror
            </div>

            <div class="acctpw-field">
                <label for="password">New password</label>
                <div class="acctpw-password-wrap">
                    <input wire:model="password" type="password" class="acctpw-input" id="password" autocomplete="new-password">
                    <button type="button" class="acctpw-password-toggle" aria-label="Show password" data-target="password" tabindex="-1">
                        <svg class="acctpw-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="acctpw-eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password') <span class="acctpw-error">{{ $message }}</span> @enderror
            </div>

            <div class="acctpw-save-wrap">
                <button type="submit" class="acctpw-save-btn" wire:loading.attr="disabled" wire:target="updatePassword">
                    <span wire:loading.remove wire:target="updatePassword">
                        Update Password
                        {{-- Thin stroked chevron (matches the Edit page). --}}
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="vertical-align:middle;">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </span>
                    <span wire:loading.flex wire:target="updatePassword" style="display:none;align-items:center;gap:8px;">
                        <i class="fa fa-spinner fa-spin"></i> Processing...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
// Password show/hide toggle. Document-delegated so it survives Livewire morphs.
(function () {
    if (window.__acctpwToggleBound) return;
    window.__acctpwToggleBound = true;
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.acctpw-password-toggle');
        if (!btn) return;
        e.preventDefault();
        var id = btn.getAttribute('data-target');
        var input = document.getElementById(id);
        if (!input) return;
        var eye = btn.querySelector('.acctpw-eye');
        var off = btn.querySelector('.acctpw-eye-off');
        if (input.type === 'password') {
            input.type = 'text';
            if (eye) eye.style.display = 'none';
            if (off) off.style.display = '';
            btn.setAttribute('aria-label', 'Hide password');
        } else {
            input.type = 'password';
            if (eye) eye.style.display = '';
            if (off) off.style.display = 'none';
            btn.setAttribute('aria-label', 'Show password');
        }
    });
})();
</script>
@endpush
