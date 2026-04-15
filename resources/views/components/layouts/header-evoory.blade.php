{{-- Evoory Theme Header - Optimized --}}
<header class="ev-header">
    <div class="ev-container">
        <div class="ev-flex ev-items-center ev-justify-between">
            {{-- Logo + Tabs grouped together --}}
            <div class="ev-flex ev-items-center">
                @if(isset($setting) && $setting->app_logo)
                <a href="/" class="ev-logo"><img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}" style="height:36px;width:auto;display:block;"></a>
                @else
                <a href="/" class="ev-logo">{{ $setting->app_name ?? 'evoory' }}</a>
                @endif

                {{-- Escorts / What's New Buttons --}}
                @php
                    $citySlug = function_exists('getFeaturedCitySlug') ? getFeaturedCitySlug() : 'dubai';
                    $currentRoute = request()->route() ? request()->route()->getName() : '';
                    $currentPath = request()->path();
                    $isHomePage = $currentRoute === 'home' || $currentPath === '/' || str_contains($currentPath, 'female-escorts-in-');
                    $isNewsPage = in_array($currentRoute, ['news.all', 'news.page']) || str_contains($currentPath, 'female-escort-news-in-');
                @endphp
                @if($isHomePage || $isNewsPage)
                <div class="ev-header-tabs">
                    <a href="/female-escorts-in-{{ $citySlug }}" class="ev-header-tab {{ $isHomePage ? 'active' : '' }}" wire:navigate>ESCORTS</a>
                    <a href="/female-escort-news-in-{{ $citySlug }}" class="ev-header-tab {{ $isNewsPage ? 'active' : '' }}" wire:navigate>WHAT'S NEW</a>
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
                        <a href="?lang=ar" class="ev-dropdown-item">العربية</a>
                        <a href="?lang=ru" class="ev-dropdown-item">Русский</a>
                        <a href="?lang=zh" class="ev-dropdown-item">中文</a>
                    </div>
                </div>
                @endguest

                {{-- Sign In --}}
                @auth
                    @if(Auth::user()->type != 1)
                        @php
                            $userProfile = auth()->user()->profiles->first();
                        @endphp
                        @if($userProfile)
                        <a href="{{ url('my-profile/'.$userProfile->slug.'/'.$userProfile->id) }}" class="ev-nav-link" wire:navigate>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            My Profile
                        </a>
                        @endif
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
