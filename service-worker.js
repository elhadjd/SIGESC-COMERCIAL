// service-worker.js

// Definindo os recursos a serem armazenados em cache
const cacheName = 'my-app-cache';
const resourcesToCache = [
  '/',
  '/auth/login',
  '/PDV/Home',
  '/PDV/Pos/?'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
      caches.open('my-cache').then((cache) => {
        return cache.addAll(['/']);
      })
    );
  });

  self.addEventListener('fetch', (event) => {
    event.respondWith(
      caches.match(event.request).then((response) => {
        return response || fetch(event.request);
      })
    );
  });
