<div>
    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1"></script>
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
            background: transparent;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar-header {
            padding: 12px 12px 6px;
            background: transparent;
        }

        /* Old h4 title kept for BC — hidden now that filter chips + search
           carry the same information more compactly. */
        .chat-sidebar-header h4 {
            display: none;
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
            margin-bottom: 10px;
        }
        .chat-search .search-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #8696a0;
            pointer-events: none;
        }
        .chat-search input {
            width: 100%;
            padding: 9px 14px 9px 40px;
            background: #202c33;
            border: none;
            border-radius: 8px;
            color: #e9edef;
            font-size: 14px;
        }
        .chat-search input:focus {
            outline: none;
            background: #2a3942;
        }
        .chat-search input::placeholder {
            color: #8696a0;
        }

        /* WhatsApp-style filter chip row */
        .chat-filter-chips {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            scrollbar-width: none;
        }
        .chat-filter-chips::-webkit-scrollbar { display: none; }
        .chat-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            padding: 5px 13px;
            border-radius: 999px;
            background: #202c33;
            color: #d1d7db;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid transparent;
            transition: background .12s, color .12s, border-color .12s;
        }
        .chat-chip:hover {
            background: #2a3942;
            color: #e9edef;
        }
        .chat-chip.active {
            background: rgba(0, 168, 132, 0.18);
            color: #00d9a3;
            border-color: rgba(0, 217, 163, 0.35);
        }
        .chat-chip-badge {
            background: #00a884;
            color: #111b21;
            font-size: 11px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }
        .chat-chip-badge-red {
            background: #ef4444;
            color: #fff;
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
            background: #0D1011;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        /* Conversation List */
        .conversation-list {
            flex: 1;
            overflow-y: auto;
            background: #15191B;
            border: 1px solid #23292B;
            border-radius: 10px;
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
            background: #0D1011;
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

        /* Support pinned conversation */
        .conversation-item.pinned .conversation-avatar {
            background: linear-gradient(135deg, #C1F11D 0%, #7fbb00 100%);
            color: #000;
        }
        .conversation-item.pinned .conversation-name::after {
            content: '\f08d'; /* fa thumb-tack */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 10px;
            margin-left: 6px;
            color: #C1F11D;
            transform: rotate(30deg);
            display: inline-block;
        }
        .support-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            background: #C1F11D;
            color: #000;
            padding: 1px 6px;
            border-radius: 8px;
            margin-left: 6px;
            vertical-align: middle;
            letter-spacing: 0.4px;
        }

        /* Missed-call indicator on conversation rows */
        .missed-call-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(239,68,68,0.15);
            color: #ef4444;
            padding: 2px 7px;
            border-radius: 10px;
            margin-top: 3px;
        }
        .missed-call-badge i { font-size: 10px; }
        .conversation-item.has-missed .conversation-avatar {
            box-shadow: 0 0 0 2px #ef4444;
        }

        /* Typing indicator */
        .typing-indicator {
            padding: 6px 20px;
            font-size: 12px;
            color: #888;
            font-style: italic;
            min-height: 24px;
        }
        .typing-indicator .dots {
            display: inline-flex;
            gap: 3px;
            margin-left: 6px;
        }
        .typing-indicator .dots span {
            width: 5px; height: 5px;
            background: #C1F11D;
            border-radius: 50%;
            animation: typingDot 1.2s infinite ease-in-out;
        }
        .typing-indicator .dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator .dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingDot {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30%           { transform: translateY(-4px); opacity: 1; }
        }

        /* Call summary bubble — same subtle palette as normal messages,
           red accent reserved for missed calls only. */
        .message-bubble.call-bubble { padding: 0 !important; }
        /* The bubble has padding:0 to let .call-msg stretch edge-to-edge,
           but the time+tick row below still needs its own gutters — otherwise
           the tick sits on/past the border-radius corner. */
        .message-bubble.call-bubble .message-time {
            padding: 0 12px 6px;
            margin-top: 0;
        }
        .call-msg {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            min-width: 220px;
        }
        .call-msg .call-icon {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            color: #9ea6ab;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .call-msg.missed .call-icon { background: rgba(239,68,68,0.15); color: #ef4444; }
        .call-msg .call-body { flex: 1; min-width: 0; }
        .call-msg .call-label { font-weight: 500; font-size: 14px; line-height: 1.2; color: #e9edef; }
        .call-msg.missed .call-label { color: #f87171; }
        .call-msg .call-sub { font-size: 11.5px; color: #9ea6ab; margin-top: 2px; }
        .call-msg .call-back-btn {
            background: transparent;
            border: none;
            color: #9ea6ab;
            font-size: 14px;
            width: 32px; height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: color .15s, background .15s;
        }
        .call-msg .call-back-btn:hover {
            color: #e9edef;
            background: rgba(255,255,255,0.06);
        }

        /* Attachments in messages */
        .msg-image {
            display: block;
            max-width: 280px;
            max-height: 320px;
            border-radius: 10px;
            margin: 4px 0;
            cursor: pointer;
        }
        .msg-audio {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 220px;
        }
        .msg-audio audio {
            width: 200px;
            height: 34px;
        }
        .msg-audio .audio-duration {
            font-size: 11px;
            opacity: 0.7;
        }
        .msg-file {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            background: rgba(0,0,0,0.15);
            border-radius: 8px;
            color: inherit;
            text-decoration: none;
            min-width: 200px;
        }
        .message-wrapper.received .msg-file { background: rgba(255,255,255,0.05); }
        .msg-file i { font-size: 22px; }
        .msg-file .file-name { font-weight: 600; font-size: 13px; word-break: break-all; }
        .msg-file .file-size { font-size: 11px; opacity: 0.7; }

        /* Image lightbox */
        .chat-lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100000;
            padding: 20px;
        }
        .chat-lightbox.open { display: flex; }
        .chat-lightbox img { max-width: 100%; max-height: 100%; border-radius: 4px; }
        .chat-lightbox .lb-close {
            position: absolute;
            top: 20px;
            right: 24px;
            background: none;
            border: none;
            color: #fff;
            font-size: 32px;
            cursor: pointer;
        }

        /* Attachment / voice buttons + recording UI */
        .chat-input-form .attach-btn,
        .chat-input-form .mic-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #C1F11D;
            cursor: pointer;
            font-size: 18px;
        }
        .chat-input-form .attach-btn:hover,
        .chat-input-form .mic-btn:hover { background: rgba(193,241,29,0.1); }
        .chat-input-form .mic-btn.recording {
            background: #ff5757;
            color: #fff;
            animation: recPulse 1s ease-in-out infinite;
        }
        @keyframes recPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(255,87,87,0.7); }
            50%     { box-shadow: 0 0 0 10px rgba(255,87,87,0); }
        }
        .voice-recording-bar {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 0 12px;
            color: #ff5757;
            font-size: 13px;
            font-weight: 600;
        }
        .voice-recording-bar.active { display: flex; flex: 1; }
        .voice-recording-bar .rec-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ff5757;
            animation: recPulse 1s ease-in-out infinite;
        }
        .voice-recording-bar .rec-cancel {
            margin-left: auto;
            background: transparent;
            border: 1px solid #ff5757;
            color: #ff5757;
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        /* Selected attachment preview above input */
        .attachment-preview {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 8px 14px;
            background: #1a1a1a;
            border-top: 1px solid #333;
        }
        .attachment-preview.open { display: flex; }
        .attachment-preview img.thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
        }
        .attachment-preview .att-info { flex: 1; color: #ddd; font-size: 13px; }
        .attachment-preview .att-info small { color: #888; display: block; font-size: 11px; }
        .attachment-preview button {
            background: transparent;
            border: none;
            color: #ff5757;
            font-size: 20px;
            cursor: pointer;
        }

        /* Emoji picker */
        .emoji-picker-wrapper {
            position: absolute;
            bottom: 70px;
            left: 20px;
            z-index: 1000;
            display: none;
        }
        .emoji-picker-wrapper.open { display: block; }
        .chat-input-form .emoji-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #C1F11D;
            cursor: pointer;
            font-size: 20px;
        }
        .chat-input-form .emoji-btn:hover { background: rgba(193,241,29,0.1); }
        emoji-picker {
            --background: #1a1a1a;
            --border-color: #333;
            --input-border-color: #444;
            --category-emoji-padding: 6px;
        }

        /* Right Panel - Chat Area — flat WA-style palette, minimal border. */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #0b141a;
            border: 1px solid #1f2c34;
            border-radius: 10px;
            overflow: hidden;
            position: relative;   /* anchor for the drop-zone overlay */
        }

        /* Drag-and-drop overlay — covers the chat main area only while
           the user is dragging files over it. Hidden by default. */
        .chat-dropzone {
            position: absolute;
            inset: 0;
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(11, 20, 26, 0.85);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            border: 2px dashed #005c4b;
            border-radius: 10px;
            pointer-events: none; /* only visual — events handled at document level */
        }
        .chat-dropzone.active { display: flex; }
        .chat-dropzone-inner { text-align: center; color: #e9edef; }
        .chat-dropzone-inner i {
            font-size: 42px;
            color: #C1F11D;
            margin-bottom: 12px;
            display: block;
        }
        .chat-dropzone-title { font-size: 17px; font-weight: 600; margin-bottom: 4px; }
        .chat-dropzone-sub { font-size: 12.5px; color: #8696a0; }

        .chat-header {
            padding: 12px 18px;
            background: #202c33;      /* slightly lighter than the messages area for clear separation */
            border-bottom: none;
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

        /* Chat Messages — subtle darker background so bubbles float above it. */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 4px;                 /* WA-style tight vertical rhythm */
            background: #0b141a;      /* deep near-black w/ blue-green tint */
        }
        /* First message in a run gets a bit of top gap. */
        .chat-messages .message-wrapper + .message-wrapper { margin-top: 2px; }

        /* WhatsApp-style day separator pill. Injected once per calendar day
           by the Blade loop above. */
        .chat-date-sep {
            display: flex;
            justify-content: center;
            margin: 14px 0 8px;
        }
        .chat-date-sep span {
            background: #1d2c33;
            color: #cfd6d9;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 8px;
            letter-spacing: 0.2px;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }

        /* Image / video bubble tightens padding so the bubble hugs the media
           tightly (no wide green frame around it). Text-only bubbles are
           unaffected. */
        .message-bubble.media-only { padding: 3px 3px 4px; }
        .message-bubble.media-only .msg-image,
        .message-bubble.media-only video {
            display: block;
            margin: 0;
            border-radius: 6px;
        }
        .message-bubble.media-only .message-time {
            padding: 2px 6px 0;
            margin-top: 2px;
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

        /* Cleaner, WhatsApp-Web-inspired message bubbles.
           Dark subtle tones on both sides, no heavy borders, softer radii. */
        .message-bubble {
            max-width: 65%;
            padding: 8px 12px 6px;
            border-radius: 8px;
            position: relative;
            font-size: 14.5px;
            line-height: 1.4;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }

        .message-wrapper.sent .message-bubble {
            background: #054d3c;      /* WA-style deep teal-green */
            color: #e9edef;
            border-top-right-radius: 3px;
        }

        .message-wrapper.received .message-bubble {
            background: #1f2c34;      /* WA-style neutral dark */
            color: #e9edef;
            border-top-left-radius: 3px;
            border: none;
        }

        .message-text {
            margin: 0;
            word-wrap: break-word;
            line-height: 1.4;
            font-size: 14px;
            color: inherit;
        }

        .message-time {
            font-size: 10.5px;
            margin-top: 3px;
            opacity: 0.65;
            display: flex;
            align-items: center;
            gap: 4px;
            justify-content: flex-end;
            color: #9ea6ab;
        }

        .message-wrapper.received .message-time {
            justify-content: flex-start;
        }

        /* WhatsApp-style tick marks — small, subtle, blue-ish when read. */
        .message-ticks {
            display: inline-flex;
            margin-left: 2px;
            font-size: 11px;
            line-height: 1;
        }
        .message-ticks .tick { color: #9ea6ab; }
        .message-ticks .tick.read { color: #53bdeb; }       /* WA "seen" blue */
        .message-ticks .double-tick { letter-spacing: -3px; }

        /* Chat Input — matches the header tone; subtle top border only. */
        .chat-input {
            padding: 10px 16px;
            background: #202c33;
            border-top: none;
        }

        .chat-input-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-input-form input {
            flex: 1;
            padding: 10px 16px;
            background: #2a3942;
            border: none;
            border-radius: 8px;
            color: #e9edef;
            font-size: 14px;
        }
        .chat-input-form input:focus {
            outline: none;
            background: #2f414c;
        }
        .chat-input-form input::placeholder {
            color: #8696a0;
        }

        .chat-input-form button[type="submit"] {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #8696a0;
            cursor: pointer;
            font-size: 17px;
            transition: color .15s, background .15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chat-input-form button[type="submit"]:hover {
            background: rgba(255,255,255,0.06);
            color: #e9edef;
        }
        .chat-input-form button[type="submit"]:disabled {
            background: transparent;
            color: #4b5b62;
            cursor: default;
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
                /* Sit on top of the centered title layer so a long name
                   can't render over the Back tap-target. */
                z-index: 2 !important;
            }
            .chat-header-avatar { display: none !important; }
            /* Reserve horizontal padding on the title wrapper equal to
               the back button's width on the left (and a symmetric gap
               on the right so the title stays visually centered) so the
               name/subtitle can never overflow into the Back button. */
            .chat-header-info > div {
                text-align: center !important;
                width: 100% !important;
                padding: 0 56px !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
            }
            .chat-header-name {
                font-size: 15px !important;
                font-weight: 600 !important;
                color: #fff !important;
                /* Single-line ellipsis so long names get truncated inside
                   the reserved space instead of wrapping and pushing the
                   subtitle off the header row. */
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                max-width: 100% !important;
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

            /* Messages area. Top padding clears the fixed .chat-header
               (14px top + max(14, safe-area) padding + ~40px content +
               1px border), bottom padding clears the pinned .chat-input
               (~60px) so the first and last bubbles are always fully
               visible. Padding shorthand set explicitly here so it doesn't
               wipe out earlier padding-top/padding-bottom overrides. */
            .chat-messages {
                background: #000 !important;
                padding: calc(50px + env(safe-area-inset-top)) 16px 76px !important;
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

            /* Chat input — pinned to the viewport bottom (above the fixed
               .ev-mobile-bottom-nav which is ~66px tall incl. safe-area) so
               it stays visible regardless of how many messages are in the
               list. In the previous flex-flow layout the input sat at the
               bottom of .chat-container, and any mismatch between the
               container's `calc(100vh - 60px)` height and the real navbar
               height meant the input rendered below the navbar (fully
               hidden until you page-scrolled). */
            .chat-input {
                position: fixed !important;
                left: 0 !important;
                right: 0 !important;
                bottom: calc(66px + env(safe-area-inset-bottom)) !important;
                z-index: 99 !important;
                padding: 10px 16px !important;
                background: #0a0a0a !important;
                border-top: 1px solid #1a1a1a !important;
            }
            /* Reserve room at the bottom of the scrollable messages so the
               last bubble isn't hidden behind the pinned input. Input is
               ~60px tall (10 padding + 40 button + 10 padding). */
            .chat-messages {
                padding-bottom: 76px !important;
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

    <div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">
                {{-- Pass `active` explicitly. Route-based detection inside
                     the nav can't run during Livewire updates (which go
                     through livewire.update, not user.chat), so an
                     explicit key keeps the Messages tab highlighted. --}}
                @include('components.communication-nav', ['active' => 'messages'])

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

                    {{-- WhatsApp-style Chat Container. Real-time updates come
                         from Reverb (see initEchoListener below) so no polling. --}}
                    <div class="chat-container">
                        {{-- Left Sidebar - Conversations --}}
                        <div class="chat-sidebar {{ $selectedConversationId ? 'mobile-hidden' : '' }}" id="chatSidebar">
                            <div class="chat-sidebar-header">
                                @php
                                    // Inline counts so we can badge the Messages + Calls chips
                                    // without pulling the full communication-nav include.
                                    $chatUnreadCount = auth()->check()
                                        ? \App\Models\Message::whereHas('conversation', function($q) {
                                                $q->where('user_one_id', auth()->id())->orWhere('user_two_id', auth()->id());
                                            })
                                            ->where('sender_id', '!=', auth()->id())
                                            ->where(function($q) {
                                                $q->whereNull('status')->orWhere('status', 'unread');
                                            })->count()
                                        : 0;
                                    $missedCallCount = auth()->check()
                                        ? \App\Models\Call::unseenMissedFor(auth()->id())->count()
                                        : 0;
                                @endphp

                                {{-- WhatsApp-style search: rounded pill with a search icon inside --}}
                                <div class="chat-search">
                                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <input
                                        type="text"
                                        placeholder="Search or start a new chat"
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

                                {{-- Chat-scoped filter chips — only Messages and
                                     Calls, since the rest of the communication
                                     categories (Questions / Reviews / Favorites)
                                     live in the outer communication-nav and
                                     aren't chat-related. --}}
                                <div class="chat-filter-chips">
                                    <a href="{{ route('user.chat') }}" class="chat-chip active">
                                        Messages
                                        @if($chatUnreadCount > 0)
                                            <span class="chat-chip-badge">{{ $chatUnreadCount }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('user.calls') }}" class="chat-chip">
                                        Calls
                                        @if($missedCallCount > 0)
                                            <span class="chat-chip-badge chat-chip-badge-red">{{ $missedCallCount }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                            
                            <div class="conversation-list">
                                @forelse($conversations as $conversation)
                                    <div
                                        class="conversation-item {{ $selectedConversationId == $conversation['id'] ? 'active' : '' }} {{ $conversation['unread_count'] > 0 ? 'unread' : '' }} {{ !empty($conversation['is_pinned']) ? 'pinned' : '' }} {{ !empty($conversation['missed_calls']) ? 'has-missed' : '' }}"
                                        wire:click="selectConversation({{ $conversation['id'] }})"
                                        wire:key="conv-{{ $conversation['id'] }}"
                                    >
                                        <div class="conversation-avatar">
                                            @if(!empty($conversation['is_support']))
                                                <i class="fa fa-headset" aria-hidden="true"></i>
                                            @elseif(!empty($conversation['other_user_avatar']))
                                                <img src="{{ asset('storage/' . $conversation['other_user_avatar']) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($conversation['other_user_name'] ?? '?', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">
                                                {{ $conversation['other_user_name'] ?? 'Unknown' }}
                                                @if(!empty($conversation['is_support']))
                                                    <span class="support-badge">SUPPORT</span>
                                                @endif
                                            </div>
                                            <div class="conversation-preview">
                                                @if(empty($conversation['last_message']) && !empty($conversation['is_support']))
                                                    Send us a message anytime
                                                @else
                                                    {{ Str::limit($conversation['last_message'], 30) ?: 'No messages yet' }}
                                                @endif
                                            </div>
                                            @if(!empty($conversation['missed_calls']))
                                                <div class="missed-call-badge">
                                                    <i class="fa fa-phone-slash"></i>
                                                    {{ $conversation['missed_calls'] }} missed {{ $conversation['missed_calls'] === 1 ? 'call' : 'calls' }}
                                                </div>
                                            @endif
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
                        <div class="chat-main" id="chatMain">
                            {{-- Drag-and-drop overlay. Toggled by JS when files are dragged
                                 over the chat area — kept OUT of the Livewire wire:if branch
                                 so its element identity is stable across re-renders. --}}
                            <div class="chat-dropzone" id="chatDropzone">
                                <div class="chat-dropzone-inner">
                                    <i class="fa fa-cloud-upload-alt"></i>
                                    <div class="chat-dropzone-title">Drop to send</div>
                                    <div class="chat-dropzone-sub">Images, videos, PDFs, docs — up to 20 MB</div>
                                </div>
                            </div>
                            @if($selectedConversationId && ($selectedUser || $selectedIsSupport))
                                {{-- Chat Header --}}
                                <div class="chat-header">
                                    <div class="chat-header-info">
                                        <button class="mobile-back-btn" wire:click="closeConversation">
                                            <i class="fa fa-angle-left"></i> Back
                                        </button>
                                        <div class="chat-header-avatar">
                                            @if($selectedIsSupport)
                                                <i class="fa fa-headset" aria-hidden="true"></i>
                                            @elseif($selectedUser && $selectedUser->profile_pic)
                                                <img src="{{ asset('storage/' . $selectedUser->profile_pic) }}" alt="">
                                            @elseif($selectedUser)
                                                {{ strtoupper(substr($selectedUser->name ?? $selectedUser->email, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="chat-header-name">
                                                @if($selectedIsSupport)
                                                    Support <span class="support-badge">HELP</span>
                                                @else
                                                    {{ $selectedUser->name ?? $selectedUser->email }}
                                                @endif
                                            </div>
                                            <div class="chat-header-email">
                                                @if($selectedIsSupport)
                                                    We usually reply within an hour
                                                @else
                                                    {{ $selectedUser->email }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chat-header-actions">
                                        @if(!$selectedIsSupport && $selectedUser)
                                            <div class="chat-call-buttons">
                                                <button type="button" title="Voice call"
                                                        data-rtc-call="audio"
                                                        data-peer-id="{{ $selectedUser->id }}"
                                                        data-peer-name="{{ $selectedUser->name ?? $selectedUser->email }}"
                                                        data-peer-avatar="{{ $selectedUser->profile_pic ? asset('storage/' . $selectedUser->profile_pic) : '' }}"
                                                        data-conversation-id="{{ $selectedConversationId }}">
                                                    <i class="fa fa-phone"></i>
                                                </button>
                                                <button type="button" title="Video call"
                                                        data-rtc-call="video"
                                                        data-peer-id="{{ $selectedUser->id }}"
                                                        data-peer-name="{{ $selectedUser->name ?? $selectedUser->email }}"
                                                        data-peer-avatar="{{ $selectedUser->profile_pic ? asset('storage/' . $selectedUser->profile_pic) : '' }}"
                                                        data-conversation-id="{{ $selectedConversationId }}">
                                                    <i class="fa fa-video"></i>
                                                </button>
                                            </div>
                                            <button wire:click="deleteConversation" onclick="return confirm('Delete this conversation?')" title="Delete conversation">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
 
                                {{-- Chat Messages --}}
                                <div class="chat-messages" id="chatMessages">
                                    @php
                                        // Track the last-rendered date so we can inject WhatsApp-style
                                        // day separators ("Today", "Yesterday", "Monday", or full date).
                                        $lastRenderedDate = null;
                                        $renderDateLabel = function ($date) {
                                            $d = \Carbon\Carbon::parse($date)->startOfDay();
                                            $today = \Carbon\Carbon::today();
                                            if ($d->equalTo($today)) return 'Today';
                                            if ($d->equalTo($today->copy()->subDay())) return 'Yesterday';
                                            if ($d->gt($today->copy()->subDays(7))) return $d->format('l');   // day name
                                            if ($d->year === $today->year) return $d->format('j F');           // e.g. "12 September"
                                            return $d->format('j F Y');
                                        };
                                    @endphp
                                    @foreach($this->conversationMessages as $message)
                                        @php
                                            $msgDate = \Carbon\Carbon::parse($message['created_at'])->format('Y-m-d');
                                            $showSeparator = $msgDate !== $lastRenderedDate;
                                            $lastRenderedDate = $msgDate;
                                            $attType = $message['attachment_type'] ?? '';
                                            $hasText = !empty(trim((string)($message['message'] ?? '')));
                                            $isMediaOnly = in_array($attType, ['image', 'video'], true) && !$hasText;
                                        @endphp
                                        @if($showSeparator)
                                            <div class="chat-date-sep" wire:key="sep-{{ $msgDate }}">
                                                <span>{{ $renderDateLabel($message['created_at']) }}</span>
                                            </div>
                                        @endif
                                        <div class="message-wrapper {{ $message['is_mine'] ? 'sent' : 'received' }}" wire:key="msg-{{ $message['id'] }}-{{ $message['status'] }}">
                                            <div class="message-bubble {{ $attType === 'call' ? 'call-bubble' : '' }} {{ $isMediaOnly ? 'media-only' : '' }}">
                                                @if(($message['attachment_type'] ?? '') === 'call')
                                                    @php
                                                        $callKind   = $message['attachment_mime'] ?? 'audio';       // 'audio' | 'video'
                                                        $callStatus = $message['attachment_original_name'] ?? 'ended';
                                                        $isMissed   = $callStatus === 'missed' && !$message['is_mine'];
                                                        $iconClass  = $callKind === 'video' ? 'fa-video' : 'fa-phone';
                                                        $arrowClass = $message['is_mine'] ? 'fa-arrow-up-right-from-square' : ($isMissed ? 'fa-phone-slash' : 'fa-arrow-down-left');
                                                        $label = match ($callStatus) {
                                                            'missed'   => 'Missed ' . $callKind . ' call',
                                                            'declined' => ($message['is_mine'] ? 'No answer' : 'Call declined'),
                                                            'ended'    => ucfirst($callKind) . ' call',
                                                            default    => ucfirst($callKind) . ' call',
                                                        };
                                                    @endphp
                                                    <div class="call-msg {{ $isMissed ? 'missed' : '' }}">
                                                        <span class="call-icon"><i class="fa {{ $iconClass }}"></i></span>
                                                        <div class="call-body">
                                                            <div class="call-label">{{ $label }}</div>
                                                            <div class="call-sub">
                                                                {{ $message['is_mine'] ? 'Outgoing' : 'Incoming' }}
                                                                @if(!empty($message['attachment_duration']))
                                                                    · {{ gmdate('i:s', $message['attachment_duration']) }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if($selectedUser && !$selectedIsSupport)
                                                            <button type="button" class="call-back-btn" title="Call back"
                                                                    data-rtc-call="{{ $callKind }}"
                                                                    data-peer-id="{{ $selectedUser->id }}"
                                                                    data-peer-name="{{ $selectedUser->name ?? $selectedUser->email }}"
                                                                    data-peer-avatar="{{ $selectedUser->profile_pic ? asset('storage/' . $selectedUser->profile_pic) : '' }}"
                                                                    data-conversation-id="{{ $selectedConversationId }}">
                                                                <i class="fa {{ $iconClass }}"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @elseif(!empty($message['attachment_url']))
                                                    @if($message['attachment_type'] === 'image')
                                                        <img src="{{ $message['attachment_url'] }}"
                                                             alt="Image"
                                                             class="msg-image"
                                                             onclick="openLightbox('{{ $message['attachment_url'] }}')">
                                                    @elseif($message['attachment_type'] === 'audio')
                                                        <div class="msg-audio">
                                                            {{-- WebM blobs from MediaRecorder ship without a duration header,
                                                                 so Chrome reports duration=Infinity on load. onloadedmetadata
                                                                 seeks to +∞ which forces the browser to scan the file, then
                                                                 rewinds. --}}
                                                            <audio controls preload="metadata" src="{{ $message['attachment_url'] }}"
                                                                   onloadedmetadata="fixAudioDuration(this)"></audio>
                                                            @if(!empty($message['attachment_duration']))
                                                                <span class="audio-duration">{{ gmdate('i:s', $message['attachment_duration']) }}</span>
                                                            @endif
                                                        </div>
                                                    @elseif($message['attachment_type'] === 'video')
                                                        <video controls preload="metadata" src="{{ $message['attachment_url'] }}" style="max-width:280px; border-radius:10px;"></video>
                                                    @else
                                                        <a href="{{ $message['attachment_url'] }}" target="_blank" rel="noopener" class="msg-file" download="{{ $message['attachment_original_name'] }}">
                                                            <i class="fa fa-file"></i>
                                                            <div>
                                                                <div class="file-name">{{ $message['attachment_original_name'] ?? 'Attachment' }}</div>
                                                                @if(!empty($message['attachment_size']))
                                                                    <div class="file-size">{{ number_format($message['attachment_size'] / 1024, 1) }} KB</div>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    @endif
                                                @endif
                                                @if(!empty($message['message']))
                                                    <p class="message-text">{{ $message['message'] }}</p>
                                                @endif
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

                                {{-- Typing indicator --}}
                                <div id="typingIndicator" class="typing-indicator" style="display:none;">
                                    <span id="typingName">Someone</span> is typing
                                    <span class="dots"><span></span><span></span><span></span></span>
                                </div>

                                {{-- Upload progress banner (shown by JS while a file is uploading via /chat/attachment-upload) --}}
                                <div class="attachment-preview" id="attachmentPreview">
                                    <i class="fa fa-spinner fa-spin" style="font-size:20px; color:#C1F11D;"></i>
                                    <div class="att-info" id="attachmentPreviewLabel">Uploading…</div>
                                </div>

                                {{-- Chat Input --}}
                                <div class="chat-input" style="position: relative;">
                                    <div class="emoji-picker-wrapper" id="emojiPickerWrapper">
                                        <emoji-picker id="emojiPicker"></emoji-picker>
                                    </div>
                                    <form wire:submit.prevent="sendReply" class="chat-input-form">
                                        <button type="button" class="emoji-btn" id="emojiToggleBtn" title="Emoji" aria-label="Emoji picker">
                                            <i class="far fa-smile"></i>
                                        </button>

                                        {{-- File attach — POSTs directly to /chat/attachment-upload,
                                             not through Livewire's wire:model, because Livewire's
                                             temp-upload also hits the putFileAs bug on this host. --}}
                                        <button type="button" class="attach-btn" id="attachBtn" title="Attach file" aria-label="Attach file">
                                            <i class="fa fa-paperclip"></i>
                                        </button>
                                        <input type="file" id="attachInput"
                                               accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt,video/*"
                                               style="display:none">

                                        {{-- Recording indicator (replaces input while recording) --}}
                                        <div class="voice-recording-bar" id="voiceRecordingBar">
                                            <span class="rec-dot"></span>
                                            Recording <span id="voiceRecTime">0:00</span>
                                            <button type="button" class="rec-cancel" id="voiceCancelBtn">Cancel</button>
                                        </div>

                                        <input
                                            type="text"
                                            wire:model="reply"
                                            id="chatReplyInput"
                                            placeholder="Type a message..."
                                            autocomplete="off"
                                            data-conversation-id="{{ $selectedConversationId }}"
                                            data-other-user-id="{{ optional($selectedUser)->id }}"
                                            data-is-support="{{ $selectedIsSupport ? '1' : '0' }}">

                                        {{-- Voice recorder (tap to start, tap again to stop & send) --}}
                                        <button type="button" class="mic-btn" id="micBtn" title="Tap to record voice message" aria-label="Record voice">
                                            <i class="fa fa-microphone"></i>
                                        </button>

                                        <button type="submit" title="Send">
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
        // Track state at module scope so Livewire DOM swaps don't reset it.
        window.__chatState = window.__chatState || {
            typingTimeout: null,
            currentChannel: null,
            typingHideTimeout: null,
            emojiWired: false,
        };

        document.addEventListener('DOMContentLoaded', () => {
            initEchoListener();
            wireEmojiPicker();
            wireTypingWhispers();
            wireAttachAndVoice();
            wireDragDrop();
            scrollToBottom();
        });

        document.addEventListener('livewire:navigated', () => {
            initEchoListener();
            wireEmojiPicker();
            wireTypingWhispers();
            wireAttachAndVoice();
            wireDragDrop();
        });

        // Re-scroll and re-wire on Livewire updates (message send re-renders the input).
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', () => {
                wireEmojiPicker();
            });
        });

        // Emoji picker: use event delegation so it survives Livewire DOM morphs.
        function wireEmojiPicker() {
            const wrapper = document.getElementById('emojiPickerWrapper');
            const picker = document.getElementById('emojiPicker');
            if (!picker || window.__chatState.emojiWired) return;
            window.__chatState.emojiWired = true;

            document.addEventListener('click', (e) => {
                const toggle = e.target.closest('#emojiToggleBtn');
                if (toggle) {
                    e.preventDefault();
                    wrapper?.classList.toggle('open');
                    return;
                }
                // Close when clicking outside
                if (!e.target.closest('.emoji-picker-wrapper') && !e.target.closest('#emojiToggleBtn')) {
                    wrapper?.classList.remove('open');
                }
            });

            picker.addEventListener('emoji-click', (event) => {
                const input = document.getElementById('chatReplyInput');
                if (!input) return;
                const emoji = event.detail.unicode;
                const start = input.selectionStart ?? input.value.length;
                const end = input.selectionEnd ?? input.value.length;
                input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
                input.setSelectionRange(start + emoji.length, start + emoji.length);
                input.focus();
                // Sync into Livewire's `reply` property.
                const root = input.closest('[wire\\:id]');
                if (root) {
                    Livewire.find(root.getAttribute('wire:id')).set('reply', input.value, false);
                }
            });
        }

        // Typing indicator via Echo whispers. Delegated to survive DOM morphs.
        function wireTypingWhispers() {
            document.addEventListener('input', (e) => {
                const input = e.target.closest('#chatReplyInput');
                if (!input) return;
                sendTypingWhisper(input);
            });
        }

        function sendTypingWhisper(input) {
            if (!window.Echo) return;
            const otherUserId = input.getAttribute('data-other-user-id');
            const isSupport = input.getAttribute('data-is-support') === '1';
            const myName = @json(auth()->user()->name ?? auth()->user()->email ?? 'Someone');

            let channelName = null;
            if (isSupport) {
                channelName = 'support-inbox';
            } else if (otherUserId) {
                channelName = `chat.${otherUserId}`;
            }
            if (!channelName) return;

            // Throttle: only whisper once per 1.5s while continuously typing.
            if (window.__chatState.typingTimeout) return;
            try {
                window.Echo.private(channelName).whisper('typing', {
                    userId: {{ auth()->id() }},
                    name: myName,
                    conversationId: input.getAttribute('data-conversation-id'),
                });
            } catch (err) { /* channel not subscribed yet */ }
            window.__chatState.typingTimeout = setTimeout(() => {
                window.__chatState.typingTimeout = null;
            }, 1500);
        }

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
                        
                        const chatChannel = window.Echo.private(`chat.${userId}`);

                        // Debug — log every event that arrives on our channel.
                        // Helps spot missing-broadcast vs listener-mismatch bugs.
                        if (chatChannel.subscription) {
                            chatChannel.subscription.bind_global((eventName, data) => {
                                if (eventName && !eventName.startsWith('pusher:')) {
                                    console.log('[rtc] channel event', eventName, data);
                                }
                            });
                        }

                        chatChannel
                            .listen('.NewChatMessage', (e) => {
                                const chatComponent = document.querySelector('[wire\\:id]');
                                if (chatComponent) {
                                    const wireId = chatComponent.getAttribute('wire:id');
                                    Livewire.find(wireId).call('refreshChat');
                                    Livewire.find(wireId).$refresh();
                                    scrollToBottom();
                                }
                            })
                            .listen('.MessageStatusUpdated', (e) => {
                                const chatComponent = document.querySelector('[wire\\:id]');
                                if (chatComponent) {
                                    const wireId = chatComponent.getAttribute('wire:id');
                                    Livewire.find(wireId).call('loadConversationMessages');
                                }
                            })
                            .listen('.CallSignal', (e) => {
                                console.log('[rtc] CallSignal received', e);
                                if (typeof window.rtcHandleSignal === 'function') {
                                    window.rtcHandleSignal(e);
                                }
                            })
                            // Fallback: some Echo/Reverb version mismatches
                            // deliver events with the fully-qualified class
                            // name instead of broadcastAs(). Try both.
                            .listen('CallSignal', (e) => {
                                console.log('[rtc] CallSignal received (unprefixed)', e);
                                if (typeof window.rtcHandleSignal === 'function') {
                                    window.rtcHandleSignal(e);
                                }
                            })
                            .listen('.App\\Events\\CallSignal', (e) => {
                                console.log('[rtc] CallSignal received (FQCN)', e);
                                if (typeof window.rtcHandleSignal === 'function') {
                                    window.rtcHandleSignal(e);
                                }
                            })
                            .listenForWhisper('typing', (e) => {
                                showTypingIndicator(e);
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

        // Lightweight status refresh — every 3 seconds while a conversation
        // is open, ask the server for the latest message states AND mark
        // any new incoming messages as read. This is a fallback so
        // sidebar badges clear + ticks always update even if the Reverb
        // broadcast is late/dropped. pollRefresh (unlike refreshChat) does
        // NOT dispatch 'message-received', so it never fights the user's
        // scroll position when they're reading history.
        // Sidebar-badge + tick-mark refresh. Uses refreshChat (which has
        // existed since day 1) instead of a newer method — opcache on this
        // Windows install stubbornly refuses to pick up newly-added Chat.php
        // methods without a PHP restart, and a MethodNotFoundException from
        // Livewire blanks the whole component.
        if (!window.__chatState.tickPoll) {
            window.__chatState.tickPoll = setInterval(() => {
                if (document.hidden) return;
                const root = document.querySelector('[wire\\:id]');
                if (!root) return;
                const comp = Livewire.find(root.getAttribute('wire:id'));
                if (!comp) return;
                try {
                    Promise.resolve(comp.call('refreshChat')).catch((err) => {
                        console.warn('[chat] refreshChat failed:', err?.message || err);
                    });
                } catch (err) {
                    console.warn('[chat] refreshChat threw:', err);
                }
            }, 3000);
        }

        // Auto-scroll to the bottom of the message list on new messages, BUT
        // only if the user was already near the bottom. If they've scrolled
        // up to read older history we leave them alone — the 3-second
        // background refreshChat would otherwise keep yanking them back
        // to the newest message, making it impossible to read history.
        function scrollToBottom(force = false) {
            setTimeout(() => {
                const chatMessages = document.getElementById('chatMessages');
                if (!chatMessages) return;
                const distanceFromBottom = chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight;
                if (force || distanceFromBottom < 160) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        }

        // Drag-and-drop file upload. Listeners bound at document level so they
        // survive Livewire's re-renders. Dropzone is only revealed when the
        // drag is happening over #chatMain AND a conversation is open.
        function wireDragDrop() {
            if (window.__chatState.dropWired) return;
            window.__chatState.dropWired = true;

            let dragDepth = 0;

            // Chrome reports 'Files' in dataTransfer.types during dragenter
            // and drop, but for security reasons some browsers omit it
            // during dragover. Only enforce the check on dragenter/drop; on
            // dragover, always preventDefault when we're over the chat area
            // so the browser knows we accept the drop.
            const looksLikeFileDrag = (e) => {
                const types = e.dataTransfer?.types;
                if (!types) return false;
                for (const t of types) { if (t === 'Files') return true; }
                return false;
            };
            const chatMain = () => document.getElementById('chatMain');
            const inChat = (target) => target && chatMain()?.contains(target);
            const dropzone = () => document.getElementById('chatDropzone');
            const conversationOpen = () => !!document.getElementById('chatReplyInput');

            // Suppress the browser's default drop behavior (opening the file)
            // page-wide, otherwise if the user misses the target the file
            // opens in a new tab. Cheap and harmless.
            document.addEventListener('dragover', (e) => {
                if (!looksLikeFileDrag(e)) return;
                e.preventDefault();
                if (inChat(e.target)) {
                    if (e.dataTransfer) e.dataTransfer.dropEffect = 'copy';
                }
            }, false);

            document.addEventListener('dragenter', (e) => {
                if (!looksLikeFileDrag(e)) return;
                if (!inChat(e.target)) return;
                e.preventDefault();
                dragDepth++;
                if (!conversationOpen()) return;
                dropzone()?.classList.add('active');
            }, false);

            document.addEventListener('dragleave', (e) => {
                if (!looksLikeFileDrag(e)) return;
                if (!inChat(e.target)) return;
                dragDepth = Math.max(0, dragDepth - 1);
                if (dragDepth === 0) dropzone()?.classList.remove('active');
            }, false);

            document.addEventListener('drop', (e) => {
                dragDepth = 0;
                dropzone()?.classList.remove('active');
                if (!inChat(e.target)) return;   // let non-chat drops be handled elsewhere
                e.preventDefault();
                if (!conversationOpen()) {
                    alert('Open a conversation first.');
                    return;
                }
                const files = e.dataTransfer?.files;
                if (!files || !files.length) {
                    console.warn('[drop] no files in dataTransfer');
                    return;
                }
                console.log('[drop] received', files.length, 'file(s)');
                (async () => {
                    for (const f of files) {
                        if (f.size > 20 * 1024 * 1024) {
                            alert(`"${f.name}" is larger than 20 MB.`);
                            continue;
                        }
                        try { await uploadAttachmentFile(f); }
                        catch (err) { console.error('[drop] upload failed', err); }
                    }
                })();
            }, false);
        }

        // Delegated wiring: paperclip button + tap-to-toggle mic recorder.
        // Both survive Livewire DOM morphs because listeners live on document.
        function wireAttachAndVoice() {
            if (window.__chatState.mediaWired) return;
            window.__chatState.mediaWired = true;

            document.addEventListener('click', (e) => {
                if (e.target.closest('#attachBtn')) {
                    e.preventDefault();
                    document.getElementById('attachInput')?.click();
                    return;
                }
                if (e.target.closest('#voiceCancelBtn')) {
                    e.preventDefault();
                    cancelRecording();
                    return;
                }
                if (e.target.closest('#micBtn')) {
                    e.preventDefault();
                    toggleRecording();
                    return;
                }
            }, true); // capture-phase so we run before Livewire's submit binding

            // File input change → POST directly (bypasses Livewire's temp upload).
            document.addEventListener('change', (e) => {
                if (!e.target.matches('#attachInput')) return;
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                uploadAttachmentFile(file);
                e.target.value = ''; // allow picking the same file twice in a row
            }, true);

            // Send button while recording: stop-and-upload the voice note
            // instead of firing wire:submit on an empty message. Capture-phase
            // so we run before Livewire's form handler.
            document.addEventListener('click', (e) => {
                const sendBtn = e.target.closest('.chat-input-form button[type="submit"]');
                if (!sendBtn) return;
                if (window.__chatState.recorder) {
                    e.preventDefault();
                    e.stopPropagation();
                    stopRecording(); // triggers onstop → uploadVoiceNote
                }
            }, true);
        }

        function toggleRecording() {
            if (window.__chatState.recorder) {
                // Second tap = stop and upload.
                stopRecording();
            } else {
                startRecording();
            }
        }

        async function startRecording() {
            if (window.__chatState.recorder) return;
            const micBtn = document.getElementById('micBtn');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                // Prefer webm/opus; fall back to browser default. Some browsers
                // (Safari) emit audio/mp4 blobs regardless of what we ask for.
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
                    // Guard against 0-byte / trivially-short recordings — they
                    // upload as empty files and Livewire rejects with
                    // "Path cannot be empty".
                    const duration = Math.round((Date.now() - window.__chatState.recStartedAt) / 1000);
                    if (blob.size < 1024 || duration < 1) {
                        console.warn('voice recording too short, skipping upload', { size: blob.size, duration });
                        alert('Recording too short. Hold or tap to record for at least a second.');
                        return;
                    }
                    uploadVoiceNote(blob, duration);
                };
                rec.start();
                window.__chatState.recorder = rec;
                window.__chatState.recStartedAt = Date.now();
                micBtn?.classList.add('recording');
                document.getElementById('voiceRecordingBar')?.classList.add('active');
                document.getElementById('chatReplyInput')?.setAttribute('disabled', 'disabled');

                window.__chatState.recTicker = setInterval(() => {
                    const s = Math.floor((Date.now() - window.__chatState.recStartedAt) / 1000);
                    const el = document.getElementById('voiceRecTime');
                    if (el) el.textContent = `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;
                    if (s >= 300) stopRecording();
                }, 250);
            } catch (err) {
                alert('Microphone access denied or unavailable.');
                console.warn(err);
            }
        }

        function stopRecording() {
            const rec = window.__chatState.recorder;
            if (!rec) return;
            clearInterval(window.__chatState.recTicker);
            window.__chatState.recTicker = null;
            try { rec.stop(); } catch (_) {}
            window.__chatState.recorder = null;
            document.getElementById('micBtn')?.classList.remove('recording');
            document.getElementById('voiceRecordingBar')?.classList.remove('active');
            document.getElementById('chatReplyInput')?.removeAttribute('disabled');
        }

        function cancelRecording() {
            const rec = window.__chatState.recorder;
            if (!rec) return;
            clearInterval(window.__chatState.recTicker);
            window.__chatState.recTicker = null;
            // Swap out the onstop handler so we don't upload the discarded audio.
            rec.onstop = () => {
                try { rec.stream && rec.stream.getTracks?.().forEach(t => t.stop()); } catch (_) {}
            };
            try { rec.stop(); } catch (_) {}
            window.__chatState.recorder = null;
            document.getElementById('micBtn')?.classList.remove('recording');
            document.getElementById('voiceRecordingBar')?.classList.remove('active');
            document.getElementById('chatReplyInput')?.removeAttribute('disabled');
        }

        async function uploadAttachmentFile(file) {
            const input = document.getElementById('chatReplyInput');
            const conversationId = input?.getAttribute('data-conversation-id');
            if (!conversationId) { alert('No conversation open.'); return; }

            const preview = document.getElementById('attachmentPreview');
            const label = document.getElementById('attachmentPreviewLabel');
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
                    console.error('[attach] upload HTTP', res.status, body);
                    alert('Upload failed (' + res.status + ').');
                    return;
                }
                const anchor = document.getElementById('chatReplyInput') || document.getElementById('attachBtn');
                const root = anchor?.closest('[wire\\:id]');
                if (root) Livewire.find(root.getAttribute('wire:id'))?.call('refreshChat');
            } catch (err) {
                console.error('[attach] upload error', err);
                alert('Upload failed. Check the console.');
            } finally {
                if (preview) preview.classList.remove('open');
            }
        }

        async function uploadVoiceNote(blob, durationSec) {
            const input = document.getElementById('chatReplyInput');
            const conversationId = input?.getAttribute('data-conversation-id');
            if (!conversationId) {
                alert('No conversation open.');
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
                    console.error('[voice] upload HTTP', res.status, body);
                    alert('Voice upload failed (' + res.status + ').');
                    return;
                }
                // Refresh the chat so the new voice message appears immediately
                // on the sender side. The receiver already sees it via the
                // Reverb broadcast fired from the controller.
                const anchor = document.getElementById('chatReplyInput') || document.getElementById('micBtn');
                const root = anchor?.closest('[wire\\:id]');
                if (root) {
                    const comp = Livewire.find(root.getAttribute('wire:id'));
                    comp?.call('refreshChat');
                }
            } catch (err) {
                console.error('[voice] upload error', err);
                alert('Voice upload failed. Check the console.');
            }
        }

        // Force Chrome/Firefox to compute the real duration of a streamed WebM.
        // MediaRecorder produces WebM without cues/duration in the header, so
        // audio.duration is Infinity until the browser scans the whole file.
        // Seeking to +∞ triggers that scan; we rewind to 0 on the first
        // durationchange with a finite value.
        window.fixAudioDuration = function (audio) {
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

        // Simple lightbox for message images.
        function openLightbox(url) {
            let lb = document.getElementById('chatLightbox');
            if (!lb) {
                lb = document.createElement('div');
                lb.id = 'chatLightbox';
                lb.className = 'chat-lightbox';
                lb.innerHTML = '<button class="lb-close">&times;</button><img alt="">';
                lb.addEventListener('click', (e) => {
                    if (e.target === lb || e.target.classList.contains('lb-close')) {
                        lb.classList.remove('open');
                    }
                });
                document.body.appendChild(lb);
            }
            lb.querySelector('img').src = url;
            lb.classList.add('open');
        }
        window.openLightbox = openLightbox;

        // Show "X is typing…" for ~2.5s after each whisper; overlapping whispers reset the timer.
        // (WebRTC call UI + logic live in the included partials below.)
        function showTypingIndicator(payload) {
            const indicator = document.getElementById('typingIndicator');
            const nameEl = document.getElementById('typingName');
            if (!indicator) return;

            // Only show when the whisper belongs to the currently open conversation.
            const input = document.getElementById('chatReplyInput');
            const currentConv = input?.getAttribute('data-conversation-id');
            if (payload?.conversationId && currentConv && String(payload.conversationId) !== String(currentConv)) {
                return;
            }

            if (nameEl) nameEl.textContent = payload?.name || 'Someone';
            indicator.style.display = 'block';

            clearTimeout(window.__chatState.typingHideTimeout);
            window.__chatState.typingHideTimeout = setTimeout(() => {
                indicator.style.display = 'none';
            }, 2500);
        }
    </script>

    {{-- WebRTC scripts moved to the layout (components/layouts/app-evoory
         .blade.php) so incoming calls ring from anywhere on the site,
         not just this chat page. --}}
</div>
