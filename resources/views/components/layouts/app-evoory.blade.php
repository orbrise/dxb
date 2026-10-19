<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="NHKPt4-TOMI7fTCn2xKG6Mkeray0v9b-B7Bk9EEKHok" />

    {{-- Service-worker purge. A legacy /serviceworker.js was installed on
         older deployments and intercepts fetches at its line 36, causing
         /call/pending and /livewire/update to pile up as "pending" in the
         browser's Network tab and slow the chat page to a crawl.
         This runs on EVERY page load and nukes any registered worker that
         isn't our push-sw.js. Browsers invalidate the old SW the moment
         .unregister() resolves; next navigation hits the network directly.
         Safe to leave in place indefinitely — push-sw.js is explicitly
         preserved and this is a no-op once no stale workers remain. --}}
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function (regs) {
            regs.forEach(function (reg) {
                var url = (reg.active && reg.active.scriptURL) || '';
                if (url.indexOf('/push-sw.js') === -1) {
                    reg.unregister();
                }
            });
            if ('caches' in window) {
                caches.keys().then(function (keys) {
                    keys.forEach(function (k) { caches.delete(k); });
                });
            }
        }).catch(function () {});
    }
    </script>

    {{-- Edge-cache CSRF workaround.
         Cloudflare caches listing HTML, so the inline meta csrf-token above may
         be a stale token from another visitor's session. Fetch a fresh token
         from /csrf-refresh (no-store) and overwrite the meta tag before any
         Livewire request fires. We also intercept Livewire 'request' events to
         inject the latest token into the X-CSRF-TOKEN / X-XSRF-TOKEN headers,
         and retry once on 419 after re-fetching. --}}
    <script>
    (function () {
        var REFRESH_URL = '/csrf-refresh';
        var latestToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        var refreshing = null;

        function setToken(token) {
            if (!token) return;
            latestToken = token;
            var m = document.querySelector('meta[name="csrf-token"]');
            if (m) m.setAttribute('content', token);
            if (window.jQuery) {
                window.jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
            }
        }

        function refresh() {
            if (refreshing) return refreshing;
            // Cache-busting query param: Cloudflare's "Cache Everything" rule
            // is what caused this whole problem in the first place. A unique
            // URL per request guarantees we always hit the origin, regardless
            // of whether the Cache-Control headers are honored.
            var url = REFRESH_URL + '?t=' + Date.now() + '_' + Math.random().toString(36).slice(2);
            refreshing = fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store'
            })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (data && data.token) setToken(data.token);
                    return data && data.token;
                })
                .catch(function () { return null; })
                .finally(function () { refreshing = null; });
            return refreshing;
        }

        // Lazy CSRF refresh. Previously fired on EVERY page load regardless of
        // whether the page was Cloudflare-cached, doubling the per-visit worker
        // demand on an 8-worker LiteSpeed pool. The inline <meta> token is
        // already fresh for any uncacheable response (which is anything with
        // Livewire markup — i.e. almost every page on this site), so skip the
        // unconditional refresh. The 419-retry path below still rescues stale
        // tokens served from the edge cache on the rare pages that CF does
        // cache. We also refresh on the first form interaction so submits
        // after a long idle still work.
        var refreshOnFirstInteraction = function () {
            document.removeEventListener('mousedown', refreshOnFirstInteraction, true);
            document.removeEventListener('keydown', refreshOnFirstInteraction, true);
            document.removeEventListener('touchstart', refreshOnFirstInteraction, true);
            refresh();
        };
        document.addEventListener('mousedown', refreshOnFirstInteraction, true);
        document.addEventListener('keydown', refreshOnFirstInteraction, true);
        document.addEventListener('touchstart', refreshOnFirstInteraction, true);

        // Refresh again after Livewire is ready, and hook every outgoing
        // Livewire request to use the freshest token + retry once on 419.
        document.addEventListener('livewire:init', function () {
            if (!window.Livewire || typeof window.Livewire.hook !== 'function') return;

            window.Livewire.hook('request', function (payload) {
                var options = payload && payload.options;
                if (!options) return;
                options.headers = options.headers || {};
                if (latestToken) {
                    options.headers['X-CSRF-TOKEN'] = latestToken;
                    options.headers['X-XSRF-TOKEN'] = latestToken;
                }

                if (typeof payload.respond === 'function') {
                    payload.respond(function (resp) {
                        if (resp && resp.status === 419) {
                            // Token went stale mid-session — refresh and let
                            // Livewire's built-in retry/refresh handle the user
                            // flow. The next request will pick up the new token.
                            refresh();
                        }
                    });
                }
            });
        });
    })();
    </script>
    @if(!empty($setting->favicon))
    <link href="{{ smart_asset($setting->favicon) }}" rel="shortcut icon" type="image/x-icon" />
    @endif
    {{-- Per-page title override wins over the SeoComposer's $seoTitle so
         profile detail pages can show the profile name in the browser tab
         while listing pages keep their SEO-driven title. --}}
    <title>{{ $pageTitle ?? $seoTitle ?? 'Evoory - Premium Escort Directory' }}</title>
    <meta name="description" content="{{ $pageDescription ?? $seoDescription ?? 'Connect with escorts from Dubai and around the world. Premium escort directory with verified listings.' }}">
    @if(!empty($seoKeywords))
    <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    {{-- Robots directive for listing/city pages: index when there are profiles
         on the current page, noindex (follow) when the listing is empty so
         Google doesn't accumulate thin empty pages. HomePage/ServicePage
         share $hasProfiles from render(); other pages leave it unset and
         fall back to the crawler default (indexable). --}}
    @isset($hasProfiles)
        @if($hasProfiles)
    <meta name="robots" content="index, follow" inertia="public-head-robots">
        @else
    <meta name="robots" content="noindex, follow" inertia="public-head-robots">
        @endif
    @endisset
    
    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://assets.evoory.com" crossorigin>
    <link rel="dns-prefetch" href="//assets.evoory.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    {{-- display=optional avoids the layout shift caused by a late font swap.
         Browsers use the system fallback if Inter doesn't load within ~100ms;
         on subsequent visits the font is cached and used immediately. Trade-off
         is intentional: zero CLS at the cost of a one-time fallback render for
         slow-connection visitors. --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=optional" rel="stylesheet">
    
    {{-- Critical CSS inline for faster FCP --}}
    <style>
        :root{--bg-primary:#0D1011;--bg-secondary:#111111;--accent:#C1F11D;--text-primary:#ffffff}
        html{min-height:100%}
        body{margin:0;background:var(--bg-primary) !important;background-image:none !important;color:var(--text-primary);font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-weight:300;display:flex;flex-direction:column;min-height:100vh}
        body > main{flex:1 0 auto}
        .ev-header{background:var(--bg-secondary);padding:12px 0;position:sticky;top:0;z-index:1000}
        .ev-container{max-width:1200px;margin:0 auto;padding:0 16px}
        .ev-logo{font-size:24px;font-weight:700;color:var(--accent);text-decoration:none}
        .ev-flex{display:flex}.ev-items-center{align-items:center}.ev-justify-between{justify-content:space-between}

        /* Anti-FOUC: mirrors the Bootstrap 3 visibility/dropdown/modal rules and
           the global link-color rule that live inside the 1.3 MB
           evoory-homepage.css bundle. Without these, a cold-cache load briefly
           shows the modal contents, dropdown items as a flat list, mobile-only
           controls on desktop, and every link as default-blue underlined text
           until the big bundle finishes downloading. */
        .modal{display:none}
        .dropdown-menu{display:none;position:absolute}
        .visible-xs,.visible-xs-block,.visible-xs-inline,.visible-xs-inline-block{display:none !important}
        @media (max-width:767px){
            .visible-xs,.visible-xs-block{display:block !important}
            .visible-xs-inline{display:inline !important}
            .visible-xs-inline-block{display:inline-block !important}
            .hidden-xs{display:none !important}
        }
        @media (min-width:768px){
            .form-inline .form-group{display:inline-block;margin-bottom:0;vertical-align:middle}
        }
        a{color:var(--accent);text-decoration:none}
        .nostyle-link,.nostyle-link *{color:inherit;text-decoration:none}
    </style>
    
    {{-- Bootstrap 4 CSS + Font Awesome (required for grid, components, icons).
         Use the minified build (~15 KB smaller, identical content). --}}
    <link rel="stylesheet" href="{{smart_asset('assets/css/app.min.css')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    {{-- Main theme CSS (loaded after Bootstrap to override) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/evoory-theme.css') }}?v=20261013-1">

    {{-- Listing-page CSS lives in the layout (not in @push) so the <link>
         elements survive wire:navigate. Pushing them caused Livewire's head
         merger to destroy + recreate the element on every back-to-listings
         navigation, forcing a fresh CSS parse and a visible reflicker.
         media="print" initially keeps the rules from applying on non-listing
         pages (preventing the legacy bundle's body/a/.fa rules from leaking
         onto homepage etc.). PHP picks the initial value from the current
         route so server-rendered listing pages are styled immediately; the
         small JS at the bottom of <head> flips it on wire:navigate. --}}
    @php
        $__isListingPage = request()->is('*-escorts-in-*');
    @endphp
    <link rel="stylesheet" id="evoory-listing-css"
          href="{{ asset('assets/css/evoory-homepage.css') }}?v={{ @filemtime(public_path('assets/css/evoory-homepage.css')) ?: 1 }}"
          media="{{ $__isListingPage ? 'all' : 'print' }}">
    <link rel="stylesheet" id="evoory-listing-inline-css"
          href="{{ asset('assets/css/listing-page-inline.css') }}?v={{ @filemtime(public_path('assets/css/listing-page-inline.css')) ?: 1 }}"
          media="{{ $__isListingPage ? 'all' : 'print' }}">
    <script>
    (function(){
        // Toggle listing-only CSS between media="all" and media="print" so its
        // rules only apply on listing/service pages. Matches BY HREF (not by
        // id), because Livewire's head merge can leave duplicate <link> tags
        // around when navigating between pages that push their own copy of
        // these stylesheets (service-page does); getElementById would only
        // touch the first match and leave the rest stuck.
        function isListingPath(path){
            return /-escorts-in-/.test(path);
        }
        function setListingCss(on){
            var links = document.querySelectorAll(
                'link[href*="evoory-homepage.css"], link[href*="listing-page-inline.css"]'
            );
            links.forEach(function(el){ el.media = on ? 'all' : 'print'; });
        }
        function syncListingCss(){
            setListingCss(!!document.querySelector('.ev-listing-page')
                  || isListingPath(window.location.pathname));
        }
        // Flip the CSS media attribute right before the body swap, based on
        // the DESTINATION url. Timing matters here:
        //
        //   - livewire:navigate fires on CLICK, before the fetch starts. If
        //     we flip here the fetch takes 100-500ms and the user sees the
        //     CURRENT page with the NEW page's CSS applied the whole time
        //     (visible restyle flash on the page they're leaving).
        //   - livewire:navigating fires after fetch, immediately before the
        //     DOM swap. The window between this event and the swap is a
        //     single microtask — no visible paint. This is the right moment.
        //
        // But alpine:navigating is dispatched WITHOUT event.detail (see
        // livewire.esm.js:9308), so we can't read the destination from it.
        // Workaround: capture the URL on livewire:navigate (which does have
        // event.detail.url) and consume it on livewire:navigating.
        var __pendingListing = null;
        document.addEventListener('livewire:navigate', function (event) {
            var url = event.detail && event.detail.url;
            if (!url) { __pendingListing = null; return; }
            try {
                var path = (typeof url === 'string')
                    ? new URL(url, window.location.origin).pathname
                    : (url.pathname || new URL(url.toString(), window.location.origin).pathname);
                __pendingListing = isListingPath(path);
            } catch (_) { __pendingListing = null; }
        });
        document.addEventListener('livewire:navigating', function () {
            if (__pendingListing !== null) {
                setListingCss(__pendingListing);
                __pendingListing = null;
            }
        });
        document.addEventListener('livewire:navigated', syncListingCss);
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncListingCss);
        } else {
            syncListingCss();
        }
    })();

    // my-chat is heavy on Alpine state, Echo listeners, polling, and inline
    // scripts that don't survive Livewire's soft-swap morph. Opt the chat
    // page out of wire:navigate ENTIRELY by intercepting any click that
    // would take the user TO /my-chat and forcing a full page load. Also
    // force full-load when leaving /my-chat so we don't leave stale listeners
    // behind. Runs in capture phase so it fires before Livewire's handler.
    (function () {
        function isChatPath(path) {
            return path === '/my-chat' || path.startsWith('/my-chat/');
        }
        document.addEventListener('click', function (e) {
            // Only hijack plain left-clicks on <a> elements. Modifier keys
            // (Ctrl/Cmd/Shift) and middle-click should keep their native
            // "open in new tab" behavior.
            if (e.defaultPrevented) return;
            if (e.button !== 0) return;
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            const a = e.target.closest('a[href]');
            if (!a) return;
            // Ignore download links, mailto/tel, external hosts, etc.
            if (a.hasAttribute('download')) return;
            if (a.target && a.target !== '_self') return;
            let href;
            try { href = new URL(a.href, window.location.origin); } catch (_) { return; }
            if (href.origin !== window.location.origin) return;
            const goingToChat   = isChatPath(href.pathname);
            const leavingChat   = isChatPath(window.location.pathname) && !isChatPath(href.pathname);
            if (!goingToChat && !leavingChat) return;
            // Hard-navigate instead of letting wire:navigate soft-swap.
            e.preventDefault();
            e.stopPropagation();
            window.location.href = a.href;
        }, true); // capture-phase so we beat Livewire's own click handler
    })();
    </script>

    {{-- Additional page-specific CSS --}}
    @stack('css')
</head>
<body>
    {{-- Admin Impersonation Banner. Rendered here (not inside the header
         partial) so it sits above every page that uses this layout,
         including /my-listings which is where the admin-as-user flow
         lands. --}}
    @if(session('admin_impersonating'))
    <div id="impersonation-banner" style="background: linear-gradient(45deg, #ff6b6b, #ffa500); color: white; padding: 10px 0; text-align: center; position: sticky; top: 0; z-index: 9999; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8">
            <strong><i class="fa fa-user-secret"></i> ADMIN IMPERSONATION MODE</strong>
            @if(session('impersonating'))
            - You are viewing as: <strong>{{ session('impersonating.user_name') }}</strong> ({{ session('impersonating.user_email') }})
            @if(session('impersonating.via_profile'))
            <br><small><i class="fa fa-id-card"></i> Via Profile: <strong>{{ session('impersonating.via_profile') }}</strong></small>
            @endif
            @endif
            <span id="impersonation-timer" style="margin-left: 15px; font-size: 12px;"></span>
          </div>
          <div class="col-md-4 text-end">
            <a href="{{ route('exit.impersonation') }}" class="btn btn-sm btn-light" onclick="return confirm('Are you sure you want to exit impersonation mode?')">
              <i class="fa fa-sign-out"></i> Exit Impersonation
            </a>
          </div>
        </div>
      </div>
    </div>
    <script data-navigate-once>
    @if(session('impersonation_started'))
    (function () {
      if (window.__impersonationTimer) return;
      window.__impersonationTimer = true;
      var startTime = new Date('{{ session('impersonation_started') }}');
      function updateTimer() {
        var el = document.getElementById('impersonation-timer');
        if (!el) return;
        var diff = Math.floor((new Date() - startTime) / 1000);
        var minutes = Math.floor(diff / 60);
        var seconds = diff % 60;
        el.innerHTML = '(Active for ' + minutes + 'm ' + seconds + 's)';
      }
      setInterval(updateTimer, 1000);
      updateTimer();
    })();
    @endif
    </script>
    @endif

    {{-- Header --}}
    @include('components.layouts.header-evoory')

    {{-- Main Content --}}
    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- WebRTC call UI (incoming banner + full-screen call overlay).
         MUST live outside the Livewire component root so re-renders
         (refreshChat every 3s, message-send re-renders) can't strip the
         .open class off the call UI or blow away the video srcObject.
         Only the HTML shell is here — call scripts stay on the chat
         page, so this is cheap on pages that never open a call. --}}
    @auth
        @include('livewire.partials.webrtc-ui')
        {{-- WebRTC signaling scripts. Runs on every authenticated page so
             an incoming call rings the user wherever they are on the site —
             not just when they happen to have /my-chat open. The scripts
             are self-contained (fetch-based polling of /call/pending; no
             Echo dependency) so they work without loading @vite globally. --}}
        {{-- Expose auth user info to the WebRTC scripts so we can pre-fill
             the display name inside Jitsi's iframe. --}}
        <script>
            window.__authUserId   = @json(auth()->id());
            window.__authUserName = @json(auth()->user()?->name ?: (auth()->user()?->email ?? ''));
        </script>
        @include('livewire.partials.webrtc-scripts')
        {{-- Web Push registration — asks for notification permission on the
             first user click, then keeps a PushSubscription on file so
             the server can wake the browser with an incoming-call
             notification even when the tab is minimized / phone screen off.
             Silently no-ops on browsers without Push support (iOS Safari
             outside PWA installs, etc.). --}}
        @include('livewire.partials.push-init')

        {{-- Real-time badge poller for the Chat & Calls icons (top header
             + mobile bottom nav). Fetches /chat/badges every 15 seconds
             and rewrites .ev-cc-badge (header, unread_messages) and
             .ev-mobile-bottom-nav__badge (mobile, total). Both targets
             are optional — the script no-ops if neither exists on the
             current page. Skips the chat page itself (which keeps its
             badge in sync via the inline Livewire component). --}}
        <script data-navigate-once>
            (function () {
                if (window.__chatBadgePoll) return;
                window.__chatBadgePoll = true;

                // Note: runs on /my-chat too. The top-header badge lives
                // in the layout (not inside the Livewire chat component),
                // so without this poll it would stay stale even while the
                // user has the chat page open and is receiving messages.

                // Exposed globally so other scripts can force an immediate
                // badge refresh — e.g. the chat page's Echo listener for
                // NewChatMessage calls this the moment a message pushes
                // over WebSocket, giving sub-second badge updates on
                // pages where Reverb is subscribed.
                // Track last-seen total so we can detect UP changes
                // (new message arrived) vs DOWN changes (user read / seen
                // a missed call) between polls. An UP change on /my-chat
                // triggers a Livewire refresh of the chat component so the
                // sidebar's unread pill + "Unread" filter counter update
                // without waiting for the chat page's own 8s tickPoll.
                let __lastTotal = null;

                window.__refreshChatBadges = async function () {
                    try {
                        const res = await fetch('/chat/badges?_=' + Date.now(), {
                            credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            cache: 'no-store',
                        });
                        if (!res.ok) {
                            console.warn('[badges] HTTP', res.status);
                            return;
                        }
                        const data = await res.json();
                        const unread = parseInt(data.unread_messages || 0, 10);
                        const total  = parseInt(data.total || 0, 10);
                        paintBadge('.ev-cc-badge', '.ev-cc-icon', unread);
                        paintBadge('.ev-mobile-bottom-nav__badge', '.ev-mobile-bottom-nav__icon', total);

                        // On /my-chat: if the total went up, force the chat
                        // Livewire component to refresh so the sidebar unread
                        // pill + Unread filter counter show the new message
                        // without needing the user to reload. On DOWN changes
                        // (read/seen) the chat page's own markConversationAsRead
                        // already drives the re-render, so no need to force one.
                        if (__lastTotal !== null && total > __lastTotal
                            && window.location.pathname === '/my-chat'
                            && window.Livewire) {
                            const sidebar = document.getElementById('chatSidebar');
                            const root = sidebar && sidebar.closest('[wire\\:id]');
                            if (root) {
                                const comp = window.Livewire.find(root.getAttribute('wire:id'));
                                try { comp && comp.call('refreshChat'); } catch (_) {}
                            }
                        }
                        __lastTotal = total;
                    } catch (err) {
                        console.warn('[badges] fetch failed', err);
                    }
                };

                const refresh = async () => {
                    if (document.hidden) return;
                    await window.__refreshChatBadges();
                };

                // Add/update/remove the <span> badge inside the given anchor.
                // Blade only renders the badge span when count > 0, so when
                // the user has nothing we may need to create it, and when
                // they drop back to zero we remove it.
                const paintBadge = (badgeSel, anchorSel, count) => {
                    const label = count > 99 ? '99+' : String(count);
                    document.querySelectorAll(anchorSel).forEach(anchor => {
                        let badge = anchor.querySelector(badgeSel);
                        if (count > 0) {
                            if (!badge) {
                                badge = document.createElement('span');
                                badge.className = badgeSel.replace('.', '');
                                anchor.appendChild(badge);
                            }
                            if (badge.textContent !== label) badge.textContent = label;
                        } else if (badge) {
                            badge.remove();
                        }
                    });
                };

                // Prime immediately so the badge reflects reality within a
                // beat of the first page load, then poll on an interval.
                // 8s matches the chat page's own tickPoll cadence and feels
                // like real time without hammering the DB (ChatBadges has
                // a 30s cache + request-local memo so repeat hits are cheap).
                refresh();
                setInterval(refresh, 8000);
                // Also refresh when the tab regains focus — users switching
                // back from another tab/app shouldn't have to wait a tick.
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) refresh();
                });
                // And when the window regains focus (desktop multi-tab).
                window.addEventListener('focus', refresh);
            })();
        </script>
    @endauth

    {{-- Footer --}}
    @include('components.layouts.footer-evoory')

    {{-- Mobile bottom nav for authenticated users --}}
    @include('components.mobile-user-bottom-nav')

    {{-- jQuery + Bootstrap JS (required for components and page scripts) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

    {{-- Theme JS - Deferred for performance --}}
    <script src="{{ asset('assets/js/evoory-theme.js') }}?v=20260318-4" defer></script>

    {{-- iOS Safari zoom-on-focus fix. If any form field has font-size < 16px,
         Safari auto-zooms the page on focus (and does NOT zoom back out,
         leaving the whole site stuck zoomed). Bumping to 16px on narrow
         viewports suppresses that behaviour while keeping pinch-zoom. */
         Scoped under max-width: 767px so desktop inputs are untouched. --}}
    <style>
    @media (max-width: 767px) {
        input:not([type=checkbox]):not([type=radio]):not([type=range]):not([type=color]):not([type=file]),
        select,
        textarea {
            font-size: 16px !important;
        }
    }
    </style>

    {{-- iOS Safari keyboard fix. CSS `100dvh` doesn't shrink when the virtual
         keyboard opens — the composer gets pushed below the keyboard and the
         user can't see what they're typing. We publish `--app-height` and
         `--kb-inset` from the actual visualViewport so any fullscreen page
         (chat, new profile, etc.) can bind height/padding to a value that
         always equals the visible area. --}}
    <script data-navigate-once>
    (function () {
        var vv = window.visualViewport;
        function sync() {
            var h   = vv ? vv.height : window.innerHeight;
            var inset = vv ? Math.max(0, window.innerHeight - vv.height - vv.offsetTop) : 0;
            document.documentElement.style.setProperty('--app-height', h + 'px');
            document.documentElement.style.setProperty('--kb-inset', inset + 'px');
        }
        sync();
        if (vv) {
            vv.addEventListener('resize', sync);
            vv.addEventListener('scroll', sync);
        }
        window.addEventListener('resize', sync);
        window.addEventListener('orientationchange', sync);
    })();
    </script>

    {{-- Additional page-specific JS --}}
    @stack('js')
</body>
</html>
