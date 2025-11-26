// javascript
document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('.btn-edit, .btn-edit-holiday');
    if (!btn) return;

    ev.preventDefault();

    // cible du modal (support data-bs-target ou fallback vers le modal existant)
    const target = btn.getAttribute('data-bs-target') || btn.dataset.bsTarget || '#modalAddHolidayPeriod';
    const modalEl = document.querySelector(target);
    if (!modalEl) {
        console.error('Modal introuvable pour le sélecteur :', target);
        return;
    }

    // récupère les datas du bouton (ou fallback sur la ligne)
    const row = btn.closest('tr');
    const rawId = btn.dataset.id || row?.dataset.id || '';
    const rawName = btn.dataset.name || row?.dataset.name || getCellText(row, 1);
    const rawStart = btn.dataset.start || row?.dataset.start || getCellText(row, 2);
    const rawEnd = btn.dataset.end || row?.dataset.end || getCellText(row, 3);

    // normalise les dates pour input[type="date"] (YYYY-MM-DD)
    const start = normalizeToIsoDate(rawStart);
    const end = normalizeToIsoDate(rawEnd);

    // trouve le formulaire dans le modal (compatible avec différents noms)
    const form = modalEl.querySelector('form#updateHoliday') || modalEl.querySelector('form#formAddHolidayPeriod') || modalEl.querySelector('form');
    if (form) {
        setField(form, ['id', 'holidayid', 'holidaysid'], rawId);
        setField(form, ['HolidayName', 'periodName', 'name'], rawName);
        setField(form, ['startDate', 'start_date', 'start'], start);
        setField(form, ['endDate', 'end_date', 'end'], end);

        // s'assurer que l'action est 'update'
        const actionInput = form.querySelector('input[name="action"]');
        if (actionInput) actionInput.value = 'update';
    }

    // ajuste titre et texte du bouton si présents
    const modalTitle = modalEl.querySelector('#modalAddHolidayPeriodLabel') || modalEl.querySelector('.modal-title');
    const smallTitle = modalEl.querySelector('#modalSmallTitle');
    const submitBtn = modalEl.querySelector('#submitHolidayBtn') || modalEl.querySelector('button[type="submit"]');

    if (modalTitle) modalTitle.textContent = 'Modifier une période de congé';
    if (smallTitle) smallTitle.textContent = 'Modifier une période de congé';
    if (submitBtn) submitBtn.textContent = 'Enregistrer';

    // ouvre la modal via l'API Bootstrap
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.show();

    // helpers
    function setField(formEl, names, value) {
        for (const n of names) {
            const el = formEl.querySelector(`[name="${n}"]`);
            if (el) { el.value = value ?? ''; return true; }
        }
        return false;
    }

    function getCellText(rowEl, index) {
        if (!rowEl) return '';
        return (typeof index === 'number' && rowEl.children[index]) ? rowEl.children[index].textContent.trim() : '';
    }

    function normalizeToIsoDate(v) {
        if (!v) return '';
        // si déjà ISO ou YYYY-MM-DD, retourne directement
        if (/^\d{4}-\d{2}-\d{2}$/.test(v)) return v;
        // accepte dd/mm/YYYY ou d/m/YYYY
        const m = v.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
        if (m) {
            const d = m[1].padStart(2, '0');
            const mo = m[2].padStart(2, '0');
            const y = m[3];
            return `${y}-${mo}-${d}`;
        }
        // dernier recours : tente Date parsing et format YYYY-MM-DD
        const date = new Date(v);
        if (!isNaN(date.getTime())) {
            const y = date.getFullYear();
            const mo = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${mo}-${d}`;
        }
        return '';
    }
});
