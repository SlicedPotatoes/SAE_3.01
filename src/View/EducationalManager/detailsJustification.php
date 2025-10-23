<?php
// detailsJustification

require_once __DIR__ . "/../../Model/Justification/Justification.php";
require_once __DIR__ . "/../../Presentation/JustificationPresentation.php";
require_once __DIR__ . "/../../Model/Justification/File.php";


$idJustification = $_GET['id'] ?? null;

if (!$idJustification) {
    echo "<div class='alert alert-danger'>Erreur : ID de justification manquant</div>";
    exit;
}

$justification = Justification::getJustificationById($idJustification);
$absences = $justification->getAbsences();


$files = $justification->getFiles();

?>
<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-4 p-3" id="tab-dashboard-stuContent">

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
                    <?php include "View/Dashboard/lineAbs.php"; ?>
                    </div>

                    <?php if ($justification->getCurrentState() === StateJustif::NotProcessed) : ?>
                        <input type="checkbox" class="form-check-input ms-2"
                               name="refuse[]"
                               value="<?= $absence->getIdAccount() ?>|<?= $absence->getTime()->format('Y-m-d H:i:s') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>



<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-4 mt-4 p-3" id="tab-dashboard-stuContent">
<h4 class="mb-3">Motif de l'absence :</h4> <?= $justification->getCause() ?>
<!-- Justificatif files-->

<h4 class="mb-3">Justificatifs</h4>
<ul class="list-group mb-3">
    <?php foreach ($files as $file): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= ($file->getFileName()) ?>
            <a href="uploads/<?= $file->getFileName() ?>" target="_blank" class="btn btn-sm btn-primary">
                Télécharger
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Justificatif non traité par le RP -->
<?php if ($justification->getCurrentState() === StateJustif::NotProcessed
    /* && $_SESSION['user']->getAccountType() === AccountType::EducationalManager */) : ?>

    <!-- Justificatif traité par le RP-->
    <!-- TODO: A changer selon la connection étu / RP -->
<?php elseif ($justification->getCurrentState() === StateJustif::Processed) : ?>
    <h4 class="mb-3">Motif du refus :</h4>

<?php endif; ?>

<a href="index.php?currPage=dashboard" class="btn btn-secondary mt-3">
    ← Retour au tableau de bord
</a>
</div>