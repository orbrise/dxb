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
        /* Chat page only: strip the top-header's dark strip so the
           header blends into the page background. The layout rule
           puts the class directly on the <header> element
           (.ev-header{background:var(--bg-secondary)} in app-evoory),
           so this is a compound selector (header.ev-header), NOT a
           descendant selector (header .ev-header) — the latter won't
           match because there's no .ev-header inside a <header>. */
        header.ev-header { background: transparent !important; }

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

        /* /my-chat only: unstick the top site header so the sub-header +
           chat UI scroll under it instead of pinning to the viewport. */
        header.ev-header { position: static !important; }

        /* Wallet-style sub-header shown at the top of /my-chat: dark
           bar with a lime "< Back" link on the left and a centered
           page title. The inner uses the shared .ev-container class so
           the Back link's left edge sits at exactly the same X as the
           top-header logo — no manual width/padding math. */
        .chat-subheader {
            background: #131616;
            padding: 7px 0;
            margin-bottom: 16px;
        }
        .chat-subheader-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .chat-subheader-back {
            color: #C1F11D;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        .chat-subheader-back:hover { color: #d9ff4a; text-decoration: none; }
        .chat-subheader-title {
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            margin: 0;
        }
        .chat-subheader-spacer { width: 60px; }

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

        /* Full-panel loading overlay shown inside chat-main while a
           Livewire round-trip is in flight AND the right panel has just
           been optimistically opened (sidebar hidden on mobile but the
           server hasn't sent the thread/settings pane yet). Gives the
           user feedback that content is loading, instead of a blank
           dark panel. wire:loading toggles the .is-loading class on
           chat-main via the wire:loading.class directive. */
        .chat-main-loading {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(13,16,17,0.6);
            color: var(--ev-text-2);
            z-index: 50;
            pointer-events: none;
            gap: 10px;
            font-size: 13px;
        }
        .chat-main.is-loading .chat-main-loading { display: flex; }
        .chat-main-loading::before {
            content: "";
            width: 18px; height: 18px;
            border: 2px solid var(--ev-border-strong);
            border-top-color: var(--ev-lime);
            border-radius: 50%;
            animation: chatSpin 700ms linear infinite;
        }
        @keyframes chatSpin { to { transform: rotate(360deg); } }
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

        /* Mobile-only top strip (back + centered logo). Hidden on desktop
           so it doesn't double up with the sidebar title. */
        .chat-mobile-top { display: none; }

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
            width: 28px; height: 28px;
            background: var(--ev-lime);
            color: var(--ev-lime-ink);
            font-size: 18px;
        }
        .chat-sidebar-title-actions button.new-chat-btn:hover {
            background: var(--ev-lime-dark);
            color: var(--ev-lime-ink);
        }
        .chat-sidebar-title-actions button.new-chat-btn svg {
            width: 20px;
            height: 20px;
        }
        /* Status-tab variant: outline circle "+" button instead of the lime
           filled chat-bubble icon. Sized to match the sibling ghost buttons
           (expand / kebab) at 30px. */
        .chat-sidebar-title-actions button.new-chat-btn.is-plus-btn {
            width: 30px; height: 30px;
            background: transparent;
            color: var(--ev-text);
        }
        .chat-sidebar-title-actions button.new-chat-btn.is-plus-btn:hover {
            background: var(--ev-hover);
            color: var(--ev-text);
        }
        .chat-sidebar-title-actions button.new-chat-btn.is-plus-btn svg {
            width: 22px; height: 22px;
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
        /* Loading state shown inside the new-chat panel while directoryUsers
           is being fetched for the first time. Visible immediately on tap. */
        .new-chat-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 40px 20px;
            color: var(--ev-text-3);
            font-size: 13px;
        }
        .new-chat-spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.1);
            border-top-color: var(--ev-lime);
            border-radius: 50%;
            animation: ncSpin 0.8s linear infinite;
            display: inline-block;
        }
        @keyframes ncSpin { to { transform: rotate(360deg); } }
        .new-chat-list.is-loading { opacity: 0.6; transition: opacity 150ms; }
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
            background: #1D2224;
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
            background: #1D2224;
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

        /* Skeleton loader shown in the conversation-list slot before
           Alpine finishes initializing. Alpine's x-init removes the
           element on boot, but the shimmer gives the user something
           to look at during the painted-but-not-interactive window. */
        .chat-list-skeleton {
            padding: 8px 4px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .chat-list-skeleton-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 8px;
        }
        .chat-list-skeleton-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(90deg,
                rgba(255,255,255,0.04) 0%,
                rgba(255,255,255,0.09) 50%,
                rgba(255,255,255,0.04) 100%);
            background-size: 200% 100%;
            animation: chatSkelShimmer 1.4s linear infinite;
        }
        .chat-list-skeleton-text {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .chat-list-skeleton-line {
            height: 10px;
            border-radius: 6px;
            background: linear-gradient(90deg,
                rgba(255,255,255,0.04) 0%,
                rgba(255,255,255,0.09) 50%,
                rgba(255,255,255,0.04) 100%);
            background-size: 200% 100%;
            animation: chatSkelShimmer 1.4s linear infinite;
        }
        .chat-list-skeleton-line--name    { width: 55%; }
        .chat-list-skeleton-line--preview { width: 85%; height: 8px; }
        @keyframes chatSkelShimmer {
            0%   { background-position: 100% 0; }
            100% { background-position: -100% 0; }
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

        /* Chat sidebar filter tabs (All / Unread / Active / Archived) */
        .chat-filter-tabs {
            display: flex;
            gap: 4px;
            padding: 10px 8px 6px;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
            flex-wrap: nowrap;
        }
        .chat-filter-tabs::-webkit-scrollbar { display: none; }
        .cf-tab {
            flex: 0 0 auto;
            background: transparent;
            border: none;
            color: var(--ev-text-2);
            font-family: inherit;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 999px;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 120ms, color 120ms;
        }
        .cf-tab:hover { color: var(--ev-text); background: var(--ev-hover); }
        .cf-tab.active {
            background: var(--ev-text);
            color: var(--ev-bg);
        }
        .cf-tab .cf-badge {
            background: var(--ev-danger);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            width: 18px;
            min-width: 18px;
            height: 18px;
            padding: 0px 8px 0px 7px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            box-sizing: border-box;
            font-variant-numeric: tabular-nums;
        }
        .cf-tab.active .cf-badge { background: var(--ev-bg); color: var(--ev-text); }
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

        /* Per-row kebab menu (⋮) — appears on hover (desktop) or always on
           touch devices; dropdown shows Archive/Unarchive. */
        .conv-kebab {
            position: relative;
            margin-left: 4px;
            opacity: 0;
            transition: opacity 120ms;
        }
        .conversation-item:hover .conv-kebab,
        .conv-kebab [x-show="open"]:not([style*="display: none"]) ~ *,
        .conv-kebab:focus-within { opacity: 1; }
        @media (hover: none) { .conv-kebab { opacity: 1; } }
        .conv-kebab-btn {
            background: transparent;
            border: none;
            color: var(--ev-text-3);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .conv-kebab-btn:hover { background: var(--ev-panel-3); color: var(--ev-text); }
        .conv-kebab-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 4px;
            background: var(--ev-panel-2);
            border: 1px solid var(--ev-border);
            border-radius: 10px;
            padding: 6px;
            min-width: 150px;
            z-index: 30;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }
        .conv-kebab-menu button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: transparent;
            border: none;
            padding: 8px 10px;
            border-radius: 8px;
            color: var(--ev-text);
            font-size: 13px;
            font-family: inherit;
            cursor: pointer;
            text-align: left;
        }
        .conv-kebab-menu button:hover { background: var(--ev-hover); }
        .conv-kebab-menu button i { width: 14px; color: var(--ev-text-2); }
        .conv-kebab-menu button.danger { color: var(--ev-danger); }
        .conv-kebab-menu button.danger:hover { background: rgba(239,68,68,0.12); }
        .conv-kebab-menu button.danger i { color: var(--ev-danger); }

        /* Long-press bottom sheet — desktop keeps the dropdown look, mobile
           turns the same markup into a WhatsApp-style sheet. The handle +
           title rows are hidden on desktop so the dropdown stays tight. */
        .conv-lp-handle,
        .conv-lp-title { display: none; }
        .conv-lp-backdrop { display: none; }

        @media (max-width: 900px) {
            /* Dim the app underneath so the sheet reads as a modal. */
            .conv-lp-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.55);
                z-index: 1100;
            }
            /* Re-skin the kebab dropdown as a bottom sheet. position:fixed
               detaches it from the row, left/right pin it to the viewport. */
            .conv-kebab { position: static; opacity: 1; margin-left: 0; }
            .conv-kebab-btn { display: none; } /* mobile has long-press, no button */
            .conv-kebab-menu {
                position: fixed !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                top: auto !important;
                margin: 0 !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                border-radius: 20px 20px 0 0;
                padding: 8px 6px calc(env(safe-area-inset-bottom, 0px) + 10px);
                background: var(--ev-panel-2);
                z-index: 1101;
                box-shadow: 0 -20px 40px rgba(0, 0, 0, 0.45);
                animation: convLpSheetUp 180ms ease-out;
            }
            .conv-lp-handle {
                display: block;
                width: 44px;
                height: 4px;
                border-radius: 2px;
                background: var(--ev-border);
                margin: 2px auto 10px;
            }
            .conv-lp-title {
                display: block;
                padding: 0 14px 10px;
                color: var(--ev-text-2);
                font-size: 13px;
                font-weight: 600;
                border-bottom: 1px solid var(--ev-border);
                margin-bottom: 6px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .conv-kebab-menu button {
                padding: 14px 16px;
                font-size: 15px;
                border-radius: 10px;
            }
            .conv-kebab-menu button i { width: 20px; font-size: 16px; }
        }
        @keyframes convLpSheetUp {
            from { transform: translateY(100%); }
            to   { transform: translateY(0); }
        }

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
            flex-shrink: 0;
            /* overflow:visible so the + badge can render past the circle;
               the inner <img> is clipped by its own border-radius. */
        }
        .status-my-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .status-my-avatar.has-status {
            box-shadow: 0 0 0 2px var(--ev-panel-2), 0 0 0 4px var(--ev-lime);
        }
        .status-my-plus {
            position: absolute;
            right: -3px; bottom: -3px;
            width: 20px; height: 20px;
            border-radius: 50%;
            background: #2b3a4a;
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid #fff;
        }
        .status-my-plus svg { width: 12px; height: 12px; display: block; }

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
            /* 100 clears any sticky bottom-nav / sidebar stacking context on
               mobile. 40 was being swallowed when a parent created a new
               stacking context (common with CSS transforms during morph). */
            z-index: 100;
        }
        /* !important defeats any inline `display` set by Alpine's x-show on
           the menu element — the .open class is the authoritative gate here. */
        .new-chat-wrap.open .new-chat-menu { display: block !important; }
        .new-chat-menu button {
            display: flex;
            align-items: center;
            /* Explicit left-align: icon first, label flush-left after it,
               and no implicit `center` text-align creeping in from any
               parent/global rule. */
            justify-content: flex-start;
            text-align: left;
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
            /* overflow:visible so the camera button can render past the
               circle; inner <img> clips itself via its own border-radius. */
        }
        .acct-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
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
            width: 20px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px 2px 5px;;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            box-sizing: border-box;
            font-variant-numeric: tabular-nums;
        }

        /* Bottom nav strip INSIDE the sidebar (chat, phone, orbit, …) -- */
        .chat-sidebar-tabs {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 12px 8px;
            border-top: 1px solid #80888a;
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
            color: var(--ev-lime);
        }
        .chat-sidebar-tab svg {
            width: 19px;
            height: 18px;
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
            position: relative;
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
        /* WhatsApp-style presence dot + text under the user's name.
           Green pulse = online (seen within 2 min), grey = offline. */
        .presence-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            display: inline-block;
        }
        .presence-dot.is-online {
            background: var(--ev-online);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
            animation: presencePulse 1.8s ease-in-out infinite;
        }
        .presence-dot.is-offline {
            background: var(--ev-text-3);
            box-shadow: none;
        }
        .chat-header-email:has(.presence-dot.is-offline) .presence-text,
        .chat-header-email:has(.presence-dot.is-offline) {
            color: var(--ev-text-3);
        }
        @keyframes presencePulse {
            0%, 100% { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2); }
            50%      { box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.1); }
        }
        /* Voice-note "sending" bubble shown optimistically while the upload
           is in flight — WhatsApp-style. Replaced with the real bubble once
           refreshChat() returns. */
        .message-wrapper.msg-pending .message-bubble {
            opacity: 0.75;
        }
        .msg-voice-sending {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
            color: #0b3b17;
            font-size: 13px;
            padding: 2px 0;
        }
        .msg-voice-sending .spinner {
            width: 20px; height: 20px;
            border: 2px solid rgba(10, 60, 25, 0.2);
            border-top-color: #4a9d57;
            border-radius: 50%;
            animation: msgSpin 0.9s linear infinite;
            flex-shrink: 0;
        }
        .msg-voice-sending.failed .spinner {
            border-top-color: var(--ev-danger);
            animation: none;
            background: var(--ev-danger);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .msg-voice-sending.failed .spinner::before {
            content: "!";
            font-weight: 700;
            font-size: 13px;
        }
        .msg-voice-sending-text { flex: 1; }
        .msg-voice-sending-dur {
            font-variant-numeric: tabular-nums;
            color: #2d5a37;
            opacity: 0.75;
            font-size: 12px;
        }
        .msg-voice-sending.failed .msg-voice-sending-dur { color: var(--ev-danger); }
        @keyframes msgSpin { to { transform: rotate(360deg); } }
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
        .chat-header-dropdown .dropdown-link {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 10px;
            border-radius: 8px;
            color: var(--ev-text);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: background 120ms;
        }
        .chat-header-dropdown .dropdown-link:hover { background: var(--ev-hover); color: var(--ev-text); }
        .chat-header-dropdown .dropdown-divider {
            height: 1px;
            background: var(--ev-border);
            margin: 6px 2px;
        }
        .chat-header-dropdown .dropdown-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 10px;
            color: var(--ev-text);
            font-size: 13px;
            font-weight: 500;
        }
        .chat-header-dropdown .dropdown-info-row > span:first-child { display: inline-flex; align-items: center; gap: 10px; }
        .chat-header-dropdown .dropdown-info-value { color: var(--ev-text-3); font-size: 12px; font-weight: 500; }

        /* Contact Info overlay panel — covers the chat-thread-wrapper when
           opened from the kebab → "View profile". */
        .contact-info-panel {
            position: absolute;
            inset: 0;
            background: var(--ev-bg);
            display: flex;
            flex-direction: column;
            z-index: 50;
        }
        .ci-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--ev-border);
            flex: 0 0 auto;
        }
        .ci-header h3 { margin: 0; font-size: 17px; font-weight: 600; color: var(--ev-text); }
        .ci-back {
            background: transparent;
            border: none;
            color: var(--ev-text);
            cursor: pointer;
            font-size: 22px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .ci-back:hover { background: var(--ev-hover); }
        .ci-body { flex: 1 1 auto; overflow-y: auto; }

        .ci-identity {
            text-align: center;
            padding: 28px 20px 18px;
        }
        .ci-avatar {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            margin: 0 auto 16px;
            background: var(--ev-panel-3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--ev-text);
            font-size: 48px;
            font-weight: 600;
            overflow: hidden;
        }
        .ci-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .ci-name { font-size: 22px; font-weight: 600; color: var(--ev-text); margin-bottom: 2px; }
        .ci-handle { color: var(--ev-text-2); font-size: 14px; }
        .ci-last-seen { color: var(--ev-text-3); font-size: 13px; margin-top: 4px; }

        .ci-actions {
            display: flex;
            justify-content: center;
            gap: 24px;
            padding: 18px 20px 22px;
            border-bottom: 8px solid var(--ev-panel-2);
        }
        .ci-action {
            background: transparent;
            border: none;
            color: var(--ev-text);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            font-family: inherit;
            font-size: 12px;
            text-decoration: none;
        }
        .ci-action-icon {
            width: 48px; height: 48px;
            border-radius: 50%;
            border: 1px solid var(--ev-border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--ev-text);
            font-size: 18px;
            transition: background 120ms;
        }
        .ci-action:hover .ci-action-icon { background: var(--ev-hover); }
        .ci-action-label { color: var(--ev-text-2); }

        .ci-list { padding: 4px 0; }
        .ci-row {
            display: flex;
            align-items: center;
            gap: 16px;
            width: 100%;
            background: transparent;
            border: none;
            padding: 14px 20px;
            cursor: pointer;
            color: var(--ev-text);
            font-size: 15px;
            font-family: inherit;
            text-align: left;
            border-bottom: 1px solid var(--ev-border);
            transition: background 120ms;
        }
        .ci-row:hover { background: var(--ev-hover); }
        .ci-row-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--ev-panel-3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--ev-text-2);
            font-size: 16px;
            flex: 0 0 auto;
        }
        .ci-row-label { flex: 1 1 auto; display: flex; flex-direction: column; gap: 2px; }
        .ci-row-sub { color: var(--ev-text-3); font-size: 12px; }
        .ci-row-value { color: var(--ev-text-3); font-size: 14px; }
        .ci-row-chev { color: var(--ev-text-3); }
        .ci-row.danger { color: var(--ev-danger); }
        .ci-row.danger .ci-row-icon { color: var(--ev-danger); background: rgba(239,68,68,0.1); }

        /* Per-conversation media grid */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 4px;
            padding: 4px;
        }
        .media-tile {
            position: relative;
            aspect-ratio: 1 / 1;
            background: var(--ev-panel-3);
            border: none;
            padding: 0;
            cursor: pointer;
            overflow: hidden;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ev-text-2);
            text-decoration: none;
        }
        .media-tile img,
        .media-tile video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .media-tile:hover { opacity: 0.9; }
        .media-tile-badge {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.3);
            color: #fff;
            font-size: 20px;
        }
        .media-tile.audio {
            flex-direction: column;
            gap: 6px;
            font-size: 22px;
        }
        .media-tile.audio .media-tile-dur {
            font-size: 11px;
            color: var(--ev-text-3);
        }
        .media-empty {
            padding: 60px 20px;
            text-align: center;
            color: var(--ev-text-3);
        }
        .media-empty i { font-size: 42px; margin-bottom: 12px; opacity: 0.6; }
        .media-empty p { margin: 0; font-size: 14px; }
        .media-empty small { color: var(--ev-text-3); font-size: 12px; opacity: 0.75; }

        /* Starred-message bubble indicator (small star next to the time) */
        .message-star {
            color: var(--ev-text-3);
            font-size: 10px;
            margin-left: 4px;
        }
        .message-wrapper.sent .message-star { color: rgba(10,10,10,0.55); }

        /* Starred-messages list panel */
        .starred-list { padding: 4px 0; }
        .starred-item {
            display: block;
            width: 100%;
            text-align: left;
            background: transparent;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            color: var(--ev-text);
            font-family: inherit;
            border-bottom: 1px solid var(--ev-border);
            transition: background 120ms;
        }
        .starred-item:hover { background: var(--ev-hover); }
        .starred-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--ev-text-3);
            margin-bottom: 4px;
        }
        .starred-sender { color: var(--ev-text); font-weight: 600; }
        .starred-arrow { color: var(--ev-text-3); }
        .starred-peer { color: var(--ev-text-2); }
        .starred-date { margin-left: auto; }
        .starred-body { font-size: 14px; color: var(--ev-text); line-height: 1.3; }
        .starred-body i { color: var(--ev-text-3); margin-right: 6px; }

        /* In-thread search bar — appears between chat header and messages. */
        .chat-search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: var(--ev-panel-2);
            border-bottom: 1px solid var(--ev-border);
            position: relative;
        }
        .chat-search-bar .search-icon { color: var(--ev-text-3); flex: 0 0 auto; }
        .chat-search-bar input {
            flex: 1 1 auto;
            background: transparent;
            border: none;
            outline: none;
            color: var(--ev-text);
            font-size: 14px;
            font-family: inherit;
        }
        .chat-search-bar input::placeholder { color: var(--ev-text-3); }
        .chat-search-close {
            background: transparent;
            border: none;
            color: var(--ev-text-3);
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }
        .chat-search-close:hover { background: var(--ev-hover); color: var(--ev-text); }

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

        /* Skeleton shown while a conversation is being fetched.
           Absolutely positioned over chat-main so it covers whichever
           state is currently shown (empty "Start Call" cards on the very
           first click, or the previous conversation on subsequent clicks).
           Alternates sent/received rows so the shape mirrors a real thread. */
        .chat-messages-skel {
            position: absolute;
            top: 70px; left: 0; right: 0; bottom: 0;
            overflow: hidden;
            padding: 22px 28px;
            display: none;
            flex-direction: column;
            gap: 14px;
            background: #0D1011;
            z-index: 40;
        }

        /* Optimistic chat header shown instantly on click (from selConvMeta)
           while the Livewire round-trip loads the real conversation. Sits
           at the top of chat-main above the skeleton. */
        .chat-header-optimistic {
            position: absolute;
            top: 0; left: 0; right: 0;
            padding: 14px 18px;
            background: var(--ev-panel-2);
            z-index: 41; /* just above the skeleton */
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .chat-messages-skel .skel-row {
            display: flex;
        }
        .chat-messages-skel .skel-row.right { justify-content: flex-end; }
        .chat-messages-skel .skel-bubble {
            height: 36px;
            border-radius: 14px;
            background: linear-gradient(90deg, #15191B 0%, #1E2426 50%, #15191B 100%);
            background-size: 200% 100%;
            animation: skel-shimmer 1.2s ease-in-out infinite;
        }
        .chat-messages-skel .skel-row.right .skel-bubble {
            background: linear-gradient(90deg, #152321 0%, #1C3330 50%, #152321 100%);
            background-size: 200% 100%;
        }
        .chat-messages-skel .skel-bubble.w-xs { width: 90px; }
        .chat-messages-skel .skel-bubble.w-sm { width: 140px; }
        .chat-messages-skel .skel-bubble.w-md { width: 220px; }
        .chat-messages-skel .skel-bubble.w-lg { width: 300px; height: 54px; }
        @keyframes skel-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .chat-date-sep {
            display: flex;
            justify-content: center;
            margin: 12px 0 6px;
        }
        .chat-date-sep span {
            background: #14141A;
            border: 1px solid #272A2B;
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
            /* Suppress the browser's native text-selection + callout on
               long-press. Without this, iOS Safari / Chrome Android show
               a "Copy / Lookup / Share" sheet on top of our custom
               Reply/Star/Forward/Delete menu after the hold timer fires,
               hiding our UI and confusing the user. Scoped to the bubble
               wrapper so selection still works everywhere else on the page. */
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        .message-wrapper.sent     { align-items: flex-end; }
        .message-wrapper.received { align-items: flex-start; }

        .message-bubble {
            max-width: 68%;
            /* Extra right-padding reserves space for the chevron (.msg-menu)
               so text content ends BEFORE the chevron and never overlaps
               under it. Match top padding too so short bubbles like "Hi"
               keep the chevron out of the first line. */
            padding: 10px 38px 10px 14px;
            border-radius: var(--ev-r-bubble);
            position: relative;
            word-wrap: break-word;
            line-height: 1.45;
            font-size: 14px;
        }
        .message-wrapper.received .message-bubble {
            background: var(--ev-panel-3);
            color: var(--ev-text);
            border-top-left-radius: 0;
        }
        .message-wrapper.sent .message-bubble {
            /* WhatsApp-style pale mint for outgoing bubbles. Dark text
               against light bg for good contrast. */
            background: #D9FDD3;
            color: #0a0a0a;
            border-bottom-right-radius: 0;
        }
        .message-bubble.media-only {
            padding: 4px;
            background: transparent !important;
        }

        /* Per-message dropdown (Reply / Forward / Delete).
           Chevron sits at the top-right of the bubble. The bubble reserves
           right-padding (see .message-bubble below) so content ends BEFORE
           the chevron zone — the chevron never overlaps text or media. */
        .msg-menu {
            position: absolute;
            top: 4px;
            right: 4px;
            z-index: 5;
            opacity: 0;
            transition: opacity 120ms;
        }
        .message-bubble:hover .msg-menu,
        .msg-menu:focus-within { opacity: 1; }
        @media (hover: none) { .msg-menu { opacity: 1; } }
        .msg-menu-btn {
            background: transparent;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: inherit;
            padding: 0;
            opacity: 0.65;
            transition: opacity 120ms;
        }
        .msg-menu-btn svg { width: 18px; height: 18px; }
        .msg-menu-btn:hover { opacity: 1; }
        .message-wrapper.received .msg-menu-btn { color: var(--ev-text-2); }
        .message-bubble.media-only .msg-menu-btn { color: #fff; }
        .msg-menu-list {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 6px;
            background: #fff;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 12px;
            padding: 6px;
            min-width: 170px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
        }
        .message-wrapper.sent .msg-menu-list { right: 0; }
        .message-wrapper.received .msg-menu-list { left: 0; right: auto; }
        .msg-menu-list button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: transparent;
            border: none;
            padding: 10px 12px;
            border-radius: 8px;
            color: #111;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            text-align: left;
        }
        .msg-menu-list button:hover { background: #f3f4f6; }
        .msg-menu-list button.danger { color: #e11d48; }
        .msg-menu-list button.danger:hover { background: #fef2f2; }
        .msg-menu-list button svg { width: 16px; height: 16px; flex: 0 0 auto; }

        /* Bubbles containing a reply quote get tighter padding so the
           quote hugs the bubble edges, matching WhatsApp. Right side
           keeps enough room for the hover chevron (.msg-menu, 28px wide
           at top:4px right:4px). Overrides .message-bubble's default
           padding of 10px 38px 10px 14px. */
        .message-bubble:has(> .msg-quote) {
            padding: 5px 38px 8px 5px;
        }

        /* Reply quote block pinned to the top of a bubble. WhatsApp-style:
           a thick colored accent bar on the left, a tinted translucent
           panel body, bold sender name in the accent colour, muted
           excerpt below. Rounded on all corners so it reads as a
           distinct card inside the bubble. */
        .msg-quote {
            display: block;
            position: relative;
            border-left: 4px solid #06cf9c;
            background: rgba(6, 207, 156, 0.12);
            padding: 6px 10px;
            border-radius: 6px;
            margin: 0 0 5px;
            font-size: 12.5px;
            line-height: 1.3;
            overflow: hidden;
            cursor: pointer;
            transition: background 120ms;
        }
        .msg-quote:hover { background: rgba(6, 207, 156, 0.20); }
        .message-wrapper.received .msg-quote:hover { background: rgba(52, 211, 153, 0.22); }
        .message-wrapper.received .msg-quote {
            border-left-color: #34d399;
            background: rgba(52, 211, 153, 0.14);
        }
        .msg-quote-name {
            font-weight: 700;
            color: #06cf9c;
            font-size: 13px;
            letter-spacing: 0.1px;
            margin: 0 0 1px;
        }
        .message-wrapper.received .msg-quote-name { color: #34d399; }
        .msg-quote-text {
            color: rgba(10, 10, 10, 0.72);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }
        .message-wrapper.received .msg-quote-text { color: var(--ev-text-2); }
        /* Snug gap between the quote card and the main message text so
           the bubble reads as one grouped unit rather than two stacked
           blocks with air between them. */
        .msg-quote + .message-text { margin-top: 2px; }

        /* Replying-to strip above the composer input. */
        .reply-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--ev-panel-3);
            border-left: 3px solid #22c55e;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .reply-strip-body { flex: 1 1 auto; min-width: 0; }
        .reply-strip-name {
            font-size: 12px;
            font-weight: 600;
            color: #22c55e;
            margin-bottom: 2px;
        }
        .reply-strip-text {
            font-size: 12px;
            color: var(--ev-text-2);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .reply-strip-close {
            background: transparent;
            border: none;
            color: var(--ev-text-3);
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }
        .reply-strip-close:hover { background: var(--ev-hover); color: var(--ev-text); }

        /* Forward picker modal. */
        .fwd-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .fwd-modal {
            background: var(--ev-panel-2);
            border: 1px solid var(--ev-border);
            border-radius: 14px;
            width: 100%;
            max-width: 420px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .fwd-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid var(--ev-border);
        }
        .fwd-header h3 { margin: 0; font-size: 16px; color: var(--ev-text); font-weight: 600; }
        .fwd-close {
            background: transparent; border: none; color: var(--ev-text-3); cursor: pointer;
            width: 32px; height: 32px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .fwd-close:hover { background: var(--ev-hover); color: var(--ev-text); }
        .fwd-list { overflow-y: auto; padding: 8px; flex: 1 1 auto; }
        .fwd-item {
            display: flex; align-items: center; gap: 12px;
            width: 100%;
            background: transparent; border: none;
            padding: 10px 12px; border-radius: 10px;
            cursor: pointer;
            color: var(--ev-text);
            font-family: inherit; font-size: 14px; text-align: left;
        }
        .fwd-item:hover { background: var(--ev-hover); }
        .fwd-item.selected { background: rgba(193,241,29,0.08); }
        .fwd-item.selected:hover { background: rgba(193,241,29,0.14); }
        /* Round checkbox indicator on the right of each row.
           Hollow by default, filled lime when selected. */
        .fwd-check {
            margin-left: auto;
            flex: 0 0 auto;
            width: 22px; height: 22px;
            border-radius: 50%;
            border: 2px solid var(--ev-border-strong);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            transition: background 120ms, border-color 120ms, color 120ms;
        }
        .fwd-item.selected .fwd-check {
            background: var(--ev-lime);
            border-color: var(--ev-lime);
            color: var(--ev-lime-ink);
        }
        .fwd-check svg { width: 12px; height: 12px; display: block; }
        /* Footer with counter + Send button; hidden when nothing selected. */
        .fwd-footer {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-top: 1px solid var(--ev-border);
            background: var(--ev-panel-2);
        }
        .fwd-footer-count {
            color: var(--ev-text-2);
            font-size: 13px;
            font-variant-numeric: tabular-nums;
        }
        .fwd-footer-send {
            margin-left: auto;
            padding: 9px 18px;
            border-radius: var(--ev-r-pill);
            border: none;
            background: var(--ev-lime);
            color: var(--ev-lime-ink);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: filter 120ms;
        }
        .fwd-footer-send:hover:not(:disabled) { filter: brightness(1.08); }
        .fwd-footer-send:disabled { opacity: 0.4; cursor: not-allowed; }
        .fwd-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--ev-panel-3);
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--ev-text); font-weight: 600;
            overflow: hidden; flex: 0 0 auto;
        }
        .fwd-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .fwd-name { font-weight: 500; }
        .fwd-empty {
            padding: 30px 20px;
            text-align: center;
            color: var(--ev-text-3);
            font-size: 14px;
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
            gap: 3px;
        }
        /* Tighter spacing between time and ticks — matches WhatsApp.
           letter-spacing compresses the ✓✓ glyph pair so they read as
           one unit, and the small negative margin pulls the ticks snug
           against the timestamp instead of floating 4-6px away. */
        .message-ticks { display: inline-flex; align-items: center; margin-left: -1px; }
        .message-ticks .tick {
            font-size: 11px;
            opacity: 0.7;
            letter-spacing: -6px;
            line-height: 1;
        }
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
            /* touch-action: manipulation prevents iOS Safari from double-tap
               zooming the page when the user taps a chat image. Without it,
               iOS waits ~300ms for a possible second tap before firing the
               click, and inside a scrollable+fixed shell the click can get
               swallowed outright — the user sees "nothing happens on tap". */
            touch-action: manipulation;
            -webkit-user-select: none;
        }

        /* WhatsApp-style image album — fixed-aspect tile grid.
           Layout per count:
             2  → side by side (1x2)
             3  → one big left, two stacked right
             4  → 2x2
             5+ → 2x2 with "+N" overlay on the 4th tile
           Each tile fills its cell; a 2-px gap acts as a divider. */
        .msg-album {
            display: grid;
            width: 320px;
            max-width: 100%;
            height: 320px;
            border-radius: 14px;
            overflow: hidden;
            gap: 2px;
            background: #000; /* shows between tiles as the divider */
            cursor: pointer;
        }
        .msg-album[data-count="2"] {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr;
        }
        .msg-album[data-count="3"] {
            grid-template-columns: 2fr 1fr;
            grid-template-rows: 1fr 1fr;
        }
        .msg-album[data-count="3"] .msg-album-tile:first-child { grid-row: span 2; }
        .msg-album[data-count="4"],
        .msg-album[data-count="5"],
        .msg-album[data-count="6"],
        .msg-album[data-count="7"],
        .msg-album[data-count="8"],
        .msg-album[data-count="9"] {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
        }
        .msg-album-tile {
            position: relative;
            overflow: hidden;
            background: #000 center/cover no-repeat;
        }
        .msg-album-tile img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 120ms;
        }
        .msg-album-tile:hover img { transform: scale(1.02); }
        /* +N overlay on the last visible tile when there are 5+ images. */
        .msg-album-tile.has-more::after {
            content: attr(data-more);
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 28px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Album caption below the grid — same look as a plain text message. */
        .msg-album-caption {
            margin: 6px 2px 0;
            font-size: 14px;
            color: inherit;
            white-space: pre-wrap;
            line-height: 1.4;
        }
        /* Compact album on narrow phones so it leaves room beside the time. */
        @media (max-width: 480px) {
            .msg-album { width: 260px; height: 260px; }
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

        /* Voice player — self-contained dark pill (not a theme-colored bubble).
           Lives inside a `.message-bubble.media-only` so no background bleeds
           through from the surrounding wrapper. The pill is identical for
           sent/received because voice bubbles need to read the same regardless
           of side. Waveform bars are generated by JS on init. */
        .msg-voice {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #1C1F21;
            /* Extra bottom-padding reserves room for the timestamp, which is
               absolute-positioned below the waveform (see .vp-body/.vp-time
               below). This lets the play button and the waveform share the
               SAME vertical centre instead of the time pulling vp-body down. */
            padding: 10px 16px 18px 12px;
            border-radius: 14px;
            min-width: 260px;
            max-width: 340px;
            box-sizing: border-box;
        }
        .msg-voice audio { display: none; }
        .msg-voice .vp-play {
            flex: 0 0 auto;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: transparent;
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.85);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .msg-voice .vp-play:hover { background: rgba(255,255,255,0.08); }
        .msg-voice .vp-play svg {
            width: 14px; height: 14px; display: block;
            /* Nudge the triangle slightly right so it looks centred inside
               the ring. Pause glyph (replaced in JS) resets the offset. */
            margin-left: 2px;
        }
        .msg-voice .vp-play.is-playing svg { margin-left: 0; }

        /* vp-body is sized to the WAVEFORM only (26px). The timestamp hangs
           below as position:absolute so it doesn't inflate vp-body's height
           — otherwise `align-items: center` on the pill would centre the
           combined (waveform + time) stack, pushing the waveform above the
           play-button centre. With this trick both land on the same y. */
        .msg-voice .vp-body {
            position: relative;
            flex: 1 1 auto;
            min-width: 0;
            height: 26px;
        }

        /* Waveform: horizontal row of thin vertical bars. Bars are generated
           in JS (initVoicePlayer) using a deterministic PRNG seeded off the
           message so the shape is stable across reloads but varied per clip.
           Playback progress is reflected by toggling `.is-played` on each
           bar as the audio's currentTime crosses that bar's time offset. */
        .msg-voice .vp-waveform {
            display: flex;
            align-items: center;
            gap: 2px;
            height: 100%;
            cursor: pointer;
            touch-action: none; /* let pointer events drag-seek on touch */
        }
        .msg-voice .vp-bar {
            flex: 1 1 0;
            min-width: 2px;
            background: rgba(255,255,255,0.35);
            border-radius: 2px;
            transition: background-color 60ms linear;
            pointer-events: none;
        }
        .msg-voice .vp-bar.is-played { background: #fff; }

        .msg-voice .vp-time {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            font-size: 11.5px;
            color: rgba(255,255,255,0.6);
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        /* Sent-side voice player keeps the same dark pill — don't invert
           colors, mockup shows identical treatment for both sides. */
        .message-wrapper.sent .msg-voice,
        .message-wrapper.received .msg-voice { background: #1C1F21; }
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
            min-width: 0;
            box-sizing: border-box;
        }
        .chat-input-form input,
        .chat-input-form textarea {
            flex: 1 1 0;
            min-width: 0;
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
            min-width: 36px; min-height: 36px;
            flex: 0 0 36px;
            aspect-ratio: 1 / 1;
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
        /* Send button — lime round pill matching the evoory accent.
           Targets both a bare submit button AND anything with .send-btn
           class so future refactors don't lose the styling. */
        .chat-input-form button[type="submit"],
        .send-btn {
            width: 40px; height: 40px;
            min-width: 40px; min-height: 40px;
            flex: 0 0 40px;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            background: #C1F11D;
            color: #0D1011;
            border: none;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 15px;
            transition: transform 120ms, background 120ms;
            padding: 0;
            box-sizing: border-box;
        }
        .chat-input-form button[type="submit"]:hover,
        .send-btn:hover { background: #d4f84d; transform: scale(1.05); }
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
        /* Icon slot — fixed-width box so row labels align. The SVGs
           supplied by product carry their own fill colours, so we
           drop the previous coloured background chip and let the
           glyphs render naturally. svg sits centred inside the slot. */
        .attach-menu button .icon {
            width: 24px; height: 20px;
            display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .attach-menu button .icon svg { display: block; }

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

        /* -----------------------------------------------------------
           Video upload — WhatsApp-style preview + progress overlay.
           Shown when the user picks a video from the attach menu;
           lets them preview the clip, see the first-frame poster
           we're about to attach, and watch upload progress before
           the video appears in the thread. 100 MB client-side cap
           matches the server. --------------------------------------- */
        .vu-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.78);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center; justify-content: center;
            z-index: 10001;
            padding: 16px;
            animation: vuFadeIn 150ms ease-out;
        }
        .vu-overlay.open { display: flex; }
        @keyframes vuFadeIn { from { opacity: 0 } to { opacity: 1 } }

        .vu-sheet {
            width: 100%;
            max-width: 460px;
            background: #0b0d10;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            overflow: hidden;
            display: flex; flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,0.55);
        }
        .vu-head {
            display: flex; align-items: center;
            padding: 12px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            gap: 10px;
        }
        .vu-head h3 {
            margin: 0;
            font-size: 15px; font-weight: 600;
            color: #fff;
            flex: 1 1 auto;
            min-width: 0;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .vu-close {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.06);
            color: #fff;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .vu-close:hover { background: rgba(255,255,255,0.14); }

        .vu-body {
            position: relative;
            background: #000;
            aspect-ratio: 16 / 9;
            width: 100%;
        }
        .vu-body video {
            width: 100%; height: 100%;
            object-fit: contain;
            background: #000;
            display: block;
        }

        .vu-progress {
            position: absolute; inset: 0;
            display: none;
            align-items: center; justify-content: center;
            flex-direction: column;
            gap: 10px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 13px;
        }
        .vu-progress.open { display: flex; }
        .vu-progress-ring {
            width: 72px; height: 72px;
            position: relative;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .vu-progress-ring svg {
            width: 100%; height: 100%;
            transform: rotate(-90deg);
        }
        .vu-progress-ring circle {
            fill: none;
            stroke-width: 5;
            stroke-linecap: round;
        }
        .vu-progress-ring .bg { stroke: rgba(255,255,255,0.15); }
        .vu-progress-ring .fg {
            stroke: #C1F11D;
            stroke-dasharray: 201; /* 2 * π * 32 ≈ 201 */
            stroke-dashoffset: 201;
            transition: stroke-dashoffset 150ms linear;
        }
        .vu-progress-ring .pct {
            position: absolute;
            font-size: 13px; font-weight: 600;
            color: #fff;
        }

        .vu-meta {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px;
            color: rgba(255,255,255,0.65);
            font-size: 12.5px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .vu-meta .vu-dot { opacity: 0.4; }

        .vu-caption {
            padding: 0 14px 12px;
        }
        .vu-caption input {
            width: 100%;
            background: #15181c;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 10px 12px;
            color: #fff;
            font-size: 14px;
            box-sizing: border-box;
        }
        .vu-caption input:focus { outline: none; border-color: rgba(193,241,29,0.5); }

        .vu-actions {
            display: flex; gap: 10px;
            padding: 12px 14px 14px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .vu-btn {
            flex: 1 1 auto;
            height: 42px;
            border-radius: 10px;
            border: none;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            gap: 8px;
            transition: filter 120ms;
        }
        .vu-btn:disabled { opacity: 0.55; cursor: not-allowed; }
        .vu-btn.cancel { background: rgba(255,255,255,0.08); color: #fff; }
        .vu-btn.cancel:hover:not(:disabled) { background: rgba(255,255,255,0.14); }
        .vu-btn.send { background: #C1F11D; color: #0D1011; }
        .vu-btn.send:hover:not(:disabled) { filter: brightness(1.08); }

        /* Compact mobile sheet. */
        @media (max-width: 640px) {
            .vu-sheet { max-width: 100%; border-radius: 12px; }
            .vu-body { aspect-ratio: 9 / 12; }
        }

        /* Chat-thread video bubble — compact WhatsApp-style tile.
           The bubble is just a poster + play overlay; clicking it opens
           the fullscreen lightbox (openVideoLightbox) for playback.
           Keeps the thread clean — no inline <video controls>. */
        .msg-video {
            display: block;
            width: 220px;
            max-width: 100%;
            height: 300px;
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: transform 120ms;
        }
        .msg-video:hover { transform: scale(0.98); }
        .msg-video-poster {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #000;
        }
        .msg-video-poster-fallback {
            background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
        }
        .msg-video-play {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 56px; height: 56px;
            border-radius: 50%;
            background: rgba(0,0,0,0.55);
            color: #fff;
            display: inline-flex;
            align-items: center; justify-content: center;
            pointer-events: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .msg-video-play svg { margin-left: 2px; } /* nudge play glyph to optical centre */

        /* Chat video playback popup — opened by clicking a video bubble.
           Sheet-style modal (not fullscreen) with a title bar + close
           button on top and the <video> inside, constrained so a portrait
           clip doesn't eat the whole viewport. Mirrors the reference
           design the product spec'd. ESC + click-outside both close. */
        .chat-video-lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.72);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10050;
            padding: 12px;
            animation: cvlFadeIn 150ms ease-out;
        }
        .chat-video-lightbox.open { display: flex; }
        @keyframes cvlFadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* The inner sheet — fills most of the viewport so videos read big,
           WhatsApp-Web style. The flex column lets the <video> body expand
           to all leftover space between the head and reply bars; min-height
           0 on .cvl-body is what makes `flex: 1` actually honour the cap. */
        .chat-video-lightbox .cvl-sheet {
            width: 100%;
            max-width: min(1100px, 96vw);
            max-height: 96vh;
            min-height: 0;
            background: #0b0d10;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 24px 60px rgba(0,0,0,0.6);
        }
        .chat-video-lightbox .cvl-head {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            gap: 10px;
            flex: 0 0 auto;
            background: #0b0d10;
        }
        .chat-video-lightbox .cvl-back {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #fff;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 auto;
        }
        .chat-video-lightbox .cvl-back:hover { background: rgba(255,255,255,0.08); }
        .chat-video-lightbox .cvl-head-info {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .chat-video-lightbox .cvl-sender {
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chat-video-lightbox .cvl-time {
            color: rgba(255,255,255,0.55);
            font-size: 12px;
            margin-top: 2px;
        }
        .chat-video-lightbox .cvl-share {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #fff;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 auto;
        }
        .chat-video-lightbox .cvl-share:hover { background: rgba(255,255,255,0.08); }
        .chat-video-lightbox .cvl-body {
            flex: 1 1 auto;
            min-height: 60vh;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            overflow: hidden;
        }
        .chat-video-lightbox video {
            display: block;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            background: #000;
            outline: none;
        }
        /* Reply bar at the bottom of the lightbox — mirrors the chat
           composer: emoji trigger, input, send button. Submitting it
           sends a reply in the thread with reply_to_id set to the video
           message, then closes the lightbox. */
        .chat-video-lightbox .cvl-reply {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px calc(10px + env(safe-area-inset-bottom, 0px));
            background: #0b0d10;
            border-top: 1px solid rgba(255,255,255,0.06);
            flex: 0 0 auto;
        }
        .chat-video-lightbox .cvl-reply-input {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            align-items: center;
            background: #15181c;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 999px;
            padding: 4px 4px 4px 12px;
        }
        .chat-video-lightbox .cvl-reply-input input {
            flex: 1 1 auto;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: 14px;
            padding: 8px 4px;
            min-width: 0;
        }
        .chat-video-lightbox .cvl-reply-input input::placeholder { color: rgba(255,255,255,0.4); }
        .chat-video-lightbox .cvl-reply-emoji {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: rgba(255,255,255,0.65);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 auto;
        }
        .chat-video-lightbox .cvl-reply-emoji:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .chat-video-lightbox .cvl-reply-send {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: none;
            background: #22c55e;
            color: #fff;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 auto;
            transition: filter 120ms;
        }
        .chat-video-lightbox .cvl-reply-send:hover { filter: brightness(1.08); }
        .chat-video-lightbox .cvl-reply-send:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Mobile: edge-to-edge sheet so the video eats the whole screen.
           Safe-area insets handled on the reply bar's padding below (so
           the iOS home indicator doesn't overlap the Send button). */
        @media (max-width: 640px) {
            .chat-video-lightbox { padding: 0; }
            .chat-video-lightbox .cvl-sheet {
                max-width: 100%;
                max-height: 100vh;
                max-height: 100dvh;
                border-radius: 0;
                height: 100%;
            }
        }

        /* -------- Simple image lightbox (single OR album) --------
           Opened by tapping a .msg-image bubble (single) OR a .msg-album
           tile (album, with prev/next walk through items). Click outside
           the image, Esc, or the X closes it. */
        .chat-lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            z-index: 10080;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            animation: cvlFadeIn 150ms ease-out;
        }
        .chat-lightbox.open { display: flex; }
        .chat-lightbox img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        }
        .chat-lightbox .lb-close,
        .chat-lightbox .lb-prev,
        .chat-lightbox .lb-next {
            position: absolute;
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: none;
            width: 44px; height: 44px;
            border-radius: 50%;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transition: background 120ms;
        }
        .chat-lightbox .lb-close { top: 16px; right: 16px; }
        .chat-lightbox .lb-prev  { left: 12px; top: 50%; transform: translateY(-50%); }
        .chat-lightbox .lb-next  { right: 12px; top: 50%; transform: translateY(-50%); }
        .chat-lightbox .lb-close:hover,
        .chat-lightbox .lb-prev:hover,
        .chat-lightbox .lb-next:hover { background: rgba(255,255,255,0.2); }
        /* Position counter + caption at the bottom so they don't fight the image. */
        .chat-lightbox .lb-caption {
            position: absolute;
            left: 50%;
            bottom: 20px;
            transform: translateX(-50%);
            max-width: 80%;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 13px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chat-lightbox .lb-count {
            position: absolute;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 13px;
            padding: 4px 10px;
            border-radius: 999px;
            font-variant-numeric: tabular-nums;
        }
        /* Hide prev/next for single-image mode. */
        .chat-lightbox.single .lb-prev,
        .chat-lightbox.single .lb-next,
        .chat-lightbox.single .lb-count { display: none; }

        /* Top header strip — sender name + sent time, matches the
           cvl-head pattern used by the video lightbox so both media
           types feel identical to the user. Gradient backdrop so the
           text stays readable over any image. The strip itself is
           pointer-events: none so the image behind it is still
           tappable; the X close button sits on top of it. */
        .chat-lightbox .lb-head {
            position: absolute;
            top: 0; left: 0; right: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 70px 14px 20px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.65), rgba(0,0,0,0));
            z-index: 2;
            pointer-events: none;
        }
        .chat-lightbox .lb-head-info {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            color: #fff;
        }
        .chat-lightbox .lb-sender {
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chat-lightbox .lb-time {
            color: rgba(255,255,255,0.65);
            font-size: 12px;
            margin-top: 2px;
        }

        /* Reply bar at the bottom — mirrors cvl-reply. Submitting it
           sends an inline reply with reply_to_id set to the image
           message, then closes the lightbox. Hidden when the lightbox
           is opened without a messageId (e.g. the media gallery). */
        .chat-lightbox .lb-reply {
            position: absolute;
            left: 0; right: 0; bottom: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px calc(12px + env(safe-area-inset-bottom, 0px));
            background: linear-gradient(to top, rgba(0,0,0,0.75), rgba(0,0,0,0));
            z-index: 2;
        }
        .chat-lightbox .lb-reply-input {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            align-items: center;
            background: rgba(21,24,28,0.95);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 999px;
            padding: 4px 4px 4px 14px;
        }
        .chat-lightbox .lb-reply-input input {
            flex: 1 1 auto;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: 16px; /* 16px keeps iOS Safari from zooming on focus */
            padding: 8px 4px;
            min-width: 0;
        }
        .chat-lightbox .lb-reply-input input::placeholder { color: rgba(255,255,255,0.4); }
        .chat-lightbox .lb-reply-emoji {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: rgba(255,255,255,0.65);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 auto;
        }
        .chat-lightbox .lb-reply-emoji:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .chat-lightbox .lb-reply-send {
            width: 42px; height: 42px;
            border-radius: 50%;
            border: none;
            background: #22c55e;
            color: #fff;
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 auto;
            transition: filter 120ms;
        }
        .chat-lightbox .lb-reply-send:hover { filter: brightness(1.08); }
        .chat-lightbox .lb-reply-send:disabled { opacity: 0.5; cursor: not-allowed; }
        /* When the lightbox was opened without a messageId (e.g. the
           profile media gallery) we hide the reply bar entirely. */
        .chat-lightbox.no-reply .lb-reply { display: none; }
        /* Bump the caption pill up so it clears the reply bar. */
        .chat-lightbox .lb-caption { bottom: 90px; }
        .chat-lightbox.no-reply .lb-caption { bottom: 20px; }
        /* Push the counter badge down so it clears the header strip. */
        .chat-lightbox .lb-count { top: 72px; }

        /* -------- WhatsApp-style image preview modal -------- */
        /* Fullscreen overlay shown AFTER the user picks an image but BEFORE
           it uploads. User can review, add a caption, and either send or
           discard. Mirrors the WhatsApp send-media flow. */
        .img-preview-modal {
            position: fixed;
            inset: 0;
            background: #0a0d0e;
            z-index: 10000;
            display: none;
            flex-direction: column;
        }
        .img-preview-modal.open { display: flex; }
        .img-preview-topbar {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            color: #fff;
            background: #15191b;
        }
        .ipm-icon-btn {
            background: transparent;
            border: none;
            color: #fff;
            cursor: pointer;
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%;
            transition: background 120ms;
        }
        .ipm-icon-btn:hover { background: rgba(255,255,255,0.08); }
        .ipm-count {
            font-size: 14px;
            color: rgba(255,255,255,0.75);
            font-variant-numeric: tabular-nums;
        }
        .img-preview-body {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 16px;
            background: #0a0d0e;
            min-height: 0;
        }
        .img-preview-body img {
            max-width: 100%;
            max-height: 100%;
            display: block;
            object-fit: contain;
            border-radius: 8px;
        }
        .img-preview-footer {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px calc(12px + env(safe-area-inset-bottom, 0px));
            background: #15191b;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .ipm-caption {
            flex: 1 1 auto;
            min-width: 0;
            background: #0a0d0e;
            border: 1px solid rgba(255,255,255,0.08);
            color: #fff;
            padding: 12px 16px;
            border-radius: 999px;
            outline: none;
            font-family: inherit;
            font-size: 14px;
        }
        .ipm-caption::placeholder { color: rgba(255,255,255,0.4); }
        .ipm-caption:focus { border-color: rgba(193,241,29,0.4); }
        .ipm-send-btn {
            flex: 0 0 auto;
            width: 44px; height: 44px;
            border-radius: 50%;
            background: #22c55e;
            color: #fff;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 120ms, transform 120ms;
        }
        .ipm-send-btn:hover { background: #16a34a; transform: scale(1.05); }
        .ipm-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* WhatsApp-style thumbnail strip for multi-image sends. Shows every
           selected image as a small square; the active one gets a lime
           border. A trailing "+" tile re-opens the picker so the user can
           keep adding until they hit the 20-image cap. Hidden entirely when
           only one image is queued. */
        .ipm-thumbs {
            flex: 0 0 auto;
            display: none;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: #15191b;
            border-top: 1px solid rgba(255,255,255,0.06);
            overflow-x: auto;
            scrollbar-width: thin;
        }
        .ipm-thumbs.open { display: flex; }
        .ipm-thumbs::-webkit-scrollbar { height: 4px; }
        .ipm-thumbs::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
        .ipm-thumb {
            position: relative;
            flex: 0 0 auto;
            width: 52px; height: 52px;
            border-radius: 6px;
            background: #000 center/cover no-repeat;
            border: 2px solid transparent;
            cursor: pointer;
            transition: border-color 120ms, transform 120ms;
        }
        .ipm-thumb:hover { transform: scale(1.05); }
        .ipm-thumb.active { border-color: #C1F11D; }
        .ipm-thumb-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.65);
            font-size: 22px;
            font-weight: 300;
            line-height: 1;
        }
        .ipm-thumb-add:hover { background: rgba(255,255,255,0.16); color: #fff; }

        /* Optimistic "sending image" bubble — same pattern as video. The
           local object URL is pinned as the tile background; a translucent
           overlay with a progress ring + "Uploading…" label sits on top
           until the upload completes, then refreshChat() swaps in the real
           bubble. Matches .msg-image dimensions so layout doesn't jump. */
        .msg-image-pending {
            display: block;
            width: 240px;
            max-width: 100%;
            min-height: 180px;
            border-radius: 10px;
            overflow: hidden;
            background: #000 center/cover no-repeat;
            position: relative;
        }
        .msg-image-pending::after {
            content: "";
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.45);
        }
        .msg-image-pending.failed::after { background: rgba(185,28,28,0.55); }

        /* Optimistic "sending video" bubble — the thumbnail we captured
           client-side is pinned as the poster, and a circular progress
           ring is overlaid while the upload is in flight. Matches the
           real .msg-video dimensions so the layout doesn't jump when
           refreshChat() swaps the pending bubble out for the real one. */
        .msg-video-pending {
            display: block;
            width: 220px;
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
            background: #000 center/cover no-repeat;
            position: relative;
        }
        .msg-video-pending::after {
            content: "";
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.35);
        }
        .msg-video-pending.failed::after {
            background: rgba(185, 28, 28, 0.55);
        }
        .mvp-ring {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 64px; height: 64px;
            display: inline-flex;
            align-items: center; justify-content: center;
            z-index: 2;
        }
        .mvp-ring svg {
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            transform: rotate(-90deg);
        }
        .mvp-ring circle {
            fill: none; stroke-width: 5; stroke-linecap: round;
        }
        .mvp-ring .bg { stroke: rgba(255,255,255,0.22); }
        .mvp-ring .fg {
            stroke: #C1F11D;
            stroke-dasharray: 182; /* 2π·29 ≈ 182 */
            stroke-dashoffset: 182;
            transition: stroke-dashoffset 150ms linear;
        }
        .mvp-pct {
            position: relative;
            font-size: 12px; font-weight: 700;
            color: #fff;
            z-index: 1;
        }
        .mvp-label {
            position: absolute;
            left: 0; right: 0; bottom: 10px;
            text-align: center;
            color: #fff; font-size: 12px; font-weight: 500;
            z-index: 2;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }

        /* Voice recording bar — hidden by default, JS toggles .open
           while a recording is actively being captured. Same permanent-
           display bug as the attachment preview above. */
        .voice-recording-bar {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
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
            flex: 0 0 auto;
        }
        .voice-recording-bar.paused .rec-dot { animation: none; opacity: 0.4; }
        .voice-recording-bar .rec-time {
            font-variant-numeric: tabular-nums;
            font-weight: 600;
            min-width: 40px;
        }
        .voice-recording-bar .rec-spacer { flex: 1 1 auto; }
        .voice-recording-bar .rec-btn {
            width: 34px; height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: 50%;
            color: var(--ev-text-2);
            cursor: pointer;
            padding: 0;
            transition: background 0.15s ease, color 0.15s ease;
            flex: 0 0 auto;
        }
        .voice-recording-bar .rec-btn:hover { background: var(--ev-hover); color: var(--ev-text); }
        .voice-recording-bar .rec-btn svg { width: 18px; height: 18px; display: block; }
        .voice-recording-bar .rec-btn.delete { color: var(--ev-danger); }
        .voice-recording-bar .rec-btn.delete:hover { background: rgba(239,68,68,0.12); color: var(--ev-danger); }
        .voice-recording-bar .rec-btn.send {
            background: #22c55e;
            color: #fff;
            width: 38px; height: 38px;
        }
        .voice-recording-bar .rec-btn.send:hover { background: #16a34a; color: #fff; }
        .voice-recording-bar .rec-btn.view-once {
            border: 1.5px dashed var(--ev-text-3);
            font-size: 11px;
            font-weight: 700;
            font-family: inherit;
        }
        .voice-recording-bar .rec-btn.view-once.on {
            border-style: solid;
            border-color: #22c55e;
            color: #22c55e;
            background: rgba(34,197,94,0.1);
        }
        @keyframes recPulse { 50% { opacity: 0.3; } }

        /* While recording, hide the normal input affordances so only the
           recording bar is interactive inside the form. */
        /* Block strip — shown above the composer when either side has
           blocked the other. */
        .block-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 8px;
            color: var(--ev-danger);
            font-size: 13px;
            margin-bottom: 8px;
        }
        .block-strip-link {
            background: transparent;
            border: none;
            color: var(--ev-danger);
            text-decoration: underline;
            cursor: pointer;
            font-weight: 600;
            padding: 0;
            font-family: inherit;
            font-size: 13px;
        }
        .chat-input-form.is-blocked {
            opacity: 0.5;
            pointer-events: none;
        }

        .chat-input-form.is-recording #chatReplyInput,
        .chat-input-form.is-recording #emojiToggleBtn,
        .chat-input-form.is-recording #attachBtn,
        .chat-input-form.is-recording #micBtn,
        .chat-input-form.is-recording > button[type="submit"] { display: none !important; }

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
            /* App-like fixed viewport — lock the whole page so the user
               can never scroll past the chat container and reveal the
               body background as black rubber-band. Only the inner
               scrollable panes (conversation list, message thread)
               scroll; the outer shell is pinned. overscroll-behavior
               also prevents the "pull-to-refresh" browser gesture from
               reloading the page mid-chat. */
            html, body {
                overflow: hidden !important;
                overscroll-behavior: none;
                /* --app-height is published by the layout JS from
                   window.visualViewport.height so it always reflects the
                   ACTUAL visible area — shrinks when the iOS Safari
                   keyboard opens, where 100dvh does not. Fallbacks keep
                   old UAs working. */
                height: 100vh;
                height: 100dvh;
                height: var(--app-height, 100dvh);
                position: fixed;
                width: 100%;
            }

            /* Full-viewport shell: kill desktop card frame + margins so
               the chat fills the phone screen edge-to-edge. Height is
               keyboard-aware (--app-height) instead of inset:0, because
               inset:0 anchors to the layout viewport which on iOS Safari
               still extends BEHIND the keyboard — the composer would get
               hidden. Width via `right:0` keeps us edge-to-edge. */
            .ev-chat-shell {
                max-width: 100%;
                margin: 0;
                padding: 0;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 100vh;
                height: 100dvh;
                height: var(--app-height, 100dvh);
            }
            .ev-chat-shell > .mb-3 {
                border-radius: 0;
                padding: 0;
                margin: 0 !important;
                height: 100%;
            }

            .chat-container {
                flex-direction: column;
                /* Full viewport on mobile — the site's global bottom
                   nav is hidden on this page (see body rule above), so
                   the chat's own icon-strip nav sits at the true
                   viewport bottom like a native app. --app-height is
                   keyboard-aware (visualViewport), 100dvh handles the
                   collapsing address-bar on mobile browsers; 100vh is
                   the fallback for older UAs. */
                height: 100vh;
                height: 100dvh;
                height: var(--app-height, 100dvh);
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

            /* Mobile-only top strip: back arrow left, evoory logo centered. */
            .chat-mobile-top {
                position: relative;
                display: flex;
                align-items: center;
                padding: 10px 12px;
                margin: 0 -12px 4px;
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }
            .chat-mobile-top-back {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                color: var(--ev-lime);
                text-decoration: none;
                font-size: 15px;
                font-weight: 500;
                flex: 1 1 0;
            }
            .chat-mobile-top-logo {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                display: inline-flex;
                align-items: center;
                text-decoration: none;
                color: var(--ev-lime);
                font-weight: 700;
            }
            .chat-mobile-top-logo img { height: 24px; width: auto; display: block; }
            .chat-mobile-top-spacer { flex: 1 1 0; }

            /* Hide desktop-only header actions on mobile — the mockup
               keeps only the primary action ("+") as a floating button. */
            .chat-sidebar-title-actions button[title="Expand panel"],
            .chat-sidebar-title-actions button[title="More"] {
                display: none;
            }

            /* Keep the "+" button inline in the Messages header on mobile
               (next to the title), not as a floating action button.
               `position: relative` MUST stay so the status +-dropdown
               (.new-chat-menu, absolute) anchors to this wrap. If this
               ever regresses to `static`, the menu attaches to .chat-sidebar
               and renders off-screen below it. */
            .chat-sidebar-title-actions .new-chat-wrap {
                position: relative;
                z-index: auto;
            }
            .chat-sidebar-title-actions button.new-chat-btn {
                width: 36px;
                height: 36px;
                font-size: 18px;
                box-shadow: none;
            }
            .chat-sidebar-title-actions button.new-chat-btn svg {
                width: 20px;
                height: 20px;
            }
            /* Hide "+" on tabs where it has no meaning (calls / gallery /
               settings) so it doesn't linger as a dead button. Alpine
               toggles `.mobile-fab-hide` from the tab state. */
            .new-chat-wrap.mobile-fab-hide { display: none; }
            /* Base .new-chat-menu rule already handles positioning (top +
               left-align under .chat-sidebar-title) — no mobile override
               needed here anymore. */

            /* Bottom nav strip — pin to viewport bottom on mobile so it
               doesn't get pushed off screen when the conversation list
               is long. Icons enlarged to native-app scale so they hit
               a proper tap target and read at arm's length. */
            .chat-sidebar-tabs {
                position: sticky;
                bottom: 0;
                padding: 12px 6px calc(12px + env(safe-area-inset-bottom, 0px));
                background: var(--ev-panel-2);
                border-top: 1px solid #727B7E;
            }
            .chat-sidebar-tab {
                width: 52px;
                height: 48px;
                border-radius: 14px;
                color: #fff;
            }
            .chat-sidebar-tab svg {
                width: 22px;
                height: 22px;
            }
            .chat-sidebar-tab svg [stroke] { stroke: #fff; }
            .chat-sidebar-tab svg [fill]:not([fill="none"]) { fill: #fff; }
            /* Active tab icon follows the lime theme colour. These rules
               must sit AFTER the forced-white ones above (same specificity)
               so they win for `.active` while non-active tabs stay white. */
            .chat-sidebar-tab.active svg [stroke] { stroke: var(--ev-lime); }
            .chat-sidebar-tab.active svg [fill]:not([fill="none"]) { fill: var(--ev-lime); }
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
            .chat-header-name {
                font-size: 14px;
                /* Long names (e.g. imported MR agency listings — "Aura Agency
                   20+ Models – Russian escort agency in Dubai") wrap to 3-4
                   lines on a 393px viewport and shove the call/menu icons off
                   the row. Clamp to one line + ellipsis so the header stays
                   compact. The flex ancestors need min-width:0 for the
                   ellipsis to actually kick in; without that, flex children
                   refuse to shrink below their content width. */
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
                min-width: 0;
            }
            .chat-header-email { font-size: 11px; }
            /* The inner <div> (name + email stack) and the outer peer-info
               wrapper both need min-width:0 so their flex basis can be the
               shrunk width rather than the intrinsic content width. */
            .chat-header-name,
            .chat-header-email,
            .chat-header .chat-header-avatar + div,
            .chat-header-peer {
                min-width: 0;
            }
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

            /* Composer — lime circular send button matching the desktop
               styling, tighter padding to keep the input area from eating
               too much vertical space on mobile. */
            .chat-input { padding: 8px 10px 12px; }
            .chat-input-form { padding: 4px 6px 4px 12px; gap: 6px; }
            .chat-input-form button[type="submit"] {
                background: #C1F11D;
                color: #0D1011;
                width: 40px;
                height: 40px;
            }
            .chat-input-form button[type="submit"]:hover {
                background: #d4f84d;
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
                width: 28px; height: 28px;
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
    {{-- Wallet-style sub-header: dark strip with "< Back" and the
         page title. Shown at the top of /my-chat so the user has a
         clear way back out of the chat UI. --}}
    <div class="chat-subheader">
        <div class="ev-container chat-subheader-inner">
            <a href="{{ url('/') }}" wire:navigate class="chat-subheader-back" aria-label="Back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
            <h1 class="chat-subheader-title">Chat &amp; Calls</h1>
            <div class="chat-subheader-spacer"></div>
        </div>
    </div>

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
                            section: @entangle('settingsSection'),
                            /* newChatOpen entangled so clicking '+' flips the panel
                               open client-side instantly — no waiting for the server
                               round-trip. Livewire still syncs behind the scenes to
                               load directoryUsers. */
                            {{-- `.live` so Alpine's `newChatOpen = true` from the "+"
                                 button pushes to the server immediately. Without .live
                                 the entangle is deferred — the server never saw the
                                 flip, so `updatedNewChatOpen()` never fired and the
                                 sidebar/directory searches never cleared. --}}
                            newChatOpen: @entangle('newChatOpen').live,
                            chatFilter: 'all',
                            /* selConvMeta — populated from the clicked conv-item so
                               the chat header (name/avatar/email) renders instantly
                               while the Livewire round-trip loads the messages. */
                            selConvMeta: null
                         }"
                         {{-- Fallback to the server-rendered values so Alpine
                              can never flip data-tab (etc.) to an empty string
                              during entangle init. If it does, every CSS rule
                              scoped by [data-tab="chats"] stops matching and
                              the whole chat page renders blank (fixed bug:
                              sidebar + right-pane empty state both vanishing
                              on refresh). --}}
                         :data-tab="tab || '{{ $activeTab ?: 'chats' }}'"
                         :data-has-conv="selConv ? '1' : '0'"
                         :data-has-status="selStatus ? '1' : '0'"
                         :data-has-section="section ? '1' : '0'">
                        {{-- Left Sidebar - Conversations
                             .mobile-hidden is now driven by Alpine state
                             (selConv / section / selStatus) instead of
                             server-rendered classes. This means tapping a
                             conversation / settings row hides the sidebar
                             INSTANTLY — no waiting for the Livewire
                             round-trip — while the server catches up and
                             fills the right-panel content. Fixes the
                             "clicks feel slow on prod" perception where
                             the round-trip latency was blocking the UI. --}}
                        <div class="chat-sidebar {{ $newChatOpen ? 'new-chat-open' : '' }}"
                             :class="{ 'mobile-hidden': selConv || section || selStatus }"
                             id="chatSidebar">
                            <div class="chat-sidebar-header">
                                @php
                                    // Shared helper — cached ~30s and shared with
                                    // the mobile nav / header / dashboard so this
                                    // doesn't fire a duplicate DB query on every
                                    // Livewire render / poll cycle.
                                    $__badges = auth()->check()
                                        ? \App\Support\ChatBadges::for(auth()->id())
                                        : ['unread_messages' => 0, 'missed_calls' => 0];
                                    $chatUnreadCount = $__badges['unread_messages'];
                                    $missedCallCount = $__badges['missed_calls'];
                                @endphp

                                {{-- Mobile-only mini top strip: back arrow (returns
                                     to listings via history.back) + centered evoory
                                     logo, matching the reference design. Hidden on
                                     desktop via CSS. --}}
                                <div class="chat-mobile-top">
                                    <a href="javascript:history.back()" class="chat-mobile-top-back" aria-label="Back">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                        <span>Back</span>
                                    </a>
                                    <a href="/" class="chat-mobile-top-logo">
                                        @php $set = \App\Models\Setting::first(); @endphp
                                        @if($set && $set->app_logo)
                                            <img src="{{ smart_asset($set->app_logo) }}" alt="{{ $set->app_name ?? 'evoory' }}">
                                        @else
                                            <span>{{ $set->app_name ?? 'evoory' }}</span>
                                        @endif
                                    </a>
                                    <span class="chat-mobile-top-spacer"></span>
                                </div>

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
                                            {{-- `.stop` prevents the document-level click-outside handler
                                                 from firing on this same bubble and immediately
                                                 closing the dropdown. --}}
                                            <button type="button" class="new-chat-btn"
                                                    :class="{ 'is-plus-btn': tab === 'status' }"
                                                    @click.stop="if (tab === 'status') { $event.currentTarget.closest('.new-chat-wrap').classList.toggle('open'); } else { newChatOpen = true; $nextTick(() => document.querySelector('.new-chat-search input')?.focus()); }"
                                                    :title="tab === 'status' ? 'Add status' : 'New chat'">
                                                {{-- Custom chat-bubble-with-plus icon supplied by product.
                                                     Uses currentColor so it inherits lime-ink on the Chats
                                                     tab and white on the Status tab variant. --}}
                                                <svg viewBox="0 0 22 22" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.7753 14.9015C-1.03709 7.78574 4.04328 0 11.5 0H11.9141C17.4847 0 22 4.65257 22 10.3925C22.0003 11.9169 21.7092 13.4265 21.1432 14.8349C20.5772 16.2434 19.7474 17.5231 18.7013 18.601C17.6552 19.6789 16.4132 20.5339 15.0463 21.1171C13.6794 21.7003 12.2144 22.0003 10.735 22H0.646478C0.512769 22.0003 0.382272 21.9578 0.273039 21.8783C0.163806 21.7989 0.0812349 21.6864 0.0367486 21.5565C-0.00773768 21.4266 -0.0119408 21.2856 0.024721 21.1531C0.0613827 21.0206 0.137097 20.9031 0.241391 20.8169L2.78544 18.7126C2.8939 18.623 2.97133 18.4996 3.0062 18.3609C3.04108 18.2222 3.03152 18.0756 2.97896 17.943L1.7753 14.9015ZM12.4534 7.24471C12.4534 6.9803 12.3514 6.72671 12.17 6.53974C11.9885 6.35277 11.7424 6.24773 11.4858 6.24773C11.2292 6.24773 10.9831 6.35277 10.8016 6.53974C10.6202 6.72671 10.5182 6.9803 10.5182 7.24471V9.90332H7.93805C7.68144 9.90332 7.43533 10.0084 7.25388 10.1953C7.07242 10.3823 6.97049 10.6359 6.97049 10.9003C6.97049 11.1647 7.07242 11.4183 7.25388 11.6053C7.43533 11.7922 7.68144 11.8973 7.93805 11.8973H10.5182V14.5559C10.5182 14.8203 10.6202 15.0739 10.8016 15.2609C10.9831 15.4478 11.2292 15.5529 11.4858 15.5529C11.7424 15.5529 11.9885 15.4478 12.17 15.2609C12.3514 15.0739 12.4534 14.8203 12.4534 14.5559V11.8973H15.0335C15.2901 11.8973 15.5362 11.7922 15.7177 11.6053C15.8992 11.4183 16.0011 11.1647 16.0011 10.9003C16.0011 10.6359 15.8992 10.3823 15.7177 10.1953C15.5362 10.0084 15.2901 9.90332 15.0335 9.90332H12.4534V7.24471Z"/>
                                                </svg>
                                            </button>
                                            {{-- Visibility purely controlled by the `.open` class on
                                                 the parent wrap (toggled by the +-button @click). No
                                                 x-show/x-cloak here — Alpine was stamping an inline
                                                 `display:none` on first paint (when tab !== 'status')
                                                 that beat the stylesheet's `.open` rule, so the menu
                                                 never appeared when the user later switched to Status
                                                 and tapped +. The button itself is already hidden on
                                                 non-chats/status tabs via `.mobile-fab-hide`, so there's
                                                 no risk of this menu opening on the wrong tab. --}}
                                            <div class="new-chat-menu">
                                                <button type="button" onclick="document.getElementById('statusMediaInput')?.click(); this.closest('.new-chat-wrap').classList.remove('open');">
                                                    <i class="fa fa-image"></i> Photos &amp; Videos
                                                </button>
                                                <button type="button" wire:click="openTextStatus" onclick="this.closest('.new-chat-wrap').classList.remove('open')">
                                                    <i class="fa fa-pen"></i> Text
                                                </button>
                                            </div>
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

                                {{-- WhatsApp-style search: rounded pill with a search icon inside.
                                     Only shown on Chats + Calls tabs — Status/Gallery/Settings
                                     don't need conversation search. x-show binds to Alpine's
                                     client-side `tab` so tab-swap is instant. --}}
                                {{-- @click.outside clears the search when the user taps
                                     anywhere else (new-chat "+", a conversation in the
                                     list, empty sidebar space). Previously `searchTerm`
                                     stuck around and the result dropdown stayed open
                                     until the user manually deleted the input.

                                     x-show also hides the whole search container the
                                     instant the New-chat panel opens — without this the
                                     user briefly saw the stale sidebar search + results
                                     flash BEFORE the server round-trip cleared them. --}}
                                <div class="chat-search"
                                     x-show="!newChatOpen && ((tab || '{{ $activeTab ?: 'chats' }}') === 'chats' || (tab || '{{ $activeTab ?: 'chats' }}') === 'calls')"
                                     x-cloak
                                     @click.outside="if ($wire.searchTerm) { $wire.set('searchTerm', ''); }">
                                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <input
                                        type="text"
                                        placeholder="Search conversations..."
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
                                {{-- Skeleton loader — bridges the gap between the
                                     browser painting the HTML and Alpine finishing
                                     init. Without this, every panel body inside
                                     is x-cloak'd (hidden) during Alpine boot and
                                     the user sees a blank black area under the
                                     "Messages" header on slow mobile. The skeleton
                                     is visible by default, then x-init removes
                                     it the instant Alpine takes over. --}}
                                <div class="chat-list-skeleton" x-show="false">
                                    @for($i = 0; $i < 7; $i++)
                                        <div class="chat-list-skeleton-row">
                                            <div class="chat-list-skeleton-avatar"></div>
                                            <div class="chat-list-skeleton-text">
                                                <div class="chat-list-skeleton-line chat-list-skeleton-line--name"></div>
                                                <div class="chat-list-skeleton-line chat-list-skeleton-line--preview"></div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <div x-show="(tab || '{{ $activeTab ?: 'chats' }}') === 'chats'" x-cloak>
                                    @php
                                        $supportConvs = collect($conversations)->filter(fn($c) => !empty($c['is_support']));
                                        $nonSupportConvs = collect($conversations)->filter(fn($c) => empty($c['is_support']));
                                        // Exclude archived — they don't appear in the Unread
                                        // filter list, so they shouldn't inflate the badge.
                                        $unreadCount = $nonSupportConvs
                                            ->filter(fn($c) => ($c['unread_count'] ?? 0) > 0 && empty($c['is_archived']))
                                            ->count();
                                    @endphp

                                    {{-- Filter pills: All / Unread / Active / Archived.
                                         Client-side only — Alpine `chatFilter` controls
                                         which conv items render below via x-show on
                                         each item's data-tags attribute. --}}
                                    <div class="chat-filter-tabs">
                                        <button type="button" class="cf-tab" :class="chatFilter === 'all'      ? 'active' : ''" @click="chatFilter = 'all'">All</button>
                                        <button type="button" class="cf-tab" :class="chatFilter === 'unread'   ? 'active' : ''" @click="chatFilter = 'unread'">
                                            Unread @if($unreadCount > 0)<span class="cf-badge">{{ $unreadCount }}</span>@endif
                                        </button>
                                        <button type="button" class="cf-tab" :class="chatFilter === 'favorites' ? 'active' : ''" @click="chatFilter = 'favorites'">Favorites</button>
                                        <button type="button" class="cf-tab" :class="chatFilter === 'active'   ? 'active' : ''" @click="chatFilter = 'active'">Active</button>
                                        <button type="button" class="cf-tab" :class="chatFilter === 'archived' ? 'active' : ''" @click="chatFilter = 'archived'">Archived</button>
                                    </div>

                                    @if($supportConvs->isNotEmpty())
                                        <div class="conv-section-header" x-show="chatFilter === 'all'">Support</div>
                                        @foreach($supportConvs as $conversation)
                                            <div x-show="chatFilter === 'all'">
                                                @include('livewire.partials._conv-item', ['conversation' => $conversation])
                                            </div>
                                        @endforeach
                                    @endif

                                    @foreach($nonSupportConvs as $conversation)
                                        @php
                                            // Archive is EXCLUSIVE — an archived conv only shows
                                            // in the Archived filter and nowhere else (not All,
                                            // Unread, Active, or Favorites). Previously 'all'
                                            // was always added, so archived chats leaked back
                                            // into every tab.
                                            if (!empty($conversation['is_archived'])) {
                                                $tags = ['archived'];
                                            } else {
                                                $tags = ['all'];
                                                if (($conversation['unread_count'] ?? 0) > 0) $tags[] = 'unread';
                                                $lastAt = $conversation['last_message_at'] ?? null;
                                                if ($lastAt && \Carbon\Carbon::parse($lastAt)->greaterThan(now()->subHours(24))) $tags[] = 'active';
                                                if (!empty($conversation['is_pinned']))   $tags[] = 'favorites';
                                            }
                                            $tagsStr = implode(' ', $tags);
                                        @endphp
                                        <div x-show="'{{ $tagsStr }}'.split(' ').includes(chatFilter)">
                                            @include('livewire.partials._conv-item', ['conversation' => $conversation])
                                        </div>
                                    @endforeach

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
                                <div x-show="(tab || '{{ $activeTab ?: 'chats' }}') === 'status'" x-cloak>
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
                                            <span class="status-my-plus" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                            </span>
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
                                                 wire:click="viewStatus({{ $s->id }})"
                                                 @click="selStatus = {{ $s->id }}">
                                                <div class="my-status-thumb">
                                                    @if($s->type === 'photo' && $s->media_path)
                                                        <img src="{{ route('status.media', $s->id) }}" alt="">
                                                    @elseif($s->type === 'video' && $s->media_path)
                                                        <video src="{{ route('status.media', $s->id) }}" muted></video>
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
                                                 wire:click="viewStatus({{ $entry['latest_id'] }})"
                                                 @click="selStatus = {{ $entry['latest_id'] }}">
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
                                <div x-show="(tab || '{{ $activeTab ?: 'chats' }}') === 'settings'" x-cloak>
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
                                             wire:click="setSettingsSection('{{ $item['id'] }}')"
                                             {{-- Optimistic UI — Alpine flips section instantly
                                                  so the sidebar hides on mobile before the
                                                  server round-trip completes. --}}
                                             @click="section = '{{ $item['id'] }}'">
                                            <div class="settings-row-icon"><i class="fa {{ $item['icon'] }}"></i></div>
                                            <div class="settings-row-body">
                                                <div class="settings-row-title">{{ $item['title'] }}</div>
                                                <div class="settings-row-sub">{{ $item['sub'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div x-show="(tab || '{{ $activeTab ?: 'chats' }}') === 'gallery'" x-cloak>
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
                                <div x-show="(tab || '{{ $activeTab ?: 'chats' }}') === 'calls'" x-cloak>
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
                            {{-- Panel is always in the DOM, Alpine controls visibility
                                 via x-show so clicking '+' opens it INSTANTLY (no
                                 Livewire round-trip blocking the UI). The server
                                 syncs newChatOpen via @entangle so directoryUsers
                                 loads in the background. --}}
                            <div class="new-chat-panel" x-show="newChatOpen" x-cloak>
                                <div class="new-chat-head">
                                    {{-- @click flips Alpine (instant UI close), wire:click
                                         fires closeNewChat() on server to also clear the
                                         search term so the panel reopens fresh next time. --}}
                                    <button type="button" class="new-chat-back"
                                            @click="newChatOpen = false"
                                            wire:click="closeNewChat"
                                            title="Back">
                                        <i class="fa fa-arrow-left"></i>
                                    </button>
                                    <span class="new-chat-title">New chat</span>
                                </div>
                                <div class="new-chat-search">
                                    <i class="fa fa-search"></i>
                                    <input type="text"
                                           wire:model.live.debounce.500ms="newChatSearch"
                                           placeholder="Name, email or @username">
                                </div>
                                <div class="new-chat-help">
                                    Name or email finds public profiles. <b>@username</b> finds that username (public or private).
                                </div>
                                <div class="new-chat-list" wire:loading.class="is-loading" wire:target="newChatSearch">
                                    {{-- Idle state: panel just opened, search box empty.
                                         We no longer eager-load the first 100 users here —
                                         that query made the "+" feel laggy on prod. The
                                         user types → we query. In-flight queries show the
                                         spinner (hidden by default, revealed by wire:loading). --}}
                                    @if(empty(trim((string) $newChatSearch)))
                                        <div class="new-chat-empty" wire:loading.remove wire:target="newChatSearch">
                                            <i class="fa fa-search"></i>
                                            <div>Type a name, email or @username to find people</div>
                                        </div>
                                        <div class="new-chat-loading" wire:loading wire:target="newChatSearch" style="display:none;">
                                            <span class="new-chat-spinner"></span>
                                            <span>Searching&hellip;</span>
                                        </div>
                                    @else
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
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="chat-main" id="chatMain">
                            {{-- Skeleton overlay shown while ANY method that loads a
                                 conversation is in flight — including the very first
                                 click (when the chat-thread-wrapper doesn't exist yet).
                                 Lives at chat-main level so wire:loading finds it on
                                 initial state too. --}}
                            {{-- Optimistic chat header — renders instantly from
                                 the clicked conv-item's metadata (selConvMeta)
                                 while the Livewire round-trip is in flight.
                                 The real header appears underneath as soon as
                                 the server returns, replacing this stub. --}}
                            <div class="chat-header-optimistic"
                                 wire:loading.flex
                                 wire:target="selectConversation,startConversation,startNewChatWith,openChatFromGallery,openChatFromCall"
                                 x-show="selConvMeta"
                                 x-cloak>
                                <div class="chat-header-info">
                                    <button type="button" class="mobile-back-btn" @click="selConv = null; selConvMeta = null">
                                        <i class="fa fa-angle-left"></i><span class="mobile-back-label"> Back</span>
                                    </button>
                                    <div class="chat-header-avatar">
                                        <template x-if="selConvMeta && selConvMeta.isSupport">
                                            <i class="fa fa-headset" aria-hidden="true"></i>
                                        </template>
                                        <template x-if="selConvMeta && !selConvMeta.isSupport && selConvMeta.avatarUrl">
                                            <img :src="selConvMeta.avatarUrl" alt="" x-on:error="selConvMeta.avatarUrl = null">
                                        </template>
                                        <template x-if="selConvMeta && !selConvMeta.isSupport && !selConvMeta.avatarUrl">
                                            <span x-text="(selConvMeta.name || '?').charAt(0).toUpperCase()"></span>
                                        </template>
                                    </div>
                                    <div>
                                        <div class="chat-header-name">
                                            <template x-if="selConvMeta && selConvMeta.isSupport">
                                                <span>Support <span class="support-badge">HELP</span></span>
                                            </template>
                                            <template x-if="selConvMeta && !selConvMeta.isSupport">
                                                <span x-text="selConvMeta.name"></span>
                                            </template>
                                        </div>
                                        {{-- Optimistic status line. Before the server returns we don't
                                             know the peer's presence yet, so render a neutral grey dot
                                             + "connecting…" that matches the shape of the real presence
                                             row. Prevents the "email flashes then flips to online/offline"
                                             jank the user noticed. --}}
                                        <div class="chat-header-email">
                                            <template x-if="selConvMeta && selConvMeta.isSupport">
                                                <span>We usually reply within an hour</span>
                                            </template>
                                            <template x-if="selConvMeta && !selConvMeta.isSupport">
                                                <span style="display:inline-flex;align-items:center;gap:6px;color:var(--ev-text-3);">
                                                    <span class="presence-dot is-offline"></span>
                                                    <span>connecting&hellip;</span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="chat-messages-skel"
                                 wire:loading.flex
                                 wire:target="selectConversation,startConversation,startNewChatWith,openChatFromGallery,openChatFromCall">
                                <div class="skel-row"><div class="skel-bubble w-sm"></div></div>
                                <div class="skel-row"><div class="skel-bubble w-md"></div></div>
                                <div class="skel-row right"><div class="skel-bubble w-sm"></div></div>
                                <div class="skel-row right"><div class="skel-bubble w-lg"></div></div>
                                <div class="skel-row"><div class="skel-bubble w-xs"></div></div>
                                <div class="skel-row"><div class="skel-bubble w-md"></div></div>
                                <div class="skel-row right"><div class="skel-bubble w-md"></div></div>
                                <div class="skel-row"><div class="skel-bubble w-sm"></div></div>
                                <div class="skel-row right"><div class="skel-bubble w-xs"></div></div>
                            </div>

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
                                            <button type="button" class="status-viewer-close" wire:click="closeStatusViewer" @click="selStatus = null" title="Close">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="status-viewer-body">
                                        @if($selectedStatus['type'] === 'photo')
                                            <img src="{{ route('status.media', $selectedStatus['id']) }}"
                                                 class="status-viewer-media" alt="">
                                        @elseif($selectedStatus['type'] === 'video')
                                            <video src="{{ route('status.media', $selectedStatus['id']) }}"
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
                                    <svg width="97" height="97" viewBox="0 0 97 97" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M94.8019 34.807L94.8949 35.0752L95.2403 36.2339L95.5293 37.2739L95.6525 37.7488L95.8545 38.6087C98.4752 50.5904 96.5624 63.1323 90.4945 73.7534C84.4402 84.4074 74.654 92.3653 63.0783 96.0477L62.2621 96.2934L61.26 96.5554L59.8114 96.9035C59.2997 97.0215 58.7695 97.0314 58.2539 96.9326C57.7383 96.8337 57.2483 96.6282 56.8145 96.3289C56.3808 96.0296 56.0125 95.643 55.7328 95.193C55.4531 94.7431 55.268 94.2396 55.1889 93.7139C55.0264 92.6349 55.2697 91.5335 55.8707 90.6278C56.4716 89.722 57.3862 89.0779 58.4336 88.8231L59.5993 88.5262C60.3266 88.3324 60.9523 88.1461 61.4762 87.9673C70.7724 84.7869 78.6026 78.2764 83.496 69.659C88.4182 61.0479 90.0651 50.9109 88.1267 41.1554L87.9873 40.5146L87.8014 39.7674L87.5691 38.9117L87.2903 37.9495C86.978 36.8999 87.0664 35.7697 87.5379 34.783C88.0094 33.7963 88.8296 33.0252 89.8359 32.6226C90.3035 32.4354 90.8029 32.3434 91.3056 32.3519C91.8083 32.3604 92.3044 32.4692 92.7656 32.672C93.2268 32.8749 93.644 33.1678 93.9934 33.5342C94.3428 33.9005 94.6176 34.333 94.8019 34.807ZM6.83982 32.5919C6.92736 32.6192 7.01357 32.6492 7.09842 32.682C8.10245 33.0785 8.92283 33.8428 9.39694 34.8233C9.87106 35.8039 9.96431 36.9291 9.65821 37.9761C9.29454 39.2167 9.0319 40.2492 8.87027 41.0735C6.94031 50.8212 8.58912 60.9476 13.507 69.5505C18.4048 78.1756 26.2446 84.6897 35.551 87.867L36.1693 88.0656L36.5208 88.1679L37.3087 88.3849L38.7109 88.735C39.7642 88.986 40.686 89.629 41.2934 90.5364C41.9008 91.4439 42.1494 92.5495 41.9899 93.634C41.8402 94.6509 41.2981 95.5659 40.4828 96.1779C39.6675 96.7898 38.6457 97.0486 37.6421 96.8973L37.3633 96.8461L36.1996 96.5698L35.1692 96.3036L34.2681 96.0477L33.864 95.9249C22.3122 92.2325 12.549 84.2806 6.50646 73.6429C0.427431 63.0032 -1.48149 50.4363 1.15858 38.4367L1.31415 37.7816L1.51619 37.0139L1.90813 35.6525L2.06168 35.1407C2.21008 34.6552 2.45139 34.2042 2.77184 33.8133C3.09229 33.4224 3.4856 33.0992 3.92931 32.8624C4.37301 32.6255 4.85842 32.4795 5.35781 32.4327C5.8572 32.386 6.36079 32.4414 6.83982 32.5919ZM48.5924 16.3778C66.4463 16.3778 80.9181 31.0421 80.9181 49.1335C80.9181 67.2248 66.4463 81.8891 48.5924 81.8891C30.7386 81.8891 16.2668 67.2248 16.2668 49.1335C16.2668 31.0421 30.7406 16.3778 48.5924 16.3778ZM48.5924 24.5668C35.2035 24.5668 24.3482 35.5665 24.3482 49.1335C24.3482 62.7004 35.2035 73.7002 48.5924 73.7002C61.9833 73.7002 72.8366 62.7004 72.8366 49.1335C72.8366 35.5665 61.9833 24.5668 48.5924 24.5668ZM48.5924 3.68791e-05C60.7255 -0.0150399 72.4207 4.59313 81.3565 12.9098L81.8393 13.3746L82.3949 13.9355L83.0212 14.5947L83.7203 15.3501C84.0769 15.7401 84.3504 16.2003 84.5237 16.7021C84.697 17.2039 84.7663 17.7365 84.7275 18.2667C84.6886 18.7969 84.5423 19.3133 84.2978 19.7836C84.0532 20.254 83.7157 20.6682 83.3061 21.0005C82.4652 21.6832 81.4018 22.0209 80.3267 21.9466C79.2516 21.8723 78.2433 21.3914 77.5016 20.5992C77.0626 20.1284 76.6551 19.7073 76.2793 19.3361L75.5722 18.6523L75.2489 18.3596C67.8831 11.7917 58.404 8.17498 48.5924 8.18894C38.7717 8.17452 29.2844 11.7974 21.9157 18.3759L21.6854 18.5848L21.1742 19.0761L20.5944 19.6596L19.5943 20.7139C18.0588 22.3701 15.5253 22.5564 13.7696 21.1438C13.3759 20.8271 13.0476 20.4348 12.8035 19.9895C12.5594 19.5441 12.4044 19.0544 12.3471 18.5483C12.2899 18.0422 12.3317 17.5296 12.47 17.0399C12.6084 16.5502 12.8406 16.0928 13.1534 15.6941L13.3352 15.4771L14.1535 14.5947L14.897 13.8209C15.2499 13.4619 15.5758 13.1446 15.8748 12.8689C24.8044 4.57856 36.4795 -0.0138865 48.5904 3.68791e-05" fill="#C0C4CC"/>
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
                                            <button type="button" class="settings-pane-back" wire:click="setSettingsSection(null)" @click="section = null" title="Back">
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
                                <div class="chat-thread-wrapper" x-data="{ contactInfoOpen: false, mediaViewOpen: false, starredViewOpen: false }">
                                {{-- Chat Header --}}
                                <div class="chat-header">
                                    <div class="chat-header-info">
                                        <button class="mobile-back-btn" wire:click="closeConversation" @click="selConv = null">
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
                                                    {{ Str::limit($selectedUser->name ?? $selectedUser->email, 12, '...') }}
                                                @endif
                                            </div>
                                            <div class="chat-header-email">
                                                @if($selectedIsSupport)
                                                    We usually reply within an hour
                                                @else
                                                    @php $__isOnline = $selectedUser->isOnline(); @endphp
                                                    <span class="presence-dot {{ $__isOnline ? 'is-online' : 'is-offline' }}"></span>
                                                    <span class="presence-text">{{ $selectedUser->presenceStatus() }}</span>
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
                                                    @php
                                                        $curConv = collect($conversations)->firstWhere('id', $selectedConversationId);
                                                        $isPinned = $curConv['is_pinned'] ?? false;
                                                        $isArchived = $curConv['is_archived'] ?? false;
                                                        $disappearingLabel = ['never'=>'Never','24h'=>'24 hours','1w'=>'1 week','1m'=>'1 month'][$prefDisappearingDefault ?? 'never'] ?? 'Never';
                                                    @endphp
                                                    <button type="button" onclick="openChatSearchBar(); this.closest('.chat-header-menu').classList.remove('open');">
                                                        <i class="fa fa-search"></i> Search
                                                    </button>
                                                    <button type="button" wire:click="togglePin({{ $selectedConversationId }})"
                                                            onclick="this.closest('.chat-header-menu').classList.remove('open')">
                                                        <i class="fa fa-star"></i> {{ $isPinned ? 'Unmark favorite' : 'Mark favorite' }}
                                                    </button>
                                                    <button type="button" wire:click="toggleArchive({{ $selectedConversationId }})"
                                                            onclick="this.closest('.chat-header-menu').classList.remove('open')">
                                                        <i class="fa {{ $isArchived ? 'fa-box-open' : 'fa-archive' }}"></i> {{ $isArchived ? 'Unarchive' : 'Archive' }}
                                                    </button>
                                                    <button type="button"
                                                            @click="mediaViewOpen = true"
                                                            onclick="this.closest('.chat-header-menu').classList.remove('open')">
                                                        <i class="fa fa-images"></i> Media
                                                    </button>
                                                    <div class="dropdown-divider"></div>
                                                    @if($selectedUser)
                                                        <button type="button"
                                                                @click="contactInfoOpen = true"
                                                                onclick="this.closest('.chat-header-menu').classList.remove('open')">
                                                            <i class="fa fa-user"></i> View profile
                                                        </button>
                                                    @endif
                                                    <div class="dropdown-divider"></div>
                                                    @if($selectedUser)
                                                        @php $isBlocked = $curConv['is_blocked_by_me'] ?? false; @endphp
                                                        <button type="button" class="danger"
                                                                wire:click="toggleBlock({{ $selectedUser->id }})"
                                                                onclick="return confirm('{{ $isBlocked ? 'Unblock' : 'Block' }} this user?')">
                                                            <i class="fa fa-ban"></i> {{ $isBlocked ? 'Unblock' : 'Block' }}
                                                        </button>
                                                    @endif
                                                    <button type="button" class="danger"
                                                            wire:click="deleteConversation"
                                                            onclick="return confirm('Move this conversation to trash?')">
                                                        <i class="fa fa-trash"></i> Move to trash
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- In-thread search bar (hidden until the kebab → "Search"
                                     item is clicked). Pure client-side: filters .message-wrapper
                                     visibility by matching the input against message text. --}}
                                <div class="chat-search-bar" id="chatSearchBar" style="display:none;">
                                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <input type="text" id="chatSearchInput" placeholder="Search messages..." oninput="filterChatMessages(this.value)">
                                    <button type="button" class="chat-search-close" onclick="closeChatSearchBar()" aria-label="Close search"><i class="fa fa-times"></i></button>
                                </div>

                                {{-- Chat Messages --}}
                                <div class="chat-messages" id="chatMessages" wire:loading.remove wire:target="selectConversation,startConversation,startNewChatWith,openChatFromGallery,openChatFromCall">
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
                                            // Audio is included so the voice-message pill (self-contained
                                            // dark bubble with waveform + timer) stands alone instead of
                                            // sitting inside a mint/panel bubble that fights its own styling.
                                            // 'album' is also media-only — the caption (if any) renders INSIDE
                                            // the album block via .msg-album-caption, not in a separate text pill.
                                            $isMediaOnly = in_array($attType, ['image', 'video', 'audio', 'album'], true) && !$hasText;
                                        @endphp
                                        @if($showSeparator)
                                            <div class="chat-date-sep" wire:key="sep-{{ $msgDate }}">
                                                <span>{{ $renderDateLabel($message['created_at']) }}</span>
                                            </div>
                                        @endif
                                        <div class="message-wrapper {{ $message['is_mine'] ? 'sent' : 'received' }} {{ !empty($message['expires_at']) ? 'disappearing' : '' }}"
                                             wire:key="msg-{{ $message['id'] }}-{{ $message['status'] }}"
                                             data-message-id="{{ $message['id'] }}"
                                             x-data="{ menuOpen: false }"
                                             @if(!empty($message['expires_at'])) data-expires-at="{{ $message['expires_at'] }}" @endif>
                                            <div class="message-bubble {{ $attType === 'call' ? 'call-bubble' : '' }} {{ $isMediaOnly ? 'media-only' : '' }}">
                                                {{-- Per-message action menu: Reply / Forward / Delete.
                                                     Chevron trigger shown on hover (desktop) or always on
                                                     touch devices. Alpine-scoped to each message. --}}
                                                <div class="msg-menu" @click.outside="menuOpen = false">
                                                    <button type="button" class="msg-menu-btn" @click.stop="menuOpen = !menuOpen" aria-label="Message actions">
                                                        <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path d="M5 7l5 6 5-6z"/></svg>
                                                    </button>
                                                    <div class="msg-menu-list" x-show="menuOpen" x-transition.opacity.duration.100ms x-cloak @click.stop>
                                                        <button type="button" @click="menuOpen = false; $wire.startReply({{ $message['id'] }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                                                            Reply
                                                        </button>
                                                        <button type="button" @click="menuOpen = false; $wire.toggleStar({{ $message['id'] }})">
                                                            <svg viewBox="0 0 24 24" fill="{{ !empty($message['is_starred']) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                            {{ !empty($message['is_starred']) ? 'Unstar' : 'Star' }}
                                                        </button>
                                                        <button type="button" @click="menuOpen = false; $wire.startForward({{ $message['id'] }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 17 20 12 15 7"/><path d="M4 18v-2a4 4 0 0 1 4-4h12"/></svg>
                                                            Forward
                                                        </button>
                                                        @if($message['is_mine'])
                                                            <button type="button" class="danger"
                                                                    @click="menuOpen = false; if (confirm('Delete this message?')) $wire.deleteMessage({{ $message['id'] }})">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                                                Delete
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(!empty($message['reply_to']))
                                                    {{-- Quoted parent snapshot: name + excerpt. Clicking it
                                                         scrolls the parent message into view and flashes the
                                                         same gallery-highlight pulse used by the Gallery
                                                         tab's "jump to message" behavior. --}}
                                                    <div class="msg-quote"
                                                         data-jump-to="{{ $message['reply_to']['id'] }}"
                                                         role="button" tabindex="0"
                                                         aria-label="Jump to replied message">
                                                        <div class="msg-quote-name">{{ $message['reply_to']['sender_name'] }}</div>
                                                        <div class="msg-quote-text">{{ $message['reply_to']['excerpt'] }}</div>
                                                    </div>
                                                @endif
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
                                                    @if(($message['attachment_type'] ?? '') === 'album' && !empty($message['album_items']))
                                                        {{-- WhatsApp-style image album. The whole grid is one
                                                             clickable surface; we also give each tile its own
                                                             index so the lightbox opens on the tapped image.
                                                             Max 4 visible tiles — if 5+ images, the 4th gets a
                                                             "+N" overlay showing remaining count. --}}
                                                        @php
                                                            $items   = $message['album_items'];
                                                            $count   = count($items);
                                                            $visible = min($count, 4);
                                                            $extra   = max(0, $count - $visible);
                                                            // Serialise once so the lightbox knows every item in the
                                                            // album; JSON-encoded + attr-escaped on the data attribute
                                                            // keeps it compact and safe.
                                                            $albumPayload = json_encode([
                                                                'messageId' => $message['id'],
                                                                'items'     => $items,
                                                            ], JSON_UNESCAPED_SLASHES);
                                                        @endphp
                                                        <div class="msg-album"
                                                             data-count="{{ $visible }}"
                                                             data-album='{{ $albumPayload }}'
                                                             data-message-id="{{ $message['id'] }}"
                                                             data-sender-name="{{ $message['is_mine'] ? 'You' : ($message['sender_name'] ?? 'User') }}"
                                                             data-sent-at="{{ \Carbon\Carbon::parse($message['created_at'])->format('M j, g:i A') }}"
                                                             role="button" tabindex="0"
                                                             aria-label="Image album, {{ $count }} images">
                                                            @for($i = 0; $i < $visible; $i++)
                                                                <div class="msg-album-tile {{ ($i === $visible - 1 && $extra > 0) ? 'has-more' : '' }}"
                                                                     @if($i === $visible - 1 && $extra > 0) data-more="+{{ $extra }}" @endif
                                                                     data-idx="{{ $i }}">
                                                                    <img src="{{ $items[$i]['url'] }}" alt="" loading="lazy">
                                                                </div>
                                                            @endfor
                                                        </div>
                                                        @if(!empty($message['message']))
                                                            {{-- Album caption lives inside the bubble, under the grid, so
                                                                 it's visually tied to the album even when non-empty. --}}
                                                            <p class="msg-album-caption">{{ $message['message'] }}</p>
                                                        @endif
                                                    @elseif($message['attachment_type'] === 'image')
                                                        {{-- No inline onclick — a document-level delegated click
                                                             handler (see albumLightboxWired below) opens the
                                                             lightbox for both albums AND single images. The
                                                             delegation path is more reliable on mobile Safari,
                                                             where inline <img onclick="…"> inside a scrollable +
                                                             position:fixed shell can silently drop taps. --}}
                                                        <img src="{{ $message['attachment_url'] }}"
                                                             alt="Image"
                                                             class="msg-image"
                                                             data-message-id="{{ $message['id'] }}"
                                                             data-sender-name="{{ $message['is_mine'] ? 'You' : ($message['sender_name'] ?? 'User') }}"
                                                             data-sent-at="{{ \Carbon\Carbon::parse($message['created_at'])->format('M j, g:i A') }}">
                                                    @elseif($message['attachment_type'] === 'audio')
                                                        <div class="msg-voice" data-vp-duration="{{ $message['attachment_duration'] ?? 0 }}">
                                                            {{-- WebM blobs from MediaRecorder ship without a duration header,
                                                                 so Chrome reports duration=Infinity on load. onloadedmetadata
                                                                 seeks to +∞ which forces the browser to scan the file, then
                                                                 rewinds. --}}
                                                            <audio preload="metadata" src="{{ $message['attachment_url'] }}"
                                                                   onloadedmetadata="initVoicePlayer(this)"></audio>
                                                            <button type="button" class="vp-play" data-vp="play" aria-label="Play">
                                                                <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z"/></svg>
                                                            </button>
                                                            <div class="vp-body">
                                                                {{-- Waveform bars are generated by JS on init so we can
                                                                     keep the Blade output tiny and let the bar count
                                                                     adapt to pill width. data-vp=seek makes the whole
                                                                     waveform a click/drag target. --}}
                                                                <div class="vp-waveform" data-vp="seek"></div>
                                                                <div class="vp-time">{{ !empty($message['attachment_duration']) ? gmdate('i:s', $message['attachment_duration']) : '0:00' }}</div>
                                                            </div>
                                                        </div>
                                                    @elseif($message['attachment_type'] === 'video')
                                                        {{-- Video bubble: compact thumbnail tile that opens
                                                             a lightbox popup when clicked (openVideoLightbox).
                                                             We render just the poster + a play-button overlay
                                                             here — no inline <video controls> — so the thread
                                                             stays clean and playback happens in a dedicated
                                                             fullscreen-ish modal. data-* attributes feed the
                                                             lightbox header (sender/time) and the reply bar
                                                             (which message to attach the reply to). --}}
                                                        <div class="msg-video" role="button" tabindex="0"
                                                             data-video-url="{{ $message['attachment_url'] }}"
                                                             data-message-id="{{ $message['id'] }}"
                                                             data-sender-name="{{ $message['is_mine'] ? 'You' : ($message['sender_name'] ?? 'User') }}"
                                                             data-sent-at="{{ \Carbon\Carbon::parse($message['created_at'])->format('M j, g:i A') }}"
                                                             aria-label="Play video">
                                                            @if(!empty($message['attachment_thumbnail_url']))
                                                                <img class="msg-video-poster" src="{{ $message['attachment_thumbnail_url'] }}" alt="" loading="lazy">
                                                            @else
                                                                <div class="msg-video-poster msg-video-poster-fallback"></div>
                                                            @endif
                                                            <span class="msg-video-play" aria-hidden="true">
                                                                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                                            </span>
                                                        </div>
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
                                                @if(!empty($message['message']) && ($message['attachment_type'] ?? '') !== 'album')
                                                    {{-- Album captions are rendered inline inside .msg-album-caption
                                                         above, so skip the trailing text pill for album rows to
                                                         avoid showing the caption twice. --}}
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
                                                @if(!empty($message['is_starred']))
                                                    <span class="message-star" title="Starred">
                                                        <i class="fa fa-star"></i>
                                                    </span>
                                                @endif
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

                                {{-- Upload progress banner (shown by JS while a non-image/non-video
                                     file is uploading via /chat/attachment-upload). Images and videos
                                     use their own optimistic bubble inside the thread instead. --}}
                                <div class="attachment-preview" id="attachmentPreview">
                                    <i class="fa fa-spinner fa-spin" style="font-size:20px; color:#C1F11D;"></i>
                                    <div class="att-info" id="attachmentPreviewLabel">Uploading…</div>
                                </div>

                                {{-- WhatsApp-style image preview modal — shown BEFORE upload so the
                                     user can see the full image, optionally add a caption, and
                                     confirm. Markup is static; JS populates src + wires buttons. --}}
                                <div class="img-preview-modal" id="imgPreviewModal" aria-hidden="true">
                                    <div class="img-preview-topbar">
                                        <button type="button" class="ipm-icon-btn" id="ipmCloseBtn" title="Close" aria-label="Close">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                        <span class="ipm-count">1/1</span>
                                        <button type="button" class="ipm-icon-btn" id="ipmDeleteBtn" title="Discard" aria-label="Discard">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </div>
                                    <div class="img-preview-body">
                                        <img id="ipmImage" alt="">
                                    </div>
                                    {{-- Thumbnail strip — shown only when 2+ images are queued.
                                         Each tile switches the preview; trailing "+" re-opens
                                         the picker. --}}
                                    <div class="ipm-thumbs" id="ipmThumbs" aria-label="Selected images"></div>
                                    <div class="img-preview-footer">
                                        <input type="text" id="ipmCaption" class="ipm-caption" placeholder="Add a caption..." autocomplete="off">
                                        <button type="button" class="ipm-send-btn" id="ipmSendBtn" title="Send" aria-label="Send">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Chat Input --}}
                                <div class="chat-input" style="position: relative;">
                                    @php
                                        $blockMe   = $curConv['is_blocked_by_me']   ?? false;
                                        $blockThem = $curConv['is_blocked_by_them'] ?? false;
                                    @endphp
                                    @if($blockMe || $blockThem)
                                        <div class="block-strip">
                                            <i class="fa fa-ban"></i>
                                            @if($blockMe)
                                                You blocked this user. <button type="button" class="block-strip-link" wire:click="toggleBlock({{ $selectedUser->id }})">Unblock</button>
                                            @else
                                                You can't message this user.
                                            @endif
                                        </div>
                                    @endif
                                    @if($replyingToPreview)
                                        {{-- Replying-to strip: name + excerpt, X to cancel.
                                             The reply_to_id is persisted on submit via sendReply(). --}}
                                        <div class="reply-strip">
                                            <div class="reply-strip-body">
                                                <div class="reply-strip-name">Replying to {{ $replyingToPreview['sender_name'] }}</div>
                                                <div class="reply-strip-text">{{ $replyingToPreview['excerpt'] }}</div>
                                            </div>
                                            <button type="button" class="reply-strip-close" wire:click="cancelReply" aria-label="Cancel reply">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                    <div class="emoji-picker-wrapper" id="emojiPickerWrapper">
                                        <emoji-picker id="emojiPicker"></emoji-picker>
                                    </div>
                                    <form wire:submit.prevent="sendReply" class="chat-input-form {{ ($blockMe || $blockThem) ? 'is-blocked' : '' }}">
                                        <button type="button" class="emoji-btn" id="emojiToggleBtn" title="Emoji" aria-label="Emoji picker">
                                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M11 21C16.5228 21 21 16.5228 21 11C21 5.47715 16.5228 1 11 1C5.47715 1 1 5.47715 1 11C1 16.5228 5.47715 21 11 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M7 8.05001V7.95001M15 8.05001V7.95001" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M15 13C14.5 14.5 13.21 16 11 16C8.79 16 7.5 14.5 7 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        {{-- Paperclip opens the WhatsApp-style attachment menu
                                             (Photos & Videos / Camera / Audio / Contact / Location /
                                             Documents). Each menu button triggers its own hidden
                                             <input type="file"> with the right accept filter, or in
                                             the case of Camera/Location/Contact uses browser APIs.
                                             All uploads POST to /chat/attachment-upload the same way
                                             the single picker used to. --}}
                                        <button type="button" class="attach-btn" id="attachBtn" title="Attach" aria-label="Attach" aria-haspopup="true">
                                            <svg width="21" height="23" viewBox="0 0 21 23" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M18.0445 2.53812C17.4769 1.97892 16.7071 1.66476 15.9044 1.66476C15.1017 1.66476 14.3318 1.97892 13.7642 2.53812L3.06296 13.0803C2.13413 13.9954 1.61231 15.2366 1.61231 16.5307C1.61231 17.8249 2.13413 19.0661 3.06296 19.9812C3.99179 20.8963 5.25155 21.4104 6.56511 21.4104C7.87868 21.4104 9.13844 20.8963 10.0673 19.9812L19.6008 10.5884C19.7564 10.4402 19.9649 10.3581 20.1813 10.3599C20.3978 10.3616 20.6048 10.4471 20.7579 10.5978C20.9111 10.7485 20.998 10.9525 21 11.1657C21.0019 11.3789 20.9188 11.5844 20.7685 11.7379L11.235 21.1317C9.99319 22.3354 8.31888 23.0069 6.5768 22.9999C4.83472 22.993 3.16602 22.308 1.93418 21.0944C0.702331 19.8807 0.00714839 18.2367 5.48207e-05 16.5203C-0.00703875 14.804 0.674532 13.1544 1.8963 11.9309L12.5976 1.38761C13.1757 0.804707 13.9004 0.383285 14.6978 0.166313C15.4952 -0.0506584 16.3367 -0.0554174 17.1365 0.152521C17.9363 0.36044 18.6658 0.773574 19.2506 1.34981C19.8355 1.92604 20.2548 2.64477 20.4659 3.43272C20.6769 4.22075 20.6721 5.04983 20.4519 5.83543C20.2317 6.62102 19.8039 7.33502 19.2123 7.90463L9.09433 17.8732C8.55282 18.41 7.81757 18.7137 7.04938 18.7179C6.28248 18.7138 5.54859 18.41 5.00884 17.8732C4.79312 17.6607 4.63683 17.3635 4.53558 17.0859C4.41441 16.7571 4.35186 16.41 4.35068 16.0601C4.35068 15.3011 4.66325 14.5713 5.20365 14.0389L14.1539 5.22192C14.2547 5.11487 14.3831 5.03661 14.5253 4.99539C14.6676 4.95416 14.8185 4.95151 14.9622 4.9877C15.1058 5.02389 15.2369 5.09758 15.3416 5.20101C15.4463 5.30445 15.5208 5.43379 15.5571 5.57542C15.5938 5.7168 15.5912 5.8653 15.5495 6.00534C15.5079 6.14537 15.4288 6.2718 15.3205 6.37135L6.3714 15.1894C6.1371 15.4207 6.0043 15.7334 6.0016 16.0601C6.0049 16.2813 6.05993 16.4993 6.16229 16.6967L6.1843 16.7314C6.42643 16.9645 6.73571 17.0914 7.04938 17.0914C7.36856 17.0914 7.68774 16.9602 7.92767 16.7227L18.0445 6.75521C18.3257 6.47829 18.5488 6.14949 18.701 5.78761C18.8532 5.42572 18.9315 5.03784 18.9315 4.64612C18.9315 4.2544 18.8532 3.86652 18.701 3.50464C18.5488 3.14275 18.3257 2.81396 18.0445 2.53704V2.53812Z" fill="currentColor"/>
                                            </svg>
                                        </button>
                                        <div class="attach-menu" id="attachMenu">
                                            <button type="button" data-attach-kind="photos">
                                                <span class="icon photos" aria-hidden="true">
                                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 10.5V1.5C15 0.675 14.325 0 13.5 0H4.5C3.675 0 3 0.675 3 1.5V10.5C3 11.325 3.675 12 4.5 12H13.5C14.325 12 15 11.325 15 10.5ZM6.75 7.5L8.2725 9.5325L10.5 6.75L13.5 10.5H4.5L6.75 7.5ZM0 3V13.5C0 14.325 0.675 15 1.5 15H12V13.5H1.5V3H0Z" fill="#1ABCFE"/></svg>
                                                </span> Photos &amp; Videos
                                            </button>
                                            <button type="button" data-attach-kind="camera">
                                                <span class="icon camera" aria-hidden="true">
                                                    <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.13742 13H13.8629C15.2784 13 16 12.2909 16 10.8867V3.7494C16 2.34515 15.2784 1.64303 13.8629 1.64303H12.2705C11.7397 1.64303 11.5765 1.53396 11.2702 1.19308L10.7187 0.579381C10.3854 0.204761 10.0384 0 9.33048 0H6.6219C5.92095 0 5.57366 0.204761 5.23364 0.579381L4.68223 1.19308C4.38285 1.52698 4.2127 1.64303 3.6819 1.64303H2.13713C0.721571 1.64303 0 2.34515 0 3.7494V10.8867C0 12.2909 0.721861 13 2.13742 13ZM7.99681 10.8186C5.94127 10.8186 4.28761 9.16189 4.28761 7.08985C4.28761 5.01723 5.94127 3.36111 7.99652 3.36111C10.0654 3.36111 11.7124 5.01723 11.7124 7.08985C11.7124 9.16189 10.0587 10.8186 7.99652 10.8186M12.8896 5.42617C12.4267 5.42617 12.0457 5.05155 12.0457 4.58793C12.0457 4.36376 12.1346 4.14878 12.2929 3.99027C12.4511 3.83176 12.6658 3.74271 12.8896 3.74271C13.1133 3.74271 13.328 3.83176 13.4862 3.99027C13.6445 4.14878 13.7334 4.36376 13.7334 4.58793C13.7334 5.05155 13.3524 5.42617 12.8896 5.42617ZM7.99681 9.78258C8.34995 9.783 8.69971 9.71363 9.02605 9.57846C9.3524 9.44329 9.64891 9.24496 9.89862 8.99483C10.1483 8.74471 10.3463 8.4477 10.4813 8.12081C10.6162 7.79392 10.6855 7.44358 10.6851 7.08985C10.6854 6.73614 10.6162 6.38583 10.4812 6.05898C10.3462 5.73213 10.1482 5.43515 9.89852 5.18506C9.64881 4.93496 9.35231 4.73666 9.02599 4.6015C8.69966 4.46635 8.34993 4.397 7.99681 4.39742C6.51301 4.39742 5.31552 5.59719 5.31552 7.08985C5.31552 8.5828 6.51998 9.78258 7.99681 9.78258Z" fill="#FF2E74"/></svg>
                                                </span> Camera
                                            </button>
                                            <button type="button" data-attach-kind="audio">
                                                <span class="icon audio" aria-hidden="true">
                                                    <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 0C3.592 0 0 3.69765 0 8.23529V13.1765C0 13.6294 0.36 14 0.8 14H3.2C3.64 14 4 13.6294 4 13.1765V9.05882C4 8.60588 3.64 8.23529 3.2 8.23529H1.6C1.6 4.60353 4.472 1.64706 8 1.64706C11.528 1.64706 14.4 4.60353 14.4 8.23529H12.8C12.36 8.23529 12 8.60588 12 9.05882V13.1765C12 13.6294 12.36 14 12.8 14H15.2C15.64 14 16 13.6294 16 13.1765V8.23529C16 3.69765 12.408 0 8 0Z" fill="#FA6533"/></svg>
                                                </span> Audio
                                            </button>
                                            <button type="button" data-attach-kind="contact">
                                                <span class="icon contact" aria-hidden="true">
                                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.75 0C6.75544 0 5.80161 0.395088 5.09835 1.09835C4.39509 1.80161 4 2.75544 4 3.75C4 4.74456 4.39509 5.69839 5.09835 6.40165C5.80161 7.10491 6.75544 7.5 7.75 7.5C8.74456 7.5 9.69839 7.10491 10.4017 6.40165C11.1049 5.69839 11.5 4.74456 11.5 3.75C11.5 2.75544 11.1049 1.80161 10.4017 1.09835C9.69839 0.395088 8.74456 0 7.75 0ZM3.75 9.5C2.75544 9.5 1.80161 9.89509 1.09835 10.5983C0.395088 11.3016 0 12.2554 0 13.25V14.438C0 15.192 0.546 15.834 1.29 15.955C5.568 16.654 9.932 16.654 14.21 15.955C14.5698 15.8958 14.897 15.7108 15.1332 15.4331C15.3694 15.1553 15.4994 14.8027 15.5 14.438V13.25C15.5 12.2554 15.1049 11.3016 14.4017 10.5983C13.6984 9.89509 12.7446 9.5 11.75 9.5H11.41C11.2233 9.50067 11.042 9.52933 10.866 9.586L10 9.869C8.53797 10.3463 6.96203 10.3463 5.5 9.869L4.634 9.586C4.45847 9.5297 4.27534 9.50069 4.091 9.5H3.75Z" fill="#009DE2"/></svg>
                                                </span> Contact
                                            </button>
                                            <button type="button" data-attach-kind="location">
                                                <span class="icon location" aria-hidden="true">
                                                    <svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 0C2.68762 0 0 2.44792 0 5.46429C0 10.3214 6 17 6 17C6 17 12 10.3214 12 5.46429C12 2.44792 9.31238 0 6 0ZM6 8.5C5.52533 8.5 5.06131 8.35757 4.66663 8.09071C4.27195 7.82386 3.96434 7.44457 3.78269 7.0008C3.60104 6.55704 3.55351 6.06873 3.64612 5.59764C3.73872 5.12654 3.9673 4.69381 4.30294 4.35417C4.63859 4.01453 5.06623 3.78323 5.53178 3.68952C5.99734 3.59581 6.4799 3.64391 6.91844 3.82772C7.35698 4.01153 7.73181 4.32281 7.99553 4.72219C8.25924 5.12156 8.4 5.5911 8.4 6.07143C8.39931 6.71531 8.14623 7.33262 7.69629 7.78791C7.24635 8.24321 6.63631 8.4993 6 8.5Z" fill="#06CF9C"/></svg>
                                                </span> Location
                                            </button>
                                            <button type="button" data-attach-kind="docs">
                                                <span class="icon docs" aria-hidden="true">
                                                    <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.46154 0.211538C8.46154 0.155435 8.43925 0.101629 8.39958 0.0619582C8.35991 0.0222871 8.3061 0 8.25 0H2.32692C1.70978 0 1.11792 0.245157 0.68154 0.68154C0.245157 1.11792 0 1.70978 0 2.32692V14.1731C0 14.7902 0.245157 15.3821 0.68154 15.8185C1.11792 16.2548 1.70978 16.5 2.32692 16.5H10.7885C11.4056 16.5 11.9975 16.2548 12.4338 15.8185C12.8702 15.3821 13.1154 14.7902 13.1154 14.1731V5.83592C13.1154 5.77982 13.0931 5.72601 13.0534 5.68634C13.0138 5.64667 12.9599 5.62438 12.9038 5.62438H9.09615C8.92784 5.62438 8.76643 5.55752 8.64741 5.43851C8.5284 5.3195 8.46154 5.15808 8.46154 4.98977V0.211538ZM9.09615 8.46154C9.26446 8.46154 9.42588 8.5284 9.54489 8.64741C9.66391 8.76643 9.73077 8.92784 9.73077 9.09615C9.73077 9.26446 9.66391 9.42588 9.54489 9.54489C9.42588 9.66391 9.26446 9.73077 9.09615 9.73077H4.01923C3.85092 9.73077 3.6895 9.66391 3.57049 9.54489C3.45148 9.42588 3.38462 9.26446 3.38462 9.09615C3.38462 8.92784 3.45148 8.76643 3.57049 8.64741C3.6895 8.5284 3.85092 8.46154 4.01923 8.46154H9.09615ZM9.09615 11.8462C9.26446 11.8462 9.42588 11.913 9.54489 12.032C9.66391 12.151 9.73077 12.3125 9.73077 12.4808C9.73077 12.6491 9.66391 12.8105 9.54489 12.9295C9.42588 13.0485 9.26446 13.1154 9.09615 13.1154H4.01923C3.85092 13.1154 3.6895 13.0485 3.57049 12.9295C3.45148 12.8105 3.38462 12.6491 3.38462 12.4808C3.38462 12.3125 3.45148 12.151 3.57049 12.032C3.6895 11.913 3.85092 11.8462 4.01923 11.8462H9.09615Z" fill="#7F66FF"/><path d="M9.73047 0.486893C9.73047 0.3312 9.89378 0.232201 10.0148 0.329508C10.1174 0.412431 10.2085 0.508893 10.2881 0.618893L12.8375 4.1702C12.8951 4.25143 12.8325 4.35635 12.7326 4.35635H9.94201C9.8859 4.35635 9.8321 4.33407 9.79243 4.2944C9.75276 4.25472 9.73047 4.20092 9.73047 4.14482V3.65782e-07Z" fill="#7F66FF"/></svg>
                                                </span> Documents
                                            </button>
                                        </div>
                                        {{-- Hidden file inputs — one per kind so each has the right accept filter.
                                             Camera also sets `capture="environment"` which on mobile launches the
                                             camera app directly instead of the gallery. --}}
                                        <input type="file" id="attachInputPhotos"  accept="image/*,video/*" multiple style="display:none">
                                        <input type="file" id="attachInputCamera"  accept="image/*" capture="environment" style="display:none">
                                        <input type="file" id="attachInputAudio"   accept="audio/*" style="display:none">
                                        <input type="file" id="attachInputDocs"    accept="application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar" style="display:none">
                                        {{-- Legacy id kept so any stray references still resolve. --}}
                                        <input type="file" id="attachInput"        accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt,video/*" style="display:none">

                                        {{-- Recording indicator (replaces input while recording) --}}
                                        <div class="voice-recording-bar" id="voiceRecordingBar">
                                            <button type="button" class="rec-btn delete" id="voiceDeleteBtn" title="Discard recording" aria-label="Discard">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                            </button>
                                            <span class="rec-dot"></span>
                                            <span class="rec-time" id="voiceRecTime">0:00</span>
                                            <span class="rec-spacer"></span>
                                            <button type="button" class="rec-btn" id="voicePauseBtn" title="Pause / Resume" aria-label="Pause recording">
                                                <svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                                            </button>
                                            <button type="button" class="rec-btn" id="voiceRestartBtn" title="Restart" aria-label="Restart recording">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                            </button>
                                            <button type="button" class="rec-btn view-once" id="voiceOnceBtn" data-vo="0" title="Play once" aria-label="Play once">1</button>
                                            <button type="button" class="rec-btn send" id="voiceSendBtn" title="Send" aria-label="Send">
                                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                            </button>
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

                                        <button type="submit" title="Send" aria-label="Send">
                                            <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M14.5543 6.79394C14.5544 6.41177 14.3387 6.07601 13.9948 5.9177L1.36966 0.090244C1.02025 -0.0735256 0.608044 -0.0107162 0.321397 0.248634C0.0320204 0.505254 -0.0717363 0.909275 0.0510848 1.27233L1.8934 6.79467L0.0504505 12.3172C-0.0642226 12.6666 0.0258401 13.0461 0.285159 13.3054L0.323375 13.3436C0.609991 13.6029 1.02219 13.6602 1.37162 13.5019L13.9947 7.67025C14.3414 7.50917 14.5543 7.17612 14.5543 6.79394ZM1.28769 1.32959L11.8737 6.21537L2.91712 6.21316L1.29042 1.32686L1.28769 1.32959ZM1.28707 12.2598L2.91432 7.37333L11.8736 7.37282L1.28707 12.2598Z" fill="currentColor"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- Contact Info overlay — covers the chat area when the
                                     chat-header kebab → "View profile" is clicked. Pure
                                     client-side (Alpine), no round-trip. --}}
                                @if($selectedUser && !$selectedIsSupport)
                                    <div class="contact-info-panel" x-show="contactInfoOpen" x-cloak style="display:none">
                                        <div class="ci-header">
                                            <button type="button" class="ci-back" @click="contactInfoOpen = false" aria-label="Back">
                                                <i class="fa fa-angle-left"></i>
                                            </button>
                                            <h3>Contact info</h3>
                                        </div>
                                        <div class="ci-body">
                                            <div class="ci-identity">
                                                <div class="ci-avatar">
                                                    @if($selectedUser->avatar)
                                                        <img src="{{ user_avatar_url($selectedUser) }}" alt="">
                                                    @else
                                                        {{ strtoupper(substr($selectedUser->name ?? $selectedUser->email ?? '?', 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div class="ci-name">{{ $selectedUser->name ?? $selectedUser->email }}</div>
                                                @if(!empty($selectedUser->username))
                                                    <div class="ci-handle">&#64;{{ $selectedUser->username }}</div>
                                                @endif
                                                <div class="ci-last-seen">
                                                    @if($selectedUser->last_seen_at ?? null)
                                                        Last seen {{ \Carbon\Carbon::parse($selectedUser->last_seen_at)->diffForHumans() }}
                                                    @else
                                                        Online
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ci-actions">
                                                <button type="button" class="ci-action"
                                                        data-rtc-call="audio"
                                                        data-peer-id="{{ $selectedUser->id }}"
                                                        data-peer-name="{{ $selectedUser->name ?? $selectedUser->email }}"
                                                        data-peer-avatar="{{ $selectedUser->avatar ? user_avatar_url($selectedUser) : '' }}"
                                                        data-conversation-id="{{ $selectedConversationId }}">
                                                    <span class="ci-action-icon"><i class="fa fa-phone"></i></span>
                                                    <span class="ci-action-label">Voice</span>
                                                </button>
                                                <button type="button" class="ci-action"
                                                        data-rtc-call="video"
                                                        data-peer-id="{{ $selectedUser->id }}"
                                                        data-peer-name="{{ $selectedUser->name ?? $selectedUser->email }}"
                                                        data-peer-avatar="{{ $selectedUser->avatar ? user_avatar_url($selectedUser) : '' }}"
                                                        data-conversation-id="{{ $selectedConversationId }}">
                                                    <span class="ci-action-icon"><i class="fa fa-video"></i></span>
                                                    <span class="ci-action-label">Video</span>
                                                </button>
                                                <button type="button" class="ci-action"
                                                        @click="contactInfoOpen = false; openChatSearchBar()">
                                                    <span class="ci-action-icon"><i class="fa fa-search"></i></span>
                                                    <span class="ci-action-label">Search</span>
                                                </button>
                                                <a href="/profile/{{ $selectedUser->id }}" target="_blank" class="ci-action">
                                                    <span class="ci-action-icon"><i class="fa fa-store"></i></span>
                                                    <span class="ci-action-label">Seller</span>
                                                </a>
                                            </div>
                                            @php
                                                $curConv2 = collect($conversations)->firstWhere('id', $selectedConversationId);
                                                $isArchived2 = $curConv2['is_archived'] ?? false;
                                                $disappearingLabel2 = ['never'=>'Never','24h'=>'24 hours','1w'=>'1 week','1m'=>'1 month'][$prefDisappearingDefault ?? 'never'] ?? 'Never';
                                                // Flatten albums back to their individual image rows so the
                                                // Media panel shows every picture, not one "album" placeholder.
                                                // Non-album rows pass through unchanged.
                                                $convMedia = collect($this->conversationMessages ?? [])
                                                    ->flatMap(function ($m) {
                                                        if (($m['attachment_type'] ?? '') === 'album' && !empty($m['album_items'])) {
                                                            return collect($m['album_items'])->map(fn($it) => [
                                                                'attachment_url'           => $it['url'] ?? null,
                                                                'attachment_thumbnail_url' => $it['thumbnail_url'] ?? null,
                                                                'attachment_type'          => 'image',
                                                                'attachment_duration'      => null,
                                                            ])->all();
                                                        }
                                                        return [$m];
                                                    })
                                                    ->filter(fn($m) => !empty($m['attachment_url']) && in_array($m['attachment_type'] ?? '', ['image','video','audio']))
                                                    ->values();
                                            @endphp
                                            <div class="ci-list">
                                                <button type="button" class="ci-row" @click="contactInfoOpen = false; mediaViewOpen = true">
                                                    <span class="ci-row-icon"><i class="fa fa-image"></i></span>
                                                    <span class="ci-row-label">Media, links and docs</span>
                                                    <span class="ci-row-value">{{ $convMedia->count() }}</span>
                                                    <i class="fa fa-angle-right ci-row-chev"></i>
                                                </button>
                                                <button type="button" class="ci-row" @click="contactInfoOpen = false; starredViewOpen = true">
                                                    <span class="ci-row-icon"><i class="fa fa-star"></i></span>
                                                    <span class="ci-row-label">Starred messages</span>
                                                </button>
                                                <button type="button" class="ci-row" onclick="alert('Disappearing messages — change in Settings')">
                                                    <span class="ci-row-icon"><i class="fa fa-clock"></i></span>
                                                    <span class="ci-row-label">
                                                        Disappearing messages
                                                        <span class="ci-row-sub">{{ $disappearingLabel2 }}</span>
                                                    </span>
                                                </button>
                                                <button type="button" class="ci-row" wire:click="toggleArchive({{ $selectedConversationId }})">
                                                    <span class="ci-row-icon"><i class="fa {{ $isArchived2 ? 'fa-box-open' : 'fa-archive' }}"></i></span>
                                                    <span class="ci-row-label">{{ $isArchived2 ? 'Unarchive chat' : 'Archive chat' }}</span>
                                                </button>
                                                @php $isBlocked2 = $curConv2['is_blocked_by_me'] ?? false; @endphp
                                                <button type="button" class="ci-row danger"
                                                        wire:click="toggleBlock({{ $selectedUser->id }})"
                                                        onclick="return confirm('{{ $isBlocked2 ? 'Unblock' : 'Block' }} this user?')">
                                                    <span class="ci-row-icon"><i class="fa fa-ban"></i></span>
                                                    <span class="ci-row-label">{{ $isBlocked2 ? 'Unblock' : 'Block' }}</span>
                                                </button>
                                                <button type="button" class="ci-row danger" wire:click="deleteConversation" onclick="return confirm('Delete this conversation?')">
                                                    <span class="ci-row-icon"><i class="fa fa-trash"></i></span>
                                                    <span class="ci-row-label">Delete chat</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Per-conversation Media panel. Grid of only this
                                         thread's image/video/audio attachments. Click image
                                         opens the existing lightbox. --}}
                                    <div class="contact-info-panel" x-show="mediaViewOpen" x-cloak>
                                        <div class="ci-header">
                                            <button type="button" class="ci-back" @click="mediaViewOpen = false" aria-label="Back">
                                                <i class="fa fa-angle-left"></i>
                                            </button>
                                            <h3>Media</h3>
                                        </div>
                                        <div class="ci-body">
                                            @if($convMedia->isEmpty())
                                                <div class="media-empty">
                                                    <i class="fa fa-image"></i>
                                                    <p>No media shared in this chat yet.</p>
                                                </div>
                                            @else
                                                <div class="media-grid">
                                                    @foreach($convMedia as $m)
                                                        @if($m['attachment_type'] === 'image')
                                                            <button type="button" class="media-tile" onclick="openLightbox('{{ $m['attachment_url'] }}')">
                                                                <img src="{{ $m['attachment_url'] }}" alt="" loading="lazy">
                                                            </button>
                                                        @elseif($m['attachment_type'] === 'video')
                                                            <button type="button" class="media-tile" onclick="window.open('{{ $m['attachment_url'] }}', '_blank')">
                                                                @if(!empty($m['attachment_thumbnail_url']))
                                                                    {{-- Prefer the pre-captured poster: avoids
                                                                         fetching the whole R2 video just to show
                                                                         the Media tab tile, and dodges the
                                                                         CORS/range quirks of inline <video>
                                                                         thumbnailing from a redirected URL. --}}
                                                                    <img src="{{ $m['attachment_thumbnail_url'] }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                                                                @else
                                                                    <video src="{{ $m['attachment_url'] }}" preload="metadata" muted></video>
                                                                @endif
                                                                <span class="media-tile-badge"><i class="fa fa-play"></i></span>
                                                            </button>
                                                        @else
                                                            <a href="{{ $m['attachment_url'] }}" target="_blank" class="media-tile audio">
                                                                <i class="fa fa-microphone"></i>
                                                                <span class="media-tile-dur">{{ !empty($m['attachment_duration']) ? gmdate('i:s', $m['attachment_duration']) : '' }}</span>
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Starred messages overlay — lists every message THIS
                                         user has starred across all conversations. Shows a
                                         compact preview per row; click to jump to that
                                         conversation. --}}
                                    <div class="contact-info-panel" x-show="starredViewOpen" x-cloak style="display:none">
                                        <div class="ci-header">
                                            <button type="button" class="ci-back" @click="starredViewOpen = false" aria-label="Back">
                                                <i class="fa fa-angle-left"></i>
                                            </button>
                                            <h3>Starred messages</h3>
                                        </div>
                                        <div class="ci-body">
                                            @php $starred = $this->getStarredMessages(); @endphp
                                            @if($starred->isEmpty())
                                                <div class="media-empty">
                                                    <i class="fa fa-star"></i>
                                                    <p>No starred messages yet.<br><small>Tap &amp; hold any message to star it.</small></p>
                                                </div>
                                            @else
                                                <div class="starred-list">
                                                    @foreach($starred as $s)
                                                        <button type="button" class="starred-item"
                                                                wire:click="selectConversation({{ $s['conversation_id'] }})"
                                                                @click="starredViewOpen = false">
                                                            <div class="starred-meta">
                                                                <span class="starred-sender">{{ $s['is_mine'] ? 'You' : $s['sender_name'] }}</span>
                                                                <span class="starred-arrow">&rsaquo;</span>
                                                                <span class="starred-peer">{{ $s['peer_name'] }}</span>
                                                                <span class="starred-date">{{ \Carbon\Carbon::parse($s['created_at'])->diffForHumans() }}</span>
                                                            </div>
                                                            <div class="starred-body">
                                                                @if($s['attachment_type'] === 'image')
                                                                    <i class="fa fa-image"></i> Photo
                                                                @elseif($s['attachment_type'] === 'audio')
                                                                    <i class="fa fa-microphone"></i> Voice message
                                                                @elseif($s['attachment_type'] === 'video')
                                                                    <i class="fa fa-video"></i> Video
                                                                @elseif(!empty($s['message']))
                                                                    {{ Str::limit($s['message'], 120) }}
                                                                @else
                                                                    <i class="fa fa-paperclip"></i> Attachment
                                                                @endif
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
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

            @if($forwardPickerOpen)
                {{-- Forward picker modal: lists the user's other conversations.
                     Multi-select up to 5 → Send button at the bottom fires
                     one $wire.forwardToMany([...ids]) call that fans the
                     message out server-side. Alpine owns the selection
                     state so toggling rows feels instant with no round-trip. --}}
                <div class="fwd-overlay" wire:click="cancelForward">
                    <div class="fwd-modal" wire:click.stop
                         x-data="{ selected: [], MAX: 5,
                                   toggle(id) {
                                       const i = this.selected.indexOf(id);
                                       if (i >= 0) { this.selected.splice(i, 1); return; }
                                       if (this.selected.length >= this.MAX) {
                                           alert(`You can forward to a maximum of ${this.MAX} chats at once.`);
                                           return;
                                       }
                                       this.selected.push(id);
                                   } }">
                        <div class="fwd-header">
                            <h3>Forward to…</h3>
                            <button type="button" class="fwd-close" wire:click="cancelForward" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="fwd-list">
                            @foreach($conversations as $conv)
                                @if(empty($conv['is_support']) && (int)$conv['id'] !== (int)$selectedConversationId)
                                    <button type="button" class="fwd-item"
                                            :class="selected.includes({{ (int) $conv['id'] }}) ? 'selected' : ''"
                                            @click="toggle({{ (int) $conv['id'] }})">
                                        <div class="fwd-avatar">
                                            @if(!empty($conv['other_user_avatar_url']))
                                                <img src="{{ $conv['other_user_avatar_url'] }}" alt="" onerror="this.style.display='none'">
                                            @else
                                                {{ strtoupper(substr($conv['other_user_name'] ?? '?', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="fwd-name">{{ $conv['other_user_name'] ?? 'Unknown' }}</div>
                                        <span class="fwd-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </span>
                                    </button>
                                @endif
                            @endforeach
                            @php
                                $forwardable = collect($conversations)->filter(fn($c) => empty($c['is_support']) && (int)$c['id'] !== (int)$selectedConversationId);
                            @endphp
                            @if($forwardable->isEmpty())
                                <div class="fwd-empty">No other conversations to forward to.</div>
                            @endif
                        </div>
                        @if($forwardable->isNotEmpty())
                            <div class="fwd-footer">
                                <span class="fwd-footer-count"
                                      x-text="selected.length ? `${selected.length}/${MAX} selected` : `Select up to ${MAX} chats`"></span>
                                <button type="button" class="fwd-footer-send"
                                        :disabled="selected.length === 0"
                                        @click="$wire.forwardToMany(selected)">
                                    <i class="fa fa-paper-plane"></i>
                                    <span x-text="selected.length > 1 ? `Send (${selected.length})` : 'Send'"></span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Contact-share modal — opened by the Contact option in the
                 attachment menu. Pure client-side; on save it drops a formatted
                 contact message into the composer and submits it as plain text.
                 Hidden by default; JS toggles the .open class. --}}
            <div class="fwd-overlay contact-overlay" id="contactOverlay" style="display:none;">
                <div class="fwd-modal" onclick="event.stopPropagation()">
                    <div class="fwd-header">
                        <h3>Share contact</h3>
                        <button type="button" class="fwd-close" onclick="closeContactShare()" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div style="padding:16px 18px 18px; display:flex; flex-direction:column; gap:12px;">
                        <div>
                            <label class="acct-label">Name</label>
                            <input type="text" id="contactShareName" class="acct-input" placeholder="e.g. Ali Khan">
                        </div>
                        <div>
                            <label class="acct-label">Phone</label>
                            <input type="tel" id="contactSharePhone" class="acct-input" placeholder="+92 300 1234567">
                        </div>
                        <button type="button" class="acct-save-btn" onclick="submitContactShare()">
                            <i class="fa fa-paper-plane"></i> Send
                        </button>
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
        // Defensive: when Livewire's wire:navigate soft-swaps us back to
        // /my-chat and Alpine/Livewire fail to re-wire the entangled `tab`
        // state, the sidebar renders as an empty shell (title + search +
        // list all hidden by x-show). Detect that condition and hard-reload
        // once so the page boots cleanly. sessionStorage flag prevents
        // reload loops in the degenerate case where even the fresh load
        // produces an empty sidebar.
        (function () {
            const key = '__chatSidebarReloaded';
            function check() {
                const sidebar = document.getElementById('chatSidebar');
                if (!sidebar) return;
                // Sidebar is "broken" if the search bar isn't actually
                // visible on-screen (Alpine's `tab` state wasn't rehydrated
                // after wire:navigate, so all x-show branches are hidden).
                // Use offsetParent to detect display:none ancestors.
                const search = sidebar.querySelector('.chat-search');
                const title  = sidebar.querySelector('.chat-sidebar-title');
                const searchVisible = search && search.offsetParent !== null;
                const titleVisible  = title  && title.offsetParent  !== null;
                if (searchVisible || titleVisible) {
                    sessionStorage.removeItem(key);
                    return;
                }
                if (sessionStorage.getItem(key)) return; // already tried
                sessionStorage.setItem(key, '1');
                console.warn('[chat] broken sidebar detected after navigation — hard-reloading');
                window.location.replace(window.location.href);
            }
            // Give Alpine + Livewire ~600ms to finish booting before checking.
            setTimeout(check, 600);
            document.addEventListener('livewire:navigated', function () {
                setTimeout(check, 600);
            });
        })();

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

        /* Long-press on a chat message bubble opens the per-message
           Reply/Star/Forward/Delete menu — mirrors the WhatsApp UX we
           already use on the conversation list items. The menu DOM
           already exists inside each .message-wrapper (Alpine-scoped
           `{ menuOpen }`); we just flip menuOpen=true from here so no
           duplicate markup / wire plumbing is needed.

           Delegated at document level + module guard so neither a
           wire:navigate DOM swap nor a Livewire morph on new messages
           can drop the binding. Mouse-down (desktop) + touch-start
           (mobile/tablet) both participate; a 450ms timer fires the
           open. Movement > ~10px or an early pointer-up cancels, so
           scrolling the chat list doesn't accidentally open menus. */
        (function () {
            if (window.__chatBubbleLongPressWired) return;
            window.__chatBubbleLongPressWired = true;

            // Hold tuning. 350ms matches WhatsApp's own tap-and-hold feel —
            // was 450ms which felt sluggish on real phones. The move cancel
            // was bumped from 10px → 24px because fingers naturally wiggle
            // during a hold; the old threshold was swallowing legitimate
            // long-presses as "the user started scrolling".
            var HOLD_MS = 350;
            var MOVE_CANCEL_PX = 24;
            var pressTimer = null;
            var startX = 0, startY = 0;
            var pressWrapper = null;
            var suppressNextClick = false;

            function cancelPress() {
                if (pressTimer) { clearTimeout(pressTimer); pressTimer = null; }
                pressWrapper = null;
            }

            function openMenuFor(wrapper) {
                if (!wrapper) return;
                // Short haptic buzz so the user feels the hold registered —
                // matches native iOS / Android long-press feedback. Silent
                // no-op on desktops and browsers without the API.
                try { if (navigator.vibrate) navigator.vibrate(15); } catch (e) {}
                // Alpine stores component state via Alpine.$data(el).
                // Fall back to toggling the menu list's visibility if
                // Alpine isn't available for any reason.
                try {
                    if (window.Alpine && typeof window.Alpine.$data === 'function') {
                        var data = window.Alpine.$data(wrapper);
                        if (data) { data.menuOpen = true; return; }
                    }
                } catch (e) {}
                var list = wrapper.querySelector('.msg-menu-list');
                if (list) {
                    list.style.display = 'block';
                    list.removeAttribute('x-cloak');
                }
            }

            function schedule(e, pageX, pageY) {
                // Ignore presses that originate on the menu itself —
                // user is interacting with the dropdown, not holding
                // the bubble. Also ignore interactive children (links,
                // buttons inside media, etc.).
                if (e.target.closest('.msg-menu-list')) return;
                if (e.target.closest('.msg-menu-btn')) return;
                if (e.target.closest('a[href], button, input, textarea, [data-rtc-call]')) return;

                var wrapper = e.target.closest('.message-wrapper');
                if (!wrapper) return;

                cancelPress();
                pressWrapper = wrapper;
                startX = pageX;
                startY = pageY;
                pressTimer = setTimeout(function () {
                    openMenuFor(pressWrapper);
                    suppressNextClick = true;
                    // Reset the suppress flag shortly after so later
                    // real clicks aren't eaten.
                    setTimeout(function () { suppressNextClick = false; }, 400);
                    pressTimer = null;
                    pressWrapper = null;
                }, HOLD_MS);
            }

            document.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return; // left click only
                schedule(e, e.pageX, e.pageY);
            });
            document.addEventListener('mousemove', function (e) {
                if (!pressTimer) return;
                if (Math.abs(e.pageX - startX) > MOVE_CANCEL_PX || Math.abs(e.pageY - startY) > MOVE_CANCEL_PX) {
                    cancelPress();
                }
            });
            document.addEventListener('mouseup', cancelPress);
            document.addEventListener('mouseleave', cancelPress);

            document.addEventListener('touchstart', function (e) {
                var t = e.touches && e.touches[0];
                if (!t) return;
                schedule(e, t.pageX, t.pageY);
            }, { passive: true });
            document.addEventListener('touchmove', function (e) {
                if (!pressTimer) return;
                var t = e.touches && e.touches[0];
                if (!t) return;
                if (Math.abs(t.pageX - startX) > MOVE_CANCEL_PX || Math.abs(t.pageY - startY) > MOVE_CANCEL_PX) {
                    cancelPress();
                }
            }, { passive: true });
            document.addEventListener('touchend', cancelPress);
            document.addEventListener('touchcancel', cancelPress);

            // Suppress the browser's native context menu that fires after a
            // long-press on mobile — otherwise iOS Safari / Chrome Android
            // show their own "copy / lookup / share" sheet on top of our
            // custom menu, hiding it and confusing the user. Scoped to
            // bubbles so right-click still works everywhere else.
            document.addEventListener('contextmenu', function (e) {
                if (e.target.closest('.message-wrapper')) {
                    e.preventDefault();
                }
            });

            // Swallow the click that follows a long-press so we don't
            // immediately trigger an image/album open under the menu.
            document.addEventListener('click', function (e) {
                if (!suppressNextClick) return;
                if (!e.target.closest('.message-wrapper')) return;
                e.preventDefault();
                e.stopPropagation();
                suppressNextClick = false;
            }, true);
        })();

        /* iOS Safari keyboard-safe focus guard.
           Problem: when the user taps #chatReplyInput, iOS auto-scrolls the
           document to try to bring the input into view. Even though html/body
           are position:fixed and we track visualViewport, iOS still scrolls
           the layout viewport, which pushes the composer to the TOP of the
           visible area and leaves a huge empty black strip above the keyboard
           (reported bug). The fix is to immediately reset the scroll back to
           0 after iOS does its thing, and rely on --app-height from the
           visualViewport sync (in app-evoory layout) to resize the shell
           above the keyboard. Document-level listener on `focusin` so it
           survives Livewire DOM swaps / navigation. */
        (function () {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent)
                       || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            if (!isIOS) return;

            const resetScroll = () => {
                window.scrollTo(0, 0);
                if (document.documentElement) document.documentElement.scrollTop = 0;
                if (document.body) document.body.scrollTop = 0;
                if (document.scrollingElement) document.scrollingElement.scrollTop = 0;
            };

            document.addEventListener('focusin', (e) => {
                const el = e.target;
                if (!el || !(el instanceof HTMLElement)) return;
                // Only guard focus inside the chat shell. We don't want this
                // scroll-reset running on OTHER pages (profile edit, auction
                // bid, etc.) where the normal iOS auto-scroll is useful.
                if (!el.closest('.ev-chat-shell') && !el.closest('.chat-container')) return;
                // iOS fires focus → the browser kicks off its own scroll in
                // the next few frames. Reset after each tick for ~500ms to
                // beat the scroll regardless of when it lands. requestAnim-
                // ationFrame + a couple of setTimeouts covers 60/120Hz devices.
                resetScroll();
                requestAnimationFrame(resetScroll);
                setTimeout(resetScroll, 50);
                setTimeout(resetScroll, 150);
                setTimeout(resetScroll, 300);
                // Also pin the thread to its bottom so the latest message
                // stays visible right above the composer.
                setTimeout(() => { try { scrollToBottom(); } catch (_) {} }, 320);
            }, true);

            // On blur (keyboard dismiss), one more reset so iOS doesn't leave
            // the viewport scrolled a few pixels after the keyboard animates
            // away.
            document.addEventListener('focusout', (e) => {
                const el = e.target;
                if (!el || !(el instanceof HTMLElement)) return;
                if (!el.closest('.ev-chat-shell') && !el.closest('.chat-container')) return;
                setTimeout(resetScroll, 50);
                setTimeout(resetScroll, 300);
            }, true);
        })();

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
                                // Single re-render path. Previous version called
                                // BOTH refreshChat() AND $refresh() — the first
                                // already triggers loadConversationMessages +
                                // markConversationAsRead + re-render, so the
                                // $refresh() was duplicate work (two round-trips,
                                // two morphs) that noticeably slowed every push.
                                const chatComponent = document.querySelector('[wire\\:id]');
                                if (chatComponent) {
                                    const wireId = chatComponent.getAttribute('wire:id');
                                    Livewire.find(wireId).call('refreshChat');
                                    scrollToBottom();
                                }
                                // Fire the global badge refresh immediately on
                                // message push — don't wait for the next poll tick
                                // to update the top-header + mobile-nav badges.
                                if (typeof window.__refreshChatBadges === 'function') {
                                    window.__refreshChatBadges();
                                }
                                // WhatsApp-style instant delivery ack. Scoped to
                                // the specific conversation that received the
                                // push (payload carries conversation_id), so the
                                // UPDATE touches one conv instead of scanning
                                // all of this user's chats. Server flips
                                // 'sent' → 'delivered' and broadcasts
                                // .MessageStatusUpdated back to the sender,
                                // whose UI shows two ticks within a few hundred ms.
                                try {
                                    const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
                                    const url = {!! json_encode(route('chat.ack.delivered')) !!};
                                    const convId = (e && e.message && e.message.conversation_id)
                                        ? Number(e.message.conversation_id)
                                        : 0;
                                    const body = new FormData();
                                    if (convId > 0) body.append('conversation_id', convId);
                                    fetch(url, {
                                        method: 'POST',
                                        credentials: 'same-origin',
                                        keepalive: true,
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        },
                                        body: body,
                                    }).catch(() => { /* best-effort — poll tick is the safety net */ });
                                } catch (ackErr) {
                                    console.warn('[chat] ack-delivered failed', ackErr);
                                }

                                // Instant READ ack: if the matching conversation
                                // is currently open in this user's chat view
                                // (chatReplyInput carries its id), the message
                                // was read the moment it arrived. Previously
                                // only the Livewire refreshChat → markConversation
                                // AsRead path handled this and it intermittently
                                // failed to flip ticks blue until the recipient
                                // re-clicked the conversation. HTTP-direct hit
                                // is reliable and mirrors the ack-delivered path.
                                try {
                                    const convIdForRead = (e && e.message && e.message.conversation_id)
                                        ? Number(e.message.conversation_id)
                                        : 0;
                                    const replyInput = document.getElementById('chatReplyInput');
                                    const openConvId = replyInput ? Number(replyInput.getAttribute('data-conversation-id') || 0) : 0;
                                    if (convIdForRead > 0 && openConvId > 0 && convIdForRead === openConvId && document.visibilityState !== 'hidden') {
                                        const tokenR = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
                                        const urlR = {!! json_encode(route('chat.ack.read')) !!};
                                        const bodyR = new FormData();
                                        bodyR.append('conversation_id', convIdForRead);
                                        fetch(urlR, {
                                            method: 'POST',
                                            credentials: 'same-origin',
                                            keepalive: true,
                                            headers: {
                                                'X-CSRF-TOKEN': tokenR,
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json',
                                            },
                                            body: bodyR,
                                        }).catch(() => { /* best-effort */ });
                                    }
                                } catch (ackReadErr) {
                                    console.warn('[chat] ack-read failed', ackReadErr);
                                }
                            })
                            .listen('.MessageStatusUpdated', (e) => {
                                // Fast path: patch the tick markup directly in the
                                // currently-open thread. Previously this called
                                // loadConversationMessages() which refetched the
                                // last 100 msgs + remorphed the whole list just to
                                // flip a single tick glyph — noticeable lag on
                                // every receipt change. Now a DOM-only swap.
                                try {
                                    const incoming = (e && e.status) || '';
                                    console.log('[chat] MessageStatusUpdated received', { status: incoming, conversation_id: e && e.conversation_id });
                                    if (incoming !== 'delivered' && incoming !== 'read') return;
                                    const RANK = { sent: 0, delivered: 1, read: 2 };
                                    const next = RANK[incoming];
                                    const open = document.querySelector('#chatMessages');
                                    if (!open) return;
                                    const bubbles = open.querySelectorAll('.message-wrapper.sent[data-message-id]');
                                    bubbles.forEach((wrap) => {
                                        const ticks = wrap.querySelector('.message-ticks');
                                        if (!ticks) return;
                                        const cur = ticks.querySelector('.tick');
                                        const curRank = cur && cur.classList.contains('read') ? 2
                                                      : cur && cur.classList.contains('double-tick') ? 1
                                                      : 0;
                                        if (next <= curRank) return; // never downgrade
                                        if (incoming === 'read') {
                                            ticks.innerHTML = '<span class="tick read double-tick">✓✓</span>';
                                        } else {
                                            ticks.innerHTML = '<span class="tick double-tick">✓✓</span>';
                                        }
                                    });
                                } catch (patchErr) {
                                    // Fallback to the server refetch if anything
                                    // in the DOM patch path blows up.
                                    const chatComponent = document.querySelector('[wire\\:id]');
                                    if (chatComponent) {
                                        const wireId = chatComponent.getAttribute('wire:id');
                                        Livewire.find(wireId).call('loadConversationMessages');
                                    }
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

            // Opening a conversation should always land you at the newest
            // message. Force-scroll (ignore the "near-bottom" guard) and
            // retry a few times because the thread markup is rendered in
            // the same Livewire round-trip — images/voice bubbles can
            // grow after their media loads, pushing the real bottom down.
            Livewire.on('conversation-opened', () => {
                scrollToBottom(true);
                setTimeout(() => scrollToBottom(true), 150);
                setTimeout(() => scrollToBottom(true), 400);
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
                // Skip while any dropdown / overlay is open. The Livewire
                // morph wipes vanilla .open classes and Alpine x-show state
                // on these menus, so the UI would snap shut mid-interaction.
                if (document.querySelector('.chat-header-menu.open')) return;
                if (document.querySelector('.new-chat-wrap.open')) return;
                if (document.querySelector('#attachMenu.open')) return;
                if (document.querySelector('#contactOverlay') && document.getElementById('contactOverlay').style.display === 'flex') return;
                if (document.querySelector('.conv-kebab .conv-kebab-menu:not([style*="display: none"])')) return;
                if (document.querySelector('.fwd-overlay')) return;
                if (document.querySelector('#chatSearchBar') && document.getElementById('chatSearchBar').style.display !== 'none') return;
                // Skip while any voice note / video / audio message is actually
                // playing. refreshChat morphs the message list, and even though
                // the <audio> element itself survives the morph, the layout shift
                // from re-rendering surrounding bubbles causes an audible hiccup
                // in playback. User-visible symptom: voice notes stutter every 8s.
                for (const media of document.querySelectorAll('#chatMessages audio, #chatMessages video')) {
                    if (!media.paused && !media.ended) return;
                }
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
            }, 3000);  // 3s safety net. Reverb WebSocket is the fast path,
                       // but when it drops (Cloudflare WS tunnel killed /
                       // Reverb server restarted / client lost socket in a
                       // background tab), 8s felt like "messages never
                       // arrive until I refresh" from the recipient's seat.
                       // 3s is still ~75% cheaper than the pre-WS legacy
                       // 1s poll but close enough to feel real-time when
                       // the WebSocket is dead.
        }

        // Dedicated sender-side tick poll — much lighter than refreshChat.
        // Every 3s, if the currently-open conversation has any outgoing
        // bubbles that aren't yet blue-double-tick, ask the server for
        // their current statuses and patch the DOM directly. Independent
        // of the broadcast path, which was intermittently failing to flip
        // ticks on real setups (recipient's Echo not firing / WebSocket
        // dropping / Cloudflare caching broadcast responses).
        //
        // Keeps DB cost near zero: skips entirely when every sent bubble
        // is already 'read', so a conversation that's caught up doesn't
        // make any requests.
        if (!window.__chatState.sentStatusPoll) {
            window.__chatState.sentStatusPoll = setInterval(() => {
                if (document.hidden) return;
                const replyInput = document.getElementById('chatReplyInput');
                if (!replyInput) return;
                const convId = Number(replyInput.getAttribute('data-conversation-id') || 0);
                if (!convId) return;

                const open = document.querySelector('#chatMessages');
                if (!open) return;

                // Any sent bubble that isn't already blue-read? If not, skip.
                let needsCheck = false;
                open.querySelectorAll('.message-wrapper.sent[data-message-id] .message-ticks .tick').forEach((t) => {
                    if (!t.classList.contains('read')) needsCheck = true;
                });
                if (!needsCheck) return;

                const url = {!! json_encode(url('chat/sent-statuses')) !!} + '/' + convId;
                fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                    .then((r) => r.ok ? r.json() : null)
                    .then((data) => {
                        if (!data || !data.ok || !data.statuses) return;
                        open.querySelectorAll('.message-wrapper.sent[data-message-id]').forEach((wrap) => {
                            const id = wrap.getAttribute('data-message-id');
                            const nextStatus = data.statuses[id];
                            if (!nextStatus) return;
                            const ticks = wrap.querySelector('.message-ticks');
                            if (!ticks) return;
                            const cur = ticks.querySelector('.tick');
                            const curRank = cur && cur.classList.contains('read') ? 2
                                          : cur && cur.classList.contains('double-tick') ? 1
                                          : 0;
                            const RANK = { sent: 0, delivered: 1, read: 2 };
                            const nextRank = RANK[nextStatus] ?? 0;
                            if (nextRank <= curRank) return;
                            if (nextStatus === 'read') {
                                ticks.innerHTML = '<span class="tick read double-tick">✓✓</span>';
                            } else if (nextStatus === 'delivered') {
                                ticks.innerHTML = '<span class="tick double-tick">✓✓</span>';
                            }
                        });
                    })
                    .catch(() => { /* best-effort */ });
            }, 3000);
        }

        // Auto-scroll to the bottom of the message list on new messages, BUT
        // only if the user was already near the bottom. If they've scrolled
        // up to read older history we leave them alone — the 3-second
        // background refreshChat would otherwise keep yanking them back
        // to the newest message, making it impossible to read history.
        // Chat-header "Search" affordance: toggles the in-thread search bar
        // and filters .message-wrapper visibility client-side (no round-trip).
        window.openChatSearchBar = function () {
            const bar = document.getElementById('chatSearchBar');
            const input = document.getElementById('chatSearchInput');
            if (!bar) return;
            bar.style.display = 'flex';
            setTimeout(() => input?.focus(), 50);
        };
        window.closeChatSearchBar = function () {
            const bar = document.getElementById('chatSearchBar');
            const input = document.getElementById('chatSearchInput');
            if (!bar) return;
            bar.style.display = 'none';
            if (input) input.value = '';
            filterChatMessages('');
        };
        window.filterChatMessages = function (q) {
            const needle = (q || '').trim().toLowerCase();
            document.querySelectorAll('#chatMessages .message-wrapper').forEach(w => {
                if (!needle) { w.style.display = ''; return; }
                const txt = (w.innerText || '').toLowerCase();
                w.style.display = txt.includes(needle) ? '' : 'none';
            });
        };

        // Translate vertical mouse-wheel into horizontal scroll on the
        // conversation filter tabs so users on narrow sidebars can still
        // reach Archived without a visible scrollbar.
        if (!window.__cfTabsWheel) {
            window.__cfTabsWheel = true;
            document.addEventListener('wheel', (e) => {
                const bar = e.target.closest('.chat-filter-tabs');
                if (!bar || !bar.scrollWidth || bar.scrollWidth <= bar.clientWidth) return;
                if (e.deltaY === 0) return;
                bar.scrollLeft += e.deltaY;
                e.preventDefault();
            }, { passive: false });
        }

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
                // Paperclip now toggles the attachment menu rather than opening
                // a single file picker. The individual kinds are handled below.
                if (e.target.closest('#attachBtn')) {
                    e.preventDefault();
                    e.stopPropagation();
                    document.getElementById('attachMenu')?.classList.toggle('open');
                    return;
                }
                // Menu row clicks — dispatch per kind.
                const kindBtn = e.target.closest('.attach-menu [data-attach-kind]');
                if (kindBtn) {
                    e.preventDefault();
                    document.getElementById('attachMenu')?.classList.remove('open');
                    handleAttachKind(kindBtn.getAttribute('data-attach-kind'));
                    return;
                }
                // Click outside closes the menu.
                if (!e.target.closest('#attachMenu') && !e.target.closest('#attachBtn')) {
                    document.getElementById('attachMenu')?.classList.remove('open');
                }
                if (e.target.closest('#voiceDeleteBtn') || e.target.closest('#voiceCancelBtn')) {
                    e.preventDefault();
                    cancelRecording();
                    return;
                }
                if (e.target.closest('#voicePauseBtn')) {
                    e.preventDefault();
                    togglePauseRecording();
                    return;
                }
                if (e.target.closest('#voiceRestartBtn')) {
                    e.preventDefault();
                    restartRecording();
                    return;
                }
                if (e.target.closest('#voiceOnceBtn')) {
                    e.preventDefault();
                    const btn = e.target.closest('#voiceOnceBtn');
                    const on = btn.getAttribute('data-vo') === '1';
                    btn.setAttribute('data-vo', on ? '0' : '1');
                    btn.classList.toggle('on', !on);
                    return;
                }
                if (e.target.closest('#voiceSendBtn')) {
                    e.preventDefault();
                    stopRecording(); // onstop handler uploads the blob
                    return;
                }
                if (e.target.closest('#micBtn')) {
                    e.preventDefault();
                    toggleRecording();
                    return;
                }
            }, true); // capture-phase so we run before Livewire's submit binding

            // File input change → POST directly (bypasses Livewire's temp upload).
            // One listener covers every attach kind — each hidden input has
            // its own id but the upload path is identical: ChatMediaController
            // detects type from the MIME on the server side.
            //
            // #attachInputPhotos is `multiple`; the others only ever yield
            // one file. When a batch arrives we split images from videos:
            // images go through the WhatsApp-style multi-image preview
            // modal (up to 20 at once), videos still go one-at-a-time
            // through their own upload flow afterwards.
            document.addEventListener('change', (e) => {
                const t = e.target;
                if (!t.matches || !t.matches('#attachInput, #attachInputPhotos, #attachInputCamera, #attachInputAudio, #attachInputDocs')) return;
                const files = Array.from(t.files || []);
                t.value = ''; // allow picking the same file twice in a row
                if (!files.length) return;

                if (t.id === 'attachInputPhotos' && files.length > 1) {
                    const images = files.filter(f => (f.type || '').startsWith('image/'));
                    const videos = files.filter(f => (f.type || '').startsWith('video/'));
                    if (images.length) openImagePreviewModal(images);
                    // Videos go through the normal single-video flow sequentially.
                    // Each openVideoUploadFlow() drops its own pending bubble + XHR,
                    // so the user can watch them progress independently.
                    videos.forEach(v => uploadAttachmentFile(v));
                    return;
                }
                uploadAttachmentFile(files[0]);
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

        // Dispatcher for the paperclip menu. File-based kinds trigger the
        // matching hidden <input type="file"> and ride the normal upload
        // path. Location and Contact don't go through file upload — they
        // compose a text message and reuse the existing send path.
        function handleAttachKind(kind) {
            switch (kind) {
                case 'photos':   document.getElementById('attachInputPhotos')?.click();  break;
                case 'camera':   document.getElementById('attachInputCamera')?.click();  break;
                case 'audio':    document.getElementById('attachInputAudio')?.click();   break;
                case 'docs':     document.getElementById('attachInputDocs')?.click();    break;
                case 'location': shareLocation();          break;
                case 'contact':  openContactShare();       break;
            }
        }

        // Share current location. Uses the browser's Geolocation API, formats
        // a short text message with a Google Maps link, and submits it through
        // the existing composer. Falls back to an alert if geolocation is
        // unavailable or the user denies permission.
        function shareLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by this browser.');
                return;
            }
            const input = document.getElementById('chatReplyInput');
            if (!input) { alert('Open a conversation first.'); return; }

            // Light feedback: temporarily disable the paperclip while we wait.
            const btn = document.getElementById('attachBtn');
            btn && btn.setAttribute('disabled', 'disabled');

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    btn && btn.removeAttribute('disabled');
                    const lat = pos.coords.latitude.toFixed(6);
                    const lng = pos.coords.longitude.toFixed(6);
                    const link = `https://maps.google.com/?q=${lat},${lng}`;
                    sendTextMessage(`📍 My location: ${link}`);
                },
                (err) => {
                    btn && btn.removeAttribute('disabled');
                    console.warn('[geo] failed', err);
                    alert('Could not get your location. Make sure location access is allowed.');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        }

        // Contact-share modal — simple name + phone fields. On submit we drop
        // a formatted text message into the thread. Future upgrade: an attachment
        // row with type=contact so the UI can render a dedicated "contact card".
        window.openContactShare = function () {
            const input = document.getElementById('chatReplyInput');
            if (!input) { alert('Open a conversation first.'); return; }
            const overlay = document.getElementById('contactOverlay');
            if (!overlay) return;
            document.getElementById('contactShareName').value = '';
            document.getElementById('contactSharePhone').value = '';
            overlay.style.display = 'flex';
            setTimeout(() => document.getElementById('contactShareName').focus(), 60);
        };
        window.closeContactShare = function () {
            const overlay = document.getElementById('contactOverlay');
            if (overlay) overlay.style.display = 'none';
        };
        window.submitContactShare = function () {
            const name  = (document.getElementById('contactShareName').value || '').trim();
            const phone = (document.getElementById('contactSharePhone').value || '').trim();
            if (!name && !phone) { alert('Enter at least a name or a phone number.'); return; }
            closeContactShare();
            const body = `👤 Contact\nName: ${name || '—'}\nPhone: ${phone || '—'}`;
            sendTextMessage(body);
        };

        // Shared helper used by Location and Contact — stuffs a text body into
        // the Livewire-bound input and triggers the composer's submit so the
        // normal sendReply() path persists + broadcasts the message.
        function sendTextMessage(body) {
            const input = document.getElementById('chatReplyInput');
            if (!input) return;
            input.value = body;
            // Sync into the Livewire $reply property BEFORE submitting, otherwise
            // Livewire ignores the value we just set programmatically.
            const root = input.closest('[wire\\:id]');
            if (root && window.Livewire) {
                const comp = Livewire.find(root.getAttribute('wire:id'));
                try { comp?.set('reply', body, false); } catch (_) {}
            }
            const form = input.closest('form.chat-input-form');
            if (form) form.requestSubmit();
        }

        function toggleRecording() {
            if (window.__chatState.recorder) {
                // Guard against a double-fired tap (mobile Safari fast-tap,
                // or any accidentally duplicated click listener). If the
                // recorder was started less than 400ms ago, treat the second
                // event as a no-op instead of stop-and-upload — otherwise a
                // single tap on the mic silently sends an empty voice note.
                const startedAt = window.__chatState.recStartedAt || 0;
                if (startedAt && Date.now() - startedAt < 400) return;
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
                    const duration = Math.max(1, currentRecordingSeconds());
                    if (blob.size < 1024 || duration < 1) {
                        console.warn('voice recording too short, skipping upload', { size: blob.size, duration });
                        alert('Recording too short. Hold or tap to record for at least a second.');
                        return;
                    }
                    const vo = document.getElementById('voiceOnceBtn')?.getAttribute('data-vo') === '1';
                    uploadVoiceNote(blob, duration, { viewOnce: vo });
                };
                rec.start();
                window.__chatState.recorder = rec;
                window.__chatState.recStartedAt = Date.now();
                window.__chatState.recPausedElapsed = 0;
                window.__chatState.recPausedAt = 0;
                micBtn?.classList.add('recording');
                const bar = document.getElementById('voiceRecordingBar');
                bar?.classList.add('active');
                bar?.classList.remove('paused');
                document.querySelector('.chat-input-form')?.classList.add('is-recording');
                document.getElementById('chatReplyInput')?.setAttribute('disabled', 'disabled');

                window.__chatState.recTicker = setInterval(() => {
                    const s = currentRecordingSeconds();
                    const el = document.getElementById('voiceRecTime');
                    if (el) el.textContent = `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;
                    if (s >= 300) stopRecording();
                }, 250);
            } catch (err) {
                alert('Microphone access denied or unavailable.');
                console.warn(err);
            }
        }

        function currentRecordingSeconds() {
            const st = window.__chatState;
            if (!st.recStartedAt) return 0;
            const now = st.recPausedAt || Date.now();
            return Math.floor((now - st.recStartedAt - (st.recPausedElapsed || 0)) / 1000);
        }

        function togglePauseRecording() {
            const rec = window.__chatState.recorder;
            if (!rec) return;
            const bar = document.getElementById('voiceRecordingBar');
            const btn = document.getElementById('voicePauseBtn');
            if (rec.state === 'recording') {
                try { rec.pause(); } catch (_) { return; }
                window.__chatState.recPausedAt = Date.now();
                bar?.classList.add('paused');
                if (btn) {
                    btn.title = 'Resume';
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
                }
            } else if (rec.state === 'paused') {
                try { rec.resume(); } catch (_) { return; }
                window.__chatState.recPausedElapsed += (Date.now() - window.__chatState.recPausedAt);
                window.__chatState.recPausedAt = 0;
                bar?.classList.remove('paused');
                if (btn) {
                    btn.title = 'Pause';
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>';
                }
            }
        }

        function restartRecording() {
            if (!window.__chatState.recorder) return;
            cancelRecording();
            // Small defer so the mic stream fully releases before re-requesting.
            setTimeout(() => startRecording(), 60);
        }

        function stopRecording() {
            const rec = window.__chatState.recorder;
            if (!rec) return;
            clearInterval(window.__chatState.recTicker);
            window.__chatState.recTicker = null;
            try { rec.stop(); } catch (_) {}
            window.__chatState.recorder = null;
            document.getElementById('micBtn')?.classList.remove('recording');
            const bar = document.getElementById('voiceRecordingBar');
            bar?.classList.remove('active', 'paused');
            document.querySelector('.chat-input-form')?.classList.remove('is-recording');
            document.getElementById('chatReplyInput')?.removeAttribute('disabled');
            resetVoiceBarUI();
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
            const bar = document.getElementById('voiceRecordingBar');
            bar?.classList.remove('active', 'paused');
            document.querySelector('.chat-input-form')?.classList.remove('is-recording');
            document.getElementById('chatReplyInput')?.removeAttribute('disabled');
            resetVoiceBarUI();
        }

        function resetVoiceBarUI() {
            const btn = document.getElementById('voicePauseBtn');
            if (btn) {
                btn.title = 'Pause';
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>';
            }
            const once = document.getElementById('voiceOnceBtn');
            if (once) {
                once.setAttribute('data-vo', '0');
                once.classList.remove('on');
            }
            const t = document.getElementById('voiceRecTime');
            if (t) t.textContent = '0:00';
        }

        async function uploadAttachmentFile(file) {
            // Videos take the WhatsApp-style preview path: show the clip
            // + duration + size, capture a first-frame thumbnail, then
            // upload both to R2 via /chat/attachment-upload with upload
            // progress rendered on the overlay. 100 MB cap matches the
            // server (ChatMediaController::VIDEO_MAX_BYTES).
            if (file && (file.type || '').startsWith('video/')) {
                openVideoUploadFlow(file);
                return;
            }

            // Images get a WhatsApp-style preview modal first — user reviews
            // the pic, optionally adds a caption, THEN hits send. The upload
            // itself (openImageUploadFlow) uses the same optimistic-bubble +
            // XHR-progress pattern as video.
            if (file && (file.type || '').startsWith('image/')) {
                openImagePreviewModal(file);
                return;
            }

            // Non-image, non-video (docs/audio) — keep the simple upload
            // path with the compact text-pill progress indicator.
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

        /**
         * WhatsApp-style multi-image send flow.
         *
         *   1. User picks 1-20 images → this modal opens with a full preview
         *      of the first one.
         *   2. For 2+ images, a thumbnail strip appears above the caption
         *      so the user can switch between pictures.
         *   3. Each image carries its own caption (saved on-the-fly as the
         *      user types + switches).
         *   4. The trailing "+" tile in the strip re-opens the picker so
         *      more images can be added up to the 20 cap.
         *   5. The topbar Discard button removes only the CURRENT image;
         *      if that was the last one, the modal closes.
         *   6. On Send, each image is handed off to openImageUploadFlow()
         *      with its own caption; each gets a pending bubble in the
         *      thread and uploads independently.
         *
         * Accepts a File OR an array of File. One pending batch at a time;
         * calling again while open merges the new files into the existing
         * queue (that's how the "+" tile works).
         */
        const IPM_MAX_IMAGES = 20;

        function openImagePreviewModal(fileOrArray) {
            const modal   = document.getElementById('imgPreviewModal');
            const img     = document.getElementById('ipmImage');
            const caption = document.getElementById('ipmCaption');
            const sendBtn = document.getElementById('ipmSendBtn');
            const closeBtn= document.getElementById('ipmCloseBtn');
            const delBtn  = document.getElementById('ipmDeleteBtn');
            const thumbs  = document.getElementById('ipmThumbs');
            const countEl = modal?.querySelector('.ipm-count');
            if (!modal || !img || !sendBtn || !thumbs) return;

            const incoming = Array.isArray(fileOrArray) ? fileOrArray : [fileOrArray];
            const imageFiles = incoming.filter(f => f && (f.type || '').startsWith('image/'));
            if (!imageFiles.length) return;

            // Merge-mode: modal already open (user clicked "+" to add more).
            // Append to the existing state instead of recreating it.
            if (modal.__ipmState) {
                const st = modal.__ipmState;
                const roomLeft = IPM_MAX_IMAGES - st.files.length;
                if (roomLeft <= 0) {
                    alert(`You can send a maximum of ${IPM_MAX_IMAGES} images at once.`);
                    return;
                }
                const toAdd = imageFiles.slice(0, roomLeft);
                if (imageFiles.length > roomLeft) {
                    alert(`Only ${roomLeft} more image${roomLeft === 1 ? '' : 's'} can be added (${IPM_MAX_IMAGES} max).`);
                }
                toAdd.forEach(f => {
                    st.files.push(f);
                    st.captions.push('');
                    st.urls.push(URL.createObjectURL(f));
                });
                st.render();
                return;
            }

            // Fresh open: cap to the first 20 images.
            if (imageFiles.length > IPM_MAX_IMAGES) {
                alert(`Only the first ${IPM_MAX_IMAGES} images will be used (${imageFiles.length} selected).`);
            }
            const files    = imageFiles.slice(0, IPM_MAX_IMAGES);
            const captions = files.map(() => '');
            const urls     = files.map(f => URL.createObjectURL(f));
            let currentIdx = 0;

            const state = {
                files, captions, urls,
                get idx() { return currentIdx; },
                set idx(v) { currentIdx = v; },
                render() { renderAll(); },
            };
            modal.__ipmState = state;

            const render = () => {
                // Preview + caption follow the active index.
                const i = currentIdx;
                img.src = urls[i];
                caption.value = captions[i] || '';
                if (countEl) countEl.textContent = `${i + 1}/${files.length}`;
                // Thumbnail strip only when 2+ files are queued.
                if (files.length > 1) thumbs.classList.add('open');
                else thumbs.classList.remove('open');
                // Rebuild the strip. Cheap — max 21 nodes.
                thumbs.innerHTML = '';
                files.forEach((_, idx) => {
                    const t = document.createElement('button');
                    t.type = 'button';
                    t.className = 'ipm-thumb' + (idx === i ? ' active' : '');
                    t.style.backgroundImage = `url('${urls[idx]}')`;
                    t.setAttribute('aria-label', `Image ${idx + 1}`);
                    t.addEventListener('click', () => {
                        // Save the current caption before switching away.
                        captions[currentIdx] = caption.value;
                        currentIdx = idx;
                        render();
                    });
                    thumbs.appendChild(t);
                });
                // Trailing "+" to add more, while under the cap.
                if (files.length < IPM_MAX_IMAGES) {
                    const add = document.createElement('button');
                    add.type = 'button';
                    add.className = 'ipm-thumb ipm-thumb-add';
                    add.textContent = '+';
                    add.setAttribute('aria-label', 'Add more images');
                    add.addEventListener('click', () => {
                        // Reuse the hidden file input so the native picker opens.
                        // Saved captions survive because we never clear state here.
                        captions[currentIdx] = caption.value;
                        document.getElementById('attachInputPhotos')?.click();
                    });
                    thumbs.appendChild(add);
                }
            };
            const renderAll = render;

            // Save caption as the user types so switching tabs doesn't lose it.
            const onCaptionInput = () => { captions[currentIdx] = caption.value; };

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            render();
            // iOS: delay focus so the keyboard opens after the modal paint.
            setTimeout(() => { try { caption.focus(); } catch (_) {} }, 100);

            const close = () => {
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
                urls.forEach(u => { try { URL.revokeObjectURL(u); } catch (_) {} });
                sendBtn.removeEventListener('click', onSend);
                closeBtn.removeEventListener('click', onClose);
                delBtn.removeEventListener('click', onDiscardCurrent);
                caption.removeEventListener('input', onCaptionInput);
                document.removeEventListener('keydown', onKey);
                delete modal.__ipmState;
            };
            const onClose = () => close();
            const onKey = (e) => { if (e.key === 'Escape') close(); };

            // Discard removes ONLY the current image (not the whole batch).
            // If it was the last one, the modal closes.
            const onDiscardCurrent = () => {
                try { URL.revokeObjectURL(urls[currentIdx]); } catch (_) {}
                files.splice(currentIdx, 1);
                captions.splice(currentIdx, 1);
                urls.splice(currentIdx, 1);
                if (!files.length) { close(); return; }
                if (currentIdx >= files.length) currentIdx = files.length - 1;
                render();
            };

            const onSend = () => {
                // Capture the latest caption before firing.
                captions[currentIdx] = caption.value;
                // When the user picked 2+ images, mint ONE batch_id and
                // ship it with every upload in the batch. The server stores
                // it on each row; Chat::loadConversationMessages then
                // collapses them into a single WhatsApp-style album grid.
                // Single-image sends get null → render as a plain bubble.
                const batchId = files.length > 1 ? makeUuid() : null;
                const snapshot = files.map((f, i) => ({
                    file: f,
                    caption: (captions[i] || '').trim(),
                    // Fresh object URL for the pending bubble so close() can
                    // revoke the preview URLs without breaking the bubbles.
                    bubbleUrl: URL.createObjectURL(f),
                    batchId,
                }));
                close();
                // Serialize uploads: fire one at a time instead of all in
                // parallel. Parallel XHRs can collide with PHP's session
                // lock and (on Windows) realpath cache, causing some uploads
                // to fail with "Path cannot be empty" from Flysystem. The
                // pending bubbles are inserted immediately inside
                // openImageUploadFlow so the user still sees all N bubbles
                // the moment they hit Send — only the actual byte transfer
                // is sequential.
                (async () => {
                    for (const { file, caption, bubbleUrl, batchId } of snapshot) {
                        try { await openImageUploadFlow(file, caption, bubbleUrl, batchId); }
                        catch (err) { console.error('[imgSend] upload failed', err); }
                    }
                })();
            };

            sendBtn.addEventListener('click', onSend);
            closeBtn.addEventListener('click', onClose);
            delBtn.addEventListener('click', onDiscardCurrent);
            caption.addEventListener('input', onCaptionInput);
            document.addEventListener('keydown', onKey);
        }

        /**
         * Optimistic image upload — mirrors openVideoUploadFlow() but for
         * images. The user already saw the preview; here we just:
         *   1. Insert a pending bubble with the local object URL as the
         *      background (so the user sees their picture inline right away).
         *   2. XHR upload to /chat/attachment-upload, driving the progress
         *      ring in the bubble as bytes go out.
         *   3. On success → remove the pending bubble + refreshChat().
         *   4. On failure → flip bubble to a red/failed state (auto-removes
         *      after 5s).
         */
        // Cheap UUID helper. Prefers the native crypto.randomUUID() when
        // available (all current browsers over https / localhost); falls
        // back to a timestamp-plus-random string for older WebViews. We
        // only use it to group N image uploads into one album server-side,
        // so uniqueness guarantees aren't security-critical.
        function makeUuid() {
            try {
                if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                    return window.crypto.randomUUID();
                }
            } catch (_) {}
            return 'b-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
        }

        function openImageUploadFlow(file, caption, bubbleUrl, batchId) {
            return new Promise((resolve) => {
                const convInput = document.getElementById('chatReplyInput');
                const conversationId = convInput?.getAttribute('data-conversation-id');
                if (!conversationId) { alert('No conversation open.'); resolve({ ok: false }); return; }

                const pending = insertPendingImageBubble(bubbleUrl);

                const fd = new FormData();
                fd.append('conversation_id', conversationId);
                fd.append('attachment', file);
                if (caption) fd.append('message', caption);
                // Album grouping — all images in one send share this UUID; the
                // server persists it and the chat loader collapses matching
                // rows into a single WhatsApp-style grid bubble.
                if (batchId) fd.append('batch_id', batchId);

                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/chat/attachment-upload', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.withCredentials = true;

                xhr.upload.onprogress = (ev) => {
                    if (!ev.lengthComputable) return;
                    const pct = Math.round((ev.loaded / ev.total) * 100);
                    if (pending?.setProgress) pending.setProgress(pct);
                };
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (bubbleUrl) URL.revokeObjectURL(bubbleUrl);
                        if (pending?.remove) pending.remove();
                        const anchor = document.getElementById('chatReplyInput') || document.getElementById('attachBtn');
                        const root = anchor?.closest('[wire\\:id]');
                        if (root) Livewire.find(root.getAttribute('wire:id'))?.call('refreshChat');
                        resolve({ ok: true });
                    } else {
                        let msg = 'Upload failed (' + xhr.status + ')';
                        try {
                            const body = JSON.parse(xhr.responseText || '{}');
                            if (body.detail) msg = body.detail;
                        } catch (_) {}
                        if (pending?.markFailed) pending.markFailed(msg);
                        resolve({ ok: false, status: xhr.status, message: msg });
                    }
                };
                xhr.onerror = () => {
                    if (pending?.markFailed) pending.markFailed('Upload failed');
                    resolve({ ok: false });
                };
                xhr.send(fd);
            });
        }

        /**
         * Pending-image bubble inserted into the thread while an upload is
         * in flight. The local object URL is used as the tile background so
         * the user sees their pic immediately; a semi-transparent overlay
         * with "Uploading…" + a progress ring sits on top. Caller gets
         * setProgress / markFailed / remove methods to drive it.
         */
        function insertPendingImageBubble(objectUrl) {
            const list = document.getElementById('chatMessages');
            if (!list) return null;

            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
            const wrap = document.createElement('div');
            wrap.className = 'message-wrapper sent msg-pending';
            wrap.innerHTML = `
                <div class="message-bubble" style="padding:4px;">
                    <div class="msg-image-pending" style="background-image:url('${objectUrl}');">
                        <div class="mvp-ring">
                            <svg viewBox="0 0 64 64">
                                <circle class="bg" cx="32" cy="32" r="29"></circle>
                                <circle class="fg" cx="32" cy="32" r="29"></circle>
                            </svg>
                            <span class="mvp-pct">0%</span>
                        </div>
                        <div class="mvp-label">Uploading…</div>
                    </div>
                </div>
                <div class="message-time">
                    <span>${timeStr}</span>
                    <span class="message-ticks"><span class="tick"><i class="fa fa-clock" aria-hidden="true"></i></span></span>
                </div>
            `;
            list.appendChild(wrap);
            list.scrollTop = list.scrollHeight;

            const fg    = wrap.querySelector('.mvp-ring .fg');
            const pctEl = wrap.querySelector('.mvp-pct');
            const label = wrap.querySelector('.mvp-label');

            return {
                el: wrap,
                setProgress(pct) {
                    pct = Math.max(0, Math.min(100, pct));
                    if (fg)    fg.style.strokeDashoffset = String(182 - (182 * pct / 100));
                    if (pctEl) pctEl.textContent = pct + '%';
                    if (label) label.textContent = pct >= 100 ? 'Finishing…' : `Uploading ${pct}%`;
                },
                markFailed(message) {
                    const tile = wrap.querySelector('.msg-image-pending');
                    if (tile)  tile.classList.add('failed');
                    if (label) label.textContent = message || 'Upload failed';
                    if (pctEl) pctEl.textContent = '!';
                    setTimeout(() => wrap.remove(), 5000);
                },
                remove() { wrap.remove(); },
            };
        }

        /**
         * WhatsApp-style video upload: NO modal. Flow:
         *  1. 100 MB cap — reject up front.
         *  2. Insert an optimistic pending bubble into the chat thread
         *     immediately so the user sees progress right where the
         *     message will land.
         *  3. In parallel: load the file into a hidden <video> element
         *     to grab a first-frame JPEG thumbnail, kick off the XHR
         *     upload. As soon as the thumbnail is ready it's painted
         *     into the pending bubble as the background; the upload
         *     starts the moment the XHR is primed (not blocked on the
         *     thumbnail — it's just a nice-to-have).
         *  4. On success → remove the pending bubble and refreshChat()
         *     brings in the real row (which has the server-side poster
         *     from R2). On failure → bubble flips red + auto-dismiss.
         */
        function openVideoUploadFlow(file) {
            const MAX = 100 * 1024 * 1024;
            if (file.size > MAX) {
                alert('Video is larger than 100 MB. Please pick a smaller clip.');
                return;
            }

            const convInput = document.getElementById('chatReplyInput');
            const conversationId = convInput?.getAttribute('data-conversation-id');
            if (!conversationId) { alert('No conversation open.'); return; }

            // Pending bubble appears immediately. Thumbnail arrives async
            // (usually within ~100-300ms); we patch it in via setThumb().
            const pending = insertPendingVideoBubble(null);

            // ---- Thumbnail capture (hidden, offscreen) ----
            // A detached <video> + <canvas> handles the first-frame grab.
            // Nothing is appended to the DOM so there's no visible flash.
            const probe = document.createElement('video');
            probe.muted = true;
            probe.playsInline = true;
            probe.preload = 'metadata';
            probe.crossOrigin = 'anonymous';
            const probeUrl = URL.createObjectURL(file);
            probe.src = probeUrl;

            let thumbBlob = null;
            let thumbReady = false;
            let dur = 0;

            const captureThumbAndMaybeAttach = () => {
                try {
                    const w = probe.videoWidth || 640;
                    const h = probe.videoHeight || 360;
                    const scale = w > 720 ? 720 / w : 1;
                    const tw = Math.round(w * scale);
                    const th = Math.round(h * scale);
                    const canvas = document.createElement('canvas');
                    canvas.width = tw;
                    canvas.height = th;
                    canvas.getContext('2d').drawImage(probe, 0, 0, tw, th);
                    canvas.toBlob((blob) => {
                        thumbBlob = blob || null;
                        thumbReady = true;
                        if (thumbBlob && pending?.setThumb) {
                            const url = URL.createObjectURL(thumbBlob);
                            pending.setThumb(url);
                            // Revoke later; the pending bubble will be
                            // removed when upload completes, releasing
                            // the reference naturally.
                            pending._thumbUrl = url;
                        }
                        // If the XHR already fetched its FormData snapshot
                        // before the thumb was ready we can't retroactively
                        // add it — that's fine, server stores the video
                        // without a poster and the frontend still renders
                        // (just as a dark tile until play).
                    }, 'image/jpeg', 0.82);
                } catch (err) {
                    console.warn('[videoUpload] thumb capture failed', err);
                    thumbReady = true;
                }
            };

            probe.addEventListener('loadedmetadata', () => {
                dur = isFinite(probe.duration) ? Math.round(probe.duration) : 0;
                try { probe.currentTime = Math.min(0.1, Math.max(0, (probe.duration || 0) - 0.05)); }
                catch (_) { captureThumbAndMaybeAttach(); }
            }, { once: true });
            probe.addEventListener('seeked',  captureThumbAndMaybeAttach, { once: true });
            probe.addEventListener('error', () => { thumbReady = true; }, { once: true });

            // ---- Upload (fires as soon as the probe has settled) ----
            // We wait up to 1.5s for the thumbnail; if it's taking longer
            // we go without one so the upload isn't blocked on slow
            // decoding of large clips. Server will have no poster, which
            // only means a darker tile in chat — acceptable fallback.
            const startUpload = () => {
                URL.revokeObjectURL(probeUrl);

                const fd = new FormData();
                fd.append('conversation_id', conversationId);
                fd.append('attachment', file);
                if (thumbBlob) fd.append('thumbnail', thumbBlob, 'thumb.jpg');
                if (dur > 0) fd.append('duration', String(dur));

                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/chat/attachment-upload', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.withCredentials = true;

                xhr.upload.onprogress = (ev) => {
                    if (!ev.lengthComputable) return;
                    const pct = Math.round((ev.loaded / ev.total) * 100);
                    if (pending?.setProgress) pending.setProgress(pct);
                };
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (pending?._thumbUrl) URL.revokeObjectURL(pending._thumbUrl);
                        if (pending?.remove) pending.remove();
                        const anchor = document.getElementById('chatReplyInput') || document.getElementById('attachBtn');
                        const root = anchor?.closest('[wire\\:id]');
                        if (root) Livewire.find(root.getAttribute('wire:id'))?.call('refreshChat');
                    } else {
                        let msg = 'Upload failed (' + xhr.status + ')';
                        try {
                            const body = JSON.parse(xhr.responseText || '{}');
                            if (body.detail) msg = body.detail;
                        } catch (_) {}
                        if (pending?.markFailed) pending.markFailed(msg);
                    }
                };
                xhr.onerror = () => {
                    if (pending?.markFailed) pending.markFailed('Upload failed');
                };
                xhr.send(fd);
            };

            // Give the thumbnail up to 1.5s to materialise before firing.
            let fired = false;
            const fire = () => { if (fired) return; fired = true; startUpload(); };
            const settleCheck = setInterval(() => {
                if (thumbReady) { clearInterval(settleCheck); fire(); }
            }, 50);
            setTimeout(() => { clearInterval(settleCheck); fire(); }, 1500);
        }

        // Lightbox-style video player — opened by clicking a sent/received
        // video bubble. Keeps the thread view uncluttered (bubbles are
        // posters, not inline players) and gives the video the whole
        // viewport for playback. Also handles ESC + background-click to
        // close, and native <video controls> for seek/volume/fullscreen.
        /**
         * Chat video playback lightbox — sheet-style modal with a WhatsApp-style
         * header (back + sender + timestamp + share) and a Reply field at the
         * bottom. Reply submits as a normal threaded reply (reply_to_id = the
         * video message), then closes the lightbox.
         *
         * ctx = { url, messageId, senderName, sentAt }
         */
        window.openVideoLightbox = function (ctx) {
            // Back-compat: callers that pass a plain URL still work.
            if (typeof ctx === 'string') ctx = { url: ctx };
            const { url, messageId, senderName, sentAt } = ctx;

            let lb = document.getElementById('chatVideoLightbox');
            if (!lb) {
                lb = document.createElement('div');
                lb.id = 'chatVideoLightbox';
                lb.className = 'chat-video-lightbox';
                lb.innerHTML = `
                    <div class="cvl-sheet" role="dialog" aria-modal="true">
                        <div class="cvl-head">
                            <button type="button" class="cvl-back" aria-label="Close">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <div class="cvl-head-info">
                                <div class="cvl-sender"></div>
                                <div class="cvl-time"></div>
                            </div>
                            <button type="button" class="cvl-share" aria-label="Forward">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 17 20 12 15 7"/><path d="M4 18v-2a4 4 0 0 1 4-4h12"/></svg>
                            </button>
                        </div>
                        <div class="cvl-body">
                            <video controls playsinline></video>
                        </div>
                        <form class="cvl-reply" autocomplete="off">
                            <div class="cvl-reply-input">
                                <input type="text" placeholder="Reply">
                                <button type="button" class="cvl-reply-emoji" aria-label="Emoji" tabindex="-1">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                                </button>
                            </div>
                            <button type="submit" class="cvl-reply-send" aria-label="Send" disabled>
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
                            </button>
                        </form>
                    </div>
                `;
                const close = () => {
                    const v = lb.querySelector('video');
                    try { v.pause(); } catch (_) {}
                    v.removeAttribute('src');
                    v.load();
                    lb.classList.remove('open');
                    lb.querySelector('.cvl-reply input').value = '';
                    lb.querySelector('.cvl-reply-send').disabled = true;
                };

                // Click on the dark backdrop OR on the back arrow closes.
                lb.addEventListener('click', (e) => {
                    if (e.target === lb || e.target.closest('.cvl-back')) close();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && lb.classList.contains('open')) close();
                });

                // Enable/disable Send based on input content.
                const input = lb.querySelector('.cvl-reply input');
                const sendBtn = lb.querySelector('.cvl-reply-send');
                input.addEventListener('input', () => {
                    sendBtn.disabled = input.value.trim().length === 0;
                });

                // Share button → forward the message.
                lb.querySelector('.cvl-share').addEventListener('click', () => {
                    const mid = lb.__messageId;
                    if (!mid) return;
                    const anchor = document.getElementById('chatSidebar');
                    const root = anchor?.closest('[wire\\:id]');
                    if (root) Livewire.find(root.getAttribute('wire:id'))?.call('startForward', mid);
                    close();
                });

                // Reply submit → send inline reply with reply_to_id set to the
                // video message, then close the lightbox. We batch three
                // Livewire calls (startReply + set reply text + sendReply)
                // so Livewire 3's request queue handles them in one trip.
                lb.querySelector('.cvl-reply').addEventListener('submit', (e) => {
                    e.preventDefault();
                    const text = input.value.trim();
                    const mid = lb.__messageId;
                    if (!text || !mid) return;
                    const anchor = document.getElementById('chatSidebar');
                    const root = anchor?.closest('[wire\\:id]');
                    const comp = root ? Livewire.find(root.getAttribute('wire:id')) : null;
                    if (!comp) { close(); return; }
                    try {
                        comp.call('startReply', mid);
                        comp.set('reply', text);
                        comp.call('sendReply');
                    } catch (err) {
                        console.warn('[cvl] inline reply failed', err);
                    }
                    close();
                });

                document.body.appendChild(lb);
            }

            // Populate per-open fields.
            lb.__messageId = messageId || null;
            lb.querySelector('.cvl-sender').textContent = senderName || 'User';
            lb.querySelector('.cvl-time').textContent = sentAt || '';
            const v = lb.querySelector('video');
            v.src = url;
            lb.classList.add('open');
            v.play().catch(() => {});
        };

        // Delegated click handler for video bubbles → opens the lightbox.
        // Document-scoped so it survives every Livewire DOM morph.
        if (!window.__chatState.videoLightboxWired) {
            window.__chatState.videoLightboxWired = true;
            document.addEventListener('click', (e) => {
                const bubble = e.target.closest('.msg-video[data-video-url]');
                if (!bubble) return;
                e.preventDefault();
                window.openVideoLightbox({
                    url:        bubble.getAttribute('data-video-url'),
                    messageId:  bubble.getAttribute('data-message-id'),
                    senderName: bubble.getAttribute('data-sender-name'),
                    sentAt:     bubble.getAttribute('data-sent-at'),
                });
            });
        }

        /**
         * Drop an optimistic "sending video" bubble into the active chat
         * thread. The captured thumbnail (object URL) is shown as the
         * tile background; a progress ring sits in the centre until the
         * upload completes, then the caller removes this bubble and
         * refreshChat() morphs in the real row from the server.
         *
         * Returns { el, setProgress, markFailed, remove } so the caller
         * can drive the ring + handle failure/cleanup.
         */
        function insertPendingVideoBubble(thumbUrl) {
            const list = document.getElementById('chatMessages');
            if (!list) return null;

            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
            const wrap = document.createElement('div');
            wrap.className = 'message-wrapper sent msg-pending';
            wrap.innerHTML = `
                <div class="message-bubble" style="padding:4px;">
                    <div class="msg-video-pending" style="${thumbUrl ? `background-image:url('${thumbUrl}');` : ''}">
                        <div class="mvp-ring">
                            <svg viewBox="0 0 64 64">
                                <circle class="bg" cx="32" cy="32" r="29"></circle>
                                <circle class="fg" cx="32" cy="32" r="29"></circle>
                            </svg>
                            <span class="mvp-pct">0%</span>
                        </div>
                        <div class="mvp-label">Uploading…</div>
                    </div>
                </div>
                <div class="message-time">
                    <span>${timeStr}</span>
                    <span class="message-ticks"><span class="tick"><i class="fa fa-clock" aria-hidden="true"></i></span></span>
                </div>
            `;
            list.appendChild(wrap);
            list.scrollTop = list.scrollHeight;

            const tileEl = wrap.querySelector('.msg-video-pending');
            const fg     = wrap.querySelector('.mvp-ring .fg');
            const pctEl  = wrap.querySelector('.mvp-pct');
            const label  = wrap.querySelector('.mvp-label');

            return {
                el: wrap,
                // Patch the background image once the thumbnail is ready
                // (the bubble is dropped into the DOM immediately on
                // upload start; the thumbnail capture completes a few
                // hundred ms later).
                setThumb(url) {
                    if (!tileEl || !url) return;
                    tileEl.style.backgroundImage = `url('${url}')`;
                },
                setProgress(pct) {
                    pct = Math.max(0, Math.min(100, pct));
                    if (fg)    fg.style.strokeDashoffset = String(182 - (182 * pct / 100));
                    if (pctEl) pctEl.textContent = pct + '%';
                    if (label) label.textContent = pct >= 100 ? 'Finishing…' : `Uploading ${pct}%`;
                },
                markFailed(message) {
                    const tile = wrap.querySelector('.msg-video-pending');
                    if (tile)  tile.classList.add('failed');
                    if (label) label.textContent = message || 'Upload failed';
                    if (pctEl) pctEl.textContent = '!';
                    setTimeout(() => wrap.remove(), 5000);
                },
                remove() { wrap.remove(); },
            };
        }

        async function uploadVoiceNote(blob, durationSec, opts) {
            const input = document.getElementById('chatReplyInput');
            const conversationId = input?.getAttribute('data-conversation-id');
            if (!conversationId) {
                alert('No conversation open.');
                return;
            }

            const type = blob.type && blob.type.includes('/') ? blob.type : 'audio/webm';
            const ext  = type.includes('mp4') ? 'm4a' : 'webm';
            const file = new File([blob], `voice-${Date.now()}.${ext}`, { type });

            // Optimistic UI — drop a "sending…" bubble into the thread
            // immediately so the user doesn't stare at a blank space while
            // the upload is in flight (can be 1-3s on slow uplinks).
            // Removed once refreshChat() morphs in the real message; replaced
            // with a retry-able error state if the upload fails.
            const pendingEl = insertPendingVoiceBubble(durationSec);

            const fd = new FormData();
            fd.append('conversation_id', conversationId);
            fd.append('voice_note', file);
            fd.append('duration', String(Math.max(1, durationSec)));
            if (opts && opts.viewOnce) fd.append('view_once', '1');

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
                    markPendingBubbleFailed(pendingEl, 'Upload failed (' + res.status + ')');
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
                // Remove the pending bubble shortly after the refresh so the
                // real message takes its place without flicker. The real
                // message is appended by Livewire's morph; delaying 400ms
                // avoids a visible "nothing there" gap between removal and
                // morph completion.
                setTimeout(() => pendingEl && pendingEl.remove(), 400);
            } catch (err) {
                console.error('[voice] upload error', err);
                markPendingBubbleFailed(pendingEl, 'Upload failed — tap to retry');
            }
        }

        /** Insert a WhatsApp-style "sending voice..." bubble into the thread. */
        function insertPendingVoiceBubble(durationSec) {
            const list = document.getElementById('chatMessages');
            if (!list) return null;
            const fmt = (s) => {
                s = Math.max(0, Math.floor(s));
                return Math.floor(s/60) + ':' + String(s%60).padStart(2,'0');
            };
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
            const wrap = document.createElement('div');
            wrap.className = 'message-wrapper sent msg-pending';
            wrap.innerHTML = `
                <div class="message-bubble">
                    <div class="msg-voice-sending">
                        <span class="spinner"></span>
                        <span class="msg-voice-sending-text">Sending voice…</span>
                        <span class="msg-voice-sending-dur">${fmt(durationSec)}</span>
                    </div>
                </div>
                <div class="message-time">
                    <span>${timeStr}</span>
                    <span class="message-ticks"><span class="tick"><i class="fa fa-clock" aria-hidden="true"></i></span></span>
                </div>
            `;
            list.appendChild(wrap);
            list.scrollTop = list.scrollHeight;
            return wrap;
        }

        /** Flip the pending bubble into a red "failed" state the user can dismiss. */
        function markPendingBubbleFailed(el, message) {
            if (!el) return;
            const body = el.querySelector('.msg-voice-sending');
            if (body) {
                body.classList.add('failed');
                const text = body.querySelector('.msg-voice-sending-text');
                if (text) text.textContent = message || 'Upload failed';
            }
            // Auto-dismiss after 4s so a failed bubble doesn't stick forever.
            setTimeout(() => el && el.remove(), 4000);
        }

        // Force Chrome/Firefox to compute the real duration of a streamed WebM.
        // MediaRecorder produces WebM without cues/duration in the header, so
        // audio.duration is Infinity until the browser scans the whole file.
        // Seeking to +∞ triggers that scan; we rewind to 0 on the first
        // durationchange with a finite value.
        //
        // iOS Safari quirk: durationchange sometimes never fires with a
        // finite value for a short WebM, which leaves currentTime stuck at
        // 1e101 — the timestamp then prints as "1.666...e+99:08" and
        // playback can't resume because the audio thinks it's past the end.
        // Guard with a 2 s safety timeout that resets currentTime back to 0
        // regardless, so playback is always usable.
        window.fixAudioDuration = function (audio) {
            if (!audio || audio.__durationFixed) return;
            audio.__durationFixed = true;
            if (audio.duration === Infinity || isNaN(audio.duration)) {
                let safetyTimer = null;
                const onChange = () => {
                    if (audio.duration !== Infinity && !isNaN(audio.duration)) {
                        audio.removeEventListener('durationchange', onChange);
                        clearTimeout(safetyTimer);
                        try { audio.currentTime = 0; } catch (_) {}
                    }
                };
                audio.addEventListener('durationchange', onChange);
                try { audio.currentTime = 1e101; } catch (_) {}
                // iOS fallback — rewind unconditionally after 2 s so the
                // player never shows the 1e101 artifact or refuses to play.
                safetyTimer = setTimeout(() => {
                    audio.removeEventListener('durationchange', onChange);
                    try { audio.currentTime = 0; } catch (_) {}
                }, 2000);
            }
        };

        // Voice-player wiring. One rAF loop runs globally and ticks every
        // player that's currently playing — this is far smoother than
        // leaning on `timeupdate` (fires at ~250ms intervals and jitters).
        // Called via inline onloadedmetadata on each <audio>; UI events go
        // through the delegated handlers below so Livewire DOM swaps don't
        // orphan listeners.
        const PLAY_ICON  = '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z"/></svg>';
        const PAUSE_ICON = '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>';
        const WAVE_BARS  = 32;

        // Any value above this is treated as "the audio element hasn't settled
        // its duration yet" and we fall back to the server-provided value.
        // 24 h is a safe cap — the server itself rejects voice notes >5 min.
        const VOICE_MAX_SECONDS = 86400;

        function fmtTime(sec) {
            // Guard against NaN, Infinity, negatives, AND the 1e101 artifact
            // iOS Safari leaves behind after fixAudioDuration seeks to EOF.
            // Without the upper cap the number formats as scientific notation
            // ("1.666...e+99:08") in the timestamp slot.
            if (!isFinite(sec) || sec < 0 || sec > VOICE_MAX_SECONDS) sec = 0;
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return m + ':' + (s < 10 ? '0' + s : s);
        }

        function voiceDuration(audio, wrap) {
            // Reject bogus durations the same way as fmtTime — iOS's seek-to-EOF
            // hack can leave audio.duration itself unreasonably large on short
            // WebM blobs. Server fallback is always the trustworthy source.
            const d = audio.duration;
            if (isFinite(d) && d > 0 && d <= VOICE_MAX_SECONDS) return d;
            const fallback = parseFloat(wrap.getAttribute('data-vp-duration') || '0');
            return isFinite(fallback) && fallback > 0 ? fallback : 0;
        }

        // Deterministic PRNG — same clip gives the same waveform shape on
        // every reload, so users recognise their own voice-notes visually.
        // Seeded off the audio src (so unique per message) via a cheap
        // string-hash → mulberry32 generator.
        function hashString(s) {
            let h = 2166136261;
            for (let i = 0; i < s.length; i++) {
                h ^= s.charCodeAt(i);
                h = Math.imul(h, 16777619);
            }
            return h >>> 0;
        }
        function mulberry32(seed) {
            return function () {
                let t = seed += 0x6D2B79F5;
                t = Math.imul(t ^ (t >>> 15), t | 1);
                t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
                return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
            };
        }

        function buildWaveform(wrap, audio) {
            const wave = wrap.querySelector('.vp-waveform');
            if (!wave || wave.childElementCount) return;
            const rand = mulberry32(hashString(audio.currentSrc || audio.src || 'x'));
            const frag = document.createDocumentFragment();
            for (let i = 0; i < WAVE_BARS; i++) {
                const bar = document.createElement('span');
                bar.className = 'vp-bar';
                // Height varies between 25% and 100% — biased toward middle
                // of the clip so the "shape" reads as a voice envelope.
                const centerBias = 1 - Math.abs((i / (WAVE_BARS - 1)) - 0.5) * 1.4;
                const h = Math.max(0.22, Math.min(1, (0.35 + rand() * 0.75) * Math.max(0.4, centerBias)));
                bar.style.height = Math.round(h * 100) + '%';
                frag.appendChild(bar);
            }
            wave.appendChild(frag);
        }

        function paintProgress(wrap, audio) {
            const dur  = voiceDuration(audio, wrap);
            const wave = wrap.querySelector('.vp-waveform');
            const time = wrap.querySelector('.vp-time');
            if (!wave) return;

            // Sanitize currentTime the same way voiceDuration sanitizes
            // audio.duration — iOS's seek-to-EOF trick can leave it at 1e101
            // on short WebM blobs. Anything beyond the known duration (or the
            // 24 h cap) is treated as "not yet positioned" → 0.
            let ct = audio.currentTime;
            if (!isFinite(ct) || ct < 0 || ct > VOICE_MAX_SECONDS) ct = 0;
            if (dur > 0 && ct > dur) ct = 0;

            const pct = dur > 0 ? Math.min(1, ct / dur) : 0;
            const bars = wave.children;
            // Which index is "played" up to? Compare against each bar's
            // position inside the row rather than count — cheaper and
            // avoids re-querying the DOM.
            const filled = Math.floor(pct * bars.length);
            for (let i = 0; i < bars.length; i++) {
                const want = i < filled;
                if (((bars[i].className.indexOf('is-played') !== -1)) !== want) {
                    bars[i].classList.toggle('is-played', want);
                }
            }
            if (time) {
                // Show elapsed while playing / after seek, remaining duration
                // when idle at the start — WhatsApp-style.
                const show = (audio.paused && ct === 0) ? dur : ct;
                time.textContent = fmtTime(show);
            }
        }

        // Global rAF ticker: paints progress for every currently-playing
        // player. One timer total instead of N `timeupdate` listeners —
        // smoother and lower overhead when a thread has lots of notes.
        const _playing = new Set();
        let _rafId = 0;
        function _tick() {
            _rafId = 0;
            if (!_playing.size) return;
            _playing.forEach(audio => {
                const wrap = audio.closest('.msg-voice');
                if (wrap) paintProgress(wrap, audio);
            });
            _rafId = requestAnimationFrame(_tick);
        }
        function _ensureTicker() {
            if (!_rafId) _rafId = requestAnimationFrame(_tick);
        }

        window.initVoicePlayer = function (audio) {
            if (!audio || audio.__vpInit) return;
            audio.__vpInit = true;
            window.fixAudioDuration(audio);
            const wrap = audio.closest('.msg-voice');
            if (!wrap) return;
            // Waveform was likely already drawn by paintAllVoiceWaveforms()
            // on first render — buildWaveform() is a no-op when bars exist.
            buildWaveform(wrap, audio);
            // Rebuild the waveform once we actually know the src — in case
            // the hashed value above was computed before `src` resolved on
            // some browsers.
            audio.addEventListener('loadedmetadata', () => paintProgress(wrap, audio));
            audio.addEventListener('durationchange', () => paintProgress(wrap, audio));
            audio.addEventListener('play',  () => { _playing.add(audio); _ensureTicker(); });
            audio.addEventListener('pause', () => { _playing.delete(audio); paintProgress(wrap, audio); });
            audio.addEventListener('ended', () => {
                _playing.delete(audio);
                audio.currentTime = 0;
                const btn = wrap.querySelector('.vp-play');
                if (btn) {
                    btn.innerHTML = PLAY_ICON;
                    btn.classList.remove('is-playing');
                    btn.setAttribute('aria-label', 'Play');
                }
                paintProgress(wrap, audio);
            });
            paintProgress(wrap, audio);
        };

        // Build waveforms immediately on render — don't wait for the audio
        // element's loadedmetadata event. R2-hosted MP3/WebM blobs can take
        // 1-3s to return a metadata response, during which the pill shows
        // only the play button + duration with no bars. The hash seed only
        // needs the src URL (available in the DOM from first paint), not the
        // actual audio data, so we can draw a stable waveform shape right
        // away. Runs on initial page load + after every Livewire morph.
        function paintAllVoiceWaveforms() {
            document.querySelectorAll('.msg-voice').forEach(wrap => {
                const audio = wrap.querySelector('audio');
                if (!audio) return;
                buildWaveform(wrap, audio);
            });
        }
        paintAllVoiceWaveforms();
        document.addEventListener('livewire:navigated', paintAllVoiceWaveforms);
        if (window.Livewire) {
            Livewire.hook('morph.updated', paintAllVoiceWaveforms);
        } else {
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('morph.updated', paintAllVoiceWaveforms);
            });
        }

        // Delegated: play/pause toggle + seek on the waveform. Attached once
        // at script load; survives Livewire morphs because it lives on
        // document, not on individual message nodes.
        if (!window.__vpWired) {
            window.__vpWired = true;

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.msg-voice [data-vp="play"]');
                if (!btn) return;
                const wrap  = btn.closest('.msg-voice');
                const audio = wrap && wrap.querySelector('audio');
                if (!audio) return;

                if (audio.paused) {
                    // Pause every other player so only one plays at a time.
                    document.querySelectorAll('.msg-voice audio').forEach(a => {
                        if (a !== audio && !a.paused) {
                            a.pause();
                            const w = a.closest('.msg-voice');
                            const b = w && w.querySelector('.vp-play');
                            if (b) {
                                b.innerHTML = PLAY_ICON;
                                b.classList.remove('is-playing');
                                b.setAttribute('aria-label', 'Play');
                            }
                        }
                    });
                    // iOS leftover from fixAudioDuration's seek-to-EOF: if
                    // currentTime is still a nonsense value when the user
                    // taps Play, rewind to 0 first — otherwise play() either
                    // fires 'ended' immediately or seeks to a frame past the
                    // end and the clip appears unplayable.
                    if (!isFinite(audio.currentTime) || audio.currentTime < 0 || audio.currentTime > VOICE_MAX_SECONDS) {
                        try { audio.currentTime = 0; } catch (_) {}
                    }
                    audio.play().then(() => {
                        btn.innerHTML = PAUSE_ICON;
                        btn.classList.add('is-playing');
                        btn.setAttribute('aria-label', 'Pause');
                    }).catch(() => {});
                } else {
                    audio.pause();
                    btn.innerHTML = PLAY_ICON;
                    btn.classList.remove('is-playing');
                    btn.setAttribute('aria-label', 'Play');
                }
            });

            // Seek by click / drag anywhere on the waveform.
            function seekFromEvent(wave, clientX) {
                const wrap  = wave.closest('.msg-voice');
                const audio = wrap && wrap.querySelector('audio');
                if (!audio) return;
                const rect = wave.getBoundingClientRect();
                const pct  = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
                const dur  = voiceDuration(audio, wrap);
                if (dur > 0) {
                    audio.currentTime = pct * dur;
                    paintProgress(wrap, audio);
                }
            }
            document.addEventListener('pointerdown', function (e) {
                const wave = e.target.closest('.msg-voice .vp-waveform');
                if (!wave) return;
                seekFromEvent(wave, e.clientX);
                const move = (ev) => seekFromEvent(wave, ev.clientX);
                const up   = () => {
                    document.removeEventListener('pointermove', move);
                    document.removeEventListener('pointerup', up);
                };
                document.addEventListener('pointermove', move);
                document.addEventListener('pointerup', up);
            });
        }

        // Lightbox for message images — handles both a single picture
        // (openLightbox(url)) and an album array walked with ◀ / ▶ arrows
        // and ← / → keys. Album items can be a plain URL list, a mixed
        // list of strings, or [{ url, caption }, ...] objects.
        function ensureLightbox() {
            let lb = document.getElementById('chatLightbox');
            if (lb) return lb;
            lb = document.createElement('div');
            lb.id = 'chatLightbox';
            lb.className = 'chat-lightbox';
            lb.innerHTML = `
                <div class="lb-head">
                    <div class="lb-head-info">
                        <div class="lb-sender"></div>
                        <div class="lb-time"></div>
                    </div>
                </div>
                <button type="button" class="lb-close" aria-label="Close">&times;</button>
                <button type="button" class="lb-prev"  aria-label="Previous">&lsaquo;</button>
                <button type="button" class="lb-next"  aria-label="Next">&rsaquo;</button>
                <span class="lb-count"></span>
                <img alt="">
                <span class="lb-caption"></span>
                <form class="lb-reply" autocomplete="off">
                    <div class="lb-reply-input">
                        <input type="text" placeholder="Reply">
                        <button type="button" class="lb-reply-emoji" aria-label="Emoji" tabindex="-1">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        </button>
                    </div>
                    <button type="submit" class="lb-reply-send" aria-label="Send" disabled>
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
                    </button>
                </form>
            `;
            // State lives on the element so we survive Livewire DOM morphs
            // elsewhere on the page (the lightbox is appended to body).
            lb.__items = [];
            lb.__idx   = 0;
            lb.__ctx   = null;   // { messageId, senderName, sentAt }

            const render = () => {
                if (!lb.__items.length) return;
                const n = lb.__items.length;
                const i = ((lb.__idx % n) + n) % n;   // safe wrap
                lb.__idx = i;
                const item = lb.__items[i];
                const url     = typeof item === 'string' ? item : (item.url || '');
                const caption = typeof item === 'string' ? '' : (item.caption || '');
                lb.querySelector('img').src = url;
                lb.querySelector('.lb-caption').textContent = caption;
                lb.querySelector('.lb-caption').style.display = caption ? '' : 'none';
                lb.querySelector('.lb-count').textContent = `${i + 1}/${n}`;
                lb.classList.toggle('single', n === 1);
            };

            const replyInput = lb.querySelector('.lb-reply input');
            const replyBtn   = lb.querySelector('.lb-reply-send');

            const close = () => {
                lb.classList.remove('open');
                lb.__items = [];
                lb.__ctx   = null;
                replyInput.value = '';
                replyBtn.disabled = true;
            };
            const next  = () => { lb.__idx++; render(); };
            const prev  = () => { lb.__idx--; render(); };

            lb.addEventListener('click', (e) => {
                if (e.target.closest('.lb-close')) { close(); return; }
                if (e.target.closest('.lb-next'))  { next();  return; }
                if (e.target.closest('.lb-prev'))  { prev();  return; }
                // Click on the dark backdrop (not the image or controls) closes.
                if (e.target === lb) close();
            });
            document.addEventListener('keydown', (e) => {
                if (!lb.classList.contains('open')) return;
                // Don't swallow ← / → while typing a reply.
                if (document.activeElement === replyInput) {
                    if (e.key === 'Escape') { replyInput.blur(); }
                    return;
                }
                if (e.key === 'Escape')     close();
                else if (e.key === 'ArrowRight') next();
                else if (e.key === 'ArrowLeft')  prev();
            });

            // Enable/disable Send based on input content.
            replyInput.addEventListener('input', () => {
                replyBtn.disabled = replyInput.value.trim().length === 0;
            });

            // Reply submit → send inline reply with reply_to_id set to the
            // image message, then close the lightbox. Same 3-call pattern
            // as the video lightbox so Livewire 3's request queue bundles
            // them in a single round-trip.
            lb.querySelector('.lb-reply').addEventListener('submit', (e) => {
                e.preventDefault();
                const text = replyInput.value.trim();
                const mid  = lb.__ctx && lb.__ctx.messageId;
                if (!text || !mid) return;
                const anchor = document.getElementById('chatSidebar');
                const root = anchor?.closest('[wire\\:id]');
                const comp = root ? Livewire.find(root.getAttribute('wire:id')) : null;
                if (!comp) { close(); return; }
                try {
                    comp.call('startReply', mid);
                    comp.set('reply', text);
                    comp.call('sendReply');
                } catch (err) {
                    console.warn('[lb] inline reply failed', err);
                }
                close();
            });

            lb.__open = (items, startIdx = 0, ctx = null) => {
                lb.__items = items;
                lb.__idx   = Math.max(0, Math.min(items.length - 1, startIdx | 0));
                lb.__ctx   = ctx;
                // Populate header + toggle reply bar based on whether we
                // have a messageId to attach the reply to.
                const senderEl = lb.querySelector('.lb-sender');
                const timeEl   = lb.querySelector('.lb-time');
                const hasCtx   = !!(ctx && ctx.messageId);
                lb.classList.toggle('no-reply', !hasCtx);
                senderEl.textContent = (ctx && ctx.senderName) ? ctx.senderName : '';
                timeEl.textContent   = (ctx && ctx.sentAt)     ? ctx.sentAt     : '';
                lb.querySelector('.lb-head').style.display = (ctx && (ctx.senderName || ctx.sentAt)) ? '' : 'none';
                replyInput.value = '';
                replyBtn.disabled = true;
                render();
                lb.classList.add('open');
            };

            document.body.appendChild(lb);
            return lb;
        }

        function openLightbox(urlOrItems, startIdx = 0, ctx = null) {
            const lb = ensureLightbox();
            const items = Array.isArray(urlOrItems) ? urlOrItems : [urlOrItems];
            lb.__open(items, startIdx, ctx);
        }
        window.openLightbox = openLightbox;

        // Delegated: tapping any .msg-album tile opens the lightbox at that
        // image. The whole album is pre-serialised on the parent via
        // data-album='{"messageId":..., "items":[{url, caption}, ...]}' so
        // we have everything we need without a round-trip.
        //
        // The same handler also catches .msg-image (single-image bubbles).
        // On mobile — especially iOS Safari — the inline `onclick` attribute
        // on an <img> inside a scrollable + position:fixed shell is
        // unreliable: taps are sometimes dropped entirely, so the desktop-
        // only inline handler isn't enough. A document-level delegated
        // handler fires via browser-synthesised click from touchend and
        // is bulletproof across every mobile browser we test.
        // Reply-quote → jump to the parent message. Reuses the same
        // scrollIntoView + gallery-highlight pulse that the Gallery tab's
        // "open from thumbnail" flow uses, so the behaviour feels consistent
        // across the app. Document-delegated so it survives Livewire DOM
        // morphs. If the parent message isn't in the DOM (older than the
        // currently-loaded window), we silently no-op — a future enhancement
        // could ask the server to extend the window.
        if (!window.__chatState.replyQuoteJumpWired) {
            window.__chatState.replyQuoteJumpWired = true;
            const jumpToMessage = (msgId) => {
                if (!msgId) return;
                const target = document.querySelector(`.message-wrapper[data-message-id="${msgId}"]`);
                if (!target) return;
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.classList.add('gallery-highlight');
                setTimeout(() => target.classList.remove('gallery-highlight'), 2200);
            };
            document.addEventListener('click', (e) => {
                const quote = e.target.closest('.msg-quote[data-jump-to]');
                if (!quote) return;
                e.preventDefault();
                e.stopPropagation();
                jumpToMessage(quote.getAttribute('data-jump-to'));
            });
            // Keyboard a11y — Enter / Space on a focused quote jumps too.
            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Enter' && e.key !== ' ') return;
                const quote = e.target.closest?.('.msg-quote[data-jump-to]');
                if (!quote) return;
                e.preventDefault();
                jumpToMessage(quote.getAttribute('data-jump-to'));
            });
        }

        if (!window.__chatState.albumLightboxWired) {
            window.__chatState.albumLightboxWired = true;
            document.addEventListener('click', (e) => {
                // Album tile → open at the tapped index.
                const tile = e.target.closest('.msg-album .msg-album-tile');
                if (tile) {
                    const album = tile.closest('.msg-album');
                    if (!album) return;
                    let payload;
                    try { payload = JSON.parse(album.getAttribute('data-album') || '{}'); }
                    catch (_) { return; }
                    const items = (payload.items || []).map(it => ({
                        url: it.url, caption: it.caption || '',
                    }));
                    if (!items.length) return;
                    const startIdx = parseInt(tile.getAttribute('data-idx') || '0', 10) || 0;
                    // All images in an album belong to one message, so the
                    // reply bar attaches to a single messageId regardless
                    // of which tile was tapped.
                    const ctx = {
                        messageId:  album.getAttribute('data-message-id'),
                        senderName: album.getAttribute('data-sender-name'),
                        sentAt:     album.getAttribute('data-sent-at'),
                    };
                    openLightbox(items, startIdx, ctx);
                    return;
                }
                // Single image bubble.
                const img = e.target.closest('.msg-image');
                if (img) {
                    const url = img.getAttribute('src');
                    if (!url) return;
                    const ctx = {
                        messageId:  img.getAttribute('data-message-id'),
                        senderName: img.getAttribute('data-sender-name'),
                        sentAt:     img.getAttribute('data-sent-at'),
                    };
                    openLightbox(url, 0, ctx);
                }
            });
        }

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
