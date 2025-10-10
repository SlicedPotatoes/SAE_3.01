<div class="accordion-item">
    <div class="accordion-header">
        <button class="accordion-button collapsed d-flex align-items-center gap-3 p-3"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#flush-collapse-justification-<?= $justification->getIdJustification(); ?>"
                aria-expanded="false" aria-controls="flush-collapse-justification-<?= $justification->getIdJustification(); ?>"
        >
            <div class="d-flex flex-column">
                <div>Date de début: <?= $justification->getStartDate()->format('d/m/Y'); ?></div>
                <div>Date de fin: <?= $justification->getEndDate()->format('d/m/Y') ?></div>
            </div>
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <span class='badge rounded-pill text-bg-<?= $justification->getCurrentState()->colorBadge() ?>'><?= $justification->getCurrentState()->label() ?></span>
            </div>
        </button>
    </div>
    <div id="flush-collapse-justification-<?= $justification->getIdJustification(); ?>" class="accordion-collapse collapse" data-bs-parent="#justificationFlush">
        <div class="accordion-body p-3">
            Le détail de la justification
        </div>
    </div>
</div>