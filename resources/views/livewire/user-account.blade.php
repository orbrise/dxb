@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* Account page styles */
    .ev-header {
    background: #0D1011;
    }
    .ev-back-bar {
        background:  #131616;
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
    .ev-account-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .ev-account-card {
        background: var(--bg-card, #1a1a1a);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: 5px;
        padding: 20px;
        flex: 1 1 220px;
        min-width: 0;
    }
    .ev-account-card h2 {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
   
    .ev-user-card {
        display: flex;
        gap: 16px;
        position: relative;
        overflow: hidden;
        overflow-wrap: anywhere;
    }
    .ev-user-card .ev-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .ev-user-card .ev-user-name {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 8px 0;
    }
    .ev-user-card .ev-user-info {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .ev-user-card .ev-user-info li {
        color: var(--text-secondary, #aaa);
        font-size: 14px;
        margin-bottom: 4px;
    }
    .ev-user-card .ev-user-info li strong {
        color: #fff;
    }
    .ev-edit-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        color: var(--accent, #C1F11D);
        background: var(--bg-card-hover, #222222);
        border: 1px solid var(--border-color, #2a2a2a);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .ev-edit-btn:hover {
        background: var(--accent, #C1F11D);
        color: #000;
    }
    .ev-credits-amount {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 16px 0;
    }
    .ev-buy-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--accent, #C1F11D);
        color: #000;
        border: none;
        border-radius: 21.5px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .ev-buy-btn:hover {
        background: var(--accent-hover, #d4f84d);
        transform: translateY(-1px);
    }
    .ev-payments-label {
        color: var(--text-muted, #666);
        font-size: 12px;
        margin: 16px 0 8px 0;
    }
    .ev-payments {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 12px;
        border-top: 1px solid var(--border-color, #2a2a2a);
    }
    .ev-comm-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .ev-comm-list li {
        margin-bottom: 8px;
    }
    .ev-comm-list li:last-child {
        margin-bottom: 0;
    }
    .ev-comm-list a {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        transition: opacity 0.2s;
    }
    .ev-comm-list a:hover {
        opacity: 0.8;
    }
    .ev-comm-list .ev-badge {
        background: #dc3545;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 8px;
        font-weight: 600;
    }
    .ev-newsletter-text {
        color: var(--text-secondary, #aaa);
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }
    .ev-newsletter-text a {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .ev-newsletter-text a:hover {
        opacity: 0.8;
    }
    .ev-delete-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: none;
        border: none;
        color: #C1F11D;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        padding: 16px 0;
        transition: color 0.2s;
    }
    .ev-delete-link:hover {
        color: #dc3545;
    }
    .ev-alert-success {
        background: rgba(193, 241, 29, 0.1);
        border: 1px solid var(--accent, #C1F11D);
        color: var(--accent, #C1F11D);
        padding: 12px 16px;
        border-radius: var(--radius, 8px);
        margin-bottom: 16px;
        font-size: 14px;
    }
    /* Modal styles */
    .ev-modal-overlay {
        background: rgba(0,0,0,0.85);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 99999;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 3rem 1rem;
    }
    .ev-modal {
        background: var(--bg-card, #1a1a1a);
        color: #fff;
        border-radius: var(--radius-lg, 12px);
        border: 1px solid var(--border-color, #2a2a2a);
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        width: 100%;
        max-width: 500px;
    }
    .ev-modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color, #2a2a2a);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ev-modal-header h2 {
        margin: 0;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .ev-modal-close {
        background: var(--bg-card-hover, #222);
        border: 1px solid var(--border-color, #2a2a2a);
        color: #fff;
        font-size: 1.25rem;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .ev-modal-close:hover {
        background: var(--border-color, #2a2a2a);
    }
    .ev-modal-body {
        padding: 1.5rem;
    }
    .ev-modal-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid var(--border-color, #2a2a2a);
        text-align: right;
    }
    .ev-search-input {
        display: flex;
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: var(--radius, 8px);
        overflow: hidden;
        background: var(--bg-secondary, #111);
    }
    .ev-search-input span {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
      
    }
    .ev-search-input input {
        flex: 1;
        padding: 0.75rem;
        background: transparent;
        border: none;
        color: #fff;
        outline: none;
        font-size: 0.95rem;
        font-family: inherit;
    }
    .ev-search-input input::placeholder {
        color: var(--text-muted, #666);
    }
    .ev-search-input button {
        padding: 0.75rem 1rem;
        background: transparent;
        border: none;
        color: var(--accent, #C1F11D);
        cursor: pointer;
    }
    .ev-city-tag {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        padding: 0.75rem 1rem;
        background: var(--bg-secondary, #111);
        border-radius: var(--radius, 8px);
        border: 1px solid var(--border-color, #2a2a2a);
    }
    .ev-dropdown-results {
        position: absolute;
        width: 100%;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        background: var(--bg-secondary, #111);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: var(--radius, 8px);
        margin-top: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .ev-dropdown-results button {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        background: transparent;
        color: #fff;
        border: none;
        border-bottom: 1px solid var(--border-color, #2a2a2a);
        text-align: left;
        cursor: pointer;
        transition: background 0.2s;
        font-family: inherit;
        font-size: 14px;
    }
    .ev-dropdown-results button:hover {
        background: var(--bg-card-hover, #222);
    }
    @media (max-width: 768px) {
        .ev-account-cards {
            flex-direction: column;
        }
        .ev-account-card {
            flex-basis: auto;
        }
        .ev-account-tabs a {
            padding: 8px 16px;
            font-size: 13px;
        }
    }
</style>
@endpush

<div>
    {{-- Back bar --}}
    <div class="ev-back-bar">
        <div class="ev-container" style="display: flex; align-items: center; justify-content: center; position: relative;">
            <a href="{{ url('/') }}" wire:navigate style="position: absolute; left: 16px;">
                <i class="fa fa-angle-left"></i> Back
            </a>
            <h1><a href="/my-account">My Account</a></h1>
        </div>
    </div>

    {{-- Main content --}}
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

        @if(session('success'))
            <div class="ev-alert-success">{{ session('success') }}</div>
        @endif

        {{-- Cards grid --}}
        <div class="ev-account-cards">
            
            {{-- User info card --}}
            <div class="ev-account-card">
                <div class="ev-user-card">
                    <img alt="{{ auth()->user()->name }}'s avatar" class="ev-avatar" src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?s=128&d=identicon" />
                    <div>
                        <h2 class="ev-user-name">{{ auth()->user()->name }}</h2>
                        <ul class="ev-user-info">
                            <li>
                                <strong>Account type</strong> @if(auth()->user()->type == 1) Standard @elseif(auth()->user()->type == 2) Individual @elseif(auth()->user()->type == 3) Agency @endif
                            </li>
                            <li>
                                <strong>Email</strong> {{ auth()->user()->email }}
                            </li>
                        </ul>
                    </div>
                    <a class="ev-edit-btn" href="/my-account/edit">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                </div>
            </div>

            {{-- Credits card --}}
            <div class="ev-account-card">
                <h2 class="ev-credits-amount">Credits ${{ auth()->user()->wallet->balance ?? 0 }}</h2>
                <div style="margin-bottom: 16px;">
                    <a class="ev-buy-btn" href="/purchase-credits" onclick="window.location.href='/purchase-credits'; return false;">
                        <i class="fa fa-coins"></i>
                        <span>Buy more</span>
                    </a>
                </div>
                <div class="ev-payments">
                    <p class="ev-payments-label" style="margin: 0; width: 100%;">We accept:</p>
                    <img alt="Mastercard logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/mc_logo-1fee638879a55506111eef88a8369601147f17d09fa23d940350fee69fb9fc79.svg" width="36" />
                    <img alt="Visa logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" width="36" />
                    <img alt="Bitcoin icon" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc-b522654df0046f6af0e8ac9f67078a87d26069445a01866c1c337bde91bbcd5f.svg" width="24" />
                    <img alt="Lightning network icon" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc_lightning-fa0277781a99cded862007ac4d2e5f5fa8fbafec4d9c2b59dbd3b2fc354e91a0.svg" width="42" />
                    <img alt="Neosurf voucher logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/layout/neosurf-bd53910fca644afad7f8660597f25b84b5c97418652d1c2794ab5f1462e2faf7.svg" width="50" />
                    <img alt="Payprocc Payments Promptpay logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_promptpay_logo-6b70d10f79bdff9dc1efde97f390f89f91019db1b14c81389d20be94fd270275.svg" style="filter: brightness(2)" width="72" />
                    <img alt="Payprocc Payments Momo logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_momo_logo-41d3eadebd629ec10dd28d7c13683b8c5cf564e37eae34ae478de6d89a72b26c.svg" width="24" />
                    <img alt="Payprocc Payments Viettelpay logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_viettelpay_logo-f99d7823ad8c66b5da8a2248c1877215a648cb16703f63c641584d684f73019b.svg" width="26" />
                </div>
            </div>

            {{-- Communication card --}}
            @php
                $userProfiles = \App\Models\UsersProfile::where('user_id', auth()->id())->pluck('id');
                $unreadMsgCount = \App\Models\Message::whereIn('profile_id', $userProfiles)
                    ->where(function($q) {
                        $q->whereNull('status')->orWhere('status', 'unread');
                    })
                    ->count();
            @endphp
            <div class="ev-account-card">
                <h2>
                    <i class="fa fa-comments ev-card-icon"></i> Communication
                    @if($unreadMsgCount > 0)
                        <span class="ev-badge" style="background:#dc3545;color:#fff;font-size:11px;padding:3px 8px;border-radius:10px;">{{ $unreadMsgCount }}</span>
                    @endif
                </h2>
                <ul class="ev-comm-list">
                    <li>
                        <a href="{{ route('user.chat') }}">
                            <i class="fa fa-envelope" style="width: 16px;"></i> Messages
                            @if($unreadMsgCount > 0)<span class="ev-badge">{{ $unreadMsgCount }}</span>@endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.questions') }}">
                            <i class="fa fa-question-circle" style="width: 16px;"></i> Questions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.reviews') }}">
                            <i class="fa fa-star" style="width: 16px;"></i> Reviews
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('favorites.dashboard') }}">
                            <i class="fa fa-heart" style="width: 16px;"></i> My Favorites
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Newsletter card --}}
            <div class="ev-account-card" x-data="{ show: false }">
                <h2>
                    <i class="fa fa-newspaper ev-card-icon"></i> Newsletter
                </h2>
                <p class="ev-newsletter-text">
                    Receiving monthly updates in {{ auth()->user()->newsletterSubscriptions()->count() }} cities.<br>
                    <a href="#" @click.prevent="show = true; $wire.call('loadUserSettings')">
                        <i class="fa fa-pencil-alt"></i> Edit Subscription
                    </a>
                </p>
                
                {{-- Newsletter Modal --}}
                <div x-show="show" x-cloak class="ev-modal-overlay" @click.self="show = false">
                    <div class="ev-modal">
                        <div class="ev-modal-header">
                            <h2>
                                <i class="fa fa-newspaper" ></i>
                                <span>Newsletter</span>
                            </h2>
                            <button type="button" class="ev-modal-close" @click="show = false">&times;</button>
                        </div>
                        
                        <div class="ev-modal-body">
                            {{-- Checkbox --}}
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: flex; align-items: center; cursor: pointer; font-size: 1rem;">
                                    <input type="checkbox" id="receiveNewsletter" wire:model.live="receiveNewsletter" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                    <span style="margin-left: 0.75rem; font-weight: 500;">Send me newsletter for:</span>
                                </label>
                            </div>

                            {{-- City Search --}}
                            <div style="margin-bottom: 1rem; position: relative;">
                                <div class="ev-search-input">
                                    <span><i class="fa fa-map-marker-alt"></i></span>
                                    <input type="text" placeholder="Find city..." wire:model.live="citySearch" autocomplete="off">
                                    @if($citySearch)
                                        <button type="button" wire:click="$set('citySearch', '')">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                                
                                @if(count($searchResults) > 0)
                                    <div class="ev-dropdown-results">
                                        @foreach($searchResults as $city)
                                            <button type="button" wire:click="addCity({{ $city['id'] }})">
                                                {{ $city['name'] }}@if($city['country']) <span style="color: var(--text-muted, #666);">({{ $city['country'] }})</span>@endif
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Selected Cities --}}
                            @if(count($selectedCities) > 0)
                                <div style="margin-bottom: 1rem; max-height: 150px; overflow-y: auto;">
                                    @foreach($selectedCities as $index => $city)
                                        <div class="ev-city-tag">
                                            <span>
                                                <i class="fa fa-map-marker-alt" style="margin-right: 0.5rem;"></i>
                                                {{ $city['name'] }}@if($city['country']) <span style="color: var(--text-muted, #666);">({{ $city['country'] }})</span>@endif
                                            </span>
                                            <button type="button" style="background: none; border: none;  cursor: pointer; font-size: 1.2rem; padding: 0;" wire:click="removeCity({{ $index }})">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Add City Button --}}
                            <button type="button" class="ev-btn-outline-sm" style="padding: 0.6rem 1rem; margin-bottom: 1.5rem; background: var(--bg-secondary, #111);  border: 1px solid var(--border-color, #2a2a2a); border-radius: var(--radius, 5px); cursor: pointer; font-weight: 600; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem;" wire:click="$set('citySearch', '')">
                                <i class="fa fa-plus"></i>
                                <span>Add city</span>
                            </button>

                            {{-- Include Genders --}}
                            <div>
                                <label style="display: block; margin-bottom: 1rem; font-weight: 600; font-size: 1rem;">Include</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="gender_female" value="female" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                        <span style="margin-left: 0.5rem;">Escorts</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="gender_male" value="male" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                        <span style="margin-left: 0.5rem;">Male Escorts</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="gender_shemale" value="shemale" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                        <span style="margin-left: 0.5rem;">Shemale Escorts</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ev-modal-footer">
                            <button type="button" class="ev-buy-btn" wire:click="saveNewsletter" @click="show = false">
                                <span>Save</span>
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Delete account --}}
        <div style="text-align: right; margin-top: 8px;">
            <form action="{{ route('user.account.delete') }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to DELETE your account? This will deactivate your account and all your profiles. This action cannot be reversed.')">
                @csrf
                <button type="submit" class="ev-delete-link">
                    <i class="fa fa-times"></i> Delete account
                </button>
            </form>
        </div>

    </div>
</div>