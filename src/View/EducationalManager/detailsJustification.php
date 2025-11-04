<?php
// detailsJustification

use Uphf\GestionAbsence\Model\Justification\Justification;
use Uphf\GestionAbsence\Model\Justification\StateJustif;


$idJustification = $_GET['id'] ?? null;
$justification = Justification::getJustificationById($idJustification);
$absences = $justification->getAbsences();
$files = $justification->getFiles();

?>
<div class="card d-flex flex-column gap-3 mt-4 p-3">
    <div>
        <!-- Justificatif + Etat -->
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-1">Justificatif du : <?= $justification->getSendDate()->format('d/m/Y') ?></h2>
            <span class="badge rounded-pill text-bg-<?= $justification->getCurrentState()->colorBadge() ?> fs-6 px-3 py-2">
                <?= $justification->getCurrentState()->label() ?>
            </span>
        </div>

        <!-- Dates de début et fin -->
        <div class="col-md-6">
            <strong>Date début :</strong> <?= $justification->getStartDate()->format('d/m/Y') ?>
        </div>
        <div class="col-md-6">
            <strong>Date fin :</strong> <?= $justification->getEndDate()->format('d/m/Y') ?>
        </div>
    </div>

    <!-- Affichage des absences -->
    <div>
        <h4>Absences</h4> <!-- TODO: Peut être ajouter un bouton "Tout Validé" et "Tout Refusé" -->
        <div class="border-top">
            <?php //for($i = 0; $i < 10; $i++): ?>
            <?php foreach ($absences as $index => $absence) : ?>
                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
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
            <?php //endfor ?>
        </div>
    </div>

    <!-- Information remplie par l'étudiant pour le justificatif -->
    <div class="row">
        <div class="col-md-6">
            <h4>Motif de l'absence:</h4>
            <p><?= htmlspecialchars($justification->getCause()) ?></p>
        </div>
        <!-- Liste des fichiers -->
        <div class="col-md-6">
            <h4>Justificatifs:</h4>
            <ul class="list-group">
                <?php foreach ($files as $file): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center pe-2">
                        <span title="<?= htmlspecialchars($file->getFileName()) ?>" class="text-truncate"><?= htmlspecialchars($file->getFileName()) ?></span>

                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-danger bi bi-eye me-1" title="Voir" type="button"></button>
                            <a class="btn btn-outline-primary bi bi-download" title="Télécharger" href="" download="raw (1).png"></a>
                        </div>
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

            <label for="JustificationRejectionReason" class="form-label h4 mb-2">Raison du Refus</label>
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
