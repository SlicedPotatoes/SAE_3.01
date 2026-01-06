<?php
/**
 * Front end de la page sur les statistiques
 */

use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Statistics\Chart;

global $dataView;

require __DIR__ . "/Composants/header.php";
?>


<script src="/script/chart.js"></script>
<script src="/script/chartjs-helper.js"></script>
<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <!-- tab -->
    <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
        <?php foreach (ProportionStatisticsType::getAll() as $type): ?>
        <li class="nav-item" role="presentation">
            <button
                    class="tab-draggable text-black nav-link <?= $dataView->currTab->value == $type->value ? 'active' : '' ?>"
                    id="<?= $type->value ?>-tab"

                    data-bs-toggle="tab"
                    data-bs-target="#<?= $type->value ?>-tab-pane"
                    type="button"
                    role="tab"
                    aria-controls="<?= $type->value ?>-tab-pane"
                    aria-selected="true"
                    title="<?= $type->title() ?>"
                    draggable="true"
            >
                <?= $type->shortTitle() ?>
            </button>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- Filtre -->
    <div class="pt-3 border-start border-end">
        <form class="border-bottom px-4 m-0" method="POST">
            <input type="hidden" name="currTab" value="">

            <div class="d-flex flex-grow gap-3 pb-3">
                <!-- Select State -->
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="state" class="col-form-label">État:</label>
                    </div>

                    <div class="col-auto">
                        <select class="form-select" id="state" name="state">
                            <option value="" <?= !isset($dataView->filters['state']) ? 'selected' : '' ?>>Tout</option>
                            <?php
                            // Liste des différents états
                            foreach(StateAbs::getAll() as $state) {
                                echo "<option value='".$state->value."'". (isset($dataView->filters['state']) && $dataView->filters['state'] == $state ? 'selected' : '') .">".$state->label()."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Groupe -->
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="group" class="col-form-label">Groupe:</label>
                    </div>

                    <div class="col-auto">
                        <select class="form-select" id="group" name="group">
                            <option value="" <?= !isset($dataView->filters['group']) ? 'selected' : '' ?>>Tout</option>
                            <?php
                            foreach($dataView->groups as $group) {
                                echo "<option value='".$group['id']."'". (isset($dataView->filters['group']) && $dataView->filters['group'] == $group['id'] ? 'selected' : '') .">".$group['label']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Examen -->
                <div class="row g-3 align-items-center flex-grow-1">
                    <div class="col-auto">
                        <input class="form-check-input" type="checkbox" id="examen" name="examen" <?= isset($dataView->filters['examen']) ? 'checked' : '' ?>>
                    </div>
                    <div class="col-auto">
                        <label for="examen" class="form-check-label">Examen</label>
                    </div>
                </div>

                <button class="btn btn-uphf ml-auto" type="submit">Appliquer les filtres</button>
            </div>
        </form>
    </div>


    <div class="flex-fill d-flex flex-column" style="min-height: 0">
        <div class="d-flex flex-row flex-fill" style="min-height: 0">
            <!-- Conteneur à statistique -->
            <div class="col-11 tab-content bg-white border-bottom border-start border-end pt-3 flex-fill d-flex flex-column" id="left-zone" style="min-height: 0; border-bottom-left-radius:  var(--bs-border-radius)" id="tab-dashboard-stuContent">
                <?php foreach(ProportionStatisticsType::getAll() as $typeS): ?>
                    <div class="tab-pane fade show <?= $dataView->currTab->value == $typeS->value ? 'active d-flex' : '' ?> flex-column flex-fill align-items-center p-4" style="min-height: 0" id="<?= $typeS->value ?>-tab-pane" role="tabpanel" aria-labelledby="<?= $typeS->value ?>-tab" tabindex="0">
                        <?php
                        $type = 'pie';
                        $data = [
                                "labels" => $dataView->datas[$typeS->value]['labels'],
                                "datasets" => [
                                        [
                                                "label" => 'dataset',
                                                "data" => $dataView->datas[$typeS->value]['data'],
                                                "backgroundColor" => $dataView->datas[$typeS->value]['backgroundColor']
                                        ]
                                ]
                        ];

                        try {
                            echo new Chart($type, $data, Chart::getOptionsForPieChart($typeS->title()))->toHtml();
                        }
                        catch (JsonException $e) {
                            echo "Erreur lors de la création du graphique";
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Conteneur pour le graphique draggable -->
            <div class="col-1 bg-white border-bottom border-end p-4 pt-5" style=" border-bottom-right-radius:  var(--bs-border-radius)" id="drag-zone">

            </div>
        </div>

    </div>
    <script src="/script/tabBarHandler.js"></script>
</div>