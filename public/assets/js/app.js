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
});
