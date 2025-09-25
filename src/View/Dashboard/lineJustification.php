<div class="accordion-flush" id="absFlush">
    <div class="accordion-item">
        <div class="accordion-header">
            <button class="accordion-button collapsed d-flex align-items-center gap-3 p-3 border-bottom accordion" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?= $justification->getId(); ?>" aria-expanded="false" aria-controls="flush-collapse-<?= $justification->getId(); ?>">
                <div class="d-flex flex-column">
                    <div>Date de début: <?= $justification->getStart()->format('Y-m-d'); ?></div>
                    <div>Date de fin: <?= $justification->getEnd()->format('Y-m-d') ?></div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <?php
                        if($justification->getProcessed()) {
                            echo "<span class='badge rounded-pill text-bg-success'>Traité</span>";
                        }
                        else {
                            echo "<span class='badge rounded-pill text-bg-secondary'>Non traité</span>";
                        }
                    ?>
                </div>
            </button>
        </div>
        <div id="flush-collapse-<?= $justification->getId(); ?>" class="accordion-collapse collapse" data-bs-parent="#absFlush">
            <div class="accordion-body border-bottom p-3">
                Le détail de la justification
            </div>
        </div>
    </div>
</div>