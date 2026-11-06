(() => {
    const config = window.SHIPFROMSTORE_DASHBOARD_MAP;
    const element = document.querySelector('#dashboard-map');
    if (!config || !element || !Array.isArray(config.orders) || config.orders.length === 0) {
        if (element) element.innerHTML = '<div class="h-100 d-flex align-items-center justify-content-center text-secondary">Nenhum pedido com localização disponível.</div>';
        return;
    }

    const first = config.orders[0];
    const map = L.map(element).setView([first.pickup.lat, first.pickup.lng], 12);
    L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Map data &copy; OpenStreetMap contributors, SRTM | Map style &copy; OpenTopoMap (CC-BY-SA)',
    }).addTo(map);

    const bounds = L.latLngBounds([]);
    const flowKeys = new Set();
    const estimateKeys = new Set();
    const formatMinutes = (seconds) => `${Math.max(1, Math.round(seconds / 60))} min`;
    const addTimeLabel = (from, to, text, color) => {
        const lat = from.lat + ((to.lat - from.lat) * 0.5);
        const lng = from.lng + ((to.lng - from.lng) * 0.5);
        L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'map-time-label',
                html: `<span style="display:flex;width:54px;height:28px;align-items:center;justify-content:center;background:#fff;color:${color};border:2px solid ${color};border-radius:999px;padding:2px 6px;font-size:12px;font-weight:700;white-space:nowrap;box-shadow:0 1px 4px #0005;">${text}</span>`,
                iconSize: [54, 28],
                iconAnchor: [27, 14],
            }),
            interactive: false,
        }).addTo(map);
    };
    const estimateUnitToPickup = async (unit, pickup) => {
        const key = `${unit.lat},${unit.lng}->${pickup.lat},${pickup.lng}`;
        if (estimateKeys.has(key) || !config.routeUrl) return;
        estimateKeys.add(key);
        try {
            const response = await fetch(config.routeUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                body: JSON.stringify({coordinates: [[unit.lng, unit.lat], [pickup.lng, pickup.lat]]}),
            });
            const result = await response.json();
            if (!response.ok) return;
            addTimeLabel(unit, pickup, formatMinutes(result.travelDurationSeconds), '#0d6efd');
        } catch (error) {
            // The line remains visible even when the external route service is unavailable.
        }
    };
    const addFlow = (from, to, color, label) => {
        const key = `${from.lat},${from.lng}->${to.lat},${to.lng}`;
        if (flowKeys.has(key)) return;
        flowKeys.add(key);
        L.polyline([[from.lat, from.lng], [to.lat, to.lng]], {color, weight: 3, opacity: 0.78})
            .addTo(map)
            .bindPopup(label);

        const arrowLat = to.lat;
        const arrowLng = to.lng;
        // Longitude grows to the right, while latitude grows upward in the
        // map. The negative latitude delta keeps the arrow aligned on screen.
        const angle = Math.atan2(-(to.lat - from.lat), to.lng - from.lng) * 180 / Math.PI;
        L.marker([arrowLat, arrowLng], {
            icon: L.divIcon({
                className: 'map-flow-arrow',
                html: `<svg width="30" height="30" viewBox="0 0 30 30" aria-hidden="true" style="transform:rotate(${angle}deg);transform-origin:27px 15px;filter:drop-shadow(0 1px 1px #fff)"><path d="M3 4 L27 15 L3 26 L8 15 Z" fill="${color}" stroke="#fff" stroke-width="1.5"/></svg>`,
                iconSize: [30, 30],
                iconAnchor: [27, 15],
            }),
            interactive: false,
        }).addTo(map);
    };
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
            addFlow(order.unit, order.pickup, '#0d6efd', `Unidade ${order.unit.id} → ponto de coleta`);
            estimateUnitToPickup(order.unit, order.pickup);
        }
        addFlow(order.pickup, order.delivery, '#198754', 'Ponto de coleta → ponto de entrega');
    });

    if (bounds.isValid()) map.fitBounds(bounds, {padding: [24, 24]});
})();
