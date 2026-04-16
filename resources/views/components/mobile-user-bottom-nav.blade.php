@auth
@php
    $__currentRoute = request()->route() ? request()->route()->getName() : '';
    $__isFavorites = $__currentRoute === 'favorites.dashboard';
    $__isMessages  = str_starts_with($__currentRoute ?? '', 'user.chat');
    $__isAsk       = $__currentRoute === 'user.questions';
    $__isReviews   = $__currentRoute === 'user.reviews';
    $__isDashboard = $__currentRoute === 'user.dashboard';
    $__userProfile = auth()->user()->profiles->first();
    $__dashboardUrl = $__userProfile
        ? url('my-profile/'.$__userProfile->slug.'/'.$__userProfile->id)
        : url('my-account');
@endphp
<style>
.ev-mobile-bottom-nav{display:none;position:fixed;bottom:0;left:0;right:0;z-index:100;background:#0a0a0a;border-top:1px solid #2a2a2a;padding:10px 12px;padding-bottom:max(10px,env(safe-area-inset-bottom));gap:8px;align-items:center;justify-content:flex-start}
.ev-mobile-bottom-nav a{display:inline-flex!important;align-items:center;gap:4px;color:#888;text-decoration:none;font-size:11px;padding:7px 10px;border-radius:5px;white-space:nowrap}
.ev-mobile-bottom-nav a i{display:inline-block!important;font-size:12px;color:inherit;visibility:visible!important}
.ev-mobile-bottom-nav a span{display:inline-block!important;color:inherit;visibility:visible!important}
.ev-mobile-bottom-nav a.ev-nav-active,.ev-mobile-bottom-nav a.ev-nav-active:hover,.ev-mobile-bottom-nav a.ev-nav-active:focus{background:#C1F11D!important;color:#000!important;font-weight:600}
.ev-mobile-bottom-nav a.ev-nav-active i,.ev-mobile-bottom-nav a.ev-nav-active span{color:#000!important}
.ev-mobile-bottom-nav a:not(.ev-nav-active):hover{color:#fff}
@media (max-width: 768px){
    .ev-mobile-bottom-nav{display:flex!important}
    body{padding-bottom:72px}
}
</style>
<div class="ev-mobile-bottom-nav">
    <a href="{{ $__dashboardUrl }}" class="{{ $__isDashboard ? 'ev-nav-active' : '' }}" wire:navigate>
        <i class="fa fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="{{ route('favorites.dashboard') }}" class="{{ $__isFavorites ? 'ev-nav-active' : '' }}" wire:navigate>
        <i class="fa fa-heart"></i>
        <span>Favorite</span>
    </a>
    <a href="{{ route('user.chat') }}" class="{{ $__isMessages ? 'ev-nav-active' : '' }}" wire:navigate>
        <i class="fa fa-comment"></i>
        <span>Message</span>
    </a>
    <a href="{{ route('user.questions') }}" class="{{ $__isAsk ? 'ev-nav-active' : '' }}" wire:navigate>
        <i class="fa fa-question-circle"></i>
        <span>Ask</span>
    </a>
    <a href="{{ route('user.reviews') }}" class="{{ $__isReviews ? 'ev-nav-active' : '' }}" wire:navigate>
        <i class="fa fa-star"></i>
        <span>Reviews</span>
    </a>
</div>
@endauth
