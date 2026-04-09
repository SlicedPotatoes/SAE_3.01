<?php
/**
 * Première slide de detail-justification
 *
 * Affiche les informations suivante:
 * - Les dates de début / fin déclaré par l'étudiant
 * - La date de traitement si le justificatif a été traité
 * - Le motif de l'absence déclaré par l'étudiant
 * - Les fichiers justificatifs fournis par l'étudiant
 * - Le commentaire du responsable pédagogique dans le cas d'un justificatif traité, et que le RP a précisé un commentaire.
 *
 */

use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;

$nbCol = $justification->getCurrentState() === StateJustif::Processed ? 3 : 2;
$sizeCol = 12 / $nbCol;

?>

<div class="scroll-parent gap-3 slide">
    <!-- Les dates -->
    <div>
        <div><strong>Date début :</strong> <?= $justification->getStartDate()->format("d/m/Y") ?></div>
        <div><strong>Date fin :</strong> <?= $justification->getEndDate()->format("d/m/Y") ?></div>
        <?php if($isProcessed) : ?>
            <div><strong>Date de traitement :</strong> <?= $justification->getProcessedDate()->format("d/m/Y") ?></div>
        <?php endif; ?>
    </div>

    <!-- Contenue -->
    <div class="row gap-3 gap-lg-0">
        <!-- Motif absence -->
        <div class="col-12 col-lg-<?= $sizeCol ?>">
            <div class="h-100 d-flex flex-column">
                <h4 class="mb-0">Motif de l'absence</h4>
                <div class="card flex-grow-1 overflow-auto p-2 detail-justification-fixed-height scroll-shadows">
                    <?= $justification->getCause() ?>
                </div>
            </div>
        </div>

        <!-- Fichiers -->
        <div class="col-12 col-lg-<?= $sizeCol ?>">
            <div class="h-100 d-flex flex-column">
                <h4 class="mb-0">Fichiers justificatifs</h4>

                <?php if(empty($data['files'])): ?>
                    <div class="card flex-grow-1 overflow-auto p-2" style="max-height: 220px;">
                        Aucun fichier justificatif n'a été fourni
                    </div>
                <?php else: ?>
                    <ul class="card flex-grow-1 list-group list-group-flush overflow-auto rounded scroll-shadows detail-justification-fixed-height">
                        <?php foreach ($data['files'] as $file): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-truncate me-3" title="<?= htmlspecialchars($file->getFileName()) ?>"><?= htmlspecialchars($file->getFileName()) ?></span>

                                <div class="btn-group btn-group-sm">
                                    <!-- Bouton pour ouvrir la modale -->
                                    <button
                                        class="btn btn-outline-danger bi bi-eye me-1"
                                        title="Voir"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#fileModal"
                                        data-bs-url="<?= '/fichier/' . $file->getIdFile() ?>"
                                        data-bs-file="<?= $file->getFileName() ?>"
                                    ></button>
                                    <!-- Télécharger -->
                                    <a class="btn btn-outline-primary bi bi-download" title="Télécharger" href="/fichier/<?= $file->getIdFile() ?>" download></a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Commentaire RP -->
        <?php if($isProcessed): ?>
            <div class="col-12 col-lg-<?= $sizeCol ?>">
                <div class="h-100 d-flex flex-column">
                    <strong>Commentaire du responsable</strong>
                    <div class="card flex-grow-1 overflow-auto p-2 detail-justification-fixed-height scroll-shadows">
                        <?= $justification->getRefusalReason() !== '' ? $justification->getRefusalReason() : 'Aucun commentaire du responsable n\'a été fourni' ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- actions -->
    <?php
    $currSlide = 0;

    $prevLabel = 'Accueil';
    $prevIcon = 'bi bi-house-door-fill';
    $prevAction = 'backToHome';
    $prevParam = '';

    $nextLabel = 'Voir les cours concernés';
    $nextIcon = 'bi-caret-right-square';
    $nextAction = 'showSlide';
    $nextParam = 1;

    require 'actionsBar.php';
    ?>
</div>