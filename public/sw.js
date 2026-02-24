// Service Worker for Performance Optimization
const CACHE_NAME = 'dxb-escorts-v1.0';
const STATIC_CACHE_URLS = [
    '/',
    '/assets/css/app.css',
    '/assets/js/app.js',
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js',
    'https://d257pz9kz95xf4.cloudfront.net/assets/application-e5aa5306f57893c0ae54df98533a2c3df315b46f19169f98d7f9be96ae07b91c.css'
];

// Install event - cache static resources
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_CACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Skip external requests
    if (!event.request.url.startsWith(self.location.origin) && 
        !event.request.url.includes('jquery.com') && 
        !event.request.url.includes('jsdelivr.net') &&
        !event.request.url.includes('cloudfront.net')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version or fetch from network
                if (response) {
                    return response;
                }

                return fetch(event.request).then((response) => {
                    // Don't cache non-successful responses
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }

                    // Clone the response
                    const responseToCache = response.clone();

                    // Cache static assets
                    if (event.request.url.match(/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/)) {
                        caches.open(CACHE_NAME)
                            .then((cache) => {
                                cache.put(event.request, responseToCache);
                            });
                    }

                    return response;
                });
            })
            .catch(() => {
                // Fallback for offline pages
                if (event.request.destination === 'document') {
                    return caches.match('/offline.html');
                }
            })
    );
});
