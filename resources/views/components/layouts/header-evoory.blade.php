{{-- Evoory Theme Header - Optimized --}}
<header class="ev-header">
    <div class="ev-container">
        <div class="ev-flex ev-items-center ev-justify-between">
            {{-- Logo --}}
            <a href="/" class="ev-logo">evoory</a>
            
            {{-- Desktop Navigation --}}
            <nav class="ev-nav">
                {{-- Language Selector --}}
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
                        <button type="submit" class="ev-nav-link" style="background:none;border:none;cursor:pointer;font:inherit;color:inherit;padding:inherit;">Sign Out</button>
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
            
            {{-- Mobile Menu Button --}}
            <button class="ev-btn ev-btn-icon ev-btn-dark ev-mobile-menu-btn" type="button" aria-label="Menu" aria-expanded="false" style="display:none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
    
    {{-- Mobile Menu --}}
    <div class="ev-mobile-menu" style="display:none">
        <div class="ev-container">
            <nav class="ev-flex ev-flex-col ev-gap-2" style="padding:16px 0">
                <a href="/" class="ev-nav-link">Home</a>
                <a href="{{ route('new.profile') }}" class="ev-nav-link">List Now</a>
                @auth
                    @if(Auth::user()->type != 1)
                        @php
                            $mobileUserProfile = auth()->user()->profiles->first();
                        @endphp
                        @if($mobileUserProfile)
                        <a href="{{ url('my-profile/'.$mobileUserProfile->slug.'/'.$mobileUserProfile->id) }}" class="ev-nav-link" wire:navigate>My Profile</a>
                        @endif
                        <a href="{{ url('my-account') }}" class="ev-nav-link" wire:navigate>My Account</a>
                    @endif
                    <form method="post" action="{{ url('sign_out') }}">
                        {{ csrf_field() }}
                        <button type="submit" class="ev-nav-link" style="background:none;border:none;cursor:pointer;font:inherit;color:inherit;padding:inherit;text-align:left;width:100%;">Sign Out</button>
                    </form>
                @else
                    <a href="{{ route('sign-in') }}" class="ev-nav-link">Sign In</a>
                @endauth
            </nav>
        </div>
    </div>
</header>
