// Kill-switch for a legacy service worker that used to live at this URL.
//
// Older deployments registered /serviceworker.js (likely from a Laravel PWA
// package that's no longer in the codebase) with a fetch handler that
// intercepted and cached every network request. The handler would then hang
// or return stale 504s for calls like /call/pending and /livewire/update,
// stacking up pending fetches in the browser and slowing the whole chat
// page to a crawl.
//
// The SW is no longer registered by any active layout, but any browser that
// installed the old version keeps it alive until it fetches this URL again
// and sees different bytes. This file does three things on activate:
//   1. delete every cache it ever created
//   2. unregister itself
//   3. reload open tabs so the next navigation hits the network directly
//
// IMPORTANT: deliberately NO `fetch` handler. The whole bug was the old
// worker intercepting requests. This replacement must not do that.

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const cacheNames = await caches.keys();
        await Promise.all(cacheNames.map((name) => caches.delete(name)));

        await self.registration.unregister();

        const clients = await self.clients.matchAll({ type: 'window' });
        clients.forEach((client) => client.navigate(client.url));
    })());
});
