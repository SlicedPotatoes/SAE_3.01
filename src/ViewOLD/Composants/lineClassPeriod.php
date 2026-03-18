<?php

/**
 * Front d'une ligne d'un créneau de cours sur le tableau de bord de l'enseignant
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

$timeSlug = $timeslot->getTime()->format('Y-m-d-H-i');
$resourceSlug = $timeslot->getResource()->getIdResource();
$teacherSlug  = $timeslot->getTeacher()->getIdAccount();
$isTeacher    = AuthManager::isRole(AccountType::Teacher);

$groupValue = $timeslot->getGroup();
$groupSlug  = $groupValue !== '' ? $groupValue : 'nogroup';
$url = sprintf(
  '/absences-a-un-cours/%s/%s/%s/%s',
  urlencode((string) $teacherSlug),
  urlencode((string) $resourceSlug),
  urlencode($groupSlug),
  urlencode($timeSlug)
);

?>
    <div class="d-flex align-items-center gap-3 p-4 pb-3 pt-3 border-bottom">
        <div class="d-flex flex-column me-5">
            <div>Créneau: <?= $timeslot->getTime()->format('d/m/Y') ?>, <?= $timeslot->getTime()->format('H\hi') ?></div>
            <div>Nombre d'absences: <?= $timeslot->getCountStudentsAbsences() ?></div>
        </div>

        <div class="d-flex flex-column">
            <div>Ressource: <?= $timeslot->getResource()->getLabel() ?></div>
            <div>Groupe: <?= $timeslot->getGroup() ?></div>
        </div>


        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <?php if ($timeslot->isExamen() && $isTeacher) : ?>
            <span class='badge rounded-pill text-bg-warning'>Examen</span>
            <?php endif; ?>
        </div>

        <a href="<?= $url ?>" class="text-decoration-none">
            <button class="btn btn-uphf" type="button">
                Voir le détail
            </button>
        </a>
    </div>
