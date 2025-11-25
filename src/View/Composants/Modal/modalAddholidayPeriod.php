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
                    <h5>Ajouter une période de congé</h5>
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
                            <textarea class="form-control" id="periodName" name="periodName" rows="3" required></textarea>
                        </div>

                        <input type="hidden" name="action" value="insert">

                        <button type="submit" class="btn btn-uphf">Ajouter</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

