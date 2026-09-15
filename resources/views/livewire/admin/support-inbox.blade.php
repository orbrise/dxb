<div>
@push('css')
{{-- Load Vite so window.Echo is defined in the admin panel (admin master
     doesn't @vite by default; scoped here so no side-effects elsewhere). --}}
@vite(['resources/js/app.js'])
@include('admin._partials.settings-modern')
<style>
/* Support Inbox — styled to match the modern admin theme (indigo/slate light).
   Uses the same design tokens as admin._partials.settings-modern. */
.s-modern .si-wrap {
    display: flex; height: calc(100vh - 220px); min-height: 520px; gap: 14px;
}

/* ---------- Left sidebar (customer list) ---------- */
.s-modern .si-side {
    width: 340px; min-width: 300px;
    background: #fff;
    border: 1px solid var(--sm-slate-200);
    border-radius: 14px;
    display: flex; flex-direction: column;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.s-modern .si-side-head { padding: 16px 16px 10px; border-bottom: 1px solid var(--sm-slate-100); }
.s-modern .si-search {
    width: 100%; padding: 9px 12px;
    background: var(--sm-slate-50);
    border: 1px solid var(--sm-slate-200);
    border-radius: 8px;
    color: var(--sm-slate-800);
    font-size: 13px;
}
.s-modern .si-search:focus {
    outline: none;
    border-color: var(--sm-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}
.s-modern .si-search::placeholder { color: var(--sm-slate-500); }

.s-modern .si-list { flex: 1; overflow-y: auto; padding: 8px; }
.s-modern .si-item {
    display: flex; align-items: center;
    padding: 11px 12px;
    border-radius: 10px; cursor: pointer;
    transition: background .15s;
    margin-bottom: 4px;
}
.s-modern .si-item:hover { background: var(--sm-slate-50); }
.s-modern .si-item.active {
    background: rgba(99, 102, 241, 0.08);
    box-shadow: inset 3px 0 0 0 var(--sm-primary);
}
.s-modern .si-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 14px;
    margin-right: 10px; overflow: hidden; flex-shrink: 0;
}
.s-modern .si-avatar img { width: 100%; height: 100%; object-fit: cover; }
.s-modern .si-info { flex: 1; min-width: 0; }
.s-modern .si-name { color: var(--sm-slate-800); font-weight: 600; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.s-modern .si-preview { color: var(--sm-slate-500); font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.s-modern .si-meta { text-align: right; margin-left: 8px; flex-shrink: 0; }
.s-modern .si-time { color: var(--sm-slate-500); font-size: 10.5px; margin-bottom: 4px; }
.s-modern .si-badge {
    background: var(--sm-primary); color: #fff;
    font-size: 10.5px; font-weight: 600;
    padding: 2px 7px; border-radius: 999px;
}

/* ---------- Right pane (message thread) ---------- */
.s-modern .si-main {
    flex: 1; display: flex; flex-direction: column;
    background: #fff;
    border: 1px solid var(--sm-slate-200);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.s-modern .si-head {
    padding: 14px 20px;
    border-bottom: 1px solid var(--sm-slate-100);
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    display: flex; align-items: center; justify-content: space-between;
}
.s-modern .si-head-name { color: var(--sm-slate-800); font-weight: 600; font-size: 15px; }
.s-modern .si-head-mail { color: var(--sm-slate-500); font-size: 12.5px; }
.s-modern .si-msgs {
    flex: 1; overflow-y: auto;
    padding: 20px;
    display: flex; flex-direction: column;
    gap: 6px;
    background: var(--sm-slate-50);
}
.s-modern .si-msg { display: flex; max-width: 100%; }
.s-modern .si-msg.sent { justify-content: flex-end; }
.s-modern .si-msg.recv { justify-content: flex-start; }
.s-modern .si-bub {
    max-width: 70%;
    padding: 9px 13px 6px;
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
    font-size: 14px; line-height: 1.4;
}
/* Support agent bubbles: solid indigo (their own replies). */
.s-modern .si-msg.sent .si-bub {
    background: linear-gradient(180deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%);
    color: #fff;
    border-top-right-radius: 4px;
}
/* Customer bubbles: neutral white card. */
.s-modern .si-msg.recv .si-bub {
    background: #fff;
    color: var(--sm-slate-800);
    border: 1px solid var(--sm-slate-200);
    border-top-left-radius: 4px;
}
.s-modern .si-bub p { margin: 0; word-wrap: break-word; }
.s-modern .si-btime { font-size: 10.5px; margin-top: 3px; opacity: 0.7; text-align: right; }
.s-modern .si-msg.sent .si-btime { color: #e0e7ff; }
.s-modern .si-msg.recv .si-btime { color: var(--sm-slate-500); }
.s-modern .si-msg.sent .si-bub .si-agent {
    font-size: 10.5px; font-weight: 700;
    color: #e0e7ff; opacity: 0.85;
    display: block; margin-bottom: 2px;
}

/* ---------- Input row ---------- */
.s-modern .si-input {
    padding: 12px 16px;
    background: #fff;
    border-top: 1px solid var(--sm-slate-100);
    display: flex; gap: 8px; align-items: center;
}
.s-modern .si-input textarea {
    flex: 1;
    padding: 10px 14px;
    background: var(--sm-slate-50);
    border: 1px solid var(--sm-slate-200);
    border-radius: 10px;
    color: var(--sm-slate-800);
    font-size: 14px;
    resize: none;
    min-height: 42px; max-height: 120px;
}
.s-modern .si-input textarea:focus {
    outline: none;
    border-color: var(--sm-primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}
.s-modern .si-input button[type="submit"] {
    width: 42px; height: 42px; border-radius: 50%; border: none;
    background: linear-gradient(180deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%);
    color: #fff; cursor: pointer; font-size: 15px;
    box-shadow: 0 3px 8px rgba(99, 102, 241, 0.25);
    transition: transform .12s, box-shadow .12s;
}
.s-modern .si-input button[type="submit"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.32);
}
.s-modern .si-input button[type="submit"]:disabled {
    background: var(--sm-slate-200); color: var(--sm-slate-500);
    cursor: not-allowed; box-shadow: none; transform: none;
}
.s-modern .si-empty {
    flex: 1;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    color: var(--sm-slate-500); text-align: center; padding: 40px;
}
.s-modern .si-empty i { font-size: 44px; color: var(--sm-slate-300); margin-bottom: 14px; }
.s-modern .si-empty h4 { color: var(--sm-slate-700); margin: 0 0 6px; font-size: 16px; font-weight: 600; }
.s-modern .si-empty p { color: var(--sm-slate-500); font-size: 13px; max-width: 280px; }

/* WebSocket state indicator next to "Customers" title */
.s-modern .si-conn { font-size: 10px; margin-left: 8px; }
.s-modern .si-conn.online { color: var(--sm-success); }
.s-modern .si-conn.offline { color: var(--sm-slate-500); animation: si-pulse 1.5s infinite ease-in-out; }
@keyframes si-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }

.s-modern .si-ticks { display: inline-flex; margin-left: 4px; }
.s-modern .si-ticks .t { color: #e0e7ff; }
.s-modern .si-ticks .t.read { color: #93c5fd; font-weight: 700; }

/* Scrollbars in soft slate to match the theme */
.s-modern .si-msgs::-webkit-scrollbar,
.s-modern .si-list::-webkit-scrollbar { width: 6px; }
.s-modern .si-msgs::-webkit-scrollbar-track,
.s-modern .si-list::-webkit-scrollbar-track { background: transparent; }
.s-modern .si-msgs::-webkit-scrollbar-thumb,
.s-modern .si-list::-webkit-scrollbar-thumb { background: var(--sm-slate-300); border-radius: 3px; }
.s-modern .si-msgs::-webkit-scrollbar-thumb:hover,
.s-modern .si-list::-webkit-scrollbar-thumb:hover { background: var(--sm-slate-500); }

/* ---------- Media inside messages ---------- */
.s-modern .si-msg-image {
    display: block; max-width: 260px; max-height: 300px;
    border-radius: 8px; margin: 4px 0; cursor: pointer;
}
.s-modern .si-msg-audio audio { width: 220px; height: 32px; }
.s-modern .si-msg-file {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 10px;
    background: rgba(255, 255, 255, 0.12);
    border-radius: 8px;
    color: inherit; text-decoration: none;
    min-width: 200px;
}
.s-modern .si-msg.recv .si-msg-file {
    background: var(--sm-slate-100);
    color: var(--sm-slate-800);
}
.s-modern .si-msg-file i { font-size: 20px; }
.s-modern .si-msg-file .fname { font-weight: 600; font-size: 13px; word-break: break-all; }
.s-modern .si-msg-file .fsize { font-size: 11px; opacity: 0.7; }

/* ---------- Attachment / mic controls ---------- */
.s-modern .si-input .si-att-btn,
.s-modern .si-input .si-mic-btn {
    width: 38px; height: 38px; border-radius: 50%; border: none;
    background: transparent;
    color: var(--sm-slate-500);
    cursor: pointer; font-size: 15px;
    transition: background .15s, color .15s;
}
.s-modern .si-input .si-att-btn:hover,
.s-modern .si-input .si-mic-btn:hover {
    background: var(--sm-slate-100);
    color: var(--sm-primary);
}
.s-modern .si-input .si-mic-btn.recording {
    background: var(--sm-danger); color: #fff; animation: siRecPulse 1s infinite;
}
@keyframes siRecPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
    50%      { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
}
.s-modern .si-rec-bar {
    display: none; align-items: center; gap: 8px;
    padding: 0 10px;
    color: var(--sm-danger);
    font-size: 12px; font-weight: 600;
}
.s-modern .si-rec-bar.active { display: flex; flex: 1; }
.s-modern .si-rec-bar .dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--sm-danger); animation: siRecPulse 1s infinite;
}
.s-modern .si-rec-bar .cancel {
    margin-left: auto;
    background: transparent; border: 1px solid var(--sm-danger); color: var(--sm-danger);
    padding: 3px 10px; border-radius: 6px;
    cursor: pointer; font-size: 11px;
}

/* ---------- Upload preview ---------- */
.s-modern .si-att-preview {
    display: none; align-items: center; gap: 10px;
    padding: 10px 16px;
    background: var(--sm-slate-50);
    border-top: 1px solid var(--sm-slate-100);
}
.s-modern .si-att-preview.open { display: flex; }
.s-modern .si-att-preview img.thumb { width: 42px; height: 42px; object-fit: cover; border-radius: 6px; }
.s-modern .si-att-preview .info { flex: 1; color: var(--sm-slate-800); font-size: 12.5px; }
.s-modern .si-att-preview .info small { display: block; color: var(--sm-slate-500); font-size: 10.5px; }
.s-modern .si-att-preview button {
    background: transparent; border: none; color: var(--sm-danger);
    font-size: 18px; cursor: pointer;
}

/* ---------- Lightbox ---------- */
.si-lightbox {
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, 0.88);
    display: none; align-items: center; justify-content: center;
    z-index: 100000; padding: 20px;
}
.si-lightbox.open { display: flex; }
.si-lightbox img { max-width: 100%; max-height: 100%; border-radius: 6px; }
.si-lightbox .close-btn {
    position: absolute; top: 20px; right: 24px;
    background: none; border: none; color: #fff;
    font-size: 32px; cursor: pointer;
}
</style>
@endpush

<div class="s-modern">
    <div class="row page-title clearfix">
        <div class="page-title-left">
            <h5 class="mr-0 mr-r-5">Support Inbox</h5>
            <p class="mr-0 text-muted d-none d-md-inline-block">Reply to customer support chats in real time</p>
        </div>
        <div class="page-title-right d-none d-sm-inline-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Support Inbox</li>
            </ol>
        </div>
    </div>

    <div class="si-wrap">
        {{-- Left: conversation list --}}
        <div class="si-side">
            <div class="si-side-head">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                    <div style="color: var(--sm-slate-800); font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa fa-headset" style="color: var(--sm-primary);"></i> Customers
                        <span id="si-conn" class="si-conn offline"><i class="fa fa-circle"></i></span>
                    </div>
                    <span class="si-badge">{{ $conversations->count() }}</span>
                </div>
                <input type="text" class="si-search" placeholder="Search by name or email…" wire:model.live.debounce.300ms="searchTerm">
            </div>
            <div class="si-list">
                @forelse($conversations as $c)
                    <div class="si-item {{ $selectedConversationId == $c['id'] ? 'active' : '' }}"
                         wire:click="selectConversation({{ $c['id'] }})"
                         wire:key="si-{{ $c['id'] }}">
                        <div class="si-avatar">
                            @if(!empty($c['customer_avatar']))
                                <img src="{{ asset('storage/' . $c['customer_avatar']) }}" alt="">
                            @else
                                {{ strtoupper(substr($c['customer_name'] ?? '?', 0, 1)) }}
                            @endif
                        </div>
                        <div class="si-info">
                            <div class="si-name">{{ $c['customer_name'] }}</div>
                            <div class="si-preview">{{ \Illuminate\Support\Str::limit($c['last_message'], 40) ?: '—' }}</div>
                        </div>
                        <div class="si-meta">
                            @if($c['last_message_at'])
                                <div class="si-time">{{ \Carbon\Carbon::parse($c['last_message_at'])->diffForHumans(null, true) }}</div>
                            @endif
                            @if($c['unread_count'] > 0)
                                <span class="si-badge">{{ $c['unread_count'] }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="si-empty" style="padding:40px 20px;">
                        <i class="fa fa-inbox"></i>
                        <h4>No support conversations yet</h4>
                        <p>They'll appear here when customers message support.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: message area --}}
        <div class="si-main">
            @if($selectedConversationId && $selectedCustomer)
                <div class="si-head">
                    <div style="display:flex; align-items:center;">
                        <div class="si-avatar" style="width:36px; height:36px; margin-right:10px;">
                            @if($selectedCustomer->avatar)
                                <img src="{{ asset('storage/' . $selectedCustomer->avatar) }}" alt="">
                            @else
                                {{ strtoupper(substr($selectedCustomer->name ?? $selectedCustomer->email, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <div class="si-head-name">{{ $selectedCustomer->name ?? $selectedCustomer->email }}</div>
                            <div class="si-head-mail">{{ $selectedCustomer->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="si-msgs" id="siMsgs">
                    @foreach($conversationMessages as $m)
                        <div class="si-msg {{ $m['is_mine'] ? 'sent' : 'recv' }}" wire:key="sim-{{ $m['id'] }}-{{ $m['status'] }}">
                            <div class="si-bub">
                                @if($m['is_mine'] && !$m['is_me'])
                                    <span class="si-agent">{{ $m['sender_name'] }}</span>
                                @endif
                                @if(!empty($m['attachment_url']))
                                    @if($m['attachment_type'] === 'image')
                                        <img src="{{ $m['attachment_url'] }}" class="si-msg-image" onclick="siOpenLightbox('{{ $m['attachment_url'] }}')">
                                    @elseif($m['attachment_type'] === 'audio')
                                        <div class="si-msg-audio">
                                            <audio controls preload="metadata" src="{{ $m['attachment_url'] }}"
                                                   onloadedmetadata="siFixAudioDuration(this)"></audio>
                                        </div>
                                    @elseif($m['attachment_type'] === 'video')
                                        <video controls preload="metadata" src="{{ $m['attachment_url'] }}" style="max-width:260px; border-radius:8px;"></video>
                                    @else
                                        <a href="{{ $m['attachment_url'] }}" target="_blank" rel="noopener" class="si-msg-file" download="{{ $m['attachment_original_name'] }}">
                                            <i class="fa fa-file"></i>
                                            <div>
                                                <div class="fname">{{ $m['attachment_original_name'] ?? 'Attachment' }}</div>
                                                @if(!empty($m['attachment_size']))
                                                    <div class="fsize">{{ number_format($m['attachment_size'] / 1024, 1) }} KB</div>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @endif
                                @if(!empty($m['message']))
                                    <p>{{ $m['message'] }}</p>
                                @endif
                                <div class="si-btime">
                                    {{ \Carbon\Carbon::parse($m['created_at'])->format('h:i A') }}
                                    @if($m['is_mine'])
                                        <span class="si-ticks">
                                            @if($m['status'] === 'read')
                                                <span class="t read">✓✓</span>
                                            @elseif($m['status'] === 'delivered')
                                                <span class="t">✓✓</span>
                                            @else
                                                <span class="t">✓</span>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="si-att-preview" id="siAttachPreview">
                    <i class="fa fa-spinner fa-spin" style="font-size:16px; color: var(--sm-primary);"></i>
                    <div class="info" id="siAttachPreviewLabel">Uploading…</div>
                </div>

                <div class="si-input">
                    <form wire:submit.prevent="sendReply" style="display:flex; gap:8px; width:100%; align-items:center;">
                        <button type="button" class="si-att-btn" id="siAttachBtn" title="Attach file"><i class="fa fa-paperclip"></i></button>
                        <input type="file" id="siAttachInput"
                               accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt,video/*"
                               style="display:none">

                        <div class="si-rec-bar" id="siRecBar">
                            <span class="dot"></span>
                            Recording <span id="siRecTime">0:00</span>
                            <button type="button" class="cancel" id="siRecCancel">Cancel</button>
                        </div>

                        <textarea wire:model="reply" id="siReplyInput" placeholder="Type a reply…" rows="1"
                                  onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();this.form.requestSubmit();}"></textarea>

                        <button type="button" class="si-mic-btn" id="siMicBtn" title="Record voice"><i class="fa fa-microphone"></i></button>
                        <button type="submit"><i class="fa fa-paper-plane"></i></button>
                    </form>
                </div>
            @else
                <div class="si-empty">
                    <i class="fa fa-comments"></i>
                    <h4>Select a customer</h4>
                    <p>Pick a conversation from the left to start replying.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script>
window.__siState = window.__siState || { recorder: null, wired: false, recStartedAt: 0, recTicker: null };

document.addEventListener('DOMContentLoaded', () => { siInit(); siWireMedia(); });
document.addEventListener('livewire:navigated', () => { siInit(); siWireMedia(); });
document.addEventListener('livewire:initialized', () => {
    Livewire.on('message-received', () => siScrollBottom());
});

function siWireMedia() {
    if (window.__siState.wired) return;
    window.__siState.wired = true;

    document.addEventListener('click', (e) => {
        if (e.target.closest('#siAttachBtn')) {
            e.preventDefault();
            document.getElementById('siAttachInput')?.click();
            return;
        }
        if (e.target.closest('#siRecCancel')) {
            e.preventDefault();
            siCancelRecording();
            return;
        }
        if (e.target.closest('#siMicBtn')) {
            e.preventDefault();
            if (window.__siState.recorder) siStopRecording();
            else siStartRecording();
            return;
        }
    }, true);

    // File input change → direct POST (bypasses Livewire temp upload).
    document.addEventListener('change', (e) => {
        if (!e.target.matches('#siAttachInput')) return;
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        siUploadAttachment(file);
        e.target.value = '';
    }, true);

    // Send button while recording: stop and upload the voice note.
    document.addEventListener('click', (e) => {
        const sendBtn = e.target.closest('.si-input button[type="submit"]');
        if (!sendBtn) return;
        if (window.__siState.recorder) {
            e.preventDefault();
            e.stopPropagation();
            siStopRecording();
        }
    }, true);
}

async function siUploadAttachment(file) {
    const root = document.querySelector('[wire\\:id]');
    const comp = root && Livewire.find(root.getAttribute('wire:id'));
    const conversationId = comp?.$get?.('selectedConversationId') ?? comp?.selectedConversationId;
    if (!conversationId) { alert('Select a conversation first.'); return; }

    const preview = document.getElementById('siAttachPreview');
    const label = document.getElementById('siAttachPreviewLabel');
    if (preview) preview.classList.add('open');
    if (label) label.textContent = 'Uploading ' + file.name + '…';

    const fd = new FormData();
    fd.append('conversation_id', conversationId);
    fd.append('attachment', file);

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
        const res = await fetch('/chat/attachment-upload', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: fd,
        });
        if (!res.ok) {
            const body = await res.text();
            console.error('[si-attach] HTTP', res.status, body);
            alert('Upload failed (' + res.status + ').');
            return;
        }
        comp?.call('refreshInbox');
    } catch (err) {
        console.error('[si-attach] error', err);
        alert('Upload failed.');
    } finally {
        if (preview) preview.classList.remove('open');
    }
}

// See fixAudioDuration in chat.blade.php — same trick for the admin inbox.
window.siFixAudioDuration = function (audio) {
    if (!audio || audio.__durationFixed) return;
    audio.__durationFixed = true;
    if (audio.duration === Infinity || isNaN(audio.duration)) {
        const onChange = () => {
            if (audio.duration !== Infinity && !isNaN(audio.duration)) {
                audio.removeEventListener('durationchange', onChange);
                audio.currentTime = 0;
            }
        };
        audio.addEventListener('durationchange', onChange);
        try { audio.currentTime = 1e101; } catch (_) {}
    }
};

async function siStartRecording() {
    if (window.__siState.recorder) return;
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        let mime = 'audio/webm';
        if (!MediaRecorder.isTypeSupported(mime)) {
            mime = MediaRecorder.isTypeSupported('audio/mp4') ? 'audio/mp4' : '';
        }
        const rec = new MediaRecorder(stream, mime ? { mimeType: mime } : {});
        const chunks = [];
        rec.ondataavailable = (ev) => { if (ev.data && ev.data.size > 0) chunks.push(ev.data); };
        rec.onstop = () => {
            stream.getTracks().forEach(t => t.stop());
            const blob = new Blob(chunks, { type: rec.mimeType || mime || 'audio/webm' });
            const duration = Math.round((Date.now() - window.__siState.recStartedAt) / 1000);
            if (blob.size < 1024 || duration < 1) {
                console.warn('voice recording too short, skipping upload');
                alert('Recording too short.');
                return;
            }
            siUploadVoice(blob, duration);
        };
        rec.start();
        window.__siState.recorder = rec;
        window.__siState.recStartedAt = Date.now();
        document.getElementById('siMicBtn')?.classList.add('recording');
        document.getElementById('siRecBar')?.classList.add('active');
        document.getElementById('siReplyInput')?.setAttribute('disabled', 'disabled');
        window.__siState.recTicker = setInterval(() => {
            const s = Math.floor((Date.now() - window.__siState.recStartedAt) / 1000);
            const el = document.getElementById('siRecTime');
            if (el) el.textContent = `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;
            if (s >= 300) siStopRecording();
        }, 250);
    } catch (err) {
        alert('Microphone access denied or unavailable.');
        console.warn(err);
    }
}

function siStopRecording() {
    const rec = window.__siState.recorder;
    if (!rec) return;
    clearInterval(window.__siState.recTicker);
    window.__siState.recTicker = null;
    try { rec.stop(); } catch (_) {}
    window.__siState.recorder = null;
    document.getElementById('siMicBtn')?.classList.remove('recording');
    document.getElementById('siRecBar')?.classList.remove('active');
    document.getElementById('siReplyInput')?.removeAttribute('disabled');
}

function siCancelRecording() {
    const rec = window.__siState.recorder;
    if (!rec) return;
    clearInterval(window.__siState.recTicker);
    window.__siState.recTicker = null;
    rec.onstop = () => { try { rec.stream && rec.stream.getTracks?.().forEach(t => t.stop()); } catch (_) {} };
    try { rec.stop(); } catch (_) {}
    window.__siState.recorder = null;
    document.getElementById('siMicBtn')?.classList.remove('recording');
    document.getElementById('siRecBar')?.classList.remove('active');
    document.getElementById('siReplyInput')?.removeAttribute('disabled');
}

async function siUploadVoice(blob, durationSec) {
    // Read the currently-selected conversation id from the Livewire state
    // (the admin side doesn't stash it on the input, so query the wire root).
    const root = document.querySelector('[wire\\:id]');
    const comp = root && Livewire.find(root.getAttribute('wire:id'));
    const conversationId = comp?.$get?.('selectedConversationId') ?? comp?.selectedConversationId;
    if (!conversationId) {
        alert('Select a conversation first.');
        return;
    }

    const type = blob.type && blob.type.includes('/') ? blob.type : 'audio/webm';
    const ext  = type.includes('mp4') ? 'm4a' : 'webm';
    const file = new File([blob], `voice-${Date.now()}.${ext}`, { type });

    const fd = new FormData();
    fd.append('conversation_id', conversationId);
    fd.append('voice_note', file);
    fd.append('duration', String(Math.max(1, durationSec)));

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const res = await fetch('/chat/voice-upload', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: fd,
        });
        if (!res.ok) {
            const body = await res.text();
            console.error('[si-voice] upload HTTP', res.status, body);
            alert('Voice upload failed (' + res.status + ').');
            return;
        }
        comp?.call('refreshInbox');
    } catch (err) {
        console.error('[si-voice] upload error', err);
        alert('Voice upload failed. Check the console.');
    }
}

function siOpenLightbox(url) {
    let lb = document.getElementById('siLightbox');
    if (!lb) {
        lb = document.createElement('div');
        lb.id = 'siLightbox';
        lb.className = 'si-lightbox';
        lb.innerHTML = '<button class="close-btn">&times;</button><img alt="">';
        lb.addEventListener('click', (e) => {
            if (e.target === lb || e.target.classList.contains('close-btn')) lb.classList.remove('open');
        });
        document.body.appendChild(lb);
    }
    lb.querySelector('img').src = url;
    lb.classList.add('open');
}
window.siOpenLightbox = siOpenLightbox;

function siInit() {
    const indicator = document.getElementById('si-conn');
    const wait = setInterval(() => {
        if (!window.Echo) return;
        clearInterval(wait);

        const pusher = window.Echo.connector?.pusher;
        if (pusher) {
            const setOnline = () => indicator?.classList.replace('offline', 'online');
            const setOffline = () => indicator?.classList.replace('online', 'offline');
            pusher.connection.bind('connected', setOnline);
            pusher.connection.bind('disconnected', setOffline);
            if (pusher.connection.state === 'connected') setOnline();
        }

        try {
            window.Echo.private('support-inbox')
                .listen('.NewChatMessage', () => {
                    const root = document.querySelector('[wire\\:id]');
                    if (root) {
                        const id = root.getAttribute('wire:id');
                        Livewire.find(id).call('refreshInbox');
                        Livewire.find(id).$refresh();
                        siScrollBottom();
                    }
                });
        } catch (err) { console.warn('support-inbox subscribe failed', err); }
    }, 100);
    setTimeout(() => clearInterval(wait), 5000);
    siScrollBottom();
}

function siScrollBottom() {
    setTimeout(() => {
        const el = document.getElementById('siMsgs');
        if (el) el.scrollTop = el.scrollHeight;
    }, 80);
}
</script>
@endpush
</div>
