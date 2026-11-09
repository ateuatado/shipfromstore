(() => {
    const config = window.SHIPFROMSTORE_ROUTES;
    const map = L.map('routes-map').setView([-23.5294829, -46.7386242], 12);
    L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {attribution: 'Map data &copy; OpenStreetMap contributors, SRTM | Map style &copy; OpenTopoMap (CC-BY-SA)', maxZoom: 19}).addTo(map);
    const layer = L.layerGroup().addTo(map);
    const list = document.getElementById('routes-list');
    const summary = document.getElementById('routes-summary');
    const labels = {collected: 'Coleta confirmada', in_transit: 'Em entrega', delivered: 'Entregue'};
    const colors = {collected: '#0dcaf0', in_transit: '#2457d6', delivered: '#198754'};
    const requestedContract = new URLSearchParams(window.location.search).get('contract');

    const render = (routes) => {
        layer.clearLayers();
        list.innerHTML = '';
        summary.textContent = `${routes.length} atendimento(s) com percurso disponível.`;
        const bounds = [];
        routes.forEach((route) => {
            const points = route.points.map((point) => [point.latitude, point.longitude]);
            if (points.length > 0) {
                const line = L.polyline(points, {color: colors[route.status] || '#6c757d', weight: 5, opacity: .8}).addTo(layer);
                line.bindPopup(`<strong>${route.contract}</strong><br>${route.courierName}<br>${labels[route.status] || route.status}`);
                points.forEach((point) => bounds.push(point));
                L.circleMarker(points[0], {radius: 6, color: '#172b4d', fillColor: '#fff', fillOpacity: 1}).bindTooltip(`Início · ${route.contract}`).addTo(layer);
                L.circleMarker(points[points.length - 1], {radius: 6, color: colors[route.status] || '#6c757d', fillColor: colors[route.status] || '#6c757d', fillOpacity: 1}).bindTooltip(`Último ponto · ${route.contract}`).addTo(layer);
            }
            const item = document.createElement('div');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action';
            item.tabIndex = 0;
            const distance = route.distanceMeters ? `${(route.distanceMeters / 1000).toFixed(1)} km` : 'sem distância';
            const elapsed = route.minutesAfterCollection === null ? 'em andamento' : `${route.minutesAfterCollection} min após coleta`;
            item.innerHTML = `<div class="d-flex justify-content-between gap-2"><strong>${route.contract}</strong><span class="badge" style="background:${colors[route.status] || '#6c757d'}">${labels[route.status] || route.status}</span></div><div class="small text-secondary mt-1">${route.courierName} · ${route.unitId}</div><div class="small text-secondary">${route.points.length} ponto(s) visíveis · ${distance} · ${elapsed}</div><div class="d-flex justify-content-between align-items-center gap-2 mt-2"><span class="small text-secondary">${route.privacyNotice || ''}</span><button type="button" class="btn btn-sm btn-outline-primary js-share-route">Link do cliente</button></div>`;
            item.addEventListener('click', () => { if (points.length > 0) map.fitBounds(points, {padding: [30, 30]}); });
            item.addEventListener('keydown', (event) => { if (event.key === 'Enter' && points.length > 0) map.fitBounds(points, {padding: [30, 30]}); });
            item.querySelector('.js-share-route').addEventListener('click', async (event) => {
                event.stopPropagation();
                const button = event.currentTarget;
                button.disabled = true;
                button.textContent = 'Gerando...';
                try {
                    const response = await fetch(config.shareUrl, {method: 'POST', headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: JSON.stringify({contract: route.contract, mode: 'approximate'})});
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.error || 'Não foi possível gerar o link.');
                    await navigator.clipboard.writeText(result.url);
                    button.textContent = 'Link copiado';
                } catch (error) {
                    button.disabled = false;
                    button.textContent = error.message;
                }
            });
            list.appendChild(item);
        });
        if (bounds.length > 0) map.fitBounds(bounds, {padding: [25, 25]});
        if (requestedContract) {
            const requested = routes.find((route) => route.contract === requestedContract);
            if (requested && requested.points.length > 0) {
                map.fitBounds(requested.points.map((point) => [point.latitude, point.longitude]), {padding: [35, 35]});
            }
        }
    };

    const load = async () => {
        summary.textContent = 'Atualizando percursos...';
        try {
            const response = await fetch(config.dataUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || 'Não foi possível carregar os percursos.');
            render(result.routes || []);
        } catch (error) { summary.textContent = error.message; }
    };
    document.getElementById('routes-refresh').addEventListener('click', load);
    load();
})();
