<div>
    @section('headerform')
    <div class="nav-bar navbar-top-nav">
        <div class="container-fluid"> 
            <a class="back-link" href="{{url('/')}}" wire:navigate>
                <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Back</span></a>
            <div class="title">
                <h1><a href="/my-account">My Messages</a></h1>
            </div>
        </div>
    </div>
    @endsection

    <style>
        /* WhatsApp-style Chat Container */
        .chat-container {
            display: flex;
            height: calc(100vh - 200px);
            min-height: 500px;
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #333;
        }

        /* Left Panel - Conversation List */
        .chat-sidebar {
            width: 350px;
            min-width: 300px;
            background: #2a2a2a;
            border-right: 1px solid #333;
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar-header {
            padding: 15px;
            background: #333;
            border-bottom: 1px solid #444;
        }

        .chat-sidebar-header h4 {
            color: #f4b827;
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
            display: flex;
            gap: 10px;
        }

        .chat-search input {
            flex: 1;
            padding: 10px 15px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 20px;
            color: #fff;
            font-size: 14px;
        }

        .chat-search input:focus {
            outline: none;
            border-color: #f4b827;
        }

        .chat-search input::placeholder {
            color: #888;
        }

        .chat-filter {
            padding: 8px 35px 8px 15px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 20px;
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

        .chat-filter:focus {
            outline: none;
            border-color: #f4b827;
        }

        /* Conversation List */
        .conversation-list {
            flex: 1;
            overflow-y: auto;
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
            border-left: 3px solid #f4b827;
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f4b827, #d4a017);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin-right: 12px;
            flex-shrink: 0;
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
            color: #f4b827;
        }

        .unread-badge {
            background: #f4b827;
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
            background: #1a1a1a;
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
            background: linear-gradient(135deg, #f4b827, #d4a017);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-right: 12px;
        }

        .chat-header-name {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
        }

        .chat-header-phone {
            color: #888;
            font-size: 13px;
        }

        .chat-header-actions button {
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

        .chat-header-actions button:hover {
            color: #f4b827;
            background: rgba(244, 184, 39, 0.1);
        }

        .chat-header-actions button.delete:hover {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }

        /* Messages Area */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23222222' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .message-group {
            margin-bottom: 15px;
        }

        .message-date {
            text-align: center;
            margin: 20px 0;
        }

        .message-date span {
            background: #333;
            color: #888;
            padding: 5px 15px;
            border-radius: 10px;
            font-size: 12px;
        }

        .message-bubble {
            max-width: 65%;
            padding: 10px 15px;
            border-radius: 12px;
            margin-bottom: 5px;
            position: relative;
            word-wrap: break-word;
        }

        /* Incoming messages - Left side (from user) */
        .message-incoming {
            background: #2a2a2a;
            color: #fff;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }

        /* Outgoing messages - Right side (our replies) */
        .message-outgoing {
            background: #f4b827;
            color: #1a1a1a;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .message-text {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 5px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
        }

        .message-outgoing .message-time {
            color: #1a1a1a;
        }

        .message-incoming .message-time {
            color: #888;
        }

        .message-check {
            font-size: 12px;
            color: #000;
        }

        .message-profile {
            font-size: 11px;
            color: #f4b827;
            margin-bottom: 3px;
        }

        .message-outgoing .message-profile {
            color: #666;
        }

        /* Chat Input */
        .chat-input {
            padding: 15px 20px;
            background: #2a2a2a;
            border-top: 1px solid #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-input textarea {
            flex: 1;
            padding: 12px 20px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 25px;
            color: #fff;
            font-size: 14px;
            resize: none;
            max-height: 100px;
            min-height: 45px;
        }

        .chat-input textarea:focus {
            outline: none;
            border-color: #f4b827;
        }

        .chat-input textarea::placeholder {
            color: #888;
        }

        .chat-input button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #f4b827;
            border: none;
            color: #1a1a1a;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-input button:hover {
            background: #d4a017;
            transform: scale(1.05);
        }

        .chat-input button:disabled {
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

        .chat-empty i {
            font-size: 80px;
            margin-bottom: 20px;
            color: #444;
        }

        .chat-empty h3 {
            color: #888;
            margin-bottom: 10px;
        }

        .chat-empty p {
            color: #666;
            max-width: 300px;
        }

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

        .stat-card h3.text-warning { color: #f4b827; }
        .stat-card h3.text-success { color: #28a745; }

        .stat-card p {
            color: #888;
            margin: 0;
            font-size: 14px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .chat-container {
                height: calc(100vh - 180px);
                flex-direction: column;
            }

            .chat-sidebar {
                width: 100%;
                min-width: 100%;
                height: 100%;
                max-height: 100%;
                border-right: none;
                border-bottom: none;
                flex: 1;
            }

            .chat-sidebar.hidden {
                display: none;
            }

            .chat-sidebar.hidden ~ .chat-main.fullscreen {
                height: calc(100vh - 60px);
            }

            .chat-main {
                display: none;
                height: 100%;
            }

            .chat-main.fullscreen {
                display: flex;
                height: 100%;
            }

            .mobile-back-btn {
                display: flex !important;
            }

            .message-bubble {
                max-width: 85%;
            }

            .stats-row {
                flex-wrap: wrap;
            }

            .stat-card {
                flex: 1 1 calc(33.33% - 10px);
                min-width: 100px;
            }

            .conversation-avatar {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            /* Hide communication nav when conversation is open */
            .chat-sidebar.hidden + .chat-main.fullscreen ~ .communication-nav,
            .chat-container:has(.chat-sidebar.hidden) ~ .communication-nav {
                display: none;
            }
        }

        /* Hide communication nav when conversation is selected on mobile */
        @media (max-width: 768px) {
            .hide-nav-mobile .communication-nav {
                display: none !important;
            }
        }

        .mobile-back-btn {
            display: none;
            background: #333;
            border: none;
            color: #f4b827;
            font-size: 20px;
            cursor: pointer;
            padding: 5px 10px;
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

    <div class="container-fluid {{ $selectedConversation ? 'hide-nav-mobile' : '' }}">
        <div class="content-wrapper no-sidebar">
            <div id="content">
                @include('components.communication-nav')
                
                <div class="mb-3 clearfix" style="clear: both;" id="my-messages">
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
                            <h3>{{ $totalMessages }}</h3>
                            <p>Total Messages</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="text-warning">{{ $unreadCount }}</h3>
                            <p>Unread</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="text-success">{{ $repliedCount }}</h3>
                            <p>Replied</p>
                        </div>
                    </div> --}}

                    {{-- WhatsApp-style Chat Container --}}
                    <div class="chat-container">
                        {{-- Left Sidebar - Conversations --}}
                        <div class="chat-sidebar {{ $selectedConversation ? 'hidden' : '' }}" id="chatSidebar">
                            <div class="chat-sidebar-header">
                                <h4>
                                    <i class="fa fa-comments"></i> Conversations
                                    <span id="connection-status" class="connection-indicator offline" title="Connecting...">
                                        <i class="fa fa-circle"></i>
                                    </span>
                                </h4>
                                <div class="chat-search">
                                    <input 
                                        type="text" 
                                        placeholder="Search conversations..." 
                                        wire:model.live.debounce.300ms="searchTerm">
                                    <select class="chat-filter" wire:model.live="filterStatus">
                                        <option value="all">All</option>
                                        <option value="unread">Unread</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="conversation-list">
                                @forelse($conversations as $conversation)
                                    <div 
                                        class="conversation-item {{ $selectedConversation === $conversation->user_email ? 'active' : '' }} {{ $conversation->unread_count > 0 ? 'unread' : '' }}"
                                        wire:click="selectConversation('{{ $conversation->user_email }}')"
                                    >
                                        <div class="conversation-avatar">
                                            {{ strtoupper(substr($conversation->user_email, 0, 1)) }}
                                        </div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">{{ $conversation->user_email }}</div>
                                            <div class="conversation-preview">
                                                @if($conversation->phone)
                                                    <i class="fa fa-phone" style="font-size: 11px;"></i> +{{ $conversation->code }} {{ $conversation->phone }}
                                                @else
                                                    {{ Str::limit($conversation->last_message ?? 'No messages', 30) }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="conversation-meta">
                                            <div class="conversation-time">
                                                {{ \Carbon\Carbon::parse($conversation->last_message_at)->diffForHumans(null, true) }}
                                            </div>
                                            @if($conversation->unread_count > 0)
                                                <span class="unread-badge">{{ $conversation->unread_count }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="chat-empty" style="padding: 40px 20px;">
                                        <i class="fa fa-inbox"></i>
                                        <h3>No conversations</h3>
                                        <p>You haven't received any messages yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Right Panel - Chat Messages --}}
                        <div class="chat-main {{ $selectedConversation ? 'fullscreen' : '' }}">
                            @if($selectedConversation)
                                {{-- Chat Header --}}
                                <button class="mobile-back-btn" wire:click="closeConversation">
                                            <i class="fa fa-arrow-left"></i>
                                        </button>
                                <div class="chat-header">
                                 
                                    <div class="chat-header-info">
                                       
                                        <div class="chat-header-avatar">
                                            {{ strtoupper(substr($selectedConversation, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="chat-header-name">{{ $selectedConversation }}</div>
                                            @php
                                                $firstMsg = collect($conversationMessages)->first();
                                            @endphp
                                            @if($firstMsg && isset($firstMsg['phone']))
                                                <div class="chat-header-phone">
                                                    <i class="fa fa-phone"></i> +{{ $firstMsg['code'] ?? '' }} {{ $firstMsg['phone'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chat-header-actions">
                                        <button wire:click="deleteConversation" class="delete" onclick="return confirm('Delete this entire conversation?')" title="Delete conversation">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button wire:click="closeConversation" title="Close">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Messages Area --}}
                                <div class="chat-messages" id="chatMessages">
                                    @php
                                        $lastDate = null;
                                    @endphp
                                    @foreach($conversationMessages as $msg)
                                        @php
                                            $msgDate = \Carbon\Carbon::parse($msg['created_at'])->format('M d, Y');
                                            $isOurReply = ($msg['status'] ?? '') === 'sent';
                                        @endphp
                                        
                                        {{-- Date separator --}}
                                        @if($lastDate !== $msgDate)
                                            <div class="message-date">
                                                <span>{{ $msgDate }}</span>
                                            </div>
                                            @php $lastDate = $msgDate; @endphp
                                        @endif

                                        {{-- Message bubble --}}
                                        <div class="message-bubble {{ $isOurReply ? 'message-outgoing' : 'message-incoming' }}">
                                            @if(!$isOurReply && isset($msg['profile']) && $msg['profile'])
                                                <div class="message-profile">
                                                    To: {{ $msg['profile']['name'] ?? 'Profile' }}
                                                </div>
                                            @endif
                                            <div class="message-text">{{ $msg['message'] }}</div>
                                            <div class="message-time">
                                                {{ \Carbon\Carbon::parse($msg['created_at'])->format('h:i A') }}
                                                @if($isOurReply)
                                                    <i class="fa fa-check-double message-check"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Chat Input --}}
                                <div class="chat-input">
                                    <textarea 
                                        wire:model="reply" 
                                        placeholder="Type a message..."
                                        rows="1"
                                        wire:keydown.enter.prevent="sendReply"
                                    ></textarea>
                                    <button wire:click="sendReply" wire:loading.attr="disabled" title="Send">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                </div>
                            @else
                                {{-- Empty state when no conversation selected --}}
                                <div class="chat-empty">
                                    <i class="fa fa-comments"></i>
                                    <h3>Select a conversation</h3>
                                    <p>Choose a conversation from the left to start chatting</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        document.addEventListener('livewire:navigated', function() {
            scrollToBottom();
        });
        
        // Livewire 3 hook for after DOM updates
        document.addEventListener('livewire:morph', function() {
            scrollToBottom();
        });
        
        // Listen for real-time message events
        document.addEventListener('livewire:init', () => {
            Livewire.on('message-received', () => {
                scrollToBottom();
                // Play notification sound (optional)
                playNotificationSound();
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

        function playNotificationSound() {
            // Optional: Add a notification sound for new messages
            try {
                const audio = new Audio('/assets/sounds/notification.mp3');
                audio.volume = 0.3;
                audio.play().catch(() => {}); // Ignore if autoplay blocked
            } catch(e) {}
        }

        // Mobile: Toggle sidebar visibility
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('chatSidebar');
            sidebar.classList.toggle('hidden');
        }
        
        // Show online status indicator
        document.addEventListener('DOMContentLoaded', function() {
            const statusIndicator = document.getElementById('connection-status');
            if (statusIndicator && window.Echo) {
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    statusIndicator.classList.remove('offline');
                    statusIndicator.classList.add('online');
                    statusIndicator.title = 'Real-time connected';
                });
                window.Echo.connector.pusher.connection.bind('disconnected', () => {
                    statusIndicator.classList.remove('online');
                    statusIndicator.classList.add('offline');
                    statusIndicator.title = 'Reconnecting...';
                });
            }
        });
    </script>
</div>
