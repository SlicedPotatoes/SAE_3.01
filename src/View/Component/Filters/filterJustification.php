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

    <!-- Filtres -->
    <div class="collapse d-lg-block" id="<?= $prefix ?>FiltersCollapse">

        <div class="row g-3 pb-3 align-items-end">

            <!-- Filtre des dates -->
            <?php
            $prefix = $prefix ?? "proof";
            require "filterDateRange.php";
            ?>

            <div class="col-12 col-lg-auto">
                <label for="<?= $prefix ?>State" class="form-label">État</label>

                <select class="form-select" id="<?= $prefix ?>State">
                    <option value="">Tout</option>
                    <option value="Processed">Traité</option>
                    <option value="NotProcessed">En cours de traitement</option>

                    <?php foreach($states as $state): ?>
                        <option value="<?= $state->value ?>">
                            <?= $state->label() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-lg-auto ms-lg-auto">
                <button
                        class="apply-filters btn btn-uphf w-100 w-lg-auto"
                        type="button"
                        data-target="justification" >
                    Appliquer les filtres
                </button>
            </div>
        </div>
    </div>
</form>