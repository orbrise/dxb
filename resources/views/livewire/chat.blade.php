<div>
    @section('headerform')
    <div class="nav-bar navbar-top-nav">
        <div class="container-fluid"> 
            <a class="back-link" href="{{url('/')}}" wire:navigate>
                <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Back</span></a>
            <div class="title">
                <h1><a href="/my-account">My Chat</a></h1>
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
            position: relative;
        }

        .chat-search input {
            width: 100%;
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
            color: #f4b827;
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
            background: linear-gradient(135deg, #f4b827, #d4a017);
            color: #1a1a1a;
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
            color: #1a1a1a;
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
            border-color: #f4b827;
        }

        .chat-input-form input::placeholder {
            color: #888;
        }

        .chat-input-form button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #f4b827, #d4a017);
            color: #1a1a1a;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-input-form button:hover {
            background: #d4a017;
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

        /* Mobile Responsive */
        .mobile-back-btn {
            display: none;
            background: none;
            border: none;
            color: #f4b827;
            font-size: 20px;
            padding: 10px;
            cursor: pointer;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .chat-container {
                height: calc(100vh - 150px);
            }

            .chat-sidebar {
                position: absolute;
                width: 100%;
                height: 100%;
                z-index: 10;
                left: 0;
                top: 0;
            }

            .chat-sidebar.mobile-hidden {
                display: none;
            }

            .chat-main {
                width: 100%;
            }

            .mobile-back-btn {
                display: block;
            }

            .message-bubble {
                max-width: 85%;
            }
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

    <div class="container-fluid {{ $selectedConversationId ? 'hide-nav-mobile' : '' }}">
        <div class="content-wrapper no-sidebar">
            <div id="content">
                @include('components.communication-nav')
                
                <div class="mb-3 clearfix" style="clear: both;" id="my-chat">
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

                    {{-- WhatsApp-style Chat Container --}}
                    <div class="chat-container" wire:poll.5s>
                        {{-- Left Sidebar - Conversations --}}
                        <div class="chat-sidebar {{ $selectedConversationId ? 'mobile-hidden' : '' }}" id="chatSidebar">
                            <div class="chat-sidebar-header">
                                <h4>
                                    <i class="fa fa-comments"></i> Chat
                                    <span id="connection-status" class="connection-indicator offline" title="Connecting...">
                                        <i class="fa fa-circle"></i>
                                    </span>
                                </h4>
                                <div class="chat-search">
                                    <input 
                                        type="text" 
                                        placeholder="Search users to chat..." 
                                        wire:model.live.debounce.300ms="searchTerm">
                                    
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
                                        <i class="fa fa-inbox"></i>
                                        <h3>No conversations</h3>
                                        <p>Search for users above to start a chat.</p>
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
                                            <i class="fa fa-arrow-left"></i>
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
                                <div class="chat-messages" id="chatMessages" wire:poll.2s="loadConversationMessages">
                                    @foreach($this->conversationMessages as $message)
                                        <div class="message-wrapper {{ $message['is_mine'] ? 'sent' : 'received' }}">
                                            <div class="message-bubble">
                                                <p class="message-text">{{ $message['message'] }}</p>
                                                <div class="message-time">
                                                    <span>{{ \Carbon\Carbon::parse($message['created_at'])->format('h:i A') }}</span>
                                                    @if($message['is_mine'])
                                                        <span class="message-ticks">
                                                            @if($message['status'] === 'read')
                                                                {{-- Double blue ticks - Read --}}
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
                                    <i class="fa fa-comments"></i>
                                    <h3>Select a conversation</h3>
                                    <p>Choose from your existing conversations or search for a user to start a new chat.</p>
                                </div>
                            @endif
                        </div>
                    </div>
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
                    
                    // Update connection status
                    if (statusIndicator) {
                        statusIndicator.classList.remove('offline');
                        statusIndicator.classList.add('online');
                        statusIndicator.title = 'Connected';
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
