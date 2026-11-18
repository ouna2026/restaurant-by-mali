self.addEventListener("install", function(e){
  e.waitUntil(
    caches.open("rbm-v1").then(function(cache){
      return cache.addAll(["./", "./index.html"]);
    })
  );
});
self.addEventListener("fetch", function(e){
  e.respondWith(
    caches.match(e.request).then(function(res){
      return res || fetch(e.request);
    })
  );
});
