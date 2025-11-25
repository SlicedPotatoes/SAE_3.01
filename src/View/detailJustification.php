<?php

global $dataView;

use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;


$justification = $dataView->justification;
$currentState = $justification->state;
$isEducationalManager = $dataView->roleUser === AccountType::EducationalManager;

$h2 = "Jusificatif";
if ($isEducationalManager)
{
    $h2 .= " de " . $justification->studentFullName . ", ";
}
$h2 .= " du " . $justification->sendDate;

require_once __DIR__ . "/../View/Composants/Modal/filePreviewModal.php";

?>

<div class="card flex-fill d-flex flex-column gap-3 mt-4 p-3" style="min-height: 0">
    <!-- Information principale d'un justificatifs -->
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-1"><?=$h2?></h2>
            <div class="d-flex gap-3 align-items-center">
                <?php if($isEducationalManager): ?>
                    <a href="/StudentProfile/<?= $dataView->student->idAccount ?>" class="btn btn-uphf">Profil Étudiant</a>
                <?php endif; ?>
                <span class="badge rounded-pill text-bg-<?= $currentState->colorBadge() ?> fs-6 px-3 py-2">
                    <?= $currentState->label() ?>
                </span>
            </div>
        </div>

        <div class="col-md-6"><strong>Date début :</strong> <?= $justification->startDate ?></div>
        <div class="col-md-6"><strong>Date fin :</strong> <?= $justification->endDate ?></div>
        <?php if($justification->processedDate !== null) : ?>
            <div class="col-md-6"><strong>Date de traitement :</strong> <?= $justification->processedDate ?> </div>
        <?php endif; ?>
    </div>

    <!-- Liste des absences -->
    <div class="d-flex flex-column" style="flex: 1 1 30%; min-height: 0">
        <div class="d-flex align-items-center mb-2">
            <h4>Absences</h4>

            <?php if ($currentState === StateJustif::NotProcessed && $isEducationalManager): ?>
                <div class="btn-group gap-1 ms-auto" id="absence-cta-rp-all">
                    <button type="button" class="btn btn-outline-success rounded">Tout valider</button>
                    <button type="button" class="btn btn-danger d-none"><i class="bi bi-lock"></i></button>
                </div>
            <?php endif; ?>
        </div>

        <div class="border-top flex-fill overflow-y-auto" style="min-height: 0">
            <?php foreach ($dataView->absences as $absence): ?>
                <div class="d-flex align-items-center border-bottom py-2">
                    <!-- Date de l'absence -->
                    <div class="me-3">
                        <div>Date: <?= $absence->date ?></div>
                        <div>Durée: <?= $absence->duration ?></div>
                    </div>

                    <div class="me-3 h-100">
                        <div>Cour: <?= $absence->resource ?></div>
                        <?php if($absence->haveTeacher): ?>
                            <div>Enseignant : <?= $absence->fullnameTeacher ?><</div>
                        <?php else: ?>
                            <div>&nbsp;</div>
                        <?php endif; ?>
                    </div>

                    <!-- Tag examen -->
                    <?php if($absence->examen): ?>
                        <span class='badge rounded-pill text-bg-warning ms-3 me-2'>Examen</span>
                    <?php endif; ?>

                    <!-- Etat de l'absence -->
                    <!-- Si le justificatif n'est pas traité, et qu'on est le RP, on peut choisir de validé ou non l'abs -->
                    <?php if ($currentState === StateJustif::NotProcessed && $isEducationalManager): ?>
                        <div class="ms-auto me-3">
                            <input type="hidden"
                                   form="validateJustificationForm"
                                   name="absences[<?= $absence->idAccount ?>_<?= $absence->time ?>][state]"
                                   value="Validated"
                                   class="absence-input-state">
                            <input type="hidden"
                                   form="validateJustificationForm"
                                   name="absences[<?= $absence->idAccount ?>_<?= $absence->time ?>][lock]"
                                   value="true"
                                   class="absence-input-lock">
                            <div class="btn-group gap-1 absence-cta-rp">
                                <button type="button" class="btn btn-outline-success absence-btn-state rounded">Validé</button>
                                <button type="button" class="btn btn-danger absence-btn-lock d-none"><i class="bi bi-lock"></i></button>
                            </div>
                        </div>
                        <!-- Dans les autres cas, on affiche seulement l'état de l'absence -->
                    <?php else: ?>
                        <span class="badge rounded-pill text-bg-<?= $absence->state->colorBadge() ?>">
                            <?= $absence->state->label() ?>
                        </span>
                    <?php endif; ?>

                    <!-- Locked -->
                    <?php if($absence->lock && ($absence->state == StateAbs::Refused || $absence->state == StateAbs::NotJustified)): ?>
                        <i style="font-size: 30px" class="bi bi-file-lock2" data-bs-toggle="tooltip" data-bs-title="Le responsable pédagogique n'autorise pas la justification de cette absence"></i>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Motif de l'absence & Justificatifs côte à côte (toujours affiché) -->
    <div class="row" style="flex: 1 1 20%; min-height: 0">
        <div class="col-md-6 d-flex flex-column h-100" style="min-height: 0">
            <h4>Motif de l'absence :</h4>
            <div class="border rounded p-2 overflow-y-auto flex-fill" style="min-height:0">
                <?= htmlspecialchars($justification->cause) ?>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column h-100" style="min-height: 0">
            <h4>Justificatifs :</h4>
            <div class="border rounded overflow-y-auto flex-fill" style="min-height:0">
                <?php if (empty($dataView->files)): ?>
                    <div class="p-2">Aucun fichier justificatif.</div>
                <?php endif; ?>
                <?php foreach ($dataView->files as $file): ?>

                    <?php
                    $fname   = $file['fileName'];
                    $originalName = $file['originalName'];
                    $viewUrl = "/file.php?idFile=" . $file['idFile'];
                    ?>

                    <div class="border-bottom p-2 border-0 border-bottom d-flex justify-content-between align-items-center pe-2">
                        <span title="<?= htmlspecialchars($fname) ?>" class="text-truncate"><?= htmlspecialchars($fname) ?></span>

                        <div class="btn-group btn-group-sm">
                            <!-- Bouton pour ouvrir la modale -->
                            <button
                                    class="btn btn-outline-danger bi bi-eye me-1"
                                    title="Voir"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#fileModal"
                                    data-bs-url="<?= $viewUrl ?>"
                                    data-bs-file="<?= $fname ?>"
                            ></button>
                            <!-- Télécharger -->
                            <a class="btn btn-outline-primary bi bi-download" title="Télécharger" href="<?= $viewUrl ?>" download></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Commentaire & Commentaires prédéfinis côte à côte -->
    <div class="row" style="flex: 1 1 20%; min-height: 0">

        <!-- Colonne Commentaire -->
        <div class="col-md-6 d-flex flex-column h-100" style="min-height: 0">
            <?php if (!($currentState === StateJustif::NotProcessed && !$isEducationalManager)): ?>
                <label for="JustificationRejectionReason" class="h4 mb-2">Commentaire :</label>
            <?php endif; ?>

            <?php if ($currentState === StateJustif::NotProcessed && $isEducationalManager): ?>
                <form class="flex-fill d-flex flex-column" style="min-height: 0" id="validateJustificationForm" method="post">
                <textarea name="rejectionReason" id="JustificationRejectionReason"
                          class="form-control flex-fill"
                          style="max-height: 100%; min-height: 0;"></textarea>
                </form>
            <?php else: ?>
                <?php if (empty($justification->commentEM)): ?>
                    <p>Aucun commentaire n'a été communiqué pour cette justification.</p>
                <?php else: ?>
                    <div class="border rounded p-2 overflow-y-auto flex-fill">
                        <?= htmlspecialchars($justification->commentEM) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Colonne Commentaires prédéfinis -->
        <?php if ($currentState === StateJustif::NotProcessed && $isEducationalManager): ?>
            <div class="col-md-6 d-flex flex-column h-100" style="min-height: 0">
                <label class="h4 mb-2">Commentaires prédéfinis :</label>

                <div class="dropdown flex-fill">
                    <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="dropdownPredefinedComments" data-bs-toggle="dropdown" aria-expanded="false">
                        Sélectionner un commentaire
                    </button>

                    <ul class="dropdown-menu w-100" aria-labelledby="dropdownPredefinedComments">
                        <?php foreach ($dataView->comments as $comment): ?>
                            <li>
                                <a class="dropdown-item predefined-comment" href="#"
                                   data-value="<?= htmlspecialchars($comment['label']) ?>">
                                    <?= htmlspecialchars($comment['label']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <?php if (empty($dataView->comments)): ?>
                            <li><span class="dropdown-item disabled">Aucun commentaire disponible</span></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>



    <!-- Button d'action -->
    <div class="d-flex justify-content-between">
        <a href="/" class="btn btn-secondary">Retour</a>
        <?php if ($isEducationalManager && $currentState === StateJustif::NotProcessed ): ?>
            <button type="submit" form="validateJustificationForm" id="JustificationRPValidation" class="btn btn-uphf">Envoyer</button>
        <?php endif; ?>
    </div>
</div>

<script src="/script/detailJustification.js"></script>