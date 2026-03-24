<?php $prefix = "timeslot"; ?>

<form class="border-bottom px-4 m-0">

    <!-- Bouton mobile pour afficher/caché les filtres -->
    <div class="d-lg-none py-2">
        <button class="btn btn-outline-secondary w-100"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#<?= $prefix ?>FiltersCollapse">
            Filtres
        </button>
    </div>

    <!-- Contenu -->
    <div class="collapse d-lg-block" id="<?= $prefix ?>FiltersCollapse">

        <div class="row g-3 pb-3 align-items-end">

            <!-- Filtre des dates -->
            <?php require "filterDateRange.php"; ?>

            <div class="col-12 col-lg-auto">
                <div class="form-check mt-2 mt-lg-4">
                    <input class="form-check-input"
                           type="checkbox"
                           id="examFilter">

                    <label class="form-check-label"
                           for="examFilter">
                        Examen
                    </label>
                </div>
            </div>

            <!-- Bouton -->
            <div class="col-12 col-lg-auto ms-lg-auto">
                <button
                    class="apply-filters btn btn-uphf w-100 w-lg-auto"
                    type="button"
                    data-target="timeslot">
                    Appliquer les filtres
                </button>
            </div>

        </div>
    </div>
</form>