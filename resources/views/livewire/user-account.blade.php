@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* Account page styles */
    .ev-header {
    background: #0D1011;
    }
    /* Mobile-only dashboard screen — hidden on desktop. */
    .ev-account-mobile { display: none; }
    /* The success alert is rendered twice: once at the top of the
       mobile dashboard, once in the desktop tab/card flow. Each is
       hidden on the other viewport so it only shows once. */
    .ev-am-alert { display: none; }
    .ev-am-alert {
        margin: 0 0 14px;
    }
    .ev-back-bar {
        background: #1f2222;
        padding: 12px 0;
    }
    .ev-back-bar a {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 400;
    }
    .ev-back-bar h1 {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        text-align: center;
    }
    .ev-back-bar h1 a {
        color: #fff;
        text-decoration: none;
    }
    .ev-account-tabs {
        display: flex;
        gap: 8px;
        padding: 20px 0;
        flex-wrap: wrap;
    }
    .ev-account-tabs a {
        display: inline-flex;
        align-items: center;
        width: 170px;
        gap: 8px;
        padding: 6px 0px;
        border-radius: 5px;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        background: #1D2224;
        justify-content: center;
    }
    .ev-account-tabs a:hover {
        border-color: var(--accent, #C1F11D);
        color: var(--accent, #C1F11D);
    }
    .ev-account-tabs a.active {
        color: #C1F11D;
    }
    .ev-account-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .ev-account-card {
        background: var(--bg-card, #1a1a1a);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: 5px;
        padding: 20px;
        flex: 1 1 220px;
        min-width: 0;
    }
    .ev-account-card h2 {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
   
    .ev-user-card {
        display: flex;
        gap: 16px;
        position: relative;
        overflow: hidden;
        overflow-wrap: anywhere;
    }
    .ev-user-card .ev-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .ev-user-card .ev-user-name {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 8px 0;
    }
    .ev-user-card .ev-user-info {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .ev-user-card .ev-user-info li {
        color: var(--text-secondary, #aaa);
        font-size: 14px;
        margin-bottom: 4px;
    }
    .ev-user-card .ev-user-info li strong {
        color: #fff;
    }
    .ev-edit-btn {
        margin-top: 16px;
        color: #111;
        background: var(--accent, #C1F11D);
        border: none;
        height: 36px;
        border-radius: 999px;
        padding: 0 20px;
        display: inline-flex;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }
    .ev-edit-btn:hover {
        background: #d4ff2b;
        color: #111;
    }
    .ev-credits-amount {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 16px 0;
    }
    .ev-credits-card { position: relative; }
    .ev-credits-history-link {
        position: absolute;
        top: 26px;
        right: 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--accent, #C1F11D);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        padding: 4px 10px;
        border: 1px solid var(--accent, #C1F11D);
        border-radius: 14px;
        transition: all 0.2s ease;
        z-index: 2;
    }
    .ev-credits-history-link:hover {
        background: var(--accent, #C1F11D);
        color: #000;
    }
    .ev-buy-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 20px;
        background: var(--accent, #C1F11D);
        color: #000;
        border: none;
        border-radius: 21.5px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .ev-buy-btn:hover {
        background: var(--accent-hover, #d4f84d);
        transform: translateY(-1px);
    }
    .ev-payments-label {
        color: white;
        font-size: 12px;
        margin: 16px 0 8px 0;
    }
    .ev-payments {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 12px;
        border-top: 1px solid var(--border-color, #2a2a2a);
    }
    .ev-comm-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .ev-comm-list li {
        margin-bottom: 8px;
    }
    .ev-comm-list li:last-child {
        margin-bottom: 0;
    }
    .ev-comm-list a {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        transition: opacity 0.2s;
    }
    .ev-comm-list a:hover {
        opacity: 0.8;
    }
    .ev-comm-list .ev-badge {
        background: #dc3545;
        color: #fff;
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 50%;
        font-weight: 600;
    }
    .ev-newsletter-text {
        color: var(--text-secondary, #aaa);
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }
    .ev-newsletter-text a {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .ev-newsletter-text a:hover {
        opacity: 0.8;
    }
    .ev-delete-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: none;
        border: none;
        color: #C1F11D;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        padding: 16px 0;
        transition: color 0.2s;
    }
    .ev-delete-link:hover {
        color: #dc3545;
    }
    .ev-alert-success {
        background: rgba(193, 241, 29, 0.1);
        border: 1px solid var(--accent, #C1F11D);
        color: var(--accent, #C1F11D);
        padding: 12px 16px;
        border-radius: var(--radius, 8px);
        margin-bottom: 16px;
        font-size: 14px;
    }
    /* Modal styles */
    .ev-modal-overlay {
        background: rgba(0,0,0,0.85);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 99999;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 3rem 1rem;
    }
    .ev-modal {
        background: var(--bg-card, #1a1a1a);
        color: #fff;
        border-radius: var(--radius-lg, 12px);
        border: 1px solid var(--border-color, #2a2a2a);
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        width: 100%;
        max-width: 500px;
    }
    .ev-modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color, #2a2a2a);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ev-modal-header h2 {
        margin: 0;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .ev-modal-close {
        background: var(--bg-card-hover, #222);
        border: 1px solid var(--border-color, #2a2a2a);
        color: #fff;
        font-size: 1.25rem;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .ev-modal-close:hover {
        background: var(--border-color, #2a2a2a);
    }
    .ev-modal-body {
        padding: 1.5rem;
    }
    .ev-modal-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid var(--border-color, #2a2a2a);
        text-align: right;
    }
    .ev-search-input {
        display: flex;
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: var(--radius, 8px);
        overflow: hidden;
        background: var(--bg-secondary, #111);
    }
    .ev-search-input span {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
      
    }
    .ev-search-input input {
        flex: 1;
        padding: 0.75rem;
        background: transparent;
        border: none;
        color: #fff;
        outline: none;
        font-size: 0.95rem;
        font-family: inherit;
    }
    .ev-search-input input::placeholder {
        color: var(--text-muted, #666);
    }
    .ev-search-input button {
        padding: 0.75rem 1rem;
        background: transparent;
        border: none;
        color: var(--accent, #C1F11D);
        cursor: pointer;
    }
    .ev-city-tag {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        padding: 0.75rem 1rem;
        background: var(--bg-secondary, #111);
        border-radius: var(--radius, 8px);
        border: 1px solid var(--border-color, #2a2a2a);
    }
    .ev-dropdown-results {
        position: absolute;
        width: 100%;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        background: var(--bg-secondary, #111);
        border: 1px solid var(--border-color, #2a2a2a);
        border-radius: var(--radius, 8px);
        margin-top: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .ev-dropdown-results button {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        background: transparent;
        color: #fff;
        border: none;
        border-bottom: 1px solid var(--border-color, #2a2a2a);
        text-align: left;
        cursor: pointer;
        transition: background 0.2s;
        font-family: inherit;
        font-size: 14px;
    }
    .ev-dropdown-results button:hover {
        background: var(--bg-card-hover, #222);
    }
    @media (max-width: 768px) {
        /* Header */
        #header { margin-bottom: 0 !important; }
        .ev-header { margin-bottom: 0 !important; padding: 8px 0 !important; }
        .ev-back-bar { padding: 8px 0 !important; }
        .ev-back-bar a { font-size: 13px; }
        .ev-back-bar h1 { font-size: 15px; }

        /* Mobile: hide the existing desktop dashboard content; show the
           new account-summary screen instead. Scoped via body:has() so
           the hide-tabs rule only fires when the mobile dashboard is
           actually on the page — otherwise wire:navigate to other
           account pages (Edit, Password) would carry over the leaked
           CSS and hide their own .ev-account-tabs row. */
        body:has(.ev-account-mobile) .ev-account-tabs,
        body:has(.ev-account-mobile) .ev-account-cards,
        body:has(.ev-account-mobile) .ev-account-delete-wrap,
        body:has(.ev-account-mobile) .ev-account-desktop-alert { display: none !important; }
        .ev-account-mobile { display: block !important; }
        .ev-am-alert { display: block !important; }
        /* Hide the app-evoory header AND the layout's "Back / My Account"
           sub-header on the mobile account screen — the page has its own
           back-link + logo header inline below. */
        body:has(.ev-account-mobile) #header,
        body:has(.ev-account-mobile) .ev-header,
        body:has(.ev-account-mobile) .ev-header-account { display: none !important; }
        /* Trim padding only on the page's own .ev-container (sibling of
           .ev-account-mobile), not the global footer's. */
        .ev-account-mobile ~ .ev-container { padding: 0 !important; }

        /* ---- Account dashboard screen ---- */
        .ev-account-mobile {
            padding: 8px 16px 24px;
            color: #fff;
            max-width: 560px;
            margin: 0 auto;
        }

        /* Header */
        .ev-am-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0 14px;
            gap: 12px;
        }
        .ev-am-back {
            display: inline-flex !important;
            align-items: center;
            gap: 4px;
            color: #C1F11D !important;
            text-decoration: none !important;
            font-size: 15px !important;
            font-weight: 500;
        }
        .ev-am-back svg {
            width: 14px !important;
            height: 14px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-logo {
            display: inline-flex;
            align-items: center;
            text-decoration: none !important;
        }
        .ev-am-logo img { height: 28px; width: auto; display: block; }
        .ev-am-logo span { color: #C1F11D; font-size: 22px; font-weight: 600; font-style: italic; }
        .ev-am-header-spacer { flex: 0 0 50px; }

        /* User identity */
        .ev-am-user {
            text-align: center;
            padding: 6px 0 22px;
        }
        .ev-am-avatar {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #0e1218;
            border: 1px solid #2a3241;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0 auto 10px;
        }
        .ev-am-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .ev-am-avatar svg {
            width: 40px !important;
            height: 40px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-name {
            color: #fff;
            font-size: 19px;
            font-weight: 400;
            margin: 0 0 0px;
        }
        .ev-am-email {
            color: #8b9298;
            font-size: 14px;
            margin: 0;
        }

        /* Menu list */
        .ev-am-menu {
            border-top: 1px solid #33393C;
            border-bottom: 1px solid #33393C;
            margin-bottom: 20px;
            padding:10px 0px;
        }
        .ev-am-item {
            display: flex !important;
            align-items: center;
            gap: 12px;
            padding: 6px 4px;
            color: #fff !important;
            text-decoration: none !important;
          
            font-size: 14px;
        }
        .ev-am-item:last-child { border-bottom: 0; }
        .ev-am-item__icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .ev-am-item__icon svg {
            width: 20px !important;
            height: 20px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-item__label {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ev-am-item__badge {
            flex-shrink: 0;
            min-width: 28px;
            height: 26px;
            padding: 0 9px;
            border-radius: 999px;
            border: 1px solid #C1F11D;
            color: #C1F11D;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .ev-am-item__badge--money { padding: 0 12px; }
        .ev-am-item__chev {
            flex-shrink: 0;
            color: #6a7280;
            display: inline-flex;
        }
        .ev-am-item__chev svg {
            width: 14px !important;
            height: 14px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }

        /* Support card */
        .ev-am-help {
            background: #0e1218;
            border: 1px solid #1a1f2a;
            border-radius: 14px;
            padding: 22px 18px;
            text-align: center;
            margin-bottom: 18px;
        }
        .ev-am-help__icon { display: inline-flex; margin: 0 auto 10px; }
        .ev-am-help__icon svg {
            width: 44px !important;
            height: 44px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-help__title {
            color: #C1F11D;
            font-size: 18px;
            font-weight: 400;
            margin: 0 0 0px;
        }
        .ev-am-help__sub {
            color: #cfd3d6;
            font-size: 13px;
            margin: 0 0 16px;
        }
        .ev-am-help__call {
            display: block;
            background: #1a2010;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 12px;
            color: #fff !important;
            text-decoration: none !important;
            margin-inline: 20px;
            text-align: center;
        }
        .ev-am-help__call-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            font-size: 17px;
            font-weight: 400;
            white-space: nowrap;
        }
        .ev-am-help__call-row svg {
            width: 18px !important;
            height: 18px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-help__call-sub {
            display: block;
            color: #8b9298;
            font-size: 12px;
        }
        .ev-am-help__chats {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
            margin-inline: 20px;
        }
        .ev-am-help__chat {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px;
            border-radius: 10px;
            background: #0a0d12;
            border: 1px solid #2a3241;
            color: #fff !important;
            text-decoration: none !important;
            font-size: 13px;
            font-weight: 500;
        }
        .ev-am-help__chat svg {
            width: 18px !important;
            height: 18px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-help__divider {
            display: flex;
            align-items: center;
            color: #6a7280;
            font-size: 12px;
            margin: 8px 20px;
        }
        .ev-am-help__divider::before,
        .ev-am-help__divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #1a1f2a;
        }
        .ev-am-help__divider span { padding: 0 8px; }
        .ev-am-help__email {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 6px;
            color: #fff !important;
            text-decoration: none !important;
            text-align: left;
            margin-inline: 20px;
        }
        .ev-am-help__email-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(193, 241, 29, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .ev-am-help__email-icon svg {
            width: 20px !important;
            height: 20px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-help__email-text { display: flex; flex-direction: column; line-height: 1.3; }
        .ev-am-help__email-label { color: #fff; font-size: 12px; }
        .ev-am-help__email-addr { color: #fff; font-size: 14px; font-weight: 400; }
        .ev-am-help__footer {
            margin: 14px 0 0;
            color: #8b9298;
            font-size: 12px;
        }

        @media (max-width: 420px) {
            .ev-am-help { padding: 20px 12px; }
            .ev-am-help__call,
            .ev-am-help__chats,
            .ev-am-help__email { margin-inline: 0; }
            .ev-am-help__divider { margin: 8px 0; }
            .ev-am-help__call-row { font-size: 16px; }
        }

        /* Sign-out */
        .ev-am-signout-form { margin: 0; }
        .ev-am-signout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-radius: 10px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #F24E1E;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .ev-am-signout:hover { background: rgba(239, 68, 68, 0.12); }
        .ev-am-signout__left {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .ev-am-signout__left svg {
            width: 18px !important;
            height: 18px !important;
            transform: none !important;
            -webkit-transform: none !important;
            margin: 0 !important;
        }
        .ev-am-signout__right {
            color: #F24E1E;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        /* Tabs - horizontal pills */
        .ev-account-tabs {
            padding: 12px 0;
            gap: 8px;
            flex-wrap: nowrap;
        }
        .ev-account-tabs a {
            padding: 8px 12px;
            font-size: 12px;
            width: auto;
            flex: 1;
            min-width: 0;
            border: 1px solid #333;
            border-radius: 5px;
            background: transparent;
            color: #999;
            justify-content: center;
        }
        .ev-account-tabs a.active {
            border-color: #C1F11D;
            color: #C1F11D;
            background: transparent;
        }

        /* Cards - full width stacked */
        .ev-account-cards {
            flex-direction: column;
            gap: 12px;
        }
        .ev-account-card {
            flex-basis: auto;
            border-radius: 5px;
            padding: 25px;
        }

        /* User card */
        .ev-user-card .ev-avatar {
            width: 56px;
            height: 56px;
        }
        .ev-user-card .ev-user-name {
            font-size: 16px;
            margin-bottom: 4px;
        }
        .ev-user-card .ev-user-info li {
            font-size: 13px;
            margin-bottom: 2px;
        }
        .ev-edit-btn {
            height: 30px;
            font-size: 13px;
            padding: 0 18px;
        }

        /* Credits card - mobile layout */
        .ev-account-card:has(.ev-credits-amount) {
            position: relative;
        }
        .ev-credits-mobile-row {
            display: flex !important;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }
        .ev-credits-icon {
            display: flex !important;
            width: 44px;
            height: 44px;
            background: #1a2a10;
            border: 1px solid #3a4a2a;
            border-radius: 5px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .ev-credits-label {
            color: #999;
            font-size: 12px;
            margin: 0;
        }
        .ev-credits-amount {
            font-size: 22px !important;
            margin: 0 !important;
        }
        .ev-buy-btn {
            width: 100%;
            justify-content: center;
            border-radius: 25px !important;
            padding: 12px 20px;
        }
        /* Hide payment icons & desktop credits on mobile */
        .ev-payments { display: none !important; }
        .ev-desktop-credits { display: none !important; }

        /* Communication card - hide on mobile (shown as bottom nav) */
        .ev-account-card:has(.ev-comm-list) { display: none !important; }

        /* Newsletter card - hide desktop version, show mobile */
        .ev-account-card:has(.ev-newsletter-text) { display: none !important; }
        .ev-newsletter-mobile {
            display: flex !important;
            align-items: center;
            gap: 12px;
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 5px;
            padding: 16px;
            cursor: pointer;
        }
        .ev-newsletter-mobile-icon {
            width: 44px;
            height: 44px;
            background: #1a2a10;
            border: 1px solid #3a4a2a;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .ev-newsletter-mobile-info { flex: 1; }
        .ev-newsletter-mobile-info .ev-nl-title {
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            margin: 0;
        }
        .ev-newsletter-mobile-info .ev-nl-sub {
            color: #888;
            font-size: 13px;
            margin: 0;
        }
        .ev-newsletter-mobile-arrow {
            color: #888;
            font-size: 20px;
            flex-shrink: 0;
        }

        /* Delete account - full width card style */
        .ev-delete-link {
            width: 100%;
            justify-content: center;
            padding: 14px;
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 5px;
            font-size: 14px;
            color: #dc3545 !important;
        }
        div:has(> form > .ev-delete-link) {
            text-align: center !important;
            margin-top: 12px !important;
        }

        /* Mobile bottom nav */
        .ev-mobile-bottom-nav {
            display: flex !important;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: #0a0a0a;
            border-top: 1px solid #2a2a2a;
            padding: 10px 12px;
            padding-bottom: max(10px, env(safe-area-inset-bottom));
            gap: 8px;
            align-items: center;
            justify-content: flex-start;
        }
        .ev-mobile-bottom-nav a {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #888;
            text-decoration: none;
            font-size: 11px;
            padding: 7px 10px;
            border-radius: 5px;
            white-space: nowrap;
        }
        .ev-mobile-bottom-nav a i { font-size: 12px; }
        .ev-mobile-bottom-nav a.ev-nav-active {
            background: #C1F11D;
            color: #000;
            font-weight: 600;
        }
        .ev-mobile-bottom-nav a:not(.ev-nav-active):hover {
            color: #fff;
        }

        /* Container spacing */
        .ev-container { padding-bottom: 0px !important; padding-top: 0 !important; }

        /* Alert */
        .ev-alert-success { border-radius: 5px; }

        /* Newsletter modal mobile */
        .ev-modal-overlay { align-items: center; padding: 1rem; }
        .ev-modal { border-radius: 5px; max-width: 100%; }
        .ev-modal-header { padding: 14px 16px; }
        .ev-modal-header h2 { font-size: 16px; }
        .ev-modal-body { padding: 16px; }
        .ev-modal-footer { padding: 12px 16px; }
        .ev-search-input { border-radius: 5px; border-color: #333; background: #1a1a1a; }
        .ev-search-input input { font-size: 14px; }
        .ev-city-tag { border-radius: 5px; border-color: #333; background: #1a1a1a; }
        .ev-dropdown-results { border-radius: 5px; }
        .ev-modal-close { border-radius: 5px; }
    }

    /* ============================================================
       NEW DESKTOP MY-ACCOUNT DESIGN (desktop only; mobile section
       `.ev-account-mobile` above handles small screens unchanged).
       All classes scoped under `.acct-*` so they can't collide
       with the legacy `.ev-*` rules.
       ============================================================ */
    .acct-page-desktop {
        background: #000;
        min-height: 100vh;
        padding-bottom: 60px;
        color: #fff;
    }
    .acct-container {
        max-width: 1337px;
        margin: 0 auto;
        padding: 0 16px;
    }
    .acct-back-wrap {
        background: #131616;
        padding: 14px 0;
        margin-bottom: 20px;
    }
    .acct-back-inner {
        max-width: 1337px;
        margin: 0 auto;
        padding: 0 16px;
    }
    .acct-back-link {
        color: #C1F11D;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
    .acct-back-link:hover { color: #d9ff4a; }

    .acct-title {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 14px 0 4px;
    }
    .acct-subtitle {
        color: rgba(255,255,255,0.5);
        font-size: 14px;
        margin: 0 0 22px;
    }

    /* -------- Hero premium banner -------- */
    .acct-hero {
        position: relative;
        border-radius: 5px;
        padding: 26px 30px;
        margin-bottom: 22px;
        overflow: hidden;
        background: #0d131a url('https://assets.evoory.com/assets/images/banner.png') right center / auto 100% no-repeat;
        border: 1px solid rgba(255,255,255,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        min-height: 130px;
    }
    .acct-hero-content { flex: 1 1 auto; min-width: 0; z-index: 1; }
    .acct-hero-label {
        font-size: 11px;
        letter-spacing: 1.5px;
        color: #22d3ee;
        font-weight: 700;
        text-transform: uppercase;
    }
    .acct-hero-title {
        font-size: 26px;
        font-weight: 700;
        color: #fff;
        margin: 6px 0 8px;
        line-height: 1.2;
    }
    .acct-hero-desc {
        color: rgba(255,255,255,0.62);
        font-size: 13px;
        max-width: 520px;
        line-height: 1.55;
        margin: 0;
    }
    .acct-hero-btn {
        flex: 0 0 auto;
        background: #C1F11D;
        color: #0a0a0a;
        border: none;
        padding: 12px 22px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
        z-index: 1;
    }
    .acct-hero-btn:hover { background: #d1ff2d; color: #0a0a0a; }

    /* -------- Tabs -------- */
    .acct-tabs {
        display: flex;
        gap: 10px;
    }
    .acct-tabs a {
        flex: 1 1 0;
        padding: 10px 16px;
        background: #131616;
        color: #fff;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 42px;
        border: 1px solid transparent;
        transition: background 120ms, color 120ms;
    }
    .acct-tabs a:hover { color: #fff; background: #1a1f1f; }
    .acct-tabs a.active {
        background: rgba(193,241,29,0.08);
        color: #C1F11D;
        border-color: rgba(193,241,29,0.3);
    }

    .acct-flash {
        background: rgba(34,197,94,0.1);
        color: #22c55e;
        border: 1px solid rgba(34,197,94,0.25);
        padding: 10px 14px;
        border-radius: 5px;
        margin-bottom: 16px;
        font-size: 13px;
    }

    /* -------- Grid layout -------- */
    .acct-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .acct-col { display: flex; flex-direction: column; gap: 16px; }

    /* Card base */
    .acct-card {
        background: #1D2224;
        border: 1px solid #252525;
        border-radius: 5px;
        padding: 20px;
    }

    /* Profile card */
    .acct-profile-card {
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
    }
    .acct-profile-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid rgba(255,255,255,0.08);
    }
    .acct-profile-info { flex: 1; min-width: 0; }
    .acct-profile-info h3 {
        color: #C1F11D;
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 6px;
    }
    .acct-profile-info p {
        color: rgba(255,255,255,0.55);
        font-size: 13px;
        margin: 2px 0;
    }
    .acct-profile-info p strong { color: #fff; font-weight: 600; margin-right: 4px; }
    .acct-profile-edit {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 13px;
    }
    .acct-profile-edit:hover { background: rgba(255,255,255,0.1); color: #fff; }

    /* Account info section */
    .acct-info-card { padding: 0; overflow: hidden; background: #1D2224; }
    .acct-info-card .acct-card-title {
        background: #262C2F;
        padding: 9px 20px;
        margin: 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .acct-info-card .acct-info-grid { padding: 18px 20px 20px; }
    .acct-card-title {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .acct-card-title i { color: rgba(255,255,255,0.6); font-size: 13px; }
    .acct-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 20px;
    }
    .acct-info-item { display: flex; align-items: flex-start; gap: 10px; }
    .acct-info-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: rgba(255,255,255,0.04);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.55);
        font-size: 12px;
        flex-shrink: 0;
    }
    .acct-info-label {
        color: rgba(255,255,255,0.45);
        font-size: 11px;
        font-weight: 500;
        margin: 0 0 2px;
    }
    .acct-info-value {
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        margin: 0;
        word-break: break-word;
    }

    /* Premium / plan card */
    .acct-premium-card { padding: 0; overflow: hidden; background: #131616; }
    .acct-premium-card .acct-premium-head {
        background: #1D2224;
        padding: 14px 20px;
        margin: 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .acct-premium-card .acct-quota-row,
    .acct-premium-card .acct-progress,
    .acct-premium-card .acct-premium-desc { margin-left: 20px; margin-right: 20px; }
    .acct-premium-card .acct-quota-row { margin-top: 18px; }
    .acct-premium-card .acct-premium-desc { margin-bottom: 20px; }
    .acct-premium-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 16px;
    }
    .acct-premium-head-title {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        font-size: 14px;
        font-weight: 400;
    }
    .acct-premium-head-title svg { display: block; }
    .acct-free-badge {
        color: #C1F11D;
        background: transparent;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 999px;
        border: 1.5px solid #C1F11D;
    }
    .acct-quota-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        color: rgba(255,255,255,0.65);
        font-size: 13px;
        margin-bottom: 8px;
    }
    .acct-quota-row b { color: #fff; font-weight: 600; }
    .acct-progress {
        height: 6px;
        background: rgba(255,255,255,0.08);
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .acct-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #ec4899, #a855f7);
        border-radius: 999px;
        transition: width 300ms ease;
    }
    .acct-premium-desc {
        color: rgba(255,255,255,0.5);
        font-size: 12px;
        margin: 0;
        line-height: 1.5;
    }

    /* Stay Connected card */
    .acct-connect-card {
        position: relative;
        overflow: hidden;
        min-height: 180px;
        padding: 22px;
        background: linear-gradient(269.67deg, #1D2224 0.25%, rgba(111, 130, 138, 0) 99.69%);
    }
    .acct-connect-content {
        position: relative;
        z-index: 1;
        max-width: 60%;
    }
    .acct-connect-head {
        color: #fff;
        font-size: 17px;
        font-weight: 600;
        margin: 0 0 2px;
    }
    .acct-connect-head b { color: #C1F11D; font-weight: 700; }
    .acct-connect-divider {
        height: 2px;
        width: 32px;
        background: #a855f7;
        border-radius: 2px;
        margin: 6px 0 6px;
    }
    .acct-connect-nl {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0px;
        margin: -8px 0 0;
        line-height: 2.8;
    }
    .acct-connect-nl svg {
        display: block;
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        margin: -18px -6px -18px -14px;
    }
    .acct-connect-desc {
        color: rgba(255,255,255,0.55);
        font-size: 12px;
        margin: 0 0 12px;
        line-height: 1.5;
        margin-top: 0;
    }
    .acct-connect-edit {
        color: #C1F11D;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        font-family: inherit;
    }
    .acct-connect-edit:hover { color: #d1ff2d; }
    .acct-connect-image {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 55%;
        background: url('https://assets.evoory.com/assets/images/single.png') right center / cover no-repeat;
        pointer-events: none;
        -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 35%);
                mask-image: linear-gradient(to right, transparent 0%, #000 35%);
    }

    /* Delete button */
    .acct-delete-wrap {
        margin: 50px 0 0;
        width: 100%;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
    }
    .acct-delete-wrap form {
        display: block;
        width: 50%;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .acct-delete-btn {
        background: #131616;
        border: 1px solid rgba(255,255,255,0.08);
        color: #F24E1E;
        padding: 12px 28px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        text-align: center;
        box-sizing: border-box;
        font-family: inherit;
        transition: background 120ms, border-color 120ms;
    }
    .acct-delete-btn svg { display: block; }
    .acct-delete-btn svg path { fill: #F24E1E; }
    .acct-delete-btn:hover { background: #1a1d1d; border-color: rgba(242,78,30,0.4); }

    /* Centered title inside the back-wrap strip, mobile detail view only. */
    .acct-back-title { display: none; }

    /* Responsive */
    @media (max-width: 900px) {
        /* By default the "desktop" new design is hidden on mobile — the
           .ev-account-mobile dashboard above handles that viewport.
           EXCEPTION: when the user has drilled into /my-account/info (via
           dashboard "My Account" row), show the detail view on mobile too
           with a mobile-optimized layout. */
        .acct-page-desktop:not(.acct-page-info) { display: none !important; }

        /* Compact top nav: "< Home" on left, "My Account" centered. */
        .acct-page-info .acct-back-wrap { margin-bottom: 14px; }
        .acct-page-info .acct-back-inner {
            padding: 0 14px;
            display: flex;
            align-items: center;
            position: relative;
            min-height: 24px;
        }

        /* Tabs on mobile: dark pill card with border + bigger radius. */
        .acct-page-info .acct-tabs a {
            background: #1A1A1A;
            border: 1px solid #364153;
            border-radius: 12px;
        }
        .acct-page-info .acct-tabs a.active {
            background: rgba(193,241,29,0.08);
            border-color: rgba(193,241,29,0.5);
        }
        .acct-page-info .acct-back-title {
            display: block;
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
        }
        .acct-page-info .acct-container { padding: 0 14px; }
        /* Hide the large H1 + subtitle on mobile — the centered back-wrap
           title already serves as the page heading. */
        .acct-page-info .acct-title,
        .acct-page-info .acct-subtitle { display: none; }

        /* Reorder: nav → tabs → hero → profile → info → newsletter → delete.
           Done with flex order after flattening .acct-grid/.acct-col via
           display:contents so inner cards become siblings and can be
           ordered individually. */
        .acct-page-info .acct-container { display: flex; flex-direction: column; }
        .acct-page-info .acct-grid { display: contents; }
        .acct-page-info .acct-col { display: contents; }

        .acct-page-info .acct-flash { order: 1; }
        .acct-page-info .acct-tabs { order: 2; margin-bottom: 4px; }
        .acct-page-info .acct-hero { order: 3; }
        .acct-page-info .acct-profile-card { display: none; }
        .acct-page-info .acct-info-card { order: 5; }
        .acct-page-info .acct-connect-card { order: 6; }
        .acct-page-info .acct-premium-card { order: 7; }
        .acct-page-info .acct-delete-wrap { order: 8; margin-top: 20px; }

        /* Hero — stack and shrink on mobile. Keep button visible to the right
           of text if it fits, otherwise it wraps naturally via flex-wrap. */
        .acct-page-info .acct-hero {
            padding: 16px 16px;
            min-height: 110px;
            flex-wrap: wrap;
            gap: 12px;
            background-position: right -40px center;
        }
        .acct-page-info .acct-hero-title { font-size: 18px; }
        .acct-page-info .acct-hero-desc { font-size: 11px; line-height: 1.4; }
        .acct-page-info .acct-hero-btn { padding: 9px 16px; font-size: 12px; }

        /* Tabs — compact row of 3 on mobile. */
        .acct-page-info .acct-tabs a {
            padding: 8px 10px;
            height: 38px;
            font-size: 12px;
        }
        .acct-page-info .acct-tabs a svg { width: 15px; height: 15px; }

        /* Stack grid columns on mobile. */
        .acct-page-info .acct-grid { grid-template-columns: 1fr; gap: 12px; }

        /* Profile card — tighter spacing. */
        .acct-page-info .acct-profile-card { padding: 14px; }
        .acct-page-info .acct-profile-avatar { width: 48px; height: 48px; }
        .acct-page-info .acct-profile-info h3 { font-size: 15px; }
        .acct-page-info .acct-profile-info p { font-size: 12px; }
        .acct-page-info .acct-profile-edit { top: 12px; right: 12px; width: 28px; height: 28px; }

        /* Account Information — keep 2 columns on mobile but tighter. */
        .acct-page-info .acct-info-card .acct-card-title { padding: 12px 14px; font-size: 13px; }
        .acct-page-info .acct-info-card .acct-info-grid { padding: 14px; gap: 14px 12px; }
        .acct-page-info .acct-info-icon { width: 26px; height: 26px; }
        .acct-page-info .acct-info-label { font-size: 10px; }
        .acct-page-info .acct-info-value { font-size: 12px; }

        /* Premium / Upgrade card — hide on mobile (the design doesn't show it). */
        .acct-page-info .acct-premium-card { display: none; }

        /* Stay Connected card — shorter on mobile. */
        .acct-page-info .acct-connect-card { min-height: 160px; padding: 16px; }
        .acct-page-info .acct-connect-head { font-size: 16px; }
        .acct-page-info .acct-connect-desc { font-size: 11px; max-width: 65%; }
        .acct-page-info .acct-connect-nl { font-size: 13px; }

        /* Delete button — full width on mobile. */
        .acct-page-info .acct-delete-wrap form { width: 100%; }
        .acct-page-info .acct-delete-btn { padding: 13px 20px; font-size: 13px; }
    }
    @media (max-width: 1024px) and (min-width: 901px) {
        .acct-grid { grid-template-columns: 1fr; }
        .acct-hero { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

<div>
    {{-- Mobile-only account dashboard (matches reference design). --}}
    @php
        $u = auth()->user();
        // First profile to deep-link "My Profiles" into the active-profiles
        // dashboard. Falls back to the archived listing when the user has
        // no profile yet, since that page is the only one we can land on
        // without a profile id.
        $firstProfile = $u ? $u->profiles->first() : null;
        $myProfilesHref = $firstProfile
            ? route('user.dashboard')
            : url('/archived-profiles');
    @endphp
    @if($u && !request()->is('my-account/info'))
    <div class="ev-account-mobile">
        {{-- Header: Back link + centered logo --}}
        <div class="ev-am-header">
            <a href="{{ url('/') }}" wire:navigate class="ev-am-back" aria-label="Back">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Back</span>
            </a>
            <a href="{{ url('/') }}" wire:navigate class="ev-am-logo">
                @if(isset($setting) && $setting->app_logo)
                    <img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}">
                @else
                    <span>{{ $setting->app_name ?? 'evoory' }}</span>
                @endif
            </a>
            <div class="ev-am-header-spacer"></div>
        </div>

        {{-- Flash messages (e.g. "Welcome back" after Google login).
             Rendered here at the top of the mobile screen instead of the
             default location further down inside .ev-container. --}}
        @if(session('success'))
            <div class="ev-alert-success ev-am-alert">{{ session('success') }}</div>
        @endif

        {{-- User identity --}}
        <div class="ev-am-user">
            <div class="ev-am-avatar">
                @if(!empty($u->avatar))
                    {{-- Routes through /u/{id}/avatar so the request hits our
                         serve controller — which reads directly from
                         storage/app/public/... and works even when public/storage
                         isn't a symlink (this host) or the value is a full URL
                         (Google OAuth users). --}}
                    <img src="{{ user_avatar_url($u) }}" alt="{{ $u->name }}" onerror="this.style.display='none'">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                @endif
            </div>
            <h2 class="ev-am-name">{{ $u->name ?? 'Account' }}</h2>
            <p class="ev-am-email">{{ $u->email }}</p>
        </div>

        {{-- Menu list --}}
        <nav class="ev-am-menu">
            <a href="/my-account/info" wire:navigate class="ev-am-item">
                <span class="ev-am-item__icon">
<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.75 14.75C0.75 13.6891 1.17143 12.6717 1.92157 11.9216C2.67172 11.1714 3.68913 10.75 4.75 10.75H12.75C13.8109 10.75 14.8283 11.1714 15.5784 11.9216C16.3286 12.6717 16.75 13.6891 16.75 14.75C16.75 15.2804 16.5393 15.7891 16.1642 16.1642C15.7891 16.5393 15.2804 16.75 14.75 16.75H2.75C2.21957 16.75 1.71086 16.5393 1.33579 16.1642C0.960714 15.7891 0.75 15.2804 0.75 14.75Z" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
<path d="M8.75 6.75C10.4069 6.75 11.75 5.40685 11.75 3.75C11.75 2.09315 10.4069 0.75 8.75 0.75C7.09315 0.75 5.75 2.09315 5.75 3.75C5.75 5.40685 7.09315 6.75 8.75 6.75Z" stroke="white" stroke-width="1.5"/>
</svg>
                </span>
                <span class="ev-am-item__label">My Account</span>
                <span class="ev-am-item__chev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </a>

            <a href="{{ $myProfilesHref }}" wire:navigate class="ev-am-item">
                <span class="ev-am-item__icon">
<svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0 0.514284C0 0.377888 0.052704 0.247077 0.146518 0.15063C0.240331 0.0541833 0.36757 0 0.500243 0H15.174C15.3067 0 15.4339 0.0541833 15.5278 0.15063C15.6216 0.247077 15.6743 0.377888 15.6743 0.514284C15.6743 0.650681 15.6216 0.781491 15.5278 0.877938C15.4339 0.974385 15.3067 1.02857 15.174 1.02857H0.500243C0.36757 1.02857 0.240331 0.974385 0.146518 0.877938C0.052704 0.781491 0 0.650681 0 0.514284ZM0.500243 6.51427H5.16918C5.30185 6.51427 5.42909 6.46009 5.5229 6.36364C5.61672 6.26719 5.66942 6.13638 5.66942 5.99999C5.66942 5.86359 5.61672 5.73278 5.5229 5.63633C5.42909 5.53988 5.30185 5.4857 5.16918 5.4857H0.500243C0.36757 5.4857 0.240331 5.53988 0.146518 5.63633C0.052704 5.73278 0 5.86359 0 5.99999C0 6.13638 0.052704 6.26719 0.146518 6.36364C0.240331 6.46009 0.36757 6.51427 0.500243 6.51427ZM6.50316 10.9714H0.500243C0.36757 10.9714 0.240331 11.0256 0.146518 11.122C0.052704 11.2185 0 11.3493 0 11.4857C0 11.6221 0.052704 11.7529 0.146518 11.8493C0.240331 11.9458 0.36757 12 0.500243 12H6.50316C6.63583 12 6.76307 11.9458 6.85688 11.8493C6.9507 11.7529 7.0034 11.6221 7.0034 11.4857C7.0034 11.3493 6.9507 11.2185 6.85688 11.122C6.76307 11.0256 6.63583 10.9714 6.50316 10.9714ZM16.8265 7.22055L14.8681 8.88255L15.465 11.3623C15.4887 11.4605 15.4836 11.5638 15.4503 11.6591C15.4171 11.7545 15.3573 11.8376 15.2783 11.8981C15.1993 11.9586 15.1048 11.9938 15.0064 11.9992C14.9081 12.0047 14.8103 11.9802 14.7255 11.9288L12.5061 10.5857L10.2867 11.9288C10.2018 11.9802 10.1041 12.0047 10.0057 11.9992C9.90739 11.9938 9.81281 11.9586 9.73384 11.8981C9.65487 11.8376 9.59502 11.7545 9.56181 11.6591C9.5286 11.5638 9.52349 11.4605 9.54714 11.3623L10.1441 8.88255L8.18564 7.22055C8.10872 7.15528 8.05252 7.06786 8.02428 6.96952C7.99604 6.87117 7.99704 6.7664 8.02715 6.66864C8.05727 6.57088 8.11512 6.48461 8.19327 6.42091C8.27143 6.3572 8.36633 6.31897 8.46578 6.31113L11.0504 6.10541L12.0433 3.73885C12.0823 3.64709 12.1464 3.569 12.2278 3.51416C12.3093 3.45931 12.4045 3.4301 12.5019 3.4301C12.5993 3.4301 12.6945 3.45931 12.776 3.51416C12.8574 3.569 12.9215 3.64709 12.9605 3.73885L13.9534 6.10541L16.538 6.31113C16.6375 6.31897 16.7324 6.3572 16.8105 6.42091C16.8887 6.48461 16.9465 6.57088 16.9767 6.66864C17.0068 6.7664 17.0078 6.87117 16.9795 6.96952C16.9513 7.06786 16.8951 7.15528 16.8182 7.22055H16.8265ZM15.2341 7.23855L13.5808 7.10741C13.4905 7.09945 13.4041 7.06647 13.3307 7.01197C13.2573 6.95748 13.1996 6.88352 13.1639 6.79798L12.5061 5.24056L11.8524 6.79798C11.8167 6.88352 11.759 6.95748 11.6856 7.01197C11.6122 7.06647 11.5258 7.09945 11.4356 7.10741L9.78225 7.23855L11.0279 8.29541C11.1005 8.35717 11.1547 8.43877 11.1842 8.53083C11.2137 8.62288 11.2173 8.72163 11.1946 8.81569L10.8086 10.422L12.256 9.54598C12.3328 9.49955 12.4203 9.47508 12.5094 9.47508C12.5985 9.47508 12.686 9.49955 12.7629 9.54598L14.2111 10.422L13.825 8.81569C13.8023 8.72163 13.8059 8.62288 13.8354 8.53083C13.8649 8.43877 13.9191 8.35717 13.9918 8.29541L15.2341 7.23855Z" fill="white"/>
</svg>
                </span>
                <span class="ev-am-item__label">My Profiles</span>
                <span class="ev-am-item__badge">{{ $profilesCount ?? 0 }}</span>
                <span class="ev-am-item__chev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </a>

            <a href="{{ url('/purchase-credits') }}" wire:navigate class="ev-am-item">
                <span class="ev-am-item__icon">
<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12.646 0.000141433C12.5406 0.00285917 12.4357 0.0169406 12.3333 0.0421412L1.5 2.89545C1.07179 3.00882 0.692843 3.26006 0.421718 3.61035C0.150592 3.96065 0.00238477 4.39048 0 4.83343V14C0 15.0967 0.903333 16 2 16H14C15.0967 16 16 15.0967 16 14V5.33343C16 4.23677 15.0967 3.33345 14 3.33345H5.08333L12.6667 1.33346V2.66678H14V1.33346C14 0.58347 13.362 -0.0105251 12.646 0.000141433ZM2 4.66677H14C14.3773 4.66677 14.6667 4.9561 14.6667 5.33343V14C14.6667 14.3773 14.3773 14.6667 14 14.6667H2C1.62267 14.6667 1.33333 14.3773 1.33333 14V5.33343C1.33333 4.9561 1.62267 4.66677 2 4.66677ZM12.3333 8.66673C12.0681 8.66673 11.8138 8.77209 11.6262 8.95962C11.4387 9.14716 11.3333 9.40151 11.3333 9.66672C11.3333 9.93194 11.4387 10.1863 11.6262 10.3738C11.8138 10.5614 12.0681 10.6667 12.3333 10.6667C12.5985 10.6667 12.8529 10.5614 13.0404 10.3738C13.228 10.1863 13.3333 9.93194 13.3333 9.66672C13.3333 9.40151 13.228 9.14716 13.0404 8.95962C12.8529 8.77209 12.5985 8.66673 12.3333 8.66673Z" fill="white"/>
</svg>
                </span>
                <span class="ev-am-item__label">My Wallet</span>
                <span class="ev-am-item__badge ev-am-item__badge--money">${{ number_format($walletBalance ?? 0) }}</span>
                <span class="ev-am-item__chev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </a>

            <a href="{{ url('/my-chat') }}" wire:navigate class="ev-am-item">
                <span class="ev-am-item__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </span>
                <span class="ev-am-item__label">My chats</span>
                <span class="ev-am-item__badge">{{ $chatsCount ?? 0 }}</span>
                <span class="ev-am-item__chev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </a>

            <a href="{{ url('/my-favorites') }}" wire:navigate class="ev-am-item">
                <span class="ev-am-item__icon">
<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M8.25 15.75C12.3923 15.75 15.75 12.3923 15.75 8.25C15.75 4.10775 12.3923 0.75 8.25 0.75C4.10775 0.75 0.75 4.10775 0.75 8.25C0.75 9.45 1.032 10.584 1.53225 11.5898C1.66575 11.8568 1.71 12.162 1.63275 12.4508L1.1865 14.1203C1.1423 14.2855 1.14234 14.4594 1.18662 14.6246C1.23089 14.7898 1.31784 14.9405 1.43874 15.0614C1.55963 15.1824 1.71022 15.2694 1.8754 15.3138C2.04057 15.3582 2.2145 15.3583 2.37975 15.3143L4.04925 14.8673C4.33904 14.794 4.6456 14.8295 4.911 14.967C5.94821 15.4834 7.09134 15.7515 8.25 15.75Z" stroke="white" stroke-width="1.5"/>
<path d="M7.19727 10.7275C7.33508 10.8377 7.45439 10.9264 7.56445 10.9922C7.62823 11.0303 7.68992 11.0605 7.75 11.0859V11.2178C7.53885 11.1381 7.34301 11.007 7.11523 10.8242L7.19727 10.7275ZM9.38477 10.8242C9.15716 11.0069 8.9613 11.1381 8.75 11.2178V11.0859C8.8099 11.0606 8.87191 11.0311 8.93555 10.9932C9.04582 10.9274 9.16464 10.8378 9.30273 10.7275L9.38477 10.8242ZM11.6143 8.08203C11.5323 8.38105 11.3864 8.67815 11.1982 8.96387L11.0674 9.15234C10.8019 9.51564 10.4749 9.86023 10.1416 10.1709L10.0605 10.0762C10.3319 9.82302 10.598 9.54841 10.8281 9.25977L10.9668 9.07812C11.2041 8.75253 11.3871 8.41583 11.4844 8.08203H11.6143ZM5.01562 8.08105C5.11293 8.41492 5.29582 8.75149 5.5332 9.07715V9.07812C5.79175 9.4316 6.11065 9.76937 6.43848 10.0752L6.35742 10.1699C6.02419 9.85916 5.6978 9.51476 5.43262 9.15137V9.15039C5.18275 8.80823 4.98591 8.44638 4.88574 8.08105H5.01562ZM9.07129 5.69531C9.58407 5.45029 10.0946 5.42334 10.5293 5.58594C11.0946 5.79763 11.5167 6.32481 11.6455 7.08203H11.5195C11.4024 6.42534 11.0538 5.96695 10.5986 5.75098L10.4844 5.70312C10.0991 5.55907 9.63774 5.57451 9.16406 5.79004L9.07129 5.69531ZM5.9707 5.58594C6.405 5.42347 6.91518 5.45063 7.42773 5.69531L7.33496 5.79004C6.86173 5.5751 6.40123 5.55953 6.01562 5.70312H6.01367C5.50611 5.894 5.10748 6.37033 4.98047 7.08105H4.85449C4.98336 6.3246 5.40546 5.7976 5.9707 5.58594Z" fill="black" stroke="white"/>
</svg>
                </span>
                <span class="ev-am-item__label">Favorites</span>
                <span class="ev-am-item__badge">{{ $favoritesCount ?? 0 }}</span>
                <span class="ev-am-item__chev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </a>
        </nav>

        {{-- Support card (mirrors the help page) --}}
        <div class="ev-am-help">
            <div class="ev-am-help__icon">
<svg width="29" height="26" viewBox="0 0 29 26" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M14.1294 26V24.375H25.7778V12.2752C25.7778 9.23108 24.6683 6.68904 22.4492 4.64913C20.2291 2.60812 17.5794 1.58763 14.5 1.58763C11.4206 1.58763 8.77089 2.60812 6.55078 4.64913C4.33067 6.69012 3.22115 9.23217 3.22222 12.2752V21.5312H0V13.884H1.61111L1.6385 11.8869C1.6675 10.1915 2.03269 8.62062 2.73406 7.17438C3.43543 5.72813 4.37202 4.46983 5.54383 3.3995C6.71565 2.32917 8.07113 1.495 9.61028 0.897C11.1494 0.299 12.7793 0 14.5 0C16.2207 0 17.8495 0.299 19.3865 0.897C20.9235 1.495 22.2731 2.32808 23.4352 3.39625C24.5974 4.46442 25.5291 5.72108 26.2305 7.16625C26.9319 8.61142 27.3089 10.1823 27.3615 11.8788L27.3889 13.884H29V21.5312H27.3889V26H14.1294ZM9.41695 15.158C9.16991 14.9305 9.04639 14.6488 9.04639 14.313C9.04639 13.9772 9.16991 13.6901 9.41695 13.4517C9.66398 13.2134 9.95398 13.0942 10.2869 13.0942C10.6199 13.0942 10.9094 13.2134 11.1553 13.4517C11.4013 13.6901 11.5248 13.9772 11.5259 14.313C11.527 14.6488 11.4034 14.9305 11.1553 15.158C10.9072 15.3855 10.6172 15.4993 10.2853 15.4993C9.95344 15.4993 9.66398 15.3855 9.41695 15.158ZM17.8447 15.158C17.5976 14.9305 17.4741 14.6488 17.4741 14.313C17.4741 13.9772 17.5976 13.6901 17.8447 13.4517C18.0917 13.2134 18.3817 13.0942 18.7147 13.0942C19.0476 13.0942 19.3371 13.2134 19.5831 13.4517C19.829 13.6901 19.9525 13.9772 19.9536 14.313C19.9547 14.6488 19.8312 14.9305 19.5831 15.158C19.3349 15.3855 19.0449 15.4993 18.7131 15.4993C18.3812 15.4993 18.0917 15.3855 17.8447 15.158ZM5.9885 12.9187C5.84243 10.4856 6.61952 8.41154 8.31978 6.69663C10.019 4.98171 12.1059 4.12425 14.5806 4.12425C16.66 4.12425 18.5031 4.75421 20.1099 6.01413C21.7167 7.27404 22.6973 8.92883 23.0518 10.9785C20.9176 10.9514 18.937 10.4022 17.11 9.33075C15.283 8.25933 13.8813 6.76975 12.905 4.862C12.5162 6.73725 11.7079 8.38067 10.4803 9.79225C9.25154 11.2038 7.75428 12.246 5.9885 12.9187Z" fill="white"/>
</svg>



            </div>
            <h3 class="ev-am-help__title">We're Here to Help</h3>
            <p class="ev-am-help__sub">Our support team is ready to assist you 24/7</p>

            <a href="tel:+15169005003" class="ev-am-help__call">
                <span class="ev-am-help__call-row">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>+1 516 900 5003</span>
                </span>
                <span class="ev-am-help__call-sub">Call us to get in touch</span>
            </a>

            <div class="ev-am-help__chats">
                <a href="https://wa.me/15169005003" target="_blank" rel="noopener" class="ev-am-help__chat">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#25D366" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                    <span>WhatsApp</span>
                </a>
                <a href="https://t.me/evoory" target="_blank" rel="noopener" class="ev-am-help__chat">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#229ED9" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0m5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.022c.243-.213-.054-.334-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.566-4.458c.538-.196 1.006.128.832.938"/></svg>
                    <span>Telegram</span>
                </a>
            </div>

            <div class="ev-am-help__divider"><span>or</span></div>

            <a href="mailto:support@evoory.com" class="ev-am-help__email">
                <span class="ev-am-help__email-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <span class="ev-am-help__email-text">
                    <span class="ev-am-help__email-label">Email us at</span>
                    <span class="ev-am-help__email-addr">support@evoory.com</span>
                </span>
            </a>

            <p class="ev-am-help__footer">Your satisfaction is our priority</p>
        </div>

        {{-- Sign-out — uses the user-facing /sign_out route which calls
             Auth::logout() and redirects to /sign-in. The named `logout`
             route lives in the admin group and would send users to the
             admin login screen instead. --}}
        <form action="{{ url('/sign_out') }}" method="POST" class="ev-am-signout-form">
            @csrf
            <button type="submit" class="ev-am-signout">
                <span class="ev-am-signout__left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Sign out</span>
                </span>
                <span class="ev-am-signout__right">SECURE EXIT</span>
            </button>
        </form>
    </div>
    @endif

    {{-- Main content --}}
    {{-- =====================================================
         NEW DESKTOP MY-ACCOUNT (hidden on mobile; the
         .ev-account-mobile block above handles mobile).
         ===================================================== --}}
    @php $isAcctInfoPage = request()->is('my-account/info'); @endphp
    <div class="acct-page-desktop {{ $isAcctInfoPage ? 'acct-page-info' : '' }}" x-data="{ newsletterOpen: false }">
        <div class="acct-back-wrap">
            <div class="acct-back-inner">
                <a href="{{ $isAcctInfoPage ? url('/my-account') : url('/') }}" wire:navigate class="acct-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    {{ $isAcctInfoPage ? 'Home' : 'Back' }}
                </a>
                {{-- Centered page title shown only on mobile detail view. --}}
                <span class="acct-back-title">My Account</span>
            </div>
        </div>
        <div class="acct-container">
            <h1 class="acct-title">My Account</h1>
            <p class="acct-subtitle">Manage your profile, credits and account settings.</p>

            {{-- Hero / Premium banner --}}
            <div class="acct-hero">
                <div class="acct-hero-content">
                    <div class="acct-hero-label">EXCLUSIVE ACCESS</div>
                    <h2 class="acct-hero-title">Your Connection Matters</h2>
                    <p class="acct-hero-desc">Upgrade to Premium to unlock unlimited messaging and connect without boundaries.</p>
                </div>
                <a href="{{ url('/premium-account') }}" wire:navigate class="acct-hero-btn">
                    <i class="fa fa-crown"></i> Upgrade to Premium
                </a>
            </div>

            @if(session('success'))
                <div class="acct-flash">{{ session('success') }}</div>
            @endif

            {{-- Two-column grid: tabs + profile + info on left, premium + newsletter on right --}}
            @php
                $authUser = auth()->user();
                $accountAvatarUrl = !empty($authUser->avatar) && \Illuminate\Support\Facades\Storage::disk('public')->exists($authUser->avatar)
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($authUser->avatar)
                    : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($authUser->email))) . '?s=128&d=identicon';
                $accountTypeLabel = ['1' => 'Standard', '2' => 'Individual', '3' => 'Agency'][$authUser->type] ?? 'Standard';
                $memberSince = $authUser->created_at ? $authUser->created_at->format('m/d/Y') : '—';
                $usernameDisplay = $authUser->username ? '@' . $authUser->username : '—';
                $phoneDisplay = $authUser->phone ?: '—';
                $quotaMax = 5;
                $quotaUsed = min($chatsCount ?? 0, $quotaMax);
                $quotaPct = ($quotaUsed / $quotaMax) * 100;
                $quotaRemaining = max(0, $quotaMax - $quotaUsed);
            @endphp

            <div class="acct-grid">
                {{-- Left column --}}
                <div class="acct-col">
                    {{-- Tabs sit inside left column so they align horizontally with
                         the Upgrade to Premium card header on the right. --}}
                    <div class="acct-tabs">
                        <a href="/my-account" wire:navigate class="{{ request()->is('my-account') && !request()->is('my-account/*') ? 'active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M1 15C1 13.9391 1.42143 12.9217 2.17157 12.1716C2.92172 11.4214 3.93913 11 5 11H13C14.0609 11 15.0783 11.4214 15.8284 12.1716C16.5786 12.9217 17 13.9391 17 15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17H3C2.46957 17 1.96086 16.7893 1.58579 16.4142C1.21071 16.0391 1 15.5304 1 15Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M9 7C10.6569 7 12 5.65685 12 4C12 2.34315 10.6569 1 9 1C7.34315 1 6 2.34315 6 4C6 5.65685 7.34315 7 9 7Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Account
                        </a>
                        <a href="/my-account/edit" wire:navigate class="{{ request()->is('my-account/edit') ? 'active' : '' }}">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M4 5H3C2.46957 5 1.96086 5.21071 1.58579 5.58579C1.21071 5.96086 1 6.46957 1 7V16C1 16.5304 1.21071 17.0391 1.58579 17.4142C1.96086 17.7893 2.46957 18 3 18H12C12.5304 18 13.0391 17.7893 13.4142 17.4142C13.7893 17.0391 14 16.5304 14 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13 3.00011L16 6.00011M17.385 4.58511C17.7788 4.19126 18.0001 3.65709 18.0001 3.10011C18.0001 2.54312 17.7788 2.00895 17.385 1.61511C16.9912 1.22126 16.457 1 15.9 1C15.343 1 14.8088 1.22126 14.415 1.61511L6 10.0001V13.0001H9L17.385 4.58511Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Edit
                        </a>
                        <a href="/my-password/edit" wire:navigate class="{{ request()->is('my-password/edit') ? 'active' : '' }}">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M1.49715 14.1093C1.17892 14.4275 1.0001 14.859 1 15.3089V17.1516C1 17.3766 1.08938 17.5924 1.24848 17.7515C1.40759 17.9106 1.62337 18 1.84838 18H4.39351C4.61851 18 4.8343 17.9106 4.9934 17.7515C5.15251 17.5924 5.24189 17.3766 5.24189 17.1516V16.3032C5.24189 16.0782 5.33127 15.8625 5.49037 15.7034C5.64947 15.5442 5.86526 15.4549 6.09027 15.4549H6.93864C7.16365 15.4549 7.37944 15.3655 7.53854 15.2064C7.69764 15.0473 7.78702 14.8315 7.78702 14.6065V13.7581C7.78702 13.5331 7.8764 13.3173 8.03551 13.1582C8.19461 12.9991 8.4104 12.9097 8.6354 12.9097H8.78132C9.23129 12.9096 9.6628 12.7308 9.98093 12.4126L10.6715 11.722C11.8506 12.1327 13.1342 12.1312 14.3123 11.7176C15.4904 11.3039 16.4933 10.5027 17.1568 9.44507C17.8204 8.38738 18.1053 7.13582 17.9651 5.89513C17.8249 4.65443 17.2677 3.49805 16.3848 2.61516C15.502 1.73226 14.3456 1.17513 13.1049 1.0349C11.8642 0.894666 10.6126 1.17964 9.55493 1.84319C8.49725 2.50675 7.69607 3.5096 7.28245 4.6877C6.86883 5.8658 6.86726 7.14939 7.27799 8.3285L1.49715 14.1093Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.3031 6.12279C13.5374 6.12279 13.7273 5.93288 13.7273 5.6986C13.7273 5.46433 13.5374 5.27441 13.3031 5.27441C13.0688 5.27441 12.8789 5.46433 12.8789 5.6986C12.8789 5.93288 13.0688 6.12279 13.3031 6.12279Z" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Password
                        </a>
                    </div>

                    {{-- Profile card --}}
                    <div class="acct-card acct-profile-card">
                        <img src="{{ $accountAvatarUrl }}" alt="{{ $authUser->name }}" class="acct-profile-avatar">
                        <div class="acct-profile-info">
                            <h3>{{ $authUser->name }}</h3>
                            <p><strong>Account</strong> type {{ $accountTypeLabel }}</p>
                            <p><strong>Email</strong> {{ $authUser->email }}</p>
                        </div>
                        <a href="/my-account/edit" wire:navigate class="acct-profile-edit" title="Edit profile">
                            <svg width="16" height="16" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M4 5H3C2.46957 5 1.96086 5.21071 1.58579 5.58579C1.21071 5.96086 1 6.46957 1 7V16C1 16.5304 1.21071 17.0391 1.58579 17.4142C1.96086 17.7893 2.46957 18 3 18H12C12.5304 18 13.0391 17.7893 13.4142 17.4142C13.7893 17.0391 14 16.5304 14 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13 3.00011L16 6.00011M17.385 4.58511C17.7788 4.19126 18.0001 3.65709 18.0001 3.10011C18.0001 2.54312 17.7788 2.00895 17.385 1.61511C16.9912 1.22126 16.457 1 15.9 1C15.343 1 14.8088 1.22126 14.415 1.61511L6 10.0001V13.0001H9L17.385 4.58511Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Account Information card --}}
                    <div class="acct-card acct-info-card">
                        <h4 class="acct-card-title"><i class="fa fa-user-circle"></i> Account Information</h4>
                        <div class="acct-info-grid">
                            <div class="acct-info-item">
                                <span class="acct-info-icon">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M0.75 14.75C0.75 13.6891 1.17143 12.6717 1.92157 11.9216C2.67172 11.1714 3.68913 10.75 4.75 10.75H12.75C13.8109 10.75 14.8283 11.1714 15.5784 11.9216C16.3286 12.6717 16.75 13.6891 16.75 14.75C16.75 15.2804 16.5393 15.7891 16.1642 16.1642C15.7891 16.5393 15.2804 16.75 14.75 16.75H2.75C2.21957 16.75 1.71086 16.5393 1.33579 16.1642C0.960714 15.7891 0.75 15.2804 0.75 14.75Z" stroke="#A6B4B8" stroke-width="1.5" stroke-linejoin="round"/>
                                        <path d="M8.75 6.75C10.4069 6.75 11.75 5.40685 11.75 3.75C11.75 2.09315 10.4069 0.75 8.75 0.75C7.09315 0.75 5.75 2.09315 5.75 3.75C5.75 5.40685 7.09315 6.75 8.75 6.75Z" stroke="#A6B4B8" stroke-width="1.5"/>
                                    </svg>
                                </span>
                                <div>
                                    <div class="acct-info-label">Username</div>
                                    <div class="acct-info-value">{{ $usernameDisplay }}</div>
                                </div>
                            </div>
                            <div class="acct-info-item">
                                <span class="acct-info-icon">
                                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M15.2115 0.75H2.28846C1.43879 0.75 0.75 1.43879 0.75 2.28846V11.5192C0.75 12.3689 1.43879 13.0577 2.28846 13.0577H15.2115C16.0612 13.0577 16.75 12.3689 16.75 11.5192V2.28846C16.75 1.43879 16.0612 0.75 15.2115 0.75Z" stroke="#A6B4B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M3.21094 3.21143L8.7494 7.51912L14.2879 3.21143" stroke="#A6B4B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div>
                                    <div class="acct-info-label">Phone number</div>
                                    <div class="acct-info-value">{{ $phoneDisplay }}</div>
                                </div>
                            </div>
                            <div class="acct-info-item">
                                <span class="acct-info-icon">
                                    <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M0.75 7.55C0.75 4.34465 0.75 2.74155 1.69605 1.7462C2.6421 0.75085 4.16445 0.75 7.21 0.75H11.29C14.3356 0.75 15.8579 0.75 16.8039 1.7462C17.75 2.7424 17.75 4.34465 17.75 7.55C17.75 10.7554 17.75 12.3584 16.8039 13.3538C15.8579 14.3491 14.3356 14.35 11.29 14.35H7.21C4.16445 14.35 2.6421 14.35 1.69605 13.3538C0.75 12.3576 0.75 10.7554 0.75 7.55Z" stroke="#A6B4B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9.67461 11.3751C9.56836 10.0151 8.45911 8.93985 7.07021 8.8489L6.69961 8.8251C6.56814 8.82906 6.44433 8.8336 6.32816 8.8387C4.95201 8.905 3.83086 10.0287 3.72461 11.3751M11.3746 5.0001H14.7746M11.3746 7.9751H14.7746M8.18711 5.2126C8.18711 5.60711 8.03039 5.98546 7.75143 6.26442C7.47247 6.54338 7.09412 6.7001 6.69961 6.7001C6.3051 6.7001 5.92675 6.54338 5.64779 6.26442C5.36883 5.98546 5.21211 5.60711 5.21211 5.2126C5.21211 4.81809 5.36883 4.43974 5.64779 4.16078C5.92675 3.88182 6.3051 3.7251 6.69961 3.7251C7.09412 3.7251 7.47247 3.88182 7.75143 4.16078C8.03039 4.43974 8.18711 4.81809 8.18711 5.2126Z" stroke="#A6B4B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div>
                                    <div class="acct-info-label">Account type</div>
                                    <div class="acct-info-value">{{ $accountTypeLabel }}</div>
                                </div>
                            </div>
                            <div class="acct-info-item">
                                <span class="acct-info-icon">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M13.55 2.3501H3.95C2.18269 2.3501 0.75 3.78279 0.75 5.5501V13.5501C0.75 15.3174 2.18269 16.7501 3.95 16.7501H13.55C15.3173 16.7501 16.75 15.3174 16.75 13.5501V5.5501C16.75 3.78279 15.3173 2.3501 13.55 2.3501Z" stroke="#A6B4B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.55 0.75V3.95M11.95 0.75V3.95M0.75 7.15H16.75" stroke="#A6B4B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div>
                                    <div class="acct-info-label">Member Since</div>
                                    <div class="acct-info-value">{{ $memberSince }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right column --}}
                <div class="acct-col">
                    {{-- Premium / plan card --}}
                    <div class="acct-card acct-premium-card">
                        <div class="acct-premium-head">
                            <span class="acct-premium-head-title">
                                <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4345 4.56809L14.9666 12.1636H3.0304L0.5625 4.56809L5.43146 7.31324L8.99849 0.564453L12.5655 7.31324L17.4345 4.56809Z" fill="url(#acctCrownFill)"/>
                                    <path d="M17.436 4.56774L17.9724 4.74204C18.0075 4.63403 18.0091 4.51794 17.9772 4.40898C17.9452 4.30001 17.881 4.20324 17.7931 4.13132C17.7053 4.0594 17.5977 4.01568 17.4846 4.00588C17.3714 3.99609 17.258 4.02067 17.159 4.07641L17.436 4.56774ZM14.9681 12.1632V12.7273C15.0872 12.7273 15.2033 12.6896 15.2996 12.6196C15.396 12.5496 15.4677 12.4508 15.5045 12.3375L14.9681 12.1632ZM3.03191 12.1632L2.49546 12.3375C2.53227 12.4508 2.604 12.5496 2.70037 12.6196C2.79673 12.6896 2.91279 12.7273 3.03191 12.7273V12.1632ZM0.564013 4.56774L0.840982 4.07641C0.74204 4.02067 0.628576 3.99609 0.515435 4.00588C0.402293 4.01568 0.294743 4.0594 0.206855 4.13132C0.118966 4.20324 0.0548315 4.30001 0.022842 4.40898C-0.00914753 4.51794 -0.00750268 4.63403 0.0275617 4.74204L0.564013 4.56774ZM5.43297 7.31289L5.156 7.80421C5.22163 7.84121 5.29402 7.86469 5.36888 7.87325C5.44373 7.88182 5.51955 7.8753 5.59185 7.85409C5.66414 7.83287 5.73146 7.79739 5.78981 7.74973C5.84817 7.70207 5.89639 7.6432 5.93162 7.5766L5.43297 7.31289ZM9 0.564099L9.49866 0.300386C9.45071 0.20971 9.37894 0.133826 9.29107 0.0808973C9.20321 0.0279688 9.10258 0 9 0C8.89743 0 8.79679 0.0279688 8.70893 0.0808973C8.62107 0.133826 8.5493 0.20971 8.50134 0.300386L9 0.564099ZM12.567 7.31289L12.0684 7.5766C12.1036 7.6432 12.1518 7.70207 12.2102 7.74973C12.2685 7.79739 12.3359 7.83287 12.4082 7.85409C12.4805 7.8753 12.5563 7.88182 12.6311 7.87325C12.706 7.86469 12.7784 7.84121 12.844 7.80421L12.567 7.31289ZM16.8995 4.39372L14.4316 11.9889L15.5045 12.3375L17.9724 4.74204L16.8995 4.39372ZM14.9681 11.5989H3.03191V12.7273H14.9681V11.5989ZM3.56836 11.9886L1.10046 4.39315L0.0275617 4.74204L2.49546 12.3375L3.56836 11.9886ZM0.287044 5.05906L5.156 7.80421L5.70965 6.82157L0.8407 4.07641L0.287044 5.05906ZM5.93162 7.5766L9.49866 0.827812L8.50134 0.300386L4.93431 7.04946L5.93162 7.5766ZM8.50106 0.827812L12.0684 7.5766L13.066 7.04918L9.49866 0.300386L8.50106 0.827812ZM12.844 7.80421L17.7135 5.05906L17.159 4.07641L12.2903 6.82157L12.844 7.80421Z" fill="url(#acctCrownStroke)"/>
                                    <path d="M14.6412 14.6665H3.35938" stroke="url(#acctCrownBar)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <defs>
                                        <linearGradient id="acctCrownFill" x1="9" y1="0.564453" x2="9" y2="12.1636" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="1" stop-color="#C1F11D"/></linearGradient>
                                        <linearGradient id="acctCrownStroke" x1="9" y1="0" x2="9" y2="12.7273" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="1" stop-color="#C1F11D"/></linearGradient>
                                        <linearGradient id="acctCrownBar" x1="9" y1="14.6665" x2="9" y2="15.6665" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="1" stop-color="#C1F11D"/></linearGradient>
                                    </defs>
                                </svg>
                                Upgrade to Premium
                            </span>
                            <span class="acct-free-badge">Free Plan</span>
                        </div>
                        <div class="acct-quota-row">
                            <span>Conversation Quota</span>
                            <b>{{ $quotaUsed }} / {{ $quotaMax }} People</b>
                        </div>
                        <div class="acct-progress">
                            <div class="acct-progress-fill" style="width: {{ $quotaPct }}%;"></div>
                        </div>
                        <p class="acct-premium-desc">You can chat with {{ $quotaRemaining }} more unique people on your Free plan.</p>
                    </div>

                    {{-- Stay Connected / Newsletter card --}}
                    <div class="acct-card acct-connect-card">
                        <div class="acct-connect-image"></div>
                        <div class="acct-connect-content">
                            <h3 class="acct-connect-head">Stay Connected with <b>Evoory!</b></h3>
                            <div class="acct-connect-divider"></div>
                            <div class="acct-connect-nl">
                                <svg width="50" height="50" viewBox="0 0 74 74" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <g filter="url(#acctNlGlow)">
                                        <circle cx="36.8008" cy="36.7998" r="13" fill="#9227FE"/>
                                    </g>
                                    <path d="M46.8008 32.3453V41.2543C46.8008 41.6057 46.7357 41.9338 46.6055 42.2387C46.4753 42.5436 46.2962 42.8121 46.0684 43.0441C45.8405 43.2761 45.5736 43.4584 45.2676 43.591C44.9616 43.7236 44.6393 43.7932 44.3008 43.7998H29.2324C28.9004 43.7998 28.5879 43.7335 28.2949 43.6009C28.002 43.4684 27.7448 43.2927 27.5234 43.074C27.3021 42.8552 27.1263 42.5934 26.9961 42.2884C26.8659 41.9835 26.8008 41.662 26.8008 41.324V29.7998H44.3008V32.3453H46.8008ZM45.5508 33.618H44.3008V40.618C44.3008 40.7903 44.2389 40.9395 44.1152 41.0654C43.9915 41.1914 43.8451 41.2543 43.6758 41.2543C43.5065 41.2543 43.36 41.1914 43.2363 41.0654C43.1126 40.9395 43.0508 40.7903 43.0508 40.618V31.0725H28.0508V41.324C28.0508 41.4897 28.0801 41.6454 28.1387 41.7913C28.1973 41.9371 28.2819 42.0631 28.3926 42.1691C28.5033 42.2752 28.6302 42.3614 28.7734 42.4276C28.9167 42.4939 29.0697 42.5271 29.2324 42.5271H44.3008C44.4766 42.5271 44.6393 42.4939 44.7891 42.4276C44.9388 42.3614 45.069 42.2719 45.1797 42.1592C45.2904 42.0465 45.3815 41.9106 45.4531 41.7515C45.5247 41.5924 45.5573 41.4267 45.5508 41.2543V33.618ZM41.8008 33.618H29.3008V32.3453H41.8008V33.618ZM41.8008 41.2543H36.8008V39.9816H41.8008V41.2543ZM41.8008 38.7089H36.8008V37.4362H41.8008V38.7089ZM41.8008 36.1634H36.8008V34.8907H41.8008V36.1634ZM35.5508 41.2543H29.3008V34.8609H35.5508V41.2543ZM30.5508 39.9816H34.3008V36.1336H30.5508V39.9816Z" fill="white"/>
                                    <defs>
                                        <filter id="acctNlGlow" x="0.000782013" y="-0.00019455" width="73.6" height="73.6" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                            <feGaussianBlur stdDeviation="11.9" result="effect1_foregroundBlur_2116_97"/>
                                        </filter>
                                    </defs>
                                </svg>
                                Newsletter
                            </div>
                            <p class="acct-connect-desc">Get the latest updates, exclusive offers and more, straight to your inbox.</p>
                            <button type="button" class="acct-connect-edit" @click="newsletterOpen = true">
                                <svg width="16" height="16" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M4 5H3C2.46957 5 1.96086 5.21071 1.58579 5.58579C1.21071 5.96086 1 6.46957 1 7V16C1 16.5304 1.21071 17.0391 1.58579 17.4142C1.96086 17.7893 2.46957 18 3 18H12C12.5304 18 13.0391 17.7893 13.4142 17.4142C13.7893 17.0391 14 16.5304 14 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13 3.00011L16 6.00011M17.385 4.58511C17.7788 4.19126 18.0001 3.65709 18.0001 3.10011C18.0001 2.54312 17.7788 2.00895 17.385 1.61511C16.9912 1.22126 16.457 1 15.9 1C15.343 1 14.8088 1.22126 14.415 1.61511L6 10.0001V13.0001H9L17.385 4.58511Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Edit Subscription
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Delete account --}}
            <div class="acct-delete-wrap">
                <form action="{{ route('user.account.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to DELETE your account? This will deactivate your account and all your profiles. This action cannot be reversed.')">
                    @csrf
                    <button type="submit" class="acct-delete-btn">
                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M6.8 2.90244H10.2C10.2 2.46257 10.0209 2.04071 9.70208 1.72968C9.38327 1.41864 8.95087 1.2439 8.5 1.2439C8.04913 1.2439 7.61673 1.41864 7.29792 1.72968C6.97911 2.04071 6.8 2.46257 6.8 2.90244ZM5.525 2.90244C5.525 2.52129 5.60195 2.14386 5.75146 1.79172C5.90097 1.43958 6.1201 1.11962 6.39636 0.850105C6.67261 0.580588 7.00057 0.366796 7.36152 0.220935C7.72246 0.0750738 8.10932 0 8.5 0C8.89068 0 9.27754 0.0750738 9.63848 0.220935C9.99943 0.366796 10.3274 0.580588 10.6036 0.850105C10.8799 1.11962 11.099 1.43958 11.2485 1.79172C11.3981 2.14386 11.475 2.52129 11.475 2.90244H16.3625C16.5316 2.90244 16.6937 2.96797 16.8133 3.0846C16.9328 3.20124 17 3.35944 17 3.52439C17 3.68934 16.9328 3.84754 16.8133 3.96418C16.6937 4.08081 16.5316 4.14634 16.3625 4.14634H15.2405L14.246 14.1896C14.1697 14.9592 13.8023 15.6734 13.2155 16.193C12.6287 16.7126 11.8646 17.0003 11.0721 17H5.9279C5.13558 17.0001 4.37164 16.7123 3.78501 16.1927C3.19838 15.6731 2.83112 14.959 2.75485 14.1896L1.7595 4.14634H0.6375C0.468424 4.14634 0.306274 4.08081 0.186719 3.96418C0.0671649 3.84754 0 3.68934 0 3.52439C0 3.35944 0.0671649 3.20124 0.186719 3.0846C0.306274 2.96797 0.468424 2.90244 0.6375 2.90244H5.525ZM7.225 6.84146C7.225 6.67651 7.15784 6.51832 7.03828 6.40168C6.91873 6.28504 6.75658 6.21951 6.5875 6.21951C6.41842 6.21951 6.25627 6.28504 6.13672 6.40168C6.01717 6.51832 5.95 6.67651 5.95 6.84146V13.061C5.95 13.2259 6.01717 13.3841 6.13672 13.5008C6.25627 13.6174 6.41842 13.6829 6.5875 13.6829C6.75658 13.6829 6.91873 13.6174 7.03828 13.5008C7.15784 13.3841 7.225 13.2259 7.225 13.061V6.84146ZM10.4125 6.21951C10.5816 6.21951 10.7437 6.28504 10.8633 6.40168C10.9828 6.51832 11.05 6.67651 11.05 6.84146V13.061C11.05 13.2259 10.9828 13.3841 10.8633 13.5008C10.7437 13.6174 10.5816 13.6829 10.4125 13.6829C10.2434 13.6829 10.0813 13.6174 9.96172 13.5008C9.84217 13.3841 9.775 13.2259 9.775 13.061V6.84146C9.775 6.67651 9.84217 6.51832 9.96172 6.40168C10.0813 6.28504 10.2434 6.21951 10.4125 6.21951ZM4.0239 14.0702C4.06975 14.5318 4.29016 14.9602 4.64216 15.2719C4.99417 15.5836 5.45253 15.7562 5.9279 15.7561H11.0721C11.5475 15.7562 12.0058 15.5836 12.3578 15.2719C12.7098 14.9602 12.9303 14.5318 12.9761 14.0702L13.9604 4.14634H3.0396L4.0239 14.0702Z" fill="#C1F11D"/>
                        </svg>
                        Delete account
                    </button>
                </form>
            </div>
        </div>

        {{-- Newsletter modal (triggered by "Edit Subscription" on the Stay Connected card) --}}
        <div x-show="newsletterOpen" x-cloak class="ev-modal-overlay" @click.self="newsletterOpen = false">
            <div class="ev-modal">
                <div class="ev-modal-header">
                    <h2>
                        <i class="fa fa-newspaper"></i>
                        <span>Newsletter</span>
                    </h2>
                    <button type="button" class="ev-modal-close" @click="newsletterOpen = false">&times;</button>
                </div>
                <div class="ev-modal-body">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer; font-size: 1rem;">
                            <input type="checkbox" wire:model.live="receiveNewsletter" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: #C1F11D;">
                            <span style="margin-left: 0.75rem; font-weight: 500;">Send me newsletter for:</span>
                        </label>
                    </div>
                    <div style="margin-bottom: 1rem; position: relative;">
                        <div class="ev-search-input">
                            <span><i class="fa fa-map-marker-alt"></i></span>
                            <input type="text" placeholder="Find city..." wire:model.live="citySearch" autocomplete="off">
                            @if($citySearch)
                                <button type="button" wire:click="$set('citySearch', '')"><i class="fa fa-times"></i></button>
                            @endif
                        </div>
                        @if(count($searchResults) > 0)
                            <div class="ev-dropdown-results">
                                @foreach($searchResults as $city)
                                    <button type="button" wire:click="addCity({{ $city['id'] }})">
                                        {{ $city['name'] }}@if($city['country']) <span style="color:#666;">({{ $city['country'] }})</span>@endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @if(count($selectedCities) > 0)
                        <div style="margin-bottom: 1rem; max-height: 150px; overflow-y: auto;">
                            @foreach($selectedCities as $index => $city)
                                <div class="ev-city-tag">
                                    <span><i class="fa fa-map-marker-alt" style="margin-right:0.5rem;"></i>{{ $city['name'] }}@if($city['country']) <span style="color:#666;">({{ $city['country'] }})</span>@endif</span>
                                    <button type="button" style="background:none;border:none;cursor:pointer;font-size:1.2rem;padding:0;" wire:click="removeCity({{ $index }})"><i class="fa fa-times"></i></button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div>
                        <label style="display: block; margin-bottom: 1rem; font-weight: 600; font-size: 1rem;">Include</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" value="female" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: #C1F11D;">
                                <span style="margin-left: 0.5rem;">Escorts</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" value="male" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: #C1F11D;">
                                <span style="margin-left: 0.5rem;">Male Escorts</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" value="shemale" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: #C1F11D;">
                                <span style="margin-left: 0.5rem;">Shemale Escorts</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="ev-modal-footer">
                    <button type="button" class="ev-buy-btn" wire:click="saveNewsletter" @click="newsletterOpen = false">
                        <span>Save</span> <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>