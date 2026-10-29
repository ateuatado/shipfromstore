(() => {
    const config = window.SHIPFROMSTORE_DASHBOARD_MAP;
    const element = document.querySelector('#dashboard-map');
    if (!config || !element || !Array.isArray(config.orders) || config.orders.length === 0) {
        if (element) element.innerHTML = '<div class="h-100 d-flex align-items-center justify-content-center text-secondary">Nenhum pedido com localização disponível.</div>';
        return;
    }

    const first = config.orders[0];
    const map = L.map(element).setView([first.pickup.lat, first.pickup.lng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const bounds = L.latLngBounds([]);
    const addMarker = (point, color, title, details) => {
        const marker = L.circleMarker([point.lat, point.lng], {radius: 9, color, fillColor: color, fillOpacity: 0.9, weight: 2})
            .addTo(map)
            .bindPopup(`<strong>${title}</strong><br>${details}`);
        bounds.extend([point.lat, point.lng]);
        return marker;
    };

    config.orders.forEach((order) => {
        const label = `<small>Coleta: ${order.collectionId}<br>Status: ${order.status}</small>`;
        addMarker(order.pickup, '#198754', 'Ponto de coleta', `${order.pickup.label}<br>${label}`);
        addMarker(order.delivery, '#dc3545', 'Ponto de entrega', `${order.delivery.label}<br>${label}`);
        if (order.unit) {
            addMarker(order.unit, '#0d6efd', `Unidade ${order.unit.id}`, `${order.unit.areaName}<br>${label}`);
        }
    });

    if (bounds.isValid()) map.fitBounds(bounds, {padding: [24, 24]});
})();
