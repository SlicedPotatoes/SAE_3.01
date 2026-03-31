<?php
/**
 * View de la page de recherche d'étudiant
 */

use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script type="module" src="/js/pages/searchStudent.js"></script>');
?>

<div class="card p-3 scroll-parent">

    <!-- Barre de recherche -->
    <div class="mb-3">
        <div class="d-flex w-100 gap-2 flex-wrap flex-sm-nowrap">
            <input class="form-control flex-fill"
                   type="search"
                   id="searchInput"
                   placeholder="Nom ou prénom"
                   aria-label="Rechercher un étudiant">

            <select class="form-select w-auto"
                    id="groupSelect"
                    aria-label="Filtrer par groupe">
                <option value="">Groupe</option>
                <?php foreach ($data['groupStudent'] as $group): ?>
                    <option value="<?= $group->getIdGroupStudent() ?>">
                        <?= $group->getLabel() ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-uphf apply-filters" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </div>

    <!-- Liste des étudiants -->
    <div id="studentsList" class="scrollable"></div>
</div>