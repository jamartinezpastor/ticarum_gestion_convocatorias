// Modal de convocatorias por alumno
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

// Inscripciones: alta y baja AJAX

const inscripcionForm = document.querySelector('[data-inscripcion-form]');
if (inscripcionForm) {
    inscripcionForm.addEventListener('submit', async e => {
        e.preventDefault();
        const convocatoriaId = inscripcionForm.dataset.convocatoriaId;
        const formData = new FormData(inscripcionForm);

        try {
            const res = await fetch(`/convocatorias/${convocatoriaId}/inscripciones`, {
                method: 'POST',
                body: formData,
            });

            if (res.ok) {
                window.location.reload();
            } else {
                const data = await res.json().catch(() => ({}));
                showAjaxMessage('error', data.message || 'Error al inscribir.');
            }
        } catch {
            showAjaxMessage('error', 'Error de conexión.');
        }
    });
}

document.querySelectorAll('[data-inscripcion-delete]').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('¿Eliminar esta inscripción?')) return;
        const id = btn.dataset.inscripcionDelete;

        try {
            const res = await fetch(`/inscripciones/${id}`, { method: 'DELETE' });

            if (res.ok) {
                window.location.reload();
            } else {
                const data = await res.json().catch(() => ({}));
                showAjaxMessage('error', data.message || 'Error al eliminar.');
            }
        } catch {
            showAjaxMessage('error', 'Error de conexión.');
        }
    });
});

function showAjaxMessage(type, message) {
    const main = document.querySelector('main.container');
    if (!main) return;

    let alert = document.querySelector('.ajax-alert');
    if (!alert) {
        alert = document.createElement('div');
        alert.className = 'ajax-alert alert';
        main.prepend(alert);
    }
    alert.className = `ajax-alert alert alert-${type}`;
    alert.textContent = message;

    setTimeout(() => alert.remove(), 5000);
}