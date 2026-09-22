{{-- Client-side registration for Web Push notifications.

     Runs on every authenticated page from app-evoory.blade.php. The
     sequence is:

       1. Confirm the browser supports Notification + Push + ServiceWorker.
       2. Register /push-sw.js.
       3. If the user hasn't been prompted before, wait for their first
          call button click before asking (browsers block Notification
          prompts that fire without a user gesture and Chrome starts
          throttling requests that appear too early). See installGesturePrompt().
       4. If permission is granted, fetch the current push subscription
          (or create a new one) and POST it to /push/subscribe.

     Also handles messages FROM the service worker (notification tap →
     auto-Accept / auto-Decline on the incoming banner) and reads
     ?call_action= from the URL when the page was opened from a
     notification tap on a closed tab.

     data-navigate-once so wire:navigate doesn't retrigger the whole
     flow on every page change — one registration is enough for the
     browser session. --}}
<script data-navigate-once>
(function () {
    if (window.__evoPushInit) return;
    window.__evoPushInit = true;

    // Feature gate — silently no-op on browsers that don't support push.
    // Notably: iOS Safari added Web Push in iOS 16.4 and ONLY when the
    // site is installed as a PWA (Add to Home Screen). Desktop / Android
    // Chrome / Edge / Firefox / Samsung Internet all work fine.
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        console.log('[push] browser does not support Web Push — skipping');
        return;
    }

    let swReg = null;
    let vapidPublicKey = null;

    function urlBase64ToUint8Array(base64) {
        const padding = '='.repeat((4 - base64.length % 4) % 4);
        const b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw = atob(b64);
        return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
    }

    function csrf() {
        return document.querySelector('meta[name=csrf-token]')?.content || '';
    }

    async function fetchVapidKey() {
        if (vapidPublicKey) return vapidPublicKey;
        try {
            const r = await fetch('/push/vapid-public-key', { credentials: 'same-origin' });
            const j = await r.json();
            vapidPublicKey = j.publicKey || null;
            return vapidPublicKey;
        } catch (_) { return null; }
    }

    async function registerSw() {
        if (swReg) return swReg;
        try {
            swReg = await navigator.serviceWorker.register('/push-sw.js', { scope: '/' });
            await navigator.serviceWorker.ready;
            return swReg;
        } catch (err) {
            console.warn('[push] SW registration failed', err);
            return null;
        }
    }

    async function persistSubscription(sub) {
        const body = JSON.parse(JSON.stringify(sub)); // strips PushSubscription methods
        // Some browsers report contentEncoding on PushManager.supportedContentEncodings —
        // pass it through so the server picks the right cipher.
        if (PushManager.supportedContentEncodings) {
            body.contentEncoding = PushManager.supportedContentEncodings.includes('aes128gcm')
                ? 'aes128gcm' : 'aesgcm';
        }
        try {
            await fetch('/push/subscribe', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });
        } catch (err) {
            console.warn('[push] subscribe POST failed', err);
        }
    }

    async function ensureSubscribed() {
        const reg = await registerSw();
        if (!reg) return;
        const key = await fetchVapidKey();
        if (!key) {
            console.log('[push] no VAPID key configured — skipping subscribe');
            return;
        }
        let sub = await reg.pushManager.getSubscription();
        if (!sub) {
            try {
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(key),
                });
            } catch (err) {
                console.warn('[push] subscribe() failed', err);
                return;
            }
        }
        await persistSubscription(sub);
    }

    /**
     * Attempt to convert the user's next real interaction into a
     * notification-permission prompt. Modern browsers reject
     * Notification.requestPermission() unless it's tied to a user gesture,
     * so we defer the ask until they click *anything* on the page.
     */
    function installGesturePrompt() {
        const handler = async () => {
            document.removeEventListener('click', handler, true);
            document.removeEventListener('touchend', handler, true);
            try {
                const perm = await Notification.requestPermission();
                if (perm === 'granted') {
                    ensureSubscribed();
                }
            } catch (err) {
                console.warn('[push] permission request failed', err);
            }
        };
        document.addEventListener('click', handler, true);
        document.addEventListener('touchend', handler, true);
    }

    // Kickoff — different paths depending on the current permission state.
    if (Notification.permission === 'granted') {
        // Already granted → immediately (re)confirm the subscription.
        ensureSubscribed();
    } else if (Notification.permission === 'default') {
        // Not asked yet → wait for the first user click before prompting.
        installGesturePrompt();
    }
    // If permission === 'denied' we don't try — that would just spam the
    // console. User has to manually re-enable in browser settings.

    // Bridge: service worker → page. When the user taps a notification
    // action, the SW postMessages us; we auto-click the corresponding
    // button on the incoming banner if it's open, otherwise cache the
    // intent for when the banner renders.
    navigator.serviceWorker.addEventListener('message', (event) => {
        const msg = event.data;
        if (!msg || msg.type !== 'evoory.call.action') return;
        window.__evoPendingCallAction = { action: msg.action, callId: msg.callId, at: Date.now() };
        // If banner is already visible, trigger action right away.
        setTimeout(() => tryApplyPendingCallAction(), 100);
    });

    function tryApplyPendingCallAction() {
        const pending = window.__evoPendingCallAction;
        if (!pending) return;
        // Only apply within 30s of the notification click — old intents shouldn't fire.
        if (Date.now() - pending.at > 30000) { window.__evoPendingCallAction = null; return; }
        if (pending.action === 'answer') {
            const btn = document.getElementById('rtcAcceptBtn');
            if (btn && document.getElementById('rtcIncoming')?.classList.contains('open')) {
                btn.click();
                window.__evoPendingCallAction = null;
            }
        } else if (pending.action === 'decline') {
            const btn = document.getElementById('rtcDeclineBtn');
            if (btn && document.getElementById('rtcIncoming')?.classList.contains('open')) {
                btn.click();
                window.__evoPendingCallAction = null;
            }
        }
    }
    // Poll a few times so a slightly-delayed banner still catches the action.
    setInterval(tryApplyPendingCallAction, 500);

    // Also handle ?call_action= on initial load (when the tab was opened
    // by the SW rather than focused).
    try {
        const params = new URLSearchParams(location.search);
        const act = params.get('call_action');
        if (act === 'answer' || act === 'decline') {
            window.__evoPendingCallAction = { action: act, callId: params.get('call_id') || '', at: Date.now() };
        }
    } catch (_) {}
})();
</script>
