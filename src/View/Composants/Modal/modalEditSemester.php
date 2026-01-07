<?php
?>
<div class="modal fade" id="modalEditSemester" tabindex="-1" aria-labelledby="modalEditSemesterLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditSemesterLabel">Modifier le semestre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form id="formEditSemester" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="semesterStartDate" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="semesterStartDate" name="startDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semesterEndDate" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="semesterEndDate" name="endDate" required>
                        </div>
                    </div>

                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="">

                    <button type="submit" class="btn btn-uphf">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>
