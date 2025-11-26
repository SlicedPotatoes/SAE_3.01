<?php
?>
<div class="modal fade" id="modalAddHolidayPeriod" tabindex="-1" aria-labelledby="modalAddHolidayPeriodLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddHolidayPeriodLabel">Ajouter une période de congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div>
                    <form id="formAddHolidayPeriod" method="POST">
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

                        <button type="submit" class="btn btn-uphf" id="submitHolidayBtn">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Écoute globale : utilise le bouton "Modifier" dans listHolidayPeriod.php
    // Le bouton dans listHolidayPeriod.php doit ressembler à :
    // <button class="btn btn-sm btn-primary btn-edit-holiday" data-id="<?= $row['id'] ?>" data-start="<?= $row['startDate'] ?>" data-end="<?= $row['endDate'] ?>" data-name="<?= htmlspecialchars($row['periodName'], ENT_QUOTES) ?>">Modifier</button>

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-holiday');
        if (!btn) return;

        // Récupère les data-*
        const id = btn.dataset.id || '';
        const start = btn.dataset.start || '';
        const end = btn.dataset.end || '';
        const name = btn.dataset.name || '';

        // Remplit la modal
        const startEl = document.getElementById('startDate');
        const endEl = document.getElementById('endDate');
        const nameEl = document.getElementById('periodName');
        const form = document.getElementById('formAddHolidayPeriod');

        if (startEl) startEl.value = start;
        if (endEl) endEl.value = end;
        if (nameEl) nameEl.value = name;

        // Met à jour hidden inputs
        const idInput = form.querySelector('input[name="id"]');
        const actionInput = form.querySelector('input[name="action"]');
        if (idInput) idInput.value = id;
        if (actionInput) actionInput.value = 'update';

        // Change titre et texte du bouton
        const modalTitle = document.getElementById('modalAddHolidayPeriodLabel');
        const modalSmallTitle = document.getElementById('modalSmallTitle');
        const submitBtn = document.getElementById('submitHolidayBtn');
        if (modalTitle) modalTitle.textContent = 'Modifier une période de congé';
        if (modalSmallTitle) modalSmallTitle.textContent = 'Modifier une période de congé';
        if (submitBtn) submitBtn.textContent = 'Enregistrer';

        // Ouvre la modal (Bootstrap 5)
        const modalEl = document.getElementById('modalAddHolidayPeriod');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
