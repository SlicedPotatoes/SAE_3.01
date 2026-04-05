<?php
/**
 * Front end de la page sur les statistiques
 */

use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script src="/script/chart.js"></script>');
Renderer::pushAsset('script', '<script>const ID_STUDENT = '. $data['student']->getIdAccount() .'</script>');
Renderer::pushAsset('script', '<script type="module" src="/js/pages/statistics/studentStatistics.js"></script>');
?>

<div class="card p-3 scroll-parent">
    <?php require __DIR__ . '/Component/Filters/filterStatistics.php' ?>

    <!-- Affichage Desktop -->
    <div class="d-none d-lg-flex flex-fill flex-column" style="min-height: 0">
        <!-- tab -->
        <ul class="nav nav-tabs" role="tablist">
            <?php foreach (ProportionStatisticsType::getAll() as $type): ?>
                <li class="nav-item" role="presentation">
                    <button
                            class="text-black nav-link <?= ProportionStatisticsType::getAll()[0] === $type ? 'active' : '' ?>"
                            id="<?= $type->value ?>-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#<?= $type->value ?>-tab-pane"
                            type="button"
                            role="tab"
                            aria-controls="<?= $type->value ?>-tab-pane"
                            aria-selected="true"
                            title="<?= $type->title() ?>"
                    >
                        <?= $type->shortTitle() ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Emplacement des filtres Desktop -->
        <div id="filter-desktop" class="border-start border-end"></div>

        <div class="tab-content scroll-parent border-bottom border-start border-end rounded-bottom">
            <?php foreach(ProportionStatisticsType::getAll() as $typeS): ?>
            <div class="container h-100 tab-pane fade <?= ProportionStatisticsType::getAll()[0] === $typeS ? 'show active' : '' ?>"
                 style="min-height: 0"
                 id="<?= $typeS->value ?>-tab-pane"
                 role="tabpanel"
                 aria-labelledby="<?= $typeS->value ?>-tab"
                 tabindex="0">
                <div class="row h-100">
                    <div class="col-6 p-4 position-relative h-100 border-end">
                        <canvas id="<?= $typeS->value ?>-chart" class="position-absolute top-50 start-50 translate-middle" style="max-height: 100%; max-width: 100%"></canvas>
                    </div>
                    <div class="col-6 p-4 position-relative h-100">
                        <canvas id="<?= $typeS->value ?>-chart-student" class="position-absolute top-50 start-50 translate-middle" style="max-height: 100%; max-width: 100%"></canvas>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Affichage Mobile -->
    <div class="d-flex d-lg-none flex-fill flex-column" style="min-height: 0">
        <!-- Emplacement des filtres mobiles -->
        <div id="filter-mobile"></div>

        <div class="scroll-parent gap-2">
            <?php foreach(ProportionStatisticsType::getAll() as $typeS): ?>
                <canvas id="<?= $typeS->value ?>-chart-mobile" style="max-width: 100%"></canvas>
                <canvas id="<?= $typeS->value ?>-chart-mobile-student" style="max-width: 100%"></canvas>
                <hr class="w-75 mx-auto">
            <?php endforeach; ?>
        </div>
    </div>
</div>