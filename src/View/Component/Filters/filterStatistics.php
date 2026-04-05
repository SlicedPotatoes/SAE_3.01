<?php
/**
 * Composant filtre, pour les statistiques.
 */

use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
?>

<form id="filter" class="border-bottom px-3 pt-2 m-0">

    <!-- Bouton mobile pour afficher/caché les filtres -->
    <div class="d-lg-none py-2">
        <button class="btn btn-outline-secondary w-100"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#statisticsFiltersCollapse">
            Filtres
        </button>
    </div>

    <!-- Contenu -->
    <div class="collapse d-lg-block" id="statisticsFiltersCollapse">

        <div class="row g-3 pb-3 align-items-end">
            <div class="col-12 col-lg-auto">
                <label for="statisticsState" class="form-label">État</label>
                <select class="form-select" id="statisticsState">
                    <option value="">Tout</option>
                    <?php foreach (StateAbs::getAll() as $state): ?>
                        <option value="<?= $state->value ?>"><?= $state->label() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-lg-auto">
                <label for="statisticsGroups" class="form-label">Groupe</label>
                <select class="form-select" id="statisticsGroups">
                    <option value="">Tout</option>
                    <?php foreach ($data['groups'] as $group): ?>
                        <option value="<?= $group->getIdGroupStudent() ?>"><?= $group->getLabel() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

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
                    type="button">
                    Appliquer les filtres
                </button>
            </div>

        </div>
    </div>
</form>