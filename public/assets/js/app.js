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
        const alumnoId = inscripcionForm.querySelector('input[name="alumno_id"]').value;

        if (!alumnoId) {
            showAjaxMessage('error', 'Selecciona un alumno del desplegable.');
            return;
        }

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

// Ahora con confirmDialog()
document.querySelectorAll('[data-inscripcion-delete]').forEach(btn => {
    btn.addEventListener('click', async () => {
        const ok = await confirmDialog('¿Eliminar esta inscripción?');
        if (!ok) return;

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

// input + lista filtrable

document.querySelectorAll('[data-combo]').forEach(combo => {
    const input  = combo.querySelector('.combo-input');
    const hidden = combo.querySelector('input[type="hidden"]');
    const items  = combo.querySelectorAll('.combo-options li');

    const open  = () => combo.classList.add('open');
    const close = () => combo.classList.remove('open');

    input.addEventListener('focus', () => {
        items.forEach(li => li.style.display = '');
        open();
    });

    // Delay para que el mousedown de los items registre antes de cerrar.
    input.addEventListener('blur', () => setTimeout(close, 150));

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase().trim();
        hidden.value = '';  // al teclear se pierde la selección previa
        items.forEach(li => {
            li.style.display = (q === '' || li.dataset.search.includes(q)) ? '' : 'none';
        });
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            const first = combo.querySelector('.combo-options li:not([style*="display: none"])');
            if (first) {
                e.preventDefault();
                selectItem(first);
            }
        } else if (e.key === 'Escape') {
            close();
        }
    });

    items.forEach(li => {
        li.addEventListener('mousedown', e => {
            e.preventDefault();  // para no perder el focus antes de procesar el clic
            selectItem(li);
        });
    });

    function selectItem(li) {
        input.value = li.textContent.replace(/\s+/g, ' ').trim();
        hidden.value = li.dataset.value;
        close();
    }
});

// Modal de confirmación

function confirmDialog(message) {
    return new Promise(resolve => {
        const modal  = document.getElementById('confirm-modal');
        modal.querySelector('[data-confirm-message]').textContent = message;
        modal.classList.add('open');

        const ok     = modal.querySelector('[data-confirm-ok]');
        const cancel = modal.querySelector('[data-confirm-cancel]');
        const close  = modal.querySelector('.modal-close');
        cancel.focus();

        const finish = (result) => {
            modal.classList.remove('open');
            ok.removeEventListener('click', onOk);
            cancel.removeEventListener('click', onCancel);
            close.removeEventListener('click', onCancel);
            modal.removeEventListener('mousedown', onBackdrop);
            document.removeEventListener('keydown', onKey);
            resolve(result);
        };
        const onOk       = () => finish(true);
        const onCancel   = () => finish(false);
        const onBackdrop = (e) => { if (e.target === modal) finish(false); };
        const onKey      = (e) => { if (e.key === 'Escape') finish(false); };

        ok.addEventListener('click', onOk);
        cancel.addEventListener('click', onCancel);
        close.addEventListener('click', onCancel);
        modal.addEventListener('mousedown', onBackdrop);
        document.addEventListener('keydown', onKey);
    });
}

// Sustituye al confirm() nativo en formularios HTML.
// Uso: <form data-confirm="¿Eliminar...?">
document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const ok = await confirmDialog(form.dataset.confirm);
        if (ok) form.submit();
    });
});