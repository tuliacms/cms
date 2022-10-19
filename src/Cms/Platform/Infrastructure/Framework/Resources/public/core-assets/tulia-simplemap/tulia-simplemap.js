(() => {
    const maps = document.querySelectorAll('.tulia-simplemap');

    for (i = 0; i < maps.length; ++i) {
        const position = [
            maps[i].getAttribute('data-lat'),
            maps[i].getAttribute('data-lng'),
        ];
        const map = L.map(maps[i].getAttribute('id')).setView(position, maps[i].getAttribute('data-zoom'));
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        L.marker(position).addTo(map);
    }
})();
