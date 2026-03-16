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

    /* Pending Alert */
    .ev-pending-alert {
        background: rgba(193, 241, 29, 0.08);
        border: 1px solid rgba(193, 241, 29, 0.3);
        border-radius: 8px;
        padding: 14px 20px;
        margin-bottom: 20px;
        color: #C1F11D;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
    }
    .ev-pending-alert .ev-close {
        background: none;
        border: none;
        color: #C1F11D;
        font-size: 20px;
        cursor: pointer;
        padding: 0 0 0 16px;
    }

    /* More Photos Alert */
    .ev-suggestion-alert {
        background: rgba(193, 241, 29, 0.06);
        border: 1px solid rgba(193, 241, 29, 0.25);
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 24px;
        position: relative;
    }
    .ev-suggestion-alert h3 {
        color: #C1F11D;
        font-size: 16px;
        margin: 0 0 6px 0;
    }
    .ev-suggestion-alert p { color: #ccc; margin: 0; font-size: 14px; }
    .ev-suggestion-alert a { color: #C1F11D; text-decoration: underline; }
    .ev-suggestion-alert .ev-close {
        position: absolute;
        top: 12px;
        right: 16px;
        background: none;
        border: none;
        color: #C1F11D;
        font-size: 20px;
        cursor: pointer;
    }

    /* Profile Card */
    .ev-profile-card {
        border-radius: 0;
        padding: 24px 0;
        margin-bottom: 0;
        display: flex;
        gap: 20px;
        border-bottom: 1px solid #2a2a2a;
    }
    .ev-profile-card:last-child {
        border-bottom: none;
    }
    .ev-profile-card .ev-avatar {
        width: 100px;
        height: 100px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .ev-profile-card .ev-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .ev-profile-card .ev-profile-info {
        flex: 1;
        min-width: 0;
        padding-left: 20px;
        border-left: 1px solid #2a2a2a;
    }
    .ev-profile-card .ev-profile-name {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .ev-profile-card .ev-profile-name a { color: #fff; text-decoration: none; }
    .ev-profile-card .ev-pending-text {
        color: #C1F11D;
        font-size: 13px;
        margin-bottom: 6px;
    }
    .ev-profile-card .ev-about {
        color: #ffffff;
        font-size: 14px;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ev-profile-card .ev-location {
        color: #777;
        font-size: 13px;
        margin-bottom: 10px;
    }
    .ev-profile-card .ev-location i { margin-right: 4px; }

    /* Action Buttons */
    .ev-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 12px;
    }
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
    .ev-btn-dark {
        background: #2a2a2a;
        color: #fff;
        border: 1px solid #3a3a3a;
    }
    .ev-btn-dark:hover { background: #333; color: #fff; text-decoration: none; }
    .ev-btn-outline {
        background: transparent;
        color: #aaa;
        border: 1px solid #3a3a3a;
    }
    .ev-btn-outline:hover { color: #C1F11D; border-color: #C1F11D; text-decoration: none; }
    .ev-btn-danger {
        background: rgba(220, 53, 69, 0.15);
        color: #ff6b6b;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }
    .ev-btn-danger:hover { background: rgba(220, 53, 69, 0.25); color: #ff4444; text-decoration: none; }

    /* Share + Help row */
    .ev-share-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
    }
    .ev-share-row span { color: #777; font-size: 13px; }
    .ev-share-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #2a2a2a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
    }
    .ev-share-icon:hover { background: #333; color: #fff; text-decoration: none; }
    .ev-need-help {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        background: #2a2a2a;
        color: #aaa;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid #3a3a3a;
    }
    .ev-need-help:hover { color: #fff; text-decoration: none; }

    /* Badges */
    .ev-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }
    .ev-badge-accent {
        color: #C1F11D;
        background: rgba(193, 241, 29, 0.1);
        border: 1px solid rgba(193, 241, 29, 0.3);
    }
    .ev-badge-green {
        color: #28a745;
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.3);
    }
    .ev-badge-yellow {
        color: #ffc107;
        background: rgba(255, 193, 7, 0.15);
        border: 1px solid rgba(255, 193, 7, 0.5);
    }

    /* Sidebar Cards */
    .ev-sidebar-card {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .ev-sidebar-card h3 {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 8px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #2a2a2a;
    }
    .ev-sidebar-card p { color: #999; font-size: 14px; margin: 8px 0 16px; }
    .ev-sidebar-card .ev-sub-text { color: #777; font-size: 13px; text-align: center; margin: 8px 0 4px; }
    .ev-sidebar-card .ev-unavailable {
        display: block;
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        background: #333;
        color: #888;
        font-size: 13px;
        text-align: center;
        border: none;
        cursor: default;
    }

    /* Accent sidebar card (lime-green bg) */
    .ev-sidebar-accent {
        background: #C1F11D !important;
        border-color: #a8d416 !important;
    }
    .ev-sidebar-accent h3 {
        color: #000 !important;
        border-bottom-color: rgba(0,0,0,0.15) !important;
    }
    .ev-sidebar-accent p { color: #333 !important; }
    .ev-sidebar-accent .ev-sub-text { color: #444 !important; }
    .ev-btn-dark-solid {
        background: #1a1a1a;
        color: #fff;
        border: 1px solid #1a1a1a;
    }
    .ev-btn-dark-solid:hover { background: #000; color: #fff; text-decoration: none; }

    /* Delete link */
    .ev-delete-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #ff6b6b;
        font-size: 13px;
        text-decoration: none;
        margin-top: 8px;
    }
    .ev-delete-link:hover { color: #ff4444; text-decoration: none; }

    /* Bottom Stats Cards */
    .ev-stats-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
        margin-top: 24px;
    }
    .ev-stats-card {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        padding: 20px;
    }
    .ev-stats-card h3 {
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 8px;
        padding-bottom: 10px;
        border-bottom: 1px solid #2a2a2a;
    }
    .ev-stats-card h3 small { color: #777; font-weight: 400; font-size: 12px; }
    .ev-stats-card p { color: #999; font-size: 14px; margin: 8px 0; }
    .ev-stat-value {
        color: #C1F11D;
        font-size: 14px;
        font-weight: 500;
        margin-right: 12px;
    }
    .ev-stat-value i { margin-right: 4px; }

    /* Verify photo preview */
    .ev-verify-preview {
        float: right;
        text-align: center;
        margin-left: 12px;
    }
    .ev-verify-preview img {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
    }
    .ev-verify-preview .ev-label { color: #666; font-size: 12px; display: block; margin-top: 4px; }
    .ev-verified-badge {
        color: #C1F11D;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 4px;
    }

    /* Link preview box */
    .ev-link-preview {
        float: right;
        margin-left: 12px;
        width: 140px;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #2a2a2a;
    }
    .ev-link-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Share link */
    .ev-share-link {
        color: #C1F11D;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 8px;
    }
    .ev-share-link:hover { text-decoration: underline; color: #C1F11D; }

    /* Rejected alert */
    .ev-alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 20px;
        position: relative;
    }
    .ev-alert-danger h4 { color: #ff6b6b; font-size: 15px; margin: 0 0 6px; }
    .ev-alert-danger p { color: #ccc; font-size: 14px; margin: 0 0 10px; }
    .ev-alert-danger .ev-close {
        position: absolute;
        top: 12px;
        right: 16px;
        background: none;
        border: none;
        color: #ff6b6b;
        font-size: 18px;
        cursor: pointer;
    }
    .ev-alert-danger .ev-btn-sm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 6px;
        background: rgba(220, 53, 69, 0.2);
        color: #ff6b6b;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid rgba(220, 53, 69, 0.3);
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

    /* Modal */
    .ev-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .ev-modal-overlay.active { display: flex; }
    .ev-modal {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        padding: 30px;
        max-width: 520px;
        width: 90%;
        text-align: center;
    }
    .ev-modal h2 { color: #fff; font-size: 20px; margin: 0 0 16px; }
    .ev-modal p { color: #ccc; font-size: 15px; margin-bottom: 12px; }
    .ev-modal .ev-highlight { color: #C1F11D; }
    .ev-modal .ev-modal-actions { margin-top: 24px; display: flex; flex-direction: column; gap: 10px; align-items: center; }

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
    /* Hide Livewire "Showing X to Y of Z results" text or style it */
    .ev-pagination p { color: #999; font-size: 13px; margin: 0; }
    /* Previous/Next buttons row */
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
    /* Numbered pagination */
    .ev-pagination .pagination { display: flex; gap: 5px; list-style: none; padding: 0; margin: 0; }
    .ev-pagination .pagination li a,
    .ev-pagination .pagination li span {
        display: block;
        padding: 4px 14px;
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
    /* Livewire tailwind/default pagination overrides */
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

    /* Floating success alert */
    #ev-success-float {
        display: none;
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 99999;
        min-width: 300px;
        max-width: 500px;
        background: rgba(193, 241, 29, 0.15);
        border: 1px solid rgba(193, 241, 29, 0.4);
        border-radius: 8px;
        padding: 14px 20px;
        color: #C1F11D;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .ev-main-grid { grid-template-columns: 1fr !important; }
        .ev-stats-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .ev-dashboard-tabs { flex-direction: column; align-items: stretch; }
        .ev-dashboard-nav { flex-direction: column; }
        .ev-dashboard-nav a { width: 100%; justify-content: center; }
        .ev-dashboard-tabs .ev-add-btn { width: 100%; justify-content: center; }
        .ev-profile-card { flex-direction: column; align-items: center; text-align: center; }
        .ev-actions { justify-content: center; }
        .ev-share-row { justify-content: center; flex-wrap: wrap; }
        .ev-need-help { margin-left: 0; }
        .ev-profile-card .ev-avatar { width: 120px; height: 120px; }
    }
</style>
@endpush

<!-- Back Bar -->
<div class="ev-back-bar">
    <div class="ev-container" style="display:flex; align-items:center; justify-content:center; position:relative;">
        <a href="/female-escorts-in-dubai" style="position:absolute; left:16px;">
            <i class="fa fa-angle-left"></i> Escorts in Dubai
        </a>
        <h1><a href="{{ route('user.dashboard', ['name' => $user->slug, 'id' => $user->id]) }}">{{Auth::user()->name}}'s Dubai profile</a></h1>
    </div>
</div>

<!-- Floating Success Alert -->
<div id="ev-success-float">
    <strong><i class="fa fa-check-circle"></i> Success!</strong>
    <span id="ev-success-text"></span>
    <button onclick="this.parentElement.style.display='none'" style="float:right; background:none; border:none; color:#C1F11D; font-size:18px; cursor:pointer;">&times;</button>
</div>

<div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">

    <!-- Success Flash -->
    @if(session('success'))
    <div class="ev-alert-success" style="margin-top: 16px;">
        <span><i class="fa fa-check-circle"></i> {{ session('success') }}</span>
        <button class="ev-close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
    @endif

    @php
        $activeCount = \App\Models\UsersProfile::where('user_id', auth()->id())->whereNull('archived_at')->where('is_active', 1)->count();
        $pendingCount = \App\Models\UsersProfile::where('user_id', auth()->id())->whereNull('archived_at')->where('is_verified', 0)->count();
        $rejectedCount = \App\Models\UsersProfile::where('user_id', auth()->id())->whereHas('rejectedVerification')->count();
        $archivedCount = \App\Models\UsersProfile::where('user_id', auth()->id())->whereNotNull('archived_at')->count();
    @endphp

    <!-- Dashboard Tabs -->
    <div class="ev-dashboard-tabs">
        <div class="ev-dashboard-nav">
            <a href="{{ route('user.dashboard', ['name' => $user->slug, 'id' => $user->id]) }}" 
               class="{{ !request()->has('filter') && request()->routeIs('user.dashboard') && !request()->has('filter') ? 'active' : '' }}">
                <i class="fa fa-check-circle"></i> Active ({{ $activeCount }})
            </a>
            <a href="{{ route('rejected.verifications') }}" 
               class="{{ request()->routeIs('rejected.verifications') ? 'active' : '' }}">
                <i class="fa fa-exclamation-triangle"></i> Rejected ({{ $rejectedCount }})
            </a>
            <a href="{{ route('user.dashboard', ['name' => $user->slug, 'id' => $user->id, 'filter' => 'pending']) }}" 
               class="{{ request()->get('filter') == 'pending' ? 'active' : '' }}">
                <i class="fa fa-clock"></i> Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('profile.archived') }}" 
               class="{{ request()->routeIs('profile.archived') ? 'active' : '' }}">
                <i class="fa fa-archive"></i> Archived ({{ $archivedCount }})
            </a>
        </div>
        <a href="{{ route('new.profile') }}" class="ev-add-btn">
            <i class="fa fa-plus"></i> Add Profile
        </a>
    </div>

    <!-- Pending Moderation Alert -->
   {{-- @if(isset($allProfiles) && $allProfiles->total() > 0)
        @foreach($allProfiles as $checkProfile)
            @if($checkProfile->is_verified == 0)
            <div class="ev-pending-alert">
                <span><i class="fa fa-info-circle"></i> Thanks for listing your profile! Once we review the profile we will send you an email when it is live on the site</span>
                <button class="ev-close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
            @break
            @endif
        @endforeach
    @endif --}}

    <!-- More Photos Suggestion -->
    <div class="ev-suggestion-alert">
        <button class="ev-close" onclick="this.parentElement.style.display='none'">&times;</button>
        <h3>More photos = More leads</h3>
        <p>Increasing the number of photos on your listing will produce more enquiries - you can add up to 30 - <a href="{{url('edit-profile/'.$user->slug.'/'.$user->id)}}#photos">Add more photos</a>.</p>
    </div>

    {{-- Check if user has any rejected verifications --}}
    @php
        $hasRejections = false;
        if(isset($allProfiles)) {
            foreach($allProfiles as $p) {
                if($p->rejectedVerification) {
                    $hasRejections = true;
                    break;
                }
            }
        }
    @endphp

    @if($hasRejections)
    <div class="ev-alert-danger">
        <button class="ev-close" onclick="this.parentElement.style.display='none'">&times;</button>
        <h4><i class="fa fa-exclamation-triangle"></i> Photo Verification Rejected</h4>
        <p>One or more of your photo verifications have been rejected. Please review the reasons and re-submit.</p>
        <a href="{{ route('rejected.verifications') }}" class="ev-btn-sm">
            <i class="fa fa-eye"></i> View Rejected Verifications
        </a>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="ev-main-grid" style="display: grid; grid-template-columns: 1fr 340px; gap: 24px;">
        <!-- Left Column: Profile Cards -->
        <div>
            @if(isset($allProfiles) && $allProfiles->total() > 0)
                @foreach($allProfiles as $profile)
                    @php
                        $daysRemaining = null;
                        if($profile->package_id && $profile->getpackage) {
                            $priceTiers = $profile->getpackage->price_tiers ? json_decode($profile->getpackage->price_tiers, true) : null;
                            if ($profile->promoted_until) {
                                $expiresAt = \Carbon\Carbon::parse($profile->promoted_until);
                                $daysRemaining = max(0, ceil(\Carbon\Carbon::now()->diffInDays($expiresAt, false)));
                            } elseif ($priceTiers && count($priceTiers) > 0) {
                                $longestTier = end($priceTiers);
                                $days = (int)$longestTier['days'];
                                $packageStartDate = $profile->updated_at ?? $profile->created_at;
                                $expiresAt = \Carbon\Carbon::parse($packageStartDate)->addDays($days);
                                $daysRemaining = max(0, ceil(\Carbon\Carbon::now()->diffInDays($expiresAt, false)));
                            }
                        }
                        $profileUrl = url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug);
                    @endphp

                    <div class="ev-profile-card">
                        <!-- Avatar -->
                        <div class="ev-avatar">
                            <a href="{{ $profileUrl }}" wire:navigate>
                                @if(!empty($profile->coverimg->image))
                                    <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" />
                                @elseif(!empty($profile->singleimg->image))
                                    <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" />
                                @else
                                    <img alt="{{$profile->name}}" src="{{smart_asset('assets/images/default-avatar.png')}}" />
                                @endif
                            </a>
                        </div>

                        <!-- Profile Info -->
                        <div class="ev-profile-info">
                            <h2 class="ev-profile-name">
                                <a href="{{ $profileUrl }}" wire:navigate>{{$profile->name}}</a>
                                <span class="ev-badge ev-badge-accent" title="Profile Views">
                                    <i class="fa fa-eye"></i> {{ $profile->profile_views ?? 0 }}
                                </span>
                                <span class="ev-badge ev-badge-green" title="Phone Clicks">
                                    <i class="fa fa-phone"></i> {{ $profile->phone_clicks ?? 0 }}
                                </span>
                                @if($profile->package_id && $profile->getpackage)
                                <span class="ev-badge ev-badge-accent">
                                    <i class="fa fa-star"></i> {{ strtoupper($profile->getpackage->name ?? 'PACKAGE') }}
                                </span>
                                    @if($daysRemaining !== null && $daysRemaining > 0)
                                    <span class="ev-badge ev-badge-accent">
                                        <i class="fa fa-clock"></i> {{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }} left
                                    </span>
                                    @endif
                                @endif
                                @if($profile->activeAuction)
                                <span class="ev-badge ev-badge-yellow">
                                    <i class="fa fa-gavel"></i> AUCTION #{{ $profile->activeAuction->spot_number }}
                                </span>
                                <span class="ev-badge ev-badge-green">
                                    <i class="fa fa-clock"></i> {{ $profile->auction_days_remaining }} {{ $profile->auction_days_remaining == 1 ? 'day' : 'days' }} left
                                </span>
                                @endif
                            </h2>

                           {{--  @if($profile->is_verified == 0)
                            <div class="ev-pending-text">
                                <i class="fa fa-info-circle"></i> Your profile is pending moderation - this can take 48 hours.
                            </div>
                            @endif --}}

                            <div class="ev-location">
                                <i class="fa fa-map-marker-alt"></i> {{$profile->getcity->name}}
                                <span style="margin: 0 6px;">|</span>
                                <i class="fa fa-venus"></i> {{$profile->ggender->name}}
                            </div>

                            @if(!empty($profile->about))
                            <div class="ev-about">{{\Str::limit($profile->about, 130, '...')}}</div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="ev-actions">
                                <a class="ev-btn ev-btn-accent" href="{{url('edit-profile/'.$profile->slug.'/'.$profile->id)}}">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </a>
                                <a class="ev-btn ev-btn-dark" href="{{ $profileUrl }}">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a class="ev-btn ev-btn-dark" href="/my-profile/{{$profile->slug}}/{{$profile->id}}/upgrade" wire:navigate>
                                    <i class="fa fa-arrow-up"></i> Upgrade
                                </a>
                                @if($profile->is_active == 1)
                                <button type="button" class="ev-btn ev-btn-dark profile-status-btn" 
                                        data-profile-id="{{ $profile->id }}" data-action="0">
                                    <i class="fa fa-pause"></i> Pause
                                </button>
                                @else
                                <button type="button" class="ev-btn ev-btn-dark profile-status-btn" 
                                        data-profile-id="{{ $profile->id }}" data-action="1">
                                    <i class="fa fa-play"></i> Resume
                                </button>
                                @endif
                                <a class="ev-btn ev-btn-danger" href="javascript:void(0)" onclick="if(confirm('Are you sure you want to DELETE this profile? This cannot be reversed.')) window.location='/action/listings/{{ $profile->id }}'">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </div>

                            <!-- Share Row -->
                            {{-- <div class="ev-share-row">
                                <span>Share profile on:</span>
                                <a class="ev-share-icon" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($profileUrl) }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
                                <a class="ev-share-icon" href="https://x.com/intent/tweet?url={{ urlencode($profileUrl) }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
                                <a class="ev-need-help" href="javascript:void(0)">
                                    <i class="fa fa-info-circle"></i> Need Help
                                </a>
                            </div> --}}
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($allProfiles->hasPages())
                <div class="ev-pagination">
                    {{ $allProfiles->links() }}
                </div>
                @endif
            @endif
        </div>

        <!-- Right Sidebar -->
        <div>
            <!-- Get to the Top -->
            <div class="ev-sidebar-card ev-sidebar-accent">
                <h3>Get to the top!</h3>
                <p>Move to a Top Spot for a week:</p>
                <a class="ev-btn ev-btn-dark-solid" href="/auctions/female-escorts-in-dubai" style="width: 100%; justify-content: space-between;">
                    See Auctions <i class="fa fa-arrow-right"></i>
                </a>
                <p class="ev-sub-text">or</p>
                <p style="color: #333; font-size: 13px; margin: 0 0 6px;">Reach the top for 1 hour, below the Top Spots:</p>
                <span class="ev-unavailable" style="background: rgba(0,0,0,0.1); color: #555; border: 1px solid rgba(0,0,0,0.15);">Not available - Profile not visible</span>
            </div>

            <!-- Free Listing / Upgrade -->
            <div class="ev-sidebar-card">
                <h3>Free Listing</h3>
                <p>Upgrade to VIP and get more enquiries!</p>
                <a class="ev-btn ev-btn-accent" href="/my-profile/{{$user->slug}}/{{$user->id}}/upgrade" wire:navigate>
                    Upgrade now <i class="fa fa-arrow-right"></i>
                </a>
            </div>

            <!-- Delete Profile -->
            <div style="text-align: right; padding: 0 4px;">
                <a class="ev-delete-link" href="javascript:void(0)" onclick="if(confirm('Are you sure you want to DELETE this profile? This cannot be reversed.')) window.location='/action/listings/{{ $user->id }}'">
                    <i class="fa fa-times"></i> Delete Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Stats Row -->
    <div class="ev-stats-row">
        <!-- Contacts -->
        <div class="ev-stats-card">
            <h3>Contacts <small>(last 30 days)</small></h3>
            <p>
                <span class="ev-stat-value"><i class="fa fa-eye"></i> {{ $totalViews ?? 0 }}</span>
                <span class="ev-stat-value"><i class="fa fa-phone"></i> {{ $totalPhoneClicks ?? 0 }}</span>
            </p>
            <a class="ev-btn ev-btn-accent" href="{{ route('user.statistics') }}" style="margin-top: 8px;">View Statistic</a>
        </div>

        <!-- Verify Photos -->
        <div class="ev-stats-card">
            <div class="ev-verify-preview">
                <a href="{{ url('my-profile/'.$user->slug.'/'.$user->id.'/verify-photo') }}" wire:navigate>
                    <div class="ev-verified-badge"><i class="fa fa-check"></i> Verified photos</div>
                    @if(!empty($user->coverimg->image))
                    <img alt="{{$user->name}}" src="{{smart_asset('/userimages/'.$user->user_id.'/'.$user->id.'/'.$user->coverimg->image)}}" />
                    @elseif(!empty($user->singleimg->image))
                    <img alt="{{$user->name}}" src="{{smart_asset('userimages/'.$user->user_id.'/'.$user->id.'/'.$user->singleimg->image)}}" />
                    @endif
                </a>
                <span class="ev-label">Preview</span>
            </div>
            <h3>Verify photos</h3>
            <p>Verified profiles get more enquiries.</p>
            <a class="ev-btn ev-btn-accent" href="{{ url('my-profile/'.$user->slug.'/'.$user->id.'/verify-photo') }}" wire:navigate>Verify now</a>
            <br>
            <a class="ev-share-link" href="javascript:void(0)" data-copy-btn="#verifLink">
                <svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16">
                    <path fill="currentColor" d="M696-96q-50 0-85-35t-35-85q0-8 1-14.5t3-14.5L342-390q-15 16-35.354 23T264-360q-50 0-85-35t-35-85q0-50 35-85t85-35q22 0 42.5 7.5T342-570l238-145q-2-8-3-14.5t-1-14.5q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35q-22.292 0-42.646-7T618-654L380-509q2 8 3 14.5t1 14.5q0 8-1 14.5t-3 14.5l238 145q15-17 35.354-23.5T696-336q50 0 85 35t35 85q0 50-35 85t-85 35Z" />
                </svg>
                share verification link
            </a>
        </div>

        <!-- Link to Us -->
        <div class="ev-stats-card">
            <div class="ev-link-preview" style="float: right;">
                <img src="{{smart_asset('assets/images/mr-square.jpg')}}" alt="Dubai Escorts">
            </div>
            <h3>Link to us</h3>
            <p>Link to us from your site and get free credits.</p>
            <a class="ev-btn ev-btn-accent" href="javascript:void(0)">Learn more</a>
        </div>
    </div>

    <!-- Upgrade Modal -->
    @if(session('show_upgrade_modal'))
    <div class="ev-modal-overlay active" id="upgradeModal">
        <div class="ev-modal">
            <h2><i class="fa fa-bullhorn"></i> Get more attention!</h2>
            <p>There are <span class="ev-highlight">6566 female escorts</span> listings in Dubai.</p>
            <p>Increase your chance to be noticed. Upgrade your listing to a VIP profile for <span class="ev-highlight">less than $10 a day!</span></p>
            <div class="ev-modal-actions">
                <a class="ev-btn ev-btn-accent" href="/my-profile/{{$user->slug}}/{{$user->id}}/upgrade" style="padding: 12px 32px; font-size: 15px;">
                    Upgrade Profile <i class="fa fa-arrow-right"></i>
                </a>
                <button class="ev-btn ev-btn-outline" onclick="document.getElementById('upgradeModal').classList.remove('active')" style="padding: 8px 24px;">Close</button>
            </div>
        </div>
    </div>
    @endif

</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for verification success from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('verification_success') === '1') {
        const floatAlert = document.getElementById('ev-success-float');
        const floatText = document.getElementById('ev-success-text');
        if (floatAlert && floatText) {
            floatText.textContent = ' Verification photo uploaded successfully! Your photo is pending review.';
            floatAlert.style.display = 'block';
        }
        window.history.replaceState({}, document.title, window.location.pathname);
        setTimeout(function() {
            if (floatAlert) floatAlert.style.display = 'none';
        }, 5000);
    }

    // Check for success message from session storage
    const successMessage = sessionStorage.getItem('successMessage');
    if (successMessage) {
        const floatAlert = document.getElementById('ev-success-float');
        const floatText = document.getElementById('ev-success-text');
        if (floatAlert && floatText) {
            floatText.textContent = successMessage;
            floatAlert.style.display = 'block';
        }
        sessionStorage.removeItem('successMessage');
        setTimeout(function() {
            if (floatAlert) floatAlert.style.display = 'none';
        }, 5000);
    }
});

// Handle Profile Status Toggle (Pause/Resume)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.profile-status-btn');
    if (!btn) return;

    e.preventDefault();
    const profileId = btn.dataset.profileId;
    const action = btn.dataset.action;
    const originalHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

    fetch('/profile-status/' + profileId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ action: action })
    })
    .then(function(response) {
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Request failed');
        }
    })
    .catch(function(error) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        alert('Error updating profile status. Please try again.');
    });
});
</script>
@endpush
</div>
