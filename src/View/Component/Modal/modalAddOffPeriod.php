<?php
/**
 * Modal d'ajout / modification d'une période de vacances
 */
?>

<div class="modal fade" id="modalAddOffPeriod" tabindex="-1" aria-labelledby="modalAddOffPeriodLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-md-down">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalAddOffPeriodLabel">Ajouter une période</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="offPeriodName">Libellé</label>
                    <input type="text"
                           class="form-control"
                           id="offPeriodName"
                           placeholder="Ex : Vacances de Noël"
                           required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col">
                        <label class="form-label" for="offPeriodStart">Date de début</label>
                        <input type="date"
                               class="form-control"
                               id="offPeriodStart"
                               required>
                    </div>
                    <div class="col">
                        <label class="form-label" for="offPeriodEnd">Date de fin</label>
                        <input type="date"
                               class="form-control"
                               id="offPeriodEnd"
                               required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-uphf" id="offPeriodSubmitBtn">Ajouter</button>
            </div>

        </div>
    </div>
</div>
