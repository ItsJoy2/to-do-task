const CACHE_NAME = "edulife-todo-app-v2";
const urlsToCache = [
    "/",
    "/login",
    "css/app.css",
    "js/app.js"
];

self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});
