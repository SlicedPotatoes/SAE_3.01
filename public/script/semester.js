// Script pour la gestion de la modal d'édition des semestres
(function () {
    const modalEl = document.getElementById('modalEditSemester');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (ev) {
        const trigger = ev.relatedTarget;
        const form = modalEl.querySelector('#formEditSemester');
        const startEl = form.querySelector('#semesterStartDate');
        const endEl = form.querySelector('#semesterEndDate');
        const idInput = form.querySelector('input[name="id"]');
        const titleEl = modalEl.querySelector('#modalEditSemesterLabel');

        /**
         * Normalise différentes représentations de date au format ISO YYYY-MM-DD.
         * @param {string} v - Valeur de date en entrée
         * @returns {string}
         */
        function toIsoDate(v) {
            if (!v) return '';
            v = v.trim();
            if (/^\d{4}-\d{2}-\d{2}$/.test(v)) return v;
            const m = v.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
            if (m) {
                const d = m[1].padStart(2, '0');
                const mo = m[2].padStart(2, '0');
                return `${m[3]}-${mo}-${d}`;
            }
            const date = new Date(v);
            if (!isNaN(date.getTime())) {
                const y = date.getFullYear();
                const mo = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${mo}-${d}`;
            }
            return '';
        }

        if (trigger && trigger.dataset) {
            const id = trigger.dataset.id || '';
            const label = trigger.dataset.label || '';
            const rawStart = trigger.dataset.start || '';
            const rawEnd = trigger.dataset.end || '';

            if (idInput) idInput.value = id;
            if (startEl) startEl.value = toIsoDate(rawStart);
            if (endEl) endEl.value = toIsoDate(rawEnd);
            if (titleEl) titleEl.textContent = 'Modifier ' + label;
        }
    });
})();
