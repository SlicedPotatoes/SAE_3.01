<?php
/*
 * Page de détail d'une justification
 */
// Récupérer la justification depuis la base de données

require_once __DIR__ . '/../Model/connection.php';
require_once __DIR__ . '/../Model/Justification.php';
require_once __DIR__ . '/../Model/StateJustif.php';
require_once __DIR__ . '/../Model/Absence.php';
require_once __DIR__ . '/../Model/File.php';

// Vérifier que l'ID de justification est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?currPage=dashboard&errorMessage[]=ID de justification invalide");
    exit;
}

$justificationId = (int)$_GET['id'];
$justification = Justification::getById($justificationId);

// Vérifier que la justification existe
if (!$justification) {
    header("Location: index.php?currPage=dashboard&errorMessage[]=Justification introuvable");
    exit;
}

// Vérifier que l'utilisateur connecté possède cette justification
$userOwnsJustification = false;
$absences = $justification->getAbsences();
foreach ($absences as $absence) {
    if ($absence->getIdStudent() == $_SESSION['account']->getIdAccount()) {
        $userOwnsJustification = true;
        break;
    }
}

if (!$userOwnsJustification) {
    header("Location: index.php?currPage=dashboard&errorMessage[]=Vous n'avez pas accès à cette justification");
    exit;
}

$absences = $justification->getAbsences();



// Formater la date d'envoi pour le titre
$sendDateFormatted = $justification->getSendDate() instanceof DateTime ?
    $justification->getSendDate()->format('d/m/Y') :
    'Date inconnue';

// Formater les dates de début et fin
$startDateFormatted = $justification->getStartDate() instanceof DateTime ?
    $justification->getStartDate()->format('d/m/Y') :
    'Date inconnue';

$endDateFormatted = $justification->getEndDate() instanceof DateTime ?
    $justification->getEndDate()->format('d/m/Y') :
    'Date inconnue';
?>


<!-- Titre avec date d'envoi et état -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Justificatif du <?= htmlspecialchars($sendDateFormatted) ?></h2>
    <span class="badge rounded-pill text-bg-<?= htmlspecialchars($justification->getCurrentState()->colorBadge()) ?> fs-6 px-3 py-2">
        <?= htmlspecialchars($justification->getCurrentState()->label()) ?>
    </span>
</div>

<!-- Cause de la justification -->
<div class="mb-4">
    <h5>Motif :</h5>
    <p class="text-muted"><?= htmlspecialchars($justification->getCause()) ?></p>
</div>

<!-- Dates de début et fin -->
<div class="mb-4">
    <div class="row">
        <div class="col-md-6">
            <strong>Date début :</strong> <?= htmlspecialchars($startDateFormatted) ?>
        </div>
        <div class="col-md-6">
            <strong>Date fin :</strong> <?= htmlspecialchars($endDateFormatted) ?>
        </div>
    </div>
</div>

<!-- Section Absences -->
<h4 class="mb-3">Absences</h4>
<div class="accordion accordion-flush" id="absencesAccordion">
    <?php if (!empty($absences)): ?>
        <?php foreach ($absences as $index => $absence): ?>
            <?php
            $abs = $absence;
            ?>
            <?php require "Dashboard/lineAbs.php"; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="p-3 text-center text-muted">
            Aucune absence associée à cette justification
        </div>
    <?php endif; ?>

    <!-- Bouton de retour -->
    <div class="d-flex justify-content-start mb-4">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php?currPage=dashboard'">
            Fermer
        </button>
    </div>

</div>
