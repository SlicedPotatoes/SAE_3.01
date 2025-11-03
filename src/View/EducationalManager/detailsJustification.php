<?php
// detailsJustification

use Uphf\GestionAbsence\Model\Justification\Justification;
use Uphf\GestionAbsence\Presentation\JustificationPresentation;
use Uphf\GestionAbsence\Model\Justification\File;
use Uphf\GestionAbsence\Model\Justification\StateJustif;


$idJustification = $_GET['id'] ?? null;
$justification = Justification::getJustificationById($idJustification);
$absences = $justification->getAbsences();
$files = $justification->getFiles();


?>
<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-4 p-3" id="Liste-Abs-Justification-PR">


    <!-- Justificatif + Etat -->
    <div class="d-flex justify-content-between align-items-center mb-4 ">
        <h2 class="mb-0">Justificatif du : <?= $justification->getSendDate()->format('d/m/Y') ?></h2>
        <span class="badge rounded-pill text-bg-<?= $justification->getCurrentState()->colorBadge() ?> fs-6 px-3 py-2">
        <?= $justification->getCurrentState()->label() ?>
    </span>
    </div>

    <!-- Dates de début et fin -->

    <div class="mb-4">
        <div class="col-md-6">
            <strong>Date début :</strong> <?= $justification->getStartDate()->format('d/m/Y') ?>
        </div>
        <div class="col-md-6">
            <strong>Date fin :</strong> <?= $justification->getEndDate()->format('d/m/Y') ?>
        </div>
    </div>

    <!-- Affichage des absences -->

    <h4 class="mb-3">Absences</h4>
    <div class="accordion accordion-flush" id="absFlush">
        <?php if (!empty($absences)) : ?>
            <?php foreach ($absences as $index => $absence) : ?>
                <?php $abs = $absence; ?>
                <div class="accordion-item d-flex align-items-center justify-content-between p-2">
                    <div class="flex-grow-1 me-3">
                        <div class="d-flex flex-column">
                            <div>Date: <?= $absence->getTime()->format('d/m/Y H:i') ?></div>
                            <div>Durée: <?= $absence->getDuration() ?></div>
                        </div>
                    </div>
                    <?php if ($justification->getCurrentState() === StateJustif::NotProcessed) : ?>
                        <div class="form-check form-switch">
                            <input type="hidden" 
                                   form="validateJustificationForm"
                                   name="absences[<?= $absence->getIdAccount() ?>_<?= $absence->getTime()->format('Y-m-d H:i:s') ?>]" 
                                   value="validated" 
                                   class="absence-state">
                            <button type="button" class="btn btn-success absence-btn">Validé</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-4 mt-4 p-3" id="Details-Justification-Abs-PR">
    <div class="row">
        <div class="col-md-6">
            <h4 class="mb-3">Motif de l'absence:</h4>
            <h6><?= $justification->getCause() ?></h6>
        </div>
        <div class="col-md-6">
            <h4 class="mb-3">Justificatifs:</h4>
            <ul class="list-group mb-3">
                <?php foreach ($files as $file): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($file->getFileName()) ?>
                        <a href="uploads/<?= htmlspecialchars($file->getFileName()) ?>" target="_blank" class="btn btn-sm btn-primary">
                            Télécharger
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>





    <!-- Justificatif état non traité côté RP -->
    <?php if ($justification->getCurrentState() === StateJustif::NotProcessed
        /* && $_SESSION['user']->getAccountType() === AccountType::EducationalManager */) : ?>
        <form id="validateJustificationForm" action="./Presentation/validateJustification.php" method="post">
            <input type="hidden" name="idJustification" value="<?= $justification->getIdJustification() ?>">

            <label for="JustificationRejectionReason" class="form-label h5 mb-2">Raison du Refus</label>
            <!-- TODO: Si absence coché donc refuser => apparation textArea sinon hidden ( JS )  -->
            <textarea name="rejectionReason" id="JustificationRejectionReason" class="form-control" rows="5"></textarea>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php?currPage=dashboard" class="btn btn-secondary"> Retour</a>
                <button type="submit" id="JustificationRPValidation" class="btn btn-uphf">Envoyer</button>
            </div>
        </form>

        <!-- Justificatif état traité côté RP-->
    <?php elseif ($justification->getCurrentState() === StateJustif::Processed) : ?>
        <?php if (!empty($justification->getRefusalReason())) : ?>
            <div class="alert alert-warning mt-4">
                <h4 class="mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Motif du refus :</h4>
                <p class="mb-0"><?= htmlspecialchars($justification->getRefusalReason()) ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($justification->getCurrentState() === StateJustif::Processed) : ?>
        <div class="mt-4">
            <a href="index.php?currPage=dashboard" class="btn btn-secondary">Retour</a>
        </div>
    <?php endif; ?>
</div>



<script>
    document.querySelectorAll('.absence-btn').forEach(button => {
        button.addEventListener('click', () => {
            const hiddenInput = button.previousElementSibling;
            
            if(button.classList.contains('btn-success')) {
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
                button.textContent = 'Refusé';
                hiddenInput.value = 'refused';
            } else {
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');
                button.textContent = 'Validé';
                hiddenInput.value = 'validated';
            }
        });
    });
</script>
