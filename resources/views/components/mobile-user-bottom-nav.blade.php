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

    // Chats badge: unread messages + unseen missed calls. Shared helper
    // deduplicates this across the mobile nav / desktop header / dashboard
    // nav and caches ~30s so these counts stop firing a DB query on every
    // page load (which was what regressed home + listings performance).
    $__chatsBadge = ($__isAuth && !$__isChats)
        ? (int) \App\Support\ChatBadges::for(auth()->id())['total']
        : 0;
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
/* Icon slot wrapper — gives the badge an anchor positioned over the icon. */
.ev-mobile-bottom-nav__icon { position: relative; display: inline-flex; }
.ev-mobile-bottom-nav__badge {
    position: absolute;
    top: -6px;
    right: -9px;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    border-radius: 999px;
    background: #ff3838;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    line-height: 16px;
    text-align: center;
    box-shadow: 0 0 0 2px #1a1e1b; /* ring to pop against the lime glass bg */
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

    {{-- Chat & Calls — uses the same Viber-style icon as the desktop
         header's "Chat & Calls" button for consistency across viewports. --}}
    <a href="{{ $__chatsHref }}" class="ev-mobile-bottom-nav__item {{ $__isChats ? 'is-active' : '' }}" wire:navigate aria-label="Chat & Calls{{ $__chatsBadge > 0 ? ' ('.$__chatsBadge.' unread)' : '' }}">
        <span class="ev-mobile-bottom-nav__icon">
            <svg viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.6281 2.14172C18.0792 1.64224 15.8558 0.0365726 10.9043 0.0154936C10.9043 0.0154936 5.06166 -0.331852 2.21552 2.2627C0.632383 3.83538 0.0779164 6.13849 0.0142588 8.99515C-0.0493988 11.8518 -0.120437 17.2059 5.07457 18.6612H5.07919L5.07457 20.8782C5.07457 20.8782 5.04044 21.7764 5.63458 21.9569C6.35234 22.1805 6.77672 21.4968 7.46404 20.7618C7.84045 20.3576 8.35894 19.7656 8.75288 19.3138C11.0104 19.5219 13.2871 19.3493 15.4867 18.8033L15.3484 18.8326C16.0661 18.6008 20.1273 18.0848 20.7842 12.7316C21.4706 7.20527 20.4576 3.71898 18.6281 2.14172ZM19.2296 12.3284C18.6696 16.7962 15.3797 17.0803 14.7745 17.2728C13.4534 17.6064 11.9367 17.797 10.3748 17.797C9.92577 17.797 9.4814 17.7814 9.04164 17.7503L9.10068 17.7539C9.10068 17.7539 6.85145 20.4475 6.15122 21.1477C5.92242 21.3749 5.67056 21.3566 5.67517 20.903C5.67517 20.6069 5.6927 17.2205 5.6927 17.2205C1.29479 16.009 1.54942 11.4486 1.60109 9.06663C1.65275 6.68471 2.10297 4.72711 3.44346 3.41288C5.85323 1.24357 10.8139 1.56526 10.8139 1.56526C15.0052 1.58267 17.0118 2.83733 17.4786 3.25799C19.023 4.57314 19.8099 7.71941 19.2296 12.3284ZM13.2227 8.85676V8.86959C13.2208 8.94096 13.1913 9.00887 13.1405 9.05929C13.0896 9.10971 13.0212 9.13881 12.9493 9.14058C12.8775 9.14234 12.8077 9.11664 12.7543 9.06877C12.701 9.0209 12.6682 8.95452 12.6627 8.88334C12.6815 8.69199 12.6583 8.49796 12.5946 8.3164C12.531 8.13484 12.4284 7.96916 12.2941 7.8308C12.1597 7.69243 11.9967 7.58468 11.8163 7.51498C11.636 7.44527 11.4425 7.41527 11.2494 7.42705H11.253C11.1814 7.42154 11.1145 7.38887 11.0664 7.33581C11.0182 7.28275 10.9924 7.21335 10.9943 7.14194C10.9962 7.07053 11.0257 7.00257 11.0766 6.9521C11.1275 6.90163 11.1959 6.87251 11.2678 6.87075H11.2835H11.2826L11.3462 6.86983C11.6038 6.86975 11.8586 6.92226 12.0949 7.02411C12.3312 7.12596 12.5439 7.27497 12.7198 7.4619C12.8957 7.64882 13.031 7.86967 13.1173 8.11072C13.2037 8.35178 13.2393 8.6079 13.2218 8.86318L13.2227 8.85676ZM14.1001 9.34341C14.1435 7.52145 12.9976 6.09541 10.8222 5.93594C10.7845 5.93477 10.7473 5.92604 10.7131 5.91027C10.6788 5.8945 10.6481 5.87202 10.6228 5.84417C10.5975 5.81632 10.5781 5.78368 10.5658 5.74821C10.5535 5.71274 10.5485 5.67517 10.5512 5.63775C10.5539 5.60033 10.5641 5.56383 10.5814 5.53044C10.5986 5.49705 10.6224 5.46746 10.6514 5.44344C10.6804 5.41942 10.714 5.40147 10.7502 5.39067C10.7864 5.37986 10.8243 5.37642 10.8619 5.38056H10.861H10.8766C11.3929 5.38054 11.9037 5.48536 12.3778 5.68857C12.8518 5.89179 13.279 6.18911 13.6331 6.56229C13.9873 6.93547 14.2609 7.37662 14.4372 7.85865C14.6135 8.34069 14.6887 8.85344 14.6583 9.36541L14.6592 9.35532C14.6542 9.42636 14.6221 9.49281 14.5694 9.54105C14.5167 9.58928 14.4474 9.61564 14.3757 9.6147C14.304 9.61376 14.2354 9.5856 14.184 9.536C14.1326 9.4864 14.1022 9.41913 14.0992 9.34799V9.34066L14.1001 9.34341ZM16.1298 9.91988V9.92171C16.126 9.99275 16.0951 10.0597 16.0433 10.1088C15.9914 10.1579 15.9227 10.1854 15.8511 10.1858C15.7795 10.1861 15.7104 10.1593 15.6581 10.1107C15.6058 10.0621 15.5742 9.99546 15.5698 9.92446C15.5476 6.42168 13.196 4.51449 10.3462 4.49432C10.272 4.49432 10.2009 4.46507 10.1485 4.41299C10.0961 4.36091 10.0666 4.29028 10.0666 4.21663C10.0666 4.14298 10.0961 4.07235 10.1485 4.02027C10.2009 3.96819 10.272 3.93894 10.3462 3.93894C13.5373 3.96093 16.1021 6.14857 16.1279 9.91896L16.1298 9.91988ZM15.6454 14.1338V14.1421C15.1786 14.9587 14.3049 15.8605 13.4054 15.5727L13.3971 15.5599C11.7931 14.9742 10.2898 14.146 8.94016 13.1046L8.97798 13.1321C8.30649 12.602 7.69714 11.9985 7.16144 11.3331L7.14391 11.3102C6.65881 10.7041 6.22612 10.0585 5.85046 9.38007L5.81356 9.30767C5.36897 8.57905 5.00019 7.80737 4.71293 7.00456L4.68525 6.91474C4.39556 6.02118 5.29876 5.15327 6.12539 4.68953H6.13369C6.29625 4.58592 6.49159 4.54555 6.68227 4.57616C6.87294 4.60677 7.04554 4.70621 7.16697 4.85542L7.16789 4.85725C7.16789 4.85725 7.70391 5.49237 7.93363 5.80672C8.14951 6.09908 8.44012 6.5674 8.5905 6.82859C8.70373 7.00707 8.7498 7.21956 8.7206 7.42851C8.69139 7.63747 8.58878 7.82944 8.4309 7.97053L8.42997 7.97144L7.91149 8.38386C7.83059 8.46133 7.76809 8.55573 7.72851 8.6602C7.68893 8.76467 7.67328 8.87659 7.68269 8.98782V8.98507C7.93949 9.8349 8.40288 10.6089 9.03197 11.2388C9.66106 11.8687 10.4365 12.3351 11.29 12.5969L11.3287 12.607C11.4401 12.6154 11.552 12.5995 11.6565 12.5604C11.7611 12.5213 11.8558 12.4599 11.9339 12.3806L12.3491 11.8656C12.492 11.7085 12.6862 11.6066 12.8973 11.578C13.1085 11.5493 13.3231 11.5956 13.5032 11.7088L13.4995 11.707C14.2496 12.1313 14.8981 12.5978 15.4895 13.1266L15.4794 13.1175C15.6294 13.2363 15.7292 13.4066 15.7593 13.5948C15.7893 13.7831 15.7474 13.9757 15.6417 14.1348L15.6436 14.132L15.6454 14.1338Z" fill="currentColor"/>
            </svg>
            @if($__chatsBadge > 0)
                <span class="ev-mobile-bottom-nav__badge">{{ $__chatsBadge > 99 ? '99+' : $__chatsBadge }}</span>
            @endif
        </span>
        <span class="ev-mobile-bottom-nav__label">Chat &amp; Calls</span>
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
