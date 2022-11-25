(() => {
    const maps = document.querySelectorAll('.tulia-simplemap');

    for (i = 0; i < maps.length; ++i) {
        const position = [
            maps[i].getAttribute('data-lat'),
            maps[i].getAttribute('data-lng'),
        ];
        const map = L.map(maps[i].getAttribute('id'), { scrollWheelZoom: false }).setView(position, maps[i].getAttribute('data-zoom'));
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        const markerIcon = L.icon({
            iconUrl: '/assets/core/leaflet/images/marker-icon.png',
            iconRetinaUrl: '/assets/core/leaflet/images/marker-icon-2x.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            shadowUrl: '/assets/core/leaflet/images/marker-shadow.png',
            shadowSize: [41, 41],
        });
        L.marker(position, {icon: markerIcon}).addTo(map);
    }
})();
