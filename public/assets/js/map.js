(() => {
    const config = window.SHIPFROMSTORE_MAP;
    const map = L.map('shipfromstore-map').setView([-23.575, -46.715], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const pickup = L.marker([config.pickup.lat, config.pickup.lng]).addTo(map).bindPopup(config.pickup.label);
    const delivery = L.marker([config.delivery.lat, config.delivery.lng]).addTo(map).bindPopup(config.delivery.label);
    pickup.openPopup();
    L.circle([config.pickup.lat, config.pickup.lng], { radius: 400, color: '#198754', fillOpacity: 0.08 }).addTo(map);
    L.circle([config.delivery.lat, config.delivery.lng], { radius: 400, color: '#dc3545', fillOpacity: 0.08 }).addTo(map);

    const routeLayer = L.layerGroup().addTo(map);
    const list = document.querySelector('#unit-list');
    const status = document.querySelector('#route-status');
    const formatDistance = (meters) => `${(meters / 1000).toFixed(1)} km`;
    const formatDuration = (seconds) => `${Math.round(seconds / 60)} min`;

    const calculate = async (unit) => {
        status.textContent = `Calculando rota para ${unit.id}...`;
        routeLayer.clearLayers();
        try {
            const response = await fetch(config.routeUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                body: JSON.stringify({coordinates: [
                    [unit.lng, unit.lat],
                    [config.pickup.lng, config.pickup.lat],
                    [config.delivery.lng, config.delivery.lat],
                ]}),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || 'Falha ao calcular rota');
            status.innerHTML = `<strong>${unit.id}</strong>: ${formatDistance(result.distanceMeters)} · ${formatDuration(result.durationSeconds)} · ${result.provider}. ` +
                (result.withinSla ? '<span class="text-success">Dentro do SLA</span>' : '<span class="text-danger">Fora do SLA</span>');
        } catch (error) {
            status.textContent = error.message;
        }
    };

    config.units.forEach((unit) => {
        const marker = L.marker([unit.lat, unit.lng]).addTo(map).bindPopup(`<strong>${unit.id}</strong><br>Capacidade: ${unit.capacity}`);
        marker.on('click', () => calculate(unit));
        const item = document.createElement('button');
        item.type = 'button'; item.className = 'list-group-item list-group-item-action py-2';
        item.innerHTML = `<span class="fw-semibold">${unit.id}</span><span class="float-end text-secondary">cap. ${unit.capacity}</span>`;
        item.addEventListener('click', () => { map.setView([unit.lat, unit.lng], 14); marker.openPopup(); calculate(unit); });
        list.appendChild(item);
    });
})();
