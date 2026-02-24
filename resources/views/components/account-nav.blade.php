<style>
/* Account navigation layout */
.my-account-nav {
    display: flex !important;
    flex-wrap: nowrap !important;
    position: relative;
}

.my-account-nav > li {
    flex: 1 1 auto !important;
    position: relative;
}

.my-account-nav > li > a {
    display: block;
    text-align: center;
}

/* Dropdown specific fixes */
.my-account-nav > li.dropdown {
    position: relative !important;
}

.my-account-nav .dropdown-menu {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    z-index: 1000 !important;
    float: none !important;
    min-width: 200px;
    border-radius: 4px;
    margin-top: 0;
    display: none;
}

.my-account-nav .dropdown.open .dropdown-menu {
    display: block !important;
}

.my-account-nav .dropdown-menu li a:hover {
    background-color: #444 !important;
    color: #fff !important;
}

.my-account-nav .dropdown-menu li a i {
    margin-right: 8px;
    width: 16px;
}

.my-account-nav .dropdown.open > a,
.my-account-nav .dropdown > a:hover {
    background-color: rgba(255,255,255,0.1);
}

/* Keep Communication text inline */
.my-account-nav .dropdown > a.dropdown-toggle {
    white-space: nowrap !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px;
}

/* Mobile layout */
@media (max-width: 768px) {
    .my-account-nav > li > a {
        font-size: 12px !important;
        padding: 10px 8px !important;
        white-space: nowrap;
    }
    
    .my-account-nav > li > a i.fa-inline {
        display: block !important;
        margin: 0 auto 5px !important;
        font-size: 16px !important;
    }
    
    .my-account-nav .dropdown-menu {
        right: 0 !important;
        left: auto !important;
    }
}

/* Message count badge */
.message-badge {
    position: relative;
    display: inline-block;
}

.message-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #dc3545;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    min-width: 18px;
    text-align: center;
}
</style>

@php
    // Get unread message count for the logged-in user
    $unreadCount = 0;
    if (auth()->check()) {
        $userProfiles = \App\Models\UsersProfile::where('user_id', auth()->id())->pluck('id');
        $unreadCount = \App\Models\Message::whereIn('profile_id', $userProfiles)
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', 'unread');
            })
            ->count();
    }
@endphp

<ul class="nav nav-justified my-nav my-account-nav nav-dark" style="min-height: 50px; margin: 20px 0; padding: 0;">
    <li class="{{ request()->is('my-account') && !request()->is('my-account/*') ? 'active' : '' }}" style="margin: 0;">
        <a href="/my-account" style="padding: 12px 15px; display: block; line-height: 1.5; margin: 0;">
            <i class="fa fa-user fa-inline"></i> <span style="margin-left: 5px;">Account</span>
        </a>
    </li>
    <li class="{{ request()->is('my-account/edit') ? 'active' : '' }}" style="margin: 0;">
        <a href="/my-account/edit" style="padding: 12px 15px; display: block; line-height: 1.7; margin: 0;">
            <i class="fa fa-pencil-alt fa-inline"></i> <span style="margin-left: 5px;">Edit</span>
        </a>
    </li>
    <li class="{{ request()->is('my-password/edit') ? 'active' : '' }}" style="margin: 0;">
        <a href="/my-password/edit" style="padding: 12px 15px; display: block; line-height: 1.7; margin: 0;">
            <i class="fa fa-key fa-inline" ></i> <span style="margin-left: 5px;">Change password</span>
        </a>
    </li>
    {{-- <li class="dropdown {{ (request()->routeIs('user.messages') || request()->routeIs('user.questions') || request()->routeIs('user.reviews') || request()->routeIs('favorites.dashboard')) ? 'active' : '' }}" style="margin: 0;">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="padding: 12px 15px; line-height: 1.7; margin: 0;">
            <i class="fa fa-comments fa-inline"></i><span class="hidden-xs">Communication</span> 
            @if($unreadCount > 0)<span class="badge" style="background-color: #dc3545; font-size: 10px;">{{ $unreadCount }}</span>@endif
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right" style="background-color: #333; border: 1px solid #444;">
            <li><a href="{{ route('user.messages') }}" style="color: #aaa; padding: 10px 15px; display: block;"><i class="fa fa-envelope"></i> Messages @if($unreadCount > 0)<span class="badge" style="background-color: #dc3545;">{{ $unreadCount }}</span>@endif</a></li>
            <li><a href="{{ route('user.questions') }}" style="color: #aaa; padding: 10px 15px; display: block;"><i class="fa fa-question-circle"></i> Questions</a></li>
            <li><a href="{{ route('user.reviews') }}" style="color: #aaa; padding: 10px 15px; display: block;"><i class="fa fa-star"></i> Reviews</a></li>
            <li><a href="{{ route('favorites.dashboard') }}" style="color: #aaa; padding: 10px 15px; display: block;"><i class="fa fa-heart"></i> My Favorites</a></li>
        </ul>
    </li> --}}
    
</ul>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown toggle manually if Bootstrap's doesn't work
    var dropdownToggle = document.querySelector('.my-account-nav .dropdown-toggle');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var parent = this.parentElement;
            parent.classList.toggle('open');
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            var dropdown = document.querySelector('.my-account-nav .dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    }
});
</script>
