@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* ===============================================================
       My Account - Edit (redesigned per 2026-10-13 mockup)
       - Dark back-bar header matching other account pages
       - Full-width "Complete Your Profile" card with circular meter
       - Tab strip (Account / Edit / Password) below the card
       - 2-column form grid: fields on the left, Profile photo +
         Trust & Verification cards on the right. Collapses to a
         single column under 900px.
       =============================================================== */

    /* Force the body to pure black on this page. The site's global
       stylesheet applies `background: var(--bg-primary) !important`
       on body, so !important is required to override it. */
    body { background: #000 !important; }

    /* My Account - Edit only: strip the top header's dark background
       so it blends into the pure-black body. Compound selector
       (header.ev-header) because the class sits on the <header>.
       Also unstick it so the top header scrolls away with the content
       instead of pinning to the viewport (the global rule sets
       `position: sticky; top: 0`). */
    header.ev-header {
        background: transparent !important;
        position: static !important;
    }

    /* Dark back-bar strip at the top of the main content. */
    .acctedit-header {
        background: #131616;
        padding: 14px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 24px;
    }
    .acctedit-header-inner {
        max-width: 1300px;
        margin: 0 auto;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        min-height: 24px;
    }
    .acctedit-header-back {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #C1F11D;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }
    .acctedit-header-back:hover { color: #d4ff3a; text-decoration: none; }
    .acctedit-header-title {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .acctedit-wrap {
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 0 48px;
        color: #fff;
    }

    .acctedit-flash {
        background: rgba(193,241,29,0.1);
        border: 1px solid rgba(193,241,29,0.3);
        color: #C1F11D;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    /* "Complete Your Profile" card — appears above the tabs. Right
       side carries a pill-shaped dark panel with the circular ring,
       matching the reference mockup. No full-width progress bar: the
       ring is the only progress indicator. */
    .acctedit-complete {
        background: #131616;
        border: 1px solid #242B2D;
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 24px;
    }
    .acctedit-complete-body { flex: 1 1 auto; min-width: 0; }
    .acctedit-complete-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    .acctedit-complete-head i {
        font-size: 20px;
        color: #fff;
    }
    .acctedit-complete-head h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 400;
        color: #fff;
    }
    .acctedit-complete-desc {
        color: rgba(255,255,255,0.65);
        font-size: 14px;
        margin: 0;
    }
    .acctedit-complete-missing {
        display: inline-block;
        background: rgba(255, 165, 0, 0.08);
        border: 1px solid rgba(255, 165, 0, 0.3);
        color: #ffb84d;
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 999px;
        margin-top: 10px;
    }
    .acctedit-complete-missing i { margin-right: 6px; font-size: 12px; }
    .acctedit-complete-missing .req { color: #ef4444; font-weight: 700; }

    /* Pill panel on the right — darker, holds the "Profile Strength"
       label + horizontal bar on the left side, circular ring on the
       right side. Both indicators live INSIDE this pill. */
    .acctedit-complete-meter {
        background: #0a0a0a;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 12px;
        padding: 14px 20px;
        display: inline-flex;
        align-items: center;
        gap: 18px;
        flex-shrink: 0;
        min-width: 320px;
    }
    .acctedit-complete-meter-text {
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1 1 auto;
        min-width: 0;
    }
    .acctedit-complete-meter-label {
        color: #fff;
        font-size: 14px;
        font-weight: 500;
    }
    .acctedit-complete-meter-bar {
        height: 6px;
        background: rgba(255,255,255,0.1);
        border-radius: 999px;
        overflow: hidden;
    }
    .acctedit-complete-meter-bar > div {
        height: 100%;
        background: linear-gradient(90deg, #C1F11D, #a8d616);
        border-radius: 999px;
        transition: width 0.5s ease;
    }
    .acctedit-ring {
        position: relative;
        display: inline-block;
        line-height: 0;
    }
    .acctedit-ring svg { display: block; }
    .acctedit-ring-pct {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        font-size: 14px;
        font-weight: 700;
        color: #fff;
    }

    /* Tabs — Account / Edit / Password. Three equal-width pills. */
    .acctedit-tabs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    .acctedit-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 5px 16px;
        background: #131616;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 5px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 120ms, border-color 120ms, background 120ms;
    }
    .acctedit-tab:hover { color: #fff; border-color: rgba(255,255,255,0.14); text-decoration: none; }
    .acctedit-tab.active {
        color: #C1F11D;
      
    }
    .acctedit-tab i { font-size: 13px; }
    .acctedit-tab svg { display: block; width: 16px; height: 16px; }

    /* Grid: form (left, 2fr) + sidebar (right, 1fr). */
    .acctedit-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    /* Left column: form fields grouped into a card. */
    .acctedit-formcard {
        
    }

    .acctedit-field { margin-bottom: 18px; }
    .acctedit-field:last-child { margin-bottom: 0; }
    .acctedit-field label {
        display: block;
        color: rgba(255,255,255,0.75);
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .acctedit-input {
        width: 100%;
        padding: 12px 14px;
        background: #14141A2E;
        border: 1px solid #5E6365;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
        transition: border-color 120ms;
    }
    .acctedit-input:focus { border-color: rgba(193,241,29,0.5); }
    .acctedit-input:disabled { opacity: 0.6; cursor: not-allowed; }
    .acctedit-input::placeholder { color: rgba(255,255,255,0.3); }
    textarea.acctedit-input { resize: vertical; min-height: 110px; }
    .acctedit-hint {
        color: rgba(255,255,255,0.4);
        font-size: 12px;
        margin-top: 6px;
    }
    .acctedit-error {
        color: #ef4444;
        font-size: 12px;
        margin-top: 6px;
        display: block;
    }

    /* Two fields side-by-side on one row. */
    .acctedit-row2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Account-type radios — minimal: a circle + white label, no box.
       Matches the reference where the options sit directly on the
       card background with just a radio button and plain text. */
    .acctedit-radiogroup {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
    }
    .acctedit-radio {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 0;
        background: transparent;
        border: none;
    }
    .acctedit-radio input {
        accent-color: #C1F11D;
        width: 16px; height: 16px;
        /* Explicit right-margin instead of relying on flex `gap`,
           which was losing out to a global form-control reset elsewhere
           in the project. */
        margin: 0 10px 0 0 !important;
        flex-shrink: 0;
    }
    .acctedit-radio span {
        color: #fff;
        font-size: 14px;
        font-weight: 400;
    }

    /* Phone row — reuses the dropdown styles from the original file. */
    .acctedit-phone-row {
        display: flex;
        gap: 10px;
    }
    .ev-country-select {
        position: relative;
        width: 140px;
        flex-shrink: 0;
    }
    .ev-country-display {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        background: #14141A2E;
        border: 1px solid #5E6365;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        transition: border-color 120ms;
    }
    .ev-country-display:hover { border-color: rgba(255,255,255,0.18); }
    .ev-country-dropdown {
        display: none;
        position: absolute;
        top: 100%; left: 0;
        width: 320px;
        max-width: calc(100vw - 32px);
        max-height: min(280px, calc(100vh - 220px));
        overflow-y: auto;
        background: #131616;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        z-index: 1000;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        margin-top: 4px;
    }
    .ev-country-dropdown input {
        width: 100%;
        padding: 10px 12px;
        background: #0a0a0a;
        border: none;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        color: #fff;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
    }
    .ev-country-dropdown input::placeholder { color: rgba(255,255,255,0.3); }
    .country-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: background 0.15s;
    }
    .country-option:hover { background: rgba(255,255,255,0.04) !important; }
    .country-option span { color: #fff !important; }
    .country-option span:last-child {
        color: rgba(255,255,255,0.5) !important;
        margin-left: auto;
    }
    .ev-country-dropdown::-webkit-scrollbar { width: 6px; }
    .ev-country-dropdown::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 3px; }

    /* Right column cards. */
    .acctedit-sidecard {
        background: #131616;
        border: 1px solid #242B2D;
        border-radius: 14px;
        padding: 16px 16px 2px 16px;
    }
    .acctedit-sidecard + .acctedit-sidecard { margin-top: 20px; }
    .acctedit-sidecard-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;

        font-size: 15px;
        font-weight: 400;
        margin: 0 0 16px;
    }
    .acctedit-sidecard-title i { color: rgba(255,255,255,0.6); font-size: 16px; }

    .acctedit-photo {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }
    /* Rounded-SQUARE preview (not a circle) matching the reference. */
    .acctedit-photo-preview {
        width: 180px;
        height: 180px;
        border-radius: 14px;
        overflow: hidden;
        background: #0a0a0a;
        border: 1px solid rgba(255,255,255,0.08);
        display: flex; align-items: center; justify-content: center;
    }
    .acctedit-photo-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }

    /* "Remove Photo" — lime text + lime X icon, inline link style. */
    .acctedit-photo-remove {
        background: transparent;
        border: none;
        color: #C1F11D;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        font-family: inherit;
        padding: 2px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .acctedit-photo-remove:hover { background: rgba(193,241,29,0.08); }
    .acctedit-photo-remove svg { width: 14px; height: 14px; display: block; }

    /* "Choose File" — full-width dark button with white text and
       image icon, matching the reference layout. */
    .acctedit-photo-choose {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 5px 18px;
        background: #1D2224;
        border: 1px solid #232A2E;
        border-radius: 6px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: border-color 120ms, background 120ms;
    }
    .acctedit-photo-choose:hover { border-color: rgba(193,241,29,0.4); background: #232A2E; }
    .acctedit-photo-choose svg { width: 21px; height: 21px; display: block; }
    /* Hide the ugly default file input — the pill above triggers it. */
    .acctedit-photo input[type="file"] { display: none; }
    .acctedit-photo-caption {
        color: #9CA3AF;

        font-size: 12px;
        text-align: center;
        line-height: 1.5;
    }

    .acctedit-trust-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
    }
    .acctedit-trust-icon svg { display: block; width: 28px; height: 28px; }
    .acctedit-trust-body {
        color: #9CA3AF;

        font-size: 12px;
        line-height: 1.6;
        margin: 0;
    }

    p.acctedit-trust-body {
    padding-bottom: 11px;
}

    /* Save button — lime pill, left-aligned under the form card. */
    .acctedit-save-wrap { margin-top: 24px; }
    .acctedit-save-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 42px;
        background: #C1F11D;
        color: #0a0a0a;
        border: none;
        border-radius: 999px;
        font-size: 16px;
        font-weight: 500;
        font-family: inherit;
        cursor: pointer;
        transition: background 120ms, transform 120ms;
    }
    .acctedit-save-btn:hover { background: #d4ff3a; transform: translateY(-1px); }
    .acctedit-save-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* Responsive — stack grid, tighten paddings on mobile. */
    @media (max-width: 900px) {
        .acctedit-grid { grid-template-columns: 1fr; }
        .acctedit-row2 { grid-template-columns: 1fr; }
        .acctedit-complete { flex-direction: row; align-items: center; padding: 16px 18px; gap: 14px; }
        .acctedit-complete-meter { padding: 10px 14px; gap: 10px; }
        .acctedit-complete-meter-label { font-size: 12px; display: none; }
        .acctedit-ring svg { width: 56px; height: 56px; }
        .acctedit-ring-pct { font-size: 12px; }
        .acctedit-complete-head h3 { font-size: 16px; }
        .acctedit-complete-desc { font-size: 13px; }
        .acctedit-tabs { gap: 8px; }
        .acctedit-tab { padding: 5px 8px; font-size: 13px; }
        .acctedit-tab i { display: none; }
        .acctedit-formcard, .acctedit-sidecard { padding: 18px; }
        .acctedit-header-title { font-size: 15px; }
        .acctedit-header-back span { display: none; }
    }

    /* Match .ev-container's 12px inset under 768px so content doesn't
       touch the screen edge on phones. */
    @media (max-width: 768px) {
        .acctedit-header-inner { padding: 0 12px; }
        .acctedit-header-back { left: 12px; }
        .acctedit-wrap { padding: 0 12px 48px; }
    }
</style>
@endpush

<div>
    {{-- Dark back-bar at the top of the page. Matches the chat sidebar
         sub-headers used elsewhere in the account section. --}}
    <div class="acctedit-header">
        <div class="acctedit-header-inner">
            <a href="{{ url('/my-account') }}" wire:navigate class="acctedit-header-back" aria-label="My account">
                <i class="fa fa-chevron-left"></i>
                <span>My account</span>
            </a>
            <h1 class="acctedit-header-title">My Account - Edit</h1>
        </div>
    </div>

    <div class="acctedit-wrap">

        @if(session()->has('message'))
            <div class="acctedit-flash">
                <i class="fa fa-check-circle"></i> {{ session('message') }}
            </div>
        @endif

        {{-- "Complete Your Profile" card — appears above the tabs,
             with a circular % meter on the right and a progress bar
             spanning the full card width below. --}}
        @php $pc = $this->profileCompletion; @endphp
        <div class="acctedit-complete">
            <div class="acctedit-complete-body">
                <div class="acctedit-complete-head">
                    @if($pc['percentage'] >= 100)
                        <i class="fa fa-check-circle"></i>
                    @else
                        {{-- Product-supplied "profile person" glyph. --}}
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M1 15C1 13.9391 1.42143 12.9217 2.17157 12.1716C2.92172 11.4214 3.93913 11 5 11H13C14.0609 11 15.0783 11.4214 15.8284 12.1716C16.5786 12.9217 17 13.9391 17 15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17H3C2.46957 17 1.96086 16.7893 1.58579 16.4142C1.21071 16.0391 1 15.5304 1 15Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M9 7C10.6569 7 12 5.65685 12 4C12 2.34315 10.6569 1 9 1C7.34315 1 6 2.34315 6 4C6 5.65685 7.34315 7 9 7Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    @endif
                    <h3>{{ $pc['percentage'] >= 100 ? 'Profile Complete!' : 'Complete Your Profile' }}</h3>
                </div>
                @if($pc['isComplete'])
                    <p class="acctedit-complete-desc">Great job! Your profile is fully complete and ready to attract visitors.</p>
                @else
                    <p class="acctedit-complete-desc">Complete your profile to attract more visitors and build trust within the platform community.</p>
                    {{-- Orange nudge chip — only after save(), gated by
                         $showMissing which the component flashes into the
                         session on submit. Keeps the card calm on first
                         load, loud after a submit with missing fields. --}}
                    @if($showMissing && count($pc['incomplete']) > 0)
                        <span class="acctedit-complete-missing">
                            <i class="fa fa-exclamation-circle"></i>
                            Missing:
                            @foreach($pc['incomplete'] as $i => $field)
                                {{ $field }} <span class="req">*</span>@if($i < count($pc['incomplete']) - 1), @endif
                            @endforeach
                        </span>
                    @endif
                @endif
            </div>
            {{-- Pill-shaped meter: left column has the "Profile Strength"
                 label stacked above the horizontal bar; right column has
                 the circular ring. Both indicators live inside the pill. --}}
            <div class="acctedit-complete-meter">
                <div class="acctedit-complete-meter-text">
                    <span class="acctedit-complete-meter-label">Profile Strength</span>
                    <div class="acctedit-complete-meter-bar">
                        <div style="width: {{ $pc['percentage'] }}%;"></div>
                    </div>
                </div>
                <div class="acctedit-ring">
                    <svg width="64" height="64" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="27" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="5"/>
                        <circle cx="32" cy="32" r="27" fill="none" stroke="#C1F11D" stroke-width="5"
                            stroke-linecap="round"
                            stroke-dasharray="{{ 2 * 3.14159 * 27 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 27 * (1 - $pc['percentage'] / 100) }}"
                            transform="rotate(-90 32 32)"
                            style="transition: stroke-dashoffset 0.5s ease;"/>
                    </svg>
                    <span class="acctedit-ring-pct">{{ $pc['percentage'] }}%</span>
                </div>
            </div>
        </div>

        {{-- 2-column grid: tabs + form fields (left) + Profile photo & Trust (right).
             Tabs live INSIDE the left column so the right-side cards align
             to the same top edge as the tabs (per 2026-10-13 mockup). --}}
        <form wire:submit.prevent="save" id="edit_my_account" novalidate="novalidate" enctype="multipart/form-data">
            <div class="acctedit-grid">
                {{-- LEFT COLUMN — tabs + form fields --}}
                <div>
                    <div class="acctedit-tabs">
                        <a href="/my-account" wire:navigate class="acctedit-tab">
                            {{-- Account: person glyph (currentColor so it
                                 adopts the tab's text color — grey when
                                 idle, lime when .active). --}}
                            <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M1 15C1 13.9391 1.42143 12.9217 2.17157 12.1716C2.92172 11.4214 3.93913 11 5 11H13C14.0609 11 15.0783 11.4214 15.8284 12.1716C16.5786 12.9217 17 13.9391 17 15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17H3C2.46957 17 1.96086 16.7893 1.58579 16.4142C1.21071 16.0391 1 15.5304 1 15Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M9 7C10.6569 7 12 5.65685 12 4C12 2.34315 10.6569 1 9 1C7.34315 1 6 2.34315 6 4C6 5.65685 7.34315 7 9 7Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Account
                        </a>
                        <a href="/my-account/edit" wire:navigate class="acctedit-tab active">
                            {{-- Edit: pencil glyph. --}}
                            <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M4 5.00012H3C2.46957 5.00012 1.96086 5.21084 1.58579 5.58591C1.21071 5.96098 1 6.46969 1 7.00012V16.0001C1 16.5306 1.21071 17.0393 1.58579 17.4143C1.96086 17.7894 2.46957 18.0001 3 18.0001H12C12.5304 18.0001 13.0391 17.7894 13.4142 17.4143C13.7893 17.0393 14 16.5306 14 16.0001V15.0001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13 3.00011L16 6.00011M17.385 4.58511C17.7788 4.19126 18.0001 3.65709 18.0001 3.10011C18.0001 2.54312 17.7788 2.00895 17.385 1.61511C16.9912 1.22126 16.457 1 15.9 1C15.343 1 14.8088 1.22126 14.415 1.61511L6 10.0001V13.0001H9L17.385 4.58511Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Edit
                        </a>
                        <a href="/my-password/edit" wire:navigate class="acctedit-tab">
                            {{-- Password: key glyph. --}}
                            <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M1.49715 14.1093C1.17892 14.4275 1.0001 14.859 1 15.3089V17.1516C1 17.3766 1.08938 17.5924 1.24848 17.7515C1.40759 17.9106 1.62337 18 1.84838 18H4.39351C4.61851 18 4.8343 17.9106 4.9934 17.7515C5.15251 17.5924 5.24189 17.3766 5.24189 17.1516V16.3032C5.24189 16.0782 5.33127 15.8625 5.49037 15.7034C5.64947 15.5442 5.86526 15.4549 6.09027 15.4549H6.93864C7.16365 15.4549 7.37944 15.3655 7.53854 15.2064C7.69764 15.0473 7.78702 14.8315 7.78702 14.6065V13.7581C7.78702 13.5331 7.8764 13.3173 8.03551 13.1582C8.19461 12.9991 8.4104 12.9097 8.6354 12.9097H8.78132C9.23129 12.9096 9.6628 12.7308 9.98093 12.4126L10.6715 11.722C11.8506 12.1327 13.1342 12.1312 14.3123 11.7176C15.4904 11.3039 16.4933 10.5027 17.1568 9.44507C17.8204 8.38738 18.1053 7.13582 17.9651 5.89513C17.8249 4.65443 17.2677 3.49805 16.3848 2.61516C15.502 1.73226 14.3456 1.17513 13.1049 1.0349C11.8642 0.894666 10.6126 1.17964 9.55493 1.84319C8.49725 2.50675 7.69607 3.5096 7.28245 4.6877C6.86883 5.8658 6.86726 7.14939 7.27799 8.3285L1.49715 14.1093Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.3011 6.12267C13.5354 6.12267 13.7253 5.93275 13.7253 5.69848C13.7253 5.46421 13.5354 5.27429 13.3011 5.27429C13.0669 5.27429 12.877 5.46421 12.877 5.69848C12.877 5.93275 13.0669 6.12267 13.3011 6.12267Z" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Password
                        </a>
                    </div>

                    <div class="acctedit-formcard">
                        {{-- Account type radios --}}
                        <div class="acctedit-field">
                            <label>Account type</label>
                            <div class="acctedit-radiogroup">
                                <label class="acctedit-radio">
                                    <input wire:model.live="account_type" type="radio" value="2" name="my_account[account_type]">
                                    <span>Individual advertiser</span>
                                </label>
                                <label class="acctedit-radio">
                                    <input wire:model.live="account_type" type="radio" value="3" name="my_account[account_type]">
                                    <span>Escort agency</span>
                                </label>
                            </div>
                        </div>

                        {{-- Email + Display name on one row --}}
                        <div class="acctedit-row2">
                            <div class="acctedit-field">
                                <label for="my_account_email">Email</label>
                                <input disabled wire:model="email" class="acctedit-input" maxlength="191" type="email" id="my_account_email">
                                @error('email') <span class="acctedit-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="acctedit-field">
                                <label for="my_account_display_name">Display name</label>
                                <input wire:model="display_name" class="acctedit-input" maxlength="191" type="text" id="my_account_display_name">
                                @error('display_name') <span class="acctedit-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Username + Phone on one row --}}
                        <div class="acctedit-row2">
                            <div class="acctedit-field">
                                <label for="my_account_username">User name</label>
                                <input wire:model="username" class="acctedit-input" maxlength="60" type="text" id="my_account_username" placeholder="e.g. john.doe">
                                <small class="acctedit-hint">Letters, numbers, dots, underscores and hyphens only.</small>
                                @error('username') <span class="acctedit-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="acctedit-field">
                                <label>Phone Number</label>
                                <div class="acctedit-phone-row">
                                    {{-- wire:ignore — same reason as the previous template:
                                         debounced wire:model.live commits elsewhere morph
                                         the DOM and would silently kill the dropdown's click
                                         handler mid-interaction. --}}
                                    <div class="ev-country-select" wire:ignore>
                                        <div class="ev-country-display" id="countryCodeDisplay">
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <img id="selectedFlag" src="https://flagcdn.com/w40/{{ $countrycode ? strtolower(\App\Models\Country::where('phonecode', $countrycode)->first()?->iso ?? 'ae') : 'ae' }}.png" style="width:22px;height:14px;object-fit:cover;border-radius:2px;{{ $countrycode ? '' : 'display:none;' }}">
                                                <span id="selectedCode">{{ $countrycode ? '+' . $countrycode : 'Select' }}</span>
                                            </div>
                                            <i class="fa fa-chevron-down" style="font-size:10px;color:rgba(255,255,255,0.4);"></i>
                                        </div>
                                        <div class="ev-country-dropdown" id="countryDropdown">
                                            <input type="text" id="countrySearch" placeholder="Search country..." autocomplete="off">
                                            <div id="countryList">
                                                @foreach($countries as $country)
                                                    <div class="country-option" data-code="{{ $country->phonecode }}" data-iso="{{ strtolower($country->iso) }}" data-name="{{ $country->nicename }}">
                                                        <img src="https://flagcdn.com/w40/{{ strtolower($country->iso) }}.png" style="width:22px;height:14px;object-fit:cover;border-radius:2px;">
                                                        <span>{{ $country->nicename }}</span>
                                                        <span>+{{ $country->phonecode }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <input type="hidden" wire:model.live="countrycode" id="my_account_countrycode">
                                    </div>
                                    <input wire:model="phone" class="acctedit-input" placeholder="Phone number" type="text" id="my_account_phone" style="flex:1;">
                                </div>
                                @error('countrycode') <span class="acctedit-error">{{ $message }}</span> @enderror
                                @error('phone') <span class="acctedit-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- About me — full width. Deferred (no modifier) so
                             typing doesn't morph the country dropdown mid-click. --}}
                        <div class="acctedit-field">
                            <label for="my_account_about_me">About me</label>
                            <textarea wire:model="about_me" rows="5" class="acctedit-input" id="my_account_about_me" placeholder="Tell visitors about yourself…"></textarea>
                            @error('about_me') <span class="acctedit-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Save button sits below the form card, left-aligned. --}}
                    <div class="acctedit-save-wrap">
                        <button class="acctedit-save-btn" type="submit" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                Save
                                {{-- Thin chevron (stroke-width 1.5) — the FA
                                     fa-chevron-right glyph is a bold solid
                                     shape and reads too heavy next to the
                                     500-weight label. --}}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="vertical-align: middle;">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                            <span wire:loading.flex wire:target="save" style="display:none;align-items:center;gap:8px;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </div>
                </div>

                {{-- RIGHT COLUMN — Profile photo + Trust & Verification --}}
                <aside>
                    <div class="acctedit-sidecard">
                        <h4 class="acctedit-sidecard-title">Profile photo</h4>
                        <div class="acctedit-photo">
                            <div class="acctedit-photo-preview">
                                {{-- Same preview resolution logic as before:
                                     Livewire temp URL → stored avatar → gravatar. --}}
                                @if($avatar && is_object($avatar) && method_exists($avatar, 'temporaryUrl'))
                                    <img id="my_account_avatar_preview" src="{{ $avatar->temporaryUrl() }}" alt="Profile photo">
                                @elseif(!empty(auth()->user()->avatar))
                                    <img id="my_account_avatar_preview" src="{{ user_avatar_url(auth()->user()) }}" alt="Profile photo"
                                         onerror="this.onerror=null;this.src='https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?s=256&d=identicon'">
                                @else
                                    <img id="my_account_avatar_preview" src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?s=256&d=identicon" alt="Profile photo">
                                @endif
                            </div>

                            @if(!empty(auth()->user()->avatar))
                                <button wire:click="removePhoto" class="acctedit-photo-remove" type="button">
                                    {{-- Thin X icon in lime. --}}
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    Remove Photo
                                </button>
                            @endif

                            {{-- Full-width dark button that triggers the
                                 hidden <input type="file"> below. --}}
                            <label for="my_account_avatar" class="acctedit-photo-choose">
                                {{-- Lime picture-frame icon. --}}
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M16.625 2.625H4.375C3.4085 2.625 2.625 3.4085 2.625 4.375V16.625C2.625 17.5915 3.4085 18.375 4.375 18.375H16.625C17.5915 18.375 18.375 17.5915 18.375 16.625V4.375C18.375 3.4085 17.5915 2.625 16.625 2.625Z" stroke="#C1F11D" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7.875 9.625C8.8415 9.625 9.625 8.8415 9.625 7.875C9.625 6.9085 8.8415 6.125 7.875 6.125C6.9085 6.125 6.125 6.9085 6.125 7.875C6.125 8.8415 6.9085 9.625 7.875 9.625Z" stroke="#C1F11D" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.375 13.125L15.6747 10.4247C15.3466 10.0967 14.9015 9.91235 14.4375 9.91235C13.9735 9.91235 13.5284 10.0967 13.2003 10.4247L5.25 18.375" stroke="#C1F11D" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Choose File
                            </label>
                            <input wire:model="avatar" accept="image/jpeg,image/jpg,image/png,image/gif" type="file" name="my_account[avatar]" id="my_account_avatar"
                                   onchange="(function(el){var f=el.files&&el.files[0];if(!f)return;var img=document.getElementById('my_account_avatar_preview');if(!img)return;var r=new FileReader();r.onload=function(e){img.src=e.target.result;};r.readAsDataURL(f);})(this)">
                            @error('avatar') <span class="acctedit-error">{{ $message }}</span> @enderror

                            <p class="acctedit-photo-caption">
                                JPEG, JPG, GIF, PNG, maximum size of 4MB.<br>
                                High resolution photos perform better.
                            </p>
                        </div>
                    </div>

                    <div class="acctedit-sidecard">
                        <h4 class="acctedit-sidecard-title">
                            <span class="acctedit-trust-icon">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M23.3327 15.1666C23.3327 21 19.2493 23.9166 14.396 25.6083C14.1419 25.6944 13.8658 25.6903 13.6143 25.5966C8.74935 23.9166 4.66602 21 4.66602 15.1666V6.99996C4.66602 6.69054 4.78893 6.39379 5.00772 6.175C5.22652 5.95621 5.52326 5.83329 5.83268 5.83329C8.16602 5.83329 11.0827 4.43329 13.1127 2.65995C13.3598 2.44879 13.6743 2.33276 13.9993 2.33276C14.3244 2.33276 14.6389 2.44879 14.886 2.65995C16.9277 4.44495 19.8327 5.83329 22.166 5.83329C22.4754 5.83329 22.7722 5.95621 22.991 6.175C23.2098 6.39379 23.3327 6.69054 23.3327 6.99996V15.1666Z" stroke="#C1F11D" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10.5 14.0001L12.8333 16.3334L17.5 11.6667" stroke="#C1F11D" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            Trust &amp; Verification
                        </h4>
                        <p class="acctedit-trust-body">
                            Verified profiles with complete information receive up to 3x more views and higher client engagement.
                        </p>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
// Phone number masking based on country code
const phoneMasks = {
    '971': '## ### ####',      // UAE
    '92': '### #######',        // Pakistan
    '1': '### ### ####',        // USA/Canada
    '44': '#### ### ####',      // UK
    '91': '##### #####',        // India
    '966': '# ### ####',        // Saudi Arabia
    '974': '#### ####',         // Qatar
    '973': '#### ####',         // Bahrain
    '968': '#### ####',         // Oman
    '965': '#### ####',         // Kuwait
    '961': '# ### ###',         // Lebanon
    '962': '# #### ####',       // Jordan
    '20': '### ### ####',       // Egypt
    '90': '### ### ## ##',      // Turkey
    '33': '# ## ## ## ##',      // France
    '49': '### #######',        // Germany
    '39': '### ### ####',       // Italy
    '34': '### ### ###',        // Spain
    '7': '### ### ## ##',       // Russia
    '86': '### #### ####',      // China
    '81': '## #### ####',       // Japan
    '82': '## #### ####',       // South Korea
    '60': '## ### ####',        // Malaysia
    '65': '#### ####',          // Singapore
    '66': '## ### ####',        // Thailand
    '63': '### ### ####',       // Philippines
    '84': '### ### ####',       // Vietnam
    '62': '### ### ####',       // Indonesia
    '61': '### ### ###',        // Australia
    '64': '## ### ####',        // New Zealand
    '27': '## ### ####',        // South Africa
    '234': '### ### ####',      // Nigeria
    '254': '### ######',        // Kenya
    '55': '## ##### ####',      // Brazil
    '52': '## #### ####',       // Mexico
    '54': '## #### ####',       // Argentina
    '57': '### #######',        // Colombia
    '351': '### ### ###',       // Portugal
    '31': '## ########',        // Netherlands
    '32': '### ## ## ##',       // Belgium
    '41': '## ### ## ##',       // Switzerland
    '43': '### #######',        // Austria
    '45': '## ## ## ##',        // Denmark
    '46': '## ### ## ##',       // Sweden
    '47': '### ## ###',         // Norway
    '48': '### ### ###',        // Poland
    '30': '### ### ####',       // Greece
    '420': '### ### ###',       // Czech Republic
    '36': '## ### ####',        // Hungary
    '40': '### ### ###',        // Romania
    '380': '## ### ## ##',      // Ukraine
    '972': '## ### ####',       // Israel
    '212': '### ######',        // Morocco
    '213': '### ## ## ##',      // Algeria
    '216': '## ### ###',        // Tunisia
};

const exampleNumbers = {
    '971': '50 123 4567',
    '92': '300 1234567',
    '1': '202 555 0123',
    '44': '7700 900 123',
    '91': '98765 43210',
    '966': '5 012 3456',
    '974': '3312 3456',
    '973': '3600 1234',
    '968': '9123 4567',
    '965': '9012 3456',
    '961': '3 123 456',
    '962': '7 9012 3456',
    '20': '100 123 4567',
    '90': '532 123 45 67',
    '33': '6 12 34 56 78',
    '49': '151 2345678',
    '39': '312 345 6789',
    '34': '612 345 678',
    '7': '912 345 67 89',
    '86': '138 0013 8000',
    '81': '90 1234 5678',
    '82': '10 1234 5678',
    '60': '12 345 6789',
    '65': '8123 4567',
    '66': '81 234 5678',
    '63': '912 345 6789',
    '84': '912 345 678',
    '62': '812 345 6789',
    '61': '412 345 678',
    '64': '21 123 4567',
    '27': '82 123 4567',
    '234': '802 345 6789',
    '254': '712 345678',
    '55': '11 91234 5678',
    '52': '55 1234 5678',
    '54': '11 1234 5678',
    '57': '321 1234567',
    '351': '912 345 678',
    '31': '06 12345678',
    '32': '470 12 34 56',
    '41': '78 123 45 67',
    '43': '660 1234567',
    '45': '31 23 45 67',
    '46': '70 123 45 67',
    '47': '412 34 567',
    '48': '512 345 678',
    '30': '690 123 4567',
    '420': '601 234 567',
    '36': '20 123 4567',
    '40': '712 345 678',
    '380': '50 123 45 67',
    '972': '50 123 4567',
    '212': '612 345678',
    '213': '551 23 45 67',
    '216': '20 123 456',
};

function applyPhoneMask(input, mask) {
    const handler = function(e) {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/^0+/, '');

        let maskedValue = '';
        let valueIndex = 0;

        for (let i = 0; i < mask.length && valueIndex < value.length; i++) {
            if (mask[i] === '#') {
                maskedValue += value[valueIndex];
                valueIndex++;
            } else {
                if (valueIndex > 0) {
                    maskedValue += mask[i];
                }
            }
        }

        if (valueIndex < value.length) {
            maskedValue += ' ' + value.substring(valueIndex);
        }

        this.value = maskedValue;
        this.dispatchEvent(new Event('input', { bubbles: true }));
    };

    if (input._phoneMaskHandler) {
        input.removeEventListener('input', input._phoneMaskHandler);
    }
    input._phoneMaskHandler = handler;
    input.addEventListener('input', handler);
}

function updatePhoneMask(countryCode, phoneInputId) {
    const phoneInput = document.getElementById(phoneInputId);
    if (!phoneInput) return;

    if (countryCode && phoneMasks[countryCode]) {
        applyPhoneMask(phoneInput, phoneMasks[countryCode]);

        let placeholder = exampleNumbers[countryCode];
        if (!placeholder) {
            placeholder = phoneMasks[countryCode].replace(/#/g, '0');
        }
        phoneInput.setAttribute('placeholder', placeholder);
    } else {
        if (phoneInput._phoneMaskHandler) {
            phoneInput.removeEventListener('input', phoneInput._phoneMaskHandler);
            phoneInput._phoneMaskHandler = null;
        }
        phoneInput.setAttribute('placeholder', 'Phone number');
    }
}

function initAccountCountryDropdown() {
    var hiddenInput = document.getElementById('my_account_countrycode');
    if (hiddenInput && hiddenInput.value) {
        updatePhoneMask(hiddenInput.value, 'my_account_phone');
    }

    if (window.__acctCountryDropdownBound) return;
    window.__acctCountryDropdownBound = true;

    function $(id) { return document.getElementById(id); }
    function closeDropdown() {
        var dd = $('countryDropdown');
        if (dd) dd.style.display = 'none';
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest && e.target.closest('#countryCodeDisplay')) {
            e.stopPropagation();
            var dd = $('countryDropdown');
            var trig = $('countryCodeDisplay');
            if (!dd) return;
            var isOpen = dd.style.display === 'block';
            dd.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) {
                var search = $('countrySearch');
                var list = $('countryList');
                if (search) search.value = '';
                if (list) list.querySelectorAll('.country-option').forEach(function (opt) { opt.style.display = 'flex'; });
                if (trig) {
                    var rect = trig.getBoundingClientRect();
                    var spaceBelow = window.innerHeight - rect.bottom;
                    var spaceAbove = rect.top;
                    var ddHeight = dd.offsetHeight || 280;
                    if (spaceBelow < ddHeight && spaceAbove > spaceBelow) {
                        dd.style.top = 'auto';
                        dd.style.bottom = '100%';
                        dd.style.marginBottom = '4px';
                        dd.style.marginTop = '';
                    } else {
                        dd.style.top = '100%';
                        dd.style.bottom = 'auto';
                        dd.style.marginTop = '4px';
                        dd.style.marginBottom = '';
                    }
                }
                if (search) { try { search.focus(); } catch (_) {} }
            }
            return;
        }

        var option = e.target.closest && e.target.closest('#countryList .country-option');
        if (option) {
            var code = option.dataset.code;
            var iso = option.dataset.iso;
            var flag = $('selectedFlag');
            var label = $('selectedCode');
            var hidden = $('my_account_countrycode');
            if (flag) {
                flag.src = 'https://flagcdn.com/w40/' + iso + '.png';
                flag.style.display = 'block';
            }
            if (label) label.textContent = '+' + code;
            if (hidden) {
                hidden.value = code;
                hidden.dispatchEvent(new Event('input',  { bubbles: true }));
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (typeof updatePhoneMask === 'function') {
                updatePhoneMask(code, 'my_account_phone');
            }
            closeDropdown();
            var search = $('countrySearch');
            if (search) search.value = '';
            var list = $('countryList');
            if (list) list.querySelectorAll('.country-option').forEach(function (opt) { opt.style.display = 'flex'; });
            return;
        }

        var dd = $('countryDropdown');
        var trig = $('countryCodeDisplay');
        if (dd && trig && !dd.contains(e.target) && !trig.contains(e.target)) {
            dd.style.display = 'none';
        }
    });

    document.addEventListener('input', function (e) {
        if (!e.target || e.target.id !== 'countrySearch') return;
        var term = (e.target.value || '').toLowerCase();
        document.querySelectorAll('#countryList .country-option').forEach(function (opt) {
            var name = (opt.dataset.name || '').toLowerCase();
            var code = (opt.dataset.code || '');
            opt.style.display = (name.indexOf(term) !== -1 || code.indexOf(term) !== -1) ? 'flex' : 'none';
        });
    });
}

document.addEventListener('DOMContentLoaded', initAccountCountryDropdown);
document.addEventListener('livewire:navigated', initAccountCountryDropdown);
document.addEventListener('livewire:initialized', initAccountCountryDropdown);
setTimeout(initAccountCountryDropdown, 100);
</script>
@endpush
