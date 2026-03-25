<form class="border-bottom px-2 px-md-4 m-0">

    <!-- Bouton mobile pour afficher/caché les filtres -->
    <div class="d-lg-none py-2">
        <button class="btn btn-outline-secondary w-100"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#absFiltersCollapse">
            Filtres
        </button>
    </div>

    <!-- Contenu -->
    <div class="collapse d-lg-block" id="absFiltersCollapse">

        <div class="row g-3 pb-3 align-items-end">

            <!-- Filtre des dates -->
            <?php
            $prefix = "abs";
            require "filterDateRange.php";
            ?>

            <div class="col-12 col-lg-auto">

                <div class="d-flex gap-3 flex-wrap align-items-end">

                    <div>
                        <label for="absState" class="form-label">État</label>
                        <select class="form-select" id="absState">
                            <option value="">Tout</option>
                            <option value="Validated">Validée</option>
                            <option value="Refused">Refusée</option>
                            <option value="NotJustified">Non-justifiée</option>
                            <option value="Pending">En attente</option>
                        </select>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="absLocked">
                        <label for="absLocked" class="form-check-label">
                            Verrouillé
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="absExam">
                        <label for="absExam" class="form-check-label">
                            Examen
                        </label>
                    </div>

                </div>
            </div>

            <!-- Bouton -->
            <div class="col-12 col-lg-auto ms-lg-auto">
                <button
                        class="apply-filters btn btn-uphf w-100 w-lg-auto"
                        type="button"
                        data-target="absence" >
                    Appliquer les filtres
                </button>
            </div>
        </div>
    </div>
</form>