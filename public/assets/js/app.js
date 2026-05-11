// Modal genérico: trigger por data-attribute, cierra con ×, click fuera o ESC.
document.querySelectorAll('[data-alumno-trigger]').forEach(el => {
    el.addEventListener('click', () => {
        const id = el.dataset.alumnoTrigger;
        const modal = document.querySelector(`[data-alumno-modal="${id}"]`);
        if (modal) modal.classList.add('open');
    });
});

document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', e => {
        if (e.target === backdrop || e.target.closest('.modal-close')) {
            backdrop.classList.remove('open');
        }
    });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop.open').forEach(m => m.classList.remove('open'));
    }
});