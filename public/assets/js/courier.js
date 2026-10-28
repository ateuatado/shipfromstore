(() => {
    const config = window.SHIPFROMSTORE_COURIER;
    const cards = document.querySelectorAll('.courier-order');

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
            const countdown = card.querySelector('.js-countdown');
            const collectedAt = Date.parse(`${card.dataset.collectedAt.replace(' ', 'T')}Z`);
            const tick = () => {
                const remaining = Math.max(0, 7200 - Math.floor((Date.now() - collectedAt) / 1000));
                const hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
                const seconds = String(remaining % 60).padStart(2, '0');
                countdown.textContent = `${hours}:${minutes}:${seconds}`;
                countdown.classList.toggle('text-danger', remaining <= 900);
            };
            tick();
            window.setInterval(tick, 1000);
            const route = `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=${card.dataset.pickupLat},${card.dataset.pickupLng};${card.dataset.deliveryLat},${card.dataset.deliveryLng}`;
            card.querySelector('.js-route').href = route;
            card.querySelector('.js-confirm-delivery').addEventListener('click', async () => {
                if (!window.confirm('Confirmar que a entrega foi concluída?')) return;
                const response = await fetch(config.deliveryUrl, {method: 'POST', headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: JSON.stringify({contract})});
                if (response.ok) window.location.reload();
            });
        }
    });
})();
