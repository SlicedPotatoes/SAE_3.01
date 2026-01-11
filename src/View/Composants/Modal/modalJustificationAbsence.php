<?php

/**
 * Contient le code html et javascript de la modale permettant à un étudiant de justifier une absence.
 *
 * Il inclut un formulaire pour saisir les dates, le motif, et ajouter des fichiers justificatifs
 *
 * Le script gère l'affichage, la sélection et la suppression des fichiers avant l'envoi du formulaire.
 */

use Uphf\GestionAbsence\Model\GlobalVariable;
?>
<div class="modal fade" id="justifyModal" tabindex="-1" aria-labelledby="justifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <!-- Button Close -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Fermer"></button>

            <form id="addJustificationForm" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <!-- Date de début et de fin -->
                    <div>
                        <h4 class="modal-title h4 mb-1">Justification d’absence</h4>
                        <div class="d-flex flex-column flex-md-row gap-2 small">
                            <div>
                                <label class="me-2 fw-semibold" for="justificationStartDate">Début :</label>
                                <input name="startDate" id="justificationStartDate" type="date" class="form-control form-control-sm d-inline-block" form="addJustificationForm" required>
                            </div>
                            <div>
                                <label class="me-2 fw-semibold" for="justificationEndDate">Fin :</label>
                                <input name="endDate" id="justificationEndDate" type="date" class="form-control form-control-sm d-inline-block" form="addJustificationForm" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Motif de l'absence -->
                        <div class="col-12 col-md-7">
                            <label for="justificationAbsenceReason" class="form-label h5 mb-2">Motif de l'absence</label>
                            <textarea name="absenceReason" id="justificationAbsenceReason" class="form-control" rows="8" placeholder="Expliquez pourquoi vous avez été absent." form="addJustificationForm" required></textarea>
                        </div>

                        <div class="col-12 col-md-5">
                            <!-- Upload un fichier -->
                            <div class="justification-files-choice mb-2 pe-3">
                                <label class="form-label h5 mb-2">Justificatif <span class="text-muted fs-6">(pdf, png, jpg)</span></label>
                                <input type="file" class="form-control" multiple id="justificationFileInput" accept="<?= '.'.implode(', .', GlobalVariable::ALLOWED_EXTENSIONS_FILE()) ?>">
                            </div>
                            <!-- Liste des fichiers upload -->
                            <ul id="justificationFileList" class="list-group mb-2 overflow-auto" style="max-height: 200px"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex">
                    <span class="text-muted me-auto">Vos déclarations doivent être exactes : toute fausse information peut entraîner des conséquences. </span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <input type="hidden" form="addJustificationForm" name="action" value="createJustification">
                    <button type="submit" id="justificationSubmitForm" form="addJustificationForm" class="btn btn-uphf me-2">Envoyer</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Étudiant : Button ouvrir modal "justifier absence" -->
<button type="button" class="btn btn-primary btn-uphf nav-link rounded-bottom-0" data-bs-toggle="modal" data-bs-target="#justifyModal">
    Justifier une absence
</button>

<script src="/script/modalJustificationAbsence.js"></script>