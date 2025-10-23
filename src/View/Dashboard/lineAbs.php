<?php
/*
 * Front d'une ligne d'absence dans le dashboard
 */
?>
<div class="accordion-item">
    <div class="accordion-header">
        <button class="accordion-button collapsed d-flex align-items-center gap-3 p-3"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#flush-collapse-abs-<?= $index ?>"
                aria-expanded="false"
                aria-controls="flush-collapse-abs-<?= $index ?>"
        >
            <div class="d-flex flex-column">
                <div>Date: <?= $abs->getTime()->format('d/m/Y H:i') ?></div>
                <div>Durée: <?= $abs->getDuration() ?></div>
            </div>
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <span class='badge rounded-pill text-bg-<?= $abs->getCurrentState()->colorBadge() ?>'><?= $abs->getCurrentState()->label() ?></span>
                <?php
                if($abs->getExamen()) {
                    echo "<span class='badge rounded-pill text-bg-warning'>Examen</span>";
                }
                if(!$abs->getAllowedJustification() && ($abs->getCurrentState() == StateAbs::Refused || $abs->getCurrentState() == StateAbs::NotJustified)) {
                    echo '<i style="font-size: 30px" class="bi bi-file-lock2" data-bs-toggle="tooltip" data-bs-title="Le responsable pédagogique n\'autorise pas la justification de cette absence"></i>';
                }
                ?>
            </div>
        </button>
    </div>
    <div id="flush-collapse-abs-<?= $index ?>" class="accordion-collapse collapse" data-bs-parent="#absFlush">
        <div class="accordion-body p-3">
            <!-- mettre les info grâce a Absence-->
            <?php if($abs->getTeacher() != null): ?>
                <p><strong>Professeur :</strong> <?= htmlspecialchars($abs->getTeacher()->getFirstname() . ' ' . $abs->getTeacher()->getLastname()) ?></p>
            <?php endif; ?>

            <p><strong>Matière :</strong> <?= htmlspecialchars($abs->getCourseType()->value ?? $abs->getCourseType()->name) ?> <?= htmlspecialchars( $abs->getResource()->getlabel()) ?></p>

            <?php if($abs->getExamen()): ?>
                <p><strong>Rattrapage :</strong> <?= $abs->getDateResit() ? $abs->getDateResit()->format('d/m/Y H:i') : 'Pas de date fixée' ?></p>
            <?php endif; ?>


        </div>
    </div>
</div>