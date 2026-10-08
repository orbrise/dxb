<div>
    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    @endpush

    <style>
        .ev-back-bar {
            background: #1f2222;
            padding: 12px 0;
        }
        .ev-back-bar a { color: #C1F11D; text-decoration: none; font-size: 15px; }
        .ev-back-bar h1 { color: #fff; font-size: 18px; font-weight: 600; margin: 0; }
        .ev-back-bar h1 a { color: #fff; text-decoration: none; }
        .ev-container { max-width: 1200px; margin: 0 auto; padding: 0 16px; }

        /* Stats Cards */
        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            flex: 1;
            background: #2a2a2a;
            border-radius: 8px;
            padding: 15px 20px;
            text-align: center;
            border: 1px solid #333;
        }

        .stat-card h3 {
            font-size: 28px;
            margin: 0 0 5px 0;
            color: #fff;
        }

        .stat-card h3.text-warning { color: #c1f11d; }
        .stat-card h3.text-success { color: #28a745; }

        .stat-card p {
            color: #888;
            margin: 0;
            font-size: 14px;
        }

        /* Questions Container */
        .questions-container {
            display: flex;
            height: calc(100vh - 280px);
            min-height: 400px;
            background: transparent;
            border-radius: 8px;
            overflow: hidden;
            gap: 12px;
          
        }

        /* Left Panel - Questions List */
        .questions-sidebar {
            width: 400px;
            min-width: 350px;
            background: #0D1011;
            
            display: flex;
            flex-direction: column;
        }

        .questions-sidebar-header {
            padding: 15px 0px;
            
            
        }

        .questions-sidebar-header h4 {
            color: #c1f11d;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .questions-search {
            display: flex;
            gap: 10px;
        }

        .questions-search input {
            flex: 1;
            padding: 10px 15px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
        }

        .questions-search input:focus {
            outline: none;
            border-color: #c1f11d;
        }

        .questions-search input::placeholder {
            color: #888;
        }

        .questions-filter {
            padding: 8px 35px 8px 15px;
            background: #1a1a1a;
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

        .questions-filter:focus {
            outline: none;
            border-color: #c1f11d;
            padding:0px 10px;
        }

        /* Questions List */
        .questions-list {
            flex: 1;
            overflow-y: auto;
            background: #15191B;
            border: 1px solid #23292B;
            border-radius: 10px;
            padding: 10px;
        }

        .question-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            cursor: pointer;
            border-bottom: 1px solid #333;
            transition: background 0.2s;
        }

        .question-item:hover {
            background: #0D1011;
        }

        .question-item.active {
            background: #3d3d3d;
            border-left: 3px solid #c1f11d;
        }

        .question-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #C1F11D;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .question-info {
            flex: 1;
            min-width: 0;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .question-from {
            color: #fff;
            font-weight: 600;
            font-size: 14px;
        }

        .question-time {
            color: #888;
            font-size: 11px;
        }

        .question-item.unanswered .question-time {
            color: #c1f11d;
        }

        .question-preview {
            color: #aaa;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .question-profile {
            color: #888;
            font-size: 11px;
            margin-top: 3px;
        }

        .unanswered-badge {
            background: #c1f11d;
            color: #1a1a1a;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 8px;
            margin-left: 8px;
        }

        /* Right Panel - Answer Area */
        .question-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #15191B;
            border: 1px solid #23292B;
            border-radius: 10px;
            overflow: hidden;
        }

        .question-detail-header {
            padding: 15px 20px;
            background: #0D1011;
            border-bottom: 1px solid #444;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .question-detail-info {
            display: flex;
            align-items: center;
        }

        .question-detail-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #C1F11D;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-right: 12px;
        }

        .question-detail-name {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
        }

        .question-detail-profile {
            color: #888;
            font-size: 13px;
        }

        .question-detail-actions button {
            background: none;
            border: none;
            color: #888;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            margin-left: 5px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .question-detail-actions button:hover {
            color: #c1f11d;
            background: rgba(244, 184, 39, 0.1);
        }

        .question-detail-actions button.delete:hover {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }

        /* Q&A Area */
        .qa-area {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23222222' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .qa-bubble {
            max-width: 75%;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            position: relative;
        }

        .qa-question {
            background: #2a2a2a;
            color: #fff;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }

        .qa-answer {
            background: #c1f11d;
            color: #1a1a1a;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .qa-label {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 5px;
            opacity: 0.7;
        }

        .qa-text {
            font-size: 14px;
            line-height: 1.5;
        }

        .qa-time {
            font-size: 11px;
            opacity: 0.7;
            text-align: right;
            margin-top: 8px;
        }

        /* Answer Input */
        .answer-input {
            padding: 15px 20px;
            background: #2a2a2a;
            border-top: 1px solid #333;
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .answer-input textarea {
            flex: 1;
            padding: 12px 20px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 20px;
            color: #fff;
            font-size: 14px;
            resize: none;
            max-height: 120px;
            min-height: 45px;
        }

        .answer-input textarea:focus {
            outline: none;
            border-color: #c1f11d;
        }

        .answer-input textarea::placeholder {
            color: #888;
        }

        .answer-input button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #c1f11d;
            border: none;
            color: #1a1a1a;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .answer-input button:hover {
            background: #d4a017;
            transform: scale(1.05);
        }

        /* Empty State */
        .question-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
            text-align: center;
            padding: 40px;
        }

        .question-empty i {
            font-size: 80px;
            margin-bottom: 20px;
            color: #444;
        }

        .question-empty h3 {
            color: #888;
            margin-bottom: 10px;
        }

        .question-empty p {
            color: #666;
            max-width: 300px;
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

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            /* Header spacing */
            #header { margin-bottom: 0 !important; }

            /* Back bar */
            .ev-back-bar { position: sticky; top: 0; z-index: 100; }
            .ev-back-bar .ev-desktop-back { display: none !important; }
            .ev-back-bar .ev-mobile-back { display: inline !important; }
            .ev-back-bar h1 { font-size: 15px !important; }

            /* When question is open, hide back bar and site header */
            .ev-back-bar.ev-question-open { display: none !important; }
            body:has(.ev-question-open) .ev-header { display: none !important; }
            body:has(.ev-question-open) #header { display: none !important; }

            /* Container */
            .ev-container { padding: 0 16px !important; padding-top: 0 !important; padding-bottom: 0 !important; }

            /* Communication nav - hide on mobile */
            .ev-comm-nav, .communication-nav { display: none !important; }

            /* Remove extra spacing */
            #my-questions { margin: 0 !important; }
            .mb-3 { margin-bottom: 0 !important; }

            /* Stats - hide on mobile */
            .stats-row { display: none !important; }

            /* Questions container */
            .questions-container {
                height: calc(100vh - 60px) !important;
                min-height: unset !important;
                border-radius: 0 !important;
                gap: 0 !important;
                position: relative !important;
                flex-direction: column !important;
            }

            /* Sidebar takes full screen */
            .questions-sidebar {
                width: 100% !important;
                min-width: unset !important;
                height: 100% !important;
                max-height: 100% !important;
                border-radius: 0 !important;
                background: #000 !important;
                flex: 1 !important;
            }
            .questions-sidebar.hidden { display: none !important; }

            /* Sidebar header */
            .questions-sidebar-header {
                padding: 12px 0 !important;
                background: #000 !important;
            }
            .questions-sidebar-header h4 { display: none !important; }

            /* Search bar */
            .questions-search {
                display: flex !important;
                gap: 8px !important;
            }
            .questions-search input {
                flex: 1 !important;
                padding: 11px 14px !important;
                border-radius: 5px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
            }
            .questions-filter {
                padding: 11px 14px !important;
                border-radius: 5px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
            }

            /* Questions list */
            .questions-list { background: #000 !important; }
            .question-item {
                padding: 14px 0 !important;
                border-bottom: 1px solid #1a1a1a !important;
            }
            .question-item:hover { background: #111 !important; }
            .question-item.active {
                background: #111 !important;
                border-left: none !important;
            }
            .question-avatar {
                width: 50px !important;
                height: 50px !important;
                margin-right: 14px !important;
            }
            .question-from { font-size: 15px !important; }
            .question-preview {
                font-size: 13px !important;
                color: #888 !important;
                white-space: normal !important;
                -webkit-line-clamp: 2;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .question-time { font-size: 10px !important; display: none !important; }
            .unanswered-badge {
                font-size: 10px !important;
                padding: 2px 7px !important;
            }

            /* Question main panel */
            .question-main {
                display: none !important;
                height: 100% !important;
                background: #000 !important;
                border-radius: 0 !important;
            }
            .question-main.fullscreen {
                display: flex !important;
                height: 100% !important;
            }

            /* Question header - fixed top nav replacing site header */
            .question-detail-header {
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
            .question-detail-info {
                display: flex !important;
                align-items: center !important;
                width: 100% !important;
                position: relative !important;
            }
            .mobile-back-btn {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
            }
            .question-detail-avatar { display: none !important; }
            .question-detail-info > div {
                text-align: center !important;
                width: 100% !important;
            }
            .question-detail-name {
                font-size: 15px !important;
                font-weight: 600 !important;
                color: #fff !important;
            }
            .question-detail-profile {
                font-size: 12px !important;
                color: #C1F11D !important;
            }
            .question-detail-actions { display: none !important; }

            /* Q&A area */
            .qa-area {
                background: #000 !important;
                padding: 16px !important;
                padding-top: 70px !important;
                gap: 4px !important;
            }
            .qa-bubble {
                max-width: 80% !important;
                border-radius: 14px !important;
                padding: 10px 14px !important;
            }
            .qa-question {
                background: #1a1a1a !important;
                color: #fff !important;
                border: none !important;
            }
            .qa-answer {
                background: #C1F11D !important;
                color: #000 !important;
            }
            .qa-text { font-size: 14px !important; }
            .qa-time { font-size: 11px !important; opacity: 0.6 !important; }
            .qa-label { font-size: 10px !important; }

            /* Answer input */
            .answer-input {
                padding: 10px 16px !important;
                background: #0a0a0a !important;
                border-top: 1px solid #1a1a1a !important;
            }
            .answer-input textarea {
                padding: 10px 16px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
                border-radius: 5px !important;
            }
            .answer-input button {
                width: 40px !important;
                height: 40px !important;
                font-size: 16px !important;
                border-radius: 5px !important;
            }

            /* Empty state */
            .question-empty { padding: 40px 20px !important; }
            .question-empty h3 { font-size: 16px !important; }
            .question-empty p { font-size: 13px !important; }
        }

        /* Scrollbar styling */
        .questions-list::-webkit-scrollbar,
        .qa-area::-webkit-scrollbar {
            width: 6px;
        }

        .questions-list::-webkit-scrollbar-track,
        .qa-area::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .questions-list::-webkit-scrollbar-thumb,
        .qa-area::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 3px;
        }
        /* ================================================================
           Mobile-only redesigned Questions list.
           Hidden on desktop; visible on mobile. When shown, the legacy
           sidebar list in `.questions-container` is suppressed via a
           sibling @media rule below.
           ================================================================ */
        .q-mobile-page { display: none; }

        @media (max-width: 768px) {
            .q-mobile-page {
                display: block;
                padding: 14px 16px 24px;
                background: #000;
                min-height: calc(100vh - 60px);
            }
            /* Hide the legacy sidebar on mobile (we use the new cards). */
            .q-mobile-page ~ .questions-container .questions-sidebar { display: none !important; }
            .q-mobile-page ~ .questions-container { display: none !important; }

            .q-mobile-title {
                color: #fff;
                font-size: 24px;
                font-weight: 700;
                margin: 0 0 6px;
                line-height: 1.2;
            }
            .q-mobile-sub {
                color: #c7cdd1;
                font-size: 14px;
                margin: 0 0 18px;
                line-height: 1.4;
            }

            .q-mobile-card {
                position: relative;
                background: #121417;
                border: 1px solid #1f2429;
                border-radius: 14px;
                padding: 14px 16px;
                margin-bottom: 14px;
            }
            .q-mobile-card-top {
                display: flex;
                gap: 12px;
                align-items: flex-start;
                margin-bottom: 14px;
            }
            .q-mobile-avatar {
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
            .q-mobile-body { flex: 1 1 auto; min-width: 0; }
            .q-mobile-meta-row {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 6px;
                padding-right: 60px; /* room for the absolute status badge */
            }
            .q-mobile-name {
                color: #fff;
                font-weight: 600;
                font-size: 15px;
                line-height: 1.2;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .q-mobile-time {
                color: #8a9099;
                font-size: 12px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                flex-shrink: 0;
                white-space: nowrap;
            }
            .q-mobile-status {
                position: absolute;
                top: 14px;
                right: 16px;
                font-size: 13px;
                font-weight: 600;
            }
            .q-mobile-status--new      { color: #C1F11D; }
            .q-mobile-status--answered { color: #17F09C; }
            .q-mobile-status--pending  { color: #F59E0B; }

            .q-mobile-quote {
                color: #c7cdd1;
                font-size: 14px;
                line-height: 1.5;
                margin: 0;
            }

            .q-mobile-answer {
                background: #1D2222;
                border: 1px solid #242B2D;
                border-radius: 10px;
                padding: 10px 12px;
                color: #c7cdd1;
                font-size: 13px;
                line-height: 1.45;
                margin-bottom: 14px;
            }
            .q-mobile-answer-label { color: #17F09C; font-weight: 600; margin-right: 4px; }

            .q-mobile-btn-wrap {
                display: flex;
                justify-content: center;
            }
            .q-mobile-btn {
                border: none;
                border-radius: 999px;
                padding: 9px 28px;
                font-weight: 400;
                font-size: 14px;
                font-family: inherit;
                cursor: pointer;
                transition: filter 120ms;
            }
            .q-mobile-btn:hover { filter: brightness(0.95); }
            .q-mobile-btn--answer { background: #C1F11D; color: #0a0a0a; }
            .q-mobile-btn--edit   { background: #2a2d30; color: #fff; }

            .q-mobile-empty {
                text-align: center;
                padding: 60px 20px;
                color: #8a9099;
            }
            .q-mobile-empty i { font-size: 48px; margin-bottom: 14px; opacity: 0.5; }
            .q-mobile-empty h3 { color: #fff; margin: 0 0 6px; font-size: 16px; }
            .q-mobile-empty p { margin: 0; font-size: 14px; }

            /* Reply modal — overlays the mobile page when Answer/Edit is tapped. */
            .q-modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.6);
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }
            .q-modal {
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 16px;
                width: 100%;
                max-width: 500px;
                padding: 20px 20px 18px;
                max-height: 90vh;
                overflow-y: auto;
            }
            .q-modal-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .q-modal-title {
                color: #fff;
                margin: 0;
                font-size: 20px;
                font-weight: 700;
            }
            .q-modal-close {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: transparent;
                border: 1px solid #555555;
                color: #c7cdd1;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                flex-shrink: 0;
            }
            .q-modal-close:hover { background: #1a1d20; color: #fff; }
            .q-modal-divider {
                border: none;
                border-top: 1px solid #272A2B;
                margin: 14px 0 16px;
            }
            .q-modal-question {
                background: #1D2222;
                border: 1px solid #242B2D;
                border-radius: 10px;
                padding: 14px 16px;
                margin-bottom: 18px;
            }
            .q-modal-question-label {
                color: #c7cdd1;
                font-size: 14px;
                margin-bottom: 6px;
            }
            .q-modal-question-text {
                color: #fff;
                font-size: 15px;
                line-height: 1.4;
            }
            .q-modal-field-label {
                display: block;
                color: #fff;
                font-size: 15px;
                margin-bottom: 8px;
            }
            .q-modal-textarea {
                width: 100%;
                background: #1D2222;
                border: 1px solid #242B2D;
                border-radius: 10px;
                padding: 12px 14px;
                color: #fff;
                font-size: 14px;
                line-height: 1.5;
                font-family: inherit;
                resize: vertical;
                min-height: 110px;
                outline: none;
            }
            .q-modal-textarea::placeholder { color: #6a7280; }
            .q-modal-textarea:focus { border-color: #3a4249; }

            .q-modal-actions {
                display: flex;
                gap: 12px;
                margin-top: 18px;
            }
            .q-modal-btn {
                flex: 1 1 0;
                border: none;
                border-radius: 999px;
                padding: 13px 20px;
                font-size: 15px;
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                transition: filter 120ms;
            }
            .q-modal-btn:hover { filter: brightness(0.95); }
            .q-modal-btn--cancel { background: #2a2d30; color: #fff; }
            .q-modal-btn--submit { background: #C1F11D; color: #0a0a0a; }
            .q-modal-btn[disabled] { opacity: 0.6; cursor: wait; }
        }

        /* ================================================================
           Desktop redesign — card list matching the mobile v2 pattern but
           sized for wide viewports. Hidden on mobile (<=768px), where the
           existing .q-mobile-page design takes over.
           ================================================================ */
        .qd-page { display: none; }

        @media (min-width: 769px) {
            [x-cloak] { display: none !important; }
            .qd-page {
                display: block;
                background: #000;
                min-height: calc(100vh - 60px);
                color: #fff;
                padding-bottom: 60px;
            }
            /* Hide the entire legacy wrapper on desktop (communication nav,
               flash messages, legacy sidebar/detail panel) — the new .qd-page
               provides its own full-width back bar + hero + card list. */
            .qd-page ~ .ev-container { display: none !important; }

            .qd-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 32px; }
            .qd-back-wrap .ev-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .qd-back-link {
                color: #C1F11D;
                font-size: 14px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-weight: 500;
            }
            .qd-back-link:hover { color: #d9ff4a; }
            .qd-crumb { color: #fff; font-size: 14px; font-weight: 500; margin: 0; }
            .qd-crumb-spacer { width: 60px; }

            .qd-container {
                max-width: 1240px;
                margin: 0 auto;
                padding: 0 16px;
            }

            .qd-hero {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 20px;
                margin-bottom:10px;
            }
            .qd-hero h2 {
                color: #fff;
                font-size: 24px;
                font-weight: 600;
                margin: 0 0 4px;
                line-height: 1.2;
            }
            .qd-hero p {
                color: #A6B4B8;
                font-size: 14px;
                margin: 0;
            }
            .qd-counter {
                color: white;
                font-size: 14px;
                white-space: nowrap;
            }
            .qd-counter strong { color: #C1F11D; font-weight: 700; margin-right: 4px; }

            .qd-card {
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 5px;
                padding: 18px 26px;
                margin-bottom: 14px;
                display: flex;
                align-items: flex-start;
                gap: 16px;
            }
            .qd-avatar {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background: linear-gradient(135deg, #2a2d30, #16181a);
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 18px;
                flex-shrink: 0;
                overflow: hidden;
            }
            .qd-avatar img { width: 100%; height: 100%; object-fit: cover; }
            .qd-body { flex: 1 1 auto; min-width: 0; }
            .qd-meta-row {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 6px;
                flex-wrap: wrap;
            }
            .qd-name { color: #fff; font-weight: 600; font-size: 15px; }
            .qd-time {
                color: #AAAEB5;
                font-size: 12px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            .qd-time i { font-size: 11px; }

            /* Status pill — matches .q-mobile-status--* color palette for consistency. */
            .qd-badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 10px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 600;
                border: 1px solid transparent;
            }
            .qd-badge--new       { color: #C1F11D; border-color: #C1F11D; background: rgba(193,241,29,0.08); }
            .qd-badge--answered  { color: #17F09C; border-color: #17F09C; background: rgba(23,240,156,0.08); }
            .qd-badge--pending   { color: #FFB020; border-color: #FFB020; background: rgba(255,176,32,0.08); }

            .qd-quote {
                color: #AAAEB5;
                font-size: 14px;
                line-height: 1.5;
                margin: 2px 0 0;
            }

            .qd-answer-box {
                display: inline-block;
                width: fit-content;
                max-width: 100%;
                margin-top: 10px;
                background: #1D2222;
                border: 1px solid #242B2D;
                border-radius: 8px;
                padding: 10px 14px;
                color: #AAAEB5;
                font-size: 13.5px;
                line-height: 1.5;
            }
            .qd-answer-box-label { color: #17F09C; font-weight: 600; margin-right: 4px; }

            .qd-action { flex: 0 0 auto; align-self: center; }
            .qd-btn {
                padding: 8px 22px;
                border-radius: 999px;
                border: none;
                font-size: 13.5px;
                font-weight: 600;
                cursor: pointer;
                font-family: inherit;
                white-space: nowrap;
                transition: filter 120ms, transform 120ms;
            }
            .qd-btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
            .qd-btn--answer { background: #C1F11D; color: #0a0a0a; }
            .qd-btn--edit   { background: #343B3E; color: #fff; }

            .qd-empty {
                background: #131616;
                border: 1px solid #242B2D;
                border-radius: 10px;
                padding: 60px 20px;
                text-align: center;
                color: #8a9398;
            }
            .qd-empty i { font-size: 36px; opacity: 0.5; display: block; margin-bottom: 12px; }
            .qd-empty h3 { color: #fff; font-size: 18px; margin: 0 0 6px; }
            .qd-empty p { margin: 0; font-size: 14px; }

            /* Desktop reply modal — mirrors the mobile one and shares the
               same showReplyModal state so sendAnswer() works unchanged. */
            .qd-modal-overlay {
                position: fixed; inset: 0;
                background: rgba(0,0,0,0.72);
                z-index: 9998;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px 16px;
                overflow-y: auto;
            }
            .qd-modal {
                background: #14141A;
                border: 1px solid #5E6365;
                border-radius: 5px;
                max-width: 560px;
                width: 100%;
                padding: 24px 26px 22px;
            }
            .qd-modal-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 18px;
                border-bottom: 1px solid #242B2D;
                margin-bottom: 20px;
            }
            .qd-modal-title { color: #fff; font-size: 20px; font-weight: 600; margin: 0; }
            .qd-modal-close {
                background: transparent;
                border: 1px solid #3a4147;
                color: #fff;
                width: 30px; height: 30px; border-radius: 50%;
                font-size: 23px; line-height: 1; cursor: pointer;
                display: inline-flex; align-items: center; justify-content: center;
                transition: background 120ms, border-color 120ms;
            }
            .qd-modal-close:hover { background: #1a2124; border-color: #4a5157; }

            /* Field group: small label on top, control below. */
            .qd-modal-field { margin-bottom: 18px; }
            .qd-modal-label {
                color: #AAAEB5;
                font-size: 13.5px;
                font-weight: 500;
                margin-bottom: 8px;
            }

            .qd-modal-q {
                background: #1A1A21;
                border: 1px solid #242B2D;
                border-radius: 10px;
                padding: 14px 16px;
                color: #fff;
                font-size: 14px;
                line-height: 1.5;
            }
            .qd-modal-textarea {
                width: 100%;
                background: #1A1A21;
                border: 1px solid #242B2D;
                border-radius: 10px;
                padding: 14px 16px;
                color: #fff;
                font-family: inherit;
                font-size: 14px;
                resize: vertical;
                min-height: 120px;
                outline: none;
                transition: border-color 120ms;
            }
            .qd-modal-textarea::placeholder { color: #6c7278; }
            .qd-modal-textarea:focus { border-color: #C1F11D; }

            /* Buttons right-aligned, hug content. */
            .qd-modal-actions {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                margin-top: 20px;
            }
            .qd-modal-btn {
                border: none;
                border-radius: 999px;
                padding: 7px 26px;
                font-size: 14px;
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                transition: filter 120ms, transform 120ms;
            }
            .qd-modal-btn:hover:not([disabled]) { filter: brightness(1.08); transform: translateY(-1px); }
            .qd-modal-btn--cancel { background: #2a2d30; color: #fff; }
            .qd-modal-btn--submit { background: #C1F11D; color: #0a0a0a; }
            .qd-modal-btn[disabled] { opacity: 0.6; cursor: wait; }
        }
    </style>

    {{-- Desktop redesign — sits OUTSIDE the ev-container wrapper so the
         back bar can stretch full width and so the sibling-selector rule
         (`.qd-page ~ .ev-container { display:none }`) can hide the legacy
         communication-nav + flash messages + sidebar/detail panel on desktop.
         Mobile users still get the legacy wrapper (which contains the
         .q-mobile-page design). --}}
    <div class="qd-page" x-data="{ qModal: @entangle('showReplyModal') }">
        <div class="qd-back-wrap">
            <div class="ev-container">
                <a href="{{ url('/') }}" wire:navigate class="qd-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
                <h1 class="qd-crumb">Questions</h1>
                <div class="qd-crumb-spacer"></div>
            </div>
        </div>

        <div class="qd-container">
            <div class="qd-hero">
                <div>
                    <h2>Questions &amp; Inquiries</h2>
                    <p>Review and reply to inquiries received from VIP members and profiles.</p>
                </div>
                @if($unansweredCount > 0)
                    <div class="qd-counter"><strong>{{ $unansweredCount }}</strong> New Inquiries</div>
                @endif
            </div>

            @forelse($questions as $question)
                @php
                    $qdAsker = $question->askedBy ? ($question->askedBy->name ?? $question->askedBy->email) : 'Guest';
                    $qdInitial = strtoupper(substr($qdAsker, 0, 1));
                    $qdTime = $question->created_at->diffForHumans(null, true) . ' ago';
                    if ($question->answer) {
                        $qdStatus = 'answered'; $qdLabel = 'Answered';
                    } elseif ($question->created_at->lt(now()->subHours(24))) {
                        $qdStatus = 'pending';  $qdLabel = 'Pending';
                    } else {
                        $qdStatus = 'new';      $qdLabel = 'New';
                    }
                @endphp
                <div class="qd-card" wire:key="qd-card-{{ $question->id }}">
                    <div class="qd-avatar">{{ $qdInitial }}</div>
                    <div class="qd-body">
                        <div class="qd-meta-row">
                            <span class="qd-name">{{ $qdAsker }}</span>
                            <span class="qd-badge qd-badge--{{ $qdStatus }}">{{ $qdLabel }}</span>
                            <span class="qd-time"><i class="far fa-clock"></i> {{ $qdTime }}</span>
                        </div>
                        <div class="qd-quote">&ldquo;{{ $question->question }}&rdquo;</div>
                        @if($question->answer)
                            <div class="qd-answer-box">
                                <span class="qd-answer-box-label">Your Answer:</span>{{ Str::limit($question->answer, 200) }}
                            </div>
                        @endif
                    </div>
                    <div class="qd-action">
                        @if($question->answer)
                            <button type="button" class="qd-btn qd-btn--edit"
                                    wire:click="selectQuestion({{ $question->id }})">Edit Answer</button>
                        @else
                            <button type="button" class="qd-btn qd-btn--answer"
                                    wire:click="selectQuestion({{ $question->id }})">Answer Inquiry</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="qd-empty">
                    <i class="fa fa-question-circle"></i>
                    <h3>No questions yet</h3>
                    <p>Inquiries from VIP members will appear here.</p>
                </div>
            @endforelse

            @if($questions->hasPages())
                <div class="mt-3">{{ $questions->links() }}</div>
            @endif
        </div>

        {{-- Desktop reply modal (same state as mobile). --}}
        <div class="qd-modal-overlay" x-show="qModal" x-cloak @click.self="qModal = false; $wire.closeModal()" style="display:none">
            <div class="qd-modal">
                <div class="qd-modal-head">
                    <h3 class="qd-modal-title">
                        @if($selectedQuestion)
                            Reply to {{ $selectedQuestion->askedBy ? ($selectedQuestion->askedBy->name ?? $selectedQuestion->askedBy->email) : 'Guest' }}
                        @else
                            Reply
                        @endif
                    </h3>
                    <button type="button" class="qd-modal-close" @click="qModal = false; $wire.closeModal()" aria-label="Close">&times;</button>
                </div>
                @if($selectedQuestion)
                    <div class="qd-modal-field">
                        <div class="qd-modal-label">Received Question:</div>
                        <div class="qd-modal-q">{{ $selectedQuestion->question }}</div>
                    </div>
                @endif
                <div class="qd-modal-field">
                    <div class="qd-modal-label">Your Professional Response</div>
                    <textarea class="qd-modal-textarea" wire:model="answer" rows="5"
                              placeholder="Type your polite and comprehensive response here..."></textarea>
                    @error('answer')
                        <div style="color:#ff6b6b; font-size:12px; margin-top:6px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="qd-modal-actions">
                    <button type="button" class="qd-modal-btn qd-modal-btn--cancel" @click="qModal = false; $wire.closeModal()">Cancel</button>
                    <button type="button" class="qd-modal-btn qd-modal-btn--submit" wire:click="sendAnswer" wire:loading.attr="disabled">Submit Response</button>
                </div>
            </div>
        </div>
    </div>

    <div class="ev-container {{ $selectedQuestion ? 'hide-nav-mobile' : '' }}" style="padding-top: 8px; padding-bottom: 40px;">
                @include('components.communication-nav')

                <div class="mb-3 clearfix" style="clear: both;" id="my-questions">
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    {{-- Statistics Cards --}}
                    {{-- <div class="stats-row">
                        <div class="stat-card">
                            <h3>{{ $totalQuestions }}</h3>
                            <p>Total Questions</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="text-warning">{{ $unansweredCount }}</h3>
                            <p>Unanswered</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="text-success">{{ $answeredCount }}</h3>
                            <p>Answered</p>
                        </div>
                    </div> --}}

                    {{-- Mobile-only redesigned list. Hidden on desktop via CSS.
                         When a question is selected, the existing full-screen
                         detail panel (below) takes over — same selectQuestion()
                         wire call drives both. --}}
                    <div class="q-mobile-page" x-data="{ qModal: @entangle('showReplyModal') }">
                            <h1 class="q-mobile-title">Questions &amp; Inquiries</h1>
                            <p class="q-mobile-sub">Review and reply to inquiries received from VIP members and profiles.</p>

                            @forelse($questions as $question)
                                @php
                                    $qAsker = $question->askedBy ? ($question->askedBy->name ?? $question->askedBy->email) : 'Guest';
                                    $qInitial = strtoupper(substr($qAsker, 0, 1));
                                    $qTime = $question->created_at->diffForHumans(null, true) . ' ago';
                                    if ($question->answer) {
                                        $qStatus = 'answered';
                                        $qLabel = 'Answered';
                                    } elseif ($question->created_at->lt(now()->subHours(24))) {
                                        $qStatus = 'pending';
                                        $qLabel = 'Pending';
                                    } else {
                                        $qStatus = 'new';
                                        $qLabel = 'New';
                                    }
                                @endphp
                                <div class="q-mobile-card">
                                    <span class="q-mobile-status q-mobile-status--{{ $qStatus }}">{{ $qLabel }}</span>
                                    <div class="q-mobile-card-top">
                                        <div class="q-mobile-avatar">{{ $qInitial }}</div>
                                        <div class="q-mobile-body">
                                            <div class="q-mobile-meta-row">
                                                <span class="q-mobile-name">{{ $qAsker }}</span>
                                                <span class="q-mobile-time"><i class="far fa-clock"></i> {{ $qTime }}</span>
                                            </div>
                                            <div class="q-mobile-quote">&ldquo;{{ $question->question }}&rdquo;</div>
                                        </div>
                                    </div>
                                    @if($question->answer)
                                        <div class="q-mobile-answer">
                                            <span class="q-mobile-answer-label">Your Answer:</span>
                                            {{ Str::limit($question->answer, 140) }}
                                        </div>
                                    @endif
                                    <div class="q-mobile-btn-wrap">
                                        @if($question->answer)
                                            <button type="button" class="q-mobile-btn q-mobile-btn--edit"
                                                    wire:click="selectQuestion({{ $question->id }})">
                                                Edit Answer
                                            </button>
                                        @else
                                            <button type="button" class="q-mobile-btn q-mobile-btn--answer"
                                                    wire:click="selectQuestion({{ $question->id }})">
                                                Answer Inquiry
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="q-mobile-empty">
                                    <i class="fa fa-question-circle"></i>
                                    <h3>No questions yet</h3>
                                    <p>Inquiries from VIP members will appear here.</p>
                                </div>
                            @endforelse

                        {{-- Reply modal — opened from Answer/Edit button via
                             qModal Alpine flag. Textarea bound to the component's
                             $answer property so sendAnswer() picks it up. --}}
                        <div class="q-modal-overlay" x-show="qModal" x-cloak @click.self="qModal = false; $wire.closeModal()" style="display:none">
                            <div class="q-modal">
                                <div class="q-modal-head">
                                    <h3 class="q-modal-title">
                                        @if($selectedQuestion)
                                            Reply to {{ $selectedQuestion->askedBy ? ($selectedQuestion->askedBy->name ?? $selectedQuestion->askedBy->email) : 'Guest' }}
                                        @else
                                            Reply
                                        @endif
                                    </h3>
                                    <button type="button" class="q-modal-close" @click="qModal = false; $wire.closeModal()" aria-label="Close">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>
                                <hr class="q-modal-divider">
                                <div class="q-modal-body">
                                    <div class="q-modal-question">
                                        <div class="q-modal-question-label">Received Question:</div>
                                        <div class="q-modal-question-text">
                                            {{ $selectedQuestion ? $selectedQuestion->question : '' }}
                                        </div>
                                    </div>
                                    <label class="q-modal-field-label">Your Professional Response</label>
                                    <textarea
                                        class="q-modal-textarea"
                                        wire:model="answer"
                                        rows="5"
                                        placeholder="Type your polite and comprehensive response here..."
                                    ></textarea>
                                </div>
                                <div class="q-modal-actions">
                                    <button type="button" class="q-modal-btn q-modal-btn--cancel" @click="qModal = false; $wire.closeModal()">
                                        Cancel
                                    </button>
                                    <button type="button" class="q-modal-btn q-modal-btn--submit"
                                            wire:click="sendAnswer"
                                            wire:loading.attr="disabled">
                                        Submit Response
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Legacy desktop view below — hidden on desktop by the
                         .qd-page ~ .ev-container CSS rule. Still rendered on
                         mobile so the existing .q-mobile-page design works. --}}

                    {{-- WhatsApp-style Questions Container --}}
                    <div class="questions-container">
                        {{-- Left Sidebar - Questions List --}}
                        <div class="questions-sidebar {{ $selectedQuestion ? 'hidden' : '' }}" id="questionsSidebar">
                            <div class="questions-sidebar-header">
                                <h4><i class="fa fa-question-circle"></i> Questions</h4>
                                <div class="questions-search">
                                    <input 
                                        type="text" 
                                        placeholder="Search questions..." 
                                        wire:model.live.debounce.300ms="searchTerm">
                                    <select class="questions-filter" wire:model.live="filterStatus">
                                        <option value="all">All</option>
                                        <option value="unanswered">Unanswered</option>
                                        <option value="answered">Answered</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="questions-list">
                                @forelse($questions as $question)
                                    <div 
                                        class="question-item {{ $selectedQuestion && $selectedQuestion->id === $question->id ? 'active' : '' }} {{ !$question->answer ? 'unanswered' : '' }}"
                                        wire:click="selectQuestion({{ $question->id }})"
                                    >
                                        <div class="question-avatar">
                                            {{ strtoupper(substr($question->askedBy->name ?? $question->askedBy->email ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="question-info">
                                            <div class="question-header">
                                                <span class="question-from">
                                                    {{ $question->askedBy ? ($question->askedBy->name ?? $question->askedBy->email) : 'Guest' }}
                                                    @if(!$question->answer)
                                                        <span class="unanswered-badge">NEW</span>
                                                    @endif
                                                </span>
                                                <span class="question-time">{{ $question->created_at->diffForHumans(null, true) }}</span>
                                            </div>
                                            <div class="question-preview">{{ Str::limit($question->question, 50) }}</div>
                                            @if($question->profile)
                                                <div class="question-profile">To: {{ $question->profile->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="question-empty" style="padding: 40px 20px;">
                                        <i class="fa fa-question-circle"></i>
                                        <h3>No questions</h3>
                                        <p>You haven't received any questions yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Right Panel - Question Detail & Answer --}}
                        <div class="question-main {{ $selectedQuestion ? 'fullscreen' : '' }}">
                            @if($selectedQuestion)
                                {{-- Question Header --}}
                                <div class="question-detail-header">
                                    <div class="question-detail-info">
                                        <button class="mobile-back-btn" wire:click="closeModal">
                                            <i class="fa fa-angle-left"></i> Back
                                        </button>
                                        <div class="question-detail-avatar">
                                            {{ strtoupper(substr($selectedQuestion->askedBy->name ?? $selectedQuestion->askedBy->email ?? 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="question-detail-name">{{ $selectedQuestion->askedBy ? ($selectedQuestion->askedBy->name ?? $selectedQuestion->askedBy->email) : 'Guest' }}</div>
                                            @if($selectedQuestion->profile)
                                                <div class="question-detail-profile">To: {{ $selectedQuestion->profile->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="question-detail-actions">
                                        <button wire:click="deleteQuestion({{ $selectedQuestion->id }})" class="delete" onclick="return confirm('Delete this question?')" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button wire:click="closeModal" title="Close">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Q&A Area --}}
                                <div class="qa-area">
                                    {{-- Question Bubble --}}
                                    <div class="qa-bubble qa-question">
                                        <div class="qa-label"><i class="fa fa-question-circle"></i> Question</div>
                                        <div class="qa-text">{{ $selectedQuestion->question }}</div>
                                        <div class="qa-time">{{ $selectedQuestion->created_at->format('M d, Y h:i A') }}</div>
                                    </div>

                                    {{-- Answer Bubble (if exists) --}}
                                    @if($selectedQuestion->answer)
                                        <div class="qa-bubble qa-answer">
                                            <div class="qa-label"><i class="fa fa-check-circle"></i> Your Answer</div>
                                            <div class="qa-text">{{ $selectedQuestion->answer }}</div>
                                            <div class="qa-time">{{ $selectedQuestion->updated_at->format('M d, Y h:i A') }}</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Answer Input --}}
                                <form wire:submit.prevent="sendAnswer" class="answer-input">
                                    <textarea 
                                        wire:model="answer" 
                                        placeholder="{{ $selectedQuestion->answer ? 'Update your answer...' : 'Type your answer...' }}"
                                        rows="1"
                                    ></textarea>
                                    <button type="submit" title="Send Answer">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Empty state when no question selected --}}
                                <div class="question-empty">
                                    <i class="fa fa-question-circle"></i>
                                    <h3>Select a question</h3>
                                    <p>Choose a question from the left to view and answer</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($questions->hasPages())
                    <div class="mt-3">
                        {{ $questions->links() }}
                    </div>
                    @endif
                </div>
            </div>

    <script>
        function scrollToAnswer() {
            setTimeout(() => {
                const qaArea = document.querySelector('.qa-area');
                if (qaArea) {
                    qaArea.scrollTop = qaArea.scrollHeight;
                }
            }, 100);
        }
    </script>
</div>
