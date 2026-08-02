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
            margin-inline:45px;
        }
        .ev-am-help__call-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            font-size: 18px;
            font-weight: 400;
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
            margin-inline: 45px;
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
            margin: 8px 42px;
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
            gap: 10px;
            padding: 10px 12px;
            border-radius: 6px;
         
         
            color: #fff !important;
            text-decoration: none !important;
            text-align: left;
            margin-inline: 45px;
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
            ? route('user.dashboard', ['name' => $firstProfile->slug, 'id' => $firstProfile->id])
            : url('/archived-profiles');
    @endphp
    @if($u)
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
                    {{-- Storage::url() resolves to the local /storage/...
                         path (the same one user-account-edit uses).
                         smart_asset() was rewriting it to a CDN host that
                         doesn't have the actual uploaded file. --}}
                    <img src="{{ Storage::url($u->avatar) }}" alt="{{ $u->name }}">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                @endif
            </div>
            <h2 class="ev-am-name">{{ $u->name ?? 'Account' }}</h2>
            <p class="ev-am-email">{{ $u->email }}</p>
        </div>

        {{-- Menu list --}}
        <nav class="ev-am-menu">
            <a href="/my-account/edit" wire:navigate class="ev-am-item">
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
    <div class="ev-container" style="padding-top: 8px; padding-bottom: 40px;">
        
        {{-- Account tabs --}}
        <div class="ev-account-tabs">
            <a href="/my-account" wire:navigate class="{{ request()->is('my-account') && !request()->is('my-account/*') ? 'active' : '' }}">
                <i class="fa fa-user"></i> Account
            </a>
            <a href="/my-account/edit" wire:navigate class="{{ request()->is('my-account/edit') ? 'active' : '' }}">
                <i class="fa fa-pencil-alt"></i> Edit
            </a>
            <a href="/my-password/edit" wire:navigate class="{{ request()->is('my-password/edit') ? 'active' : '' }}">
                <i class="fa fa-key"></i> Password
            </a>
        </div>

        @if(session('success'))
            <div class="ev-alert-success ev-account-desktop-alert">{{ session('success') }}</div>
        @endif

        {{-- Cards grid --}}
        <div class="ev-account-cards">
            
            {{-- User info card --}}
            @php
                // Mirror the avatar resolution used on the edit page: prefer the
                // user's uploaded/Google-fetched avatar on the public disk; fall
                // back to a Gravatar identicon when they don't have one yet.
                $authUser = auth()->user();
                $accountAvatarUrl = !empty($authUser->avatar) && \Illuminate\Support\Facades\Storage::disk('public')->exists($authUser->avatar)
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($authUser->avatar)
                    : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($authUser->email))) . '?s=128&d=identicon';
            @endphp
            <div class="ev-account-card">
                <div class="ev-user-card">
                    <img alt="{{ $authUser->name }}'s avatar" class="ev-avatar" src="{{ $accountAvatarUrl }}" />
                    <div>
                        <h2 class="ev-user-name">{{ auth()->user()->name }}</h2>
                        <ul class="ev-user-info">
                            <li>
                                <strong>Account type</strong> @if(auth()->user()->type == 1) Standard @elseif(auth()->user()->type == 2) Individual @elseif(auth()->user()->type == 3) Agency @endif
                            </li>
                            <li>
                                <strong>Email</strong> {{ auth()->user()->email }}
                            </li>
                        </ul>
                    </div>
                </div>
                <a class="ev-edit-btn" href="/my-account/edit">
                    <i class="fa fa-pencil-alt"></i>
                    <span>Edit</span>
                </a>
            </div>

            {{-- Credits card --}}
            <div class="ev-account-card">
                <div class="ev-credits-mobile-row" style="display:none;">
                    <div class="ev-credits-icon" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 10h20"/></svg>
                    </div>
                    <div>
                        <p class="ev-credits-label">Credits</p>
                        <h2 class="ev-credits-amount">${{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}</h2>
                    </div>
                </div>
                <h2 class="ev-credits-amount ev-desktop-credits">Credits ${{ auth()->user()->wallet->balance ?? 0 }}</h2>
                <div style="margin-bottom: 16px;">
                    <a class="ev-buy-btn" href="/purchase-credits" onclick="window.location.href='/purchase-credits'; return false;">
                        <i class="fa fa-coins"></i>
                        <span>Buy more</span>
                    </a>
                </div>
                <div class="ev-payments">
                    <p class="ev-payments-label" style="margin: 0; width: 100%;">We accept:</p>
                    <img alt="Mastercard logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/mc_logo-1fee638879a55506111eef88a8369601147f17d09fa23d940350fee69fb9fc79.svg" width="36" />
                    <img alt="Visa logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" width="36" />
                    <img alt="Bitcoin icon" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc-b522654df0046f6af0e8ac9f67078a87d26069445a01866c1c337bde91bbcd5f.svg" width="24" />
                    <img alt="Lightning network icon" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc_lightning-fa0277781a99cded862007ac4d2e5f5fa8fbafec4d9c2b59dbd3b2fc354e91a0.svg" width="42" />
                    <img alt="Neosurf voucher logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/layout/neosurf-bd53910fca644afad7f8660597f25b84b5c97418652d1c2794ab5f1462e2faf7.svg" width="50" />
                    <img alt="Payprocc Payments Promptpay logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_promptpay_logo-6b70d10f79bdff9dc1efde97f390f89f91019db1b14c81389d20be94fd270275.svg" style="filter: brightness(2)" width="72" />
                    <img alt="Payprocc Payments Momo logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_momo_logo-41d3eadebd629ec10dd28d7c13683b8c5cf564e37eae34ae478de6d89a72b26c.svg" width="24" />
                    <img alt="Payprocc Payments Viettelpay logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_viettelpay_logo-f99d7823ad8c66b5da8a2248c1877215a648cb16703f63c641584d684f73019b.svg" width="26" />
                </div>
            </div>

            {{-- Communication card --}}
            @php
                // Mirror the chat page's unread logic exactly: count unread messages
                // in conversations the user participates in, EXCLUDING messages the
                // user themselves sent. Previously this counted messages by
                // messages.profile_id (legacy pre-conversation schema), which
                // surfaced a badge count that /my-chat couldn't show.
                $uid = auth()->id();
                $convoIds = \App\Models\Conversation::query()
                    ->where(function($q) use ($uid) {
                        $q->where('user_one_id', $uid)->orWhere('user_two_id', $uid);
                    })
                    ->pluck('id');
                // Match Chat::markAsRead's definition of "unread": a message is
                // unread until the recipient opens the conversation and it gets
                // stamped 'read'. Statuses along the way: NULL → sent → delivered.
                $unreadMsgCount = \App\Models\Message::whereIn('conversation_id', $convoIds)
                    ->where('sender_id', '!=', $uid)
                    ->where(function($q) {
                        $q->whereNull('status')->orWhereIn('status', ['sent', 'delivered', 'unread']);
                    })
                    ->count();
            @endphp
            <div class="ev-account-card">
                <h2>
                    <i class="fa fa-comments ev-card-icon"></i> Communication
                    @if($unreadMsgCount > 0)
                        <span class="ev-badge" style="background:#dc3545;color:#fff;font-size:11px;padding:1px 8px;border-radius:50%;">{{ $unreadMsgCount }}</span>
                    @endif
                </h2>
                <ul class="ev-comm-list">
                    <li>
                        <a href="{{ route('user.chat') }}">
                            <i class="fa fa-envelope" style="width: 16px;"></i> Messages
                            @if($unreadMsgCount > 0)<span class="ev-badge">{{ $unreadMsgCount }}</span>@endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.questions') }}">
                            <i class="fa fa-question-circle" style="width: 16px;"></i> Questions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.reviews') }}">
                            <i class="fa fa-star" style="width: 16px;"></i> Reviews
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('favorites.dashboard') }}">
                            <i class="fa fa-heart" style="width: 16px;"></i> My Favorites
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Newsletter card --}}
            <div class="ev-account-card" x-data="{ show: false }">
                <h2>
                    <i class="fa fa-newspaper ev-card-icon"></i> Newsletter
                </h2>
                <p class="ev-newsletter-text">
                    Receiving monthly updates in {{ auth()->user()->newsletterSubscriptions()->count() }} cities.<br>
                    <a href="#" @click.prevent="show = true">
                        <i class="fa fa-pencil-alt"></i> Edit Subscription
                    </a>
                </p>

                {{-- Newsletter Modal --}}
                <div x-show="show" x-cloak class="ev-modal-overlay" @click.self="show = false">
                    <div class="ev-modal">
                        <div class="ev-modal-header">
                            <h2>
                                <i class="fa fa-newspaper" ></i>
                                <span>Newsletter</span>
                            </h2>
                            <button type="button" class="ev-modal-close" @click="show = false">&times;</button>
                        </div>
                        
                        <div class="ev-modal-body">
                            {{-- Checkbox --}}
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: flex; align-items: center; cursor: pointer; font-size: 1rem;">
                                    <input type="checkbox" id="receiveNewsletter" wire:model.live="receiveNewsletter" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                    <span style="margin-left: 0.75rem; font-weight: 500;">Send me newsletter for:</span>
                                </label>
                            </div>

                            {{-- City Search --}}
                            <div style="margin-bottom: 1rem; position: relative;">
                                <div class="ev-search-input">
                                    <span><i class="fa fa-map-marker-alt"></i></span>
                                    <input type="text" placeholder="Find city..." wire:model.live="citySearch" autocomplete="off">
                                    @if($citySearch)
                                        <button type="button" wire:click="$set('citySearch', '')">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                                
                                @if(count($searchResults) > 0)
                                    <div class="ev-dropdown-results">
                                        @foreach($searchResults as $city)
                                            <button type="button" wire:click="addCity({{ $city['id'] }})">
                                                {{ $city['name'] }}@if($city['country']) <span style="color: var(--text-muted, #666);">({{ $city['country'] }})</span>@endif
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Selected Cities --}}
                            @if(count($selectedCities) > 0)
                                <div style="margin-bottom: 1rem; max-height: 150px; overflow-y: auto;">
                                    @foreach($selectedCities as $index => $city)
                                        <div class="ev-city-tag">
                                            <span>
                                                <i class="fa fa-map-marker-alt" style="margin-right: 0.5rem;"></i>
                                                {{ $city['name'] }}@if($city['country']) <span style="color: var(--text-muted, #666);">({{ $city['country'] }})</span>@endif
                                            </span>
                                            <button type="button" style="background: none; border: none;  cursor: pointer; font-size: 1.2rem; padding: 0;" wire:click="removeCity({{ $index }})">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Add City Button --}}
                            <button type="button" class="ev-btn-outline-sm" style="padding: 0.6rem 1rem; margin-bottom: 1.5rem; background: var(--bg-secondary, #111);  border: 1px solid var(--border-color, #2a2a2a); border-radius: var(--radius, 5px); cursor: pointer; font-weight: 600; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem;" wire:click="$set('citySearch', '')">
                                <i class="fa fa-plus"></i>
                                <span>Add city</span>
                            </button>

                            {{-- Include Genders --}}
                            <div>
                                <label style="display: block; margin-bottom: 1rem; font-weight: 600; font-size: 1rem;">Include</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="gender_female" value="female" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                        <span style="margin-left: 0.5rem;">Escorts</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="gender_male" value="male" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                        <span style="margin-left: 0.5rem;">Male Escorts</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="gender_shemale" value="shemale" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--accent, #C1F11D);">
                                        <span style="margin-left: 0.5rem;">Shemale Escorts</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ev-modal-footer">
                            <button type="button" class="ev-buy-btn" wire:click="saveNewsletter" @click="show = false">
                                <span>Save</span>
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Newsletter Row --}}
            <div class="ev-newsletter-mobile" style="display:none;" x-data="{ show: false }">
                <div class="ev-newsletter-mobile-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C1F11D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div class="ev-newsletter-mobile-info" @click="show = true">
                    <p class="ev-nl-title">Newsletter</p>
                    <p class="ev-nl-sub">Subscribed</p>
                </div>
                <span class="ev-newsletter-mobile-arrow" @click="show = true">›</span>

                {{-- Reuse newsletter modal --}}
                <div x-show="show" x-cloak class="ev-modal-overlay" @click.self="show = false">
                    <div class="ev-modal">
                        <div class="ev-modal-header">
                            <h2><i class="fa fa-newspaper"></i> <span>Newsletter</span></h2>
                            <button type="button" class="ev-modal-close" @click="show = false">&times;</button>
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
                                            <button type="button" wire:click="addCity({{ $city['id'] }})">{{ $city['name'] }}@if($city['country']) <span style="color:#666;">({{ $city['country'] }})</span>@endif</button>
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
                        </div>
                        <div class="ev-modal-footer">
                            <button type="button" class="ev-buy-btn" wire:click="saveNewsletter" @click="show = false"><span>Save</span> <i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Delete account --}}
        <div class="ev-account-delete-wrap" style="text-align: right; margin-top: 8px;">
            <form action="{{ route('user.account.delete') }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to DELETE your account? This will deactivate your account and all your profiles. This action cannot be reversed.')">
                @csrf
                <button type="submit" class="ev-delete-link">
                    <i class="fa fa-times"></i> Delete account
                </button>
            </form>
        </div>

    </div>

</div>