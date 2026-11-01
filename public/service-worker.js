/*
 * Antik Nümizmatik PWA Service Worker (v1.0)
 * Müze, arazi ve zayıf internet ortamları için çevrimdışı sikke kataloğu ve görsel önbellekleme
 */

const CACHE_NAME_STATIC = 'numismatic-static-v1';
const CACHE_NAME_PAGES = 'numismatic-pages-v1';
const CACHE_NAME_IMAGES = 'numismatic-images-v1';

const STATIC_SHELL_ASSETS = [
  '/',
  '/offline',
  '/manifest.json',
  '/coins',
  '/periods',
  '/rulers',
  'https://cdn.tailwindcss.com',
  'https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap'
];

// 1. Kurulum (Install) & Pre-caching
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME_STATIC).then((cache) => {
      return cache.addAll(STATIC_SHELL_ASSETS).catch((err) => {
        console.warn('Statik önbellekleme sırasında bazı kaynaklar atlandı:', err);
      });
    }).then(() => self.skipWaiting())
  );
});

// 2. Etkinleştirme (Activate) & Eski Önbellekleri Temizleme
self.addEventListener('activate', (event) => {
  const currentCaches = [CACHE_NAME_STATIC, CACHE_NAME_PAGES, CACHE_NAME_IMAGES];
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (!currentCaches.includes(key)) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// 3. İstekleri Yakalama (Fetch Strategy)
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // Sadece GET istekleri önbelleklenebilir
  if (request.method !== 'GET') {
    return;
  }

  // API veya Admin İsteklerini Doğrudan Ağ Üzerinden Çalıştır
  if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/admin')) {
    return;
  }

  // Strateji A: Görseller için Cache-First (Özellikle sikke WebP fotoğrafları)
  if (
    request.destination === 'image' ||
    url.pathname.includes('/storage/coins/') ||
    url.pathname.endsWith('.webp') ||
    url.pathname.endsWith('.jpg') ||
    url.pathname.endsWith('.png')
  ) {
    event.respondWith(
      caches.open(CACHE_NAME_IMAGES).then(async (cache) => {
        const cachedResponse = await cache.match(request);
        if (cachedResponse) {
          return cachedResponse;
        }

        try {
          const networkResponse = await fetch(request);
          if (networkResponse && networkResponse.status === 200) {
            cache.put(request, networkResponse.clone());
          }
          return networkResponse;
        } catch (error) {
          // Çevrimdışında görsel bulunamazsa boş veya fallback
          return new Response('', { status: 404, statusText: 'Offline Image Not Found' });
        }
      })
    );
    return;
  }

  // Strateji B: Sayfa Gezinmeleri (HTML Navigation) İçin Network-First (Önbellek & Çevrimdışı Fallback)
  if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME_PAGES).then((cache) => cache.put(request, responseClone));
          }
          return networkResponse;
        })
        .catch(async () => {
          // Ağ koptuysa önce ziyaret edilen sayfa önbelleğini ara
          const cachedPage = await caches.match(request);
          if (cachedPage) {
            return cachedPage;
          }

          // Sayfa daha önce gezilmemişse çevrimdışı fallback sayfasını göster
          const offlineFallback = await caches.match('/offline');
          return offlineFallback || new Response('Çevrimdışı mod: İnternet bağlantısı yok.', {
            headers: { 'Content-Type': 'text/html; charset=utf-8' },
          });
        })
    );
    return;
  }

  // Strateji C: Diğer Statik Kaynaklar İçin Stale-While-Revalidate
  event.respondWith(
    caches.match(request).then((cachedResponse) => {
      const fetchPromise = fetch(request).then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
          caches.open(CACHE_NAME_STATIC).then((cache) => cache.put(request, networkResponse.clone()));
        }
        return networkResponse;
      }).catch(() => {
        // Sessiz hata
      });

      return cachedResponse || fetchPromise;
    })
  );
});

