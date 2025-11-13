<?php
/**
 * Front d'une ligne d'absence dans le dashboard
 */

global $index, $abs;
?>
<div class="accordion-item border-bottom">
    <!-- Information générale de l'absence -->
    <div class="accordion-header">
        <button class="accordion-button collapsed d-flex align-items-center gap-3 p-3 ps-4"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#flush-collapse-abs-<?= $index ?>"
                aria-expanded="false"
                aria-controls="flush-collapse-abs-<?= $index ?>"
        >
            <div class="d-flex flex-column">
                <div>Date: <?= $abs->date ?></div>
                <div>Durée: <?= $abs->duration ?></div>
            </div>
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <span class='badge rounded-pill text-bg-<?= $abs->state->colorBadge() ?>'><?= $abs->state->label() ?></span>
                <?php
                if($abs->examen) {
                    echo "<span class='badge rounded-pill text-bg-warning'>Examen</span>";
                }
                if($abs->lock) {
                    echo '<i style="font-size: 30px" class="bi bi-file-lock2" data-bs-toggle="tooltip" data-bs-title="Le responsable pédagogique n\'autorise pas la justification de cette absence"></i>';
                }
                ?>
            </div>
        </button>
    </div>
    <!-- Détail d'une absence -->
    <div id="flush-collapse-abs-<?= $index ?>" class="accordion-collapse collapse" data-bs-parent="#absFlush">
        <div class="accordion-body p-3">
            <!-- mettre les infos grâce à Absence-->
            <?php if($abs->haveTeacher != null): ?>
                <p><strong>Professeur :</strong> <?= htmlspecialchars($abs->fullnameTeacher) ?></p>
            <?php endif; ?>

            <p><strong>Matière :</strong> <?= htmlspecialchars($abs->courseType . " " . $abs->resource) ?></p>

            <?php if($abs->examen): ?>
                <p><strong>Rattrapage :</strong> <?= $abs->dateResit ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>