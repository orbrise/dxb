{{-- Admin-side support-call inbox.

     Any admin logged into the admin panel sees an "incoming support call"
     toast when a user initiates a call from the Support chat on the public
     site. The toast is shown by subscribing to the shared `support-inbox`
     private channel and watching for CallSignal events with type='offer'
     and isSupport=true.

     First admin to tap Accept wins — the atomic UPDATE in
     CallController::supportAccept() returns 409 for everyone else. On a
     winning claim we hand the stored SDP offer to window.rtcAcceptSupport()
     which runs the standard callee answer flow against the caller.

     Loaded only for admins (@if guard). The webrtc-ui + webrtc-scripts
     partials provide the full in-call modal (same UI the public site uses). --}}
@auth
    @if(auth()->user()->is_admin ?? false)

<style>
    /* Floating toast anchored top-right. Mirrors the public site's
       incoming-call modal visually so admins recognize the pattern. */
    .admin-support-call-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 100080;
        background: #15191B;
        border: 1px solid #23292B;
        border-radius: 14px;
        padding: 18px 22px 16px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.6);
        display: none;
        min-width: 320px;
        color: #fff;
        animation: adminSupportCallPulse 1.5s ease-in-out infinite;
    }
    .admin-support-call-toast.open { display: block; }
    @keyframes adminSupportCallPulse {
        0%,100% { box-shadow: 0 12px 40px rgba(0,0,0,0.6), 0 0 0 0 rgba(29,161,242,0.35); }
        50%     { box-shadow: 0 12px 40px rgba(0,0,0,0.6), 0 0 0 14px rgba(29,161,242,0); }
    }
    .admin-support-call-toast .who {
        display: flex; align-items: center; gap: 12px; margin-bottom: 14px;
    }
    .admin-support-call-toast .avatar {
        width: 46px; height: 46px; border-radius: 50%;
        background: #1DA1F2; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; overflow: hidden;
    }
    .admin-support-call-toast .avatar img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .admin-support-call-toast .name { font-weight: 600; font-size: 15px; }
    .admin-support-call-toast .sub { font-size: 12px; color: #8b969a; }
    .admin-support-call-toast .actions { display: flex; gap: 10px; }
    .admin-support-call-toast .btn-accept,
    .admin-support-call-toast .btn-decline {
        flex: 1; border: none; padding: 10px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        font-family: inherit;
    }
    .admin-support-call-toast .btn-accept  { background: #22c55e; color: #fff; }
    .admin-support-call-toast .btn-decline { background: #ef4444; color: #fff; }
    .admin-support-call-toast .btn-accept:hover  { background: #16a34a; }
    .admin-support-call-toast .btn-decline:hover { background: #dc2626; }
</style>

{{-- Live-status pill (bottom-left). Flips green when we're actually
     subscribed to support-inbox so an admin can tell at a glance that
     the listener is live. Removed by the JS below if Echo never
     initializes. Styled minimally to not clash with the admin chrome. --}}
<div id="adminSupportCallStatus" style="position:fixed;bottom:14px;left:14px;z-index:100075;background:#1f2937;color:#fff;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.25);display:inline-flex;align-items:center;gap:8px;font-family:system-ui,-apple-system,'Segoe UI',sans-serif;">
    <span id="adminSupportCallStatusDot" style="width:8px;height:8px;border-radius:50%;background:#f59e0b;flex-shrink:0;"></span>
    <span id="adminSupportCallStatusLabel">Support calls: connecting…</span>
</div>

<div class="admin-support-call-toast" id="adminSupportCallToast" role="dialog" aria-label="Incoming support call">
    <div class="who">
        <div class="avatar" id="adminSupportCallAvatar">?</div>
        <div>
            <div class="name" id="adminSupportCallName">—</div>
            <div class="sub"><span id="adminSupportCallType">Voice</span> call from a user</div>
        </div>
    </div>
    <div class="actions">
        <button type="button" class="btn-decline" id="adminSupportCallDeclineBtn">Dismiss</button>
        <button type="button" class="btn-accept"  id="adminSupportCallAcceptBtn">Accept</button>
    </div>
</div>

{{-- Hidden ringtone — plays while a support call is pending so an admin
     with the browser tab in the background still hears it. Source is
     set dynamically from the server-side settings only if the file
     exists (checked below); keeps us off a hard-coded path that would
     404 on any install missing the asset. --}}
<audio id="adminSupportCallRing" loop preload="auto" data-no-player style="display:none;position:absolute;left:-9999px;width:0;height:0;">
    @php $__ringFile = public_path('assets/newtheme/ringtone.mp3'); @endphp
    @if(file_exists($__ringFile))
        <source src="{{ asset('assets/newtheme/ringtone.mp3') }}" type="audio/mpeg">
    @endif
</audio>

{{-- Load Reverb + full WebRTC UI on every admin page so the admin can
     actually take the call. Vite dev-server serves app.js which boots
     Echo globally; the webrtc-* partials carry the in-call modal + logic. --}}
@vite(['resources/js/app.js'])
@include('livewire.partials.webrtc-ui')
@include('livewire.partials.webrtc-scripts')

{{-- theme.js enableMedia() replaces every <video>/<audio> on the page with a
     MediaElement player: the real element is renamed and buried inside a
     <mediaelementwrapper>, so srcObject assignments silently die and a control
     bar renders inside the call modal. It skips [data-no-player], but that file
     ships with no cache-buster, so a stale copy would silently undo the fix —
     this partial is server-rendered and cannot go stale. --}}
<script>
(function () {
    if (!window.jQuery) return;
    var $ = window.jQuery;
    var orig = $.fn && $.fn.mediaelementplayer;
    if (typeof orig !== 'function' || orig.__rtcSkip) return;
    var guarded = function (options) { return orig.call(this.not('[data-no-player]'), options); };
    guarded.__rtcSkip = true;
    $.fn.mediaelementplayer = guarded;
})();
</script>

<script>
(function () {
    if (window.__adminSupportCallWired) return;
    window.__adminSupportCallWired = true;

    // An insecure origin kills the call path twice over: the browser removes
    // navigator.mediaDevices (so Accept can never answer) and Reverb is only
    // reachable over wss. Say so up front instead of letting it look like a
    // socket bug.
    if (!window.isSecureContext) {
        console.error('[admin-support-call] Insecure origin — support calls cannot work here. Open the admin panel over HTTPS.');
        const stamp = () => {
            const dot = document.getElementById('adminSupportCallStatusDot');
            const lbl = document.getElementById('adminSupportCallStatusLabel');
            if (!dot || !lbl) return false;
            dot.style.background = '#ef4444';
            lbl.textContent = 'Support calls: HTTPS required';
            return true;
        };
        if (!stamp()) document.addEventListener('DOMContentLoaded', stamp);
    }

    // Signals currently showing a toast — keyed by call_id. Lets us dismiss
    // a specific toast when a `support-claimed` event tells us another admin
    // won the race (prevents the toast from ringing forever on this screen).
    const pending = new Map(); // call_id → { signal, timeoutId }

    const toast     = document.getElementById('adminSupportCallToast');
    const avatarEl  = document.getElementById('adminSupportCallAvatar');
    const nameEl    = document.getElementById('adminSupportCallName');
    const typeEl    = document.getElementById('adminSupportCallType');
    const acceptBtn = document.getElementById('adminSupportCallAcceptBtn');
    const declineBtn= document.getElementById('adminSupportCallDeclineBtn');
    const ring      = document.getElementById('adminSupportCallRing');

    let activeCallId = null; // the signal currently rendered in the toast

    function escapeAttr(s) {
        return String(s || '').replace(/"/g, '&quot;');
    }

    function show(signal) {
        activeCallId = signal.callId;
        nameEl.textContent = signal.fromName || 'A user';
        typeEl.textContent = signal.callType === 'video' ? 'Video' : 'Voice';
        if (signal.fromAvatar) {
            avatarEl.innerHTML = '<img src="' + escapeAttr(signal.fromAvatar) + '" alt="">';
        } else {
            avatarEl.textContent = (signal.fromName || '?').charAt(0).toUpperCase();
        }
        toast.classList.add('open');
        try { ring.currentTime = 0; ring.play().catch(() => {}); } catch (_) {}
    }

    function hide() {
        toast.classList.remove('open');
        activeCallId = null;
        try { ring.pause(); ring.currentTime = 0; } catch (_) {}
    }

    function dismiss(callId) {
        const entry = pending.get(callId);
        if (entry && entry.timeoutId) clearTimeout(entry.timeoutId);
        pending.delete(callId);
        if (activeCallId === callId) {
            // If more pending signals are queued, surface the next one;
            // otherwise close the toast entirely.
            const next = pending.values().next().value;
            if (next) show(next.signal); else hide();
        }
    }

    acceptBtn.addEventListener('click', async function () {
        if (!activeCallId) return;
        const entry = pending.get(activeCallId);
        if (!entry) return;
        const signal = entry.signal;
        const callId = signal.callId;

        acceptBtn.disabled = true;
        acceptBtn.textContent = 'Connecting…';

        try {
            const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
            const res = await fetch({!! json_encode(route('call.support.accept')) !!}, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ call_id: callId }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                if (res.status === 409) {
                    // Another admin beat us to it — dismiss quietly.
                    dismiss(callId);
                    return;
                }
                throw new Error(data.error || ('status ' + res.status));
            }
            // Hand the stored offer + claim data to webrtc-scripts so it
            // runs the normal answer flow against the caller.
            dismiss(callId);
            if (typeof window.rtcAcceptSupport === 'function') {
                await window.rtcAcceptSupport(signal, data);
            } else {
                alert('Call UI failed to load. Please refresh and try again.');
            }
        } catch (err) {
            console.error('[admin-support-call] accept failed', err);
            alert('Could not accept the call: ' + (err && err.message ? err.message : err));
        } finally {
            acceptBtn.disabled = false;
            acceptBtn.textContent = 'Accept';
        }
    });

    declineBtn.addEventListener('click', function () {
        if (!activeCallId) return;
        // Local dismiss only — other admins can still answer.
        dismiss(activeCallId);
    });

    function setStatus(color, label) {
        const dot = document.getElementById('adminSupportCallStatusDot');
        const lbl = document.getElementById('adminSupportCallStatusLabel');
        if (dot) dot.style.background = color;
        if (lbl) lbl.textContent = label;
    }

    let echoWaitAttempts = 0;
    function wireChannel() {
        if (!window.Echo) {
            echoWaitAttempts++;
            if (echoWaitAttempts > 20) {
                setStatus('#ef4444', 'Support calls: Echo not loaded');
                console.error('[admin-support-call] window.Echo never appeared — Vite is probably not loading app.js on this admin page');
                return;
            }
            console.log('[admin-support-call] waiting for Echo, attempt ' + echoWaitAttempts);
            setTimeout(wireChannel, 500);
            return;
        }
        try {
            const ch = window.Echo.private('support-inbox');
            // Bind connector-level status events so we can see auth failures
            // / connection drops in real time instead of guessing.
            if (window.Echo.connector?.pusher?.connection) {
                const conn = window.Echo.connector.pusher.connection;
                conn.bind('connected', () => {
                    console.log('[admin-support-call] Reverb WS connected');
                    setStatus('#22c55e', 'Support calls: live');
                });
                conn.bind('disconnected', () => {
                    console.warn('[admin-support-call] Reverb WS disconnected');
                    setStatus('#f59e0b', 'Support calls: reconnecting');
                });
                conn.bind('error', (e) => {
                    console.error('[admin-support-call] Reverb WS error', e);
                    setStatus('#ef4444', 'Support calls: error');
                });
                if (conn.state === 'connected') {
                    setStatus('#22c55e', 'Support calls: live');
                }
            }

            // Subscription success / failure — tells us if channel auth
            // (support-inbox: requires is_admin) is working.
            if (typeof ch.subscribed === 'function') {
                ch.subscribed(() => {
                    console.log('[admin-support-call] subscribed to support-inbox OK');
                    setStatus('#22c55e', 'Support calls: live');
                });
            }
            if (typeof ch.error === 'function') {
                ch.error((err) => {
                    console.error('[admin-support-call] subscribe failed', err);
                    setStatus('#ef4444', 'Support calls: auth failed');
                });
            }

            const forward = function (e) {
                console.log('[admin-support-call] event received', e);
                if (!e || e.type !== 'offer') {
                    // support-claimed signal → dismiss matching pending toast
                    if (e && e.type === 'support-claimed' && e.callId) {
                        dismiss(e.callId);
                    }
                    return;
                }
                if (!e.isSupport || !e.callId) return;
                // De-dupe: a signal can arrive multiple times via different
                // bindings (Pusher/dot-prefix) — ignore if we already have it.
                if (pending.has(e.callId)) return;

                // Auto-expire the toast after 45 s if nobody answers (matches
                // the caller's own ringTimeout in webrtc-scripts).
                const timeoutId = setTimeout(() => dismiss(e.callId), 45000);
                pending.set(e.callId, { signal: e, timeoutId });

                // Only one toast visible at a time; if another is already up,
                // keep it visible and show this one when the active clears.
                if (!activeCallId) show(e);
            };
            ch.listen('.CallSignal', forward);
            ch.listen('CallSignal', forward);
            ch.listen('.App\\Events\\CallSignal', forward);
            console.log('[admin-support-call] bound to support-inbox');
        } catch (err) {
            console.warn('[admin-support-call] channel bind failed', err);
            setStatus('#ef4444', 'Support calls: bind failed');
            setTimeout(wireChannel, 2000);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', wireChannel);
    } else {
        wireChannel();
    }
})();
</script>

    @endif
@endauth
