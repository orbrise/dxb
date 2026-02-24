@extends("admin.layout.master")

@section("content")
<style>
    .whatsapp-container {
        display: flex;
        height: calc(100vh - 180px);
        background: #111b21;
        border-radius: 8px;
        overflow: hidden;
    }
    
    /* Sidebar - Conversations List */
    .conversations-sidebar {
        width: 350px;
        border-right: 1px solid #2a3942;
        display: flex;
        flex-direction: column;
        background: #111b21;
    }
    
    .sidebar-header {
        padding: 15px;
        background: #202c33;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .sidebar-header h5 {
        color: #e9edef;
        margin: 0;
    }
    
    .search-box {
        padding: 10px 15px;
        background: #111b21;
    }
    
    .search-box input {
        width: 100%;
        padding: 10px 15px;
        border-radius: 8px;
        border: none;
        background: #202c33;
        color: #e9edef;
    }
    
    .search-box input::placeholder {
        color: #8696a0;
    }
    
    .conversations-list {
        flex: 1;
        overflow-y: auto;
    }
    
    .conversation-item {
        padding: 12px 15px;
        display: flex;
        align-items: center;
        cursor: pointer;
        border-bottom: 1px solid #2a3942;
        transition: background 0.2s;
    }
    
    .conversation-item:hover, .conversation-item.active {
        background: #2a3942;
    }
    
    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #00a884;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .conversation-info {
        flex: 1;
        overflow: hidden;
    }
    
    .conversation-name {
        color: #e9edef;
        font-weight: 500;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-preview {
        color: #8696a0;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-meta {
        text-align: right;
        flex-shrink: 0;
    }
    
    .conversation-time {
        color: #8696a0;
        font-size: 12px;
    }
    
    .unread-badge {
        background: #00a884;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
        margin-top: 5px;
    }
    
    /* Chat Area */
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #0b141a;
    }
    
    .chat-header {
        padding: 10px 20px;
        background: #202c33;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #2a3942;
    }
    
    .chat-header .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #00a884;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 15px;
    }
    
    .chat-header-info h6 {
        color: #e9edef;
        margin: 0;
    }
    
    .chat-header-info small {
        color: #8696a0;
    }
    
    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAABdklEQVR4nO2ZsU7DMBCGv4KQGBhYGJB4BQZmVt6AN2BhQmJgYOYNWBgZmMrCU7AwsDDxCiwMDMidFClKUeI4d05a95O+odu5//qfHTtFo9FoNBqNRqPR/B9mQBt4AI5rF0qgB9wCr8AZcFq7YALdIuA18AgsgOOyB+ZAC7gEnoFTYLdswQTaZYB3oAt8hf8/AM2yBRPolgE+gHfgGzioCPgCdCoKRnkCBkAf+AYOKwJGOQcGwPK3A/AK7FcEjHIODEpKRtkCbgELuCoDKN0RcBMBP8AB8BQBv4Bt4DoC1h0x0PtrIuBnpjsCdqu6UWLgi+T+W3dEzA9LTAz8XGlH9P6aCoiB7wsHJyZ0R8T8sMTEwM+VdkTPrymAGPheCTgxoTsi5oclJgZ+rrQjen5NAcTA90rAiQndETE/LDEx8HOlHdHzawogBr5XAk5M6I6I+WGJiYGfK+2Inl9TADHwvRJwYkJ3RMwPS0wM/Nz/1hE/SfmZ0J8kK/MAAAAASUVORK5CYII=');
    }
    
    .message {
        max-width: 65%;
        margin-bottom: 10px;
        clear: both;
    }
    
    .message-outgoing {
        float: right;
    }
    
    .message-incoming {
        float: left;
    }
    
    .message-bubble {
        padding: 8px 12px;
        border-radius: 8px;
        position: relative;
    }
    
    .message-outgoing .message-bubble {
        background: #005c4b;
        color: #e9edef;
        border-top-right-radius: 0;
    }
    
    .message-incoming .message-bubble {
        background: #202c33;
        color: #e9edef;
        border-top-left-radius: 0;
    }
    
    .message-text {
        word-wrap: break-word;
        white-space: pre-wrap;
    }
    
    .message-time {
        font-size: 11px;
        color: rgba(255,255,255,0.6);
        text-align: right;
        margin-top: 3px;
    }
    
    .message-status {
        display: inline-block;
        margin-left: 5px;
    }
    
    /* Input Area */
    .chat-input {
        padding: 15px 20px;
        background: #202c33;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .chat-input textarea {
        flex: 1;
        padding: 12px 15px;
        border-radius: 8px;
        border: none;
        background: #2a3942;
        color: #e9edef;
        resize: none;
        max-height: 100px;
    }
    
    .chat-input textarea::placeholder {
        color: #8696a0;
    }
    
    .send-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: none;
        background: #00a884;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .send-btn:hover {
        background: #00c896;
    }
    
    /* No Chat Selected */
    .no-chat-selected {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #8696a0;
    }
    
    .no-chat-selected i {
        font-size: 80px;
        margin-bottom: 20px;
        color: #00a884;
    }
    
    /* New Chat Modal */
    .new-chat-modal .modal-content {
        background: #202c33;
        color: #e9edef;
    }
    
    .new-chat-modal .modal-header {
        border-bottom: 1px solid #2a3942;
    }
    
    .new-chat-modal .modal-footer {
        border-top: 1px solid #2a3942;
    }
    
    .new-chat-modal .form-control {
        background: #2a3942;
        border: none;
        color: #e9edef;
    }
    
    .new-chat-modal .btn-close {
        filter: invert(1);
    }
    
    /* Quick Send Form */
    .quick-send-form {
        background: #202c33;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .quick-send-form .form-control {
        background: #2a3942;
        border: none;
        color: #e9edef;
    }
    
    .quick-send-form .form-control::placeholder {
        color: #8696a0;
    }
</style>

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5"><i class="fa fa-whatsapp text-success"></i> WhatsApp Messaging</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Send and receive WhatsApp messages</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">WhatsApp</li>
        </ol>
    </div>
</div>

<!-- Quick Send Section -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="quick-send-form">
            <h6 class="text-white mb-3"><i class="fa fa-paper-plane"></i> Quick Send Message</h6>
            <form id="quickSendForm">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="quickPhone" placeholder="Phone number (with country code)" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="quickName" placeholder="Name (optional)">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" id="quickMessage" placeholder="Type your message..." required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-send"></i> Send
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Main Chat Interface -->
<div class="row">
    <div class="col-lg-12">
        <div class="whatsapp-container">
            <!-- Conversations Sidebar -->
            <div class="conversations-sidebar">
                <div class="sidebar-header">
                    <h5><i class="fa fa-comments"></i> Chats</h5>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <div class="search-box">
                    <input type="text" id="searchConversations" placeholder="Search or start new chat">
                </div>
                <div class="conversations-list" id="conversationsList">
                    @forelse($conversations as $conv)
                    <div class="conversation-item" data-id="{{ $conv->id }}" data-phone="{{ $conv->phone_number }}" data-name="{{ $conv->name }}">
                        <div class="conversation-avatar">
                            {{ strtoupper(substr($conv->name, 0, 1)) }}
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">{{ $conv->name }}</div>
                            <div class="conversation-preview">
                                @if($conv->lastMessage)
                                    {{ Str::limit($conv->lastMessage->message, 30) }}
                                @else
                                    {{ $conv->phone_number }}
                                @endif
                            </div>
                        </div>
                        <div class="conversation-meta">
                            <div class="conversation-time">
                                {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}
                            </div>
                            @if($conv->unread_count > 0)
                            <div class="unread-badge">{{ $conv->unread_count }}</div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center p-4 text-muted">
                        <i class="fa fa-comments fa-3x mb-3"></i>
                        <p>No conversations yet.<br>Start a new chat!</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="chat-area">
                <div id="noChatSelected" class="no-chat-selected">
                    <i class="fa fa-whatsapp"></i>
                    <h4>WhatsApp Admin Panel</h4>
                    <p>Select a conversation or start a new chat</p>
                </div>
                
                <div id="chatContainer" style="display: none; flex: 1; display: none; flex-direction: column;">
                    <div class="chat-header">
                        <div class="avatar" id="chatAvatar">A</div>
                        <div class="chat-header-info">
                            <h6 id="chatName">Contact Name</h6>
                            <small id="chatPhone">+1234567890</small>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-outline-danger btn-sm" id="deleteConversation">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="chat-messages" id="chatMessages">
                        <!-- Messages will be loaded here -->
                    </div>
                    
                    <div class="chat-input">
                        <textarea id="messageInput" rows="1" placeholder="Type a message"></textarea>
                        <button class="send-btn" id="sendMessage">
                            <i class="fa fa-send"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade new-chat-modal" id="newChatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus"></i> Start New Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Phone Number (with country code)</label>
                    <input type="text" class="form-control" id="newChatPhone" placeholder="e.g., 971501234567">
                </div>
                <div class="mb-3">
                    <label class="form-label">Name (optional)</label>
                    <input type="text" class="form-control" id="newChatName" placeholder="Contact name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="startNewChat">
                    <i class="fa fa-comments"></i> Start Chat
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    let currentConversationId = null;
    const token = '{{ csrf_token() }}';
    
    // Search conversations
    $('#searchConversations').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('.conversation-item').each(function() {
            const name = $(this).data('name').toLowerCase();
            const phone = $(this).data('phone').toLowerCase();
            if (name.includes(search) || phone.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Select conversation
    $(document).on('click', '.conversation-item', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const phone = $(this).data('phone');
        
        $('.conversation-item').removeClass('active');
        $(this).addClass('active');
        $(this).find('.unread-badge').remove();
        
        currentConversationId = id;
        
        $('#chatName').text(name);
        $('#chatPhone').text('+' + phone);
        $('#chatAvatar').text(name.charAt(0).toUpperCase());
        
        $('#noChatSelected').hide();
        $('#chatContainer').css('display', 'flex');
        
        loadMessages(id);
    });
    
    // Load messages
    function loadMessages(conversationId) {
        $('#chatMessages').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        
        $.get(`{{ url('admin/whatsapp/messages') }}/${conversationId}`, function(response) {
            const messages = response.messages;
            let html = '';
            
            messages.forEach(function(msg) {
                const isOutgoing = msg.direction === 'outgoing';
                const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                html += `
                    <div class="message ${isOutgoing ? 'message-outgoing' : 'message-incoming'}">
                        <div class="message-bubble">
                            <div class="message-text">${escapeHtml(msg.message)}</div>
                            <div class="message-time">
                                ${time}
                                ${isOutgoing ? '<span class="message-status"><i class="fa fa-check"></i></span>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            if (messages.length === 0) {
                html = '<div class="text-center text-muted p-4">No messages yet. Send your first message!</div>';
            }
            
            $('#chatMessages').html(html);
            scrollToBottom();
        });
    }
    
    // Send message
    $('#sendMessage').click(sendCurrentMessage);
    
    $('#messageInput').keypress(function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendCurrentMessage();
        }
    });
    
    function sendCurrentMessage() {
        const message = $('#messageInput').val().trim();
        if (!message || !currentConversationId) return;
        
        const $btn = $('#sendMessage');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("admin.whatsapp.send") }}',
            type: 'POST',
            data: {
                _token: token,
                conversation_id: currentConversationId,
                message: message
            },
            success: function(response) {
                if (response.success) {
                    $('#messageInput').val('');
                    loadMessages(currentConversationId);
                } else {
                    alert('Failed to send: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr) {
                alert('Error sending message');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-send"></i>');
            }
        });
    }
    
    // Quick send form
    $('#quickSendForm').submit(function(e) {
        e.preventDefault();
        
        const phone = $('#quickPhone').val().trim();
        const name = $('#quickName').val().trim();
        const message = $('#quickMessage').val().trim();
        
        if (!phone || !message) return;
        
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("admin.whatsapp.quick-send") }}',
            type: 'POST',
            data: {
                _token: token,
                phone_number: phone,
                name: name,
                message: message
            },
            success: function(response) {
                if (response.success) {
                    $('#quickPhone').val('');
                    $('#quickName').val('');
                    $('#quickMessage').val('');
                    
                    // Reload page to show new conversation
                    location.reload();
                } else {
                    alert('Failed to send: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr) {
                alert('Error sending message');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-send"></i> Send');
            }
        });
    });
    
    // Start new chat
    $('#startNewChat').click(function() {
        const phone = $('#newChatPhone').val().trim();
        const name = $('#newChatName').val().trim();
        
        if (!phone) {
            alert('Please enter a phone number');
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.whatsapp.start-conversation") }}',
            type: 'POST',
            data: {
                _token: token,
                phone_number: phone,
                name: name
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Error starting conversation');
            }
        });
    });
    
    // Delete conversation
    $('#deleteConversation').click(function() {
        if (!currentConversationId) return;
        
        if (confirm('Are you sure you want to delete this conversation?')) {
            $.ajax({
                url: `{{ url('admin/whatsapp/conversation') }}/${currentConversationId}`,
                type: 'DELETE',
                data: { _token: token },
                success: function() {
                    location.reload();
                }
            });
        }
    });
    
    // Scroll to bottom of messages
    function scrollToBottom() {
        const container = document.getElementById('chatMessages');
        container.scrollTop = container.scrollHeight;
    }
    
    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Auto-refresh messages every 10 seconds
    setInterval(function() {
        if (currentConversationId) {
            loadMessages(currentConversationId);
        }
    }, 10000);
});
</script>
@endpush
