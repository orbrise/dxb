<div>
    <style>
        .ev-back-bar {
            background: #131616;
            padding: 12px 0;
        }
        .ev-back-bar a { color: #C1F11D; text-decoration: none; font-size: 15px; }
        .ev-back-bar h1 { color: #fff; font-size: 18px; font-weight: 600; margin: 0; }
        .ev-back-bar h1 a { color: #fff; text-decoration: none; }
        .ev-container { max-width: 1200px; margin: 0 auto; padding: 0 16px; }

        /* WhatsApp-style Reviews Container */
        .reviews-container {
            display: flex;
            height: calc(100vh - 200px);
            min-height: 500px;
            background: transparent;
            border-radius: 10px;
            overflow: hidden;
            gap: 12px;
        }

        /* Left Sidebar - Reviews List */
        .review-sidebar {
            width: 380px;
            min-width: 380px;
            background: #0D1011;
            display: flex;
            flex-direction: column;
        }

        .review-sidebar-header {
            padding: 15px 20px;
            background: #0a0a0a;
            
        }

        .review-sidebar-header h4 {
            color: #c1f11d;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .review-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .review-filters input {
            flex: 1;
            min-width: 100px;
            padding: 8px 12px;
            background: #333;
            border: 1px solid #444;
            border-radius: 5px;
            color: #fff;
            font-size: 13px;
        }

        .review-filters select {
            flex: 1;
            min-width: 100px;
            padding: 8px 35px 8px 12px;
            background: #333;
            border: 1px solid #444;
            border-radius: 5px;
            color: #fff;
            font-size: 13px;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 10px;
        }

        .review-filters input::placeholder {
            color: #888;
        }

        .review-filters input:focus,
        .review-filters select:focus {
            outline: none;
            border-color: #c1f11d;
        }

        .review-list {
            flex: 1;
            overflow-y: auto;
        }

        .review-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 1px solid #2a2a2a;
            transition: background 0.2s;
        }

        .review-item:hover {
            background: #2a2a2a;
        }

        .review-item.active {
            background: #333;
        }

        .review-item-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c1f11d 0%, #d4a017 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a1a;
            font-weight: bold;
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .review-item-content {
            flex: 1;
            min-width: 0;
        }

        .review-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .review-item-name {
            color: #fff;
            font-weight: 600;
            font-size: 15px;
        }

        .review-item-time {
            color: #888;
            font-size: 12px;
        }

        .review-item-stars {
            color: #c1f11d;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .review-item-preview {
            color: #aaa;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .review-item-profile {
            color: #888;
            font-size: 11px;
            margin-top: 2px;
        }

        .review-item-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .unreplied-badge {
            background: #c1f11d;
            color: #1a1a1a;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        /* Right Panel - Review Detail */
        .review-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #0D1011;
        }

        .review-detail-header {
            padding: 15px 20px;
            background: #2a2a2a;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .review-detail-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .review-detail-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c1f11d 0%, #c1f11d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a1a;
            font-weight: bold;
            font-size: 18px;
        }

        .review-detail-name {
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }

        .review-detail-profile {
            color: #888;
            font-size: 13px;
        }

        .review-detail-stars {
            color: #c1f11d;
            font-size: 14px;
            margin-top: 2px;
        }

        .review-detail-actions {
            display: flex;
            gap: 10px;
        }

        .review-detail-actions button {
            background: #333;
            border: none;
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .review-detail-actions button:hover {
            background: #444;
        }

        .review-detail-actions button.delete:hover {
            background: #dc3545;
        }

        .mobile-back-btn {
            display: none;
            background: none;
            border: none;
            color: #c1f11d;
            font-size: 14px;
            cursor: pointer;
            padding: 0;
        }

        /* Review & Reply Area */
        .review-reply-area {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .review-bubble {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 15px;
            position: relative;
        }

        .review-bubble.review-incoming {
            background: #2a2a2a;
            color: #fff;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }

        .review-bubble.review-outgoing {
            background: linear-gradient(135deg, #c1f11d 0%, #d4a017 100%);
            color: #1a1a1a;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .review-label {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 5px;
            opacity: 0.8;
        }

        .review-text {
            font-size: 14px;
            line-height: 1.5;
        }

        .review-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
            text-align: right;
        }

        /* Reply Input */
        .reply-input {
            padding: 15px 20px;
            background: #2a2a2a;
            border-top: 1px solid #333;
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .reply-input textarea {
            flex: 1;
            background: #333;
            border: none;
            border-radius: 20px;
            padding: 12px 18px;
            color: #fff;
            font-size: 14px;
            resize: none;
            max-height: 120px;
            min-height: 45px;
        }

        .reply-input textarea::placeholder {
            color: #888;
        }

        .reply-input textarea:focus {
            outline: none;
        }

        .reply-input button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c1f11d 0%, #d4a017 100%);
            border: none;
            color: #1a1a1a;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: transform 0.2s;
        }

        .reply-input button:hover {
            transform: scale(1.1);
        }

        /* Empty State */
        .review-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
            text-align: center;
            padding: 40px;
        }

        .review-empty i {
            font-size: 80px;
            margin-bottom: 20px;
            color: #444;
        }

        .review-empty h3 {
            color: #888;
            margin-bottom: 10px;
        }

        .review-empty p {
            color: #666;
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 28px;
            margin: 0 0 5px 0;
            color: #fff;
        }

        .stat-card h3.gold {
            color: #c1f11d;
        }

        .stat-card h3.red {
            color: #dc3545;
        }

        .stat-card h3.green {
            color: #28a745;
        }

        .stat-card p {
            margin: 0;
            color: #888;
            font-size: 14px;
        }

        /* Alerts */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .alert-info {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            /* Header spacing */
            #header { margin-bottom: 0 !important; }

            /* Back bar */
            .ev-back-bar { position: sticky; top: 0; z-index: 100; }
            .ev-back-bar .ev-desktop-back { display: none !important; }
            .ev-back-bar .ev-mobile-back { display: inline !important; }
            .ev-back-bar h1 { font-size: 15px !important; }

            /* When review is open, hide back bar and site header */
            .ev-back-bar.ev-review-open { display: none !important; }
            body:has(.ev-review-open) .ev-header { display: none !important; }
            body:has(.ev-review-open) #header { display: none !important; }

            /* Container */
            .ev-container { padding: 0 16px !important; padding-top: 0 !important; padding-bottom: 0 !important; }

            /* Communication nav - hide on mobile */
            .ev-comm-nav, .communication-nav { display: none !important; }

            /* Remove extra spacing */
            #my-reviews { margin: 0 !important; }
            .mb-3 { margin-bottom: 0 !important; }

            /* Stats - hide on mobile */
            .stats-row { display: none !important; }

            /* Reviews container */
            .reviews-container {
                height: calc(100vh - 60px) !important;
                min-height: unset !important;
                border-radius: 0 !important;
                gap: 0 !important;
                position: relative !important;
                flex-direction: column !important;
            }

            /* Sidebar takes full screen */
            .review-sidebar {
                width: 100% !important;
                min-width: unset !important;
                height: 100% !important;
                max-height: 100% !important;
                border-radius: 0 !important;
                background: #000 !important;
                flex: 1 !important;
            }
            .review-sidebar.hidden { display: none !important; }

            /* Sidebar header */
            .review-sidebar-header {
                padding: 12px 0 !important;
                background: #000 !important;
            }
            .review-sidebar-header h4 { display: none !important; }

            /* Filters / search bar */
            .review-filters {
                display: flex !important;
                gap: 8px !important;
            }
            .review-filters input {
                flex: 2 !important;
                padding: 11px 14px !important;
                border-radius: 5px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
            }
            .review-filters select {
                flex: 1 !important;
                padding: 11px 10px !important;
                border-radius: 5px !important;
                font-size: 13px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
            }

            /* Reviews list */
            .review-list { background: #000 !important; }
            .review-item {
                padding: 14px 0 !important;
                border-bottom: 1px solid #1a1a1a !important;
            }
            .review-item:hover { background: #111 !important; }
            .review-item.active {
                background: #111 !important;
            }
            .review-item-avatar {
                width: 50px !important;
                height: 50px !important;
                margin-right: 14px !important;
            }
            .review-item-name { font-size: 15px !important; }
            .review-item-preview {
                font-size: 13px !important;
                color: #888 !important;
                white-space: normal !important;
                -webkit-line-clamp: 2;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .review-item-time { font-size: 10px !important; display: none !important; }
            .review-item-stars { font-size: 11px !important; }
            .unreplied-badge {
                font-size: 10px !important;
                padding: 2px 7px !important;
            }

            /* Review main panel */
            .review-main {
                display: none !important;
                height: 100% !important;
                background: #000 !important;
                border-radius: 0 !important;
            }
            .review-main.fullscreen {
                display: flex !important;
                height: 100% !important;
            }

            /* Hide the extra back button bar above header */
            .review-main > div[style*="background: #2a2a2a"] { display: none !important; }

            /* Review header - fixed top nav replacing site header */
            .review-detail-header {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 1001 !important;
                padding: 14px 16px !important;
                padding-top: max(14px, env(safe-area-inset-top)) !important;
                background: #0a0a0a !important;
                border-bottom: 1px solid #1a1a1a !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .review-detail-info {
                display: flex !important;
                align-items: center !important;
                width: 100% !important;
                position: relative !important;
                gap: 0 !important;
            }
            .mobile-back-btn {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
            }
            .review-detail-avatar { display: none !important; }
            .review-detail-info > div {
                text-align: center !important;
                width: 100% !important;
            }
            .review-detail-name {
                font-size: 15px !important;
                font-weight: 600 !important;
                color: #fff !important;
            }
            .review-detail-profile {
                font-size: 12px !important;
                color: #C1F11D !important;
            }
            .review-detail-stars { display: none !important; }
            .review-detail-actions { display: none !important; }

            /* Review & Reply area */
            .review-reply-area {
                background: #000 !important;
                padding: 16px !important;
                padding-top: 70px !important;
                gap: 4px !important;
            }
            .review-bubble {
                max-width: 80% !important;
                border-radius: 14px !important;
                padding: 10px 14px !important;
            }
            .review-bubble.review-incoming {
                background: #1a1a1a !important;
                color: #fff !important;
                border: none !important;
            }
            .review-bubble.review-outgoing {
                background: #C1F11D !important;
                color: #000 !important;
            }
            .review-text { font-size: 14px !important; }
            .review-time { font-size: 11px !important; opacity: 0.6 !important; }
            .review-label { font-size: 10px !important; }

            /* Reply input */
            .reply-input {
                padding: 10px 16px !important;
                background: #0a0a0a !important;
                border-top: 1px solid #1a1a1a !important;
            }
            .reply-input textarea {
                padding: 10px 16px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
                border-radius: 5px !important;
            }
            .reply-input button {
                width: 40px !important;
                height: 40px !important;
                font-size: 16px !important;
                border-radius: 5px !important;
                background: #C1F11D !important;
            }

            /* Empty state */
            .review-empty { padding: 40px 20px !important; }
            .review-empty h3 { font-size: 16px !important; }
            .review-empty p { font-size: 13px !important; }
        }
    </style>

    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    @endpush

    <!-- Back Bar -->
    <div class="ev-back-bar {{ $selectedReview ? 'ev-review-open' : '' }}">
        <div class="ev-container" style="display:flex; align-items:center; justify-content:center; position:relative;">
            <a href="/female-escorts-in-dubai" style="position:absolute; left:16px;">
                <span class="ev-desktop-back"><i class="fa fa-angle-left"></i> Escorts in Dubai</span>
                <span class="ev-mobile-back" style="display:none;"><i class="fa fa-angle-left"></i> Back</span>
            </a>
            <h1><span class="ev-desktop-back">My Reviews</span><span class="ev-mobile-back" style="display:none;">Reviews</span></h1>
        </div>
    </div>

    <div class="ev-container {{ $selectedReview ? 'hide-nav-mobile' : '' }}" style="padding-top: 8px; padding-bottom: 40px;">
                @include('components.communication-nav')

                <div class="mb-3 clearfix" style="clear: both;" id="my-reviews">
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session()->has('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    {{-- Statistics Cards --}}
                    {{-- <div class="stats-row">
                        <div class="stat-card">
                            <h3>{{ $totalReviews }}</h3>
                            <p>Total Reviews</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="gold">
                                @if($avgRating > 0)
                                    {{ number_format($avgRating, 1) }} <i class="fa fa-star"></i>
                                @else
                                    N/A
                                @endif
                            </h3>
                            <p>Average Rating</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="red">{{ $unrepliedCount }}</h3>
                            <p>Unreplied</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="green">{{ $repliedCount }}</h3>
                            <p>Replied</p>
                        </div>
                    </div> --}}

                    {{-- WhatsApp-style Reviews Container --}}
                    <div class="reviews-container" style="position: relative;">
                        {{-- Left Sidebar - Reviews List --}}
                        <div class="review-sidebar {{ $selectedReview ? 'hidden' : '' }}">
                            <div class="review-sidebar-header">
                                <h4><i class="fa fa-star"></i> Reviews</h4>
                                <div class="review-filters">
                                    <input 
                                        type="text" 
                                        placeholder="Search reviews..." 
                                        wire:model.live.debounce.300ms="searchTerm">
                                    <select wire:model.live="filterRating">
                                        <option value="all">All Stars</option>
                                        <option value="5">5 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="2">2 Stars</option>
                                        <option value="1">1 Star</option>
                                    </select>
                                    <select wire:model.live="filterStatus">
                                        <option value="all">All</option>
                                        <option value="unreplied">Unreplied</option>
                                        <option value="replied">Replied</option>
                                    </select>
                                </div>
                            </div>
                            <div class="review-list">
                                @forelse($reviews as $review)
                                    <div class="review-item {{ $selectedReview && $selectedReview->id == $review->id ? 'active' : '' }}" 
                                         wire:click="selectReview({{ $review->id }})"
                                         onclick="scrollToReply()">
                                        <div class="review-item-avatar">
                                            {{ strtoupper(substr($review->user->name ?? $review->user->email ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="review-item-content">
                                            <div class="review-item-header">
                                                <span class="review-item-name">
                                                    {{ $review->user ? ($review->user->name ?? $review->user->email) : 'Guest' }}
                                                </span>
                                                <div class="review-item-status">
                                                    @if(!$review->reply)
                                                        <span class="unreplied-badge">NEW</span>
                                                    @endif
                                                    <span class="review-item-time">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div class="review-item-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->star)
                                                        <i class="fa fa-star"></i>
                                                    @else
                                                        <i class="fa fa-star-o"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <div class="review-item-preview">{{ Str::limit($review->review, 60) }}</div>
                                            @if($review->profile)
                                                <div class="review-item-profile">For: {{ $review->profile->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="review-empty" style="padding: 40px;">
                                        <i class="fa fa-star-o"></i>
                                        <h3>No reviews found</h3>
                                        <p>
                                            @if($searchTerm || $filterStatus !== 'all' || $filterRating !== 'all')
                                                Try adjusting your filters.
                                            @else
                                                You haven't received any reviews yet.
                                            @endif
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Right Panel - Review Detail & Reply --}}
                        <div class="review-main {{ $selectedReview ? 'fullscreen' : '' }}">
                            @if($selectedReview)
                                {{-- Review Header --}}
                                <div class="review-detail-header">
                                    <div class="review-detail-info">
                                        <button class="mobile-back-btn" wire:click="closeModal">
                                            <i class="fa fa-angle-left"></i> Back
                                        </button>
                                        <div class="review-detail-avatar">
                                            {{ strtoupper(substr($selectedReview->user->name ?? $selectedReview->user->email ?? 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="review-detail-name">{{ $selectedReview->user ? ($selectedReview->user->name ?? $selectedReview->user->email) : 'Guest' }}</div>
                                            @if($selectedReview->profile)
                                                <div class="review-detail-profile">To: {{ $selectedReview->profile->name }}</div>
                                            @endif
                                            <div class="review-detail-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $selectedReview->star)
                                                        <i class="fa fa-star"></i>
                                                    @else
                                                        <i class="fa fa-star-o"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review-detail-actions">
                                        <button wire:click="deleteReview({{ $selectedReview->id }})" class="delete" onclick="return confirm('Delete this review?')" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button wire:click="closeModal" title="Close">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Review & Reply Area --}}
                                <div class="review-reply-area">
                                    {{-- Review Bubble --}}
                                    <div class="review-bubble review-incoming">
                                        <div class="review-label"><i class="fa fa-star"></i> Review</div>
                                        <div class="review-text">{{ $selectedReview->review }}</div>
                                        <div class="review-time">{{ $selectedReview->created_at->format('M d, Y h:i A') }}</div>
                                    </div>

                                    {{-- Reply Bubble (if exists) --}}
                                    @if($selectedReview->reply)
                                        <div class="review-bubble review-outgoing">
                                            <div class="review-label"><i class="fa fa-reply"></i> Your Reply</div>
                                            <div class="review-text">{{ $selectedReview->reply }}</div>
                                            <div class="review-time">{{ $selectedReview->updated_at->format('M d, Y h:i A') }}</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Reply Input --}}
                                <form wire:submit.prevent="sendReply" class="reply-input">
                                    <textarea 
                                        wire:model="reply" 
                                        placeholder="{{ $selectedReview->reply ? 'Update your reply...' : 'Type your reply...' }}"
                                        rows="1"
                                    ></textarea>
                                    <button type="submit" title="Send Reply">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Empty state when no review selected --}}
                                <div class="review-empty">
                                    <i class="fa fa-star"></i>
                                    <h3>Select a review</h3>
                                    <p>Choose a review from the left to view and reply</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($reviews->hasPages())
                    <div class="mt-3">
                        {{ $reviews->links() }}
                    </div>
                    @endif
                </div>
            </div>

    <script>
        function scrollToReply() {
            setTimeout(() => {
                const replyArea = document.querySelector('.review-reply-area');
                if (replyArea) {
                    replyArea.scrollTop = replyArea.scrollHeight;
                }
            }, 100);
        }
    </script>
</div>
