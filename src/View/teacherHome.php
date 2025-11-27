<?php
/** Front-end pour le tableau de bord professeur */
global $dataView;

require_once __DIR__ . "/Composants/header.php";
?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">

  <!-- Tab -->
  <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="text-black nav-link active" id="abs-tab" data-bs-toggle="tab" data-bs-target="#abs-tab-pane" type="button" role="tab" aria-controls="abs-tab-pane" aria-selected="true">Absences</button>
    </li>
  </ul>

  <!-- Contenu de la page -->
  <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
    <div class="tab-pane fade show active d-flex flex-column flex-fill" style="min-height: 0" id="abs-tab-pane" role="tabpanel" aria-labelledby="abs-tab" tabindex="0">
      <?php
      $tabName = 'abs';
//      $filter = $dataView->filterVM->filter;
//      require __DIR__ . "/Composants/filter_bar.php";
      ?>

      <div class="h-100 overflow-y-auto" id="justificationFlush">
        <?php
        $listPeriod = $dataView->periods;

        foreach($listPeriod as $period) {
          require __DIR__ . "/Composants/lineClassPeriod.php";
        }
        ?>
        <?php if (count($listPeriod) == 0): ?>
          <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <p class='fs-1 text-body-secondary p-3'>Il n'y a pas eu d'absences lors de vos cours</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

