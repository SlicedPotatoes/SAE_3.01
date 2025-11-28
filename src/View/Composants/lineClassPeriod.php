<?php

/**
 * Front d'une ligne d'un créneau de cours sur le tableau de bord de l'enseignant
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

global $period;

$timeSlug = $period->getTime()->format('Y-m-d-H-i');
$resourceSlug = $period->getResource()->getIdResource();
$teacherSlug  = $period->getTeacher()->getIdAccount();
$isTeacher    = AuthManager::isRole(AccountType::Teacher);

$groupValue = $period->getGroup();
$groupSlug  = $groupValue !== '' ? $groupValue : 'nogroup';

$url = sprintf(
  '/detailPeriod?time=%s&resourceId=%s&teacher=%s&group=%s',
  urlencode($timeSlug),
  urlencode((string) $resourceSlug),
  urlencode((string) $teacherSlug),
  urlencode($groupSlug)
);

?>
    <div class="d-flex align-items-center gap-3 p-4 pb-3 pt-3 border-bottom">
        <div class="d-flex flex-column me-5">
            <div>Créneau: <?= $period->getTime()->format('d/m/Y') ?>, <?= $period->getTime()->format('H\hi') ?></div>
            <div>Nombre d'absences: <?= $period->getCountStudentsAbsences() ?></div>
        </div>

        <div class="d-flex flex-column">
            <div>Ressource: <?= $period->getResource()->getLabel() ?></div>
            <div>Groupe: <?= $period->getGroup() ?></div>
        </div>


        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <?php if ($period->isExamen() && $isTeacher) : ?>
            <span class='badge rounded-pill text-bg-warning'>Examen</span>
            <?php endif; ?>
        </div>

        <a href="<?= $url ?>" class="text-decoration-none">
            <button class="btn btn-uphf" type="button">
                Voir le détail
            </button>
        </a>
    </div>
