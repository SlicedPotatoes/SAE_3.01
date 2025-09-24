<div class="accordion-flush" id="absFlush">
    <div class="accordion-item">
        <div class="accordion-header">
            <button class="accordion-button collapsed d-flex align-items-center gap-3 p-3 border-bottom accordion" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?= $abs->getId(); ?>" aria-expanded="false" aria-controls="flush-collapse-<?= $abs->getId(); ?>">
                <div class="d-flex flex-column">
                    <div>Date: <?= $abs->getTime()->format('Y-m-d H:i'); ?></div>
                    <div>Durée: <?= $abs->getDuration(); ?></div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <?php
                    $color = "success";
                    if($abs->getState()->getId() == 1) {
                        $color = "danger";
                    }
                    if($abs->getState()->getId() == 2) {
                        $color = "secondary";
                    }
                    echo "<span class='badge rounded-pill text-bg-$color'>".$abs->getState()->getLabel()."</span>";

                    if($abs->getExamen()) {
                        echo "<span class='badge rounded-pill text-bg-warning'>Examen</span>";
                    }
                    ?>
                </div>
            </button>
        </div>
        <div id="flush-collapse-<?= $abs->getId(); ?>" class="accordion-collapse collapse" data-bs-parent="#absFlush">
            <div class="accordion-body border-bottom p-3">
                Le détail de l'abs
            </div>
        </div>
    </div>
</div>