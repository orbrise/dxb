@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    .ev-header {
        background: #0D1011;
    }
    .ev-back-bar {
        background: #131616;
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
            width: 100%;
            justify-content: center;
            border-radius: 5px;
            font-size: 16px;
            padding: 14px 28px;
        }
        .ev-alert-success {
            border-radius: 5px;
        }
    }
</style>
@endpush

<div>
    {{-- Back bar --}}
    <div class="ev-back-bar">
        <div class="ev-container" style="display: flex; align-items: center; justify-content: center; position: relative;">
            <a href="/" style="position: absolute; left: 16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Home
            </a>
            <h1>Change Password</h1>
        </div>
    </div>

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
                <input wire:model="current_password" type="password" class="ev-input" id="current_password">
                @error('current_password') <span class="ev-text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="ev-form-group">
                <label for="password">New password</label>
                <input wire:model="password" type="password" class="ev-input" id="password">
                @error('password') <span class="ev-text-danger">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="ev-save-btn">
                Update Password <i class="fa fa-chevron-right"></i>
            </button>
        </form>
    </div>
</div>