/**
 * Service worker for evoory Web Push notifications.
 *
 * Kept intentionally minimal — only responsible for waking up when a push
 * arrives from the server and translating it into a native OS notification
 * with an Answer / Decline pair of actions. The actual call flow (SDP,
 * ICE, media) still runs inside the page's WebRTC scripts once the user
 * taps the notification and lands on the chat.
 *
 * Deliberately NO `fetch` handler — the legacy /sw.js file was killed off
 * precisely because it intercepted GETs and served stale assets. This
 * worker only listens for `push` and `notificationclick`, so it can't
 * touch normal page requests.
 *
 * Registered from resources/views/livewire/partials/push-init.blade.php.
 * Change SW_VERSION when the message shape or actions change so browsers
 * install the new worker instead of reusing an old cached copy.
 */
const SW_VERSION = 'evoory-push-sw-v1';

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    // Take control of any already-open tabs so notifications fire without
    // needing a hard reload after first install.
    event.waitUntil(self.clients.claim());
});

/**
 * Push events are the whole reason this worker exists. Payload shape
 * (built server-side by CallController::signal on `offer`):
 *   { type: 'call.offer', callId, callType, from: { id, name, avatar },
 *     chatUrl, title, body }
 */
self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'Incoming call', body: 'Someone is calling…' };
    }

    const title = data.title || (data.from && data.from.name
        ? data.from.name + ' is calling…'
        : 'Incoming call');
    const isVideo = data.callType === 'video';
    const options = {
        body: data.body || (isVideo ? 'Video call' : 'Voice call'),
        icon: (data.from && data.from.avatar) || '/favicon.ico',
        badge: '/favicon.ico',
        tag: 'call-' + (data.callId || 'unknown'),
        // renotify: force OS to vibrate/beep even if a prior notification
        // with the same tag is still visible — an incoming call should
        // always break through, not silently replace an old one.
        renotify: true,
        requireInteraction: true,
        vibrate: [200, 100, 200, 100, 200, 100, 200],
        data: {
            callId: data.callId,
            chatUrl: data.chatUrl || '/my-chat',
        },
        actions: [
            { action: 'answer',  title: 'Answer' },
            { action: 'decline', title: 'Decline' },
        ],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

/**
 * On tap / action click: focus an existing chat tab if we have one,
 * otherwise open a fresh window. The incoming-call banner will already
 * be visible there because the offer signal is still queued server-side
 * (see /call/pending polling).
 */
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.chatUrl) || '/my-chat';
    const action = event.action; // 'answer' | 'decline' | ''  (body tap)
    const callId = (event.notification.data && event.notification.data.callId) || '';

    event.waitUntil((async () => {
        const allClients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });

        // Try to reuse an already-open tab first.
        for (const client of allClients) {
            try {
                const url = new URL(client.url);
                if (url.pathname.startsWith('/my-chat') || url.pathname === '/') {
                    await client.focus();
                    // The page can watch for these messages and
                    // programmatically click Accept / Decline so the
                    // user doesn't have to do a second tap.
                    client.postMessage({
                        type: 'evoory.call.action',
                        action: action || 'open',
                        callId: callId,
                    });
                    return;
                }
            } catch (_) { /* invalid URL, skip */ }
        }

        // No suitable tab — open a fresh one, encoding the action so the
        // page can auto-accept / auto-decline right on load.
        const sep = targetUrl.includes('?') ? '&' : '?';
        const qs  = action
            ? sep + 'call_action=' + encodeURIComponent(action) + '&call_id=' + encodeURIComponent(callId)
            : '';
        await self.clients.openWindow(targetUrl + qs);
    })());
});
