<div>
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush

<style>
    .ev-back-bar {
        background: #131616;
        padding: 12px 0;
    }
    .ev-back-bar a { color: #C1F11D; text-decoration: none; font-size: 15px; }
    .ev-back-bar h1 { color: #fff; font-size: 18px; font-weight: 600; margin: 0; }
    .ev-back-bar h1 a { color: #fff; text-decoration: none; }
    .ev-container { max-width: 1200px; margin: 0 auto; padding: 0 16px; }

    /* Stats Card */
    .stats-card {
        background: #2a2a2a;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    .stats-card h3 {
        font-size: 28px;
        margin: 0 0 5px 0;
        color: #C1F11D;
    }

    .stats-card p {
        margin: 0;
        color: #888;
        font-size: 14px;
    }

    /* Favorites Grid */
    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }

    .favorite-card {
        background: #2a2a2a;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #333;
    }

    .favorite-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        border-color: #C1F11D;
    }

    .favorite-card-body {
        display: flex;
        padding: 15px;
        gap: 15px;
    }

    .favorite-card-img {
        width: 100px;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .favorite-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .favorite-card-info {
        flex: 1;
        min-width: 0;
    }

    .favorite-card-name {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .favorite-card-name a {
        color: #fff;
        text-decoration: none;
    }

    .favorite-card-name a:hover {
        color: #C1F11D;
    }

    .favorite-card-meta {
        color: #888;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .favorite-card-meta i {
        color: #C1F11D;
        margin-right: 5px;
    }

    .favorite-card-desc {
        color: #aaa;
        font-size: 13px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .favorite-card-actions {
        display: flex;
        border-top: 1px solid #333;
    }

    .favorite-card-actions a,
    .favorite-card-actions button {
        flex: 1;
        padding: 12px;
        background: transparent;
        border: none;
        color: #aaa;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        text-decoration: none;
    }

    .favorite-card-actions a:hover {
        background: #333;
        color: #C1F11D;
    }

    .favorite-card-actions button:hover {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }

    .favorite-card-actions a + button {
        border-left: 1px solid #333;
    }

    .profile-views-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        color: #C1F11D;
        padding: 3px 8px;
        background-color: rgba(193, 241, 29, 0.1);
        border-radius: 4px;
        white-space: nowrap;
    }

    .package-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        color: #000;
        padding: 3px 8px;
        background: #C1F11D;
        border-radius: 4px;
        text-transform: uppercase;
    }

    /* Empty State */
    .favorites-empty {
        background: #2a2a2a;
        border-radius: 12px;
        padding: 60px 40px;
        text-align: center;
    }

    .favorites-empty i {
        font-size: 60px;
        color: #444;
        margin-bottom: 20px;
    }

    .favorites-empty h4 {
        color: #fff;
        margin-bottom: 10px;
    }

    .favorites-empty p {
        color: #888;
        margin-bottom: 20px;
    }

    .favorites-empty .btn {
        background: #C1F11D;
        border: none;
        color: #000;
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: transform 0.2s;
        text-decoration: none;
    }

    .favorites-empty .btn:hover {
        transform: scale(1.05);
        color: #000;
    }

    /* Success alert */
    .ev-alert-success {
        background: rgba(193, 241, 29, 0.1);
        border: 1px solid rgba(193, 241, 29, 0.3);
        border-radius: 8px;
        padding: 14px 20px;
        margin-bottom: 16px;
        color: #C1F11D;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ev-alert-success .ev-close {
        background: none;
        border: none;
        color: #C1F11D;
        font-size: 18px;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .favorites-grid {
            grid-template-columns: 1fr;
        }

        .favorite-card-body {
            padding: 12px;
            gap: 12px;
        }

        .favorite-card-img {
            width: 80px;
            height: 100px;
        }

        .favorite-card-name {
            font-size: 16px;
        }
    }
</style>

<!-- Back Bar -->
<div class="ev-back-bar">
    <div class="ev-container" style="display:flex; align-items:center; justify-content:center; position:relative;">
        <a href="/female-escorts-in-dubai" style="position:absolute; left:16px;">
            <i class="fa fa-angle-left"></i> Escorts in Dubai
        </a>
        <h1>My Favorites</h1>
    </div>
</div>

<div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">
    @include('components.communication-nav')

    <!-- Success Message -->
    @if(session('success'))
    <div class="ev-alert-success">
        <span><i class="fa fa-check-circle"></i> {{ session('success') }}</span>
        <button class="ev-close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
    @endif

    {{-- Stats Card --}}
    <div class="stats-card">
        <h3><i class="fa fa-heart"></i> {{ $favorites->total() }}</h3>
        <p>Favorite Profiles</p>
    </div>

    @if($favorites->total() > 0)
        <div class="favorites-grid">
            @foreach($favorites as $favorite)
            @php
                $profile = $favorite->profile;
            @endphp
            <div class="favorite-card">
                <div class="favorite-card-body">
                    <div class="favorite-card-img">
                        <a href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}">
                            @if(!empty($profile->coverimg->image))
                            <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" />
                            @elseif(!empty($profile->singleimg->image))
                            <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" />
                            @else
                            <img alt="{{$profile->name}}" src="{{smart_asset('assets/images/default-avatar.png')}}" />
                            @endif
                        </a>
                    </div>
                    <div class="favorite-card-info">
                        <div class="favorite-card-name">
                            <a href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}">
                                {{$profile->name}}
                            </a>
                            <span class="profile-views-badge">
                                <i class="fa fa-eye"></i> {{ $profile->profile_views ?? 0 }}
                            </span>
                            @if($profile->package_id && $profile->getpackage)
                            <span class="package-badge">
                                {{ $profile->getpackage->name ?? 'PACKAGE' }}
                            </span>
                            @endif
                        </div>
                        <div class="favorite-card-meta">
                            <i class="fa fa-map-marker-alt"></i> {{$profile->getcity->name}}
                            <span style="margin: 0 6px;">|</span>
                            <i class="fa fa-venus"></i> {{$profile->ggender->name}}
                        </div>
                        @if(!empty($profile->about))
                        <div class="favorite-card-desc">
                            {{ \Str::limit($profile->about, 100) }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="favorite-card-actions">
                    <a href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}">
                        <i class="fa fa-eye"></i> View Profile
                    </a>
                    <button wire:click="removeFavorite({{ $profile->id }})" type="button">
                        <i class="fa fa-heart-broken"></i> Remove
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($favorites->hasPages())
        <div style="margin-top: 20px;">
            {{ $favorites->links() }}
        </div>
        @endif
    @else
        <div class="favorites-empty">
            <i class="fa fa-heart"></i>
            <h4>No favorites yet</h4>
            <p>You haven't favorited any profiles yet. Browse escorts and click the bookmark icon to add them to your favorites.</p>
            <a href="/female-escorts-in-dubai" class="btn">
                <i class="fa fa-search" style="font-size:20px"></i> Browse Escorts
            </a>
        </div>
    @endif
</div>
</div>
