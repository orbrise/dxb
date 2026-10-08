{{-- Evoory Theme Header - Optimized --}}
@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $currentPath = request()->path();
    $citySlug = function_exists('getFeaturedCitySlug') ? getFeaturedCitySlug() : 'dubai';
    // Listings (home) pages: /, /{gender}-escorts-in-{city}, /{gender}-escorts-in-{city}/page/{n}
    $isHomePage = $currentRoute === 'home' || $currentRoute === 'home.paginated' || $currentRoute === 'newhome' || $currentPath === '/' || (bool) preg_match('#^(female|male|shemale)-escorts-in-[^/]+(/page/\d+)?$#', $currentPath);
    // Service-filtered listing pages: /{service}-{gender}-escorts-in-{city}
    // Treat them as a listing page so the ESCORTS / WHAT'S NEW tabs render
    // and the desktop nav matches the homepage. The non-capturing prefix
    // `(?:[a-z][a-z0-9-]+-)?` allows zero or one service slug before the
    // gender; without it the URL fell through to the no-tabs branch and the
    // service page header looked stripped down vs the homepage.
    $isServicePage = (bool) preg_match('#^[a-z][a-z0-9-]+-(female|male|shemale)-escorts-in-[^/]+(/page/\d+)?$#', $currentPath);
    $isHomePage = $isHomePage || $isServicePage;
    // News / What's new pages
    $isNewsPage = in_array($currentRoute, ['news.all', 'news.page']) || str_contains($currentPath, 'escort-news-in-');
    // Auth pages keep their own layout
    $isAuthPage = in_array($currentRoute, ['sign-in', 'register', 'login', 'forgot-password']);
    // Profile details: /{gender}-escorts-in-{city}/{id}/{slug}
    $isProfileDetails = (bool) preg_match('#^(female|male|shemale)-escorts-in-[^/]+/\d+/[^/]+#', $currentPath);
    // Listing create/edit
    $isListingCreateEdit = in_array($currentRoute, ['new.profile', 'user.profile']);
    // Listings (home) page other than /
    $isListingsHome = ($currentRoute === 'home' || $currentRoute === 'home.paginated') || (bool) preg_match('#^(female|male|shemale)-escorts-in-[^/]+(/page/\d+)?$#', $currentPath);
    // Account-related pages get a distinct header ("< Home" + title)
    $accountPageTitles = [
        'user.account' => 'My Account',
        'user.account.edit' => 'Edit',
        'user.account.password' => 'Password',
        'user.account.newsletter' => 'Newsletter',
        'user.chat' => 'Messages',
        'user.chat.with' => 'Messages',
        'user.questions' => 'Questions',
        'user.reviews' => 'Reviews',
        'purchase.credits' => 'Buy Credits',
        'user.wallet' => 'Wallet',
        'favorites.dashboard' => 'My Favorite Profiles',
        'profile.archived' => 'Archived Profiles',
        'profile.status' => 'Profile Status',
        'rejected.verifications' => 'Rejected Verifications',
    ];
    $isAccountPage = array_key_exists($currentRoute, $accountPageTitles);
    $accountPageTitle = $accountPageTitles[$currentRoute] ?? '';
    // Simple header shown for: listing create/edit, what's new, listings page (NOT profile details - it has its own nav)
    $useSimpleHeader = !$isAccountPage && ($isListingCreateEdit || $isNewsPage || $isListingsHome);
    // Profile details and user dashboard hide both headers on mobile (they have their own internal nav)
    $hideAllHeadersMobile = $isProfileDetails || $currentRoute === 'user.dashboard';

    // Auth-nav active states. Used by the desktop "My Profile" / "My Account"
    // links so the current page renders with a lime outline, matching the
    // selected-tab look the rest of the site uses. Each predicate covers
    // every URL that lives "under" that menu entry:
    //   - My Profile  → /my-profile/{slug}/{id}, the create-profile flow,
    //                   the edit-profile flow, and the legacy user.dashboard.
    //   - My Account  → any of the account-area routes from $accountPageTitles
    //                   above (Messages, Reviews, Buy Credits, Favorites, …).
    $isMyProfileActive = str_starts_with($currentPath, 'my-profile/')
        || str_starts_with($currentPath, 'my-listings')
        || in_array($currentRoute, ['new.profile', 'user.profile', 'user.dashboard'], true);
    $isMyAccountActive = $isAccountPage;

    // Stats shown in the new header auth dropdown. Guarded by auth()->check()
    // because this component also renders for guests.
    $authUser          = auth()->user();
    $authAvatarUrl     = $authUser && $authUser->avatar ? user_avatar_url($authUser) : null;
    $authInitial       = $authUser ? strtoupper(substr($authUser->name ?? $authUser->email ?? '?', 0, 1)) : '?';
    $authName          = $authUser ? ($authUser->name ?? $authUser->username ?? strstr($authUser->email ?? '', '@', true)) : '';
    $authEmail         = $authUser->email ?? '';
    $authProfileCount  = $authUser ? \App\Models\UsersProfile::where('user_id', $authUser->id)->count() : 0;
    $authWalletBalance = $authUser && $authUser->wallet ? (float) $authUser->wallet->balance : 0;
    $authChatUnread    = $authUser
        ? \App\Models\Message::whereHas('conversation', function ($q) use ($authUser) {
                $q->where('user_one_id', $authUser->id)->orWhere('user_two_id', $authUser->id);
            })
            ->where('sender_id', '!=', $authUser->id)
            ->where(function ($q) { $q->whereNull('status')->orWhereIn('status', ['sent', 'delivered', 'unread']); })
            ->count()
        : 0;
@endphp

{{-- Selected-state pill for the auth nav. `.ev-nav-link` already has the
     dark pill background + 1px border from evoory-theme.css; we just need
     to swap the border + text to the brand lime when the link's page is
     the one currently being viewed. Slight background tint mirrors the
     hover state so the selection reads even without comparing to siblings. --}}
<style>
    .ev-nav.ev-nav .ev-nav-link.active,
    .ev-nav.ev-nav .ev-nav-link.active:link,
    .ev-nav.ev-nav .ev-nav-link.active:visited,
    .ev-nav.ev-nav .ev-nav-link.active:hover,
    .ev-nav.ev-nav .ev-nav-link.active:focus {
        color: #C1F11D !important;
        border-color: #C1F11D !important;
        background: rgba(193, 241, 29, 0.08) !important;
    }

    /* ============================================================
       New auth header pattern: Chat & Calls button + user menu
       dropdown + Add Profile CTA. Desktop-only (hidden ≤768px).
       ============================================================ */
    .ev-cc-btn,
    .ev-add-profile-btn,
    .ev-user-wrap { display: none; }
    @media (min-width: 769px) {
        .ev-cc-btn,
        .ev-add-profile-btn,
        .ev-user-wrap { display: inline-flex; }
        .ev-user-wrap { position: relative; }
    }

    .ev-cc-btn {
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: transparent;
        color: #fff;
        text-decoration: none;
    }
    .ev-cc-btn:hover { color: #C1F11D; }
    .ev-cc-btn:hover .ev-cc-label { color: #fff; }
    .ev-cc-icon {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .ev-cc-label {
        font-size: 12px;
        color: #fff;
        line-height: 1;
    }
    .ev-cc-badge {
        position: absolute;
        top: -6px;
        right: -8px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        background: #C1F11D;
        color: #0a0a0a;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #000;
    }

    .ev-user-trigger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1B1F1F;
        border: 1px solid transparent;
        color: #fff;
        cursor: pointer;
        padding: 6.5px 16px 6.5px 7px;
        border-radius: 5px;
        font-family: inherit;
    }
    .ev-user-trigger:hover { background: #22272a; }
    .ev-user-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2a2d30, #16181a);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .ev-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ev-user-avatar--lg { width: 44px; height: 44px; font-size: 16px; }
    .ev-user-name {
        color: #fff;
        font-weight: 500;
        font-size: 14px;
        max-width: 140px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ev-user-chev { color: #c7cdd1; }

    .ev-user-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 260px;
        background: #14141A;
        border: 1px solid #272A2B;
        border-radius: 14px;
        padding: 10px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        z-index: 1100;
    }
    .ev-user-menu-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 8px 10px;
    }
    .ev-user-menu-head-text { min-width: 0; }
    .ev-user-menu-handle {
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ev-user-menu-email {
        color: #C1F11D;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ev-user-menu-divider {
        height: 1px;
        background: #272A2B;
        margin: 6px 0;
    }
    .ev-user-menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 10px 10px;
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        background: transparent;
        border: none;
        font-family: inherit;
        cursor: pointer;
        text-align: left;
    }
    .ev-user-menu-item:hover { background: rgba(255,255,255,0.04); color: #fff; }
    .ev-user-menu-item svg { width: 18px; height: 18px; flex-shrink: 0; color: #fff; }
    .ev-user-menu-item > span:not(.ev-user-menu-badge) { flex: 1 1 auto; }
    .ev-user-menu-item > .ev-user-menu-badge { flex: 0 0 auto; }
    .ev-user-menu-item--btn { color: #fff; }
    .ev-user-menu-badge {
        background: rgba(193, 241, 29, 0.12);
        border: 1px solid rgba(193, 241, 29, 0.3);
        color: #C1F11D;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 999px;
    }
    .ev-user-menu-badge--money {
        background: rgba(193, 241, 29, 0.12);
        border-color: rgba(193, 241, 29, 0.3);
        color: #C1F11D;
    }

    .ev-add-profile-btn {
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        background: linear-gradient(90deg, #101E1B 0%, #2B421B 100%);
        color: #C1F11D;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: filter 120ms;
    }
    .ev-add-profile-btn:hover {
        filter: brightness(1.1);
        color: #C1F11D;
    }


</style>

{{-- Mobile Account Header ("< Home" + page title, no logo) --}}
@if($isAccountPage)
<header class="ev-header-account">
    <div class="ev-header-account-inner">
        <a href="javascript:history.back()" class="ev-header-account-home">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span>Back</span>
        </a>
        <span class="ev-header-account-title">{{ $accountPageTitle }}</span>
        <span class="ev-header-account-spacer"></span>
    </div>
</header>
@endif

{{-- Mobile Simple Header (non-home, non-auth pages) --}}
@if($useSimpleHeader)
<header class="ev-header-simple">
    <div class="ev-header-simple-inner">
        <a href="javascript:history.back()" class="ev-header-simple-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span>Back</span>
        </a>
        <a href="/" class="ev-header-simple-logo">
            @if(isset($setting) && $setting->app_logo)
            <img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}" style="height:28px;width:auto;display:block;">
            @else
            <span>{{ $setting->app_name ?? 'evoory' }}</span>
            @endif
        </a>
        <span class="ev-header-simple-spacer"></span>
    </div>
</header>
@endif

<header class="ev-header {{ $useSimpleHeader ? 'ev-header--has-simple' : '' }} {{ $isAccountPage ? 'ev-header--has-account' : '' }} {{ $hideAllHeadersMobile ? 'ev-header--hide-mobile' : '' }}">
    <div class="ev-container">
        <div class="ev-flex ev-items-center ev-justify-between">
            {{-- Logo + Tabs grouped together --}}
            <div class="ev-flex ev-items-center">
                @if(isset($setting) && $setting->app_logo)
                <a href="/" class="ev-logo"><img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}" style="height:36px;width:auto;display:block;"></a>
                @else
                <a href="/" class="ev-logo">{{ $setting->app_name ?? 'evoory' }}</a>
                @endif

                @if($isHomePage || $isNewsPage)
                <div class="ev-header-tabs">
                    <a href="/female-escorts-in-{{ $citySlug }}" class="ev-header-tab {{ $isHomePage ? 'active' : '' }}" wire:navigate>ESCORTS</a>
                    {{-- WHAT'S NEW must be a full navigation (no wire:navigate). The
                         news page uses the legacy `components.layouts.app` layout
                         while the rest of the site uses `app-evoory`; wire:navigate
                         swaps the body but keeps the source layout's <head>, so the
                         news page's CSS/JS bundle is missing and hydration breaks
                         intermittently → blank screen until a hard refresh. --}}
                    <a href="/female-escort-news-in-{{ $citySlug }}" class="ev-header-tab {{ $isNewsPage ? 'active' : '' }}">WHAT'S NEW</a>
                </div>
                @endif
            </div>

            {{-- Desktop Navigation --}}
            <nav class="ev-nav">
                {{-- Language Selector (only for guests) --}}
                @guest
                <div class="ev-relative">
                    <button class="ev-nav-link ev-lang-btn" type="button" aria-label="Select Language">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 8l6 6"></path>
                            <path d="M4 14l6-6 2-3"></path>
                            <path d="M2 5h12"></path>
                            <path d="M7 2h1"></path>
                            <path d="M22 22l-5-10-5 10"></path>
                            <path d="M14 18h6"></path>
                        </svg>
                        Language
                    </button>
                    <div class="ev-dropdown ev-lang-dropdown">
                        <a href="?lang=en" class="ev-dropdown-item">English</a>
                    </div>
                </div>
                @endguest

                {{-- Sign In --}}
                @auth
                    @if(Auth::user()->type != 1)
                        {{-- Chat & Calls — stacked (icon above, label below) with
                             unread badge pinned to the icon top-right. --}}
                        <a href="{{ url('my-chat') }}" class="ev-cc-btn" aria-label="Chat & Calls">
                            <span class="ev-cc-icon">
                                <svg width="22" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.6281 2.14172C18.0792 1.64224 15.8558 0.0365726 10.9043 0.0154936C10.9043 0.0154936 5.06166 -0.331852 2.21552 2.2627C0.632383 3.83538 0.0779164 6.13849 0.0142588 8.99515C-0.0493988 11.8518 -0.120437 17.2059 5.07457 18.6612H5.07919L5.07457 20.8782C5.07457 20.8782 5.04044 21.7764 5.63458 21.9569C6.35234 22.1805 6.77672 21.4968 7.46404 20.7618C7.84045 20.3576 8.35894 19.7656 8.75288 19.3138C11.0104 19.5219 13.2871 19.3493 15.4867 18.8033L15.3484 18.8326C16.0661 18.6008 20.1273 18.0848 20.7842 12.7316C21.4706 7.20527 20.4576 3.71898 18.6281 2.14172ZM19.2296 12.3284C18.6696 16.7962 15.3797 17.0803 14.7745 17.2728C13.4534 17.6064 11.9367 17.797 10.3748 17.797C9.92577 17.797 9.4814 17.7814 9.04164 17.7503L9.10068 17.7539C9.10068 17.7539 6.85145 20.4475 6.15122 21.1477C5.92242 21.3749 5.67056 21.3566 5.67517 20.903C5.67517 20.6069 5.6927 17.2205 5.6927 17.2205C1.29479 16.009 1.54942 11.4486 1.60109 9.06663C1.65275 6.68471 2.10297 4.72711 3.44346 3.41288C5.85323 1.24357 10.8139 1.56526 10.8139 1.56526C15.0052 1.58267 17.0118 2.83733 17.4786 3.25799C19.023 4.57314 19.8099 7.71941 19.2296 12.3284ZM13.2227 8.85676V8.86959C13.2208 8.94096 13.1913 9.00887 13.1405 9.05929C13.0896 9.10971 13.0212 9.13881 12.9493 9.14058C12.8775 9.14234 12.8077 9.11664 12.7543 9.06877C12.701 9.0209 12.6682 8.95452 12.6627 8.88334C12.6815 8.69199 12.6583 8.49796 12.5946 8.3164C12.531 8.13484 12.4284 7.96916 12.2941 7.8308C12.1597 7.69243 11.9967 7.58468 11.8163 7.51498C11.636 7.44527 11.4425 7.41527 11.2494 7.42705H11.253C11.1814 7.42154 11.1145 7.38887 11.0664 7.33581C11.0182 7.28275 10.9924 7.21335 10.9943 7.14194C10.9962 7.07053 11.0257 7.00257 11.0766 6.9521C11.1275 6.90163 11.1959 6.87251 11.2678 6.87075H11.2835H11.2826L11.3462 6.86983C11.6038 6.86975 11.8586 6.92226 12.0949 7.02411C12.3312 7.12596 12.5439 7.27497 12.7198 7.4619C12.8957 7.64882 13.031 7.86967 13.1173 8.11072C13.2037 8.35178 13.2393 8.6079 13.2218 8.86318L13.2227 8.85676ZM14.1001 9.34341C14.1435 7.52145 12.9976 6.09541 10.8222 5.93594C10.7845 5.93477 10.7473 5.92604 10.7131 5.91027C10.6788 5.8945 10.6481 5.87202 10.6228 5.84417C10.5975 5.81632 10.5781 5.78368 10.5658 5.74821C10.5535 5.71274 10.5485 5.67517 10.5512 5.63775C10.5539 5.60033 10.5641 5.56383 10.5814 5.53044C10.5986 5.49705 10.6224 5.46746 10.6514 5.44344C10.6804 5.41942 10.714 5.40147 10.7502 5.39067C10.7864 5.37986 10.8243 5.37642 10.8619 5.38056H10.861H10.8766C11.3929 5.38054 11.9037 5.48536 12.3778 5.68857C12.8518 5.89179 13.279 6.18911 13.6331 6.56229C13.9873 6.93547 14.2609 7.37662 14.4372 7.85865C14.6135 8.34069 14.6887 8.85344 14.6583 9.36541L14.6592 9.35532C14.6542 9.42636 14.6221 9.49281 14.5694 9.54105C14.5167 9.58928 14.4474 9.61564 14.3757 9.6147C14.304 9.61376 14.2354 9.5856 14.184 9.536C14.1326 9.4864 14.1022 9.41913 14.0992 9.34799V9.34066L14.1001 9.34341ZM16.1298 9.91988V9.92171C16.126 9.99275 16.0951 10.0597 16.0433 10.1088C15.9914 10.1579 15.9227 10.1854 15.8511 10.1858C15.7795 10.1861 15.7104 10.1593 15.6581 10.1107C15.6058 10.0621 15.5742 9.99546 15.5698 9.92446C15.5476 6.42168 13.196 4.51449 10.3462 4.49432C10.272 4.49432 10.2009 4.46507 10.1485 4.41299C10.0961 4.36091 10.0666 4.29028 10.0666 4.21663C10.0666 4.14298 10.0961 4.07235 10.1485 4.02027C10.2009 3.96819 10.272 3.93894 10.3462 3.93894C13.5373 3.96093 16.1021 6.14857 16.1279 9.91896L16.1298 9.91988ZM15.6454 14.1338V14.1421C15.1786 14.9587 14.3049 15.8605 13.4054 15.5727L13.3971 15.5599C11.7931 14.9742 10.2898 14.146 8.94016 13.1046L8.97798 13.1321C8.30649 12.602 7.69714 11.9985 7.16144 11.3331L7.14391 11.3102C6.65881 10.7041 6.22612 10.0585 5.85046 9.38007L5.81356 9.30767C5.36897 8.57905 5.00019 7.80737 4.71293 7.00456L4.68525 6.91474C4.39556 6.02118 5.29876 5.15327 6.12539 4.68953H6.13369C6.29625 4.58592 6.49159 4.54555 6.68227 4.57616C6.87294 4.60677 7.04554 4.70621 7.16697 4.85542L7.16789 4.85725C7.16789 4.85725 7.70391 5.49237 7.93363 5.80672C8.14951 6.09908 8.44012 6.5674 8.5905 6.82859C8.70373 7.00707 8.7498 7.21956 8.7206 7.42851C8.69139 7.63747 8.58878 7.82944 8.4309 7.97053L8.42997 7.97144L7.91149 8.38386C7.83059 8.46133 7.76809 8.55573 7.72851 8.6602C7.68893 8.76467 7.67328 8.87659 7.68269 8.98782V8.98507C7.93949 9.8349 8.40288 10.6089 9.03197 11.2388C9.66106 11.8687 10.4365 12.3351 11.29 12.5969L11.3287 12.607C11.4401 12.6154 11.552 12.5995 11.6565 12.5604C11.7611 12.5213 11.8558 12.4599 11.9339 12.3806L12.3491 11.8656C12.492 11.7085 12.6862 11.6066 12.8973 11.578C13.1085 11.5493 13.3231 11.5956 13.5032 11.7088L13.4995 11.707C14.2496 12.1313 14.8981 12.5978 15.4895 13.1266L15.4794 13.1175C15.6294 13.2363 15.7292 13.4066 15.7593 13.5948C15.7893 13.7831 15.7474 13.9757 15.6417 14.1348L15.6436 14.132L15.6454 14.1338Z" fill="currentColor"/>
                                </svg>
                                @if($authChatUnread > 0)
                                    <span class="ev-cc-badge">{{ $authChatUnread > 99 ? '99+' : $authChatUnread }}</span>
                                @endif
                            </span>
                            <span class="ev-cc-label">Chat &amp; Calls</span>
                        </a>

                        {{-- Profile dropdown trigger --}}
                        <div class="ev-user-wrap" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" class="ev-user-trigger" @click="open = !open">
                                <span class="ev-user-avatar">
                                    @if($authAvatarUrl)
                                        <img src="{{ $authAvatarUrl }}" alt="" onerror="this.style.display='none'">
                                    @else
                                        {{ $authInitial }}
                                    @endif
                                </span>
                                <span class="ev-user-name">{{ $authName }}</span>
                                <svg class="ev-user-chev" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>

                            <div class="ev-user-menu" x-show="open" x-cloak x-transition.opacity.duration.100ms>
                                <div class="ev-user-menu-head">
                                    <span class="ev-user-avatar ev-user-avatar--lg">
                                        @if($authAvatarUrl)
                                            <img src="{{ $authAvatarUrl }}" alt="" onerror="this.style.display='none'">
                                        @else
                                            {{ $authInitial }}
                                        @endif
                                    </span>
                                    <div class="ev-user-menu-head-text">
                                        <div class="ev-user-menu-handle">{{ $authUser->username ?? $authName }}</div>
                                        <div class="ev-user-menu-email">{{ $authEmail }}</div>
                                    </div>
                                </div>
                                <div class="ev-user-menu-divider"></div>
                                <a href="{{ url('my-listings') }}" class="ev-user-menu-item">
                                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.75 14.75H10.75M4.75 11.75H10.75M14.749 6.75C14.746 6.465 14.735 6.284 14.695 6.111C14.645 5.90633 14.565 5.71367 14.455 5.533C14.331 5.331 14.159 5.159 13.813 4.813L10.687 1.688C10.342 1.342 10.169 1.169 9.967 1.045C9.788 0.935 9.592 0.855 9.388 0.805C9.216 0.764 9.036 0.753 8.75 0.75H3.95C2.83 0.75 2.27 0.75 1.842 0.968C1.46569 1.15974 1.15974 1.46569 0.968 1.842C0.75 2.27 0.75 2.83 0.75 3.95V15.55C0.75 16.67 0.75 17.23 0.968 17.658C1.15974 18.0343 1.46569 18.3403 1.842 18.532C2.269 18.75 2.829 18.75 3.947 18.75H11.553C12.671 18.75 13.23 18.75 13.657 18.532C14.034 18.34 14.34 18.034 14.532 17.658C14.75 17.23 14.75 16.672 14.75 15.554V7.076L14.749 6.75ZM8.75 0.75V3.55C8.75 4.67 8.75 5.23 8.968 5.658C9.15974 6.03431 9.46569 6.34026 9.842 6.532C10.269 6.75 10.829 6.75 11.947 6.75H14.749" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span>My Profiles</span>
                                    @if($authProfileCount > 0)
                                        <span class="ev-user-menu-badge">{{ $authProfileCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ url('my-chat') }}" class="ev-user-menu-item">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.9669 1.75232C15.4964 1.34365 13.5907 0.0299231 9.34656 0.0126766C9.34656 0.0126766 4.33856 -0.271515 1.89902 1.8513C0.542043 3.13803 0.0667855 5.0224 0.0122218 7.35967C-0.0423418 9.69694 -0.103232 14.0775 4.34964 15.2683H4.35359L4.34964 17.0822C4.34964 17.0822 4.32038 17.817 4.82964 17.9647C5.44486 18.1477 5.80862 17.5883 6.39775 16.9869C6.72039 16.6563 7.1648 16.1719 7.50246 15.8022C9.4375 15.9724 11.389 15.8312 13.2743 15.3845L13.1557 15.4085C13.771 15.2188 17.252 14.7966 17.815 10.4168C18.4033 5.89522 17.5351 3.0428 15.9669 1.75232ZM16.4825 10.0869C16.0025 13.7424 13.1826 13.9748 12.6639 14.1323C11.5315 14.4052 10.2314 14.5612 8.89265 14.5612C8.50781 14.5612 8.12691 14.5484 7.74998 14.5229L7.80059 14.5259C7.80059 14.5259 5.87267 16.7297 5.27247 17.3026C5.07636 17.4886 4.86048 17.4736 4.86443 17.1024C4.86443 16.8602 4.87946 14.0895 4.87946 14.0895C1.10982 13.0982 1.32808 9.367 1.37236 7.41815C1.41664 5.46931 1.80254 3.86763 2.95154 2.79236C5.01705 1.01747 9.26906 1.28067 9.26906 1.28067C12.8616 1.29491 14.5815 2.32145 14.9816 2.66563C16.3054 3.74166 16.9799 6.31588 16.4825 10.0869ZM11.3338 7.24644V7.25694C11.3321 7.31533 11.3069 7.37089 11.2633 7.41215C11.2196 7.4534 11.161 7.47721 11.0994 7.47865C11.0378 7.4801 10.978 7.45907 10.9323 7.4199C10.8866 7.38074 10.8585 7.32643 10.8538 7.26819C10.8699 7.11163 10.85 6.95287 10.7954 6.80432C10.7408 6.65578 10.6529 6.52022 10.5378 6.40701C10.4226 6.29381 10.2829 6.20565 10.1283 6.14862C9.97369 6.09159 9.80788 6.06704 9.64231 6.07668H9.64547C9.58402 6.07217 9.52674 6.04544 9.48547 6.00203C9.44421 5.95861 9.4221 5.90183 9.42372 5.8434C9.42535 5.78498 9.45058 5.72937 9.4942 5.68808C9.53782 5.64679 9.5965 5.62296 9.65812 5.62152H9.67157H9.67078L9.72534 5.62077C9.94611 5.6207 10.1645 5.66367 10.3671 5.747C10.5696 5.83033 10.7519 5.95225 10.9027 6.10519C11.0534 6.25813 11.1694 6.43882 11.2434 6.63605C11.3175 6.83327 11.3479 7.04282 11.333 7.25169L11.3338 7.24644ZM12.0858 7.64461C12.123 6.15391 11.1408 4.98715 9.27618 4.85668C9.24382 4.85572 9.21201 4.84858 9.18264 4.83567C9.15326 4.82277 9.12694 4.80438 9.10525 4.78159C9.08356 4.75881 9.06695 4.73211 9.0564 4.70308C9.04586 4.67406 9.0416 4.64332 9.0439 4.6127C9.04619 4.58209 9.05498 4.55222 9.06973 4.52491C9.08449 4.49759 9.10492 4.47338 9.12979 4.45373C9.15465 4.43407 9.18345 4.41939 9.21445 4.41055C9.24545 4.40171 9.27801 4.39889 9.31018 4.40227H9.30939H9.32283C9.76535 4.40226 10.2032 4.48802 10.6095 4.65429C11.0158 4.82056 11.382 5.06382 11.6855 5.36915C11.9891 5.67447 12.2236 6.03542 12.3747 6.42981C12.5258 6.8242 12.5903 7.24372 12.5642 7.6626L12.565 7.65436C12.5608 7.71247 12.5332 7.76685 12.4881 7.80631C12.4429 7.84578 12.3835 7.86734 12.322 7.86657C12.2606 7.8658 12.2018 7.84276 12.1577 7.80218C12.1137 7.7616 12.0876 7.70656 12.085 7.64836V7.64236L12.0858 7.64461ZM13.8255 8.11626V8.11776C13.8223 8.17589 13.7958 8.23065 13.7514 8.27082C13.707 8.31099 13.648 8.33353 13.5866 8.33382C13.5253 8.3341 13.4661 8.31212 13.4213 8.27236C13.3764 8.23261 13.3493 8.1781 13.3455 8.12001C13.3265 5.2541 11.3108 3.69367 8.86814 3.67717C8.80459 3.67717 8.74364 3.65324 8.69871 3.61063C8.65378 3.56802 8.62853 3.51023 8.62853 3.44997C8.62853 3.38971 8.65378 3.33192 8.69871 3.28931C8.74364 3.2467 8.80459 3.22277 8.86814 3.22277C11.6034 3.24076 13.8018 5.03065 13.8239 8.11551L13.8255 8.11626ZM13.4104 11.5641V11.5708C13.0102 12.2389 12.2614 12.9768 11.4904 12.7413L11.4832 12.7308C10.1084 12.2516 8.81982 11.574 7.66299 10.722L7.69541 10.7445C7.11985 10.3107 6.59755 9.81697 6.13837 9.27252L6.12335 9.25378C5.70755 8.75793 5.33668 8.22966 5.01468 7.6746L4.98305 7.61537C4.60197 7.01922 4.28588 6.38785 4.03965 5.731L4.01593 5.65752C3.76762 4.92642 4.54179 4.21631 5.25033 3.83689H5.25745C5.39679 3.75212 5.56422 3.71909 5.72766 3.74413C5.89109 3.76918 6.03903 3.85053 6.14312 3.97261L6.14391 3.97411C6.14391 3.97411 6.60335 4.49376 6.80025 4.75095C6.9853 4.99015 7.23439 5.37333 7.36329 5.58703C7.46034 5.73306 7.49983 5.90691 7.4748 6.07787C7.44977 6.24884 7.36182 6.40591 7.22648 6.52134L7.22569 6.52209L6.78128 6.85952C6.71194 6.92291 6.65836 7.00014 6.62444 7.08562C6.59051 7.17109 6.5771 7.26266 6.58516 7.35367V7.35142C6.80528 8.04674 7.20247 8.68001 7.74169 9.19537C8.28091 9.71074 8.94559 10.0924 9.6771 10.3066L9.71031 10.3148C9.80579 10.3217 9.90171 10.3087 9.99132 10.2767C10.0809 10.2447 10.1621 10.1945 10.2291 10.1296L10.5849 9.70818C10.7074 9.57967 10.8739 9.49633 11.0549 9.47287C11.2359 9.44941 11.4198 9.48734 11.5742 9.57996L11.571 9.57846C12.2139 9.92564 12.7698 10.3073 13.2767 10.74L13.268 10.7325C13.3966 10.8297 13.4822 10.969 13.5079 11.123C13.5337 11.277 13.4978 11.4346 13.4072 11.5648L13.4088 11.5626L13.4104 11.5641Z" fill="currentColor"/></svg>
                                    <span>Chat &amp; Calls</span>
                                </a>
                                <a href="{{ url('my-wallet') }}" class="ev-user-menu-item">
                                    <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.931 0.577833C9.05484 0.418946 9.20818 0.286822 9.38216 0.18911C9.55613 0.0913977 9.74728 0.0300377 9.94454 0.00858174C10.1418 -0.0128742 10.3412 0.00599992 10.5313 0.0641118C10.7214 0.122224 10.8984 0.218419 11.052 0.347129L15.945 4.44443C16.2141 4.6697 16.3986 4.98376 16.467 5.33298C16.5354 5.6822 16.4834 6.04494 16.32 6.35927C15.8922 6.22521 15.4473 6.1573 15 6.15778H14.55L14.997 5.63486L12.42 3.47701L10.2945 6.15932H8.361L11.2575 2.50498L10.1025 1.53756L6.5055 6.15778H4.5855L8.931 0.577833ZM12.75 13.8479C12.5511 13.8479 12.3603 13.9289 12.2197 14.0731C12.079 14.2174 12 14.413 12 14.6169C12 14.8209 12.079 15.0165 12.2197 15.1607C12.3603 15.3049 12.5511 15.3859 12.75 15.3859H14.25C14.4489 15.3859 14.6397 15.3049 14.7803 15.1607C14.921 15.0165 15 14.8209 15 14.6169C15 14.413 14.921 14.2174 14.7803 14.0731C14.6397 13.9289 14.4489 13.8479 14.25 13.8479H12.75ZM1.5 6.9268C1.5 6.72284 1.57902 6.52724 1.71967 6.38302C1.86032 6.2388 2.05109 6.15778 2.25 6.15778H3.087L4.2795 4.61976H2.25C1.65326 4.61976 1.08097 4.86282 0.65901 5.29548C0.237053 5.72813 0 6.31493 0 6.9268V16.1549C0 17.1747 0.395088 18.1527 1.09835 18.8738C1.80161 19.5949 2.75544 20 3.75 20H15C15.7956 20 16.5587 19.6759 17.1213 19.099C17.6839 18.5222 18 17.7398 18 16.924V10.7719C18 9.95604 17.6839 9.17363 17.1213 8.59676C16.5587 8.01989 15.7956 7.69581 15 7.69581H2.25C2.05109 7.69581 1.86032 7.61479 1.71967 7.47057C1.57902 7.32635 1.5 7.13075 1.5 6.9268ZM1.5 16.1549V9.1031C1.736 9.18923 1.986 9.23281 2.25 9.23383H15C15.3978 9.23383 15.7794 9.39587 16.0607 9.68431C16.342 9.97274 16.5 10.3639 16.5 10.7719V16.924C16.5 17.3319 16.342 17.7231 16.0607 18.0115C15.7794 18.2999 15.3978 18.462 15 18.462H3.75C3.15326 18.462 2.58097 18.2189 2.15901 17.7863C1.73705 17.3536 1.5 16.7668 1.5 16.1549Z" fill="currentColor"/></svg>
                                    <span>My Wallet</span>
                                    <span class="ev-user-menu-badge ev-user-menu-badge--money">${{ number_format($authWalletBalance, 0) }}</span>
                                </a>
                                <a href="{{ url('my-questions') }}" class="ev-user-menu-item">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.4062 8.5C15.4062 10.3317 14.6786 12.0883 13.3835 13.3835C12.0883 14.6786 10.3317 15.4062 8.5 15.4062C6.66835 15.4062 4.91172 14.6786 3.61654 13.3835C2.32137 12.0883 1.59375 10.3317 1.59375 8.5C1.59375 6.66835 2.32137 4.91172 3.61654 3.61654C4.91172 2.32137 6.66835 1.59375 8.5 1.59375C10.3317 1.59375 12.0883 2.32137 13.3835 3.61654C14.6786 4.91172 15.4062 6.66835 15.4062 8.5ZM17 8.5C17 10.7543 16.1045 12.9163 14.5104 14.5104C12.9163 16.1045 10.7543 17 8.5 17C6.24566 17 4.08365 16.1045 2.48959 14.5104C0.895533 12.9163 0 10.7543 0 8.5C0 6.24566 0.895533 4.08365 2.48959 2.48959C4.08365 0.895533 6.24566 0 8.5 0C10.7543 0 12.9163 0.895533 14.5104 2.48959C16.1045 4.08365 17 6.24566 17 8.5ZM5.23494 5.30187C4.93248 5.75804 4.78125 6.20783 4.78125 6.65125C4.78125 6.86729 4.87687 7.06775 5.06812 7.25263C5.25938 7.4375 5.49348 7.52958 5.77044 7.52887C6.24148 7.52887 6.56129 7.26467 6.72988 6.73625C6.90838 6.23121 7.12654 5.84871 7.38437 5.58875C7.64221 5.3295 8.04383 5.19987 8.58925 5.19987C9.05533 5.19987 9.43606 5.32844 9.73144 5.58556C10.0261 5.8434 10.1734 6.15931 10.1734 6.53331C10.1747 6.72094 10.1248 6.90536 10.0289 7.06669C9.93049 7.23046 9.81041 7.38021 9.67194 7.51188C9.44853 7.71646 9.21892 7.91418 8.98344 8.10475C8.62219 8.40438 8.3346 8.66292 8.12069 8.88037C7.90819 9.09854 7.73712 9.35106 7.6075 9.63794C7.26537 10.9608 9.04187 11.067 9.452 10.1224C9.50158 10.0318 9.57702 9.93119 9.67831 9.82069C9.78031 9.7109 9.9156 9.5834 10.0842 9.43819C10.5141 9.08016 10.937 8.71388 11.3528 8.33956C11.588 8.12281 11.7909 7.86427 11.9616 7.56394C12.138 7.24418 12.2267 6.88351 12.2188 6.51844C12.2188 6.0141 12.0686 5.5466 11.7683 5.11594C11.4686 4.68456 11.0436 4.34385 10.4933 4.09381C9.94288 3.84377 9.30821 3.71875 8.58925 3.71875C7.81575 3.71875 7.13894 3.86856 6.55881 4.16819C5.97869 4.46781 5.5374 4.84642 5.23494 5.30187ZM7.50869 12.8244C7.50869 13.1062 7.62063 13.3764 7.81989 13.5757C8.01914 13.7749 8.2894 13.8869 8.57119 13.8869C8.85298 13.8869 9.12323 13.7749 9.32249 13.5757C9.52175 13.3764 9.63369 13.1062 9.63369 12.8244C9.63369 12.5426 9.52175 12.2723 9.32249 12.0731C9.12323 11.8738 8.85298 11.7619 8.57119 11.7619C8.2894 11.7619 8.01914 11.8738 7.81989 12.0731C7.62063 12.2723 7.50869 12.5426 7.50869 12.8244Z" fill="currentColor"/></svg>
                                    <span>Questions</span>
                                </a>
                                <a href="{{ url('my-reviews') }}" class="ev-user-menu-item">
                                    <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.25 10.3192H5.63125C5.74458 10.3192 5.85452 10.2977 5.96105 10.2547C6.06758 10.2117 6.16307 10.1472 6.2475 10.0612L10.2425 6.01952C10.37 5.89053 10.4658 5.74348 10.5298 5.57837C10.5938 5.41327 10.6256 5.25217 10.625 5.09509C10.6244 4.93801 10.589 4.78408 10.5187 4.63331C10.4485 4.48253 10.3564 4.34265 10.2425 4.21366L9.4775 3.39673C9.35 3.26774 9.20833 3.17114 9.0525 3.10693C8.89667 3.04272 8.73375 3.01033 8.56375 3.00976C8.40792 3.00976 8.24868 3.04215 8.08605 3.10693C7.92342 3.17171 7.77807 3.26831 7.65 3.39673L3.655 7.4384C3.57 7.5244 3.50625 7.62128 3.46375 7.72906C3.42125 7.83684 3.4 7.94777 3.4 8.06185V9.45924C3.4 9.70289 3.4816 9.90727 3.6448 10.0724C3.808 10.2375 4.00973 10.3197 4.25 10.3192ZM4.675 9.02928V8.21234L6.82125 6.04102L7.24625 6.42799L7.62875 6.85795L5.4825 9.02928H4.675ZM7.79875 10.3192H12.75C12.9908 10.3192 13.1928 10.2366 13.356 10.0715C13.5192 9.90641 13.6006 9.70232 13.6 9.45924C13.5994 9.21617 13.5178 9.01208 13.3552 8.84697C13.1926 8.68186 12.9908 8.59931 12.75 8.59931H9.49875L7.79875 10.3192ZM3.4 13.7589L1.445 15.7367C1.17583 16.0091 0.867568 16.0701 0.520201 15.9199C0.172835 15.7697 -0.000565282 15.5008 1.38436e-06 15.1133V1.71986C1.38436e-06 1.2469 0.166601 0.842159 0.499801 0.50564C0.833001 0.16912 1.23307 0.000573287 1.7 0H15.3C15.7675 0 16.1678 0.168547 16.501 0.50564C16.8342 0.842733 17.0006 1.24747 17 1.71986V12.039C17 12.512 16.8337 12.917 16.501 13.2541C16.1684 13.5912 15.7681 13.7595 15.3 13.7589H3.4ZM2.6775 12.039H15.3V1.71986H1.7V13.0065L2.6775 12.039Z" fill="currentColor"/></svg>
                                    <span>Reviews</span>
                                </a>
                                <a href="{{ url('my-favorites') }}" class="ev-user-menu-item">
                                    <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.585 13.5586L8.5 13.6458L8.4065 13.5586C4.369 9.80054 1.7 7.31553 1.7 4.79564C1.7 3.05177 2.975 1.74387 4.675 1.74387C5.984 1.74387 7.259 2.6158 7.7095 3.80164H9.2905C9.741 2.6158 11.016 1.74387 12.325 1.74387C14.025 1.74387 15.3 3.05177 15.3 4.79564C15.3 7.31553 12.631 9.80054 8.585 13.5586ZM12.325 0C10.846 0 9.4265 0.706267 8.5 1.81362C7.5735 0.706267 6.154 0 4.675 0C2.057 0 0 2.10136 0 4.79564C0 8.08283 2.89 10.7771 7.2675 14.849L8.5 16L9.7325 14.849C14.11 10.7771 17 8.08283 17 4.79564C17 2.10136 14.943 0 12.325 0Z" fill="currentColor"/></svg>
                                    <span>My Favorites</span>
                                </a>
                                <div class="ev-user-menu-divider"></div>
                                <a href="{{ url('my-account') }}" class="ev-user-menu-item">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 15C1 13.9391 1.42143 12.9217 2.17157 12.1716C2.92172 11.4214 3.93913 11 5 11H13C14.0609 11 15.0783 11.4214 15.8284 12.1716C16.5786 12.9217 17 13.9391 17 15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17H3C2.46957 17 1.96086 16.7893 1.58579 16.4142C1.21071 16.0391 1 15.5304 1 15Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 7C10.6569 7 12 5.65685 12 4C12 2.34315 10.6569 1 9 1C7.34315 1 6 2.34315 6 4C6 5.65685 7.34315 7 9 7Z" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Account</span>
                                </a>
                                <a href="{{ url('my-account/newsletter') }}" class="ev-user-menu-item">
                                    <svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 2.36364V10.6364C17 10.9626 16.9447 11.2673 16.834 11.5504C16.7233 11.8336 16.5711 12.0829 16.3774 12.2983C16.1838 12.5137 15.9569 12.683 15.6968 12.8061C15.4367 12.9292 15.1628 12.9938 14.875 13H2.06689C1.78467 13 1.51904 12.9384 1.27002 12.8153C1.021 12.6922 0.802409 12.5291 0.614258 12.326C0.426107 12.1229 0.276693 11.8797 0.166016 11.5966C0.0553385 11.3134 0 11.0149 0 10.701V0H14.875V2.36364H17ZM15.9375 3.54545H14.875V10.0455C14.875 10.2055 14.8224 10.344 14.7173 10.4609C14.6121 10.5779 14.4876 10.6364 14.3438 10.6364C14.1999 10.6364 14.0754 10.5779 13.9702 10.4609C13.8651 10.344 13.8125 10.2055 13.8125 10.0455V1.18182H1.0625V10.701C1.0625 10.8549 1.0874 10.9995 1.13721 11.1349C1.18701 11.2704 1.25895 11.3873 1.35303 11.4858C1.4471 11.5843 1.55501 11.6643 1.67676 11.7259C1.7985 11.7874 1.92855 11.8182 2.06689 11.8182H14.875C15.0244 11.8182 15.1628 11.7874 15.29 11.7259C15.4173 11.6643 15.528 11.5812 15.6221 11.4766C15.7161 11.3719 15.7936 11.2457 15.8545 11.098C15.9154 10.9503 15.943 10.7964 15.9375 10.6364V3.54545ZM12.75 3.54545H2.125V2.36364H12.75V3.54545ZM12.75 10.6364H8.5V9.45455H12.75V10.6364ZM12.75 8.27273H8.5V7.09091H12.75V8.27273ZM12.75 5.90909H8.5V4.72727H12.75V5.90909ZM7.4375 10.6364H2.125V4.69957H7.4375V10.6364ZM3.1875 9.45455H6.375V5.88139H3.1875V9.45455Z" fill="currentColor"/></svg>
                                    <span>Newsletter</span>
                                </a>
                                <form method="post" action="{{ url('sign_out') }}" style="margin:0">
                                    {{ csrf_field() }}
                                    <button type="submit" class="ev-user-menu-item ev-user-menu-item--btn">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.77778 16C1.28889 16 0.870519 15.8261 0.522667 15.4782C0.174815 15.1304 0.000592593 14.7117 0 14.2222V1.77778C0 1.28889 0.174222 0.870519 0.522667 0.522667C0.871111 0.174815 1.28948 0.000592593 1.77778 0H8V1.77778H1.77778V14.2222H8V16H1.77778ZM11.5556 12.4444L10.3333 11.1556L12.6 8.88889H5.33333V7.11111H12.6L10.3333 4.84444L11.5556 3.55556L16 8L11.5556 12.4444Z" fill="currentColor"/></svg>
                                        <span>Log out</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Add Profile CTA. No wire:navigate — the add-profile
                             page uses a different layout stylesheet and the
                             soft-swap back to /my-chat occasionally drops the
                             chat's panel background CSS vars; a full page load
                             avoids that. --}}
                        <a href="{{ route('new.profile') }}" class="ev-add-profile-btn">
                            Add Profile
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    @else
                        <form method="post" action="{{ url('sign_out') }}" style="display:inline">
                            {{ csrf_field() }}
                            <button type="submit" class="ev-nav-link" style="padding: 12px 20px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('sign-in') }}" class="ev-nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Sign in
                    </a>
                @endauth
            </nav>
            
            {{-- Mobile Auth/Dashboard Button --}}
            @auth
                @php
                    $mobileAuthHref = Auth::user()->type == 1 ? url('admin/dashboard') : url('my-account');
                @endphp
                <a href="{{ $mobileAuthHref }}" class="ev-mobile-auth-btn" aria-label="My Account" wire:navigate>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
            @else
                <a href="{{ route('help') }}" class="ev-mobile-auth-btn" aria-label="Sign in">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
            @endauth

        </div>
    </div>
</header>
