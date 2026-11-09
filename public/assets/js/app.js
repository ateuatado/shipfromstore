document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => new bootstrap.Tooltip(element));
    document.querySelectorAll('[data-countdown-deadline]').forEach((element) => {
        const deadline = Date.parse(`${element.dataset.countdownDeadline.replace(' ', 'T')}Z`);
        if (Number.isNaN(deadline)) return;
        const update = () => {
            const remaining = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
            const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
            const seconds = String(remaining % 60).padStart(2, '0');
            element.textContent = remaining > 0 ? `${minutes}:${seconds}` : 'prazo expirado';
            element.closest('.operational-alert')?.classList.toggle('alert-danger', remaining <= 300);
            element.closest('.operational-alert')?.classList.toggle('alert-warning', remaining > 300);
        };
        update();
        window.setInterval(update, 1000);
    });

    document.querySelectorAll('[data-random-location]').forEach((button) => {
        button.addEventListener('click', async () => {
            const form = button.closest('[data-simulator-form]');
            const type = button.dataset.randomLocation;
            const endpoint = form?.dataset.randomLocationUrl;
            if (!form || !endpoint) return;

            button.disabled = true;
            const originalLabel = button.textContent;
            button.textContent = 'Sorteando...';
            try {
                const currentAddress = form.querySelector(`#${type}_address`)?.value ?? '';
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('exclude', currentAddress);
                url.searchParams.set('_', String(Date.now()));
                const response = await fetch(url, {cache: 'no-store', headers: {Accept: 'application/json'}});
                if (!response.ok) throw new Error('Falha ao sortear endereço');
                const location = await response.json();
                form.querySelector(`#${type}_address`).value = location.address;
                form.querySelector(`#${type}_lat`).value = location.lat;
                form.querySelector(`#${type}_lng`).value = location.lng;
            } catch (error) {
                window.alert('Não foi possível sortear o endereço agora.');
            } finally {
                button.disabled = false;
                button.textContent = originalLabel;
            }
        });
    });
});
