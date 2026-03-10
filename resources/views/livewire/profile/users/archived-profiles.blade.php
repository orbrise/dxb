<div>
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    .ev-back-bar {
        background: #131616;
        padding: 12px 0;
    }
    .ev-back-bar a { color: #C1F11D; text-decoration: none; font-size: 15px; }
    .ev-back-bar h1 { color: #fff; font-size: 18px; font-weight: 600; margin: 0; }
    .ev-back-bar h1 a { color: #fff; text-decoration: none; }

    .ev-container { max-width: 1200px; margin: 0 auto; padding: 0 16px; }

    /* Dashboard Tabs */
    .ev-dashboard-tabs {
        display: flex;
        gap: 8px;
        padding: 20px 0;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }
    .ev-dashboard-nav {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }
    .ev-dashboard-nav a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 20px;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        background: #1D2224;
        transition: all 0.2s;
    }
    .ev-dashboard-nav a:hover { color: #C1F11D; }
    .ev-dashboard-nav a.active {
        background: #C1F11D;
        color: #000;
        font-weight: 600;
    }
    .ev-dashboard-tabs .ev-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 20px;
        background: #C1F11D;
        color: #000;
        font-weight: 600;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
    }
    .ev-dashboard-tabs .ev-add-btn:hover { opacity: 0.9; color: #000; }

    /* Archived Profile Card */
    .ev-archived-card {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        transition: all 0.2s;
    }
    .ev-archived-card .ev-card-body {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    .ev-archived-card .ev-avatar {
        width: 100px;
        height: 100px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        opacity: 0.7;
    }
    .ev-archived-card .ev-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .ev-archived-card .ev-profile-info {
        flex: 1;
        min-width: 0;
    }
    .ev-archived-card .ev-profile-name {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .ev-archived-card .ev-profile-name a { color: #aaa; text-decoration: none; }
    .ev-archived-card .ev-location {
        color: #777;
        font-size: 13px;
        margin-bottom: 8px;
    }
    .ev-archived-card .ev-location i { margin-right: 4px; }
    .ev-archived-card .ev-about {
        color: #999;
        font-size: 14px;
        margin-bottom: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ev-archived-card .ev-archive-reason {
        color: #777;
        font-size: 13px;
        margin-top: 6px;
    }
    .ev-archived-card .ev-archive-reason i { margin-right: 4px; }

    /* Badge */
    .ev-badge-archived {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 4px;
        background: rgba(255, 107, 107, 0.1);
        color: #ff6b6b;
        border: 1px solid rgba(255, 107, 107, 0.3);
    }

    /* Actions */
    .ev-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
        align-items: flex-start;
    }

    /* Buttons */
    .ev-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .ev-btn-accent {
        background: #C1F11D;
        color: #000;
    }
    .ev-btn-accent:hover { opacity: 0.9; color: #000; text-decoration: none; }
    .ev-btn-success {
        background: #28a745;
        color: #fff;
        padding: 11px 10px;
    }
    .ev-btn-success:hover { background: #218838; color: #fff; text-decoration: none; }
    .ev-btn-dark {
        background: #2a2a2a;
        color: #fff;
        border: 1px solid #3a3a3a;
    }
    .ev-btn-dark:hover { background: #333; color: #fff; text-decoration: none; }

    /* Archived time */
    .ev-archived-time {
        color: #ff6b6b;
        font-size: 12px;
        margin-top: 8px;
    }
    .ev-archived-time i { margin-right: 4px; }

    /* Success/Error alerts */
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
    .ev-alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        border-radius: 8px;
        padding: 14px 20px;
        margin-bottom: 16px;
        color: #ff6b6b;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ev-alert-danger .ev-close {
        background: none;
        border: none;
        color: #ff6b6b;
        font-size: 18px;
        cursor: pointer;
    }

    /* Empty state */
    .ev-empty-state {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
    }
    .ev-empty-state i { font-size: 40px; color: #C1F11D; margin-bottom: 16px; }
    .ev-empty-state h3 { color: #fff; font-size: 18px; margin: 0 0 8px; }
    .ev-empty-state p { color: #999; font-size: 14px; margin: 0 0 20px; }

    /* Pagination */
    .ev-pagination {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin: 30px 0;
    }
    .ev-pagination nav { width: 100%; }
    .ev-pagination div { display: flex; flex-direction: column; align-items: center; gap: 12px; }
    .ev-pagination p { color: #999; font-size: 13px; margin: 0; }
    .ev-pagination nav > div > div:first-child { display: flex; justify-content: center; gap: 8px; }
    .ev-pagination span[aria-disabled],
    .ev-pagination a[rel] {
        display: inline-block;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 600;
        color: #999;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
    }
    .ev-pagination a[rel]:hover {
        background: #2a2a2a;
        color: #C1F11D;
        border-color: #C1F11D;
    }
    .ev-pagination span[aria-disabled] {
        color: #444;
        background: #111;
        border-color: #222;
        cursor: not-allowed;
    }
    .ev-pagination .pagination { display: flex; gap: 5px; list-style: none; padding: 0; margin: 0; }
    .ev-pagination .pagination li a,
    .ev-pagination .pagination li span {
        display: block;
        padding: 8px 14px;
        min-width: 42px;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        color: #999;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .ev-pagination .pagination li a:hover {
        background: #2a2a2a;
        color: #C1F11D;
        border-color: #C1F11D;
    }
    .ev-pagination .pagination li.active span {
        background: #C1F11D;
        color: #000;
        border-color: #C1F11D;
    }
    .ev-pagination .pagination li.disabled span {
        color: #444;
        background: #111;
        border-color: #222;
        cursor: not-allowed;
    }
    .ev-pagination span[aria-current="page"] span {
        display: block;
        padding: 8px 14px;
        min-width: 42px;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        background: #C1F11D !important;
        color: #000 !important;
        border: 1px solid #C1F11D !important;
        border-radius: 4px;
    }
    .ev-pagination button {
        display: inline-block;
        padding: 8px 14px;
        min-width: 42px;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        color: #999;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ev-pagination button:hover { background: #2a2a2a; color: #C1F11D; border-color: #C1F11D; }
    .ev-pagination button:disabled { color: #444; background: #111; border-color: #222; cursor: not-allowed; }
    .ev-pagination button:disabled:hover { color: #444; background: #111; border-color: #222; }

    /* Responsive */
    @media (max-width: 768px) {
        .ev-dashboard-tabs { flex-direction: column; align-items: stretch; }
        .ev-dashboard-nav { flex-direction: column; }
        .ev-dashboard-nav a { width: 100%; justify-content: center; }
        .ev-dashboard-tabs .ev-add-btn { width: 100%; justify-content: center; }
        .ev-archived-card .ev-card-body { flex-direction: column; align-items: center; text-align: center; }
        .ev-archived-card .ev-avatar { width: 120px; height: 120px; }
        .ev-archived-card .ev-profile-name { justify-content: center; }
        .ev-archived-card .ev-location { text-align: center; }
        .ev-actions { justify-content: center; width: 100%; }
    }
</style>
@endpush

<!-- Back Bar -->
<div class="ev-back-bar">
    <div class="ev-container" style="display:flex; align-items:center; justify-content:center; position:relative;">
        <a href="/my-account" style="position:absolute; left:16px;">
            <i class="fa fa-angle-left"></i> Back to My Account
        </a>
        <h1>Archived Profiles</h1>
    </div>
</div>

<div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">

    <!-- Success Flash -->
    @if(session('success'))
    <div class="ev-alert-success" style="margin-top: 16px;">
        <span><i class="fa fa-check-circle"></i> {{ session('success') }}</span>
        <button class="ev-close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="ev-alert-danger" style="margin-top: 16px;">
        <span><i class="fa fa-exclamation-circle"></i> {{ session('error') }}</span>
        <button class="ev-close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
    @endif

    <!-- Dashboard Tabs -->
    <div class="ev-dashboard-tabs">
        <div class="ev-dashboard-nav">
            <a href="{{ route('user.dashboard', ['name' => $user->slug ?? 'profile', 'id' => $user->id ?? 0]) }}">
                <i class="fa fa-check-circle"></i> Active ({{ $activeCount ?? 0 }})
            </a>
            <a href="{{ route('rejected.verifications') }}">
                <i class="fa fa-exclamation-triangle"></i> Rejected ({{ $rejectedCount ?? 0 }})
            </a>
            <a href="{{ route('user.dashboard', ['name' => $user->slug ?? 'profile', 'id' => $user->id ?? 0, 'filter' => 'pending']) }}">
                <i class="fa fa-clock"></i> Pending ({{ $pendingCount ?? 0 }})
            </a>
            <a href="{{ route('profile.archived') }}" class="active">
                <i class="fa fa-archive"></i> Archived ({{ $archivedCount ?? 0 }})
            </a>
        </div>
        <a href="{{ route('new.profile') }}" class="ev-add-btn">
            <i class="fa fa-plus"></i> Add Profile
        </a>
    </div>

    <!-- Archived Profiles -->
    @if(isset($archivedProfiles) && $archivedProfiles->total() > 0)
        @foreach($archivedProfiles as $profile)
            <div class="ev-archived-card">
                <div class="ev-card-body">
                    <!-- Avatar -->
                    <div class="ev-avatar">
                        @if(!empty($profile->coverimg->image))
                            <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" />
                        @elseif(!empty($profile->singleimg->image))
                            <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" />
                        @else
                            <img alt="{{$profile->name}}" src="{{smart_asset('assets/images/default-avatar.png')}}" />
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="ev-profile-info">
                        <h2 class="ev-profile-name">
                            <a href="javascript:void(0)" title="{{$profile->name}}, escort in {{$profile->getcity->name}}">
                                {{$profile->name}}
                            </a>
                            <span class="ev-badge-archived">
                                <i class="fa fa-archive"></i> Archived
                            </span>
                        </h2>
                        <div class="ev-location">
                            <i class="fa fa-map-marker-alt"></i> {{$profile->getcity->name}}
                            <span style="margin: 0 6px;">|</span>
                            <i class="fa fa-venus"></i> {{$profile->ggender->name}}
                        </div>
                        @if($profile->archive_reason)
                        <div class="ev-archive-reason">
                            <i class="fa fa-info-circle"></i> Reason: {{ $profile->archive_reason }}
                        </div>
                        @endif
                        @if(!empty($profile->about))
                        <div class="ev-about">{{\Str::limit($profile->about, 130, '...')}}</div>
                        @endif
                        @if($profile->archived_at)
                        <div class="ev-archived-time">
                            <i class="fa fa-calendar"></i> Archived {{ \Carbon\Carbon::parse($profile->archived_at)->diffForHumans() }}
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="ev-actions">
                        <button type="button"
                                wire:click="repostProfile({{ $profile->id }})"
                                wire:loading.attr="disabled"
                                class="ev-btn ev-btn-success">
                            <span wire:loading.remove wire:target="repostProfile({{ $profile->id }})">
                                <i class="fa fa-undo"></i> Repost
                            </span>
                            <span wire:loading wire:target="repostProfile({{ $profile->id }})">
                                <i class="fa fa-spinner fa-spin"></i> Reposting...
                            </span>
                        </button>
                        <a class="ev-btn ev-btn-dark" href="{{url('my-profile/'.$profile->slug.'/'.$profile->id)}}">
                            <i class="fa fa-eye"></i> View
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if($archivedProfiles->hasPages())
        <div class="ev-pagination">
            {{ $archivedProfiles->links() }}
        </div>
        @endif
    @else
        <div class="ev-empty-state">
            <i class="fa fa-archive"></i>
            <h3>No Archived Profiles</h3>
            <p>You don't have any archived profiles at the moment.</p>
            <a class="ev-btn ev-btn-accent" href="/my-account">
                <i class="fa fa-arrow-left"></i> Back to My Account
            </a>
        </div>
    @endif

</div>
</div>
