@php
    // Get user slug and id
    $user = auth()->user();
    $firstProfile = $user->profiles->first();
    $userSlug = $firstProfile->slug ?? 'user';
    $userId = $firstProfile->id ?? $user->id;
    
    // Get counts for navigation badges
    use App\Models\UsersProfile;
    
    $activeCount = UsersProfile::where('user_id', auth()->id())
        ->whereNull('archived_at')
        ->where('is_active', 1)
        ->count();
        
    $pendingCount = UsersProfile::where('user_id', auth()->id())
        ->whereNull('archived_at')
        ->where('is_verified', 0)
        ->count();
        
    $rejectedCount = UsersProfile::where('user_id', auth()->id())
        ->whereHas('rejectedVerification')
        ->count();
        
    $archivedCount = UsersProfile::where('user_id', auth()->id())
        ->whereNotNull('archived_at')
        ->count();
@endphp

<style>
/* Profile Dashboard Nav Styles */
.profile-dashboard-nav {
    margin-bottom: 20px;
}

.profile-dashboard-nav .btn-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    float:left;
}

.profile-dashboard-nav .btn {
    padding: 6px 12px;
    border-radius: 4px;
}

.profile-dashboard-nav .my-listing-new-link {
    color: #333;
    float: right;
    background: #f4b827 linear-gradient(#f4b827, #d3980b) repeat-x;
    border-color: #c9910a;
}

/* Mobile Optimization */
@media (max-width: 768px) {
    .profile-dashboard-nav .btn-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 100%;
        gap: 8px;
    }
    
    .profile-dashboard-nav .btn-group .btn-group {
        grid-column: 1 / -1;
        width: 100%;
        display: block;
    }
    
    .profile-dashboard-nav .btn-group .btn-group .btn {
        width: 100%;
        display: block;
    }
    
    .profile-dashboard-nav .btn {
        text-align: center;
        padding: 12px 8px !important;
        border-radius: 6px !important;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .profile-dashboard-nav .btn i {
        margin-right: 6px;
        flex-shrink: 0;
    }
    
    .profile-dashboard-nav .my-listing-new-link {
        width: 100%;
        margin-top: 12px;
        padding: 12px 16px !important;
        font-size: 16px;
        font-weight: 600;
        border-radius: 6px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        float: none;
    }
}

/* Dropdown Menu Styles */
.profile-dashboard-nav .dropdown-menu {
    background-color: #333;
    border: 1px solid #444;
    border-radius: 4px;
    padding: 0;
    margin-top: 5px;
    min-width: 200px;
}

.profile-dashboard-nav .dropdown-menu li {
    list-style: none;
}

.profile-dashboard-nav .dropdown-menu li a {
    display: block;
    padding: 10px 15px;
    color: #aaa;
    text-decoration: none;
    border-bottom: 1px solid #444;
    transition: all 0.2s;
}

.profile-dashboard-nav .dropdown-menu li:last-child a {
    border-bottom: none;
}

.profile-dashboard-nav .dropdown-menu li a:hover {
    background-color: #444;
    color: #fff;
}

.profile-dashboard-nav .dropdown-menu li a i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

.listing-li .listing-info {
    height: 130px;
}

/* Simple Clean Pagination - Theme Colors */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 30px 0;
    gap: 5px;
}

.pagination li {
    list-style: none;
}

.pagination li a,
.pagination li span {
    display: block;
    padding: 10px 16px;
    min-width: 45px;
    text-align: center;
    font-size: 16px;
    font-weight: 600;
    color: #555;
    background-color: #fff;
    border: 2px solid #ddd;
    text-decoration: none;
    transition: all 0.2s;
    border-radius: 4px;
}

.pagination li a:hover {
    background-color: #333;
    color: #dca623;
    border-color: #333;
}

.pagination li.active span {
    background-color: #333;
    color: #dca623;
    border-color: #333;
}

.pagination li.disabled span {
    color: #646464;
    cursor: not-allowed;
    background-color: #8f8f8f;
    border-color: #8f8f8f;
}

.my-profile .listing-li {
    border-bottom: 1px solid #444444;
    padding-bottom: 20px;
}

.pagination>li>a, .pagination>li>button, .pagination>li>span {
    font-size: 18px;
    height: 48px;
    line-height: 26px;
    padding: 11px 16px;
    background: #535353;
    border: 1px solid #535353;
}

.nbtn {
  background: transparent;
    color: #aaaaaa;
    border-radius: 30px;
    border: 1px solid #4e4e4e;
    padding: 2px 10px;
}

.nbtn:hover {
    color:white;
}

.nbtn:focus,
.nbtn:active,
.nbtn.dropdown-toggle:focus,
.nbtn.dropdown-toggle:active {
    color: white !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
}

.margin-right {
    margin-right: 8px !important;
}

/* Archived Badge */
.archived-badge {
    display: inline-block;
    font-size: 14px;
    font-weight: normal;
    color: #ff6b6b;
    padding: 4px 10px;
    background-color: rgba(255, 107, 107, 0.1);
    border-radius: 4px;
    border: 1px solid rgba(255, 107, 107, 0.3);
}

/* Profile Filter Buttons */
.btn-group .btn {
    margin-right: 5px;
    border-radius: 4px !important;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-group .btn i {
    margin-right: 5px;
}

@media (max-width: 576px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 8px;
        width: 100%;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
}

.btn-primary:focus, .btn-primary:hover, .my-listings-nav>.my-listing-new-link:focus, .my-listings-nav>.my-listing-new-link:hover {
    background-color: transparent;
}
.btn-primary.active, .btn-primary:active, .btn-primary:hover, .my-listings-nav.open>.dropdown-toggle.my-listing-new-link, .my-listings-nav>.active.my-listing-new-link, .my-listings-nav>.my-listing-new-link:active, .my-listings-nav>.my-listing-new-link:hover, .open>.btn-primary.dropdown-toggle {
    color: white;
    background-color: transparent;
    border-color: #747474ff;
}

.btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
        color: #fff;
        background-color: transparent;
        border-color: #747474ff;
    }
</style>

<div class=" profile-dashboard-nav" style="margin-bottom:4rem">
    <div class="btn-group" role="group">
        <a href="{{ route('user.dashboard', ['name' => $userSlug, 'id' => $userId]) }}" 
           class="btn {{ !request()->has('filter') && request()->routeIs('user.dashboard') ? 'btn-primary' : 'nbtn' }}">
            <i class="fa fa-check-circle"></i> Active ({{ $activeCount }})
        </a>
        <a href="{{ route('rejected.verifications') }}" 
           class="btn {{ request()->routeIs('rejected.verifications') ? 'btn-primary' : 'nbtn' }}">
            <i class="fa fa-exclamation-triangle"></i> Rejected ({{ $rejectedCount }})
        </a>
        <a href="{{ route('user.dashboard', ['name' => $userSlug, 'id' => $userId, 'filter' => 'pending']) }}" 
           class="btn {{ request()->get('filter') == 'pending' ? 'btn-primary' : 'nbtn' }}">
            <i class="fa fa-clock"></i> Pending ({{ $pendingCount }})
        </a>
        <a href="{{ route('profile.archived') }}" 
           class="btn {{ request()->routeIs('profile.archived') ? 'btn-primary' : 'nbtn' }}">
            <i class="fa fa-archive"></i> Archived ({{ $archivedCount }})
        </a>
    </div>
    <a class="btn my-listing-new-link" href="{{ route('new.profile') }}" data-turbolinks="false">
        Add Profile <i class="fa fa-plus fa-text-default"></i>
    </a>
</div>
