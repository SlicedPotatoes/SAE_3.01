<?php
/**
 * Front end de la page sur les statistiques
 */

use Uphf\GestionAbsence\Model\Statistics\Chart;

global $dataView;

require __DIR__ . "/Composants/header.php";
?>


<script src="/script/chart.js"></script>
<script src="/script/chartjs-callbacks-function.js"></script>
<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link <?= 'active'//$dataView->currTab == 'proof' ? 'active' : '' ?>" id="stat1-tab" data-bs-toggle="tab" data-bs-target="#stat1-tab-pane" type="button" role="tab" aria-controls="stat1-tab-pane" aria-selected="true">Proportion par type de cours</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link <?= ''//$dataView->currTab == 'abs' ? 'active' : '' ?>" id="stat2-tab" data-bs-toggle="tab" data-bs-target="#stat2-tab-pane" type="button" role="tab" aria-controls="stat2-tab-pane" aria-selected="false">Absences</button>
        </li>
    </ul>

    <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
        <div class="tab-pane fade show <?= 'active d-flex'//$dataView->currTab == 'proof' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0" id="stat1-tab-pane" role="tabpanel" aria-labelledby="stat1-tab" tabindex="0">
            <?php
            $type = 'pie';
            $data = [
                    "labels" => ["Red", "Blue", "Yellow"],
                    "datasets" => [
                            [
                                    "labels" => '# of Votes',
                                    "data" => [4, 4, 8],
                            ]
                    ]
            ];

            try {
                echo new Chart($type, $data, Chart::getOptionsForPieChart())->toHtml();
            }
            catch (JsonException $e) {
                echo "Erreur lors de la création du graphique";
            }
            ?>
        </div>
        <div class="tab-pane fade show <?= ''//$dataView->currTab == 'proof' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0" id="stat2-tab-pane" role="tabpanel" aria-labelledby="stat2-tab" tabindex="0">
            2
        </div>
    </div>
    <script src="/script/tabBarHandler.js"></script>
</div>