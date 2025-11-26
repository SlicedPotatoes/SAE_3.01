// javascript
document.addEventListener('click', function (ev) {
    // Récupère le bouton le plus proche correspondant aux sélecteurs d'édition
    const btn = ev.target.closest('.btn-edit, .btn-edit-holiday');
    if (!btn) return;

    ev.preventDefault();

    // cible du modal : supporte `data-bs-target` ou `data-bs-target` via dataset,
    // ou fallback vers l'ID par défaut '#modalAddHolidayPeriod'
    const target = btn.getAttribute('data-bs-target') || btn.dataset.bsTarget || '#modalAddHolidayPeriod';
    const modalEl = document.querySelector(target);
    if (!modalEl) {
        console.error('Modal introuvable pour le sélecteur :', target);
        return;
    }

    // Récupération des données (préférer les data-* du bouton, sinon fallback sur la ligne)
    const row = btn.closest('tr');
    const rawId = btn.dataset.id || row?.dataset.id || '';
    const rawName = btn.dataset.name || row?.dataset.name || getCellText(row, 1);
    const rawStart = btn.dataset.start || row?.dataset.start || getCellText(row, 2);
    const rawEnd = btn.dataset.end || row?.dataset.end || getCellText(row, 3);

    // Normalise les dates au format ISO attendu par les inputs[type="date"] (YYYY-MM-DD)
    const start = normalizeToIsoDate(rawStart);
    const end = normalizeToIsoDate(rawEnd);

    // Recherche du formulaire dans le modal. Supporte plusieurs IDs/noms courants.
    const form = modalEl.querySelector('form#updateHoliday') || modalEl.querySelector('form#formAddHolidayPeriod') || modalEl.querySelector('form');
    if (form) {
        // Remplit les champs du formulaire en essayant plusieurs noms possibles pour chaque champ
        setField(form, ['id', 'holidayid', 'holidaysid'], rawId);
        setField(form, ['HolidayName', 'periodName', 'name'], rawName);
        setField(form, ['startDate', 'start_date', 'start'], start);
        setField(form, ['endDate', 'end_date', 'end'], end);

        // Force l'action à 'update' si un input[name="action"] existe
        const actionInput = form.querySelector('input[name="action"]');
        if (actionInput) actionInput.value = 'update';
    }

    // Ajuste le titre du modal, le petit titre et le texte du bouton de soumission si présents
    const modalTitle = modalEl.querySelector('#modalAddHolidayPeriodLabel') || modalEl.querySelector('.modal-title');
    const smallTitle = modalEl.querySelector('#modalSmallTitle');
    const submitBtn = modalEl.querySelector('#submitHolidayBtn') || modalEl.querySelector('button[type="submit"]');

    if (modalTitle) modalTitle.textContent = 'Modifier une période de congé';
    if (smallTitle) smallTitle.textContent = 'Modifier une période de congé';
    if (submitBtn) submitBtn.textContent = 'Enregistrer';

    // Ouvre le modal via l'API Bootstrap (récupère instance existante ou en crée une nouvelle)
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.show();

    // -----------------
    // Helpers
    // -----------------

    /**
     * Remplit le premier champ existant trouvé parmi une liste de noms.
     * @param {HTMLFormElement} formEl - Le formulaire cible
     * @param {string[]} names - Liste de noms d'attribut `name` à tester (ordonnée)
     * @param {string} value - Valeur à placer dans le champ (vide si null/undefined)
     * @returns {boolean} true si un champ a été trouvé et rempli, false sinon
     */
    function setField(formEl, names, value) {
        for (const n of names) {
            const el = formEl.querySelector(`[name="${n}"]`);
            if (el) { el.value = value ?? ''; return true; }
        }
        return false;
    }

    /**
     * Récupère le texte d'une cellule de tableau (td) à l'index donné dans une ligne.
     * Retourne chaîne vide si la ligne est absente ou si l'index n'existe pas.
     * @param {HTMLElement|null} rowEl - Ligne `<tr>` cible
     * @param {number} index - Index de cellule (0-based)
     * @returns {string}
     */
    function getCellText(rowEl, index) {
        if (!rowEl) return '';
        return (typeof index === 'number' && rowEl.children[index]) ? rowEl.children[index].textContent.trim() : '';
    }

    /**
     * Normalise différentes représentations de date au format ISO `YYYY-MM-DD`.
     * - Accepte déjà le format `YYYY-MM-DD`.
     * - Accepte `dd/mm/YYYY` ou `d/m/YYYY` (ou avec `-` comme séparateur).
     * - Tente un parsing via `new Date(v)` en dernier recours.
     * Retourne une chaîne vide si la normalisation échoue.
     * @param {string} v - Valeur de date en entrée
     * @returns {string}
     */
    function normalizeToIsoDate(v) {
        if (!v) return '';
        // si déjà ISO ou YYYY-MM-DD, retourne directement
        if (/^\d{4}-\d{2}-\d{2}$/.test(v)) return v;
        // accepte dd/mm/YYYY ou d/m/YYYY (ou avec '-')
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