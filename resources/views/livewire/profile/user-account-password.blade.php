@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    .ev-header {
        background: #0D1011;
    }
    .ev-back-bar {
        background: #1f2222;
        padding: 12px 0;
    }
    .ev-back-bar a {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 400;
    }
    .ev-back-bar h1 {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        text-align: center;
    }
    .ev-back-bar h1 a {
        color: #fff;
        text-decoration: none;
    }
    .ev-account-tabs {
        display: flex;
        gap: 8px;
        padding: 20px 0;
        flex-wrap: wrap;
    }
    .ev-account-tabs a {
        display: inline-flex;
        align-items: center;
        width: 170px;
        gap: 8px;
        padding: 6px 0px;
        border-radius: 5px;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        background: #1D2224;
        justify-content: center;
    }
    .ev-account-tabs a:hover {
        border-color: var(--accent, #C1F11D);
        color: var(--accent, #C1F11D);
    }
    .ev-account-tabs a.active {
        color: #C1F11D;
    }
    .ev-alert-success {
        background: rgba(193, 241, 29, 0.1);
        border: 1px solid var(--accent, #C1F11D);
        color: var(--accent, #C1F11D);
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }
    .ev-form-group {
        margin-bottom: 20px;
    }
    .ev-form-group label {
        display: block;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .ev-form-group .ev-input {
        width: 100%;
        max-width: 500px;
        padding: 10px 14px;
        background: var(--bg-secondary, #111111);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .ev-form-group .ev-input:focus {
        border-color: var(--accent, #C1F11D);
    }
    .ev-form-group .ev-input::placeholder {
        color: var(--text-muted, #666);
    }
    .ev-text-danger {
        color: #dc3545;
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }
    /* Password input with show/hide toggle */
    .ev-password-wrap {
        position: relative;
        width: 100%;
        max-width: 500px;
    }
    .ev-password-wrap .ev-password-input {
        padding-right: 44px;
    }
    .ev-password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        padding: 4px;
        color: var(--text-muted, #888);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
    }
    .ev-password-toggle:hover { color: var(--accent, #C1F11D); }
    .ev-password-toggle:focus { outline: none; color: var(--accent, #C1F11D); }
    .ev-password-toggle svg {
        width: 20px !important;
        height: 20px !important;
        transform: none !important;
        -webkit-transform: none !important;
        margin: 0 !important;
    }
    .ev-save-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: var(--accent, #C1F11D);
        color: #000;
        border: none;
        border-radius: 21.5px;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 10px;
    }
    .ev-save-btn:hover {
        background: var(--accent-hover, #d4f84d);
        transform: translateY(-1px);
    }
    @media (max-width: 768px) {
        .ev-back-bar a {
            font-size: 14px;
        }
        .ev-back-bar h1 {
            font-size: 16px;
        }
        .ev-account-tabs {
            padding: 12px 0;
            gap: 6px;
        }
        .ev-account-tabs a {
            width: auto;
            flex: 1;
            min-width: 0;
            padding: 8px 10px;
            font-size: 12px;
            border: 1px solid #333;
        }
        .ev-account-tabs a.active {
            border-color: #C1F11D;
        }
        .ev-form-group label {
            font-size: 13px;
            color: #ccc;
        }
        .ev-form-group .ev-input {
            max-width: 100%;
            background: #1a1a1a;
            border-color: #333;
            border-radius: 5px;
            font-size: 14px;
        }
        .ev-save-btn {
            display: flex;
            width: auto;
            margin: 16px auto 0;
            justify-content: center;
            border-radius: 30px;
            padding: 12px 36px;
            font-size: 16px;
        }
        .ev-alert-success {
            border-radius: 5px;
        }
    }
</style>
@endpush

<div>
    <div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">

        {{-- Account tabs --}}
        <div class="ev-account-tabs">
            <a href="/my-account" wire:navigate class="{{ request()->is('my-account') && !request()->is('my-account/*') ? 'active' : '' }}">
                <i class="fa fa-user"></i> Account
            </a>
            <a href="/my-account/edit" wire:navigate class="{{ request()->is('my-account/edit') ? 'active' : '' }}">
                <i class="fa fa-pencil-alt"></i> Edit
            </a>
            <a href="/my-password/edit" wire:navigate class="{{ request()->is('my-password/edit') ? 'active' : '' }}">
                <i class="fa fa-key"></i> Password
            </a>
        </div>

        @if (session()->has('message'))
            <div class="ev-alert-success">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePassword">
            <div class="ev-form-group">
                <label for="current_password">Current password</label>
                <div class="ev-password-wrap">
                    <input wire:model="current_password" type="password" class="ev-input ev-password-input" id="current_password">
                    <button type="button" class="ev-password-toggle" aria-label="Show password" data-target="current_password" tabindex="-1">
                        <svg class="ev-password-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="ev-password-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('current_password') <span class="ev-text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="ev-form-group">
                <label for="password">New password</label>
                <div class="ev-password-wrap">
                    <input wire:model="password" type="password" class="ev-input ev-password-input" id="password">
                    <button type="button" class="ev-password-toggle" aria-label="Show password" data-target="password" tabindex="-1">
                        <svg class="ev-password-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="ev-password-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password') <span class="ev-text-danger">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="ev-save-btn" wire:loading.attr="disabled" wire:target="updatePassword">
                <span wire:loading.remove wire:target="updatePassword">
                    Update Password <i class="fa fa-chevron-right"></i>
                </span>
                {{-- Hidden by default (inline style) so it doesn't flash
                     in beside the idle label before Livewire takes over.
                     wire:loading.flex overrides display:none to show this
                     span ONLY while updatePassword is running. --}}
                <span wire:loading.flex wire:target="updatePassword" style="display:none;align-items:center;gap:8px;">
                    <i class="fa fa-spinner fa-spin"></i> Processing...
                </span>
            </button>
        </form>
    </div>
</div>

@push('js')
<script>
// Password show/hide toggle. Delegated so it survives Livewire morphs.
(function () {
    if (window.__evPasswordToggleBound) return;
    window.__evPasswordToggleBound = true;
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.ev-password-toggle');
        if (!btn) return;
        e.preventDefault();
        var id = btn.getAttribute('data-target');
        var input = document.getElementById(id);
        if (!input) return;
        var eye = btn.querySelector('.ev-password-eye');
        var off = btn.querySelector('.ev-password-eye-off');
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