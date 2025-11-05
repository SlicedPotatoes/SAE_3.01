<?php
use Uphf\GestionAbsence\Model\Justification\Justification;
use Uphf\GestionAbsence\Model\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Account\AccountType;

$idJustification = $_GET['id'] ?? null;
$justification = Justification::getJustificationById($idJustification);
$absences = $justification->getAbsences();
$files = $justification->getFiles();
$currentState = $justification->getCurrentState();
$accountType = $_SESSION['account']->getAccountType();
?>

<div class="card d-flex flex-column gap-3 mt-4 p-3">
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-1">Justificatif du : <?= $justification->getSendDate()->format('d/m/Y') ?></h2>
            <span class="badge rounded-pill text-bg-<?= $currentState->colorBadge() ?> fs-6 px-3 py-2">
                <?= $currentState->label() ?>
            </span>
        </div>

        <div class="col-md-6"><strong>Date début :</strong> <?= $justification->getStartDate()->format('d/m/Y') ?></div>
        <div class="col-md-6"><strong>Date fin :</strong> <?= $justification->getEndDate()->format('d/m/Y') ?></div>
    </div>

    <!-- Bloc Absences (une seule fois, conditions internes selon le profil) -->
    <div>
        <h4>Absences</h4>
        <div class="border-top flex-fill overflow-y-auto" style="max-height: 200px;">
            <?php foreach ($absences as $absence): ?>
                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                    <div class="flex-grow-1 me-3">
                        <div>Date: <?= $absence->getTime()->format('d/m/Y H:i') ?></div>
                        <div>Durée: <?= $absence->getDuration() ?></div>
                    </div>

                    <?php if ($currentState === StateJustif::NotProcessed): ?>
                        <?php if ($accountType === AccountType::EducationalManager): ?>
                            <div class="form-check form-switch">
                                <input type="hidden"
                                       form="validateJustificationForm"
                                       name="absences[<?= $absence->getIdAccount() ?>_<?= $absence->getTime()->format('Y-m-d H:i:s') ?>]"
                                       value="validated"
                                       class="absence-state">
                                <button type="button" class="btn btn-success absence-btn">Validé</button>
                            </div>
                        <?php else: ?>
                            <div class="form-check form-switch">
                                <input type="hidden"
                                       form="validateJustificationForm"
                                       name="absences[<?= $absence->getIdAccount() ?>_<?= $absence->getTime()->format('Y-m-d H:i:s') ?>]"
                                       value="validated"
                                       class="absence-state">
                                <span class="badge rounded-pill text-bg-<?= $absence->getCurrentState()->colorBadge() ?>">
                                    <?= $absence->getCurrentState()->label() ?>
                                </span>
                            </div>

                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Motif de l'absence & Justificatifs côte à côte (toujours affiché) -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h4>Motif de l'absence :</h4>
            <p><?= htmlspecialchars($justification->getCause()) ?></p>
        </div>
        <div class="col-md-6">
            <h4>Justificatifs :</h4>
            <ul class="list-group">
                <?php foreach ($files as $file): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center pe-2">
                        <span title="<?= htmlspecialchars($file->getFileName()) ?>" class="text-truncate"><?= htmlspecialchars($file->getFileName()) ?></span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-danger bi bi-eye me-1" title="Voir" type="button"></button>
                            <a class="btn btn-outline-primary bi bi-download" title="Télécharger" href="" download></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Bloc Motif du refus & Actions : toujours en dessous -->
    <div class="mt-4">
        <h4 class="mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Motif du refus :</h4>
        <?php if ($justification->getRefusalReason() === null): ?>
            <p class="mb-0">Aucun motif de refus n'a été communiqué pour cette justification.</p>
        <?php else: ?>
            <p class="mb-0"><?= htmlspecialchars($justification->getRefusalReason()) ?></p>
        <?php endif; ?>

        <?php if ($accountType === AccountType::EducationalManager && $currentState === StateJustif::NotProcessed): ?>
            <form id="validateJustificationForm" action="./Presentation/validateJustification.php" method="post" class="mt-4">
                <input type="hidden" name="idJustification" value="<?= $justification->getIdJustification() ?>">
                <label for="JustificationRejectionReason" class="form-label h4 mb-2">Raison du Refus</label>
                <textarea name="rejectionReason" id="JustificationRejectionReason" class="form-control" rows="5"></textarea>
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?currPage=justificationList" class="btn btn-secondary">Retour</a>
                    <button type="submit" id="JustificationRPValidation" class="btn btn-uphf">Envoyer</button>
                </div>
            </form>
        <?php elseif ($currentState === StateJustif::Processed): ?>
            <div class="mt-4">
                <a href="index.php?currPage=dashboard" class="btn btn-secondary">Retour</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.absence-btn').forEach(button => {
        button.addEventListener('click', () => {
            const hiddenInput = button.previousElementSibling;
            if(button.classList.contains('btn-success')) {
                button.classList.replace('btn-success', 'btn-danger');
                button.textContent = 'Refusé';
                hiddenInput.value = 'refused';
            } else {
                button.classList.replace('btn-danger', 'btn-success');
                button.textContent = 'Validé';
                hiddenInput.value = 'validated';
            }
        });
    });
</script>
