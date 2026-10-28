(() => {
    const config = window.SHIPFROMSTORE_COURIER;
    const cards = document.querySelectorAll('.courier-order');
    const updateCountdown = (element, collectedAt) => {
        const parsed = Date.parse(`${collectedAt.replace(' ', 'T')}Z`);
        if (Number.isNaN(parsed)) return;
        const tick = () => {
            const remaining = Math.max(0, 7200 - Math.floor((Date.now() - parsed) / 1000));
            const hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const seconds = String(remaining % 60).padStart(2, '0');
            element.textContent = `${hours}:${minutes}:${seconds}`;
            element.classList.toggle('text-danger', remaining <= 900);
        };
        tick();
        window.setInterval(tick, 1000);
    };
    document.querySelectorAll('.js-focus-countdown').forEach((element) => updateCountdown(element, element.dataset.collectedAt));

    cards.forEach((card) => {
        const contract = card.dataset.contract;
        const status = card.dataset.status;
        const signature = card.querySelector('.js-signature');
        const error = card.querySelector('.js-error');
        let drawing = false;

        if (signature) {
            const context = signature.getContext('2d');
            const resize = () => {
                const ratio = window.devicePixelRatio || 1;
                const width = signature.clientWidth;
                signature.width = width * ratio;
                signature.height = 180 * ratio;
                context.scale(ratio, ratio);
                context.lineWidth = 2;
                context.lineCap = 'round';
                context.strokeStyle = '#172b4d';
            };
            const point = (event) => {
                const rect = signature.getBoundingClientRect();
                const source = event.touches ? event.touches[0] : event;
                return {x: source.clientX - rect.left, y: source.clientY - rect.top};
            };
            const start = (event) => { drawing = true; context.beginPath(); const p = point(event); context.moveTo(p.x, p.y); event.preventDefault(); };
            const move = (event) => { if (!drawing) return; const p = point(event); context.lineTo(p.x, p.y); context.stroke(); event.preventDefault(); };
            const stop = () => { drawing = false; };
            ['mousedown', 'touchstart'].forEach((event) => signature.addEventListener(event, start, {passive: false}));
            ['mousemove', 'touchmove'].forEach((event) => signature.addEventListener(event, move, {passive: false}));
            ['mouseup', 'mouseleave', 'touchend'].forEach((event) => signature.addEventListener(event, stop));
            window.addEventListener('resize', resize);
            resize();
            card.querySelector('.js-clear-signature').addEventListener('click', () => { context.clearRect(0, 0, signature.width, signature.height); });
            card.querySelector('.js-confirm-collection').addEventListener('click', async () => {
                error.textContent = '';
                const data = signature.toDataURL('image/png');
                try {
                    const response = await fetch(config.collectionUrl, {method: 'POST', headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: JSON.stringify({contract, signature: data})});
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.error || 'Não foi possível confirmar a coleta.');
                    window.location.reload();
                } catch (exception) { error.textContent = exception.message; }
            });
        }

        if (status === 'collected' || status === 'in_transit') {
            let trackingWatchId = null;
            let lastSentAt = 0;
            let lastPosition = null;
            const trackingStatus = card.querySelector('.js-tracking-status');
            const distanceMeters = (first, second) => {
                const radians = (value) => value * Math.PI / 180;
                const earthRadius = 6371000;
                const dLat = radians(second.latitude - first.latitude);
                const dLng = radians(second.longitude - first.longitude);
                const a = Math.sin(dLat / 2) ** 2 + Math.cos(radians(first.latitude)) * Math.cos(radians(second.latitude)) * Math.sin(dLng / 2) ** 2;
                return 2 * earthRadius * Math.asin(Math.min(1, Math.sqrt(a)));
            };
            const sendTrackingPoint = async (position) => {
                const current = {latitude: position.coords.latitude, longitude: position.coords.longitude};
                const now = Date.now();
                if (lastPosition && now - lastSentAt < 15000 && distanceMeters(lastPosition, current) < 50) return;
                try {
                    const response = await fetch(config.trackingUrl, {method: 'POST', headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: JSON.stringify({contract, latitude: current.latitude, longitude: current.longitude, accuracy: position.coords.accuracy})});
                    if (!response.ok) throw new Error('Não foi possível registrar a posição.');
                    lastPosition = current;
                    lastSentAt = now;
                    if (trackingStatus) trackingStatus.textContent = 'Rastreamento ativo. Último ponto registrado agora.';
                } catch (exception) {
                    if (trackingStatus) trackingStatus.textContent = 'Rastreamento aguardando conexão.';
                }
            };
            const trackingError = (error) => {
                if (trackingStatus) trackingStatus.textContent = error.code === 1 ? 'Permissão de localização não concedida.' : 'Não foi possível obter a localização agora.';
            };
            if ('geolocation' in navigator) {
                trackingWatchId = navigator.geolocation.watchPosition(sendTrackingPoint, trackingError, {enableHighAccuracy: true, maximumAge: 10000, timeout: 15000});
            } else if (trackingStatus) trackingStatus.textContent = 'Este dispositivo não oferece localização.';

            const countdown = card.querySelector('.js-countdown');
            updateCountdown(countdown, card.dataset.collectedAt);
            const route = `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=${card.dataset.pickupLat},${card.dataset.pickupLng};${card.dataset.deliveryLat},${card.dataset.deliveryLng}`;
            card.querySelector('.js-route').href = route;
            card.querySelector('.js-confirm-delivery').addEventListener('click', async () => {
                if (!window.confirm('Confirmar que a entrega foi concluída?')) return;
                if (trackingWatchId !== null) navigator.geolocation.clearWatch(trackingWatchId);
                const response = await fetch(config.deliveryUrl, {method: 'POST', headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: JSON.stringify({contract})});
                if (response.ok) window.location.reload();
            });
        }
    });
})();
