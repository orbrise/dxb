<div>
    <style>
        .ev-back-bar {
            background: #1f2222;
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
            background: #0D1011;
            border: 1px solid #444;
            border-radius: 5px;
            color: #fff;
            font-size: 13px;
        }

        .review-filters select {
            flex: 1;
            min-width: 100px;
            padding: 8px 35px 8px 12px;
            background: #0D1011;
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
            background: #15191B;
            border: 1px solid #23292B;
            border-radius: 10px;
            padding: 10px;
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
            background: #0D1011;
        }

        .review-item-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #C1F11D;
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
            background: #15191B;
            border: 1px solid #23292B;
            border-radius: 10px;
            overflow: hidden;
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
            background: #0D1011;
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
            background: #C1F11D;
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
            background: #0D1011;
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
            background: #C1F11D;
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
        /* ================================================================
           Mobile-only redesigned Reviews list. Hidden on desktop.
           ================================================================ */
        .r-mobile-page { display: none; }

        @media (max-width: 768px) {
            .r-mobile-page {
                display: block;
                padding: 14px 16px 24px;
                background: #000;
                min-height: calc(100vh - 60px);
            }
            /* Suppress legacy sidebar/detail on mobile. */
            .r-mobile-page ~ .reviews-container { display: none !important; }

            .r-mobile-title {
                color: #fff;
                font-size: 24px;
                font-weight: 700;
                margin: 0 0 6px;
                line-height: 1.2;
            }
            .r-mobile-sub {
                color: #c7cdd1;
                font-size: 14px;
                margin: 0 0 18px;
                line-height: 1.4;
            }

            .r-mobile-summary {
                display: flex;
                align-items: center;
                gap: 14px;
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 14px;
                padding: 18px 18px;
                margin-bottom: 18px;
            }
            .r-mobile-summary-star {
                color: #FFC72C;
                font-size: 32px;
            }
            .r-mobile-summary-score {
                color: #fff;
                font-size: 32px;
                font-weight: 700;
                line-height: 1;
            }
            .r-mobile-summary-text { flex: 1 1 auto; min-width: 0; }
            .r-mobile-summary-total {
                color: #fff;
                font-size: 20px;
                font-weight: 400;
                margin-bottom: 4px;
            }
            .r-mobile-summary-verified {
                color: #c7cdd1;
                font-size: 15px;
            }

            .r-mobile-card {
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 14px;
                padding: 14px 16px;
                margin-bottom: 14px;
            }
            .r-mobile-card-top {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 10px;
            }
            .r-mobile-avatar {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: linear-gradient(135deg, #2a2d30, #16181a);
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 16px;
                flex-shrink: 0;
                overflow: hidden;
            }
            .r-mobile-avatar img { width: 100%; height: 100%; object-fit: cover; }
            .r-mobile-head { flex: 1 1 auto; min-width: 0; }
            .r-mobile-name {
                color: #fff;
                font-weight: 600;
                font-size: 15px;
                line-height: 1.2;
                margin-bottom: 2px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .r-mobile-time {
                color: #8a9099;
                font-size: 12px;
            }
            .r-mobile-hearts {
                display: inline-flex;
                align-items: center;
                gap: 3px;
                background: #1D2222;
                border: 1px solid #242B2D;
                padding: 6px 12px;
                border-radius: 999px;
                flex-shrink: 0;
            }
            .r-mobile-hearts .fa-heart { font-size: 13px; }
            .r-mobile-hearts .on  { color: #C1F11D; }
            .r-mobile-hearts .off { color: #2a2d30; }

            .r-mobile-divider {
                border: none;
                border-top: 1px solid #242B2D;
                margin: 12px 0;
            }
            .r-mobile-quote {
                color: #fff;
                font-size: 14px;
                line-height: 1.5;
                margin: 0;
            }

            .r-mobile-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }
            .r-mobile-profile {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                min-width: 0;
            }
            .r-mobile-profile-label { color: #c7cdd1; }
            .r-mobile-profile-avatar {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                object-fit: cover;
            }
            .r-mobile-profile-name {
                color: #fff;
                font-weight: 500;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .r-mobile-verified {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                color: #1CF3A0;
                font-size: 13px;
                font-weight: 500;
                flex-shrink: 0;
            }

            .r-mobile-empty {
                text-align: center;
                padding: 60px 20px;
                color: #8a9099;
            }
            .r-mobile-empty i { font-size: 48px; margin-bottom: 14px; opacity: 0.5; }
            .r-mobile-empty h3 { color: #fff; margin: 0 0 6px; font-size: 16px; }
            .r-mobile-empty p { margin: 0; font-size: 14px; }
        }

        /* ================================================================
           Desktop redesign — hero + rating summary + 2-column card grid.
           Hidden on mobile where the existing .r-mobile-page design wins.
           ================================================================ */
        .rd-page { display: none; }

        @media (min-width: 769px) {
            [x-cloak] { display: none !important; }
            .rd-page {
                display: block;
                background: #000;
                min-height: calc(100vh - 60px);
                color: #fff;
                padding-bottom: 60px;
            }
            .rd-page ~ .ev-container { display: none !important; }

            .rd-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 32px; }
            .rd-back-wrap .ev-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .rd-back-link {
                color: #C1F11D; font-size: 14px; text-decoration: none;
                display: inline-flex; align-items: center; gap: 6px; font-weight: 500;
            }
            .rd-back-link:hover { color: #d9ff4a; }
            .rd-crumb { color: #fff; font-size: 14px; font-weight: 500; margin: 0; }
            .rd-crumb-spacer { width: 60px; }

            .rd-container { max-width: 1240px; margin: 0 auto; padding: 0 16px; }

            /* Hero: title/subtitle (left) + rating summary pill (right). */
            .rd-hero {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 20px;
                margin-bottom: 22px;
            }
            .rd-hero h2 {
                color: #fff; font-size: 24px; font-weight: 600;
                margin: 0 0 4px; line-height: 1.2;
            }
            .rd-hero p { color: #A6B4B8; font-size: 14px; margin: 0; }

            .rd-rating-card {
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 5px;
                padding: 14px 18px 14px 10px;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .rd-rating-star {
                color: #FFC107;
                font-size: 28px;
                line-height: 1;
            }
            .rd-rating-num {
                color: #fff;
                font-size: 26px;
                font-weight: 700;
                line-height: 1;
                margin-right: 4px;
            }
            .rd-rating-text { line-height: 1.3; }
            .rd-rating-total { color: #fff; font-weight: 400; font-size: 12px; }
            .rd-rating-sub   { color: #AAAEB5; font-size: 11px; margin-top: 4px; }

            /* Two-column grid for the review cards. */
            .rd-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            @media (max-width: 1024px) { .rd-grid { grid-template-columns: 1fr; } }

            .rd-card {
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 6px;
                padding: 14px 20px;
            }
            .rd-card-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding-bottom: 14px;
                margin-bottom: 14px;
                border-bottom: 1px solid #242B2D;
            }
            .rd-user {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
                flex: 1 1 auto;
            }
            .rd-avatar {
                width: 44px; height: 44px; border-radius: 50%;
                background: linear-gradient(135deg, #2a2d30, #16181a);
                color: #fff; display: inline-flex; align-items: center;
                justify-content: center; font-weight: 700; font-size: 16px;
                flex-shrink: 0; overflow: hidden;
            }
            .rd-avatar img { width: 100%; height: 100%; object-fit: cover; }
            .rd-user-text { min-width: 0; }
            .rd-user-name { color: #fff; font-weight: 600; font-size: 15px; }
            .rd-user-date { color: #AAAEB5; font-size: 12.5px; margin-top: 2px;         font-weight: 500;}

            /* Heart rating pill — filled lime for score, muted for the rest. */
            .rd-hearts {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                background: #1A1A21;
                border-radius: 999px;
                padding: 6px 12px;
                flex-shrink: 0;
            }
            .rd-heart        { color: #C1F11D; font-size: 14px; }
            .rd-heart.is-off { color: #3b4247; }

            .rd-divider { border-top: 1px solid #242B2D; margin: 0 -20px 14px; }

            .rd-text {
                color: white;
                font-size: 14px;
                line-height: 1.55;
                margin: 0 0 40px;
            }

            .rd-card-foot {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding-top: 12px;
                border-top: 1px solid #242B2D;
            }
            .rd-reviewed {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: #AAAEB5;
                font-size: 11px;
                min-width: 0;
            }
            .rd-reviewed-avatar {
                width: 24px; height: 24px; border-radius: 50%;
                background: linear-gradient(135deg, #2a2d30, #16181a);
                color: #fff; display: inline-flex; align-items: center;
                justify-content: center; font-weight: 700; font-size: 11px;
                flex-shrink: 0; overflow: hidden;
            }
            .rd-reviewed-avatar img { width: 100%; height: 100%; object-fit: cover; }
            .rd-reviewed-name { color: #fff; font-weight: 500; }

            .rd-verified {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                color: #17F09C;
                font-size: 11px;
                font-weight: 400;
            }

            .rd-empty {
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 12px;
                padding: 60px 20px;
                text-align: center;
                color: #8a9398;
                grid-column: 1 / -1;
            }
            .rd-empty i { font-size: 36px; opacity: 0.5; display: block; margin-bottom: 12px; }
            .rd-empty h3 { color: #fff; font-size: 18px; margin: 0 0 6px; }
            .rd-empty p { margin: 0; font-size: 14px; }
        }
    </style>

    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    @endpush

    {{-- Desktop redesign — sits OUTSIDE .ev-container so the full-width
         back bar stretches edge-to-edge AND the sibling CSS rule
         (.rd-page ~ .ev-container { display:none }) can hide the legacy
         communication-nav + sidebar/detail panel on desktop. Mobile keeps
         the legacy wrapper so the .r-mobile-page design still works. --}}
    <div class="rd-page">
        <div class="rd-back-wrap">
            <div class="ev-container">
                <a href="{{ url('/') }}" wire:navigate class="rd-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
                <h1 class="rd-crumb">Reviews</h1>
                <div class="rd-crumb-spacer"></div>
            </div>
        </div>

        <div class="rd-container">
            <div class="rd-hero">
                <div>
                    <h2>Reviews &amp; Ratings</h2>
                    <p>Feedback and verified ratings from completed luxury escort bookings.</p>
                </div>
                @if($totalReviews > 0)
                    <div class="rd-rating-card">
                        <span class="rd-rating-star">&#9733;</span>
                        <span class="rd-rating-num">{{ number_format((float) $avgRating, 1) }}</span>
                        <div class="rd-rating-text">
                            <div class="rd-rating-total">{{ $totalReviews }} Total {{ $totalReviews === 1 ? 'Rating' : 'Ratings' }}</div>
                            <div class="rd-rating-sub">100% Verified Feedback</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="rd-grid">
                @forelse($reviews as $review)
                    @php
                        $rdReviewer = $review->user ? ($review->user->name ?? $review->user->email) : 'Guest';
                        $rdInitial  = strtoupper(substr($rdReviewer, 0, 1));
                        $rdProfileName = $review->profile ? $review->profile->name : 'Profile';
                        $rdProfileInitial = strtoupper(substr($rdProfileName, 0, 1));
                        $rdStars = (int) $review->star;
                    @endphp
                    <div class="rd-card" wire:key="rd-card-{{ $review->id }}">
                        <div class="rd-card-head">
                            <div class="rd-user">
                                <div class="rd-avatar">{{ $rdInitial }}</div>
                                <div class="rd-user-text">
                                    <div class="rd-user-name">{{ $rdReviewer }}</div>
                                    <div class="rd-user-date">{{ \Carbon\Carbon::parse($review->created_at)->format('F d, Y') }}</div>
                                </div>
                            </div>
                            <div class="rd-hearts" aria-label="{{ $rdStars }} out of 5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-heart rd-heart {{ $i > $rdStars ? 'is-off' : '' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <p class="rd-text">&ldquo;{{ $review->review }}&rdquo;</p>

                        <div class="rd-card-foot">
                            <div class="rd-reviewed">
                                <span>Reviewed Profile:</span>
                                <span class="rd-reviewed-avatar">{{ $rdProfileInitial }}</span>
                                <span class="rd-reviewed-name">{{ $rdProfileName }}</span>
                            </div>
                            <span class="rd-verified">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6 11.25C4.60761 11.25 3.27226 10.6969 2.28769 9.71231C1.30312 8.72774 0.75 7.39239 0.75 6C0.75 4.60761 1.30312 3.27226 2.28769 2.28769C3.27226 1.30312 4.60761 0.75 6 0.75C7.39239 0.75 8.72774 1.30312 9.71231 2.28769C10.6969 3.27226 11.25 4.60761 11.25 6C11.25 7.39239 10.6969 8.72774 9.71231 9.71231C8.72774 10.6969 7.39239 11.25 6 11.25ZM6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12Z" fill="#1CF3A0"/>
                                    <path d="M8.22727 3.72747L8.21227 3.74397L5.60752 7.06272L4.03777 5.49222C3.93113 5.39286 3.7901 5.33876 3.64437 5.34134C3.49865 5.34391 3.35961 5.40294 3.25655 5.506C3.15349 5.60906 3.09446 5.7481 3.09188 5.89382C3.08931 6.03955 3.14341 6.18059 3.24277 6.28722L5.22727 8.27247C5.28073 8.32583 5.34439 8.36788 5.41445 8.39611C5.48452 8.42433 5.55955 8.43816 5.63508 8.43676C5.7106 8.43536 5.78507 8.41876 5.85404 8.38796C5.92301 8.35716 5.98507 8.31278 6.03652 8.25747L9.03052 4.51497C9.13246 4.40796 9.1882 4.26514 9.18568 4.11737C9.18317 3.9696 9.12259 3.82875 9.01706 3.72529C8.91152 3.62182 8.76951 3.56405 8.62171 3.56446C8.47392 3.56486 8.33223 3.62342 8.22727 3.72747Z" fill="#1CF3A0"/>
                                </svg>
                                Verified
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="rd-empty">
                        <i class="fa fa-star"></i>
                        <h3>No reviews yet</h3>
                        <p>Verified reviews from completed bookings will appear here.</p>
                    </div>
                @endforelse
            </div>

            @if($reviews->hasPages())
                <div class="mt-3">{{ $reviews->links() }}</div>
            @endif
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

                    {{-- Mobile-only redesigned reviews list. Hidden on desktop
                         via CSS. The legacy sidebar/detail layout below is
                         suppressed on mobile when this is visible. --}}
                    <div class="r-mobile-page">
                        <h1 class="r-mobile-title">Reviews &amp; Ratings</h1>
                        <p class="r-mobile-sub">Feedback and verified ratings from completed luxury escort bookings.</p>

                        <div class="r-mobile-summary">
                            <i class="fa fa-star r-mobile-summary-star"></i>
                            <span class="r-mobile-summary-score">{{ number_format((float) $avgRating ?? 0, 1) }}</span>
                            <div class="r-mobile-summary-text">
                                <div class="r-mobile-summary-total">{{ $totalReviews }} Total Ratings</div>
                                <div class="r-mobile-summary-verified">100% Verified Feedback</div>
                            </div>
                        </div>

                        @forelse($reviews as $review)
                            @php
                                $rUser = $review->user ? ($review->user->name ?? $review->user->email) : 'Anonymous';
                                $rInitial = strtoupper(substr($rUser, 0, 1));
                                $rTime = $review->created_at->diffForHumans(null, true) . ' ago';
                                $rStar = (int) ($review->star ?? 5);
                                $rProfile = $review->profile?->name ?? 'Unknown';
                                $rAvatar = $review->user?->avatar ?? null;
                            @endphp
                            <div class="r-mobile-card">
                                <div class="r-mobile-card-top">
                                    <div class="r-mobile-avatar">
                                        @if($rAvatar)
                                            <img src="{{ user_avatar_url($review->user) }}" alt="" onerror="this.style.display='none'">
                                        @else
                                            {{ $rInitial }}
                                        @endif
                                    </div>
                                    <div class="r-mobile-head">
                                        <div class="r-mobile-name">{{ $rUser }}</div>
                                        <div class="r-mobile-time">{{ $rTime }}</div>
                                    </div>
                                    <div class="r-mobile-hearts">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="fa fa-heart {{ $i < $rStar ? 'on' : 'off' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <hr class="r-mobile-divider">
                                <div class="r-mobile-quote">&ldquo;{{ $review->review }}&rdquo;</div>
                                <hr class="r-mobile-divider">
                                <div class="r-mobile-footer">
                                    <div class="r-mobile-profile">
                                        <span class="r-mobile-profile-label">Reviewed Profile:</span>
                                        @if($review->profile?->image)
                                            <img src="{{ asset('storage/' . $review->profile->image) }}" alt="" class="r-mobile-profile-avatar" onerror="this.style.display='none'">
                                        @endif
                                        <span class="r-mobile-profile-name">{{ $rProfile }}</span>
                                    </div>
                                    <span class="r-mobile-verified">
                                        <svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 9.375C3.83968 9.375 2.72688 8.91406 1.90641 8.09359C1.08594 7.27312 0.625 6.16032 0.625 5C0.625 3.83968 1.08594 2.72688 1.90641 1.90641C2.72688 1.08594 3.83968 0.625 5 0.625C6.16032 0.625 7.27312 1.08594 8.09359 1.90641C8.91406 2.72688 9.375 3.83968 9.375 5C9.375 6.16032 8.91406 7.27312 8.09359 8.09359C7.27312 8.91406 6.16032 9.375 5 9.375ZM5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10Z" fill="#1CF3A0"/>
                                            <path d="M6.85573 3.10606L6.84323 3.11981L4.6726 5.88543L3.36448 4.57668C3.27562 4.49388 3.15809 4.44881 3.03665 4.45095C2.91521 4.45309 2.79935 4.50229 2.71347 4.58817C2.62758 4.67405 2.57839 4.78992 2.57624 4.91136C2.5741 5.0328 2.61918 5.15033 2.70198 5.23918L4.35573 6.89356C4.40028 6.93803 4.45333 6.97307 4.51172 6.99659C4.57011 7.02012 4.63263 7.03164 4.69557 7.03047C4.75851 7.0293 4.82056 7.01547 4.87804 6.9898C4.93551 6.96413 4.98723 6.92715 5.0301 6.88106L7.5251 3.76231C7.61006 3.67314 7.65651 3.55412 7.65441 3.43098C7.65231 3.30783 7.60183 3.19046 7.51389 3.10424C7.42594 3.01802 7.3076 2.96988 7.18444 2.97022C7.06128 2.97056 6.9432 3.01935 6.85573 3.10606Z" fill="#1CF3A0"/>
                                        </svg>
                                        Verified
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="r-mobile-empty">
                                <i class="fa fa-star"></i>
                                <h3>No reviews yet</h3>
                                <p>Verified ratings from completed bookings will appear here.</p>
                            </div>
                        @endforelse
                    </div>

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
