(() => {
    const config = window.SHIPFROMSTORE_MAP;
    const map = L.map('shipfromstore-map').setView([config.center.lat, config.center.lng], 12);
    L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Map data &copy; OpenStreetMap contributors, SRTM | Map style &copy; OpenTopoMap (CC-BY-SA)',
    }).addTo(map);

    const pickup = L.marker([config.pickup.lat, config.pickup.lng]).addTo(map).bindPopup(config.pickup.label);
    const delivery = L.marker([config.delivery.lat, config.delivery.lng]).addTo(map).bindPopup(config.delivery.label);
    const coverage = L.circle([config.center.lat, config.center.lng], {
        radius: config.radiusMeters,
        color: '#0d6efd',
        fillColor: '#0d6efd',
        fillOpacity: 0.05,
        weight: 2,
    }).addTo(map).bindPopup(`Área de referência: ${config.center.label}<br>Raio: 15 km`);
    L.marker([config.center.lat, config.center.lng]).addTo(map).bindPopup(config.center.label);
    map.fitBounds(coverage.getBounds(), {padding: [18, 18]});
    pickup.openPopup();
    L.circle([config.pickup.lat, config.pickup.lng], { radius: 400, color: '#198754', fillOpacity: 0.08 }).addTo(map);
    L.circle([config.delivery.lat, config.delivery.lng], { radius: 400, color: '#dc3545', fillOpacity: 0.08 }).addTo(map);

    const routeLayer = L.layerGroup().addTo(map);
    const list = document.querySelector('#unit-list');
    const areaSummary = document.querySelector('#area-summary');
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
        const marker = L.marker([unit.lat, unit.lng]).addTo(map).bindPopup(`<strong>${unit.id}</strong><br>${unit.areaName}<br>Capacidade: ${unit.capacity}`);
        marker.on('click', () => calculate(unit));
        const item = document.createElement('button');
        item.type = 'button'; item.className = 'list-group-item list-group-item-action py-2';
        item.innerHTML = `<span class="fw-semibold">${unit.id}</span><span class="float-end text-secondary">${unit.areaName}</span>`;
        item.addEventListener('click', () => { map.setView([unit.lat, unit.lng], 14); marker.openPopup(); calculate(unit); });
        list.appendChild(item);
    });

    const areaCounts = config.areas.map((area) => {
        const count = config.units.filter((unit) => unit.areaId === area.id).length;
        return `${area.name}: ${count} unidades`;
    });
    areaSummary.textContent = areaCounts.join(' · ');
})();
