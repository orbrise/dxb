// Self-unregistering no-op service worker.
//
// A previous version of this file aggressively cached HTML/CSS/JS and
// intercepted all GET requests, which caused stale assets to be served on
// navigation (broken styling on the listing page after returning from /news).
// The SW is no longer registered by any active layout, but browsers that
// installed it from the legacy layouts will keep it alive until they fetch
// this URL again and see different bytes.
//
// What this version does:
//   1. install / activate immediately, claim all clients
//   2. unregister itself
//   3. delete every cache it ever created
//   4. reload open tabs so the next navigation hits the network directly

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

// No fetch handler — every request goes straight to the network.
