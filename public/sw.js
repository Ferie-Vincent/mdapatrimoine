const CACHE_NAME = 'scimanager-v1';
const STATIC_ASSETS = [
    '/assets/img/logo.jpg',
    '/assets/img/logo-2.jpg',
    '/assets/img/pwa-192.png',
    '/assets/img/pwa-512.png',
];

// Install: cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// Activate: clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch: Network First for HTML, Cache First for static assets
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip Chrome extensions and other non-http
    if (!request.url.startsWith('http')) return;

    const url = new URL(request.url);

    // Cache First for static assets (images, fonts, CSS, JS)
    if (
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/build/') ||
        url.pathname.match(/\.(png|jpg|jpeg|svg|gif|webp|woff2?|ttf|eot|css|js)$/)
    ) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // Network First for HTML pages
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(request).then((cached) => {
                        if (cached) return cached;
                        return new Response(
                            '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Hors ligne</title><style>body{font-family:Nunito,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f3f4f6;color:#374151;text-align:center}h1{font-size:1.5rem;margin-bottom:.5rem}p{color:#6b7280}</style></head><body><div><h1>Vous êtes hors ligne</h1><p>Vérifiez votre connexion internet et réessayez.</p></div></body></html>',
                            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                        );
                    });
                })
        );
        return;
    }
});
