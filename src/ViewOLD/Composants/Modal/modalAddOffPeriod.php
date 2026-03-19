<?php
?>
<div class="modal fade" id="modalAddOffPeriod" tabindex="-1" aria-labelledby="modalAddOffPeriodLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddOffPeriodLabel">Ajouter une période de congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div>
                    <form id="formAddOffPeriod" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="startDate" class="form-label">Date de début</label>
                                <input type="date" class="form-control" id="startDate" name="startDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="endDate" class="form-label">Date de fin</label>
                                <input type="date" class="form-control" id="endDate" name="endDate" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="periodName" class="form-label">Nom de la période</label>
                            <input type="text" class="form-control" id="periodName" name="periodName" rows="3" required></input>
                        </div>

                        <input type="hidden" name="action" value="insert">
                        <input type="hidden" name="id" value="">

                        <button type="submit" class="btn btn-uphf" id="submitOffBtn">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    /*
      Utilise l'événement Bootstrap `show.bs.modal` pour récupérer le bouton déclencheur
      (event.relatedTarget) et remplir / adapter la modal en mode "modifier" ou "ajouter".
    */
    (function () {
        const modalEl = document.getElementById('modalAddOffPeriod');
        if (!modalEl) return;

        modalEl.addEventListener('show.bs.modal', function (ev) {
            const trigger = ev.relatedTarget; // le bouton ayant déclenché l'ouverture
            const form = modalEl.querySelector('#formAddOffPeriod');
            const startEl = form.querySelector('#startDate');
            const endEl = form.querySelector('#endDate');
            const nameEl = form.querySelector('#periodName');
            const idInput = form.querySelector('input[name="id"]');
            const actionInput = form.querySelector('input[name="action"]');
            const submitBtn = form.querySelector('#submitOffBtn');
            const titleEl = modalEl.querySelector('#modalAddOffPeriodLabel');

            // Helper: convertit dd/mm/YYYY ou YYYY-MM-DD en YYYY-MM-DD
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

            if (trigger && trigger.dataset && trigger.dataset.id) {
                // Mode "modifier"
                const id = trigger.dataset.id || '';
                const rawStart = trigger.dataset.start || '';
                const rawEnd = trigger.dataset.end || '';
                const name = trigger.dataset.name || '';

                if (idInput) idInput.value = id;
                if (actionInput) actionInput.value = 'update';
                if (startEl) startEl.value = toIsoDate(rawStart);
                if (endEl) endEl.value = toIsoDate(rawEnd);
                if (nameEl) nameEl.value = name;

                if (titleEl) titleEl.textContent = 'Modifier une période de congé';
                if (submitBtn) submitBtn.textContent = 'Enregistrer';
            } else {
                // Mode "ajouter" (réinitialise le formulaire)
                if (idInput) idInput.value = '';
                if (actionInput) actionInput.value = 'insert';
                if (startEl) startEl.value = '';
                if (endEl) endEl.value = '';
                if (nameEl) nameEl.value = '';

                if (titleEl) titleEl.textContent = 'Ajouter une période de congé';
                if (submitBtn) submitBtn.textContent = 'Ajouter';
            }
        });
    })();
</script>
