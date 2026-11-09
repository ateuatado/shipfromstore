(() => {
    const config = window.SHIPFROMSTORE_PUBLIC_TRACKING;
    const map = L.map('public-tracking-map').setView([-23.5294829, -46.7386242], 12);
    L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Map data &copy; OpenStreetMap contributors, SRTM | Map style &copy; OpenTopoMap (CC-BY-SA)',
    }).addTo(map);

    const points = (config.points || []).map((point) => [point.latitude, point.longitude]);
    const layers = [];
    if (points.length > 1) {
        layers.push(L.polyline(points, {color: '#dfac34', weight: 5, opacity: .9}).addTo(map));
    }
    if (points.length > 0) {
        layers.push(L.circleMarker(points[0], {radius: 7, color: '#071427', fillColor: '#dfac34', fillOpacity: 1}).bindTooltip('Início exibido do percurso').addTo(map));
        layers.push(L.circleMarker(points[points.length - 1], {radius: 7, color: '#071427', fillColor: '#a8d28e', fillOpacity: 1}).bindTooltip('Última localização registrada').addTo(map));
    }
    if (config.delivery && config.delivery.lat && config.delivery.lng) {
        layers.push(L.marker([config.delivery.lat, config.delivery.lng]).bindPopup('Destino da entrega').addTo(map));
    }
    const bounds = points.slice();
    if (config.delivery && config.delivery.lat && config.delivery.lng) bounds.push([config.delivery.lat, config.delivery.lng]);
    if (bounds.length > 0) map.fitBounds(bounds, {padding: [25, 25]});
})();
