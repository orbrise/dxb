@php
    $__currentRoute = request()->route() ? request()->route()->getName() : '';
    $__path = request()->path();
    $__isHome      = $__currentRoute === 'newhome' || $__path === '/' || $__path === '';
    $__isChats     = str_starts_with($__currentRoute ?? '', 'user.chat');
    $__isAddProfile= $__currentRoute === 'new.profile';
    $__isFavorites = $__currentRoute === 'favorites.dashboard';
    $__isMenu      = $__currentRoute === 'user.account';

    // Auth-gated links: when the visitor is a guest, send them to /help
    // (which has Log In / Sign up). Once they authenticate, the auth
    // middleware redirects back to whatever they originally requested.
    $__isAuth = auth()->check();
    $__chatsHref       = $__isAuth ? route('user.chat')          : route('help');
    $__addProfileHref  = $__isAuth ? route('new.profile')        : route('help');
    $__favoritesHref   = $__isAuth ? route('favorites.dashboard'): route('help');
    $__menuHref        = $__isAuth ? url('/my-account')          : route('help');
@endphp
<style>
.ev-mobile-bottom-nav {
    display: none;
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: auto !important;
    width: 100vw !important;
    max-width: 100vw !important;
    z-index: 100;
    background: #C1F11D26;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1px solid rgba(193, 241, 29, 0.25);
    padding: 8px 6px !important;
    padding-bottom: max(8px, env(safe-area-inset-bottom)) !important;
    box-sizing: border-box !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    grid-auto-flow: column !important;
    gap: 0 !important;
}
.ev-mobile-bottom-nav__item {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 0 !important;
    width: 100% !important;
    gap: 4px;
    padding: 6px 4px;
    color: #fff !important;
    text-decoration: none !important;
    font-size: 11px;
    border-radius: 8px;
    line-height: 1;
    transition: color 0.15s ease;
    box-sizing: border-box !important;
}
.ev-mobile-bottom-nav__item svg {
    width: 22px !important;
    height: 22px !important;
    transform: none !important;
    -webkit-transform: none !important;
    margin: 0 !important;
    display: block;
}
.ev-mobile-bottom-nav__label {
    color: inherit;
    font-size: 11px;
    line-height: 1;
    white-space: nowrap;
}
.ev-mobile-bottom-nav__item:hover,
.ev-mobile-bottom-nav__item:focus,
.ev-mobile-bottom-nav__item:active,
.ev-mobile-bottom-nav__item.is-active,
.ev-mobile-bottom-nav__item.is-active:hover,
.ev-mobile-bottom-nav__item.is-active:focus { color: #C1F11D !important; }

@media (max-width: 768px) {
    .ev-mobile-bottom-nav { display: grid !important; }
    /* Reserve space so the fixed bar doesn't cover the last page row. */
    body { padding-bottom: calc(64px + env(safe-area-inset-bottom)) !important; }
    /* Hide the global site footer on mobile — the bottom nav above
       handles primary navigation, and the footer's small text links
       (About, Advertise, Help, Stop Human Trafficking, copyright, etc.)
       are accessible via the Menu tab and individual pages instead. */
    .ev-footer,
    #footer,
    footer.ev-footer { display: none !important; }
    /* Hide the legacy top-right person icon — its destination (Help /
       My Account) is reachable from the bottom nav's Menu tab now. */
    .ev-mobile-auth-btn { display: none !important; }
    /* Headers scroll with the page on mobile instead of sticking. The
       evoory-theme.css default is `position: sticky; top: 0` for all
       three header variants — override to static so they leave the
       viewport on scroll. */
    .ev-header,
    .ev-header-simple,
    .ev-header-account { position: static !important; top: auto !important; }
}
</style>

<nav class="ev-mobile-bottom-nav" aria-label="Primary mobile navigation">
    {{-- Home --}}
    <a href="{{ url('/') }}" class="ev-mobile-bottom-nav__item {{ $__isHome ? 'is-active' : '' }}" wire:navigate aria-label="Home">
        <svg viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.750244 8.13111V10.0833C0.750244 12.65 0.750244 13.9333 1.54747 14.7306C2.34469 15.5278 3.62802 15.5278 6.19469 15.5278H9.3058C11.8725 15.5278 13.1558 15.5278 13.953 14.7306C14.7502 13.9333 14.7502 12.65 14.7502 10.0833V8.13111C14.7502 6.82289 14.7502 6.16956 14.4734 5.60333C14.1965 5.03711 13.68 4.63578 12.6487 3.83311L11.0931 2.62367C9.48702 1.37456 8.68358 0.75 7.75024 0.75C6.81691 0.75 6.01347 1.37456 4.40736 2.62367L2.8518 3.83311C1.81969 4.63578 1.30402 5.03711 1.02713 5.60333C0.750244 6.16956 0.750244 6.82289 0.750244 8.13111Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10.0837 12.0273C9.46144 12.5111 8.64477 12.8051 7.75033 12.8051C6.85588 12.8051 6.03921 12.5111 5.41699 12.0273" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="ev-mobile-bottom-nav__label">Home</span>
    </a>

    {{-- Chats --}}
    <a href="{{ $__chatsHref }}" class="ev-mobile-bottom-nav__item {{ $__isChats ? 'is-active' : '' }}" wire:navigate aria-label="Chats">
        <svg viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.25 15.75C12.3923 15.75 15.75 12.3923 15.75 8.25C15.75 4.10775 12.3923 0.75 8.25 0.75C4.10775 0.75 0.75 4.10775 0.75 8.25C0.75 9.45 1.032 10.584 1.53225 11.5898C1.66575 11.8568 1.71 12.162 1.63275 12.4508L1.1865 14.1203C1.1423 14.2855 1.14234 14.4594 1.18662 14.6246C1.23089 14.7898 1.31784 14.9405 1.43874 15.0614C1.55963 15.1824 1.71022 15.2694 1.8754 15.3138C2.04057 15.3582 2.2145 15.3583 2.37975 15.3143L4.04925 14.8673C4.33904 14.794 4.6456 14.8295 4.911 14.967C5.94821 15.4834 7.09134 15.7515 8.25 15.75Z" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="5.5" cy="8.25" r="0.9" fill="currentColor"/>
            <circle cx="8.25" cy="8.25" r="0.9" fill="currentColor"/>
            <circle cx="11" cy="8.25" r="0.9" fill="currentColor"/>
        </svg>
        <span class="ev-mobile-bottom-nav__label">Chats</span>
    </a>

    {{-- Add Profile --}}
    <a href="{{ $__addProfileHref }}" class="ev-mobile-bottom-nav__item {{ $__isAddProfile ? 'is-active' : '' }}" wire:navigate aria-label="Add Profile">
        <svg viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 2.3375C0 1.71756 0.246272 1.123 0.684638 0.684638C1.123 0.246272 1.71756 0 2.3375 0H12.9625C13.5824 0 14.177 0.246272 14.6154 0.684638C15.0537 1.123 15.3 1.71756 15.3 2.3375V7.6687C14.9025 7.41418 14.474 7.21165 14.025 7.06605V2.3375C14.025 1.751 13.549 1.275 12.9625 1.275H2.3375C1.751 1.275 1.275 1.751 1.275 2.3375V12.9625C1.275 13.549 1.751 14.025 2.3375 14.025H7.06605C7.2131 14.4789 7.41625 14.9065 7.6687 15.3H2.3375C1.71756 15.3 1.123 15.0537 0.684638 14.6154C0.246272 14.177 0 13.5824 0 12.9625V2.3375ZM17 12.325C17 11.0851 16.5075 9.89601 15.6307 9.01928C14.754 8.14254 13.5649 7.65 12.325 7.65C11.0851 7.65 9.89601 8.14254 9.01928 9.01928C8.14254 9.89601 7.65 11.0851 7.65 12.325C7.65 13.5649 8.14254 14.754 9.01928 15.6307C9.89601 16.5075 11.0851 17 12.325 17C13.5649 17 14.754 16.5075 15.6307 15.6307C16.5075 14.754 17 13.5649 17 12.325ZM12.75 12.75L12.7508 14.8776C12.7508 14.9903 12.7061 15.0984 12.6264 15.1781C12.5467 15.2578 12.4386 15.3026 12.3258 15.3026C12.2131 15.3026 12.105 15.2578 12.0253 15.1781C11.9456 15.0984 11.9009 14.9903 11.9009 14.8776V12.75H9.7716C9.65888 12.75 9.55078 12.7052 9.47108 12.6255C9.39138 12.5458 9.3466 12.4377 9.3466 12.325C9.3466 12.2123 9.39138 12.1042 9.47108 12.0245C9.55078 11.9448 9.65888 11.9 9.7716 11.9H11.9V9.775C11.9 9.66228 11.9448 9.55418 12.0245 9.47448C12.1042 9.39478 12.2123 9.35 12.325 9.35C12.4377 9.35 12.5458 9.39478 12.6255 9.47448C12.7052 9.55418 12.75 9.66228 12.75 9.775V11.9H14.8724C14.9852 11.9 15.0933 11.9448 15.173 12.0245C15.2527 12.1042 15.2975 12.2123 15.2975 12.325C15.2975 12.4377 15.2527 12.5458 15.173 12.6255C15.0933 12.7052 14.9852 12.75 14.8724 12.75H12.75Z" fill="currentColor"/>
        </svg>
        <span class="ev-mobile-bottom-nav__label">Add Profile</span>
    </a>

    {{-- Favorite --}}
    <a href="{{ $__favoritesHref }}" class="ev-mobile-bottom-nav__item {{ $__isFavorites ? 'is-active' : '' }}" wire:navigate aria-label="Favorites">
        <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.6349 9.1803C15.3401 6.6523 14.9081 4.75673 14.5081 3.4102C14.1805 2.30536 14.0163 1.75295 13.3443 1.25189C12.6723 0.750842 11.9852 0.75 10.6109 0.75H6.04582C4.67151 0.75 3.98436 0.75 3.31236 1.25189C2.64036 1.75295 2.47615 2.30536 2.14857 3.4102C1.74857 4.75673 1.31741 6.6523 1.02268 9.1803C0.674889 12.1605 0.500574 13.6502 1.5052 14.7786C2.50983 15.9079 4.13762 15.9079 7.39487 15.9079H9.26098" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5.80176 4.11816C5.80176 4.78818 6.06792 5.43076 6.5417 5.90454C7.01547 6.37831 7.65805 6.64447 8.32807 6.64447C8.99809 6.64447 9.64066 6.37831 10.1144 5.90454C10.5882 5.43076 10.8544 4.78818 10.8544 4.11816M13.8017 16.7497C13.8017 16.7497 10.8544 14.9661 10.8544 13.2407C10.8544 12.3885 11.475 11.6971 12.3281 11.6971C12.7702 11.6971 13.2123 11.8461 13.8017 12.4398C14.3912 11.8453 14.8333 11.6971 15.2754 11.6971C16.1285 11.6971 16.7491 12.3876 16.7491 13.2407C16.7491 14.967 13.8017 16.7497 13.8017 16.7497Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="ev-mobile-bottom-nav__label">Favorite</span>
    </a>

    {{-- Menu --}}
    <a href="{{ $__menuHref }}" class="ev-mobile-bottom-nav__item {{ $__isMenu ? 'is-active' : '' }}" wire:navigate aria-label="Menu">
        <svg viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.71387 9.7002H15.7861C16.0819 9.7003 16.3654 9.81614 16.5742 10.0225C16.783 10.2288 16.9003 10.5085 16.9004 10.7998C16.9004 11.0911 16.7829 11.3708 16.5742 11.5771C16.3654 11.7835 16.0819 11.9003 15.7861 11.9004H9.71387C9.41811 11.9003 9.13456 11.7835 8.92578 11.5771C8.71707 11.3708 8.59961 11.0911 8.59961 10.7998C8.59966 10.5085 8.717 10.2288 8.92578 10.0225C9.13456 9.81614 9.41811 9.7003 9.71387 9.7002ZM1.21387 4.90039H15.7861C16.0819 4.9005 16.3654 5.01633 16.5742 5.22266C16.783 5.42903 16.9004 5.7087 16.9004 6C16.9004 6.2913 16.783 6.57097 16.5742 6.77734C16.3654 6.98367 16.0819 7.0995 15.7861 7.09961H1.21387C0.918112 7.0995 0.634563 6.98367 0.425781 6.77734C0.217011 6.57097 0.0996094 6.2913 0.0996094 6C0.0996094 5.7087 0.217011 5.42903 0.425781 5.22266C0.634563 5.01633 0.918112 4.9005 1.21387 4.90039ZM1.21387 0.0996094H7.28613C7.58189 0.0997189 7.86544 0.216526 8.07422 0.422852C8.28293 0.629215 8.40039 0.908939 8.40039 1.2002C8.40034 1.49147 8.283 1.7712 8.07422 1.97754C7.86544 2.18386 7.58189 2.2997 7.28613 2.2998H1.21387C0.918112 2.2997 0.634563 2.18386 0.425781 1.97754C0.216996 1.7712 0.0996617 1.49147 0.0996094 1.2002C0.0996094 0.908939 0.217072 0.629215 0.425781 0.422852C0.634563 0.216526 0.918112 0.0997188 1.21387 0.0996094Z" fill="currentColor"/>
        </svg>
        <span class="ev-mobile-bottom-nav__label">Menu</span>
    </a>
</nav>
