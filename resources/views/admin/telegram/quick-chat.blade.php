@extends('admin.layout.master')
@section('title', 'Quick Telegram Chat')
@section('content')

<style>
    .telegram-quick-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .telegram-header {
        background: linear-gradient(135deg, #0088cc 0%, #006699 100%);
        color: white;
        padding: 30px;
        border-radius: 10px 10px 0 0;
        text-align: center;
    }
    
    .telegram-header i {
        font-size: 48px;
        margin-bottom: 15px;
    }
    
    .telegram-header h2 {
        margin: 0;
        font-size: 24px;
    }
    
    .telegram-header p {
        margin: 10px 0 0;
        opacity: 0.9;
    }
    
    .telegram-form-container {
        background: #fff;
        padding: 30px;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .phone-input-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .country-code-input {
        width: 100px;
        flex-shrink: 0;
    }
    
    .phone-number-input {
        flex: 1;
    }
    
    .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 16px;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        border-color: #0088cc;
        box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1);
    }
    
    .btn-telegram {
        background: linear-gradient(135deg, #0088cc 0%, #006699 100%);
        border: none;
        color: white;
        padding: 15px 30px;
        font-size: 18px;
        border-radius: 8px;
        width: 100%;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-telegram:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 136, 204, 0.4);
        color: white;
    }
    
    .btn-telegram:active {
        transform: translateY(0);
    }
    
    .recent-chats {
        margin-top: 30px;
    }
    
    .recent-chats h4 {
        color: #666;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .recent-chat-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .recent-chat-item:hover {
        background: #e3f2fd;
    }
    
    .recent-chat-icon {
        width: 40px;
        height: 40px;
        background: #0088cc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .recent-chat-info {
        flex: 1;
    }
    
    .recent-chat-phone {
        font-weight: 600;
        color: #333;
    }
    
    .recent-chat-time {
        font-size: 12px;
        color: #999;
    }
    
    .recent-chat-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-sm-action {
        padding: 5px 12px;
        border-radius: 5px;
        font-size: 12px;
        border: none;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    
    .btn-sm-action:hover {
        opacity: 0.8;
    }
    
    .btn-open {
        background: #0088cc;
        color: white;
    }
    
    .btn-copy {
        background: #6c757d;
        color: white;
    }
    
    .btn-clear-history {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 12px;
        cursor: pointer;
    }
    
    .btn-clear-history:hover {
        text-decoration: underline;
    }
    
    .message-preview {
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        display: none;
    }
    
    .message-preview.show {
        display: block;
    }
    
    .message-preview-label {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
    }
    
    .message-preview-link {
        word-break: break-all;
        color: #0088cc;
        font-family: monospace;
        font-size: 13px;
    }
    
    .input-hint {
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }
    
    .username-section {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid #e0e0e0;
    }
    
    .username-section h5 {
        color: #333;
        margin-bottom: 15px;
    }
    
    .btn-telegram-username {
        background: linear-gradient(135deg, #0088cc 0%, #006699 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-telegram-username:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 136, 204, 0.4);
        color: white;
    }
</style>

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Telegram</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Send messages via Telegram</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Support</a></li>
            <li class="breadcrumb-item active">Telegram</li>
        </ol>
    </div>
</div>

<div class="container-fluid mt-3 mb-3">
    <div class="telegram-quick-container">
        <div class="telegram-header">
            <i class="fab fa-telegram-plane"></i>
            <h2 style="color:white">Quick Telegram Chat</h2>
            <p>Enter a phone number or username to open Telegram instantly</p>
        </div>
        
        <div class="telegram-form-container">
            <form id="quickTelegramForm">
                <label class="form-label mb-2"><strong>Phone Number</strong></label>
                <div class="phone-input-group">
                    <input type="text" 
                           id="countryCode" 
                           class="form-control country-code-input" 
                           placeholder="+971" 
                           value="971"
                           maxlength="5">
                    <input type="text" 
                           id="phoneNumber" 
                           class="form-control phone-number-input" 
                           placeholder="50 123 4567"
                           autofocus>
                </div>
                <p class="input-hint">Enter country code without + sign (e.g., 971 for UAE, 92 for Pakistan)</p>
                
                <div class="message-preview" id="messagePreview">
                    <div class="message-preview-label">Generated Link:</div>
                    <div class="message-preview-link" id="previewLink"></div>
                </div>
                
                <button type="submit" class="btn-telegram mt-3">
                    <i class="fab fa-telegram-plane"></i>
                    Open Telegram Chat
                </button>
            </form>
            
            {{-- Username Section --}}
            <div class="username-section">
                <h5><i class="fab fa-telegram mr-2"></i> Or Chat by Username</h5>
                <div class="input-group mb-3">
                    <span class="input-group-text" style="background: #e3f2fd; border: 2px solid #e0e0e0; border-right: none;font-size: 28px;">@</span>
                    <input type="text" 
                           id="telegramUsername" 
                           class="form-control" 
                           placeholder="username"
                           style="border-left: none;">
                    <button type="button" class="btn-telegram-username" id="openByUsername">
                        <i class="fab fa-telegram-plane"></i> Open
                    </button>
                </div>
                <p class="input-hint">Enter Telegram username without @ symbol</p>
            </div>
            
            @if(!empty($recentChats))
            <div class="recent-chats">
                <h4>
                    <span><i class="fa fa-history mr-2"></i> Recent Chats</span>
                    <button type="button" class="btn-clear-history" id="clearHistory">
                        <i class="fa fa-trash"></i> Clear History
                    </button>
                </h4>
                
                <div id="recentChatsList">
                    @foreach($recentChats as $chat)
                    <div class="recent-chat-item" data-phone="+{{ $chat['full_phone'] }}" data-url="{{ $chat['url'] }}">
                        <div class="recent-chat-icon">
                            <i class="fab fa-telegram-plane"></i>
                        </div>
                        <div class="recent-chat-info">
                            <div class="recent-chat-phone">+{{ $chat['full_phone'] }}</div>
                            <div class="recent-chat-time">{{ \Carbon\Carbon::parse($chat['created_at'])->diffForHumans() }}</div>
                        </div>
                        <div class="recent-chat-actions">
                            <button type="button" class="btn-sm-action btn-open" onclick="window.open('{{ $chat['url'] }}', '_blank')">
                                <i class="fab fa-telegram-plane"></i> Open
                            </button>
                            <button type="button" class="btn-sm-action btn-copy" onclick="copyToClipboard('{{ $chat['url'] }}')">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update preview when inputs change
    function updatePreview() {
        const countryCode = document.getElementById('countryCode').value.replace(/[^0-9]/g, '');
        const phone = document.getElementById('phoneNumber').value.replace(/[^0-9]/g, '');
        
        if (countryCode && phone) {
            let url = 'https://t.me/+' + countryCode + phone;
            document.getElementById('previewLink').textContent = url;
            document.getElementById('messagePreview').classList.add('show');
        } else {
            document.getElementById('messagePreview').classList.remove('show');
        }
    }
    
    document.getElementById('countryCode').addEventListener('input', updatePreview);
    document.getElementById('phoneNumber').addEventListener('input', updatePreview);
    
    // Form submit - directly open Telegram
    document.getElementById('quickTelegramForm').addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const countryCode = document.getElementById('countryCode').value.replace(/[^0-9]/g, '');
        const phone = document.getElementById('phoneNumber').value.replace(/[^0-9]/g, '');
        
        if (!countryCode || !phone) {
            alert('Please enter both country code and phone number');
            return false;
        }
        
        // Build Telegram URL
        let url = 'https://t.me/+' + countryCode + phone;
        
        // Open Telegram in new tab
        window.open(url, '_blank');
        
        // Also try to save to history via AJAX (optional, won't block)
        fetch('{{ route("admin.telegram.generate-link") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                country_code: countryCode,
                phone: phone
            })
        }).catch(function() {
            // Ignore errors
        });
        
        return false;
    });
    
    // Open by username
    document.getElementById('openByUsername').addEventListener('click', function() {
        const username = document.getElementById('telegramUsername').value.trim().replace('@', '');
        
        if (!username) {
            alert('Please enter a Telegram username');
            return;
        }
        
        window.open('https://t.me/' + username, '_blank');
    });
    
    // Also submit on Enter for username
    document.getElementById('telegramUsername').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('openByUsername').click();
        }
    });
    
    // Clear history
    var clearBtn = document.getElementById('clearHistory');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear the chat history?')) {
                fetch('{{ route("admin.telegram.clear-history") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(function() {
                    var list = document.getElementById('recentChatsList');
                    if (list) list.remove();
                    var recentChats = document.querySelector('.recent-chats');
                    if (recentChats) recentChats.remove();
                });
            }
        });
    }
    
    // Click on recent chat to fill form
    document.querySelectorAll('.recent-chat-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            if (e.target.closest('.recent-chat-actions')) return;
            
            const phone = this.getAttribute('data-phone').replace('+', '');
            
            if (phone.length > 9) {
                document.getElementById('countryCode').value = phone.substring(0, phone.length - 9);
                document.getElementById('phoneNumber').value = phone.substring(phone.length - 9);
            } else {
                document.getElementById('phoneNumber').value = phone;
            }
            
            updatePreview();
        });
    });
    
    // Initialize preview
    updatePreview();
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copied to clipboard!');
    }).catch(function() {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Link copied to clipboard!');
    });
}
</script>

@endsection
