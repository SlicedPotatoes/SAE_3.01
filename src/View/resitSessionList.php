<?php
global $dataView;

$filter = $dataView->filters;

require_once __DIR__ . "/Composants/header.php";
?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">

    <!-- Tab -->
    <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
        <li class="nav-item" role="presentation">
            <button
              class="text-black nav-link active"
              id="resit-tab"
              data-bs-toggle="tab"
              data-bs-target="#resit-tab-pane"
              type="button"
              role="tab"
              aria-controls="resit-tab-pane"
              aria-selected="true">Rattrapages</button>
        </li>
    </ul>

    <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
        <div class="tab-pane fade show active d-flex flex-column flex-fill" style="min-height: 0" id="resit-tab-pane" role="tabpanel" aria-labelledby="resit-tab" tabindex="0">
            <!-- Bar de filtres -->
            <?php
            require __DIR__ . "/Composants/resitFilterBar.php";
            ?>

            <!-- Affiche la liste des crénaux selectionner avec les filtres -->
            <?php
            foreach($dataView->periods as $period) {
                require __DIR__ . "/Composants/lineClassPeriod.php";
            }
            ?>
            <?php if (count($dataView->periods) == 0): ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <p class='fs-1 text-body-secondary p-3'>Il n'y a pas eu d'absence lors d'examen</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
