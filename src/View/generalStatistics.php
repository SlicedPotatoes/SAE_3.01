<?php
/**
 * Front end de la page sur les statistiques
 */

use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script src="/script/chart.js"></script>');
Renderer::pushAsset('script', '<script type="module" src="/js/pages/statistics/generalStatistics.js"></script>');
Renderer::pushAsset('script', '<script type="module" src="/js/pages/statistics/draggableGraphics.js"></script>');

?>

<div class="card p-3 scroll-parent">
    <?php require __DIR__ . '/Component/Filters/filterStatistics.php' ?>

    <!-- Affichage Desktop -->
    <div class="d-none d-lg-flex flex-fill flex-column" style="min-height: 0">
        <!-- tab -->
        <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
            <?php foreach (ProportionStatisticsType::getAll() as $type): ?>
                <li class="nav-item" role="presentation">
                    <button
                            class="tab-draggable text-black nav-link <?= ProportionStatisticsType::getAll()[0] === $type ? 'active' : '' ?>"
                            id="<?= $type->value ?>-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#<?= $type->value ?>-tab-pane"
                            type="button"
                            role="tab"
                            aria-controls="<?= $type->value ?>-tab-pane"
                            aria-selected="true"
                            title="<?= $type->title() ?>"
                            data-drag="<?= $type->value ?>"
                            draggable="true"
                    >
                        <?= $type->shortTitle() ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Emplacement des filtres Desktop -->
        <div id="filter-desktop" class="border-start border-end"></div>

        <div class="scroll-parent flex-row">
            <!-- Conteneur à statistique -->
            <div class="col-10 tab-content bg-white border-bottom border-start border-end pt-3 scroll-parent" id="left-zone" style="border-bottom-left-radius:  var(--bs-border-radius)" id="tab-dashboard-stuContent">
                <?php foreach(ProportionStatisticsType::getAll() as $typeS): ?>
                    <div class="tab-pane fade show <?= ProportionStatisticsType::getAll()[0] === $typeS ? 'active' : '' ?> p-4 position-relative h-100 w-100" style="min-height: 0" id="<?= $typeS->value ?>-tab-pane" role="tabpanel" aria-labelledby="<?= $typeS->value ?>-tab" tabindex="0">
                        <canvas id="<?= $typeS->value ?>-chart" class="position-absolute top-50 start-50 translate-middle" style="max-height: 100%; max-width: 100%"></canvas>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Conteneur pour le graphique draggable -->
            <div class="col-2 bg-whitept-3 flex-fill d-flex flex-column" style="border: 2px dashed black; border-bottom-right-radius: var(--bs-border-radius)" id="drag-zone">
                <div class="p-1 text-center m-auto">Déposez un onglet ici pour activer la vue multi-graphique</div>
            </div>
        </div>
    </div>
    <!-- Affichage Mobile -->
    <div class="d-flex d-lg-none flex-fill flex-column" style="min-height: 0">
        <!-- Emplacement des filtres mobiles -->
        <div id="filter-mobile"></div>

        <div class="scroll-parent gap-2">
            <?php foreach(ProportionStatisticsType::getAll() as $typeS): ?>
                <canvas id="<?= $typeS->value ?>-chart-mobile" style="max-width: 100%"></canvas>
                <hr class="w-75 mx-auto">
            <?php endforeach; ?>
        </div>
    </div>
</div>