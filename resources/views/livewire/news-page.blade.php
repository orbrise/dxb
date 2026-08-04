@section('headerform')
@include('components.layouts.headerform')
@endsection

@push('js')
{{-- Disable Turbolinks on this page.
     `app2.js` (loaded by the legacy `app` layout) bundles Turbolinks, which
     intercepts every internal link click and does its own SPA navigation by
     swapping <body> only — leaving this page's <head> (Bootstrap 3 + app2/3/4
     stylesheets, news-specific scripts) attached when the user clicks
     ESCORTS to leave for the listing page. The listing page then renders
     with the wrong stylesheet set: Bootstrap 4 (app.min.css) is missing,
     and the Advanced Search modal has no `position:fixed` rule, so it
     renders as an unstyled black backdrop. The "Detected multiple
     instances of Livewire/Alpine" warnings in the console are the same
     symptom — old instances stay alive in the leftover head.

     Setting data-turbolinks="false" on <body> opts every link on this
     page out of Turbolinks, forcing real browser navigation (full reload).
     We do it as early as possible so it lands before the user can click. --}}
<script>
    if (document.body) document.body.setAttribute('data-turbolinks', 'false');
    else document.addEventListener('DOMContentLoaded', function () {
        document.body.setAttribute('data-turbolinks', 'false');
    });
</script>
@endpush

@push('css')
{{-- evoory-theme.css carries the .ev-header / .ev-logo / .ev-header-tab styles
     used by the included `components.layouts.header-evoory` below. Loaded here
     because this page renders on the legacy `app` layout (which does not load
     evoory-theme.css by default), but we still want the same top header bar
     as the home-page evoory layout. --}}
<link rel="stylesheet" href="{{ asset('assets/css/evoory-theme.css') }}?v=20260416-1">
<style>
/* === Evoory Dark Theme === */
body { background: #000 !important; }
/* Hide the legacy layout's top <header id="header"> on this page so we don't
   render two headers (legacy layout one + evoory inline one). News-page also
   has its own <header id="header"> further down for the city/filters bar — we
   target only the legacy layout one (direct child of <body>). */
body > header#header { display: none !important; }
#header { margin-bottom: 0px !important; }
#footer { background: #0D1011 !important; border-top: 0px !important; }
#footer .list-inline li { margin-bottom: 0px !important; }

/* Header - Evoory style */
.navbar.navbar-inverse { background: #0D1011 !important; border: none !important; }
.logo.navbar-brand, .logo2.navbar-brand { display: none !important; }
.navbar-header::before { display: none !important; }
.auth-button-group { gap: 10px !important; }
.auth-button-group .btn-navbar-header,
.auth-button-group .button_to .btn-navbar-header {
    border-radius: 8px !important;
    border: 1px solid #2a2a2a !important;
    border-right: 1px solid #2a2a2a !important;
    background: transparent !important;
    color: #ccc !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    padding: 10px 20px !important;
    transition: all 0.2s ease;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}
.auth-button-group .btn-navbar-header:hover,
.auth-button-group .button_to .btn-navbar-header:hover {
    color: #fff !important;
    background: #1a1a1a !important;
    border-color: #ccc !important;
}
.auth-button-group .btn-navbar-header:first-child {
    border-radius: 8px !important;
}

/* Guest header buttons (Language, Sign in) */
.header-nav-buttons--separated {
    position: static !important;
    display: flex !important;
    align-items: center;
    gap: 10px !important;
    margin-left: auto !important;
}
.header-nav-buttons--separated .btn-navbar-header {
    border-radius: 8px !important;
    border: 1px solid #2a2a2a !important;
    background: transparent !important;
    color: #ccc !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    padding: 10px 20px !important;
    transition: all 0.2s ease;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}
.header-nav-buttons--separated .btn-navbar-header:hover {
    color: #fff !important;
    background: #1a1a1a !important;
    border-color: #ccc !important;
}
.dropdown--lang .btn-navbar-header {
    background: transparent !important;
    border: 1px solid #2a2a2a !important;
    border-radius: 8px !important;
}

/* Escorts / What's New tabs next to logo */
#main-nav {
    display: inline-flex !important;
    background: #1D2224;
    border-radius: 5px;
    padding: 0;
    margin: 0 !important;
    margin-left: 16px !important;
    gap: 0;
    border: none !important;
    position: absolute;
    left: 220px;
    top: 50%;
    transform: translateY(-50%);
}
.navbar-header { position: relative !important; }
#main-nav .btn,
#main-nav .btn.lead {
    color: #C1F11D !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    padding: 10px 20px !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #1D2224 !important;
    border: none !important;
    border-radius: 0 !important;
    position: relative;
    line-height: 1.2 !important;
}
#main-nav .btn:first-child { border-radius: 5px 0 0 5px !important; }
#main-nav .btn:last-child { border-radius: 0 5px 5px 0 !important; }
#main-nav .btn.selected {
    background: #262C2F !important;
    color: #C1F11D !important;
}
#main-nav .btn.selected::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid #262C2F;
}

/* News nav bar */
.nav-bar { padding: 8px 0 !important; background: #111 !important; }
.nav-bar .form-control { background: #1a1a1a !important; color: #fff !important; }
.nav-bar .btn-dark { box-shadow: none !important;border: 0px solid #2a2a2a !important;background: #1a1a1a !important; color: #ccc !important; }
.nav-bar .btn-dark:hover { border-color:none !important; color: #fff !important; }
.nav-bar .btn-dark.active {     background: #1a1a1a !important; color: #fff !important; border-color: #2a2a2a !important; }
/* Activity stream nav buttons */
.activity-stream-nav .btn-dark { background: #1D2224!important border: none !important; color: #C1F11D !important; padding: 8px 15px !important; }
.activity-stream-nav .btn-dark.active { background: #1D2224 !important; color: #fff !important; border: none !important; }
.activity-stream-nav .btn-dark:not(.active) { color: #C1F11D !important; }
.activity-stream-nav .btn-dark:focus { outline: none !important; box-shadow: none !important; }
.activity-stream-nav .btn-dark:focus-visible { outline: 2px solid #C1F11D !important; outline-offset: 2px !important; }
/* Content area */
.body { background: #000 !important; }
.page-title h1 { color: #fff !important; }
/* Green link color on the news page only.
   Two rules:
   1. .ev-news-root a — fallback for older browsers without :has() support.
      Covers links inside the page content wrapper (header tabs, news items).
   2. body:has(.ev-news-root) a — modern browsers (Chrome 105+, Safari 15.4+,
      Firefox 121+). Matches any anchor when the body contains the news page
      wrapper, so it also picks up the footer links that are rendered by the
      layout outside .ev-news-root. Cannot leak to the homepage because the
      selector only fires on pages whose DOM contains .ev-news-root. */
.ev-news-root a:not(.ev-mobile-bottom-nav__item) { color: #C1F11D !important; }
body:has(.ev-news-root) a:not(.ev-mobile-bottom-nav__item) { color: #C1F11D !important; }

/* Exclude the top header's auth nav (My Profile / My Account / Sign Out
   and the Sign In / Language buttons) from the lime-anchor rule above —
   on the listing pages those entries render as white text via .ev-nav-link,
   and the news page should match instead of repainting them lime. */
.ev-news-root .ev-header .ev-nav-link,
.ev-news-root .ev-header .ev-nav-link:link,
.ev-news-root .ev-header .ev-nav-link:visited { color: #ffffff !important; }
.ev-news-root .ev-header .ev-nav-link:hover,
.ev-news-root .ev-header .ev-nav-link:focus { color: #C1F11D !important; }

/* Breathing room between the sticky filter bar (#header .nav-bar) and the
   first content row. Without this the page title ("Delhi Escort News", etc.)
   visually butts right up against the bar. */
.ev-news-content { padding-top: 24px !important; }
@media (max-width: 767px) {
    .ev-news-content { padding-top: 16px !important; }
}

/* Newsletter subscribe modal styles (mirrors the home page's scoped copy
   under .ev-empty-listings-wrap, repeated unprefixed here so the Subscribe
   popup opened from the news page top-right button gets the same look). */
[x-cloak] { display: none !important; }
.ev-news-root .ev-modal-overlay {
    background: rgba(0,0,0,0.85);
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: 99999;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 3rem 1rem;
}
.ev-news-root .ev-modal {
    background: #1a1a1a;
    color: #fff;
    border-radius: 12px;
    border: 1px solid #2a2a2a;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    width: 100%;
    max-width: 500px;
}
.ev-news-root .ev-modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #2a2a2a;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ev-news-root .ev-modal-header h2 {
    margin: 0;
    font-size: 1.25rem;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.ev-news-root .ev-modal-close {
    background: #222;
    border: 1px solid #2a2a2a;
    color: #fff;
    font-size: 1.25rem;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ev-news-root .ev-modal-close:hover { background: #2a2a2a; }
.ev-news-root .ev-modal-body { padding: 1.5rem; color: #fff; }
.ev-news-root .ev-modal-footer {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid #2a2a2a;
    text-align: right;
}
.ev-news-root .ev-search-input {
    display: flex;
    border: 1px solid #2a2a2a;
    border-radius: 8px;
    overflow: hidden;
    background: #111;
}
.ev-news-root .ev-search-input span {
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    color: #fff;
}
.ev-news-root .ev-search-input input {
    flex: 1;
    padding: 0.75rem;
    background: transparent;
    border: none;
    color: #fff;
    outline: none;
    font-size: 0.95rem;
}
.ev-news-root .ev-search-input input::placeholder { color: #666; }
.ev-news-root .ev-search-input button {
    padding: 0.75rem 1rem;
    background: transparent;
    border: none;
    color: #C1F11D;
    cursor: pointer;
}
.ev-news-root .ev-city-tag {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    padding: 0.75rem 1rem;
    background: #111;
    border-radius: 8px;
    border: 1px solid #2a2a2a;
    color: #fff;
}
.ev-news-root .ev-city-tag button { color: #C1F11D; }
.ev-news-root .ev-dropdown-results {
    position: absolute;
    width: 100%;
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
    background: #111;
    border: 1px solid #2a2a2a;
    border-radius: 8px;
    margin-top: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.ev-news-root .ev-dropdown-results button {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    background: transparent;
    color: #fff;
    border: none;
    border-bottom: 1px solid #2a2a2a;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
}
.ev-news-root .ev-dropdown-results button:hover { background: #222; }
.ev-news-root .ev-buy-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #C1F11D;
    color: #000;
    border: none;
    border-radius: 21.5px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
}
.ev-news-root .ev-buy-btn:hover { background: #d4f84d; }

/* Activity stream items */
.activity-stream { color: #fff; }
.activity-stream li { border-color: #2a2a2a !important; }
.activity-record { background: transparent !important; color: #fff !important; }
.activity-record .headline { color: #fff !important; }
.activity-record .headline a { color: #C1F11D !important; }
.date .day { color: #C1F11D !important; }
.date .month { color: #999 !important; }

/* Reply/answer boxes */
.listing-reply, .listing-reply > div { background: #1a1a1a !important; color: #fff !important; border-color: #2a2a2a !important; }

/* Subscribe button */
.subscribe-btn-wrapper .btn-primary { background: #C1F11D !important; color: #000 !important; border: none !important; border-radius: 8px; }
.subscribe-btn-wrapper .btn-primary:hover { background: #d4f84d !important; }

/* Text colors */
h1, h2, h3, h4, .h3 { color: #fff !important; }

.nav-bar {
    padding: 5px 0 !important;
}


.activity-stream .photo img,
.activity-stream .right-thumbs img {
    width: 143.857143px !important;
    height: 148.57142857px !important;
    object-fit: cover;
}

/* Fix Font Awesome icons in news navigation and content */
.activity-stream-nav .fas,
.activity-stream-nav .fa,
.activity-record .fas,
.activity-record .fa {
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
    display: inline-block !important;
    margin-right: 5px;
}

/* Specific icon fixes */
.fa-certificate:before {
    content: "\f0a3";
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
}

.fa-heart:before {
    content: "\f004";
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
}

.fa-question-circle:before {
    content: "\f059";
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
}

/* Show country flags using flag CDN */
.tt-suggestion .fs,
.tt-dropdown-menu .tt-dataset-location .fs {
    display: inline-block !important;
    width: 20px !important;
    height: 15px !important;
    margin-right: 8px !important;
    vertical-align: middle !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    border-radius: 2px !important;
    visibility: visible !important;
    overflow: hidden !important;
}

/* Flag images from flagcdn.com */
.tt-suggestion .fs.ae { background-image: url(https://flagcdn.com/16x12/ae.png) !important; }
.tt-suggestion .fs.ie { background-image: url(https://flagcdn.com/16x12/ie.png) !important; }
.tt-suggestion .fs.gb,
.tt-suggestion .fs.uk { background-image: url(https://flagcdn.com/16x12/gb.png) !important; }
.tt-suggestion .fs.us { background-image: url(https://flagcdn.com/16x12/us.png) !important; }
.tt-suggestion .fs.ca { background-image: url(https://flagcdn.com/16x12/ca.png) !important; }
.tt-suggestion .fs.au { background-image: url(https://flagcdn.com/16x12/au.png) !important; }
.tt-suggestion .fs.de { background-image: url(https://flagcdn.com/16x12/de.png) !important; }
.tt-suggestion .fs.fr { background-image: url(https://flagcdn.com/16x12/fr.png) !important; }
.tt-suggestion .fs.es { background-image: url(https://flagcdn.com/16x12/es.png) !important; }
.tt-suggestion .fs.it { background-image: url(https://flagcdn.com/16x12/it.png) !important; }
.tt-suggestion .fs.nl { background-image: url(https://flagcdn.com/16x12/nl.png) !important; }
.tt-suggestion .fs.be { background-image: url(https://flagcdn.com/16x12/be.png) !important; }
.tt-suggestion .fs.ch { background-image: url(https://flagcdn.com/16x12/ch.png) !important; }
.tt-suggestion .fs.at { background-image: url(https://flagcdn.com/16x12/at.png) !important; }
.tt-suggestion .fs.se { background-image: url(https://flagcdn.com/16x12/se.png) !important; }
.tt-suggestion .fs.no { background-image: url(https://flagcdn.com/16x12/no.png) !important; }
.tt-suggestion .fs.dk { background-image: url(https://flagcdn.com/16x12/dk.png) !important; }
.tt-suggestion .fs.fi { background-image: url(https://flagcdn.com/16x12/fi.png) !important; }
.tt-suggestion .fs.pl { background-image: url(https://flagcdn.com/16x12/pl.png) !important; }
.tt-suggestion .fs.cz { background-image: url(https://flagcdn.com/16x12/cz.png) !important; }
.tt-suggestion .fs.gr { background-image: url(https://flagcdn.com/16x12/gr.png) !important; }
.tt-suggestion .fs.pt { background-image: url(https://flagcdn.com/16x12/pt.png) !important; }
.tt-suggestion .fs.tr { background-image: url(https://flagcdn.com/16x12/tr.png) !important; }
.tt-suggestion .fs.ru { background-image: url(https://flagcdn.com/16x12/ru.png) !important; }
.tt-suggestion .fs.jp { background-image: url(https://flagcdn.com/16x12/jp.png) !important; }
.tt-suggestion .fs.cn { background-image: url(https://flagcdn.com/16x12/cn.png) !important; }
.tt-suggestion .fs.in { background-image: url(https://flagcdn.com/16x12/in.png) !important; }
.tt-suggestion .fs.sg { background-image: url(https://flagcdn.com/16x12/sg.png) !important; }
.tt-suggestion .fs.hk { background-image: url(https://flagcdn.com/16x12/hk.png) !important; }
.tt-suggestion .fs.th { background-image: url(https://flagcdn.com/16x12/th.png) !important; }
.tt-suggestion .fs.my { background-image: url(https://flagcdn.com/16x12/my.png) !important; }
.tt-suggestion .fs.ph { background-image: url(https://flagcdn.com/16x12/ph.png) !important; }
.tt-suggestion .fs.id { background-image: url(https://flagcdn.com/16x12/id.png) !important; }
.tt-suggestion .fs.nz { background-image: url(https://flagcdn.com/16x12/nz.png) !important; }
.tt-suggestion .fs.za { background-image: url(https://flagcdn.com/16x12/za.png) !important; }
.tt-suggestion .fs.br { background-image: url(https://flagcdn.com/16x12/br.png) !important; }
.tt-suggestion .fs.mx { background-image: url(https://flagcdn.com/16x12/mx.png) !important; }
.tt-suggestion .fs.ar { background-image: url(https://flagcdn.com/16x12/ar.png) !important; }

/* Hide the green circle that typeahead adds */
.tt-suggestion .tt-highlight,
.tt-suggestion strong {
    font-weight: normal;
}

.tt-suggestion::before,
.tt-dataset-location::before {
    display: none !important;
    content: none !important;
}

/* Disable autocomplete hint */
.tt-hint,
input.tt-hint,
.twitter-typeahead .tt-hint,
.typeahead-city-wrapper .tt-hint,
.typeahead-city-wrapper input.tt-hint {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    position: absolute !important;
    left: -9999px !important;
    pointer-events: none !important;
}

/* Ensure main input stays visible */
.typeahead-city-wrapper .tt-input {
    display: block !important;
}

.typeahead-city-wrapper .tt-hint {
    display: none !important;
}

/* News page city search dropdown - themed scrollbar */
#news_cityappend::-webkit-scrollbar { width: 6px; }
#news_cityappend::-webkit-scrollbar-track { background: transparent; }
#news_cityappend::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 3px; }
#news_cityappend::-webkit-scrollbar-thumb:hover { background: #3a3a3a; }
#news_cityappend { scrollbar-width: thin; scrollbar-color: #2a2a2a transparent; }

/* Show country flags in dropdown */
.tt-suggestion .flag-icon {
    display: inline-block !important;
    width: 20px;
    height: 15px;
    margin-right: 8px;
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
}

/* Reply box arrow style */
.listing-reply > div:before {
    font-family: FontAwesome !important;
    content: "";
    color: rgba(0, 0, 0, .2);
    font-size: 1.5em;
    position: absolute;
    left: -7px;
    top: 0;
}


 #main-nav a.selected:before {width: 16px;border-top-color:transparent !important;}

/* Gender select button — widen to match the dropdown's width so the button
   and its open menu align (dropdown has min-width: 168px). */
form.activity-nav-form .activity-search-gender { min-width: 200px; }
form.activity-nav-form button.search-bar--gender {
    min-width: 200px;
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
form.activity-nav-form button.search-bar--gender:focus,
form.activity-nav-form button.search-bar--gender:focus-visible,
form.activity-nav-form button.search-bar--gender:active,
form.activity-nav-form input.search-bar--city:focus,
form.activity-nav-form input.search-bar--city:focus-visible {
    outline: none !important;
    box-shadow: none !important;
}
.dropdown-gender-menu { min-width: 200px !important; }

/* Bootstrap 4 JS (loaded by the app layout) toggles `.show` on the dropdown
   and its menu, but the loaded CSS bundle is Bootstrap 3 — which only has
   `.open > .dropdown-menu { display: block }`. Without a matching `.show`
   rule the click handler fires, classes get toggled, but the menu stays
   hidden. Add the Bootstrap 4 equivalent so the gender dropdown opens. */
.dropdown.show > .dropdown-menu,
.dropdown-menu.show { display: block; }

/* Belt-and-suspenders: hide the mobile-only ESCORTS/WHAT'S NEW pill bar and
   "Back" back-bar on desktop. Bootstrap's `.visible-xs` rule should already
   do this, but the duplicate row was leaking onto the desktop view — force
   it gone with an explicit min-width override. */
@media (min-width: 768px) {
    div.visible-xs,
    .ev-news-back-bar { display: none !important; }

    /* Lay the search form and the activity-stream-nav (All news / Escorts /
       Reviews / Questions) on the same horizontal row instead of stacking. */
    header#header .nav-bar .container-fluid {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    header#header form.activity-nav-form { margin: 0; }
    header#header .activity-stream-nav.btn-group { margin: 0; }

    /* Activity stream layout — explicit so we don't silently depend on the
       legacy `.activity-stream-full` floats in app2.css (which were getting
       clobbered, leaving the date badge unstyled and right-thumb images
       dropping below the text). Date badge column on the left, the rest of
       the row to its right; inside, photo floats left, right-thumbs floats
       right, and .activity-content fills the remaining space. */
    ul.activity-stream.activity-stream-full {
        list-style: none !important;
        padding: 0 !important;
        margin: 0;
    }
    ul.activity-stream.activity-stream-full > li {
        display: block;
        overflow: hidden;
        padding: 20px 0;
        margin: 0;
        border-top: 1px solid #2a2a2a;
    }
    ul.activity-stream.activity-stream-full > li:first-child { border-top: none; }

    .activity-stream-full .date-wrapper {
        float: left;
        width: 80px;
        padding: 0 16px 0 0;
        box-sizing: content-box;
    }
    .activity-stream-full .date-wrapper .date {
        background: #1a1a1a;
        border-radius: 6px;
        padding: 12px 0;
        text-align: center;
    }
    .activity-stream-full .date-wrapper .date .day {
        display: block;
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        line-height: 1.1;
    }
    .activity-stream-full .date-wrapper .date .month {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        color: #999;
        margin-top: 2px;
    }

    .activity-stream-full .activity-record-wrapper {
        overflow: hidden;
        padding: 0 16px;
    }
    .activity-stream-full .activity-record { overflow: hidden; padding: 0 !important; border-top: none !important; }
    .activity-stream-full .activity-record .activity-row { overflow: hidden; }
    .activity-stream-full .activity-record .headline { margin: 0 0 12px; }
    .activity-stream-full .activity-record .photo {
        float: left;
        margin: 0 16px 8px 0;
        padding: 0;
        text-align: left;
    }
    .activity-stream-full .activity-record .right-thumbs {
        float: right;
        display: flex;
        gap: 6px;
        margin: 0 0 8px 16px;
    }
    .activity-stream-full .activity-record .activity-content {
        overflow: hidden;
        word-break: break-word;
    }
}

/* ═══ MOBILE VIEW ═══ */
@media (max-width: 767px) {
    /* Hide ESCORTS/WHAT'S NEW mobile tabs - we have back bar */
    div.visible-xs { display: none !important; }

    /* Center the evoory logo in the top header on mobile. The desktop layout
       uses justify-between (logo left, auth nav right); on mobile the auth
       nav and ev-header-tabs are hidden, leaving the logo stranded on the
       left. Override to center-align so the logo sits in the middle. */
    .ev-news-root .ev-header .ev-container > .ev-flex.ev-justify-between {
        justify-content: center !important;
    }
    .ev-news-root .ev-header .ev-logo { margin: 0 auto; }

    /* Nav bar */
    header#header .nav-bar { background: #000 !important; padding: 8px 16px !important; }
    header#header .nav-bar .container-fluid { padding: 0 !important; }

    /* Gender + City row. All three controls (gender button, city input,
       activity-nav filter buttons below) share the same box: 40px height,
       5px radius, 13px text, matching padding — so the "What's New"
       filter bar reads as one grid of equal-sized chips. */
    form.activity-nav-form { display: flex !important; gap: 8px !important; margin-bottom: 10px !important; flex-wrap: nowrap !important; }
    form.activity-nav-form .form-group { flex: 1 !important; margin: 0 !important; float: none !important; }
    form.activity-nav-form .activity-search-gender { flex: none !important; width: auto !important; min-width: 0 !important; }
    form.activity-nav-form button.search-bar--gender {
        background: #1a1a1a !important;
        border: 1px solid #333 !important;
        border-radius: 5px !important;
        color: #fff !important;
        font-size: 13px !important;
        height: 40px !important;
        line-height: 1 !important;
        padding: 0 12px !important;
        min-width: 0 !important;
        width: auto !important;
        box-sizing: border-box !important;
    }
    form.activity-nav-form input.search-bar--city {
        background: #1a1a1a !important;
        border: 1px solid #333 !important;
        border-radius: 5px !important;
        font-size: 13px !important;
        height: 40px !important;
        line-height: 1 !important;
        padding: 0 12px 0 36px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Activity stream nav - horizontal pills, same 40px height as row above. */
    div.activity-stream-nav.btn-group {
        display: flex !important;
        gap: 8px !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        padding-bottom: 8px !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    div.activity-stream-nav.btn-group::-webkit-scrollbar { display: none; }
    div.activity-stream-nav a.btn.btn-dark {
        white-space: nowrap !important;
        height: 40px !important;
        line-height: 1 !important;
        padding: 0 14px !important;
        font-size: 13px !important;
        border-radius: 5px !important;
        background: transparent !important;
        border: 1px solid #333 !important;
        color: #999 !important;
        flex-shrink: 0 !important;
        box-shadow: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        box-sizing: border-box !important;
    }
    div.activity-stream-nav a.btn.btn-dark.active {
        border-color: #C1F11D !important;
        color: #C1F11D !important;
        background: transparent !important;
    }
    div.activity-stream-nav a.btn.btn-dark:not(.active) {
        color: #999 !important;
    }

    /* Main content */
    div.container-fluid { padding: 0 16px !important; }

    /* Hide subscribe button and page title */
    .subscribe-btn-wrapper { display: none !important; }
    a.page-title { display: none !important; }

    /* Date headers */
    .date-wrapper .date {
        background: #1a1a1a !important;
        border-radius: 5px !important;
        padding: 8px 16px !important;
        text-align: center !important;
        margin: 16px 0 12px !important;
    }
    .date-wrapper .date .day {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #fff !important;
    }
    .date-wrapper .date .month {
        font-size: 12px !important;
        color: #999 !important;
        text-transform: uppercase !important;
    }

    /* Activity items */
    ul.activity-stream { padding: 0 !important; list-style: none !important; }
    ul.activity-stream li { border-color: #1a1a1a !important; padding: 12px 0 !important; }
    .activity-record { padding: 0 !important; }
    .activity-record .headline { font-size: 14px !important; margin-bottom: 8px !important; }
    .activity-record .photo img,
    .activity-stream .photo img,
    .activity-stream .right-thumbs img {
        width: 80px !important;
        height: 80px !important;
        border-radius: 5px !important;
        object-fit: cover !important;
    }

    /* Lay the main photo and the two thumbs side-by-side on mobile instead of
       stacking them vertically. The activity-content div below (block) will
       naturally start on a new line beneath the image row. */
    .activity-record .photo {
        display: inline-block !important;
        vertical-align: top;
        margin: 0 6px 8px 0 !important;
        padding: 0 !important;
    }
    .activity-record .right-thumbs {
        display: inline-flex !important;
        gap: 6px !important;
        vertical-align: top;
        margin: 0 0 8px 0 !important;
    }
    .activity-record .right-thumbs .hidden-md { display: inline-block !important; }

    /* Reply boxes */
    .listing-reply, .listing-reply > div {
        border-radius: 5px !important;
        padding: 8px 12px !important;
        font-size: 13px !important;
    }

    /* Dropdown menu */
    .dropdown-menu { background: #1a1a1a !important; border: 1px solid #333 !important; border-radius: 5px !important; }
    .dropdown-menu li a { color: #fff !important; padding: 8px 16px !important; }
    .dropdown-menu li a:hover { background: #2a2a2a !important; }
    .dropdown-menu li.active a { color: #C1F11D !important; }
}
</style>
@endpush

<div class="ev-news-root">
{{-- Evoory top header bar (logo + ESCORTS/WHAT'S NEW tabs + Language + Sign in).
     Inlined here because this page is rendered with the legacy `app` layout
     (which loads Bootstrap 3 styles needed for the city dropdown / activity
     stream below). The legacy layout has its own header — we hide it via CSS
     in @push('css') above and render this evoory-styled bar in its place. --}}
@php
    $newsCitySlug = strtolower($selectedcity ?? 'dubai');
    $newsGender = $gender ?? 'female';
@endphp
<header class="ev-header">
    <div class="ev-container">
        <div class="ev-flex ev-items-center ev-justify-between">
            <div class="ev-flex ev-items-center">
                @if(isset($setting) && $setting->app_logo)
                    <a href="/" class="ev-logo"><img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}" style="height:36px;width:auto;display:block;"></a>
                @else
                    <a href="/" class="ev-logo">{{ ($setting->app_name ?? null) ?: 'evoory' }}</a>
                @endif
                <div class="ev-header-tabs">
                    <a href="/{{ $newsGender }}-escorts-in-{{ $newsCitySlug }}" class="ev-header-tab">ESCORTS</a>
                    <a href="/{{ $newsGender }}-escort-news-in-{{ $newsCitySlug }}" class="ev-header-tab active">WHAT'S NEW</a>
                </div>
            </div>
            <nav class="ev-nav">
                @guest
                <div class="ev-relative">
                    <button class="ev-nav-link ev-lang-btn" type="button" aria-label="Select Language">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 8l6 6"></path><path d="M4 14l6-6 2-3"></path><path d="M2 5h12"></path>
                            <path d="M7 2h1"></path><path d="M22 22l-5-10-5 10"></path><path d="M14 18h6"></path>
                        </svg>
                        Language
                    </button>
                </div>
                <a href="{{ route('sign-in') }}" class="ev-nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Sign in
                </a>
                @endguest
                @auth
                    @if(Auth::user()->type != 1)
                        @php $userProfile = auth()->user()->profiles->first(); @endphp
                        @if($userProfile)
                        <a href="{{ url('my-profile/'.$userProfile->slug.'/'.$userProfile->id) }}" class="ev-nav-link">
                            {{-- Brand mark instead of the generic person SVG,
                                 matching the rest of the site (see
                                 header-evoory.blade.php). This page renders
                                 its own header markup inline because it uses
                                 the legacy `app` layout, so the icon swap has
                                 to be repeated here too. --}}
                            <img src="https://assets.evoory.com/assets/newtheme/evooryicon.svg"
                                 alt=""
                                 aria-hidden="true"
                                 width="18"
                                 height="18"
                                 style="display:inline-block;vertical-align:middle;">
                            My Profile
                        </a>
                        @endif
                        <a href="{{ url('my-account') }}" class="ev-nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                            My Account
                        </a>
                    @endif
                    {{-- Sign Out matches header-evoory.blade.php — the news page
                         inlines its own header so the entry has to be repeated
                         here. Without it the auth nav was missing a way out. --}}
                    <form method="post" action="{{ url('sign_out') }}" style="display:inline">
                        {{ csrf_field() }}
                        <button type="submit" class="ev-nav-link" style="padding: 12px 20px;background:transparent;border:none;cursor:pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Sign Out
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </div>
</header>

{{-- Mobile Back Bar --}}
<div class="ev-news-back-bar" style="display:none;">
    <div style="display:flex;align-items:center;justify-content:center;position:relative;padding:12px 16px;background:#1f2222;">
        <a href="/{{ $gender ?? 'female' }}-escorts-in-{{ strtolower($selectedcity ?? 'dubai') }}" style="position:absolute;left:16px;color:#C1F11D;text-decoration:none;font-size:13px;">
            <i class="fa fa-angle-left"></i> Back
        </a>
        <span style="color:#fff;font-size:15px;font-weight:500;">What's New</span>
    </div>
</div>
<style>@media screen and (max-width:768px){.ev-news-back-bar{display:block!important}}</style>

{{-- ESCORTS / WHAT'S NEW Navigation Tabs - Mobile Only --}}
<div class="visible-xs" style="padding: 8px 15px 0 15px; margin: 0; width: 100%;">
    <div class="btn-group" role="group" style="display: flex !important; width: 100%; margin: 0; border-radius: 4px; overflow: visible; position: relative;">
        <a class="btn" href="/{{ $gender ?? 'female' }}-escorts-in-{{ strtolower($selectedcity ?? 'dubai') }}" 
           style="flex: 1; background-color: #1D2224 !important; color: #C1F11D !important; font-weight: normal; font-size: 13px; text-transform: uppercase; padding: 8px 15px; border: none; border-radius: 4px 0 0 4px; text-align: center;">
            ESCORTS
        </a>
        <a class="btn" href="/{{ $gender ?? 'female' }}-escort-news-in-{{ strtolower($selectedcity ?? 'dubai') }}"
           style="flex: 1; background-color: #262C2F !important; color: #C1F11D !important; font-weight: normal; font-size: 13px; text-transform: uppercase; padding: 8px 15px; border: none; border-radius: 0 4px 4px 0; text-align: center; position: relative;">
            WHAT'S NEW
            <span style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid #262C2F;"></span>
        </a>
    </div>
</div>

<header id="header" style="margin-bottom:0px">
    <div class="nav-bar">
        <div class="container-fluid">
            <form class="simple_form activity-nav-form dark-form search-form form-inline" 
                  id="new_q" 
                  activity-nav-form="true"
                  novalidate="novalidate" 
                  action="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" 
                  accept-charset="UTF-8" 
                  method="get">
                
                <!-- Gender Dropdown -->
                <div class="form-group dropdown activity-search-gender">
                    <button class="btn btn-dark search-bar--gender" data-toggle="dropdown" tabindex="3" type="button">
                        {{ ucfirst($gender) }} escorts <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu nav nav-pills nav-stacked nav-dark dropdown-gender-menu">
                        <li class="{{ $gender === 'female' ? 'active' : '' }}">
                            <a href="/female-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" title="Escorts in {{ $cityname }}">Female escorts</a>
                        </li>
                        <li class="{{ $gender === 'male' ? 'active' : '' }}">
                            <a href="/male-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" title="Gay escorts in {{ $cityname }}">Male escorts</a>
                        </li>
                        <li class="{{ $gender === 'shemale' ? 'active' : '' }}">
                            <a href="/shemale-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" title="Escort shemales in {{ $cityname }}">Shemale escorts</a>
                        </li>
                    </ul>
                </div>

                <!-- City Search -->
                <div class="form-group" style="position: relative;">
                    <div class='typeahead-city-wrapper' style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 58%; transform: translateY(-50%); z-index: 1; pointer-events: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </span>
                        <input tabindex="1" 
                               id="news_citysearch" 
                               class="city search-bar--city form-control" 
                               placeholder="Type city..." 
                               type="text" 
                               value="{{ $cityname }}" 
                               autocomplete="off"
                               style="background-color: #333; color: white; border: 1px solid #555; padding-left: 38px;">
                        <div id="news_cityappend" class="citys" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #1D2224; border: 1px solid #2a2a2a; border-radius: 8px; max-height: 320px; overflow-y: auto; z-index: 9999; box-shadow: 0 8px 24px rgba(0,0,0,0.5); padding: 4px 0;"></div>
                    </div>
                </div>
            </form>
            
            <!-- Activity Stream Navigation -->
            <div class="activity-stream-nav btn-group">
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}" 
                   class="btn btn-dark {{ !isset($type) || $type === 'all' ? 'active' : '' }}" 
                   style="color:{{ !isset($type) || $type === 'all' ? '#fff' : '#C1F11D' }}">All<span class="hidden-xs hidden-sm"> news</span></a>
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/new-escorts" 
                   class="btn btn-dark {{ $type === 'new-escorts' ? 'active' : '' }}" 
                   style="color:{{ $type === 'new-escorts' ? '#fff' : '#C1F11D' }}"><i class="fas fa-certificate"></i> Escorts</a>
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/new-reviews" 
                   class="btn btn-dark {{ $type === 'new-reviews' ? 'active' : '' }}" 
                   style="color:{{ $type === 'new-reviews' ? '#fff' : '#C1F11D' }}"><i class="fas fa-heart"></i> Reviews</a>
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/new-questions" 
                   class="btn btn-dark {{ $type === 'new-questions' ? 'active' : '' }}" 
                   style="color:{{ $type === 'new-questions' ? '#fff' : '#C1F11D' }}"><i class="fas fa-question-circle"></i> Questions</a>
            </div>
        </div>
    </div>
</header>

      <div class="container-fluid mt-2 ev-news-content">
    <div class="subscribe-btn-wrapper subscribe-btn-wrapper--small-right" x-data="{ show: @entangle('showSubscribeModal') }">
        @auth
            <a class="btn btn-primary btn-lg" href="#" @click.prevent="show = true; $wire.prefillSubscribeCity()">
                <i class="fa fa-newspaper"></i> Subscribe
            </a>
        @else
            <a class="btn btn-primary btn-lg" href="/register">
                <i class="fa fa-newspaper"></i> Subscribe
            </a>
        @endauth

        @auth
        {{-- Newsletter subscribe modal — mirrors the home page implementation
             so logged-in users can manage their multi-city / multi-gender
             newsletter subscriptions from the What's New page too, instead of
             being kicked to /register. --}}
        <div x-show="show" x-cloak class="ev-modal-overlay" @click.self="show = false" @keydown.escape.window="show = false" style="display:none;">
            <div class="ev-modal">
                <div class="ev-modal-header">
                    <h2>
                        <i class="fa fa-newspaper"></i>
                        <span>Subscribe</span>
                    </h2>
                    <button type="button" class="ev-modal-close" @click="show = false">&times;</button>
                </div>
                <div class="ev-modal-body">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer; font-size: 1rem;">
                            <input type="checkbox" wire:model.live="subReceiveNewsletter" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                            <span style="margin-left:0.75rem;font-weight:500;">Send me newsletter for:</span>
                        </label>
                    </div>

                    @if($subReceiveNewsletter)
                    <div style="margin-bottom: 1rem; position: relative;">
                        <div class="ev-search-input">
                            <span><i class="fa fa-map-marker-alt"></i></span>
                            <input type="text" placeholder="Find city..." wire:model.live="subCitySearch" autocomplete="off">
                            @if($subCitySearch)
                                <button type="button" wire:click="$set('subCitySearch', '')"><i class="fa fa-times"></i></button>
                            @endif
                        </div>
                        @if(count($subSearchResults) > 0)
                            <div class="ev-dropdown-results">
                                @foreach($subSearchResults as $resultCity)
                                    <button type="button" wire:click="subAddCity({{ $resultCity['id'] }})">{{ $resultCity['name'] }}@if(!empty($resultCity['country'])) <span style="color:#666;">({{ $resultCity['country'] }})</span>@endif</button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if(count($subSelectedCities) > 0)
                        <div style="margin-bottom: 1rem; max-height: 150px; overflow-y: auto;">
                            @foreach($subSelectedCities as $index => $subCity)
                                <div class="ev-city-tag">
                                    <span><i class="fa fa-map-marker-alt" style="margin-right:0.5rem;"></i>{{ $subCity['name'] }}@if(!empty($subCity['country'])) <span style="color:#666;">({{ $subCity['country'] }})</span>@endif</span>
                                    <button type="button" style="background:none;border:none;cursor:pointer;font-size:1.2rem;padding:0;" wire:click="subRemoveCity({{ $index }})"><i class="fa fa-times"></i></button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label style="display:block;margin-bottom:1rem;font-weight:600;font-size:1rem;">Include</label>
                        <div style="display:flex;flex-wrap:wrap;gap:1.5rem;">
                            <label style="display:flex;align-items:center;cursor:pointer;">
                                <input type="checkbox" value="female" wire:model="subSelectedGenders" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                                <span style="margin-left:0.5rem;">Escorts</span>
                            </label>
                            <label style="display:flex;align-items:center;cursor:pointer;">
                                <input type="checkbox" value="male" wire:model="subSelectedGenders" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                                <span style="margin-left:0.5rem;">Male Escorts</span>
                            </label>
                            <label style="display:flex;align-items:center;cursor:pointer;">
                                <input type="checkbox" value="shemale" wire:model="subSelectedGenders" style="width:18px;height:18px;margin:0;cursor:pointer;accent-color:#C1F11D;">
                                <span style="margin-left:0.5rem;">Shemale Escorts</span>
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="ev-modal-footer">
                    <button type="button" class="ev-buy-btn" wire:click="subSaveNewsletter" @click="show = false">
                        <span>Save</span> <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        @endauth
    </div>
    
    <a class="page-title" href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}">
        <h1>{{ $title }}</h1>
    </a>
  
    <ul class="activity-stream activity-stream-full ">
        {{-- Activity items are pre-rendered (and cached when no filters are active)
             in NewsPage::render() and injected here. Falls back to the legacy
             inline foreach below when the cached HTML is missing or empty —
             prevents a stale/empty Redis entry from rendering a blank page. --}}
        @if(!empty(trim($activityItemsHtml ?? '')))
            {!! $activityItemsHtml !!}
        @elseif($type === 'all')
            @foreach($items as $item)
            @if(isset($item->item_type) && $item->item_type === 'escort')
                @php $profile = $item; @endphp
                <li wire:key="news-escort-{{ $profile->id }}" wire:ignore>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->created_at->format('Y-m-d') != $profile->created_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $profile->created_at->format('d') }}</span>
                        <span class="month">{{ $profile->created_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-listing {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-certificate"></i> New escort 
                                <a title="{{ $profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">{{ $profile->name }}</a>
                            </div>
                            
                            <div class="photo">
                                <a class="pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                    <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($profile->photoverify) && $profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            @if(!empty($profile->coverimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                
                                                 src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image) }}">
                                            @elseif(!empty($profile->singleimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                
                                                 src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($profile->multipleimgs && $profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($profile->multipleimgs->take(2) as $img)
                                <a class="pb-photo-link1" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                    <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($profile->photoverify) && $profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper1" wire:ignore>
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                
                                                 src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                {{ Str::limit($profile->about, 400) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @elseif(isset($item->item_type) && $item->item_type === 'question')
                @php $question = $item; @endphp
                <li wire:key="news-question-{{ $question->id }}" wire:ignore>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->updated_at->format('Y-m-d') != $question->updated_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $question->updated_at->format('d') }}</span>
                        <span class="month">{{ $question->updated_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-question-answered {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-question-circle"></i>
                                <a title="{{ $question->profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">{{ $question->profile->name }}</a>
                                answered a question
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            @if(!empty($question->profile->coverimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->coverimg->image) }}">
                                            @elseif(!empty($question->profile->singleimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($question->profile->multipleimgs && $question->profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($question->profile->multipleimgs->take(2) as $img)
                                <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                <div class="listing-question">
                                    <ul class="list-unstyled list-separated">
                                        <li>
                                            <div class="question-block">
                                                <p class="question">{!! nl2br(e($question->question)) !!}</p>
                                            </div>
                                            <span class="questioner">
                                                by <a href="/u/{{ $question->askedBy->name ?? 'anonymous' }}">{{ $question->askedBy->name ?? 'Anonymous' }}</a>
                                            </span>
                                            <span class="question-date">&nbsp;– {{ $question->created_at->format('d M Y') }}</span>
                                            <div class="answer-wrapper">
                                                <div class="answer-block">
                                                    <p class="answer">{!! nl2br(e($question->answer)) !!}</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endif
            @endforeach
            
        @elseif($type === 'new-escorts')
            @foreach($items as $profile)
            <li wire:key="news-escort-{{ $profile->id }}" wire:ignore>
                @if($loop->first || $loop->iteration == 1 || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->created_at->format('Y-m-d') != $profile->created_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $profile->created_at->format('d') }}</span>
                        <span class="month">{{ $profile->created_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-listing {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-certificate"></i> New escort 
                                <a title="{{ $profile->name }}, {{ $profile->gnat->nicename ?? 'Unknown' }} escort in {{ $profile->gcity->name ?? $cityname }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">{{ $profile->name }}</a>
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                    <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($profile->photoverify) && $profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            @if(!empty($profile->coverimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image) }}">
                                            @elseif(!empty($profile->singleimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            <div class="right-thumbs">
                                @if($profile->multipleimgs && $profile->multipleimgs->count() > 0)
                                    @foreach($profile->multipleimgs->take(2) as $img)
                                    <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                        <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                            <div class="image-wrapper" wire:ignore>
                                                <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                     class="img-responsive" decoding="async" 
                                                     height="208" 
                                                     width="200"
                                                     src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                            </div>
                                        </span>
                                    </a>
                                    @endforeach
                                @endif
                            </div>
                            
                            <div class="activity-content">
                                {{ Str::limit($profile->about, 400) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
            
        @elseif($type === 'new-reviews')
            @foreach($items as $review)
            <li wire:key="news-review-{{ $review->id }}" wire:ignore>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->created_at->format('Y-m-d') != $review->created_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $review->created_at->format('d') }}</span>
                        <span class="month">{{ $review->created_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-review {{ $review->profile->package_id == 21 || $review->profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-heart"></i> New review for 
                                <a title="{{ $review->profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $review->profile->id }}/{{ $review->profile->slug }}">{{ $review->profile->name }}</a>
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $review->profile->id }}/{{ $review->profile->slug }}">
                                    <span class="img-wrapper {{ $review->profile->package_id == 21 || $review->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($review->profile->photoverify) && $review->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            @if(!empty($review->profile->coverimg))
                                            <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->coverimg->image) }}">
                                            @elseif(!empty($review->profile->singleimg))
                                            <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($review->profile->multipleimgs && $review->profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($review->profile->multipleimgs->take(2) as $img)
                                <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $review->profile->id }}/{{ $review->profile->slug }}">
                                    <span class="img-wrapper {{ $review->profile->package_id == 21 || $review->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($review->profile->photoverify) && $review->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                <div class="review">
                                    <span class="star-rating" data-val="{{ $review->star }}" title="Rating: {{ $review->star }} / 5">
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $review->star ? 'selected' : '' }}" data-val="{{ $i }}"></span>
                                            @endfor
                                        </div>
                                    </span>
                                    <span class="reviewer">
                                        by <a href="/u/{{ $review->user->name ?? 'anonymous' }}">{{ $review->user->name ?? 'Anonymous' }}</a>
                                    </span>
                                    <span class="review-date">&nbsp;– {{ $review->created_at->format('d M Y') }}</span>
                                    <div class="review-description">
                                        <p class="review-text">{{ Str::limit($review->review, 500) }}</p>
                                        @if($review->reply)
                                    <div class="listing-reply" style="margin-top: 15px; position: relative;">
                                        <div style="display: inline-block;
    max-width: 100%;
    background: #2a2a2a;
    padding: 10px 15px;
    border-radius: 9px;
    position: relative;">
                                            <!-- Arrow effect -->
                                            <div style="position: absolute;
    top: 10px;
    left: -10px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid #2a2a2a;
    transform: rotate(269deg);"></div>
                                            
                                           
                                            <span style="color: #aaa; font-size: 13px;">
                                                {{ $review->reply }}
                                            </span>
                                        </div>
                                        
                                    </div>
                                    @endif
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
            
        @elseif($type === 'new-questions')
            @foreach($items as $question)
            <li wire:key="news-question-{{ $question->id }}" wire:ignore>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->updated_at->format('Y-m-d') != $question->updated_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $question->updated_at->format('d') }}</span>
                        <span class="month">{{ $question->updated_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-question-answered {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-question-circle"></i>
                                <a title="{{ $question->profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">{{ $question->profile->name }}</a>
                                answered a question
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            @if(!empty($question->profile->coverimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->coverimg->image) }}">
                                            @elseif(!empty($question->profile->singleimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($question->profile->multipleimgs && $question->profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($question->profile->multipleimgs->take(2) as $img)
                                <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper" wire:ignore>
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" decoding="async" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                <div class="listing-question">
                                    <ul class="list-unstyled list-separated">
                                        <li>
                                            <div class="question-block">
                                                <p class="question">{!! nl2br(e($question->question)) !!}</p>
                                            </div>
                                            <span class="questioner">
                                                by <a href="/u/{{ $question->askedBy->name ?? 'anonymous' }}">{{ $question->askedBy->name ?? 'Anonymous' }}</a>
                                            </span>
                                            <span class="question-date">&nbsp;– {{ $question->created_at->format('d M Y') }}</span>
                                            @if($question->answer)
                                            <div class="answer-wrapper">
                                                <div class="answer-block">
                                                    <p class="answer">{!! nl2br(e($question->answer)) !!}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        @endif
        
        {{-- Empty state: shown when the paginator has no items AND the
             pre-rendered cached HTML is also empty. Without this, cities
             like Bajram Curri that have zero escort activity render just
             the header + a blank <ul>, which looks like a broken page. --}}
        @if($items->count() === 0 && empty(trim($activityItemsHtml ?? '')))
        <li class="ev-news-empty" style="text-align:center;padding:60px 20px;color:#8a8a8a;list-style:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5;margin-bottom:12px;">
                <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                <path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>
            </svg>
            <p style="margin:0;font-size:15px;font-weight:500;color:#c9c9c9;">No news yet in {{ ucwords(str_replace('-', ' ', $selectedcity ?: 'this location')) }}</p>
            <p style="margin:6px 0 0;font-size:13px;color:#8a8a8a;">Be the first to know when new escorts, reviews, or questions appear here.</p>
        </li>
        @endif

        @if($items->count() > 0 && ($items->hasMorePages() || $items->count() >= 3))
        {{-- IntersectionObserver fires loadMore() when the trigger is within
             100px of the viewport. Reduced from 500px which made the trigger
             "visible" on initial render of short pages and fired loadMore
             before the user scrolled. Combined with the failure handler in
             the script below, this prevents Livewire's full-screen error
             overlay from blocking the page on /livewire/update errors. --}}
        <li class="activity-footer" id="load-more-trigger" x-data="{
            observe() {
                let observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            $wire.loadMore();
                        }
                    });
                }, { rootMargin: '100px' });
                observer.observe(this.$el);
            }
        }" x-init="observe()">
            <div class="text-center" style="padding: 20px;">
                <div wire:loading wire:target="loadMore">
                    <i class="fa fa-spinner fa-spin" style="font-size: 24px; color: #C1F11D;"></i>
                    <p style="color: #999; margin-top: 10px;">Loading more...</p>
                </div>
                <div wire:loading.remove wire:target="loadMore">
                    <p style="color: #666;">Scroll for more...</p>
                </div>
            </div>
        </li>
        @endif
    </ul>
    </div>
    {{-- <div class="subscribe-rss">
        <a href="https://massagerepublic.com/{{ $gender }}-escort-news-in-{{ $selectedcity }}/{{ $type }}.rss" type="application/rss+xml">
            <i class="fa fa-rss"></i> Subscribe by RSS
        </a>
    </div> --}}
</div>

@push('js')
<script>
// Suppress Livewire's full-screen error overlay (the black <div id="livewire-error">
// with an iframe inside that fills the viewport when a Livewire request fails).
// On the news page the IntersectionObserver fires loadMore() automatically, so
// any 500 from /livewire/update would otherwise blanket the page on first paint
// before the user even scrolled. The actual error still goes to the JS console
// via Livewire.hook('request') — search the console for "[livewire]" if loadMore
// stops working and you need to debug.
document.addEventListener('livewire:init', function () {
    if (!window.Livewire) return;
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, content, preventDefault }) => {
            console.warn('[livewire] request failed (status=' + status + ')', content);
            preventDefault();
        });
    });
    // Belt-and-braces: if the overlay still slips through (older Livewire
    // versions render it before hooks run), kill it on sight.
    new MutationObserver(function () {
        var el = document.getElementById('livewire-error');
        if (el) el.remove();
    }).observe(document.body, { childList: true });
});
</script>
<script>
// Image flicker fix. The news page on prod sees previously-loaded <img>
// elements briefly lose their decoded pixels during the morph after loadMore
// — visible as black/blank rectangles where images used to be. Cannot
// reproduce locally; almost certainly a morph race where the <img> element
// is detached and reattached faster than the browser preserves decoded data.
//
// Strategy: once each <img> has successfully loaded, also paint that same
// image URL as a CSS background-image on its parent .image-wrapper. CSS
// backgrounds live on the parent element and are not affected by what the
// child <img> is doing — so even if the <img> briefly goes blank during a
// re-attach, the wrapper still shows the image visually.
//
// One-time only per image (guarded by data-bg-painted). Skips broken images
// so we don't paint a black placeholder backed by a 404 URL.
(function () {
    if (window.__newsImgBgFallbackBound) return;
    window.__newsImgBgFallbackBound = true;

    function paint(img) {
        if (!img || img.dataset.bgPainted) return;
        var wrapper = img.parentElement;
        if (!wrapper || !wrapper.classList.contains('image-wrapper')) return;
        if (!img.complete || img.naturalWidth === 0) return;
        var src = img.currentSrc || img.src;
        if (!src) return;
        wrapper.style.backgroundImage = "url('" + src.replace(/'/g, "\\'") + "')";
        wrapper.style.backgroundSize = 'cover';
        wrapper.style.backgroundPosition = 'center';
        wrapper.style.backgroundRepeat = 'no-repeat';
        img.dataset.bgPainted = '1';
    }

    function attach(img) {
        if (!img || img.__bgAttached) return;
        img.__bgAttached = true;
        if (img.complete) {
            paint(img);
        } else {
            img.addEventListener('load', function () { paint(img); }, { once: true });
        }
    }

    function scan() {
        document.querySelectorAll('.activity-stream .image-wrapper img').forEach(attach);
    }

    function start() {
        var ul = document.querySelector('.activity-stream');
        if (!ul) { setTimeout(start, 250); return; }
        scan();
        new MutationObserver(scan).observe(ul, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
</script>
<script>
// The news-page renders on the legacy `app` layout which loads css-optimized.blade.php
// — that bundle pulls in app.css, app2.css, app3.css, AND app4.css, all of which
// carry Bootstrap-flavoured anchor + button rules and an inline FontAwesome 4
// @font-face. Once `wire:navigate` puts them in <head> they stay there and leak
// onto the next evoory-layout page (home `/`, listing, dashboard). Strip them
// when navigating into an evoory-layout page (marker: `body > main`); re-attach
// when going back to a legacy page.
(function() {
    var LEGACY_CSS = ['app.css', 'app2.css', 'app3.css', 'app4.css'];

    function syncLegacyCss() {
        var onEvooryLayout = !!document.querySelector('body > main');
        window.__evooryStashedLegacyCss = window.__evooryStashedLegacyCss || {};
        var stash = window.__evooryStashedLegacyCss;

        LEGACY_CSS.forEach(function (name) {
            var live = document.querySelector('link[rel="stylesheet"][href*="assets/css/' + name + '"]');

            if (onEvooryLayout && live) {
                stash[name] = live.getAttribute('href');
                live.parentNode.removeChild(live);
            } else if (!onEvooryLayout && !live && stash[name]) {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = stash[name];
                document.head.appendChild(link);
            }
        });
    }

    // Only strip on actual navigations, not on initial script load — the
    // initial load can race with body swap and produce an unstyled flash.
    if (!window.__evooryNewsLegacyListener) {
        window.__evooryNewsLegacyListener = true;
        document.addEventListener('livewire:navigated', syncLegacyCss);
    }
})();
</script>
<script>
// News page city search — uses document-level event delegation so it survives
// Livewire morphs and runs even if this script loads before the input.
(function() {
    'use strict';
    if (window.__newsCitySearchBound) return;
    window.__newsCitySearchBound = true;
    console.log('🏙️ News page city search delegation bound');

    var INPUT_ID = 'news_citysearch';
    var APPEND_ID = 'news_cityappend';
    var GENDER = @json($gender ?? 'female');
    var TYPE = @json($type ?? 'all');
    var CSRF = @json(csrf_token());
    var searchTimeout = null;

    var COUNTRY_MAP = {
        'United Arab Emirates': 'AE', 'Pakistan': 'PK', 'India': 'IN',
        'United Kingdom': 'GB', 'United States': 'US', 'Brazil': 'BR',
        'Philippines': 'PH', 'Thailand': 'TH', 'Singapore': 'SG',
        'China': 'CN', 'Japan': 'JP', 'France': 'FR', 'Germany': 'DE',
        'Italy': 'IT', 'Spain': 'ES', 'Canada': 'CA', 'Australia': 'AU',
        'Netherlands': 'NL', 'Belgium': 'BE', 'Switzerland': 'CH',
        'Austria': 'AT', 'Sweden': 'SE', 'Norway': 'NO', 'Denmark': 'DK',
        'Finland': 'FI', 'Poland': 'PL', 'Czech Republic': 'CZ',
        'Turkey': 'TR', 'Egypt': 'EG', 'South Africa': 'ZA',
        'Saudi Arabia': 'SA', 'Qatar': 'QA', 'Kuwait': 'KW',
        'Bahrain': 'BH', 'Oman': 'OM', 'Lebanon': 'LB', 'Jordan': 'JO',
        'Ireland': 'IE', 'Portugal': 'PT', 'Greece': 'GR', 'Russia': 'RU',
        'Malaysia': 'MY', 'Indonesia': 'ID', 'Vietnam': 'VN',
        'South Korea': 'KR', 'Hong Kong': 'HK', 'New Zealand': 'NZ',
        'Argentina': 'AR', 'Mexico': 'MX', 'Colombia': 'CO'
    };

    function getInput()  { return document.getElementById(INPUT_ID); }
    function getAppend() { return document.getElementById(APPEND_ID); }

    function searchCities(query) {
        var cityAppend = getAppend();
        if (!cityAppend) return;
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            cityAppend.style.display = 'none';
            cityAppend.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(function() {
            console.log('🔍 Searching for city:', query);
            var fetchFn = window.SessionRecovery ? window.SessionRecovery.fetch.bind(window.SessionRecovery) : fetch;
            fetchFn('/cities/search?_=' + Date.now(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.SessionRecovery ? window.SessionRecovery.getToken() : CSRF,
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                body: JSON.stringify({ query: query })
            })
            .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function(data) {
                var ca = getAppend();
                if (!ca) return;
                ca.innerHTML = '';
                if (!data.length) {
                    ca.innerHTML = '<div style="padding:12px 16px;color:#999;font-size:13px;">No cities found</div>';
                    ca.style.display = 'block';
                    return;
                }
                data.forEach(function(city) {
                    var citySlug = city.name.toLowerCase().replace(/\s+/g, '-');
                    var countryCode = COUNTRY_MAP[city.country] || null;
                    var opt = document.createElement('div');
                    opt.className = 'news-city-opt';
                    opt.style.cssText = 'padding:10px 16px;margin:0 4px;border-radius:6px;cursor:pointer;color:#fff;display:flex;align-items:center;transition:background 0.15s,color 0.15s;font-size:14px;';
                    opt.dataset.slug = citySlug;
                    if (countryCode) {
                        var flag = document.createElement('span');
                        flag.style.cssText = 'margin-right:10px;width:20px;height:14px;background-size:cover;background-position:center;display:inline-block;';
                        flag.style.backgroundImage = 'url(https://flagcdn.com/w40/' + countryCode.toLowerCase() + '.png)';
                        opt.appendChild(flag);
                    }
                    var nameSpan = document.createElement('span');
                    nameSpan.textContent = city.name;
                    nameSpan.style.flex = '1';
                    opt.appendChild(nameSpan);
                    if (city.profile_count !== undefined) {
                        var countSpan = document.createElement('span');
                        countSpan.textContent = city.profile_count;
                        countSpan.style.cssText = 'color:#999;font-size:12px;margin-left:auto;';
                        opt.appendChild(countSpan);
                    }
                    ca.appendChild(opt);
                });
                ca.style.display = 'block';
            })
            .catch(function(err) {
                console.error('❌ City search error:', err);
                var ca = getAppend();
                if (ca) {
                    ca.innerHTML = '<div style="padding:12px 16px;color:#dc3545;font-size:13px;">Connection error. Please try again.</div>';
                    ca.style.display = 'block';
                }
            });
        }, 300);
    }

    // input → trigger search
    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === INPUT_ID) {
            searchCities(e.target.value.trim());
        }
    });

    // focus → if value >= 2, show results (focus doesn't bubble, use focusin)
    document.addEventListener('focusin', function(e) {
        if (e.target && e.target.id === INPUT_ID) {
            var v = e.target.value.trim();
            if (v.length >= 2) searchCities(v);
        }
    });

    // mouseover/mouseout on dropdown items
    document.addEventListener('mouseover', function(e) {
        var opt = e.target.closest && e.target.closest('.news-city-opt');
        if (opt && getAppend() && getAppend().contains(opt)) {
            opt.style.backgroundColor = '#262C2F';
            opt.style.color = '#C1F11D';
        }
    });
    document.addEventListener('mouseout', function(e) {
        var opt = e.target.closest && e.target.closest('.news-city-opt');
        if (opt && getAppend() && getAppend().contains(opt)) {
            opt.style.backgroundColor = 'transparent';
            opt.style.color = '#fff';
        }
    });

    // click on a dropdown item → navigate
    document.addEventListener('click', function(e) {
        var opt = e.target.closest && e.target.closest('.news-city-opt');
        if (opt && getAppend() && getAppend().contains(opt)) {
            var slug = opt.dataset.slug;
            var url = '/' + GENDER + '-escort-news-in-' + slug;
            if (TYPE && TYPE !== 'all') url += '/' + TYPE;
            console.log('🚀 Redirecting to:', url);
            window.location.href = url;
            return;
        }
        // outside click → close dropdown
        var input = getInput();
        var ca = getAppend();
        if (input && ca && !input.contains(e.target) && !ca.contains(e.target)) {
            ca.style.display = 'none';
        }
    });
})();
</script>
@endpush
