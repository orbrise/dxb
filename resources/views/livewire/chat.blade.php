<div wire:poll.2s="pollMessages">
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
        .chat-container {
            display: flex;
            height: calc(100vh - 200px);
            min-height: 500px;
            background: transparent;
            border-radius: 8px;
            overflow: hidden;
            gap: 8px;
        }

        /* Left Panel - Conversation List */
        .chat-sidebar {
            width: 350px;
            min-width: 300px;
           
            
            border-radius: 8px;
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar-header {
            padding: 15px;
            background: transparent;
         
        }

        .chat-sidebar-header h4 {
            color: #C1F11D;
            margin: 0 0 10px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Real-time connection indicator */
        .connection-indicator {
            font-size: 10px;
            transition: all 0.3s ease;
        }
        .connection-indicator.online {
            color: #4CAF50;
        }
        .connection-indicator.offline {
            color: #888;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .chat-search {
            position: relative;
        }

        .chat-search input {
            width: 100%;
            padding: 10px 15px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
        }

        .chat-search input:focus {
            outline: none;
            border-color: #C1F11D;
        }

        .chat-search input::placeholder {
            color: #888;
        }

        /* Search Results Dropdown */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 100;
            margin-top: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #333;
            transition: background 0.2s;
        }

        .search-result-item:hover {
            background: #333;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        /* Conversation List */
        .conversation-list {
            flex: 1;
            overflow-y: auto;
            background: #0D1011;
            padding: 10px;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #333;
            transition: background 0.2s;
        }

        .conversation-item:hover {
            background: #333;
        }

        .conversation-item.active {
            background: #3d3d3d;
            border-left: 3px solid #C1F11D;
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #C1F11D;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin-right: 12px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .conversation-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-name {
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-preview {
            color: #888;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            text-align: right;
            flex-shrink: 0;
            margin-left: 10px;
        }

        .conversation-time {
            color: #888;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .conversation-item.unread .conversation-time {
            color: #C1F11D;
        }

        .unread-badge {
            background: #C1F11D;
            color: #1a1a1a;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 10px;
            display: inline-block;
        }

        /* Right Panel - Chat Area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #0D1011;
            border-radius: 8px;
            overflow: hidden;
        }

        .chat-header {
            padding: 15px 20px;
            background: #333;
            border-bottom: 1px solid #444;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
        }

        .chat-header-avatar {
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
            overflow: hidden;
        }

        .chat-header-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-header-name {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
        }

        .chat-header-email {
            color: #888;
            font-size: 13px;
        }

        .chat-header-actions button {
            background: none;
            border: none;
            color: #888;
            font-size: 18px;
            padding: 5px 10px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .chat-header-actions button:hover {
            color: #C1F11D;
        }

        /* Chat Messages */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message-wrapper {
            display: flex;
            max-width: 100%;
        }

        .message-wrapper.sent {
            justify-content: flex-end;
        }

        .message-wrapper.received {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 18px;
            position: relative;
        }

        .message-wrapper.sent .message-bubble {
            background: #C1F11D;
            color: #000;
            border-bottom-right-radius: 4px;
        }

        .message-wrapper.received .message-bubble {
            background: #2a2a2a;
            color: #fff;
            border-bottom-left-radius: 4px;
            border: 1px solid #333;
        }

        .message-text {
            margin: 0;
            word-wrap: break-word;
            line-height: 1.4;
        }

        .message-time {
            font-size: 10px;
            margin-top: 5px;
            opacity: 0.7;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .message-wrapper.sent .message-time {
            justify-content: flex-end;
            color: #000;
        }

        .message-wrapper.received .message-time {
            color: #888;
        }

        /* WhatsApp-style tick marks */
        .message-ticks {
            display: inline-flex;
            margin-left: 3px;
            font-size: 12px;
        }

        .message-ticks .tick {
            color: #000;
        }

        .message-ticks .tick.read {
            color: #000; /* Black tick */
        }

        .message-ticks .double-tick {
            letter-spacing: -4px;
        }

        /* Chat Input */
        .chat-input {
            padding: 15px 20px;
            background: #2a2a2a;
            border-top: 1px solid #333;
        }

        .chat-input-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-input-form input {
            flex: 1;
            padding: 12px 20px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 25px;
            color: #fff;
            font-size: 14px;
        }

        .chat-input-form input:focus {
            outline: none;
            border-color: #C1F11D;
        }

        .chat-input-form input::placeholder {
            color: #888;
        }

        .chat-input-form button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: #C1F11D;
            color: #000;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-input-form button:hover {
            background: #b0dd1a;
            transform: scale(1.05);
        }

        .chat-input-form button:disabled {
            background: #444;
            color: #888;
            cursor: not-allowed;
            transform: none;
        }

        /* Empty State */
        .chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
            text-align: center;
            padding: 40px;
        }

        .chat-empty .chat-empty-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            color: #555;
        }

        .chat-empty h3 {
            color: #888;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .chat-empty p {
            color: #666;
            max-width: 300px;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Mobile Responsive */
        .mobile-back-btn {
            display: none;
            background: none;
            border: none;
            color: #C1F11D;
            font-size: 20px;
            padding: 10px;
            cursor: pointer;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            /* Hide site header spacing */
            #header { margin-bottom: 0 !important; }

            /* Back bar */
            .ev-back-bar { position: sticky; top: 0; z-index: 100; }
            .ev-back-bar .ev-desktop-back { display: none !important; }
            .ev-back-bar .ev-mobile-back { display: inline !important; }
            .ev-back-bar h1 { font-size: 15px !important; }

            /* When chat is open, hide the back bar AND site header */
            .ev-back-bar.ev-chat-open { display: none !important; }
            .ev-back-bar.ev-chat-open ~ .ev-container { padding-top: 0 !important; }
            body:has(.ev-chat-open) .ev-header { display: none !important; }
            body:has(.ev-chat-open) #header { display: none !important; }

            /* Full height chat */
            .ev-container { padding: 0 16px !important; padding-top: 0 !important; padding-bottom: 0 !important; }
            .chat-container {
                height: calc(100vh - 60px) !important;
                min-height: unset !important;
                border-radius: 0 !important;
                gap: 0 !important;
                position: relative !important;
            }
            /* When back bar is hidden (chat open), use full height */
            .ev-chat-open ~ .ev-container .chat-container,
            .ev-back-bar[style*="display: none"] ~ .ev-container .chat-container {
                height: 100vh !important;
            }

            /* Sidebar takes full screen */
            .chat-sidebar {
                position: absolute !important;
                width: 100% !important;
                height: 100% !important;
                z-index: 10;
                left: 0;
                top: 0;
                min-width: unset !important;
                border-radius: 0 !important;
                background: #000 !important;
            }
            .chat-sidebar.mobile-hidden { display: none !important; }

            /* Sidebar header - hidden on mobile, we use the back bar */
            .chat-sidebar-header {
                padding: 12px 16px !important;
                background: #000 !important;
            }
            .chat-sidebar-header h4 { display: none !important; }

            /* Search bar */
            .chat-search {
                display: flex !important;
                gap: 8px !important;
                align-items: center !important;
            }
            .chat-search input {
                flex: 1 !important;
                padding: 11px 16px 11px 36px !important;
                border-radius: 5px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
                color: #fff !important;
            }
            .chat-search::before {
                content: '';
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                width: 14px;
                height: 14px;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                z-index: 1;
                pointer-events: none;
            }
            .chat-search select {
                padding: 11px 14px !important;
                border-radius: 5px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
                color: #fff !important;
                min-width: 70px;
            }

            /* Conversation list */
            .conversation-list {
                background: #000 !important;
                padding: 0 !important;
            }
            .conversation-item {
                padding: 14px 16px !important;
                border-bottom: 1px solid #1a1a1a !important;
            }
            .conversation-item:hover { background: #111 !important; }
            .conversation-item.active {
                background: #111 !important;
                border-left: none !important;
            }
            .conversation-avatar {
                width: 50px !important;
                height: 50px !important;
                margin-right: 14px !important;
            }
            .conversation-name {
                font-size: 15px !important;
                font-weight: 600 !important;
                color: #fff !important;
                margin-bottom: 4px !important;
            }
            .conversation-preview {
                font-size: 13px !important;
                color: #888 !important;
                line-height: 1.3 !important;
                -webkit-line-clamp: 2;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
                white-space: normal !important;
            }
            .conversation-meta { margin-left: 8px !important; }
            .conversation-time { font-size: 10px !important; display: none !important; }
            .unread-badge {
                font-size: 11px !important;
                padding: 2px 8px !important;
                background: #C1F11D !important;
                color: #000 !important;
                border-radius: 12px !important;
            }

            /* Chat main panel */
            .chat-main {
                width: 100% !important;
                border-radius: 0 !important;
                background: #000 !important;
            }

            /* Chat header - acts as top nav bar replacing site header */
            .chat-header {
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
            /* Add top padding to messages so they don't hide behind fixed header */
            .chat-messages { padding-top: 70px !important; }
            .chat-header-info {
                display: flex !important;
                align-items: center !important;
                width: 100% !important;
                position: relative !important;
            }
            .mobile-back-btn {
                display: block !important;
                color: #C1F11D !important;
                font-size: 14px !important;
                padding: 0 !important;
                margin-right: 0 !important;
                position: absolute !important;
                left: 0 !important;
            }
            .chat-header-avatar { display: none !important; }
            .chat-header-info > div {
                text-align: center !important;
                width: 100% !important;
            }
            .chat-header-name {
                font-size: 15px !important;
                font-weight: 600 !important;
                color: #fff !important;
            }
            .chat-header-email {
                font-size: 0 !important;
            }
            .chat-header-email::after {
                content: 'Active now';
                font-size: 12px;
                color: #C1F11D;
            }
            .chat-header-actions { display: none !important; }

            /* Messages area */
            .chat-messages {
                background: #000 !important;
                padding: 16px !important;
                gap: 4px !important;
            }

            /* Message bubbles */
            .message-bubble {
                max-width: 80% !important;
                border-radius: 14px !important;
                padding: 10px 14px !important;
            }
            .message-wrapper.sent .message-bubble {
                background: #C1F11D !important;
                color: #000 !important;
                border-bottom-right-radius: 4px !important;
            }
            .message-wrapper.received .message-bubble {
                background: #1a1a1a !important;
                color: #fff !important;
                border: none !important;
                border-bottom-left-radius: 4px !important;
            }
            .message-text { font-size: 14px !important; }

            /* Message time - outside bubble */
            .message-time {
                font-size: 11px !important;
                margin-top: 4px !important;
                opacity: 0.6 !important;
            }
            .message-wrapper.sent .message-time { color: #888 !important; }
            .message-wrapper.received .message-time { color: #666 !important; }

            /* Chat input */
            .chat-input {
                padding: 10px 16px !important;
                background: #0a0a0a !important;
                border-top: 1px solid #1a1a1a !important;
            }
            .chat-input-form input {
                padding: 10px 16px !important;
                font-size: 14px !important;
                background: #1a1a1a !important;
                border: 1px solid #333 !important;
                border-radius: 5px !important;
            }
            .chat-input-form button {
                width: 40px !important;
                height: 40px !important;
                font-size: 16px !important;
                border-radius: 5px !important;
            }

            /* Communication nav - hide on mobile */
            .ev-comm-nav, .communication-nav { display: none !important; }

            /* Remove extra spacing */
            #my-chat { margin: 0 !important; }
            .mb-3 { margin-bottom: 0 !important; }

            /* Empty state */
            .chat-empty { padding: 40px 20px !important; }
            .chat-empty h3 { font-size: 16px !important; }
            .chat-empty p { font-size: 13px !important; }
        }

        /* Scrollbar styling */
        .conversation-list::-webkit-scrollbar,
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .conversation-list::-webkit-scrollbar-track,
        .chat-messages::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .conversation-list::-webkit-scrollbar-thumb,
        .chat-messages::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 3px;
        }

        .conversation-list::-webkit-scrollbar-thumb:hover,
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    <!-- Back Bar -->
    <div class="ev-back-bar {{ $selectedConversationId ? 'ev-chat-open' : '' }}" id="chatBackBar">
        <div class="ev-container" style="display:flex; align-items:center; justify-content:center; position:relative;">
            <a href="/female-escorts-in-dubai" style="position:absolute; left:16px;">
                <span class="ev-desktop-back"><i class="fa fa-angle-left"></i> Escorts in Dubai</span>
                <span class="ev-mobile-back" style="display:none;"><i class="fa fa-angle-left"></i> Back</span>
            </a>
            <h1><span class="ev-desktop-back">My Messages</span><span class="ev-mobile-back" style="display:none;">Conversations</span></h1>
        </div>
    </div>

    <div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">
                @include('components.communication-nav')
                
                <div class="mb-3 clearfix" style="clear: both;" id="my-chat">
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div style="background:rgba(193,241,29,0.1);border:1px solid rgba(193,241,29,0.3);border-radius:8px;padding:14px 20px;margin-bottom:16px;color:#C1F11D;font-size:14px;display:flex;align-items:center;justify-content:space-between;">
                            <span><i class="fa fa-check-circle"></i> {{ session('success') }}</span>
                            <button onclick="this.parentElement.style.display='none'" style="background:none;border:none;color:#C1F11D;font-size:18px;cursor:pointer;">&times;</button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div style="background:rgba(220,53,69,0.1);border:1px solid rgba(220,53,69,0.3);border-radius:8px;padding:14px 20px;margin-bottom:16px;color:#ff6b6b;font-size:14px;display:flex;align-items:center;justify-content:space-between;">
                            <span><i class="fa fa-exclamation-circle"></i> {{ session('error') }}</span>
                            <button onclick="this.parentElement.style.display='none'" style="background:none;border:none;color:#ff6b6b;font-size:18px;cursor:pointer;">&times;</button>
                        </div>
                    @endif

                    {{-- WhatsApp-style Chat Container --}}
                    <div class="chat-container" wire:poll.5s>
                        {{-- Left Sidebar - Conversations --}}
                        <div class="chat-sidebar {{ $selectedConversationId ? 'mobile-hidden' : '' }}" id="chatSidebar">
                            <div class="chat-sidebar-header">
                                <h4>
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    Conversations
                                    <span id="connection-status" class="connection-indicator offline" title="Connecting...">
                                        <i class="fa fa-circle"></i>
                                    </span>
                                </h4>
                                <div class="chat-search" style="display: flex; gap: 8px; align-items: center;">
                                    <input 
                                        type="text" 
                                        placeholder="Search conversations..." 
                                        wire:model.live.debounce.300ms="searchTerm"
                                        style="flex: 1;">
                                    <select style="padding: 10px 12px; background: #1a1a1a; border: 1px solid #444; border-radius: 5px; color: #fff; font-size: 14px; cursor: pointer;">
                                        <option>All</option>
                                    </select>
                                    @if($searchResults && $searchResults->count() > 0)
                                        <div class="search-results">
                                            @foreach($searchResults as $user)
                                                <div class="search-result-item" wire:click="startConversation({{ $user->id }})">
                                                    <div class="conversation-avatar">
                                                        @if($user->profile_pic)
                                                            <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="">
                                                        @else
                                                            {{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}
                                                        @endif
                                                    </div>
                                                    <div class="conversation-info">
                                                        <div class="conversation-name">{{ $user->name ?? $user->email }}</div>
                                                        <div class="conversation-preview">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="conversation-list">
                                @forelse($conversations as $conversation)
                                    <div 
                                        class="conversation-item {{ $selectedConversationId == $conversation['id'] ? 'active' : '' }} {{ $conversation['unread_count'] > 0 ? 'unread' : '' }}"
                                        wire:click="selectConversation({{ $conversation['id'] }})"
                                    >
                                        <div class="conversation-avatar">
                                            @if(!empty($conversation['other_user_avatar']))
                                                <img src="{{ asset('storage/' . $conversation['other_user_avatar']) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($conversation['other_user_name'] ?? '?', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">{{ $conversation['other_user_name'] ?? 'Unknown' }}</div>
                                            <div class="conversation-preview">
                                                {{ Str::limit($conversation['last_message'], 30) ?: 'No messages yet' }}
                                            </div>
                                        </div>
                                        <div class="conversation-meta">
                                            @if($conversation['last_message_at'])
                                                <div class="conversation-time">
                                                    {{ \Carbon\Carbon::parse($conversation['last_message_at'])->diffForHumans(null, true) }}
                                                </div>
                                            @endif
                                            @if($conversation['unread_count'] > 0)
                                                <span class="unread-badge">{{ $conversation['unread_count'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="chat-empty" style="padding: 40px 20px;">
                                        <svg class="chat-empty-icon" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M40 10 L40 35" stroke="#555" stroke-width="3" stroke-linecap="round"/>
                                            <path d="M32 28 L40 35 L48 28" stroke="#555" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            <rect x="20" y="45" rx="12" ry="12" width="40" height="24" stroke="#555" stroke-width="3" fill="none"/>
                                            <circle cx="33" cy="57" r="2.5" fill="#555"/>
                                            <circle cx="40" cy="57" r="2.5" fill="#555"/>
                                            <circle cx="47" cy="57" r="2.5" fill="#555"/>
                                        </svg>
                                        <h3>No conversations</h3>
                                        <p>You haven't received any messages yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Right Panel - Chat Messages --}}
                        <div class="chat-main">
                            @if($selectedConversationId && $selectedUser)
                                {{-- Chat Header --}}
                                <div class="chat-header">
                                    <div class="chat-header-info">
                                        <button class="mobile-back-btn" wire:click="closeConversation">
                                            <i class="fa fa-angle-left"></i> Back
                                        </button>
                                        <div class="chat-header-avatar">
                                            @if($selectedUser->profile_pic)
                                                <img src="{{ asset('storage/' . $selectedUser->profile_pic) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($selectedUser->name ?? $selectedUser->email, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="chat-header-name">{{ $selectedUser->name ?? $selectedUser->email }}</div>
                                            <div class="chat-header-email">{{ $selectedUser->email }}</div>
                                        </div>
                                    </div>
                                    <div class="chat-header-actions">
                                        <button wire:click="deleteConversation" onclick="return confirm('Delete this conversation?')" title="Delete conversation">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
 
                                {{-- Chat Messages --}}
                                <div class="chat-messages" id="chatMessages">
                                    @foreach($this->conversationMessages as $message)
                                        <div class="message-wrapper {{ $message['is_mine'] ? 'sent' : 'received' }}" wire:key="msg-{{ $message['id'] }}-{{ $message['status'] }}">
                                            <div class="message-bubble">
                                                <p class="message-text">{{ $message['message'] }}</p>
                                                <div class="message-time">
                                                    <span>{{ \Carbon\Carbon::parse($message['created_at'])->format('h:i A') }}</span>
                                                    @if($message['is_mine'])
                                                        <span class="message-ticks">
                                                            @if($message['status'] === 'read')
                                                                {{-- Double black ticks - Read --}}
                                                                <span class="tick read double-tick">✓✓</span>
                                                            @elseif($message['status'] === 'delivered')
                                                                {{-- Double grey ticks - Delivered --}}
                                                                <span class="tick double-tick">✓✓</span>
                                                            @else
                                                                {{-- Single tick - Sent --}}
                                                                <span class="tick">✓</span>
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Chat Input --}}
                                <div class="chat-input">
                                    <form wire:submit.prevent="sendReply" class="chat-input-form">
                                        <input 
                                            type="text" 
                                            wire:model="reply" 
                                            placeholder="Type a message..."
                                            autocomplete="off">
                                        <button type="submit" {{ empty($reply) ? 'disabled' : '' }}>
                                            <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                {{-- Empty State --}}
                                <div class="chat-empty">
                                    <img src="{{ smart_asset('assets/newtheme/chaticon.svg') }}" alt="No conversations" style="width:120px; margin-bottom:20px; opacity:0.7;">
                                    <h3>Select a conversation</h3>
                                    <p>Choose a conversation from the left to start chatting</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initEchoListener();
            scrollToBottom();
        });
        
        document.addEventListener('livewire:navigated', () => {
            initEchoListener();
        });

        function initEchoListener() {
            const userId = {{ auth()->id() }};
            const statusIndicator = document.getElementById('connection-status');
            
            console.log('Initializing Echo listener for user:', userId);
            
            // Wait for Echo to be available
            const checkEcho = setInterval(() => {
                if (window.Echo) {
                    clearInterval(checkEcho);
                    console.log('Echo is available, setting up listener...');
                    console.log('Echo config:', {
                        host: window.Echo.options?.wsHost,
                        port: window.Echo.options?.wsPort,
                        key: window.Echo.options?.key
                    });
                    
                    try {
                        // Check WebSocket connection state
                        if (window.Echo.connector && window.Echo.connector.pusher) {
                            const pusher = window.Echo.connector.pusher;
                            
                            pusher.connection.bind('connected', () => {
                                console.log('✅ WebSocket connected!');
                                if (statusIndicator) {
                                    statusIndicator.classList.remove('offline');
                                    statusIndicator.classList.add('online');
                                    statusIndicator.title = 'Connected';
                                }
                            });
                            
                            pusher.connection.bind('disconnected', () => {
                                console.log('❌ WebSocket disconnected');
                                if (statusIndicator) {
                                    statusIndicator.classList.remove('online');
                                    statusIndicator.classList.add('offline');
                                    statusIndicator.title = 'Disconnected';
                                }
                            });
                            
                            pusher.connection.bind('error', (err) => {
                                console.error('❌ WebSocket error:', err);
                            });
                            
                            // Check current state
                            console.log('Current connection state:', pusher.connection.state);
                            if (pusher.connection.state === 'connected') {
                                if (statusIndicator) {
                                    statusIndicator.classList.remove('offline');
                                    statusIndicator.classList.add('online');
                                    statusIndicator.title = 'Connected';
                                }
                            }
                        }
                        
                        window.Echo.private(`chat.${userId}`)
                            .listen('.NewChatMessage', (e) => {
                                console.log('Received NewChatMessage:', e);
                                // Find the Livewire component and refresh
                                const chatComponent = document.querySelector('[wire\\:id]');
                                if (chatComponent) {
                                    const wireId = chatComponent.getAttribute('wire:id');
                                    // Refresh the chat and trigger full component refresh
                                    Livewire.find(wireId).call('refreshChat');
                                    Livewire.find(wireId).$refresh();
                                    scrollToBottom();
                                }
                            })
                            .listen('.MessageStatusUpdated', (e) => {
                                console.log('Received MessageStatusUpdated:', e);
                                // Refresh to update tick marks when message status changes
                                const chatComponent = document.querySelector('[wire\\:id]');
                                if (chatComponent) {
                                    const wireId = chatComponent.getAttribute('wire:id');
                                    // Reload messages to show updated ticks
                                    Livewire.find(wireId).call('loadConversationMessages');
                                }
                            });
                        
                        console.log('Echo listener registered for channel: chat.' + userId);
                    } catch (err) {
                        console.error('Failed to setup Echo listener:', err);
                    }
                }
            }, 100);
            
            // Stop checking after 5 seconds
            setTimeout(() => {
                clearInterval(checkEcho);
                if (statusIndicator && !statusIndicator.classList.contains('online')) {
                    console.warn('Echo connection timed out');
                }
            }, 5000);
        }

        // Also use Livewire hooks
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('message-received', () => {
                scrollToBottom();
            });
        });

        function scrollToBottom() {
            setTimeout(() => {
                const chatMessages = document.getElementById('chatMessages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        }
    </script>
</div>
