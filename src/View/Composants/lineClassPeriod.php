<?php

/**
 * Front d'une ligne d'un créneau de cours sur le tableau de bord de l'enseignant
 */

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

global $period;
?>
    <div class="d-flex align-items-center gap-3 p-4 pb-3 pt-3 border-bottom">
        <div class="d-flex flex-column me-5">
            <div>Créneau: <?= $period->date ?>, <?= $period->time ?></div>
            <div>Nombre d'absences: <?= $period->absencesCount ?></div>
        </div>

        <div class="d-flex flex-column">
            <div>Ressource: <?= $period->course ?></div>
            <div>Groupe: <?= $period->group ?></div>
        </div>


        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <?php if ($period->isExam) : ?>
            <span class='badge rounded-pill text-bg-warning'>Examen</span>
            <?php endif; ?>
        </div>

        <a href="/DetailCrenau/<?= $period->idPeriod ?>" class="text-decoration-none">
            <button class="btn btn-uphf" type="button">
                Voir le détail
            </button>
        </a>
    </div>
