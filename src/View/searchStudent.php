<?php
/**
 * View de la page de recherche d'étudiant
 */

$firstPartMessage = 'Vous pouvez rechercher ici un';
$secondPartMessage = 'étudiant';
$thirdPartMessage = '!';
require __DIR__ . "/Component/header.php";
?>

<div class="card p-3 scroll-parent">

    <!-- Barre de recherche -->
    <div class="mb-3">
        <div class="d-flex w-100 gap-2">
            <input class="form-control flex-fill"
                   type="search"
                   id="searchInput"
                   placeholder="Nom, prénom ou n° étudiant"
                   aria-label="Rechercher un étudiant">
            <select class="form-select w-auto"
                    id="groupSelect"
                    aria-label="Filtrer par groupe">
                <option value="">Groupe</option>
                <?php foreach ($data['groups'] as $group): ?>
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
    <div id="studentsList" class="scrollable">
        <?php if (empty($data['students'])): ?>
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="fs-1 text-body-secondary p-3">Pas d'étudiant</p>
            </div>
        <?php else: ?>
            <?php foreach ($data['students'] as $student): ?>
                <?php require __DIR__ . "/Component/lineStudent.php"; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<script type="module" src="/js/pages/searchStudent.js"></script>
