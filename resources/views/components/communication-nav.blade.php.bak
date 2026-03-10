@php
    // Get unread message count for the logged-in user
    $unreadMsgCount = 0;
    if (auth()->check()) {
        $userProfiles = \App\Models\UsersProfile::where('user_id', auth()->id())->pluck('id');
        $unreadMsgCount = \App\Models\Message::whereIn('profile_id', $userProfiles)
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', 'unread');
            })
            ->count();
    }
@endphp

<style>
/* Communication Navigation Styles */
.communication-nav {
    margin-bottom: 25px;
}

.communication-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.communication-nav-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f4b827;
    font-size: 20px;
    font-weight: 600;
    margin: 0;
}

.communication-nav-title i {
    font-size: 22px;
}

.communication-nav-back {
    color: #888;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: color 0.2s;
}

.communication-nav-back:hover {
    color: #f4b827;
    text-decoration: none;
}

.communication-nav-menu {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    padding: 0;
    margin: 0;
    list-style: none;
}

.communication-nav-item {
    flex: 1;
    min-width: 120px;
}

.communication-nav-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    background: #2a2a2a;
    border: 1px solid #444;
    border-radius: 8px;
    color: #aaa;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.communication-nav-link:hover {
    background: #333;
    border-color: #555;
    color: #fff;
    text-decoration: none;
}

.communication-nav-link.active {
    background: linear-gradient(135deg, #f4b827, #d4a017);
    border-color: #f4b827;
    color: #1a1a1a;
    font-weight: 600;
}

.communication-nav-link.active:hover {
    background: linear-gradient(135deg, #d4a017, #c49516);
    color: #1a1a1a;
}

.communication-nav-link i {
    font-size: 16px;
}

.communication-nav-badge {
    background: #dc3545;
    color: #fff;
    font-size: 11px;
    font-weight: bold;
    padding: 2px 7px;
    border-radius: 10px;
    margin-left: 5px;
}

.communication-nav-link.active .communication-nav-badge {
    background: #1a1a1a;
    color: #f4b827;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .communication-nav-menu {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .communication-nav-item {
        min-width: auto;
    }
    
    .communication-nav-link {
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .communication-nav-link i {
        font-size: 14px;
    }
    
    .communication-nav-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}

@media (max-width: 480px) {
    .communication-nav-menu {
        grid-template-columns: 1fr;
    }
    
    .communication-nav-link {
        justify-content: flex-start;
    }
}
</style>

<div class="communication-nav">
    <div class="communication-nav-header">
        <h2 class="communication-nav-title">
            <i class="fa fa-comments"></i>
            Communication
        </h2>
        <a href="{{ route('user.dashboard', ['name' => auth()->user()->profiles->first()->slug ?? 'user', 'id' => auth()->user()->profiles->first()->id ?? auth()->id()]) }}" class="communication-nav-back">
            <i class="fa fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
    
    <ul class="communication-nav-menu">
        <li class="communication-nav-item">
            <a href="{{ route('user.chat') }}" class="communication-nav-link {{ request()->routeIs('user.chat*') ? 'active' : '' }}">
                <i class="fa fa-comments"></i>
                <span>Messages</span>
                @php
                    $chatUnreadCount = 0;
                    if (auth()->check()) {
                        $chatUnreadCount = \App\Models\Message::whereHas('conversation', function($q) {
                            $q->where('user_one_id', auth()->id())->orWhere('user_two_id', auth()->id());
                        })->where('sender_id', '!=', auth()->id())
                          ->where(function($q) {
                              $q->whereNull('status')->orWhere('status', 'unread');
                          })->count();
                    }
                @endphp
                @if($chatUnreadCount > 0)
                    <span class="communication-nav-badge">{{ $chatUnreadCount }}</span>
                @endif
            </a>
        </li>
        <li class="communication-nav-item">
            <a href="{{ route('user.questions') }}" class="communication-nav-link {{ request()->routeIs('user.questions') ? 'active' : '' }}">
                <i class="fa fa-question-circle"></i>
                <span>Questions</span>
            </a>
        </li>
        <li class="communication-nav-item">
            <a href="{{ route('user.reviews') }}" class="communication-nav-link {{ request()->routeIs('user.reviews') ? 'active' : '' }}">
                <i class="fa fa-star"></i>
                <span>Reviews</span>
            </a>
        </li>
        <li class="communication-nav-item">
            <a href="{{ route('favorites.dashboard') }}" class="communication-nav-link {{ request()->routeIs('favorites.dashboard') ? 'active' : '' }}">
                <i class="fa fa-heart"></i>
                <span>My Favorites</span>
            </a>
        </li>
    </ul>
</div>
