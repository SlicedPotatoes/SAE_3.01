<?php

/**
 * Front de la bar de filtre pour le tableau de bord de l'enseignant
 */

global $filter;

?>

<div class="h-100 overflow-y-auto" id="justificationFlush">
    <!-- FILTRE -->
    <form class="border-bottom px-4 m-0" method="POST">
        <div class="d-flex flex-row gap-3 pb-3">
            <!-- Filtre Date début -->
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="DateStart" class="col-form-label">Date début :</label>
                </div>
                <div class="col-auto">
                    <input
                      type="date"
                      id="DateStart"
                      class="form-control"
                      name="dateStartFilter"
                      value="<?= $filter['dateStartFilter'] ?? '' ?>"
                    >
                </div>
            </div>

            <!-- Filtre Date fin -->
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="DateEnd" class="col-form-label">Date fin :</label>
                </div>
                <div class="col-auto">
                    <input
                      type="date"
                      id="DateEnd"
                      class="form-control"
                      name="dateEndFilter"
                      value="<?= $filter['dateEndFilter'] ?? '' ?>"
                    >
                </div>
            </div>

            <!-- Filtre Examen -->
            <div class="row g-3 align-items-center flex-grow-1">
                <div class="col-auto">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="Exam"
                      name="examFilter"
                      value="1"
                      <?= !empty($filter['examFilter']) ? 'checked' : '' ?>
                    >
                </div>
                <div class="col-auto">
                    <label for="Exam" class="form-check-label">Examen</label>
                </div>
            </div>

            <button class="btn btn-uphf ms-auto" type="submit">Appliquer les filtres</button>
        </div>