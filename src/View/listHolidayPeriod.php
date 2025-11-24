<?php
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Bonjour</h1>
    <button type="button" class="btn btn-uphf" data-bs-toggle="modal" data-bs-target="#modalAddHolidayPeriod">
        Ajouter une période
    </button>
</div>

<div class="modal fade" id="modalAddHolidayPeriod" tabindex="-1" aria-labelledby="modalAddHolidayPeriodLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddHolidayPeriodLabel">Ajouter une période de congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <?php require_once __DIR__ . '/Composants/Modal/modalAddholidayPeriod.php'; ?>
            </div>
        </div>
    </div>
</div>
