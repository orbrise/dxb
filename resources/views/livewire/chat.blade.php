<div>
    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1"></script>

    {{-- CRITICAL: this whole <style> block MUST live inside @push('css'),
         not inline in the component root. Livewire re-sends the entire
         component HTML on every request (tab click, poll refresh, send),
         so leaving ~2000 lines of CSS inline meant shipping ~80 KB of
         identical bytes on every round-trip. Pushed content is rendered
         to the layout <head> ONCE on the initial page load and skipped
         on subsequent Livewire updates — cutting round-trip payload
         dramatically. --}}
    <style>
        /* --------------------------------------------------------------
           evoory chat — dark theme design tokens
           Refreshed to match the messages + calls mockups. Kept every
           class name the existing template renders, just re-styled.
           -------------------------------------------------------------- */
        :root {
            --ev-bg:            #000;
            /* Panel palette — three progressively lighter shades:
                shell → inner cards → search / active states.
                The jumps must be wide enough that the 12px gap between
                sidebar and chat main reads as a distinct seam, otherwise
                the two panels merge into one strip across the top. */
            --ev-panel:         #050506;
            --ev-panel-2:       #17181a;
            --ev-panel-3:       #1f2124;
            --ev-hover:         #1e1f21;
            --ev-border:        rgba(255,255,255,0.06);
            --ev-border-strong: rgba(255,255,255,0.12);
            --ev-text:          #ffffff;
            --ev-text-2:        rgba(255,255,255,0.62);
            --ev-text-3:        rgba(255,255,255,0.38);
            --ev-lime:          #C1F11D;
            --ev-lime-dark:     #a8d616;
            --ev-lime-ink:      #0a0a0a;
            --ev-pink:          #ff2f95;
            --ev-pink-2:        #ec4899;
            --ev-online:        #22c55e;
            --ev-danger:        #ef4444;

            --ev-r-panel:  20px;
            --ev-r-card:   16px;
            --ev-r-btn:    12px;
            --ev-r-bubble: 18px;
            --ev-r-pill:   999px;
        }

        .ev-back-bar { background: #0a0a0a; padding: 12px 0; border-bottom: 1px solid var(--ev-border); }
        .ev-back-bar a { color: var(--ev-lime); text-decoration: none; font-size: 15px; }
        .ev-back-bar h1 { color: #fff; font-size: 18px; font-weight: 600; margin: 0; }
        .ev-back-bar h1 a { color: #fff; text-decoration: none; }
        .ev-container { max-width: 1200px; margin: 0 auto; padding: 0 16px; }

        /* --------------------------------------------------------------
           Chat page shell — body bg is pure black; the chat panels sit
           on top as slightly-lighter cards. Shell frame uses the same
           #0D1011 as the message area so the frame padding blends into
           the display pane without a visible seam.
           -------------------------------------------------------------- */
        /* !important needed — the site's global stylesheet applies
           `background: var(--bg-primary) !important` on body, which
           would otherwise win the cascade against a plain declaration. */
        body { background: #000 !important; }
        body > .ev-header-account { display: none !important; }

        /* Hide the site's global mobile bottom nav (Home / Chats / Add
           Profile / Favorite / Menu) on this page. The chat has its own
           icon-strip nav (chats / calls / status / gallery / settings)
           which is the primary navigation on the chat page — showing
           both would double-stack navs and confuse the user.
           Kept as a body-scoped rule so it wins over the component's
           display: flex rule from the layout stylesheet. */
        body .ev-mobile-bottom-nav { display: none !important; }

        .ev-chat-shell {
            max-width: 1240px;
            margin: 16px auto 24px;
            padding: 0 12px;
        }
        .ev-chat-shell > .mb-3 {
            background: #0D1011;
            border-radius: 5px;
            /* No inner padding — sidebar + chat main fill the shell
               edge-to-edge, matching the mockup. */
            padding: 0;
        }

        /* --------------------------------------------------------------
           Layout — two panels floating on the page background
           -------------------------------------------------------------- */
        .chat-container {
            display: flex;
            gap: 0;
            height: calc(100vh - 160px);
            min-height: 560px;
            background: var(--ev-panel-2);
            padding: 0;
            /* Match the shell's 5px radius so the sidebar's corners
               don't clip inside a rounder container. */
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        /* x-cloak hides Alpine-controlled elements until Alpine has
           booted and evaluated x-show/x-if expressions. Without this,
           every sidebar-tab body flashes into view on initial page load
           before Alpine catches up and hides the inactive ones. */
        [x-cloak] { display: none !important; }

        /* Thin indeterminate progress bar shown while any Livewire
           request is in flight — instant visual feedback so users know
           something is happening even if the actual data takes 100-800ms
           to arrive. Sits at the very top of the chat card, floating over
           both sidebar and main panel. */
        .chat-loading-bar {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--ev-lime), transparent);
            background-size: 40% 100%;
            background-repeat: no-repeat;
            z-index: 100;
            pointer-events: none;
            animation: chatLoadingSlide 900ms linear infinite;
        }
        @keyframes chatLoadingSlide {
            0%   { background-position: -40% 0; }
            100% { background-position: 140% 0; }
        }

        /* ------------- Left sidebar (conversations) ------------------ */
        .chat-sidebar {
            width: 360px;
            min-width: 300px;
            background: var(--ev-panel-2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            /* Anchor for the absolutely-positioned .new-chat-panel overlay. */
            position: relative;
        }

        .chat-sidebar-header {
            padding: 16px 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Title row above the search bar — kept compact to visually
           balance with the chat header on the right panel. */
        .chat-sidebar-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--ev-text);
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            line-height: 1;
        }
        .chat-sidebar-title-actions {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .chat-sidebar-title-actions button,
        .chat-sidebar-title-actions a {
            width: 30px; height: 30px;
            display: inline-flex; align-items: center; justify-content: center;
            background: transparent;
            color: var(--ev-text-2);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: background 120ms, color 120ms;
            font-size: 13px;
        }
        .chat-sidebar-title-actions button:hover,
        .chat-sidebar-title-actions a:hover {
            background: var(--ev-panel-3);
            color: var(--ev-text);
        }
        /* SVG icons inside title-action buttons follow the button's
           currentColor so they respond to hover just like the FA icons. */
        .chat-sidebar-title-actions button svg { display: block; }

        /* Lime round "+" — opens the New-chat user directory overlay.
           Sits at the far right of the sidebar title actions, styled as
           a filled lime pill rather than the transparent ghost buttons. */
        .chat-sidebar-title-actions button.new-chat-btn {
            width: 22px; height: 22px;
            background: var(--ev-lime);
            color: var(--ev-lime-ink);
            font-size: 14px;
        }
        .chat-sidebar-title-actions button.new-chat-btn:hover {
            background: var(--ev-lime-dark);
            color: var(--ev-lime-ink);
        }
        .chat-sidebar-title-actions button.new-chat-btn svg {
            width: 14px;
            height: 14px;
        }

        /* --------------------------------------------------------------
           New-chat directory overlay — appears inside the sidebar and
           covers its content when $newChatOpen is true.
           -------------------------------------------------------------- */
        .new-chat-panel {
            position: absolute;
            inset: 0;
            background: var(--ev-panel-2);
            display: flex;
            flex-direction: column;
            z-index: 20;
        }
        .new-chat-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 16px 12px;
        }
        .new-chat-back {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--ev-text);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 15px;
            transition: background 120ms;
        }
        .new-chat-back:hover { background: var(--ev-panel-3); }
        .new-chat-title {
            color: var(--ev-text);
            font-size: 16px;
            font-weight: 600;
        }
        .new-chat-search {
            position: relative;
            padding: 0 16px;
        }
        .new-chat-search i {
            position: absolute;
            top: 50%; left: 30px;
            transform: translateY(-50%);
            color: var(--ev-text-3);
            font-size: 13px;
        }
        .new-chat-search input {
            width: 100%;
            padding: 12px 16px 12px 40px;
            background: #000;
            color: var(--ev-text);
            border: 1px solid var(--ev-border);
            border-radius: var(--ev-r-pill);
            outline: none;
            font-size: 14px;
        }
        .new-chat-search input::placeholder { color: var(--ev-text-3); }
        .new-chat-help {
            padding: 8px 16px 12px;
            font-size: 12px;
            color: var(--ev-text-3);
        }
        .new-chat-help b { color: var(--ev-text-2); font-weight: 600; }
        .new-chat-list {
            flex: 1;
            overflow-y: auto;
            padding: 4px 8px 12px;
        }
        .new-chat-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--ev-r-card);
            cursor: pointer;
            transition: background 120ms;
        }
        .new-chat-row:hover { background: var(--ev-hover); }
        .new-chat-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2a2d30, #16181a);
            color: var(--ev-text-2);
            display: inline-flex; align-items: center; justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .new-chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .new-chat-info { min-width: 0; flex: 1; }
        .new-chat-name {
            color: var(--ev-text);
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .new-chat-handle {
            color: var(--ev-text-3);
            font-size: 12px;
            margin-top: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .new-chat-empty {
            padding: 60px 20px;
            text-align: center;
            color: var(--ev-text-3);
        }
        .new-chat-empty i { font-size: 32px; margin-bottom: 8px; display: block; }
        .new-chat-list::-webkit-scrollbar { width: 6px; }
        .new-chat-list::-webkit-scrollbar-track { background: transparent; }
        .new-chat-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }
        .new-chat-list::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.16); }

        /* Search pill */
        .chat-search {
            position: relative;
        }
        .chat-search input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            background: #000;
            color: var(--ev-text);
            border: 1px solid var(--ev-border);
            border-radius: var(--ev-r-pill);
            outline: none;
            font-size: 14px;
            transition: border-color 120ms;
        }
        .chat-search input::placeholder { color: var(--ev-text-3); }
        .chat-search input:focus {
            border-color: rgba(255,255,255,0.18);
            background: #000;
        }
        .chat-search .search-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: var(--ev-text-3);
            pointer-events: none;
        }
        .chat-search .search-results {
            position: absolute;
            top: calc(100% + 6px);
            left: 0; right: 0;
            background: var(--ev-panel-2);
            border: 1px solid var(--ev-border);
            border-radius: var(--ev-r-card);
            max-height: 320px;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            z-index: 30;
        }
        .search-result-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            cursor: pointer;
            transition: background 120ms;
        }
        .search-result-item:hover { background: var(--ev-hover); }
        .search-result-item .conversation-avatar { width: 36px; height: 36px; font-size: 14px; }
        .search-result-item .conversation-name { font-size: 14px; }
        .search-result-item .conversation-preview { font-size: 12px; color: var(--ev-text-3); }

        /* Filter chips — All / Unread / Active / Archived --------------- */
        .chat-filter-chips {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .chat-filter-chips::-webkit-scrollbar { display: none; }
        .chat-chip {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            color: var(--ev-text-2);
            background: var(--ev-panel-3);
            border-radius: var(--ev-r-pill);
            text-decoration: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 120ms, color 120ms;
            border: none;
            cursor: pointer;
        }
        .chat-chip:hover {
            background: var(--ev-hover);
            color: var(--ev-text);
        }
        .chat-chip.active {
            background: #000;
            color: #fff;
            font-weight: 600;
        }
        .chat-chip.active:hover { background: #0a0a0a; color: #fff; }
        .chat-chip-badge {
            background: var(--ev-lime);
            color: var(--ev-lime-ink);
            font-size: 11px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: var(--ev-r-pill);
            min-width: 18px;
            text-align: center;
        }
        .chat-chip.active .chat-chip-badge {
            background: var(--ev-lime);
            color: var(--ev-lime-ink);
        }
        .chat-chip-badge-red {
            background: var(--ev-pink);
            color: #fff;
        }

        /* Conversation list --------------------------------------------- */
        .conversation-list {
            flex: 1;
            overflow-y: auto;
            padding: 4px 8px 12px;
        }
        /* Section header labels above Support / Favourites / Recent
           groups. Small white bold text with just enough vertical
           breathing room to feel like a category divider without a rule. */
        .conv-section-header {
            font-size: 14px;
            font-weight: 600;
            color: var(--ev-text);
            padding: 14px 12px 8px;
            letter-spacing: 0.1px;
        }
        /* "Add Favorite" tile — sits under the Favourites header. A small
           lime-green round button plus a label, styled like a conversation
           row so hover treatment feels consistent. */
        .add-favorite-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: var(--ev-r-card);
            cursor: pointer;
            transition: background 120ms;
        }
        .add-favorite-row:hover { background: var(--ev-hover); }
        .add-favorite-btn {
            width: 46px; height: 46px;
            border-radius: 50%;
            background: var(--ev-online);   /* mint green from mockup */
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .add-favorite-label {
            color: var(--ev-text);
            font-size: 14px;
            font-weight: 500;
        }
        .conversation-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--ev-r-card);
            cursor: pointer;
            transition: background 120ms;
            position: relative;
        }
        .conversation-item:hover { background: var(--ev-hover); }
        .conversation-item.active {
            background: var(--ev-panel-3);
        }
        .conversation-item.pinned::before {
            content: "";
            position: absolute;
            left: 4px;
            top: 12px;
            width: 4px;
            height: 4px;
            background: var(--ev-lime);
            border-radius: 50%;
        }

        .conversation-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2a2d30, #16181a);
            color: var(--ev-text-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .conversation-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .conversation-avatar i { font-size: 18px; }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }
        .conversation-name {
            color: var(--ev-text);
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conversation-preview {
            color: var(--ev-text-2);
            font-size: 13px;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conversation-item.unread .conversation-preview {
            color: var(--ev-text);
            font-weight: 500;
        }
        .support-badge {
            background: var(--ev-lime);
            color: var(--ev-lime-ink);
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 6px;
            letter-spacing: 0.4px;
        }
        .missed-call-badge {
            color: var(--ev-danger);
            font-size: 11px;
            font-weight: 500;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* --------------------------------------------------------------
           Status feature — sidebar cards, viewer, text composer.
           -------------------------------------------------------------- */
        /* "My status" card at the top of the Status tab. Circular avatar
           with a green "+" plus-badge overlay when the user has no
           active statuses; a lime ring when they have some. */
        .status-my-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--ev-r-card);
            cursor: pointer;
            transition: background 120ms;
        }
        .status-my-card:hover { background: var(--ev-hover); }
        .status-my-avatar {
            position: relative;
            width: 46px; height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2a2d30, #16181a);
            color: var(--ev-text-2);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 600;
            overflow: hidden;
            flex-shrink: 0;
        }
        .status-my-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .status-my-avatar.has-status {
            box-shadow: 0 0 0 2px var(--ev-panel-2), 0 0 0 4px var(--ev-lime);
        }
        .status-my-plus {
            position: absolute;
            right: -2px; bottom: -2px;
            width: 20px; height: 20px;
            border-radius: 50%;
            background: var(--ev-online);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 10px;
            border: 2px solid var(--ev-panel-2);
        }

        /* "My statuses" per-post list — appears under the summary card
           when the user clicks it to drill in. Each row = one status,
           with a thumbnail, type label, view count, and timestamp. */
        .my-statuses-head {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px 6px;
            color: var(--ev-text);
            font-size: 15px;
            font-weight: 600;
        }
        .my-statuses-back {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: var(--ev-text);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .my-statuses-back:hover { background: var(--ev-panel-3); }
        .my-status-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: var(--ev-r-card);
            cursor: pointer;
            transition: background 120ms;
        }
        .my-status-row:hover { background: var(--ev-hover); }
        .my-status-row.active { background: var(--ev-panel-3); }
        .my-status-thumb {
            position: relative;
            width: 46px; height: 46px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
            background: var(--ev-panel-3);
        }
        .my-status-thumb img, .my-status-thumb video {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .my-status-thumb-text {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 14px;
        }
        .my-status-thumb-play {
            position: absolute;
            inset: 0;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 12px;
            background: rgba(0,0,0,0.35);
        }
        .my-status-body { flex: 1; min-width: 0; }
        .my-status-label {
            color: var(--ev-text);
            font-size: 14px;
            font-weight: 600;
        }
        .my-status-meta {
            display: flex;
            gap: 12px;
            font-size: 12px;
            color: var(--ev-text-2);
            margin-top: 3px;
        }
        .my-status-meta i { font-size: 11px; margin-right: 3px; }
        .my-status-time {
            font-size: 11px;
            color: var(--ev-text-3);
        }

        /* Ring around avatars in the Recent list — green for unseen,
           grey for already-seen. WhatsApp uses the same convention. */
        .status-ring {
            box-shadow: 0 0 0 2px var(--ev-panel-2), 0 0 0 4px var(--ev-online);
        }
        .status-ring.seen {
            box-shadow: 0 0 0 2px var(--ev-panel-2), 0 0 0 4px rgba(255,255,255,0.25);
        }

        /* "+" dropdown menu (Photos & Videos / Text) — shown on Status tab. */
        .new-chat-wrap {
            position: relative;
            display: inline-flex;
        }
        .new-chat-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--ev-panel-3);
            border: 1px solid var(--ev-border);
            border-radius: 10px;
            padding: 6px;
            min-width: 180px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            display: none;
            z-index: 40;
        }
        .new-chat-wrap.open .new-chat-menu { display: block; }
        .new-chat-menu button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: transparent;
            border: none;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--ev-text);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }
        .new-chat-menu button:hover { background: var(--ev-hover); }
        .new-chat-menu button i { width: 16px; color: var(--ev-text-2); }

        /* Right-panel status viewer. */
        .status-viewer {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #0D1011;
            overflow: hidden;
        }
        .status-viewer-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: var(--ev-panel-2);
        }
        .status-viewer-user { display: flex; align-items: center; gap: 12px; }
        .status-viewer-actions { display: flex; gap: 6px; }
        .status-viewer-actions button {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: var(--ev-text);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .status-viewer-actions button:hover { background: var(--ev-panel-3); }
        .status-viewer-delete:hover { background: rgba(239,68,68,0.15) !important; color: var(--ev-danger); }
        .status-viewer-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: auto;
        }
        .status-viewer-media {
            max-width: 100%;
            max-height: calc(100vh - 320px);
            border-radius: 12px;
            display: block;
        }
        .status-viewer-text {
            width: min(600px, 92%);
            min-height: 360px;
            padding: 40px 30px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .status-viewer-caption {
            margin-top: 14px;
            color: var(--ev-text-2);
            font-size: 14px;
            max-width: 600px;
            text-align: center;
        }
        .status-viewer-viewers {
            padding: 12px 18px 18px;
            background: var(--ev-panel-2);
            border-top: 1px solid var(--ev-border);
            max-height: 200px;
            overflow-y: auto;
        }
        .status-viewers-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--ev-text-2);
            margin-bottom: 8px;
        }
        .status-viewer-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 0;
        }
        .status-viewer-row .conversation-avatar { flex-shrink: 0; }
        .status-viewer-row-name { flex: 1; font-size: 13px; color: var(--ev-text); }
        .status-viewer-row-time { font-size: 11px; color: var(--ev-text-3); }

        /* Empty-state ring icon for "Share statuses" pitch. */
        .status-empty-icon {
            width: 130px;
            height: 130px;
            color: var(--ev-text-2);
            margin-bottom: 12px;
        }
        .status-empty-icon svg { width: 100%; height: 100%; }

        /* Text-status composer — modal overlay covering the whole shell.
           Big coloured card with textarea + colour swatches + Post button. */
        .text-status-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 200;
        }
        .text-status-dialog {
            position: relative;
            width: min(520px, 92vw);
            min-height: 380px;
            border-radius: 20px;
            padding: 30px 24px 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
        }
        .text-status-close {
            position: absolute;
            top: 12px; right: 12px;
            width: 34px; height: 34px;
            border-radius: 50%;
            border: none;
            background: rgba(0,0,0,0.25);
            color: #fff;
            cursor: pointer;
        }
        .text-status-dialog textarea {
            flex: 1;
            min-height: 200px;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: 26px;
            font-weight: 600;
            text-align: center;
            resize: none;
            padding: 20px 10px;
        }
        .text-status-dialog textarea::placeholder { color: rgba(255,255,255,0.5); }
        .text-status-bg-row {
            display: flex;
            gap: 8px;
            justify-content: center;
            padding: 12px 0;
        }
        .text-status-swatch {
            width: 28px; height: 28px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
            cursor: pointer;
        }
        .text-status-swatch.active {
            border-color: #fff;
            transform: scale(1.15);
        }
        .text-status-post {
            align-self: flex-end;
            padding: 10px 20px;
            border: none;
            border-radius: var(--ev-r-pill);
            background: rgba(0,0,0,0.35);
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .text-status-post:disabled { opacity: 0.4; cursor: not-allowed; }

        /* --------------------------------------------------------------
           Settings tab — sidebar profile card + section list, right-panel
           splash and per-section pane.
           -------------------------------------------------------------- */
        .settings-profile-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 12px 20px;
        }
        .settings-profile-avatar {
            width: 54px; height: 54px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, #2a2d30, #16181a);
            color: var(--ev-text-2);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }
        .settings-profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .settings-profile-name {
            color: var(--ev-text);
            font-size: 16px;
            font-weight: 600;
        }
        .settings-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            border-radius: var(--ev-r-card);
            cursor: pointer;
            transition: background 120ms;
        }
        .settings-row:hover { background: var(--ev-hover); }
        .settings-row.active { background: var(--ev-panel-3); }
        .settings-row-icon {
            width: 30px;
            color: var(--ev-text-2);
            font-size: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .settings-row-title {
            color: var(--ev-text);
            font-size: 14px;
            font-weight: 600;
        }
        .settings-row-sub {
            color: var(--ev-text-2);
            font-size: 12px;
            margin-top: 2px;
        }

        /* Right panel splash — big centred gear with "Settings" heading. */
        .settings-splash-icon {
            font-size: 120px;
            color: var(--ev-text);
            margin-bottom: 20px;
            opacity: 0.9;
        }
        .settings-splash-title {
            color: var(--ev-text);
            font-size: 36px !important;
            font-weight: 700 !important;
        }

        /* Per-section pane — header with back button + a stack of links.
           NOTE: min-height:0 on the flex items is REQUIRED so the body's
           overflow-y:auto actually kicks in; without it, the flex child
           defaults to min-height:auto and grows to fit its content,
           making the scrollbar never appear. */
        .settings-pane {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            background: #0D1011;
            overflow: hidden;
        }
        .settings-pane-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: var(--ev-panel-2);
            flex-shrink: 0;
        }
        .settings-pane-back {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--ev-text);
            cursor: pointer;
        }
        .settings-pane-back:hover { background: var(--ev-panel-3); }
        .settings-pane-title {
            color: var(--ev-text);
            font-size: 17px;
            font-weight: 700;
        }
        .settings-pane-body {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 18px 24px 40px;
            max-width: 720px;
            width: 100%;
        }
        /* Scrollbar styling matches conversation-list / chat-messages so
           the pane doesn't look out of place. */
        .settings-pane-body::-webkit-scrollbar { width: 6px; }
        .settings-pane-body::-webkit-scrollbar-track { background: transparent; }
        .settings-pane-body::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.08);
            border-radius: 4px;
        }
        .settings-pane-body::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.16); }
        .settings-pane-hint {
            color: var(--ev-text-3);
            font-size: 13px;
            margin: 0 0 16px;
        }
        .settings-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 12px;
            border-radius: 10px;
            color: var(--ev-text);
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            transition: background 120ms;
        }
        .settings-link:hover { background: var(--ev-hover); }
        .settings-link i:first-child {
            width: 22px;
            color: var(--ev-text-2);
            font-size: 15px;
        }
        .settings-link span { flex: 1; }
        .settings-link i:last-child {
            color: var(--ev-text-3);
            font-size: 12px;
        }
        .settings-link.danger { color: var(--ev-danger); }
        .settings-link.danger i:first-child { color: var(--ev-danger); }

        /* ============ ACCOUNT SECTION ============ */
        .acct-avatar-wrap { text-align: center; padding: 10px 0 24px; }
        .acct-avatar {
            position: relative;
            width: 120px; height: 120px;
            margin: 0 auto;
            border-radius: 50%;
            background: linear-gradient(135deg, #2a2d30, #16181a);
            color: var(--ev-text-2);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 46px; font-weight: 700;
            overflow: hidden;
        }
        .acct-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .acct-avatar-btn {
            position: absolute;
            bottom: 4px; right: 4px;
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--ev-online);
            color: #fff;
            border: 3px solid var(--ev-panel-2);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .acct-avatar-caption { margin-top: 10px; color: var(--ev-text-3); font-size: 13px; }

        .acct-field { margin-bottom: 18px; }
        .acct-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ev-text-2);
            margin-bottom: 6px;
        }
        .acct-input {
            width: 100%;
            padding: 12px 14px;
            background: var(--ev-panel-3);
            border: 1px solid var(--ev-border);
            border-radius: 8px;
            color: var(--ev-text);
            font-size: 14px;
            outline: none;
        }
        .acct-input:focus { border-color: rgba(255,255,255,0.25); }
        .acct-hint { font-size: 12px; color: var(--ev-text-3); margin-top: 6px; }
        .acct-error { font-size: 12px; color: var(--ev-danger); margin-top: 6px; }
        .acct-phone-row { display: flex; gap: 8px; }
        .acct-phone-cc {
            display: inline-flex; align-items: center; gap: 4px;
            background: var(--ev-panel-3);
            border: 1px solid var(--ev-border);
            border-radius: 8px;
            padding: 0 10px;
            color: var(--ev-text);
            font-size: 14px;
        }
        .acct-phone-cc input {
            width: 40px;
            background: transparent;
            border: none;
            outline: none;
            color: var(--ev-text);
            font-size: 14px;
            padding: 12px 0;
            text-align: left;
        }
        .acct-radio {
            display: flex;
            gap: 12px;
            padding: 14px;
            border: 1px solid var(--ev-border);
            border-radius: 10px;
            cursor: pointer;
            margin-bottom: 8px;
            transition: border-color 120ms, background 120ms;
        }
        .acct-radio input { margin-top: 3px; accent-color: var(--ev-online); }
        .acct-radio.active { border-color: var(--ev-online); background: rgba(34,197,94,0.05); }
        .acct-radio-title { color: var(--ev-text); font-size: 14px; font-weight: 600; }
        .acct-radio-sub { color: var(--ev-text-2); font-size: 12px; margin-top: 4px; line-height: 1.5; }
        .acct-save-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: var(--ev-online);
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            margin-top: 20px;
        }
        .acct-save-btn:hover { background: #1ba954; }
        .acct-flash {
            background: rgba(34,197,94,0.12);
            color: var(--ev-online);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-top: 8px;
        }

        /* ============ CHATS / NOTIFICATIONS SHARED PREFS ============ */
        .pref-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 14px 4px;
        }
        .pref-title { color: var(--ev-text); font-size: 14px; font-weight: 600; }
        .pref-sub   { color: var(--ev-text-2); font-size: 12px; margin-top: 4px; line-height: 1.5; }
        .pref-block { padding: 14px 4px 8px; }
        .pref-divider { height: 1px; background: var(--ev-border); margin: 4px 0; }
        .pref-link {
            display: block;
            padding: 12px 4px;
            color: var(--ev-lime);
            font-size: 14px;
            text-decoration: none;
        }
        .pref-link:hover { color: #dbff44; text-decoration: underline; }
        /* iOS-style toggle */
        .pref-toggle { position: relative; display: inline-block; width: 42px; height: 24px; flex-shrink: 0; }
        .pref-toggle input { opacity: 0; width: 0; height: 0; }
        .pref-toggle-slider {
            position: absolute; inset: 0;
            background: #444;
            border-radius: 24px;
            transition: background 200ms;
            cursor: pointer;
        }
        .pref-toggle-slider::before {
            content: "";
            position: absolute;
            left: 3px; top: 3px;
            width: 18px; height: 18px;
            background: #fff;
            border-radius: 50%;
            transition: transform 200ms;
        }
        .pref-toggle input:checked + .pref-toggle-slider { background: var(--ev-online); }
        .pref-toggle input:checked + .pref-toggle-slider::before { transform: translateX(18px); }
        /* Radio row (Disappearing messages, ringtones, message tones) */
        .pref-radio-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 12px;
            border-radius: 10px;
            cursor: pointer;
        }
        .pref-radio-row:hover { background: var(--ev-hover); }
        .pref-radio-row.active { background: var(--ev-panel-3); }
        .pref-radio-row input { accent-color: var(--ev-danger); }
        .pref-radio-label { flex: 1; color: var(--ev-text); font-size: 14px; }
        .pref-radio-default { color: var(--ev-online); font-size: 12px; }
        .pref-play-btn {
            background: transparent;
            border: none;
            color: var(--ev-danger);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 10px;
            border-radius: 6px;
        }
        .pref-play-btn:hover { background: rgba(239,68,68,0.1); }

        /* Notifications section — big alert card + red allow button */
        .notif-block {
            border: 1px solid var(--ev-border);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 18px;
        }
        .notif-block-title { color: var(--ev-text); font-weight: 600; margin-bottom: 6px; }
        .notif-block-sub   { color: var(--ev-text-2); font-size: 13px; margin-bottom: 14px; line-height: 1.5; }
        .notif-allow-btn {
            padding: 12px 20px;
            background: #ef6a5e;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .notif-allow-btn:hover { background: #e15147; }
        .notif-allow-btn:disabled { background: #555; cursor: default; }

        /* ============ VIDEO & VOICE ============ */
        .vv-preview {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 18px;
        }
        .vv-preview video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .vv-preview-placeholder {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            color: var(--ev-text-3);
            font-size: 13px;
            pointer-events: none;
        }
        .vv-preview video[srcObject] + .vv-preview-placeholder { display: none; }
        .vv-field { margin-bottom: 14px; }
        .vv-label { display: block; color: var(--ev-text-2); font-size: 13px; margin-bottom: 6px; font-weight: 600; }
        .vv-select {
            width: 100%;
            padding: 12px 14px;
            background: var(--ev-panel-3);
            border: 1px solid var(--ev-border);
            border-radius: 8px;
            color: var(--ev-text);
            font-size: 14px;
            outline: none;
        }
        .vv-meter {
            width: 100%; height: 8px;
            background: var(--ev-panel-3);
            border-radius: 4px;
            overflow: hidden;
        }
        .vv-meter-fill {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--ev-online), var(--ev-lime));
            transition: width 60ms linear;
        }
        .vv-actions {
            display: flex;
            gap: 10px;
            margin: 18px 0 14px;
            flex-wrap: wrap;
        }
        .vv-btn {
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .vv-btn.ghost {
            background: transparent;
            color: var(--ev-text);
            border: 1px solid var(--ev-border-strong);
        }
        .vv-btn.ghost:hover { background: var(--ev-hover); }
        .vv-btn.primary {
            background: #0a0a0a;
            color: #fff;
            border: 1px solid #0a0a0a;
        }
        .vv-btn.primary:hover { background: #1a1a1a; }
        .vv-result-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--ev-border);
            color: var(--ev-text);
            font-size: 14px;
        }
        .vv-result-row:last-child { border-bottom: none; }
        .vv-result { color: var(--ev-text-3); }
        .vv-result.pass { color: var(--ev-online); font-weight: 600; }
        .vv-result.fail { color: var(--ev-danger); font-weight: 600; }

        /* --------------------------------------------------------------
           Gallery tab — shared-media grid.
           -------------------------------------------------------------- */
        .gallery-intro {
            padding: 4px 12px 10px;
            font-size: 12px;
            color: var(--ev-text-3);
            line-height: 1.4;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            padding: 4px 8px 12px;
        }
        .gallery-tile {
            position: relative;
            aspect-ratio: 1 / 1;
            border-radius: 8px;
            overflow: hidden;
            background: var(--ev-panel-3);
            cursor: pointer;
            transition: transform 120ms;
        }
        .gallery-tile:hover { transform: scale(0.97); }
        .gallery-tile img,
        .gallery-tile video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .gallery-tile-badge {
            position: absolute;
            top: 6px; right: 6px;
            width: 22px; height: 22px;
            border-radius: 50%;
            background: rgba(0,0,0,0.55);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 10px;
        }

        /* Highlight pulse applied to a message wrapper after a Gallery
           tile click, so the user can see exactly which message the
           tapped media belongs to. Fades away after ~2s. */
        .message-wrapper.gallery-highlight .message-bubble {
            animation: galleryFlash 2.2s ease-out;
        }
        @keyframes galleryFlash {
            0%   { box-shadow: 0 0 0 0 rgba(193,241,29,0.6); }
            30%  { box-shadow: 0 0 0 8px rgba(193,241,29,0.35); }
            100% { box-shadow: 0 0 0 0 rgba(193,241,29,0); }
        }

        /* Call-history row meta line — small direction icon + label
           (Incoming / Outgoing / Missed) under the peer name. Colours
           match the mockup: white for the row, red for a missed call. */
        .call-row-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--ev-text-2);
            margin-top: 3px;
        }
        .call-row-meta i { font-size: 11px; }
        .call-row.missed .conversation-name,
        .call-row.missed .call-row-meta { color: var(--ev-danger); }
        .conversation-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex-shrink: 0;
        }
        .conversation-time {
            font-size: 11px;
            color: var(--ev-text-3);
        }
        .unread-badge {
            background: var(--ev-pink);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: var(--ev-r-pill);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Bottom nav strip INSIDE the sidebar (chat, phone, orbit, …) -- */
        .chat-sidebar-tabs {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 12px 8px;
            border-top: 1px solid var(--ev-border);
            background: var(--ev-panel-2);
        }
        .chat-sidebar-tab {
            width: 40px; height: 40px;
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--ev-text-3);
            border-radius: var(--ev-r-pill);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background 120ms, color 120ms;
            position: relative;
            text-decoration: none;
        }
        .chat-sidebar-tab:hover { color: var(--ev-text); background: var(--ev-panel-3); }
        .chat-sidebar-tab.active {
            background: #000;
            color: var(--ev-text);
        }
        .chat-sidebar-tab svg {
            width: 15px;
            height: 14px;
            display: block;
        }
        .chat-sidebar-tab .tab-badge {
            position: absolute;
            top: -2px; right: -4px;
            min-width: 16px; height: 16px;
            padding: 0 4px;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: var(--ev-r-pill);
            background: var(--ev-pink);
            color: #fff;
        }

        /* --------------------------------------------------------------
           Right panel — chat thread
           -------------------------------------------------------------- */
        .chat-main {
            flex: 1;
            background: #0D1011;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        /* Data-driven wrappers inside chat-main. These divs exist so that
           Alpine's x-show can toggle whole subtrees, but they also need to
           be proper flex containers themselves — otherwise their inner
           flex:1 elements (chat-messages, settings-pane, etc.) collapse
           to 0 height and the right panel appears blank. Symptom on mobile
           was a fully-black screen when tapping into a conversation or
           opening a settings sub-section, because chat-main filled the
           viewport but its child wrapper had no measurable size. */
        .chat-thread-wrapper,
        .settings-pane-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            min-width: 0;
            overflow: hidden;
        }

        /* Right-panel visibility driven by a data-tab attribute on
           chat-container. The attribute is set both from Blade (initial
           render, server-authoritative) and from Alpine `:data-tab="tab"`
           (reactive to sidebar clicks). Belt-and-suspenders: if Alpine
           is briefly out of sync after a Livewire morph, the Blade value
           still points at the current activeTab, so the correct panel
           stays visible. No more black-screen from stuck x-cloak.

           chat-thread-wrapper only shows on Chats/Gallery; hide otherwise.
           settings-pane-wrapper only shows on Settings.
           status-viewer only shows on Status. */
        .chat-container:not([data-tab="chats"]):not([data-tab="gallery"]) .chat-thread-wrapper { display: none; }
        .chat-container:not([data-tab="settings"]) .settings-pane-wrapper { display: none; }
        .chat-container:not([data-tab="status"]) .status-viewer { display: none; }
        /* Empty states — mirror the same rule. Only one empty state
           should be visible at a time based on tab + conversation state.
           Rendered by Blade with data-empty markers; CSS hides all that
           don't match. */
        .chat-container [data-empty] { display: none; }
        .chat-container[data-tab="status"][data-has-status="0"]  [data-empty="status"]   { display: flex; }
        .chat-container[data-tab="settings"][data-has-section="0"] [data-empty="settings"] { display: flex; }
        .chat-container[data-tab="gallery"][data-has-conv="0"]   [data-empty="gallery"]  { display: flex; }
        .chat-container[data-tab="chats"][data-has-conv="0"]     [data-empty="callable"] { display: flex; }
        .chat-container[data-tab="calls"]                          [data-empty="callable"] { display: flex; }

        .chat-header {
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            /* Match the sidebar top so the whole top row across both
               panels reads as one continuous header band. */
            background: var(--ev-panel-2);
        }
        /* Mobile-only "Back" button — hides on desktop (both panels are
           always visible there so it's redundant). Below 900px the
           sidebar hides when a conversation opens and this button lets
           the user return to it. */
        .mobile-back-btn {
            display: none;
            background: transparent;
            color: var(--ev-text);
            border: none;
            padding: 6px 10px;
            border-radius: var(--ev-r-btn);
            font-size: 14px;
            cursor: pointer;
        }
        .mobile-back-btn:hover { background: var(--ev-panel-3); }
        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .chat-header-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, #2a2d30, #16181a);
            display: flex; align-items: center; justify-content: center;
            color: var(--ev-text-2);
            font-weight: 600;
            flex-shrink: 0;
        }
        .chat-header-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .chat-header-name {
            color: var(--ev-text);
            font-weight: 700;
            font-size: 15px;
            display: flex; align-items: center; gap: 6px;
        }
        .chat-header-email {
            font-size: 12px;
            color: var(--ev-online);   /* mockup shows Online in green */
            margin-top: 2px;
            display: flex; align-items: center; gap: 6px;
        }
        .chat-header-actions {
            display: flex; align-items: center; gap: 6px;
        }
        .chat-call-buttons {
            display: flex;
            gap: 4px;
        }
        .chat-call-buttons button,
        .chat-header-actions > button,
        .chat-header-menu > button {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: transparent;
            color: #fff;
            border: none;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: background 120ms, color 120ms;
        }
        /* Override any inherited FA icon colour from the site's global
           stylesheet — the phone / video / ellipsis icons were showing
           lime-green because of an outer <a i> rule. */
        .chat-call-buttons button i,
        .chat-header-actions > button i,
        .chat-header-menu > button i { color: #fff; }
        .chat-call-buttons button:hover,
        .chat-header-actions > button:hover,
        .chat-header-menu > button:hover {
            background: var(--ev-panel-3);
            color: #fff;
        }

        /* 3-dot options dropdown in the chat header. Click the ellipsis
           to reveal / hide; the click-outside close is handled by the
           document listener at the bottom of the file. */
        .chat-header-menu {
            position: relative;
        }
        .chat-header-menu > button {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: transparent;
            color: var(--ev-text-2);
            border: none;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: background 120ms, color 120ms;
        }
        .chat-header-menu > button:hover { background: var(--ev-panel-3); color: var(--ev-text); }
        .chat-header-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            background: var(--ev-panel-2);
            border: 1px solid var(--ev-border);
            border-radius: 10px;
            min-width: 200px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            padding: 6px;
            display: none;
            z-index: 30;
        }
        .chat-header-menu.open .chat-header-dropdown { display: block; }
        .chat-header-dropdown button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: transparent;
            border: none;
            padding: 9px 10px;
            border-radius: 8px;
            color: var(--ev-text);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 120ms;
        }
        .chat-header-dropdown button:hover { background: var(--ev-hover); }
        .chat-header-dropdown button.danger { color: var(--ev-danger); }
        .chat-header-dropdown button.danger:hover { background: rgba(239,68,68,0.15); }

        /* --------------- Messages thread ----------------------------- */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 22px 28px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            /* Slightly darker than the header/input strips so the
               display area reads as a distinct pane, matching the mockup. */
            background: #0D1011;
        }

        .chat-date-sep {
            display: flex;
            justify-content: center;
            margin: 12px 0 6px;
        }
        .chat-date-sep span {
            background: var(--ev-panel-3);
            color: var(--ev-text-2);
            padding: 4px 12px;
            border-radius: var(--ev-r-pill);
            font-size: 11px;
            font-weight: 500;
        }

        .message-wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
            /* No gap between bubble and time — the time row already has
               margin-top of its own. */
        }
        .message-wrapper.sent     { align-items: flex-end; }
        .message-wrapper.received { align-items: flex-start; }

        .message-bubble {
            max-width: 68%;
            padding: 10px 14px;
            border-radius: var(--ev-r-bubble);
            position: relative;
            word-wrap: break-word;
            line-height: 1.45;
            font-size: 14px;
        }
        .message-wrapper.received .message-bubble {
            background: var(--ev-panel-3);
            color: var(--ev-text);
            border-top-left-radius: 6px;
        }
        .message-wrapper.sent .message-bubble {
            /* WhatsApp-style pale mint for outgoing bubbles. Dark text
               against light bg for good contrast. */
            background: #D9FDD3;
            color: #0a0a0a;
            border-top-right-radius: 6px;
        }
        .message-bubble.media-only {
            padding: 4px;
            background: transparent !important;
        }
        .message-text {
            margin: 0;
            white-space: pre-wrap;
        }
        /* Time + delivery ticks are OUTSIDE the bubble now — they sit
           on the chat backdrop, muted grey on both sides regardless of
           the bubble colour above. */
        .message-time {
            font-size: 10px;
            color: var(--ev-text-3);
            margin: 2px 4px 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .message-ticks .tick { font-size: 11px; opacity: 0.7; }
        .message-ticks .tick.read.double-tick { color: #007adf; opacity: 1; }

        /* Disappearing-message clock — sits next to the time on each row
           where expires_at is set. The countdown text is filled in by JS. */
        .message-expires {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            color: var(--ev-text-3);
            font-size: 10px;
            opacity: 0.8;
        }
        .message-expires i { font-size: 10px; }
        .message-wrapper.disappearing.vanishing {
            opacity: 0;
            transform: translateY(-4px) scale(0.98);
            transition: opacity 600ms ease-out, transform 600ms ease-out;
        }

        /* Media inside bubbles ---------------------------------------- */
        .msg-image {
            display: block;
            max-width: 320px;
            max-height: 380px;
            border-radius: 14px;
            cursor: zoom-in;
        }
        .msg-audio {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 240px;
        }
        .msg-audio audio {
            width: 100%;
            height: 36px;
        }
        .audio-duration {
            font-size: 11px;
            color: var(--ev-text-3);
        }
        .message-wrapper.sent .audio-duration { color: rgba(10,10,10,0.55); }
        .msg-file {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
        }
        .msg-file i { font-size: 22px; }
        .msg-file .file-name { font-size: 13px; font-weight: 600; }
        .msg-file .file-size { font-size: 11px; color: var(--ev-text-3); }
        .message-wrapper.sent .msg-file { background: rgba(0,0,0,0.08); }
        .message-wrapper.sent .msg-file .file-size { color: rgba(10,10,10,0.55); }

        /* Call bubble ------------------------------------------------- */
        .call-msg {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 200px;
        }
        .call-msg.missed .call-icon { background: rgba(239,68,68,0.15); color: var(--ev-danger); }
        .call-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 15px;
        }
        .message-wrapper.sent .call-icon {
            background: rgba(10,10,10,0.15);
        }
        .call-body { flex: 1; }
        .call-label { font-size: 13px; font-weight: 600; }
        .call-sub {
            font-size: 11px;
            color: var(--ev-text-3);
            margin-top: 2px;
        }
        .message-wrapper.sent .call-sub { color: rgba(10,10,10,0.5); }
        .call-back-btn {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            color: inherit;
            border: none;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            transition: background 120ms;
        }
        .call-back-btn:hover { background: rgba(255,255,255,0.16); }
        .message-wrapper.sent .call-back-btn { background: rgba(10,10,10,0.15); }

        /* --------------- Input area ---------------------------------- */
        .chat-input {
            padding: 14px 18px 18px;
            /* Transparent — the composer sits directly on top of the
               chat-messages backdrop with no divider band. */
            background: transparent;
        }
        .chat-input-form {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--ev-panel-3);
            border-radius: var(--ev-r-pill);
            padding: 6px 8px 6px 14px;
        }
        .chat-input-form input,
        .chat-input-form textarea {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--ev-text);
            font-size: 14px;
            padding: 10px 4px;
            resize: none;
        }
        .chat-input-form input::placeholder,
        .chat-input-form textarea::placeholder { color: var(--ev-text-3); }
        /* Every icon button inside the input bar shares one style —
           circular, transparent by default, faint hover fill. Emoji /
           attach / mic buttons all match visually. */
        .chat-input-form .attach-btn,
        .chat-input-form .emoji-btn,
        .chat-input-form .mic-btn {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            background: transparent;
            color: var(--ev-text-2);
            border: none;
            cursor: pointer;
            transition: background 120ms, color 120ms;
            font-size: 15px;
            padding: 0;
        }
        .chat-input-form .attach-btn:hover,
        .chat-input-form .emoji-btn:hover,
        .chat-input-form .mic-btn:hover {
            background: var(--ev-hover);
            color: var(--ev-text);
        }
        /* Send button — pink round pill matching the mockup. Targets
           both a bare submit button AND anything with .send-btn class
           so future refactors don't lose the styling. */
        .chat-input-form button[type="submit"],
        .send-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--ev-pink), #d81b76);
            color: #fff;
            border: none;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 15px;
            transition: transform 120ms, background 120ms;
            padding: 0;
        }
        .chat-input-form button[type="submit"]:hover,
        .send-btn:hover { transform: scale(1.05); }
        .chat-input-form button[type="submit"]:disabled,
        .send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* Emoji picker popup — Web Component from emoji-picker-element.
           The button toggles `.open` on the wrapper; we position it
           absolutely above the composer so it sits over the message
           thread rather than pushing layout around. */
        .emoji-picker-wrapper {
            position: absolute;
            bottom: 68px;
            left: 18px;
            display: none;
            z-index: 25;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            border-radius: 12px;
            overflow: hidden;
        }
        .emoji-picker-wrapper.open { display: block; }
        /* Theme the picker to match the dark chat panel. The custom
           element exposes a set of CSS custom properties per its docs. */
        .emoji-picker-wrapper emoji-picker {
            --background: var(--ev-panel-2);
            --border-color: var(--ev-border);
            --input-border-color: var(--ev-border);
            --input-background: var(--ev-panel-3);
            --input-font-color: var(--ev-text);
            --input-placeholder-color: var(--ev-text-3);
            --category-emoji-padding: 6px;
            --num-columns: 8;
            --emoji-size: 1.15rem;
            height: 340px;
            width: 320px;
        }

        /* Attachment menu popup (Photos & Videos / Camera / …) ---------- */
        .attach-menu {
            position: absolute;
            bottom: 76px;
            left: 18px;
            background: var(--ev-panel-2);
            border: 1px solid var(--ev-border);
            border-radius: var(--ev-r-card);
            padding: 8px;
            min-width: 220px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            z-index: 20;
            display: none;
        }
        .attach-menu.open { display: block; }
        .attach-menu button {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            background: transparent;
            border: none;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--ev-text);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 120ms;
        }
        .attach-menu button:hover { background: var(--ev-hover); }
        .attach-menu button .icon {
            width: 32px; height: 32px;
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 14px;
        }
        .attach-menu .icon.photos   { background: #3b82f6; }
        .attach-menu .icon.camera   { background: #ec4899; }
        .attach-menu .icon.audio    { background: #f97316; }
        .attach-menu .icon.contact  { background: #0ea5e9; }
        .attach-menu .icon.location { background: #22c55e; }
        .attach-menu .icon.docs     { background: #4338ca; }

        /* Attachment preview strip above the input. Hidden by default —
           the JS attachment upload path toggles .open when a file is
           actively being uploaded. Was leaking a permanent "Uploading…"
           banner because there was no display:none baseline. */
        .attachment-preview {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: var(--ev-panel-3);
            border-radius: var(--ev-r-card);
            margin: 0 18px 10px;
        }
        .attachment-preview.open { display: flex; }
        .attachment-preview img { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; }
        .attachment-preview .remove {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            border: none;
            color: var(--ev-text);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .attachment-preview .remove:hover { background: rgba(239,68,68,0.25); }

        /* Voice recording bar — hidden by default, JS toggles .open
           while a recording is actively being captured. Same permanent-
           display bug as the attachment preview above. */
        .voice-recording-bar {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 6px 14px;
            background: var(--ev-panel-3);
            border-radius: var(--ev-r-pill);
            color: var(--ev-text);
            font-size: 13px;
            flex: 1;
        }
        .voice-recording-bar.active { display: flex; }
        .voice-recording-bar .rec-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: var(--ev-danger);
            animation: recPulse 1s ease-in-out infinite;
        }
        @keyframes recPulse { 50% { opacity: 0.3; } }

        /* --------------- Empty states -------------------------------- */
        .chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 30px;
            color: var(--ev-text-2);
            /* Match .chat-messages bg so opening a conversation doesn't
               cause the right panel to visibly shift shade. */
            background: #0D1011;
        }
        .chat-empty-icon {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: var(--ev-panel-3);
            display: flex; align-items: center; justify-content: center;
            font-size: 34px;
            margin-bottom: 16px;
            color: var(--ev-text-2);
        }
        .chat-empty h3 {
            color: var(--ev-text);
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 6px;
        }
        .chat-empty p {
            font-size: 14px;
            margin: 0;
            max-width: 300px;
        }

        /* Two large action cards centred in the empty state — replace
           the "Select a conversation" prompt. Buttons look like WhatsApp
           / iMessage's "quick action" tiles. */
        .empty-actions {
            display: flex;
            gap: 32px;
            align-items: center;
            justify-content: center;
        }
        .empty-action-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            padding: 0;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--ev-text);
        }
        .empty-action-card .empty-action-icon {
            width: 140px;
            height: 140px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--ev-panel-2);
            border-radius: 24px;
            color: #fff;
            transition: background 150ms, transform 150ms;
        }
        .empty-action-card:hover .empty-action-icon {
            background: var(--ev-panel-3);
            transform: translateY(-2px);
        }
        .empty-action-card .empty-action-label {
            font-size: 15px;
            font-weight: 500;
            color: var(--ev-text);
        }

        /* Dropzone overlay -------------------------------------------- */
        .chat-dropzone {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.75);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 40;
            pointer-events: none;
        }
        .chat-dropzone.active { display: flex; }
        .chat-dropzone-inner {
            border: 2px dashed var(--ev-lime);
            border-radius: var(--ev-r-card);
            padding: 40px 60px;
            text-align: center;
            color: var(--ev-text);
            background: rgba(15,16,17,0.9);
        }
        .chat-dropzone-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; color: var(--ev-lime); }
        .chat-dropzone-sub { font-size: 13px; color: var(--ev-text-2); }

        /* --------------- Scrollbars ---------------------------------- */
        .conversation-list::-webkit-scrollbar,
        .chat-messages::-webkit-scrollbar { width: 6px; }
        .conversation-list::-webkit-scrollbar-track,
        .chat-messages::-webkit-scrollbar-track { background: transparent; }
        .conversation-list::-webkit-scrollbar-thumb,
        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.08);
            border-radius: 4px;
        }
        .conversation-list::-webkit-scrollbar-thumb:hover,
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.16);
        }

        /* --------------- Responsive ---------------------------------
           Mobile treatment intentionally goes further than a naive
           column-flip: on a phone the chat replaces the whole screen
           (edge-to-edge, no shell padding, no rounded card), the "+"
           becomes a floating action button, and the header trims to
           just the primary controls. Matches the app-style mockups.
           -------------------------------------------------------------- */
        @media (max-width: 900px) {
            /* Full-viewport shell: kill desktop card frame + margins so
               the chat fills the phone screen edge-to-edge. */
            .ev-chat-shell {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }
            .ev-chat-shell > .mb-3 {
                border-radius: 0;
                padding: 0;
                margin: 0 !important;
            }

            .chat-container {
                flex-direction: column;
                /* Full viewport on mobile — the site's global bottom
                   nav is hidden on this page (see body rule above), so
                   the chat's own icon-strip nav sits at the true
                   viewport bottom like a native app. 100dvh handles
                   the collapsing address-bar on mobile browsers;
                   100vh is the fallback for older UAs. */
                height: 100vh;
                height: 100dvh;
                min-height: 0;
                border-radius: 0;
            }
            .chat-sidebar {
                width: 100%;
                min-width: 0;
                /* On mobile the container is flex-column, so the sidebar
                   needs flex:1 to fill the viewport height. Without this
                   it collapsed to content height and the bottom nav
                   would float up mid-screen on tabs with little content
                   (e.g. Messages with 2 rows), while working fine on
                   tabs with lots of content (e.g. Calls). min-height:0
                   is required so the inner .conversation-list can
                   actually scroll instead of blowing out the layout. */
                flex: 1;
                min-height: 0;
            }
            .chat-sidebar.mobile-hidden { display: none; }
            .chat-main {
                display: none;
                border-radius: 0;
            }
            .chat-sidebar:not(.mobile-hidden) ~ .chat-main { display: none; }
            .chat-sidebar.mobile-hidden ~ .chat-main { display: flex; }

            /* Sidebar header — tighter padding on mobile. */
            .chat-sidebar-header {
                padding: 12px 12px 10px;
                gap: 10px;
            }

            /* Hide desktop-only header actions on mobile — the mockup
               keeps only the primary action ("+") as a floating button. */
            .chat-sidebar-title-actions button[title="Expand panel"],
            .chat-sidebar-title-actions button[title="More"] {
                display: none;
            }

            /* Turn the "+" action into a floating action button (FAB)
               anchored to the bottom-right of the viewport, hovering
               above the bottom tab strip — WhatsApp-app style. */
            .chat-sidebar-title-actions .new-chat-wrap {
                position: fixed;
                right: 18px;
                /* Above the ~72px bottom tab strip + iOS safe-area,
                   so the FAB never overlaps the nav icons. */
                bottom: calc(90px + env(safe-area-inset-bottom, 0px));
                z-index: 45;
            }
            .chat-sidebar-title-actions button.new-chat-btn {
                width: 56px;
                height: 56px;
                font-size: 22px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            }
            .chat-sidebar-title-actions button.new-chat-btn svg {
                width: 22px;
                height: 22px;
            }
            /* Hide FAB on tabs where "+" has no meaning (calls / gallery /
               settings) so it doesn't linger as a dead button. Alpine
               toggles `.mobile-fab-hide` from the tab state. */
            .new-chat-wrap.mobile-fab-hide { display: none; }
            /* Status "+" dropdown positions above the FAB. */
            .chat-sidebar-title-actions .new-chat-wrap .new-chat-menu {
                bottom: calc(100% + 8px);
                top: auto;
            }

            /* Bottom nav strip — pin to viewport bottom on mobile so it
               doesn't get pushed off screen when the conversation list
               is long. Icons enlarged to native-app scale so they hit
               a proper tap target and read at arm's length. */
            .chat-sidebar-tabs {
                position: sticky;
                bottom: 0;
                padding: 12px 6px calc(12px + env(safe-area-inset-bottom, 0px));
                background: var(--ev-panel-2);
                border-top: 1px solid var(--ev-border);
            }
            .chat-sidebar-tab {
                width: 52px;
                height: 48px;
                border-radius: 14px;
            }
            .chat-sidebar-tab svg {
                width: 22px;
                height: 22px;
            }
            .chat-sidebar-tab .tab-badge {
                top: 2px; right: 4px;
                min-width: 18px; height: 18px;
                font-size: 11px;
            }

            /* --------- Chat thread (right panel on mobile) --------- */
            .chat-header {
                padding: 10px 12px;
                gap: 8px;
            }
            .chat-header-avatar { width: 38px; height: 38px; }
            .chat-header-name { font-size: 14px; }
            .chat-header-email { font-size: 11px; }
            .chat-call-buttons button,
            .chat-header-actions > button,
            .chat-header-menu > button {
                width: 36px;
                height: 36px;
                font-size: 15px;
            }

            /* Icon-only mobile back button — the "Back" label is hidden
               (wrapped in .mobile-back-label span) so only the caret shows. */
            .mobile-back-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                padding: 0;
                font-size: 20px;
                margin-right: 2px;
            }
            .mobile-back-label { display: none; }

            /* Messages — allow bubbles to breathe on narrow screens. */
            .chat-messages { padding: 16px 12px; }
            .message-bubble { max-width: 82%; font-size: 14px; }

            /* Composer — green circular send button matching the mockup,
               tighter padding to keep the input area from eating too much
               vertical space. */
            .chat-input { padding: 8px 10px 12px; }
            .chat-input-form { padding: 4px 6px 4px 12px; gap: 6px; }
            .chat-input-form button[type="submit"] {
                background: var(--ev-online);
                width: 40px;
                height: 40px;
            }
            .chat-input-form button[type="submit"]:hover {
                background: #1ba954;
                transform: none;
            }

            /* Empty-state cards shrink so both fit side-by-side on a
               phone screen without wrapping. */
            .empty-actions { gap: 20px; }
            .empty-action-card .empty-action-icon {
                width: 110px;
                height: 110px;
                border-radius: 20px;
            }

            /* Search pill margin — same as sidebar padding so it aligns. */
            .chat-search input { padding: 10px 14px 10px 40px; font-size: 14px; }
        }

        /* Very narrow phones — extra squeeze. */
        @media (max-width: 380px) {
            .chat-sidebar-title-actions button.new-chat-btn {
                width: 52px; height: 52px;
            }
            .empty-actions { gap: 12px; }
            .empty-action-card .empty-action-icon { width: 96px; height: 96px; }
        }
    </style>
    @endpush

    {{-- On the chat page we drop the shared communication-nav strip (that
         "Communication" title + Messages/Calls/Questions/Reviews/Favorites
         tabs) and give the whole page a WhatsApp-Web feel: one big dark
         rounded shell floating on a pale-grey backdrop. Category navigation
         is preserved as the icon strip at the bottom of the sidebar. --}}
    <div class="ev-chat-shell">
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
                         from Reverb (see initEchoListener below) so no polling.

                         `x-data.tab` mirrors the Livewire $activeTab prop
                         with @entangle. Bottom-nav clicks flip `tab`
                         instantly on the client (no round-trip needed to
                         see the sidebar switch), and Livewire syncs the
                         backing state async in the background so refreshes
                         land on the right tab. Combined with x-show below
                         it gives WhatsApp-app-feel navigation. --}}
                    {{-- data-tab / data-has-* are rendered server-side by
                         Blade AND kept in sync client-side by Alpine
                         :data-*. CSS attribute selectors above use these
                         to decide which right-panel view is visible,
                         which is more reliable than x-cloak+x-show
                         (which can stick hidden if Alpine is out of
                         sync after a Livewire DOM morph — that's what
                         caused the mobile black-screen bug). --}}
                    <div class="chat-container"
                         data-tab="{{ $activeTab }}"
                         data-has-conv="{{ $selectedConversationId ? '1' : '0' }}"
                         data-has-status="{{ $selectedStatus ? '1' : '0' }}"
                         data-has-section="{{ $settingsSection ? '1' : '0' }}"
                         x-data="{
                            tab: @entangle('activeTab'),
                            selConv: @entangle('selectedConversationId'),
                            selStatus: @entangle('selectedStatusId'),
                            section: @entangle('settingsSection')
                         }"
                         :data-tab="tab"
                         :data-has-conv="selConv ? '1' : '0'"
                         :data-has-status="selStatus ? '1' : '0'"
                         :data-has-section="section ? '1' : '0'">
                        {{-- Thin progress bar across the top of the chat
                             container while any Livewire request is in
                             flight. Gives instant visual feedback so tab
                             switches feel responsive even if the server
                             round-trip takes a moment. --}}
                        <div wire:loading class="chat-loading-bar"></div>
                        {{-- Left Sidebar - Conversations
                             .mobile-hidden triggers the mobile CSS that
                             hides the sidebar and reveals chat-main. It
                             fires whenever ANY detail view is active —
                             an open conversation, an opened Settings
                             sub-section, or a Status viewer — because
                             on a phone the detail view needs the full
                             screen and the tab list is not useful. --}}
                        <div class="chat-sidebar {{ ($selectedConversationId || $settingsSection || $selectedStatus) ? 'mobile-hidden' : '' }} {{ $newChatOpen ? 'new-chat-open' : '' }}" id="chatSidebar">
                            <div class="chat-sidebar-header">
                                @php
                                    // Badge counts still used by the bottom
                                    // icon nav below, so keep computing them.
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

                                {{-- Sidebar title bar: "Messages" + action icons.
                                     The three icons mirror the mockup: expand
                                     panel, options menu, and a lime "new
                                     message" round button. --}}
                                <div class="chat-sidebar-title">
                                    {{-- Client-side reactive title so it
                                         flips the instant the tab button is
                                         clicked, without waiting for the
                                         Livewire response. --}}
                                    <span x-text="({chats:'Messages',calls:'Calls',status:'Status',gallery:'Media',settings:'Settings'})[tab] || 'Messages'">
                                        @php
                                            $titleMap = ['calls' => 'Calls', 'status' => 'Status', 'gallery' => 'Media', 'settings' => 'Settings'];
                                        @endphp
                                        {{ $titleMap[$activeTab] ?? 'Messages' }}
                                    </span>
                                    <div class="chat-sidebar-title-actions">
                                        <button type="button" title="Expand panel" onclick="document.getElementById('chatSidebar')?.classList.toggle('expanded');">
                                            <i class="fa fa-expand"></i>
                                        </button>
                                        <button type="button" title="More">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        {{-- Lime round button — its behavior depends on the
                                             active tab. On Chats it opens the New-chat
                                             directory; on Status it opens the media/text
                                             add-status dropdown.

                                             On mobile this element is repositioned as a
                                             floating action button by the responsive CSS,
                                             and hidden on tabs where "+" has no meaning
                                             (calls, gallery, settings) via mobile-fab-hide. --}}
                                        <div class="new-chat-wrap" :class="{ 'mobile-fab-hide': tab !== 'chats' && tab !== 'status' }">
                                            <button type="button" class="new-chat-btn"
                                                    @if($activeTab === 'status')
                                                        onclick="this.closest('.new-chat-wrap').classList.toggle('open')"
                                                    @else
                                                        wire:click="openNewChat"
                                                    @endif
                                                    title="{{ $activeTab === 'status' ? 'Add status' : 'New chat' }}">
                                                <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.37611 11.21C-0.803888 5.857 3.13411 0 8.91411 0H9.23511C13.5531 0 17.0531 3.5 17.0531 7.818C17.0534 8.96478 16.8277 10.1004 16.389 11.1599C15.9502 12.2194 15.307 13.1821 14.4962 13.993C13.6853 14.8039 12.7225 15.4471 11.663 15.8859C10.6035 16.3246 9.46789 16.5503 8.32111 16.55H0.501112C0.397469 16.5502 0.296315 16.5182 0.211644 16.4585C0.126972 16.3987 0.0629685 16.3141 0.0284854 16.2164C-0.0059978 16.1186 -0.00925578 16.0126 0.0191622 15.9129C0.0475802 15.8132 0.10627 15.7249 0.187112 15.66L2.15911 14.077C2.24318 14.0095 2.3032 13.9167 2.33023 13.8124C2.35726 13.7081 2.34986 13.5978 2.30911 13.498L1.37611 11.21ZM9.65311 5.45C9.65311 5.25109 9.57409 5.06032 9.43344 4.91967C9.29279 4.77902 9.10203 4.7 8.90311 4.7C8.7042 4.7 8.51343 4.77902 8.37278 4.91967C8.23213 5.06032 8.15311 5.25109 8.15311 5.45V7.45H6.15311C5.9542 7.45 5.76343 7.52902 5.62278 7.66967C5.48213 7.81032 5.40311 8.00109 5.40311 8.2C5.40311 8.39891 5.48213 8.58968 5.62278 8.73033C5.76343 8.87098 5.9542 8.95 6.15311 8.95H8.15311V10.95C8.15311 11.1489 8.23213 11.3397 8.37278 11.4803C8.51343 11.621 8.7042 11.7 8.90311 11.7C9.10203 11.7 9.29279 11.621 9.43344 11.4803C9.57409 11.3397 9.65311 11.1489 9.65311 10.95V8.95H11.6531C11.852 8.95 12.0428 8.87098 12.1834 8.73033C12.3241 8.58968 12.4031 8.39891 12.4031 8.2C12.4031 8.00109 12.3241 7.81032 12.1834 7.66967C12.0428 7.52902 11.852 7.45 11.6531 7.45H9.65311V5.45Z" fill="currentColor"/>
                                                </svg>
                                            </button>
                                            @if($activeTab === 'status')
                                                <div class="new-chat-menu">
                                                    <button type="button" onclick="document.getElementById('statusMediaInput')?.click(); this.closest('.new-chat-wrap').classList.remove('open');">
                                                        <i class="fa fa-image"></i> Photos &amp; Videos
                                                    </button>
                                                    <button type="button" wire:click="openTextStatus" onclick="this.closest('.new-chat-wrap').classList.remove('open')">
                                                        <i class="fa fa-pen"></i> Text
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- Hidden file input for status media upload —
                                     lives at page scope so the dropdown button
                                     can trigger it without Livewire remounts. --}}
                                <input type="file" id="statusMediaInput" accept="image/*,video/*" style="display:none">
                                @if($textStatusOpen)
                                    <div class="text-status-overlay" wire:click.self="closeTextStatus">
                                        <div class="text-status-dialog" style="background: {{ $textStatusBg }};">
                                            <button type="button" class="text-status-close" wire:click="closeTextStatus" title="Close"><i class="fa fa-times"></i></button>
                                            <textarea wire:model.live="textStatusContent"
                                                      placeholder="Type your status..."
                                                      autofocus></textarea>
                                            <div class="text-status-bg-row">
                                                @foreach(['#075E54', '#128C7E', '#25D366', '#34B7F1', '#7c3aed', '#ec4899', '#f59e0b', '#0a0a0a'] as $c)
                                                    <button type="button" class="text-status-swatch {{ $textStatusBg === $c ? 'active' : '' }}"
                                                            style="background: {{ $c }}"
                                                            wire:click="$set('textStatusBg', '{{ $c }}')"></button>
                                                @endforeach
                                            </div>
                                            <button type="button" class="text-status-post" wire:click="postTextStatus" @if(trim($textStatusContent) === '') disabled @endif>
                                                <i class="fa fa-paper-plane"></i> Post
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                {{-- WhatsApp-style search: rounded pill with a search icon inside --}}
                                <div class="chat-search">
                                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <input
                                        type="text"
                                        placeholder="{{ $activeTab === 'settings' ? 'Search Settings' : 'Search conversations...' }}"
                                        wire:model.live.debounce.300ms="searchTerm">
                                    @if($searchResults && $searchResults->count() > 0)
                                        <div class="search-results">
                                            @foreach($searchResults as $user)
                                                <div class="search-result-item" wire:click="startConversation({{ $user->id }})">
                                                    <div class="conversation-avatar">
                                                        @if($user->avatar)
                                                            <img src="{{ user_avatar_url($user) }}" alt="">
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
                            
                            {{-- Sidebar body — swaps between the Chats
                                 conversation list and the Calls history
                                 based on which bottom-nav tab is active.
                                 Same $activeTab drives the title above. --}}
                            {{-- All tab bodies live in the DOM at once and
                                 are toggled with x-show driven by the
                                 Alpine `tab` variable. That way clicking
                                 the bottom nav is a pure client-side
                                 switch (zero server round-trip) — matching
                                 WhatsApp/Telegram tab feel. --}}
                            <div class="conversation-list">
                                <div x-show="tab === 'chats'">
                                    @php
                                        $supportConvs = collect($conversations)->filter(fn($c) => !empty($c['is_support']));
                                        $pinnedConvs  = collect($conversations)->filter(fn($c) => empty($c['is_support']) && !empty($c['is_pinned']));
                                        $recentConvs  = collect($conversations)->filter(fn($c) => empty($c['is_support']) && empty($c['is_pinned']));
                                    @endphp

                                    @if($supportConvs->isNotEmpty())
                                        <div class="conv-section-header">Support</div>
                                        @foreach($supportConvs as $conversation)
                                            @include('livewire.partials._conv-item', ['conversation' => $conversation])
                                        @endforeach
                                    @endif

                                    {{-- Favourites always shown so the "Add Favorite"
                                         tile is always available even when nothing
                                         is pinned yet. --}}
                                    <div class="conv-section-header">Favourites</div>
                                    <div class="add-favorite-row" wire:click="/* wire when favourites API lands */">
                                        <span class="add-favorite-btn" title="Add favorite">
                                            <i class="fa fa-user-plus"></i>
                                        </span>
                                        <span class="add-favorite-label">Add Favorite</span>
                                    </div>
                                    @foreach($pinnedConvs as $conversation)
                                        @include('livewire.partials._conv-item', ['conversation' => $conversation])
                                    @endforeach

                                    @if($recentConvs->isNotEmpty())
                                        <div class="conv-section-header">Recent</div>
                                        @foreach($recentConvs as $conversation)
                                            @include('livewire.partials._conv-item', ['conversation' => $conversation])
                                        @endforeach
                                    @endif

                                    @if(count($conversations) === 0)
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
                                    @endif
                                </div>
                                <div x-show="tab === 'status'" x-cloak>
                                    {{-- Status tab — My status card on top,
                                         then Recent list of everyone else's
                                         active statuses (last 24 h). Clicking
                                         "My status" opens the per-post list
                                         if the user has statuses; otherwise
                                         triggers the file picker. --}}
                                    <div class="status-my-card"
                                         @if($statusFeed['mine']->isNotEmpty())
                                             wire:click="openMyStatusList"
                                         @else
                                             onclick="document.getElementById('statusMediaInput')?.click();"
                                         @endif>
                                        <div class="status-my-avatar {{ $statusFeed['mine']->isNotEmpty() ? 'has-status' : '' }}">
                                            @php $me = auth()->user(); @endphp
                                            @if($me && $me->avatar)
                                                <img src="{{ user_avatar_url($me) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($me->name ?? '?', 0, 1)) }}
                                            @endif
                                            <span class="status-my-plus"><i class="fa fa-plus"></i></span>
                                        </div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">My status</div>
                                            <div class="conversation-preview">
                                                @if($statusFeed['mine']->isNotEmpty())
                                                    {{ $statusFeed['mine']->count() }} update{{ $statusFeed['mine']->count() === 1 ? '' : 's' }}
                                                @else
                                                    Click to add status update
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- "My statuses" per-post list — appears
                                         directly below the summary card when
                                         expanded. Shows a thumbnail, type
                                         label, view count and timestamp. --}}
                                    @if($myStatusListOpen && $statusFeed['mine']->isNotEmpty())
                                        <div class="my-statuses-head">
                                            <button type="button" class="my-statuses-back" wire:click="closeMyStatusList" title="Back">
                                                <i class="fa fa-angle-left"></i>
                                            </button>
                                            <span>My statuses</span>
                                        </div>
                                        @foreach($statusFeed['mine'] as $s)
                                            <div class="my-status-row {{ $selectedStatusId == $s->id ? 'active' : '' }}"
                                                 wire:key="mine-{{ $s->id }}"
                                                 wire:click="viewStatus({{ $s->id }})">
                                                <div class="my-status-thumb">
                                                    @if($s->type === 'photo' && $s->media_path)
                                                        <img src="{{ smart_asset('storage/' . $s->media_path) }}" alt="">
                                                    @elseif($s->type === 'video' && $s->media_path)
                                                        <video src="{{ smart_asset('storage/' . $s->media_path) }}" muted></video>
                                                        <span class="my-status-thumb-play"><i class="fa fa-play"></i></span>
                                                    @else
                                                        <div class="my-status-thumb-text" style="background: {{ $s->background_color ?: '#075E54' }};">
                                                            <i class="fa fa-font"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="my-status-body">
                                                    <div class="my-status-label">
                                                        @if($s->type === 'photo')   Photo
                                                        @elseif($s->type === 'video') Video
                                                        @else                        Text
                                                        @endif
                                                    </div>
                                                    <div class="my-status-meta">
                                                        <span><i class="fa fa-eye"></i> {{ $s->views_count ?? 0 }}</span>
                                                    </div>
                                                </div>
                                                <div class="my-status-time">
                                                    {{ $s->created_at->format('h:i A') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($statusFeed['others']->isNotEmpty())
                                        <div class="conv-section-header">Recent</div>
                                        @foreach($statusFeed['others'] as $entry)
                                            <div class="conversation-item status-row" wire:key="stat-{{ $entry['user_id'] }}"
                                                 wire:click="viewStatus({{ $entry['latest_id'] }})">
                                                <div class="conversation-avatar status-ring {{ $entry['unseen'] > 0 ? 'unseen' : 'seen' }}">
                                                    @if(!empty($entry['user_avatar']))
                                                        <img src="{{ $entry['user_avatar_url'] }}" alt="">
                                                    @else
                                                        {{ strtoupper(substr($entry['user_name'] ?? '?', 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div class="conversation-info">
                                                    <div class="conversation-name">{{ $entry['user_name'] }}</div>
                                                    <div class="conversation-preview">
                                                        {{ \Carbon\Carbon::parse($entry['latest_at'])->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif(!$myStatusListOpen)
                                        <div class="chat-empty" style="padding: 40px 20px; background: transparent;">
                                            <p style="font-size:12px; color:var(--ev-text-3);">No recent statuses from other users</p>
                                        </div>
                                    @endif
                                </div>
                                <div x-show="tab === 'settings'" x-cloak>
                                    {{-- Settings tab — user profile card
                                         at top, then a list of setting
                                         sub-sections. Each row opens its
                                         pane on the right. --}}
                                    @php $me = auth()->user(); @endphp
                                    <div class="settings-profile-card">
                                        <div class="settings-profile-avatar">
                                            @if($me && $me->avatar)
                                                <img src="{{ user_avatar_url($me) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($me->name ?? $me->email ?? '?', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="settings-profile-name">{{ $me->name ?? $me->email ?? 'Guest' }}</div>
                                    </div>

                                    @php
                                        $settingsItems = [
                                            ['id' => 'account',       'icon' => 'fa-key',            'title' => 'Account',              'sub' => 'Security notifications, account info'],
                                            ['id' => 'chats',         'icon' => 'fa-comment',        'title' => 'Chats',                'sub' => 'Theme, wallpaper, chat settings'],
                                            ['id' => 'notifications', 'icon' => 'fa-bell',           'title' => 'Notification and Sound','sub' => 'Ringtone, message tone, alerts'],
                                            ['id' => 'video',         'icon' => 'fa-video',          'title' => 'Video & voice',        'sub' => 'Camera, microphone & speakers'],
                                        ];
                                    @endphp
                                    @foreach($settingsItems as $item)
                                        <div class="settings-row {{ $settingsSection === $item['id'] ? 'active' : '' }}"
                                             wire:key="set-{{ $item['id'] }}"
                                             wire:click="setSettingsSection('{{ $item['id'] }}')">
                                            <div class="settings-row-icon"><i class="fa {{ $item['icon'] }}"></i></div>
                                            <div class="settings-row-body">
                                                <div class="settings-row-title">{{ $item['title'] }}</div>
                                                <div class="settings-row-sub">{{ $item['sub'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div x-show="tab === 'gallery'" x-cloak>
                                    {{-- Gallery tab — grid of every photo /
                                         video shared or received across all
                                         conversations. Clicking a thumbnail
                                         opens the parent chat on the right. --}}
                                    <div class="gallery-intro">
                                        Photos and videos shared or received. Click any item to open the chat.
                                    </div>
                                    @if($galleryMedia->isNotEmpty())
                                        <div class="gallery-grid">
                                            @foreach($galleryMedia as $m)
                                                <div class="gallery-tile"
                                                     wire:key="gal-{{ $m['id'] }}"
                                                     wire:click="openChatFromGallery({{ $m['conversation_id'] }}, {{ $m['id'] }})">
                                                    @if($m['type'] === 'video')
                                                        <video src="{{ $m['url'] }}" muted preload="metadata"></video>
                                                        <span class="gallery-tile-badge"><i class="fa fa-play"></i></span>
                                                    @else
                                                        <img src="{{ $m['url'] }}" alt="" loading="lazy">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="chat-empty" style="padding: 40px 20px; background: transparent;">
                                            <p style="font-size:13px; color:var(--ev-text-3);">No shared media yet.</p>
                                        </div>
                                    @endif
                                </div>
                                <div x-show="tab === 'calls'" x-cloak>
                                    {{-- Calls tab — Favourites (empty for now,
                                         reuses the same Add Favorite tile as Chats)
                                         plus Recent call history. --}}
                                    <div class="conv-section-header">Favourites</div>
                                    <div class="add-favorite-row">
                                        <span class="add-favorite-btn" title="Add favorite">
                                            <i class="fa fa-user-plus"></i>
                                        </span>
                                        <span class="add-favorite-label">Add Favorite</span>
                                    </div>

                                    @if($callHistory->isNotEmpty())
                                        <div class="conv-section-header">Recent</div>
                                        @foreach($callHistory as $call)
                                            <div class="conversation-item call-row {{ $call['status'] === 'missed' && !$call['is_outgoing'] ? 'missed' : '' }}"
                                                 wire:key="call-{{ $call['id'] }}"
                                                 @if($call['peer_id']) wire:click="openChatFromCall({{ $call['peer_id'] }})" @endif>
                                                <div class="conversation-avatar">
                                                    @if(!empty($call['peer_avatar']))
                                                        <img src="{{ $call['peer_avatar_url'] }}" alt="">
                                                    @else
                                                        {{ strtoupper(substr($call['peer_name'] ?? '?', 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div class="conversation-info">
                                                    <div class="conversation-name">{{ $call['peer_name'] }}</div>
                                                    <div class="call-row-meta">
                                                        @if($call['status'] === 'missed' && !$call['is_outgoing'])
                                                            <i class="fa {{ $call['type'] === 'video' ? 'fa-video' : 'fa-phone' }}"></i>
                                                            <span>Missed</span>
                                                        @elseif($call['is_outgoing'])
                                                            <i class="fa {{ $call['type'] === 'video' ? 'fa-video' : 'fa-phone' }}"></i>
                                                            <span>Outgoing</span>
                                                        @else
                                                            <i class="fa {{ $call['type'] === 'video' ? 'fa-video' : 'fa-phone' }}"></i>
                                                            <span>Incoming</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="conversation-meta">
                                                    <div class="conversation-time">
                                                        {{ \Carbon\Carbon::parse($call['started_at'])->format('h:i A') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="chat-empty" style="padding: 40px 20px;">
                                            <div class="chat-empty-icon"><i class="fa fa-phone"></i></div>
                                            <h3>No calls yet</h3>
                                            <p>Your call history will appear here.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Icon-strip bottom nav — replaces the removed
                                 outer communication-nav. Every icon is a
                                 wire:navigate link to the corresponding
                                 category page so state (open call, etc.)
                                 survives the transition. --}}
                            <div class="chat-sidebar-tabs">
                                <button type="button" @click="tab = 'chats'" wire:click="setActiveTab('chats')" :class="{ 'active': tab === 'chats' }" class="chat-sidebar-tab" title="Chats">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M1.53846 16.8005L4.26683 14.6154H16.9231C17.3311 14.6154 17.7224 14.4533 18.0109 14.1648C18.2995 13.8763 18.4615 13.485 18.4615 13.077V3.07695C18.4615 2.66893 18.2995 2.27761 18.0109 1.9891C17.7224 1.70058 17.3311 1.53849 16.9231 1.53849H3.07692C2.6689 1.53849 2.27758 1.70058 1.98907 1.9891C1.70055 2.27761 1.53846 2.66893 1.53846 3.07695V16.8005ZM4.80769 16.1539L1.25 19C1.13684 19.0906 1.00038 19.1474 0.856346 19.1638C0.712315 19.1803 0.566576 19.1556 0.435927 19.0928C0.305279 19.03 0.195037 18.9316 0.11791 18.8088C0.0407831 18.6861 -9.05108e-05 18.544 1.50494e-07 18.3991V3.07695C1.50494e-07 2.2609 0.324175 1.47828 0.90121 0.90124C1.47825 0.324205 2.26087 3.05176e-05 3.07692 3.05176e-05H16.9231C17.7391 3.05176e-05 18.5218 0.324205 19.0988 0.90124C19.6758 1.47828 20 2.2609 20 3.07695V13.077C20 13.893 19.6758 14.6756 19.0988 15.2527C18.5218 15.8297 17.7391 16.1539 16.9231 16.1539H4.80769Z" fill="currentColor"/>
                                        <path d="M6.154 9.2308H13.8463C14.3591 9.2308 14.6155 9.48721 14.6155 10C14.6155 10.5129 14.3591 10.7693 13.8463 10.7693H6.154C5.64118 10.7693 5.38477 10.5129 5.38477 10C5.38477 9.48721 5.64118 9.2308 6.154 9.2308ZM6.154 4.61542H13.8463C14.3591 4.61542 14.6155 4.87183 14.6155 5.38465C14.6155 5.89747 14.3591 6.15388 13.8463 6.15388H6.154C5.64118 6.15388 5.38477 5.89747 5.38477 5.38465C5.38477 4.87183 5.64118 4.61542 6.154 4.61542Z" fill="currentColor"/>
                                    </svg>
                                    @if($chatUnreadCount > 0)
                                        <span class="tab-badge">{{ $chatUnreadCount }}</span>
                                    @endif
                                </button>
                                <button type="button" @click="tab = 'calls'" wire:click="setActiveTab('calls')" :class="{ 'active': tab === 'calls' }" class="chat-sidebar-tab" title="Calls">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M18.1891 14.8547C17.502 14.1624 15.8378 13.1521 15.0304 12.745C13.979 12.2154 13.8924 12.1721 13.0659 12.7861C12.5147 13.1958 12.1482 13.5619 11.503 13.4243C10.8579 13.2867 9.45591 12.5109 8.22833 11.2873C7.00075 10.0637 6.17991 8.62116 6.04188 7.9782C5.90385 7.33525 6.27597 6.9731 6.68185 6.42058C7.25388 5.64177 7.21061 5.51196 6.72166 4.46057C6.34045 3.64281 5.30066 1.99433 4.60574 1.3107C3.86236 0.576455 3.86236 0.706257 3.38336 0.905287C2.99338 1.06932 2.61925 1.26875 2.26568 1.50108C1.57336 1.96101 1.18912 2.34306 0.920411 2.91722C0.651703 3.49138 0.530979 4.83743 1.91866 7.35818C3.30633 9.87894 4.27992 11.1679 6.29501 13.1772C8.31011 15.1865 9.85962 16.2669 12.1248 17.5373C14.927 19.1066 16.0018 18.8007 16.5778 18.5324C17.1537 18.2642 17.5375 17.8834 17.9983 17.1911C18.2313 16.8382 18.4312 16.4645 18.5955 16.0748C18.7949 15.5976 18.9247 15.5976 18.1891 14.8547Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"/>
                                    </svg>
                                    @if($missedCallCount > 0 && $activeTab !== 'calls')
                                        <span class="tab-badge">{{ $missedCallCount }}</span>
                                    @endif
                                </button>
                                <button type="button" @click="tab = 'status'" wire:click="setActiveTab('status')" :class="{ 'active': tab === 'status' }" class="chat-sidebar-tab" title="Status">
                                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M18.5694 6.81786L18.5877 6.87039L18.6553 7.09736L18.7119 7.30107L18.7361 7.3941L18.7756 7.56252C19.2889 9.90945 18.9143 12.3661 17.7257 14.4465C16.5398 16.5334 14.6229 18.0922 12.3556 18.8135L12.1957 18.8616L11.9994 18.9129L11.7156 18.9811C11.6154 19.0042 11.5116 19.0062 11.4106 18.9868C11.3096 18.9674 11.2136 18.9272 11.1286 18.8686C11.0437 18.8099 10.9715 18.7342 10.9167 18.6461C10.862 18.5579 10.8257 18.4593 10.8102 18.3563C10.7784 18.145 10.826 17.9292 10.9437 17.7518C11.0614 17.5744 11.2406 17.4482 11.4457 17.3983L11.6741 17.3402C11.8166 17.3022 11.9391 17.2657 12.0417 17.2307C13.8626 16.6077 15.3964 15.3325 16.3549 13.6445C17.319 11.9578 17.6416 9.97224 17.2619 8.06137L17.2346 7.93586L17.1982 7.78949L17.1527 7.62187L17.0981 7.4334C17.0369 7.22781 17.0542 7.00643 17.1466 6.81316C17.239 6.6199 17.3996 6.46886 17.5967 6.38999C17.6883 6.35332 17.7861 6.33531 17.8846 6.33697C17.9831 6.33863 18.0802 6.35994 18.1706 6.39967C18.2609 6.43941 18.3426 6.49679 18.4111 6.56855C18.4795 6.6403 18.5333 6.72502 18.5694 6.81786ZM1.33976 6.38398C1.35691 6.38932 1.37379 6.39521 1.39041 6.40162C1.58708 6.47929 1.74777 6.62899 1.84064 6.82106C1.93351 7.01313 1.95177 7.23354 1.89181 7.43861C1.82058 7.68162 1.76913 7.88386 1.73748 8.04533C1.35944 9.95468 1.6824 11.9382 2.6457 13.6233C3.60507 15.3128 5.14069 16.5887 6.96361 17.2111L7.0847 17.25L7.15356 17.27L7.3079 17.3125L7.58254 17.3811C7.78887 17.4302 7.96943 17.5562 8.0884 17.7339C8.20737 17.9117 8.25607 18.1283 8.22483 18.3407C8.19551 18.5399 8.08933 18.7191 7.92962 18.839C7.76992 18.9588 7.56977 19.0095 7.3732 18.9799L7.31858 18.9699L7.09064 18.9157L6.88881 18.8636L6.71231 18.8135L6.63316 18.7894C4.37043 18.0662 2.45806 16.5086 1.27446 14.4249C0.0837235 12.3408 -0.290189 9.87928 0.226939 7.52884L0.257411 7.40052L0.296985 7.25014L0.373758 6.98348L0.403835 6.88323C0.432902 6.78814 0.48017 6.69979 0.542939 6.62322C0.605707 6.54665 0.682747 6.48335 0.769658 6.43696C0.856569 6.39056 0.951649 6.36196 1.04947 6.3528C1.14729 6.34364 1.24593 6.3545 1.33976 6.38398ZM9.5181 3.20803C13.0152 3.20803 15.8499 6.08042 15.8499 9.62408C15.8499 13.1677 13.0152 16.0401 9.5181 16.0401C6.02096 16.0401 3.18628 13.1677 3.18628 9.62408C3.18628 6.08042 6.02135 3.20803 9.5181 3.20803ZM9.5181 4.81204C6.89554 4.81204 4.76923 6.96663 4.76923 9.62408C4.76923 12.2815 6.89554 14.4361 9.5181 14.4361C12.1411 14.4361 14.267 12.2815 14.267 9.62408C14.267 6.96663 12.1411 4.81204 9.5181 4.81204ZM9.5181 7.22374e-06C11.8947 -0.00294597 14.1855 0.899685 15.9358 2.52873L16.0304 2.61976L16.1392 2.72964L16.2619 2.85876L16.3988 3.00673C16.4687 3.08311 16.5222 3.17326 16.5562 3.27155C16.5901 3.36984 16.6037 3.47417 16.5961 3.57801C16.5885 3.68186 16.5598 3.78301 16.5119 3.87514C16.464 3.96727 16.3979 4.04841 16.3177 4.1135C16.153 4.24723 15.9447 4.31337 15.7341 4.29881C15.5235 4.28426 15.326 4.19006 15.1807 4.0349C15.0947 3.94267 15.0149 3.8602 14.9413 3.78748L14.8028 3.65355L14.7395 3.5962C13.2967 2.30971 11.44 1.60129 9.5181 1.60402C7.59446 1.60119 5.73612 2.31084 4.29276 3.59941L4.24765 3.64031L4.14753 3.73655L4.03395 3.85084L3.83806 4.05736C3.5373 4.38177 3.04104 4.41826 2.69714 4.14157C2.62002 4.07953 2.55572 4.0027 2.50791 3.91546C2.4601 3.82823 2.42972 3.73231 2.41851 3.63317C2.4073 3.53404 2.41548 3.43364 2.44258 3.33771C2.46968 3.24178 2.51517 3.15221 2.57644 3.0741L2.61206 3.03159L2.77233 2.85876L2.91797 2.70718C2.98709 2.63687 3.05093 2.57471 3.1095 2.52071C4.8586 0.896831 7.14546 -0.00272003 9.51771 7.22374e-06" fill="currentColor"/>
                                    </svg>
                                </button>
                                <button type="button" @click="tab = 'gallery'" wire:click="setActiveTab('gallery')" :class="{ 'active': tab === 'gallery' }" class="chat-sidebar-tab" title="Gallery">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12.3006 12.4248C12.9348 12.4248 13.5247 12.3081 14.0701 12.0748C14.6156 11.8415 15.1058 11.5188 15.5408 11.1068C15.905 10.7606 16.2053 10.368 16.4418 9.92906C16.6784 9.49011 16.8422 9.01713 16.9332 8.51014C16.9705 8.35759 16.9354 8.22411 16.828 8.1097C16.7213 7.99529 16.5892 7.94967 16.4318 7.97286C15.9177 8.03268 15.4341 8.17476 14.9812 8.39909C14.5283 8.62343 14.1197 8.90908 13.7556 9.25605C13.3266 9.66733 12.9863 10.144 12.7348 10.6862C12.4834 11.2283 12.3386 11.8079 12.3006 12.4248ZM12.3006 12.4248C12.2632 11.8079 12.1189 11.2283 11.8674 10.6862C11.6159 10.144 11.2753 9.66733 10.8455 9.25605C10.4814 8.90983 10.0728 8.62455 9.61991 8.40021C9.16698 8.17588 8.68383 8.03343 8.17046 7.97286C8.01227 7.94967 7.87982 7.99529 7.77312 8.1097C7.66642 8.22411 7.63172 8.35759 7.66903 8.51014C7.7623 9.01713 7.93019 9.49011 8.1727 9.92906C8.41521 10.368 8.71853 10.7606 9.08266 11.1068C9.51172 11.5181 9.99673 11.8408 10.5377 12.0748C11.0787 12.3089 11.6663 12.4255 12.3006 12.4248ZM12.3006 9.05976C12.6177 9.05976 12.8837 8.95245 13.0986 8.73784C13.3135 8.52322 13.4206 8.25664 13.4198 7.93808V7.65767L13.6996 7.76983C13.9795 7.882 14.2641 7.91004 14.5536 7.85396C14.8432 7.79787 15.0622 7.63897 15.2107 7.37725C15.3785 7.09683 15.4345 6.79772 15.3785 6.47991C15.3226 6.1621 15.136 5.93851 14.8189 5.80915L14.5391 5.69922L14.8189 5.58818C15.136 5.45881 15.3215 5.23298 15.3752 4.91069C15.4297 4.58764 15.3748 4.28816 15.2107 4.01223C15.0577 3.74602 14.8447 3.58263 14.5716 3.52205C14.2984 3.46148 14.0078 3.49401 13.6996 3.61964L13.4198 3.73181V3.45139C13.4198 3.13358 13.3127 2.86737 13.0986 2.65276C12.8844 2.43814 12.6184 2.33046 12.3006 2.32972C11.9827 2.32897 11.717 2.43665 11.5036 2.65276C11.2902 2.86887 11.1828 3.13508 11.1813 3.45139V3.73181L10.9015 3.61964C10.5933 3.49252 10.3027 3.45962 10.0296 3.52093C9.75721 3.58225 9.54455 3.74602 9.39158 4.01223C9.22742 4.28891 9.1722 4.58802 9.22593 4.90956C9.27965 5.23261 9.46508 5.45881 9.78221 5.58818L10.062 5.69922L9.78221 5.80915C9.46508 5.93851 9.27853 6.1621 9.22257 6.47991C9.16661 6.79772 9.22257 7.09683 9.39046 7.37725C9.5397 7.63897 9.75907 7.79787 10.0486 7.85396C10.3381 7.91004 10.6224 7.882 10.9015 7.76983L11.1813 7.65767V7.93808C11.1813 8.25589 11.2887 8.52248 11.5036 8.73784C11.7185 8.9532 11.9842 9.0605 12.3006 9.05976ZM12.3006 6.81641C11.9834 6.81641 11.7178 6.7091 11.5036 6.49449C11.2895 6.27988 11.182 6.01329 11.1813 5.69474C11.1805 5.37618 11.288 5.10997 11.5036 4.8961C11.7193 4.68224 11.9849 4.57456 12.3006 4.57306C12.6162 4.57157 12.8822 4.67925 13.0986 4.8961C13.315 5.11296 13.4221 5.37917 13.4198 5.69474C13.4176 6.0103 13.3105 6.27689 13.0986 6.49449C12.8867 6.71209 12.6207 6.8194 12.3006 6.81641ZM6.74115 15.1C6.22629 15.1 5.79612 14.9269 5.45064 14.5806C5.10516 14.2344 4.93279 13.8033 4.93354 13.2873V1.81262C4.93354 1.29591 5.1059 0.86481 5.45064 0.519335C5.79537 0.173859 6.22554 0.000747782 6.74115 0H18.1913C18.7069 0 19.137 0.173112 19.4818 0.519335C19.8265 0.865558 19.9993 1.29665 20 1.81262V13.2873C20 13.8033 19.8273 14.2344 19.4818 14.5806C19.1363 14.9269 18.7061 15.1 18.1913 15.1H6.74115ZM6.74115 13.9783H18.1913C18.3927 13.9783 18.558 13.9136 18.6871 13.7842C18.8162 13.6549 18.8807 13.4892 18.8807 13.2873V1.81262C18.8807 1.61072 18.8162 1.44509 18.6871 1.31572C18.558 1.18636 18.3931 1.12167 18.1924 1.12167H6.74115C6.53969 1.12167 6.37478 1.18636 6.24644 1.31572C6.11735 1.44509 6.0528 1.61072 6.0528 1.81262V13.2873C6.0528 13.4892 6.11735 13.6549 6.24644 13.7842C6.37553 13.9136 6.54043 13.9783 6.74115 13.9783ZM3.41245 19.9849C2.91102 20.0454 2.46331 19.9243 2.06933 19.6214C1.67535 19.3186 1.44851 18.917 1.38882 18.4168L0.0143553 7.52194C-0.0453389 7.02018 0.0811383 6.5659 0.393787 6.15911C0.706436 5.75157 1.1131 5.52275 1.61379 5.47265L2.10067 5.45021C2.25961 5.4375 2.40026 5.48237 2.52263 5.58481C2.64575 5.68726 2.70731 5.8241 2.70731 5.99535C2.70731 6.13892 2.65956 6.26679 2.56405 6.37896C2.46854 6.49113 2.35288 6.55357 2.21707 6.56628L1.78056 6.58759C1.57909 6.60254 1.4209 6.68517 1.30599 6.83548C1.19108 6.98653 1.14817 7.16263 1.17727 7.36379L2.51592 18.2665C2.54427 18.4676 2.63008 18.6258 2.77335 18.7409C2.91736 18.8561 3.08973 18.8991 3.29045 18.8699L16.4676 17.2211C16.6273 17.1986 16.7683 17.2341 16.8907 17.3276C17.013 17.4211 17.0839 17.5478 17.1033 17.7079C17.1257 17.8671 17.0903 18.0036 16.997 18.1173C16.9037 18.2309 16.7776 18.2971 16.6187 18.3158L3.41245 19.9849Z" fill="currentColor"/>
                                    </svg>
                                </button>
                                <button type="button" @click="tab = 'settings'" wire:click="setActiveTab('settings')" :class="{ 'active': tab === 'settings' }" class="chat-sidebar-tab" title="Settings">
                                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M17.7623 11.9151L16.3723 10.7275C16.4381 10.3245 16.4721 9.91313 16.4721 9.50172C16.4721 9.09031 16.4381 8.67889 16.3723 8.27597L17.7623 7.08838C17.8672 6.99869 17.9422 6.87923 17.9775 6.74589C18.0127 6.61255 18.0065 6.47164 17.9597 6.3419L17.9406 6.28676C17.5579 5.21799 16.9849 4.22722 16.2492 3.36234L16.211 3.31781C16.1218 3.21294 16.0028 3.13756 15.8699 3.1016C15.7369 3.06564 15.5961 3.07078 15.4662 3.11634L13.7409 3.72922C13.1042 3.20753 12.3933 2.79612 11.6251 2.50771L11.2919 0.705127C11.2668 0.569493 11.2009 0.444712 11.1031 0.347363C11.0053 0.250014 10.8802 0.184705 10.7444 0.160112L10.6871 0.149508C9.58147 -0.049836 8.41853 -0.049836 7.3129 0.149508L7.2556 0.160112C7.11978 0.184705 6.99466 0.250014 6.89686 0.347363C6.79907 0.444712 6.73323 0.569493 6.70809 0.705127L6.37279 2.51619C5.61071 2.80468 4.90102 3.21587 4.27187 3.73346L2.53384 3.11634C2.40389 3.07041 2.26304 3.06509 2.12999 3.10107C1.99694 3.13706 1.878 3.21265 1.78897 3.31781L1.75077 3.36234C1.01596 4.22784 0.443048 5.21844 0.0594242 6.28676L0.0403249 6.3419C-0.0551714 6.60699 0.0233479 6.90388 0.237684 7.08838L1.64466 8.28869C1.57888 8.68738 1.54704 9.09455 1.54704 9.4996C1.54704 9.90677 1.57888 10.3139 1.64466 10.7105L0.237684 11.9108C0.132841 12.0005 0.0578039 12.12 0.0225499 12.2533C-0.0127041 12.3867 -0.00650436 12.5276 0.0403249 12.6573L0.0594242 12.7124C0.443532 13.7813 1.01227 14.7674 1.75077 15.6369L1.78897 15.6814C1.87821 15.7863 1.99716 15.8616 2.13013 15.8976C2.2631 15.9336 2.40385 15.9284 2.53384 15.8829L4.27187 15.2657C4.90427 15.7853 5.61094 16.1967 6.37279 16.483L6.70809 18.2941C6.73323 18.4297 6.79907 18.5545 6.89686 18.6518C6.99466 18.7492 7.11978 18.8145 7.2556 18.8391L7.3129 18.8497C8.42869 19.0501 9.57131 19.0501 10.6871 18.8497L10.7444 18.8391C10.8802 18.8145 11.0053 18.7492 11.1031 18.6518C11.2009 18.5545 11.2668 18.4297 11.2919 18.2941L11.6251 16.4915C12.393 16.2038 13.1079 15.7911 13.7409 15.27L15.4662 15.8829C15.5961 15.9288 15.737 15.9341 15.87 15.8981C16.0031 15.8621 16.122 15.7865 16.211 15.6814L16.2492 15.6369C16.9877 14.7653 17.5565 13.7813 17.9406 12.7124L17.9597 12.6573C18.0552 12.3965 17.9767 12.0996 17.7623 11.9151ZM14.8656 8.52621C14.9186 8.84643 14.9462 9.17513 14.9462 9.50384C14.9462 9.83255 14.9186 10.1613 14.8656 10.4815L14.7255 11.3319L16.3108 12.687C16.0705 13.2403 15.7671 13.764 15.4067 14.2478L13.4374 13.5501L12.771 14.0972C12.2639 14.5129 11.6994 14.8395 11.0882 15.0685L10.2797 15.3718L9.89979 17.4288C9.30043 17.4967 8.69532 17.4967 8.09597 17.4288L7.71611 15.3675L6.91394 15.06C6.30913 14.831 5.74676 14.5044 5.24381 14.0909L4.57746 13.5416L2.59538 14.2457C2.23462 13.7601 1.93327 13.2362 1.69135 12.6849L3.29357 11.317L3.15563 10.4688C3.1047 10.1528 3.07711 9.82618 3.07711 9.50384C3.07711 9.17938 3.10257 8.85491 3.15563 8.53893L3.29357 7.69066L1.69135 6.32282C1.93115 5.76932 2.23462 5.24763 2.59538 4.76199L4.57746 5.46606L5.24381 4.9168C5.74676 4.50327 6.30913 4.17668 6.91394 3.94765L7.71823 3.64439L8.09809 1.58309C8.69441 1.51523 9.30347 1.51523 9.90191 1.58309L10.2818 3.64015L11.0903 3.94341C11.6994 4.17244 12.266 4.49903 12.7732 4.91468L13.4395 5.46182L15.4089 4.76411C15.7696 5.24975 16.071 5.77356 16.3129 6.32494L14.7277 7.68005L14.8656 8.52621ZM9.00212 5.55937C6.9394 5.55937 5.26716 7.23047 5.26716 9.29177C5.26716 11.3531 6.9394 13.0242 9.00212 13.0242C11.0648 13.0242 12.7371 11.3531 12.7371 9.29177C12.7371 7.23047 11.0648 5.55937 9.00212 5.55937ZM10.6829 10.9714C10.4624 11.1923 10.2004 11.3675 9.91199 11.4868C9.62354 11.6062 9.31433 11.6674 9.00212 11.6669C8.3676 11.6669 7.77128 11.4188 7.32139 10.9714C7.10031 10.7511 6.925 10.4893 6.80554 10.201C6.68609 9.91277 6.62484 9.60376 6.62533 9.29177C6.62533 8.65769 6.87362 8.06178 7.32139 7.61219C7.77128 7.16261 8.3676 6.91661 9.00212 6.91661C9.63664 6.91661 10.233 7.16261 10.6829 7.61219C10.9039 7.83248 11.0792 8.09428 11.1987 8.38253C11.3182 8.67078 11.3794 8.97978 11.3789 9.29177C11.3789 9.92586 11.1306 10.5218 10.6829 10.9714Z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- New-chat directory overlay — absolutely
                                 positioned so it covers the sidebar (list,
                                 title, bottom nav) when open. Back arrow
                                 dismisses it; picking a user starts a
                                 conversation and closes. --}}
                            @if($newChatOpen)
                                <div class="new-chat-panel">
                                    <div class="new-chat-head">
                                        <button type="button" class="new-chat-back" wire:click="closeNewChat" title="Back">
                                            <i class="fa fa-arrow-left"></i>
                                        </button>
                                        <span class="new-chat-title">New chat</span>
                                    </div>
                                    <div class="new-chat-search">
                                        <i class="fa fa-search"></i>
                                        <input type="text"
                                               wire:model.live.debounce.250ms="newChatSearch"
                                               placeholder="Public name or @username"
                                               autofocus>
                                    </div>
                                    <div class="new-chat-help">
                                        A name finds public profiles. <b>@username</b> finds that username.
                                    </div>
                                    <div class="new-chat-list">
                                        @forelse($directoryUsers as $u)
                                            <div class="new-chat-row"
                                                 wire:key="dir-{{ $u->id }}"
                                                 wire:click="startNewChatWith({{ $u->id }})">
                                                <div class="new-chat-avatar">
                                                    @if($u->avatar)
                                                        <img src="{{ user_avatar_url($u) }}" alt="">
                                                    @else
                                                        <i class="fa fa-user"></i>
                                                    @endif
                                                </div>
                                                <div class="new-chat-info">
                                                    <div class="new-chat-name">{{ $u->name ?? $u->email }}</div>
                                                    <div class="new-chat-handle">{{ $u->username ? ('@' . $u->username) : ('@' . \Illuminate\Support\Str::slug($u->name ?? explode('@', $u->email)[0]) . '-' . $u->id) }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="new-chat-empty">
                                                <i class="fa fa-users"></i>
                                                <div>No users found</div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
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
                            {{-- Right-panel blocks below are structured as siblings
                                 (not an @if/@elseif chain) so all empty states
                                 live in the DOM at once and are toggled with
                                 Alpine x-show. That makes the right side switch
                                 instantly on tab click, same as the sidebar.
                                 Data-driven views (status viewer, chat thread,
                                 settings pane) remain server-conditional — they
                                 only render when the underlying data is loaded. --}}
                            @if($selectedStatus)
                                {{-- Status viewer — full-panel view of the
                                     selected status. Delete button if it's
                                     the current user's. --}}
                                {{-- Visibility controlled by
                                     .chat-container[data-tab="status"]
                                     .status-viewer in CSS. Server-gated
                                     by @if($selectedStatus). --}}
                                <div class="status-viewer">
                                    <div class="status-viewer-head">
                                        <div class="status-viewer-user">
                                            <div class="chat-header-avatar">
                                                @if($selectedStatus['user_avatar'])
                                                    <img src="{{ $selectedStatus['user_avatar_url'] }}" alt="">
                                                @else
                                                    {{ strtoupper(substr($selectedStatus['user_name'] ?? '?', 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="chat-header-name">{{ $selectedStatus['user_name'] }}</div>
                                                <div class="chat-header-email" style="color: var(--ev-text-3);">
                                                    {{ \Carbon\Carbon::parse($selectedStatus['created_at'])->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="status-viewer-actions">
                                            @if($selectedStatus['is_mine'])
                                                <button type="button" class="status-viewer-delete"
                                                        wire:click="deleteStatus({{ $selectedStatus['id'] }})"
                                                        onclick="return confirm('Delete this status?')"
                                                        title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="status-viewer-close" wire:click="closeStatusViewer" title="Close">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="status-viewer-body">
                                        @if($selectedStatus['type'] === 'photo')
                                            <img src="{{ smart_asset('storage/' . $selectedStatus['media_path']) }}"
                                                 class="status-viewer-media" alt="">
                                        @elseif($selectedStatus['type'] === 'video')
                                            <video src="{{ smart_asset('storage/' . $selectedStatus['media_path']) }}"
                                                   class="status-viewer-media" controls autoplay muted playsinline></video>
                                        @else
                                            <div class="status-viewer-text"
                                                 style="background: {{ $selectedStatus['background_color'] ?: '#075E54' }}; color: {{ $selectedStatus['text_color'] ?: '#fff' }};">
                                                {{ $selectedStatus['content'] }}
                                            </div>
                                        @endif
                                        @if($selectedStatus['type'] !== 'text' && !empty($selectedStatus['content']))
                                            <div class="status-viewer-caption">{{ $selectedStatus['content'] }}</div>
                                        @endif
                                    </div>

                                    @if($selectedStatus['is_mine'] && $selectedStatus['viewers']->isNotEmpty())
                                        <div class="status-viewer-viewers">
                                            <div class="status-viewers-label"><i class="fa fa-eye"></i> {{ $selectedStatus['viewers']->count() }} viewer{{ $selectedStatus['viewers']->count() === 1 ? '' : 's' }}</div>
                                            @foreach($selectedStatus['viewers'] as $v)
                                                <div class="status-viewer-row">
                                                    <div class="conversation-avatar" style="width:32px; height:32px; font-size:12px;">
                                                        @if($v->viewer->avatar ?? false)
                                                            <img src="{{ user_avatar_url($v->viewer) }}" alt="">
                                                        @else
                                                            {{ strtoupper(substr($v->viewer->name ?? $v->viewer->email ?? '?', 0, 1)) }}
                                                        @endif
                                                    </div>
                                                    <div class="status-viewer-row-name">{{ $v->viewer->name ?? $v->viewer->email }}</div>
                                                    <div class="status-viewer-row-time">{{ $v->viewed_at?->diffForHumans() }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Status tab empty state — always in DOM.
                                 CSS uses data-tab + data-has-status on
                                 the chat-container to decide visibility. --}}
                            <div class="chat-empty" data-empty="status">
                                <div class="status-empty-icon">
                                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="50" cy="50" r="42" stroke="currentColor" stroke-width="4" stroke-dasharray="6 5" opacity="0.5"/>
                                        <circle cx="50" cy="50" r="30" stroke="currentColor" stroke-width="4" opacity="0.7"/>
                                        <circle cx="50" cy="50" r="14" fill="currentColor" opacity="0.4"/>
                                    </svg>
                                </div>
                                <h3>Share statuses</h3>
                                <p>Share photos, videos and text that disappear after 24 hours.</p>
                            </div>

                            {{-- Settings splash — always in DOM. CSS uses
                                 data-tab + data-has-section to control
                                 visibility. --}}
                            <div class="chat-empty settings-splash" data-empty="settings">
                                <div class="settings-splash-icon">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <h3 class="settings-splash-title">Settings</h3>
                            </div>

                            @if($settingsSection !== null)
                                {{-- Settings pane — only rendered when a
                                     sub-section is active; Alpine keeps it
                                     hidden unless the Settings tab is on.
                                     .settings-pane-wrapper gives the outer
                                     div proper flex sizing so the inner
                                     .settings-pane can take chat-main's
                                     full height and scroll correctly. --}}
                                {{-- Visibility controlled by
                                     .chat-container[data-tab="settings"]
                                     .settings-pane-wrapper in CSS. --}}
                                <div class="settings-pane-wrapper">
                                    <div class="settings-pane">
                                        <div class="settings-pane-head">
                                            <button type="button" class="settings-pane-back" wire:click="setSettingsSection(null)" title="Back">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                            @php
                                                $headings = ['account' => 'Account', 'chats' => 'Chats', 'notifications' => 'Notification and Sound', 'video' => 'Video &amp; voice'];
                                            @endphp
                                            <span class="settings-pane-title">{!! $headings[$settingsSection] ?? 'Settings' !!}</span>
                                        </div>
                                        <div class="settings-pane-body">

                                        {{-- ============= ACCOUNT ============= --}}
                                        @if($settingsSection === 'account')
                                            <div class="acct-avatar-wrap">
                                                <div class="acct-avatar">
                                                    @if(auth()->user()->avatar)
                                                        <img src="{{ user_avatar_url(auth()->user()) }}" alt="">
                                                    @else
                                                        {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                                                    @endif
                                                    <button type="button" class="acct-avatar-btn" onclick="document.getElementById('acctAvatarInput')?.click()" title="Change photo">
                                                        <i class="fa fa-camera"></i>
                                                    </button>
                                                </div>
                                                <input type="file" id="acctAvatarInput" accept="image/*" style="display:none">
                                                <div class="acct-avatar-caption">Tap to change profile photo</div>
                                            </div>

                                            <div class="acct-field">
                                                <label class="acct-label">Username</label>
                                                <input type="text" class="acct-input" wire:model="accountUsername" placeholder="username">
                                                @error('accountUsername') <div class="acct-error">{{ $message }}</div> @enderror
                                                <div class="acct-hint">Used in your public profile URL.</div>
                                            </div>

                                            <div class="acct-field">
                                                <label class="acct-label">Phone number</label>
                                                <div class="acct-phone-row">
                                                    <div class="acct-phone-cc">
                                                        <span>+</span>
                                                        <input type="text" wire:model="accountCountryCode" maxlength="4">
                                                    </div>
                                                    <input type="text" class="acct-input" wire:model="accountPhone" placeholder="Phone number">
                                                </div>
                                            </div>

                                            <div class="acct-field">
                                                <label class="acct-label">Account</label>
                                                <label class="acct-radio {{ !$accountIsPrivate ? 'active' : '' }}">
                                                    <input type="radio" wire:model.live="accountIsPrivate" value="0">
                                                    <div>
                                                        <div class="acct-radio-title">Public</div>
                                                        <div class="acct-radio-sub">Anyone can message you. Other people find you by your name, or by @username. You are not listed in your own New chat.</div>
                                                    </div>
                                                </label>
                                                <label class="acct-radio {{ $accountIsPrivate ? 'active' : '' }}">
                                                    <input type="radio" wire:model.live="accountIsPrivate" value="1">
                                                    <div>
                                                        <div class="acct-radio-title">Private</div>
                                                        <div class="acct-radio-sub">Only an exact @username search finds you. They can send one message until you reply or accept.</div>
                                                    </div>
                                                </label>
                                            </div>

                                            @if($accountSaved)
                                                <div class="acct-flash">Saved.</div>
                                            @endif
                                            <button type="button" class="acct-save-btn" wire:click="saveAccountSettings">Save</button>

                                        {{-- ============= CHATS ============= --}}
                                        @elseif($settingsSection === 'chats')
                                            <div class="pref-row">
                                                <div>
                                                    <div class="pref-title">Press "enter" to send</div>
                                                    <div class="pref-sub">Enter sends the message; Shift + Enter for a new line.</div>
                                                </div>
                                                <label class="pref-toggle">
                                                    <input type="checkbox" data-pref-key="enterToSend">
                                                    <span class="pref-toggle-slider"></span>
                                                </label>
                                            </div>
                                            <div class="pref-divider"></div>

                                            <div class="pref-row">
                                                <div>
                                                    <div class="pref-title">Unanswered</div>
                                                    <div class="pref-sub">Show only chats where the last message wasn't from you.</div>
                                                </div>
                                                <label class="pref-toggle">
                                                    <input type="checkbox" @checked($prefShowUnanswered) wire:click="togglePrefShowUnanswered">
                                                    <span class="pref-toggle-slider"></span>
                                                </label>
                                            </div>
                                            <div class="pref-divider"></div>

                                            <div class="pref-block">
                                                <div class="pref-title">Disappearing messages</div>
                                                <div class="pref-sub">Sets the default expiry for messages you send. Existing messages aren't affected.</div>
                                            </div>
                                            @foreach(['never' => 'Never', '24h' => '24 hours', '1w' => '1 week', '1m' => '1 month'] as $k => $label)
                                                <label class="pref-radio-row {{ $prefDisappearingDefault === $k ? 'active' : '' }}"
                                                       wire:click="setPrefDisappearing('{{ $k }}')">
                                                    <input type="radio" name="disappearing" value="{{ $k }}" @checked($prefDisappearingDefault === $k)>
                                                    <span class="pref-radio-label">{{ $label }}</span>
                                                    @if($k === 'never')<span class="pref-radio-default">Default</span>@endif
                                                </label>
                                            @endforeach

                                            <div class="pref-divider"></div>
                                            <a href="#" class="pref-link" wire:click.prevent="setActiveTab('gallery')">Photo stream</a>
                                            <a href="#" class="pref-link" wire:click.prevent="setSettingsSection('video')">Test call devices</a>
                                            <a href="#" class="pref-link">Help Centre</a>

                                        {{-- ============= NOTIFICATIONS ============= --}}
                                        @elseif($settingsSection === 'notifications')
                                            <div class="notif-block">
                                                <div class="notif-block-title">Notifications</div>
                                                <div class="notif-block-sub">Allow alerts for messages and calls when this tab is in the background.</div>
                                                <button type="button" class="notif-allow-btn" id="notifAllowBtn">Allow notifications</button>
                                            </div>

                                            <div class="pref-block">
                                                <div class="pref-title">Call ringtone</div>
                                                <div class="pref-sub">Played when someone calls you.</div>
                                            </div>
                                            @foreach(['classic' => 'Classic', 'bright' => 'Bright', 'soft' => 'Soft', 'chime' => 'Chime', 'pulse' => 'Pulse'] as $k => $label)
                                                <label class="pref-radio-row" data-pref-key="callRingtone" data-pref-value="{{ $k }}">
                                                    <input type="radio" name="callRingtone" value="{{ $k }}">
                                                    <span class="pref-radio-label">{{ $label }}</span>
                                                    @if($k === 'classic')<span class="pref-radio-default">Default</span>@endif
                                                    <button type="button" class="pref-play-btn" data-tone="{{ $k }}" data-kind="ring" onclick="event.preventDefault(); event.stopPropagation();">Play</button>
                                                </label>
                                            @endforeach

                                            <div class="pref-divider"></div>
                                            <div class="pref-block">
                                                <div class="pref-title">Message tone</div>
                                                <div class="pref-sub">Played when a new message arrives.</div>
                                            </div>
                                            @foreach(['pop' => 'Pop', 'click' => 'Click', 'drop' => 'Drop', 'bell' => 'Bell', 'soft' => 'Soft'] as $k => $label)
                                                <label class="pref-radio-row" data-pref-key="messageTone" data-pref-value="{{ $k }}">
                                                    <input type="radio" name="messageTone" value="{{ $k }}">
                                                    <span class="pref-radio-label">{{ $label }}</span>
                                                    @if($k === 'pop')<span class="pref-radio-default">Default</span>@endif
                                                    <button type="button" class="pref-play-btn" data-tone="{{ $k }}" data-kind="msg" onclick="event.preventDefault(); event.stopPropagation();">Play</button>
                                                </label>
                                            @endforeach

                                        {{-- ============= VIDEO & VOICE ============= --}}
                                        @elseif($settingsSection === 'video')
                                            <p class="settings-pane-hint">Test microphone, camera, and network before you call.</p>

                                            {{-- wire:ignore stops Livewire from touching this subtree.
                                                 Every element inside is manipulated by JS
                                                 (srcObject on <video>, populated <option>s in the
                                                 selects, meter width, result text) and the 3s
                                                 refreshChat poll would otherwise revert them all
                                                 back to their server-rendered "Loading…" state. --}}
                                            <div wire:ignore>
                                                <div class="vv-preview" id="vvPreview">
                                                    <video id="vvVideo" autoplay muted playsinline></video>
                                                    <div class="vv-preview-placeholder">Camera preview will appear here</div>
                                                </div>

                                                <div class="vv-field">
                                                    <label class="vv-label">Microphone level</label>
                                                    <div class="vv-meter"><div class="vv-meter-fill" id="vvMeter"></div></div>
                                                </div>

                                                <div class="vv-field">
                                                    <label class="vv-label">Microphone</label>
                                                    <select class="vv-select" id="vvMicSelect"><option>Loading…</option></select>
                                                </div>
                                                <div class="vv-field">
                                                    <label class="vv-label">Camera</label>
                                                    <select class="vv-select" id="vvCamSelect"><option>Loading…</option></select>
                                                </div>
                                                <div class="vv-field">
                                                    <label class="vv-label">Speaker</label>
                                                    <select class="vv-select" id="vvSpkSelect"><option>Loading…</option></select>
                                                </div>

                                                <div class="vv-actions">
                                                    <button type="button" class="vv-btn ghost" id="vvFlipBtn">Flip camera</button>
                                                    <button type="button" class="vv-btn primary" id="vvTestIceBtn">Test ICE / TURN connection</button>
                                                </div>

                                                <div class="vv-result-row"><span>Microphone</span><span id="vvMicResult" class="vv-result">Not tested</span></div>
                                                <div class="vv-result-row"><span>Camera</span><span id="vvCamResult" class="vv-result">Not tested</span></div>
                                                <div class="vv-result-row"><span>Network</span><span id="vvNetResult" class="vv-result">Not tested</span></div>
                                            </div>

                                        @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Gallery empty state — always in DOM. CSS uses
                                 data-tab + data-has-conv to control
                                 visibility. --}}
                            <div class="chat-empty" data-empty="gallery">
                                <div class="chat-empty-icon"><i class="fa fa-image"></i></div>
                                <h3>Select a photo or video</h3>
                                <p>Click any item on the left to open the chat it was shared in.</p>
                            </div>

                            @if($selectedConversationId && ($selectedUser || $selectedIsSupport))
                                {{-- Chat thread (header + messages + reply) —
                                     only rendered when a conversation is
                                     actually selected. Alpine keeps it
                                     hidden unless the Chats or Gallery tab
                                     is active. --}}
                                {{-- Visibility controlled by
                                     .chat-container[data-tab="chats"/"gallery"]
                                     .chat-thread-wrapper in CSS. --}}
                                <div class="chat-thread-wrapper">
                                {{-- Chat Header --}}
                                <div class="chat-header">
                                    <div class="chat-header-info">
                                        <button class="mobile-back-btn" wire:click="closeConversation">
                                            <i class="fa fa-angle-left"></i><span class="mobile-back-label"> Back</span>
                                        </button>
                                        <div class="chat-header-avatar">
                                            @if($selectedIsSupport)
                                                <i class="fa fa-headset" aria-hidden="true"></i>
                                            @elseif($selectedUser && $selectedUser->avatar)
                                                <img src="{{ user_avatar_url($selectedUser) }}" alt="">
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
                                                        data-peer-avatar="{{ $selectedUser->avatar ? user_avatar_url($selectedUser) : '' }}"
                                                        data-conversation-id="{{ $selectedConversationId }}">
                                                    <i class="fa fa-phone"></i>
                                                </button>
                                                <button type="button" title="Video call"
                                                        data-rtc-call="video"
                                                        data-peer-id="{{ $selectedUser->id }}"
                                                        data-peer-name="{{ $selectedUser->name ?? $selectedUser->email }}"
                                                        data-peer-avatar="{{ $selectedUser->avatar ? user_avatar_url($selectedUser) : '' }}"
                                                        data-conversation-id="{{ $selectedConversationId }}">
                                                    <i class="fa fa-video"></i>
                                                </button>
                                            </div>
                                            <div class="chat-header-menu">
                                                <button type="button" title="More options"
                                                        onclick="this.parentElement.classList.toggle('open')">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <div class="chat-header-dropdown">
                                                    <button type="button" class="danger"
                                                            wire:click="deleteConversation"
                                                            onclick="return confirm('Delete this conversation?')">
                                                        <i class="fa fa-trash"></i> Delete conversation
                                                    </button>
                                                </div>
                                            </div>
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
                                        <div class="message-wrapper {{ $message['is_mine'] ? 'sent' : 'received' }} {{ !empty($message['expires_at']) ? 'disappearing' : '' }}"
                                             wire:key="msg-{{ $message['id'] }}-{{ $message['status'] }}"
                                             data-message-id="{{ $message['id'] }}"
                                             @if(!empty($message['expires_at'])) data-expires-at="{{ $message['expires_at'] }}" @endif>
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
                                                                    data-peer-avatar="{{ $selectedUser->avatar ? user_avatar_url($selectedUser) : '' }}"
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
                                            </div>
                                            {{-- Time + delivery ticks live BELOW the bubble (as a
                                                 sibling, not a child) so they render on the empty
                                                 chat backdrop instead of inside the coloured bubble.
                                                 The parent .message-wrapper uses flex-column so
                                                 the bubble stacks on top and this row underneath. --}}
                                            <div class="message-time">
                                                <span>{{ \Carbon\Carbon::parse($message['created_at'])->format('h:i A') }}</span>
                                                @if(!empty($message['expires_at']))
                                                    <span class="message-expires" title="Disappearing message">
                                                        <i class="fa fa-clock"></i>
                                                        <span class="message-expires-countdown" data-expires-at="{{ $message['expires_at'] }}"></span>
                                                    </span>
                                                @endif
                                                @if($message['is_mine'])
                                                    <span class="message-ticks">
                                                        @if($message['status'] === 'read')
                                                            <span class="tick read double-tick">✓✓</span>
                                                        @elseif($message['status'] === 'delivered')
                                                            <span class="tick double-tick">✓✓</span>
                                                        @else
                                                            <span class="tick">✓</span>
                                                        @endif
                                                    </span>
                                                @endif
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
                                            placeholder="{{ $prefDisappearingDefault !== 'never' ? 'Type a message — disappears in ' . ['24h'=>'24 hours','1w'=>'1 week','1m'=>'1 month'][$prefDisappearingDefault] : 'Type a message...' }}"
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
                                </div>
                            @endif

                            {{-- Start Call / Call number empty state —
                                 always in DOM. CSS uses data-tab +
                                 data-has-conv to show this on Chats
                                 with no conv, or on Calls. --}}
                            <div class="chat-empty" data-empty="callable">
                                <div class="empty-actions">
                                        <button type="button" class="empty-action-card" id="startCallCard">
                                            <span class="empty-action-icon">
                                                <svg width="33" height="26" viewBox="0 0 33 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.55 14.625V17.875C11.55 18.3354 11.7084 18.7216 12.0252 19.0336C12.342 19.3456 12.7336 19.5011 13.2 19.5C13.6664 19.4989 14.0585 19.3429 14.3765 19.032C14.6944 18.7211 14.8522 18.3354 14.85 17.875V14.625H18.15C18.6175 14.625 19.0096 14.469 19.3265 14.157C19.6432 13.845 19.8011 13.4593 19.8 13C19.7989 12.5407 19.6405 12.155 19.3248 11.843C19.0091 11.531 18.6175 11.375 18.15 11.375H14.85V8.125C14.85 7.66458 14.6916 7.27892 14.3748 6.968C14.058 6.65708 13.6664 6.50108 13.2 6.5C12.7336 6.49892 12.342 6.65492 12.0252 6.968C11.7084 7.28108 11.55 7.66675 11.55 8.125V11.375H8.25C7.7825 11.375 7.3909 11.531 7.0752 11.843C6.7595 12.155 6.6011 12.5407 6.6 13C6.5989 13.4593 6.7573 13.8455 7.0752 14.1586C7.3931 14.4717 7.7847 14.6272 8.25 14.625H11.55ZM3.3 26C2.3925 26 1.6159 25.682 0.9702 25.0461C0.3245 24.4102 0.0011 23.6448 0 22.75V3.25C0 2.35625 0.3234 1.59142 0.9702 0.9555C1.617 0.319583 2.3936 0.00108333 3.3 0H23.1C24.0075 0 24.7846 0.3185 25.4314 0.9555C26.0782 1.5925 26.4011 2.35733 26.4 3.25V10.5625L31.5975 5.44375C31.8725 5.17292 32.175 5.10521 32.505 5.24062C32.835 5.37604 33 5.63333 33 6.0125V19.9875C33 20.3667 32.835 20.624 32.505 20.7594C32.175 20.8948 31.8725 20.8271 31.5975 20.5562L26.4 15.4375V22.75C26.4 23.6437 26.0772 24.4091 25.4314 25.0461C24.7857 25.6831 24.0086 26.0011 23.1 26H3.3ZM3.3 22.75H23.1V3.25H3.3V22.75Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <span class="empty-action-label">Start Call</span>
                                        </button>
                                        <button type="button" class="empty-action-card" id="callNumberCard">
                                            <span class="empty-action-icon">
                                                <svg width="19" height="27" viewBox="0 0 19 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.71429 5.4C1.99441 5.4 1.30402 5.11554 0.794996 4.60919C0.285968 4.10284 0 3.41608 0 2.7C0 1.98392 0.285968 1.29716 0.794996 0.790812C1.30402 0.284464 1.99441 0 2.71429 0C3.43416 0 4.12455 0.284464 4.63358 0.790812C5.1426 1.29716 5.42857 1.98392 5.42857 2.7C5.42857 3.41608 5.1426 4.10284 4.63358 4.60919C4.12455 5.11554 3.43416 5.4 2.71429 5.4ZM9.5 5.4C8.78013 5.4 8.08974 5.11554 7.58071 4.60919C7.07168 4.10284 6.78571 3.41608 6.78571 2.7C6.78571 1.98392 7.07168 1.29716 7.58071 0.790812C8.08974 0.284464 8.78013 0 9.5 0C10.2199 0 10.9103 0.284464 11.4193 0.790812C11.9283 1.29716 12.2143 1.98392 12.2143 2.7C12.2143 3.41608 11.9283 4.10284 11.4193 4.60919C10.9103 5.11554 10.2199 5.4 9.5 5.4ZM16.2857 5.4C15.5658 5.4 14.8755 5.11554 14.3664 4.60919C13.8574 4.10284 13.5714 3.41608 13.5714 2.7C13.5714 1.98392 13.8574 1.29716 14.3664 0.790812C14.8755 0.284464 15.5658 0 16.2857 0C17.0056 0 17.696 0.284464 18.205 0.790812C18.714 1.29716 19 1.98392 19 2.7C19 3.41608 18.714 4.10284 18.205 4.60919C17.696 5.11554 17.0056 5.4 16.2857 5.4ZM2.71429 12.15C1.99441 12.15 1.30402 11.8655 0.794996 11.3592C0.285968 10.8528 0 10.1661 0 9.45C0 8.73392 0.285968 8.04716 0.794996 7.54081C1.30402 7.03446 1.99441 6.75 2.71429 6.75C3.43416 6.75 4.12455 7.03446 4.63358 7.54081C5.1426 8.04716 5.42857 8.73392 5.42857 9.45C5.42857 10.1661 5.1426 10.8528 4.63358 11.3592C4.12455 11.8655 3.43416 12.15 2.71429 12.15ZM9.5 12.15C8.78013 12.15 8.08974 11.8655 7.58071 11.3592C7.07168 10.8528 6.78571 10.1661 6.78571 9.45C6.78571 8.73392 7.07168 8.04716 7.58071 7.54081C8.08974 7.03446 8.78013 6.75 9.5 6.75C10.2199 6.75 10.9103 7.03446 11.4193 7.54081C11.9283 8.04716 12.2143 8.73392 12.2143 9.45C12.2143 10.1661 11.9283 10.8528 11.4193 11.3592C10.9103 11.8655 10.2199 12.15 9.5 12.15ZM16.2857 12.15C15.5658 12.15 14.8755 11.8655 14.3664 11.3592C13.8574 10.8528 13.5714 10.1661 13.5714 9.45C13.5714 8.73392 13.8574 8.04716 14.3664 7.54081C14.8755 7.03446 15.5658 6.75 16.2857 6.75C17.0056 6.75 17.696 7.03446 18.205 7.54081C18.714 8.04716 19 8.73392 19 9.45C19 10.1661 18.714 10.8528 18.205 11.3592C17.696 11.8655 17.0056 12.15 16.2857 12.15ZM2.71429 18.9C1.99441 18.9 1.30402 18.6155 0.794996 18.1092C0.285968 17.6028 0 16.9161 0 16.2C0 15.4839 0.285968 14.7972 0.794996 14.2908C1.30402 13.7845 1.99441 13.5 2.71429 13.5C3.43416 13.5 4.12455 13.7845 4.63358 14.2908C5.1426 14.7972 5.42857 15.4839 5.42857 16.2C5.42857 16.9161 5.1426 17.6028 4.63358 18.1092C4.12455 18.6155 3.43416 18.9 2.71429 18.9ZM9.5 18.9C8.78013 18.9 8.08974 18.6155 7.58071 18.1092C7.07168 17.6028 6.78571 16.9161 6.78571 16.2C6.78571 15.4839 7.07168 14.7972 7.58071 14.2908C8.08974 13.7845 8.78013 13.5 9.5 13.5C10.2199 13.5 10.9103 13.7845 11.4193 14.2908C11.9283 14.7972 12.2143 15.4839 12.2143 16.2C12.2143 16.9161 11.9283 17.6028 11.4193 18.1092C10.9103 18.6155 10.2199 18.9 9.5 18.9ZM9.5 27C8.78013 27 8.08974 26.7155 7.58071 26.2092C7.07168 25.7028 6.78571 25.0161 6.78571 24.3C6.78571 23.5839 7.07168 22.8972 7.58071 22.3908C8.08974 21.8845 8.78013 21.6 9.5 21.6C10.2199 21.6 10.9103 21.8845 11.4193 22.3908C11.9283 22.8972 12.2143 23.5839 12.2143 24.3C12.2143 25.0161 11.9283 25.7028 11.4193 26.2092C10.9103 26.7155 10.2199 27 9.5 27ZM16.2857 18.9C15.5658 18.9 14.8755 18.6155 14.3664 18.1092C13.8574 17.6028 13.5714 16.9161 13.5714 16.2C13.5714 15.4839 13.8574 14.7972 14.3664 14.2908C14.8755 13.7845 15.5658 13.5 16.2857 13.5C17.0056 13.5 17.696 13.7845 18.205 14.2908C18.714 14.7972 19 15.4839 19 16.2C19 16.9161 18.714 17.6028 18.205 18.1092C17.696 18.6155 17.0056 18.9 16.2857 18.9Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <span class="empty-action-label">Call a number</span>
                                        </button>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

    @push('js')
    {{-- Same reasoning as the CSS push above: keeping this ~1100-line
         script inline in the component root meant re-sending it on every
         Livewire update. Pushed to the layout, it lands in <body> once
         on initial page load and every subsequent tab-click/poll response
         is dramatically smaller. --}}
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

        // Emoji picker — registered directly at script parse time.
        // Fully document-delegated with fresh DOM lookups on every event,
        // so it survives every Livewire re-render (poll refresh, message
        // send, conversation switch) without needing to be re-wired.
        (function wireEmojiPickerOnce() {
            if (window.__chatState.emojiWired) return;
            window.__chatState.emojiWired = true;

            document.addEventListener('click', (e) => {
                const toggleBtn = e.target.closest('#emojiToggleBtn');
                if (toggleBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const wrap = document.getElementById('emojiPickerWrapper');
                    if (!wrap) { console.warn('[emoji] wrapper not found in DOM'); return; }
                    wrap.classList.toggle('open');
                    return;
                }
                // Click outside → close.
                if (!e.target.closest('#emojiPickerWrapper')) {
                    document.getElementById('emojiPickerWrapper')?.classList.remove('open');
                }
                // Chat header options dropdown — close on any click that
                // isn't inside the menu itself. The toggle-open click is
                // handled by the button's inline onclick.
                if (!e.target.closest('.chat-header-menu')) {
                    document.querySelectorAll('.chat-header-menu.open').forEach(el => el.classList.remove('open'));
                }
            });

            // emoji-click bubbles from <emoji-picker> to document.
            document.addEventListener('emoji-click', (event) => {
                const input = document.getElementById('chatReplyInput');
                if (!input) return;
                const emoji = event.detail?.unicode;
                if (!emoji) return;
                const start = input.selectionStart ?? input.value.length;
                const end = input.selectionEnd ?? input.value.length;
                input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
                input.setSelectionRange(start + emoji.length, start + emoji.length);
                input.focus();
                const root = input.closest('[wire\\:id]');
                if (root && window.Livewire) {
                    Livewire.find(root.getAttribute('wire:id'))?.set('reply', input.value, false);
                }
            });
        })();

        // Kept as a no-op so the existing DOMContentLoaded / livewire:navigated
        // hooks that call wireEmojiPicker() don't error out.
        function wireEmojiPicker() { /* IIFE above handles wiring */ }

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

            // Gallery → scroll to a specific message in the opened thread.
            // The event fires from Chat.php right after the conversation
            // is selected; we retry for ~1.5s because the message DOM is
            // rendered in the same Livewire round-trip and may not be
            // present the instant the event lands.
            Livewire.on('scroll-to-message', (payload) => {
                const msgId = payload?.messageId ?? payload?.[0]?.messageId;
                if (!msgId) return;
                let tries = 0;
                const tick = () => {
                    const el = document.querySelector(`[data-message-id="${msgId}"]`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.classList.add('gallery-highlight');
                        setTimeout(() => el.classList.remove('gallery-highlight'), 2200);
                        return;
                    }
                    if (++tries < 15) setTimeout(tick, 100);
                };
                tick();
            });
        });

        // Lightweight status refresh — every 3 seconds while a conversation
        // is open, ask the server for the latest message states AND mark
        // any new incoming messages as read. This is a fallback so
        // sidebar badges clear + ticks always update even if the Reverb
        // broadcast is late/dropped. pollRefresh (unlike refreshChat) does
        // NOT dispatch 'message-received', so it never fights the user's
        // scroll position when they're reading history.
        // Sidebar-badge + tick-mark refresh. Anchored on #chatSidebar so
        // the poll only fires when the Chat Livewire component is actually
        // on the page — otherwise (e.g. after client-side navigation to
        // another page that has its own Livewire component higher in the
        // DOM), querySelector('[wire\\:id]') would find a non-Chat component
        // and Livewire would throw MethodNotFoundException for refreshChat.
        if (!window.__chatState.tickPoll) {
            window.__chatState.tickPoll = setInterval(() => {
                if (document.hidden) return;
                // Skip while a voice recording is in progress — the poll's
                // Livewire re-render morphs the input row and can cause the
                // MediaRecorder pipeline to abort partway through, sending
                // an incomplete blob.
                if (window.__chatState.recorder) return;
                // Chat sidebar only exists on /my-chat, so use it as the
                // "am I still on the chat page?" sentinel.
                const anchor = document.getElementById('chatSidebar');
                if (!anchor) return;
                const root = anchor.closest('[wire\\:id]');
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
            }, 8000);  // Was 3000. Reverb WebSocket handles real-time
                       // delivery/receipts; this poll is a safety net for
                       // dropped broadcasts, so 8s is plenty. Cuts DB
                       // pressure ~60% and stops the whole page feeling
                       // sluggish from constant re-render churn.
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

        // Enter-to-send + disappearing-message countdown wiring.
        // Both are lightweight, so we wire them at module scope and rely on
        // document-level event delegation to survive Livewire DOM morphs.
        (function wireChatPrefs() {
            if (window.__chatState.chatPrefsWired) return;
            window.__chatState.chatPrefsWired = true;

            const PREF_PREFIX = 'evoory.pref.';
            const getPref = (k, fallback) => {
                try { const v = localStorage.getItem(PREF_PREFIX + k); return v === null ? fallback : v; }
                catch (_) { return fallback; }
            };

            // Enter-to-send — when pref is true, Enter submits the form
            // and Shift+Enter inserts a newline (browser default). When
            // false, Enter does nothing special (browser inserts newline
            // in the <input>, which for a text input does nothing).
            document.addEventListener('keydown', (e) => {
                if (e.target.id !== 'chatReplyInput') return;
                if (e.key !== 'Enter' || e.shiftKey) return;
                if (getPref('enterToSend', 'false') !== 'true') return;

                e.preventDefault();
                // Find the composer form and submit it — the wire:submit
                // handler fires and calls sendReply() on the component.
                const form = e.target.closest('form.chat-input-form');
                if (form) form.requestSubmit();
            });

            // Disappearing-message countdown ticker — updates every 15s.
            // Formats the ETA as a short "23h", "6d", "45m" etc. When a
            // message crosses its expires_at we fade it out and remove
            // the row so the user sees the vanish without a page reload.
            const formatDelta = (ms) => {
                if (ms <= 0) return '0s';
                const s = Math.floor(ms / 1000);
                if (s < 60) return s + 's';
                const m = Math.floor(s / 60);
                if (m < 60) return m + 'm';
                const h = Math.floor(m / 60);
                if (h < 24) return h + 'h';
                const d = Math.floor(h / 24);
                return d + 'd';
            };
            const updateCountdowns = () => {
                const now = Date.now();
                document.querySelectorAll('.message-wrapper.disappearing').forEach(wrap => {
                    const exp = Date.parse(wrap.dataset.expiresAt || '');
                    if (!exp) return;
                    const remaining = exp - now;
                    if (remaining <= 0) {
                        wrap.classList.add('vanishing');
                        setTimeout(() => wrap.remove(), 700);
                        return;
                    }
                    const el = wrap.querySelector('.message-expires-countdown');
                    if (el) el.textContent = formatDelta(remaining);
                });
            };
            updateCountdowns();
            setInterval(updateCountdowns, 15000);
        })();

        // Settings pane wiring — prefs are stored in localStorage under the
        // "evoory.pref.*" namespace. Also handles device-enumeration for the
        // Video & voice pane and synth tone playback for the ringtone /
        // message-tone lists.
        (function wireSettings() {
            if (window.__chatState.settingsWired) return;
            window.__chatState.settingsWired = true;

            const PREF_PREFIX = 'evoory.pref.';
            const getPref = (k, fallback) => {
                try { const v = localStorage.getItem(PREF_PREFIX + k); return v === null ? fallback : v; }
                catch (_) { return fallback; }
            };
            const setPref = (k, v) => { try { localStorage.setItem(PREF_PREFIX + k, String(v)); } catch (_) {} };

            // Hydrate + wire toggles & radio rows every time the Settings
            // sub-pane renders. Livewire morphs the DOM, so we watch for it.
            const hydrateSettings = () => {
                // Boolean toggles
                document.querySelectorAll('[data-pref-key]').forEach(el => {
                    const key = el.dataset.prefKey;
                    if (el.type === 'checkbox') {
                        el.checked = getPref(key, 'false') === 'true';
                        if (!el.__prefWired) {
                            el.__prefWired = true;
                            el.addEventListener('change', () => setPref(key, el.checked));
                        }
                    } else if (el.matches('.pref-radio-row')) {
                        const value = el.dataset.prefValue;
                        const current = getPref(key, el.parentElement.querySelector('.pref-radio-row')?.dataset.prefValue);
                        const input = el.querySelector('input[type="radio"]');
                        if (input) input.checked = (current === value);
                        el.classList.toggle('active', current === value);
                        if (!el.__prefWired) {
                            el.__prefWired = true;
                            el.addEventListener('click', (e) => {
                                if (e.target.closest('.pref-play-btn')) return;
                                setPref(key, value);
                                document.querySelectorAll(`.pref-radio-row[data-pref-key="${key}"]`).forEach(r => {
                                    r.classList.toggle('active', r.dataset.prefValue === value);
                                    const i = r.querySelector('input[type="radio"]');
                                    if (i) i.checked = (r.dataset.prefValue === value);
                                });
                            });
                        }
                    }
                });

                // Play buttons — synth a short tone matching the selected
                // preset. We use WebAudio so no sound file uploads needed.
                document.querySelectorAll('.pref-play-btn').forEach(btn => {
                    if (btn.__playWired) return;
                    btn.__playWired = true;
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        playTone(btn.dataset.tone, btn.dataset.kind);
                    });
                });

                // Allow-notifications button — reflect the CURRENT browser
                // permission state whenever the pane re-renders. The click
                // handler itself is registered once at document scope below
                // so it survives Livewire morphs without needing per-render
                // rewiring.
                const notifBtn = document.getElementById('notifAllowBtn');
                if (notifBtn && 'Notification' in window) {
                    if (Notification.permission === 'granted') {
                        notifBtn.textContent = 'Notifications enabled';
                        notifBtn.disabled = true;
                    } else if (Notification.permission === 'denied') {
                        notifBtn.textContent = 'Notifications blocked';
                        notifBtn.title = 'Enable notifications for this site in your browser settings.';
                    } else {
                        notifBtn.textContent = 'Allow notifications';
                        notifBtn.disabled = false;
                    }
                }

                // Video & voice pane — enumerate devices, start preview + meter.
                if (document.getElementById('vvVideo')) initVideoVoicePane();

                // Account avatar upload
                const avatarInput = document.getElementById('acctAvatarInput');
                if (avatarInput && !avatarInput.__wired) {
                    avatarInput.__wired = true;
                    avatarInput.addEventListener('change', uploadAvatar);
                }
            };

            // Run once now, then after every Livewire DOM morph.
            hydrateSettings();
            document.addEventListener('livewire:navigated', hydrateSettings);
            if (window.Livewire) {
                Livewire.hook('morph.updated', hydrateSettings);
            } else {
                document.addEventListener('livewire:initialized', () => {
                    Livewire.hook('morph.updated', hydrateSettings);
                });
            }

            // ---------- Tone synthesis ----------
            function playTone(name, kind) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    // Ringtones = two-note pattern, longer; message tones = single short blip.
                    const presets = {
                        ring: {
                            classic: [[520, 0.15], [660, 0.25]],
                            bright:  [[880, 0.12], [1100, 0.20]],
                            soft:    [[440, 0.20], [520, 0.30]],
                            chime:   [[784, 0.10], [988, 0.10], [1175, 0.30]],
                            pulse:   [[600, 0.08], [0, 0.06], [600, 0.08], [0, 0.06], [600, 0.20]],
                        },
                        msg: {
                            pop:   [[840, 0.06], [560, 0.08]],
                            click: [[1200, 0.04]],
                            drop:  [[900, 0.05], [500, 0.07]],
                            bell:  [[1400, 0.05], [1050, 0.20]],
                            soft:  [[600, 0.15]],
                        },
                    };
                    const seq = presets[kind]?.[name] || presets[kind]?.[Object.keys(presets[kind])[0]];
                    let t = ctx.currentTime;
                    seq.forEach(([freq, dur]) => {
                        if (freq > 0) {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = kind === 'ring' ? 'sine' : 'triangle';
                            osc.frequency.value = freq;
                            gain.gain.setValueAtTime(0.0001, t);
                            gain.gain.exponentialRampToValueAtTime(0.25, t + 0.01);
                            gain.gain.exponentialRampToValueAtTime(0.0001, t + dur);
                            osc.connect(gain).connect(ctx.destination);
                            osc.start(t);
                            osc.stop(t + dur + 0.02);
                        }
                        t += dur;
                    });
                    setTimeout(() => ctx.close(), (t - ctx.currentTime + 0.2) * 1000);
                } catch (err) { console.warn('[tone] play failed', err); }
            }

            // ---------- Video & voice pane ----------
            let vvStream = null;
            let vvAudioCtx = null;
            let vvRafId = null;
            let vvFacing = 'user';

            async function initVideoVoicePane() {
                const micSel = document.getElementById('vvMicSelect');
                const camSel = document.getElementById('vvCamSelect');
                const spkSel = document.getElementById('vvSpkSelect');
                if (!micSel || micSel.__vvWired) { return; }
                micSel.__vvWired = true;

                // First getUserMedia to unlock device labels, then enumerate.
                try {
                    const tmp = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
                    tmp.getTracks().forEach(t => t.stop());
                } catch (_) { /* user might deny; still enumerate */ }

                const devs = await navigator.mediaDevices.enumerateDevices();
                const fill = (sel, kind) => {
                    sel.innerHTML = '';
                    devs.filter(d => d.kind === kind).forEach(d => {
                        const o = document.createElement('option');
                        o.value = d.deviceId;
                        o.textContent = d.label || `${kind} (${d.deviceId.slice(0,6)}…)`;
                        sel.appendChild(o);
                    });
                    if (!sel.options.length) {
                        const o = document.createElement('option');
                        o.textContent = 'No devices found';
                        sel.appendChild(o);
                    }
                };
                fill(micSel, 'audioinput');
                fill(camSel, 'videoinput');
                fill(spkSel, 'audiooutput');

                const startPreview = async () => {
                    if (vvStream) vvStream.getTracks().forEach(t => t.stop());
                    if (vvRafId) cancelAnimationFrame(vvRafId);
                    if (vvAudioCtx) { try { vvAudioCtx.close(); } catch (_) {} vvAudioCtx = null; }

                    try {
                        vvStream = await navigator.mediaDevices.getUserMedia({
                            audio: { deviceId: micSel.value ? { exact: micSel.value } : undefined },
                            video: { deviceId: camSel.value ? { exact: camSel.value } : undefined, facingMode: vvFacing },
                        });
                        const video = document.getElementById('vvVideo');
                        video.srcObject = vvStream;
                        document.getElementById('vvMicResult').textContent = 'Pass';
                        document.getElementById('vvMicResult').className = 'vv-result pass';
                        document.getElementById('vvCamResult').textContent = 'Pass';
                        document.getElementById('vvCamResult').className = 'vv-result pass';

                        // Mic level meter
                        vvAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const src = vvAudioCtx.createMediaStreamSource(vvStream);
                        const analyser = vvAudioCtx.createAnalyser();
                        analyser.fftSize = 512;
                        src.connect(analyser);
                        const buf = new Uint8Array(analyser.frequencyBinCount);
                        const meter = document.getElementById('vvMeter');
                        const tick = () => {
                            analyser.getByteTimeDomainData(buf);
                            let peak = 0;
                            for (let i = 0; i < buf.length; i++) {
                                const v = Math.abs(buf[i] - 128) / 128;
                                if (v > peak) peak = v;
                            }
                            if (meter) meter.style.width = Math.min(100, peak * 240) + '%';
                            vvRafId = requestAnimationFrame(tick);
                        };
                        tick();
                    } catch (err) {
                        console.warn('[vv] preview failed', err);
                        document.getElementById('vvMicResult').textContent = 'Fail';
                        document.getElementById('vvMicResult').className = 'vv-result fail';
                        document.getElementById('vvCamResult').textContent = 'Fail';
                        document.getElementById('vvCamResult').className = 'vv-result fail';
                    }
                };

                micSel.onchange = startPreview;
                camSel.onchange = startPreview;

                document.getElementById('vvFlipBtn')?.addEventListener('click', () => {
                    vvFacing = vvFacing === 'user' ? 'environment' : 'user';
                    document.getElementById('vvFlipBtn').textContent = 'Flip camera (' + (vvFacing === 'user' ? 'front' : 'back') + ')';
                    startPreview();
                });

                document.getElementById('vvTestIceBtn')?.addEventListener('click', async () => {
                    const netEl = document.getElementById('vvNetResult');
                    netEl.textContent = 'Testing…';
                    netEl.className = 'vv-result';
                    try {
                        const r = await fetch('/rtc/turn');
                        const cfg = await r.json();
                        const pc = new RTCPeerConnection(cfg);
                        pc.createDataChannel('probe');
                        const done = new Promise((resolve) => {
                            let candidateSeen = false;
                            pc.onicecandidate = (e) => { if (e.candidate) candidateSeen = true; };
                            pc.onicegatheringstatechange = () => {
                                if (pc.iceGatheringState === 'complete') resolve(candidateSeen);
                            };
                            setTimeout(() => resolve(candidateSeen), 5000);
                        });
                        const offer = await pc.createOffer();
                        await pc.setLocalDescription(offer);
                        const ok = await done;
                        pc.close();
                        netEl.textContent = ok ? 'Pass' : 'Fail';
                        netEl.className = 'vv-result ' + (ok ? 'pass' : 'fail');
                    } catch (err) {
                        netEl.textContent = 'Fail';
                        netEl.className = 'vv-result fail';
                        console.warn('[vv] ICE test failed', err);
                    }
                });

                startPreview();
            }

            // ---------- Account avatar upload ----------
            async function uploadAvatar(e) {
                const file = e.target.files?.[0];
                if (!file) return;
                e.target.value = '';
                if (file.size > 5 * 1024 * 1024) { alert('Photo must be under 5 MB.'); return; }
                const fd = new FormData();
                fd.append('avatar', file);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch('/settings/avatar', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: fd,
                    });
                    if (!res.ok) { alert('Upload failed (' + res.status + ').'); return; }
                    const root = document.querySelector('[wire\\:id]');
                    if (root) Livewire.find(root.getAttribute('wire:id'))?.$refresh();
                } catch (err) {
                    console.error('[avatar] upload failed', err);
                }
            }

            // Stop preview stream when we leave the video pane, so the
            // browser doesn't keep the camera light on unnecessarily.
            document.addEventListener('click', (e) => {
                const backBtn = e.target.closest('.settings-pane-back');
                const settingsRow = e.target.closest('.settings-row');
                if ((backBtn || settingsRow) && vvStream) {
                    vvStream.getTracks().forEach(t => t.stop());
                    vvStream = null;
                    if (vvRafId) cancelAnimationFrame(vvRafId);
                    if (vvAudioCtx) { try { vvAudioCtx.close(); } catch (_) {} vvAudioCtx = null; }
                }
            });

            // Document-delegated "Allow notifications" click. Registering
            // via delegation means the handler still fires the first time
            // the button appears in the DOM — the direct addEventListener
            // approach missed it when the pane wasn't rendered at page
            // load. Notification.requestPermission() needs a user gesture,
            // which the click provides.
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('#notifAllowBtn');
                if (!btn) return;
                if (!('Notification' in window)) {
                    alert('This browser does not support notifications.');
                    return;
                }
                if (Notification.permission === 'denied') {
                    alert('Notifications are blocked in your browser. Enable them in the site permissions.');
                    return;
                }
                try {
                    const perm = await Notification.requestPermission();
                    if (perm === 'granted') {
                        btn.textContent = 'Notifications enabled';
                        btn.disabled = true;
                        // A quick confirmation ping so the user sees it works.
                        try { new Notification('Notifications enabled', { body: 'You will get alerts for new messages and calls.' }); } catch (_) {}
                    } else {
                        btn.textContent = perm === 'denied' ? 'Notifications blocked' : 'Allow notifications';
                    }
                } catch (err) {
                    console.warn('[notif] permission request failed', err);
                }
            });
        })();

        // Status media upload — the "+" dropdown "Photos & Videos" option
        // triggers the hidden #statusMediaInput; on change we POST to
        // /status/upload as multipart/form-data (same pattern as chat
        // attachment upload) then refresh the Livewire component so the
        // new status shows up on the "My status" card.
        (function wireStatusUpload() {
            if (window.__chatState.statusWired) return;
            window.__chatState.statusWired = true;

            document.addEventListener('change', async (e) => {
                if (!e.target.matches('#statusMediaInput')) return;
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                e.target.value = '';
                if (file.size > 30 * 1024 * 1024) {
                    alert('File is larger than 30 MB.');
                    return;
                }

                const fd = new FormData();
                fd.append('media', file);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch('/status/upload', {
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
                        console.error('[status] upload HTTP', res.status, body);
                        alert('Status upload failed (' + res.status + ').');
                        return;
                    }
                    const anchor = document.getElementById('chatSidebar');
                    const root = anchor?.closest('[wire\\:id]');
                    if (root) Livewire.find(root.getAttribute('wire:id'))?.$refresh();
                } catch (err) {
                    console.error('[status] upload error', err);
                    alert('Status upload failed. Check the console.');
                }
            }, true);

            // Close the +-dropdown when clicking outside it.
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.new-chat-wrap')) {
                    document.querySelectorAll('.new-chat-wrap.open').forEach(el => el.classList.remove('open'));
                }
            });
        })();

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
    @endpush

    {{-- WebRTC scripts moved to the layout (components/layouts/app-evoory
         .blade.php) so incoming calls ring from anywhere on the site,
         not just this chat page. --}}
</div>
