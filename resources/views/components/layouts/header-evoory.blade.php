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
@endphp

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
                        @php
                            $userProfile = auth()->user()->profiles->first();
                            // Always surface a "My Profile" entry in the header, even for
                            // brand-new accounts that haven't created a profile yet — the
                            // link then points to the create-profile flow so users have a
                            // clear path forward instead of a missing menu item.
                            $myProfileHref = $userProfile
                                ? url('my-profile/'.$userProfile->slug.'/'.$userProfile->id)
                                : route('new.profile');
                        @endphp
                        <a href="{{ $myProfileHref }}" class="ev-nav-link" wire:navigate>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            My Profile
                        </a>
                        <a href="{{ url('my-account') }}" class="ev-nav-link" wire:navigate>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                            My Account
                        </a>
                    @endif
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
                <a href="{{ route('sign-in') }}" class="ev-mobile-auth-btn" aria-label="Sign in">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
            @endauth

        </div>
    </div>
</header>
