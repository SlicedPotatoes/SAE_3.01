<?php

global $dataView;

/**
 * View pour les détails d'un crénaux
 */

use Uphf\GestionAbsence\Model\CookieManager;

?>

<div class="card flex-fill d-flex flex-column gap-3 mt-4 p-3" style="min-height: 0">

    <!-- Information principale d'un crénaux -->
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-1">
              Crénau du <?= $dataView->time->format('d/m/Y') ?>
              à <?= $dataView->time->format('H\hi') ?>
            </h2>
            <div class="d-flex gap-3 align-items-center">
                <?php if ($dataView->examen) : ?>
                <span class="badge rounded-pill text-bg-warning fs-6 px-3 py-2">
                    Examen
                </span>
                <?php endif; ?>
            </div>
        </div>

      <?php if (!$dataView->isTeacher) : ?>
        <div class="col-md-6"><strong>Enseignant :</strong> <?= $dataView->teacherName ?></div>
      <?php endif; ?>
        <div class="col-md-6"><strong>Ressource :</strong> <?= $dataView->ressource->getLabel() ?></div>
        <div class="col-md-6"><strong>Type de cours :</strong> <?= $dataView->courseType->value ?></div>
        <?php if ($dataView->group !== null && $dataView->group !== '') : ?>
            <div class="col-md-6"><strong>Groupe :</strong> <?= $dataView->group ?></div>
        <?php endif; ?>
    </div>

    <!-- Liste des absences -->
    <div class="d-flex flex-column" style="flex: 1 1 30%; min-height: 0">
        <div class="d-flex align-items-center mb-2">
            <h4 class="mb-0">Absents</h4>
        </div>

        <div class="border-top flex-fill overflow-y-auto" style="min-height: 0">
            <?php foreach ($dataView->absences as $absence): ?>
                <div class="d-flex align-items-center border-bottom py-2">
                    <!-- Nom de l'étudiant -->
                    <div class="me-3">
                        <div>Prénom: <?= $absence->getStudent()->getFirstName() ?></div>
                        <div>Nom: <?= $absence->getStudent()->getLastName() ?></div>
                    </div>

                    <!-- Etat de l'absence -->
                        <span class="badge rounded-pill text-bg-<?= $absence->getCurrentState()->colorBadge() ?>">
                            <?= $absence->getCurrentState()->label() ?>
                        </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Button de retour -->
    <div class="d-flex justify-content-between">
        <a href="<?= CookieManager::getLastPath() ?>" class="btn btn-secondary">Retour</a>
    </div>
</div>
