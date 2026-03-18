<?php

global $dataView;

/**
 * View pour les détails d'un crénaux
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

?>

<div class="card flex-fill d-flex flex-column gap-3 mt-4 p-3" style="min-height: 0">

    <!-- Information principale d'un crénaux -->
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-1">
              Crénau du <?= $data['timeslot']->getTime()->format('d/m/Y') ?>
              à <?= $data['timeslot']->getTime()->format('H\hi') ?>
            </h2>
            <div class="d-flex gap-3 align-items-center">
                <?php if ($data['timeslot']->isExamen()) : ?>
                <span class="badge rounded-pill text-bg-warning fs-6 px-3 py-2">
                    Examen
                </span>
                <?php endif; ?>
            </div>
        </div>

      <?php if (!AuthManager::isRole(AccountType::Teacher)) : ?>
        <div class="col-md-6"><strong>Enseignant :</strong> <?= $data['timeslot']->getTeacher()->getLastName() . ", " . $data['timeslot']->getTeacher()->getFirstName() ?></div>
      <?php endif; ?>
        <div class="col-md-6"><strong>Ressource :</strong> <?= $data['timeslot']->getResource()->getLabel() ?></div>
        <div class="col-md-6"><strong>Type de cours :</strong> <?= $data['timeslot']->getCourseType()->value ?></div>
        <?php if ($data['timeslot']->getGroup() !== null && $data['timeslot']->getGroup() !== '') : ?>
            <div class="col-md-6"><strong>Groupe :</strong> <?= $data['timeslot']->getGroup() ?></div>
        <?php endif; ?>
    </div>

    <!-- Liste des absences -->
    <div class="d-flex flex-column" style="flex: 1 1 30%; min-height: 0">
        <div class="d-flex align-items-center mb-2">
            <h4 class="mb-0">Absents</h4>
        </div>

        <div class="border-top flex-fill overflow-y-auto" style="min-height: 0">
            <?php foreach ($data['absences'] as $absence): ?>
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
