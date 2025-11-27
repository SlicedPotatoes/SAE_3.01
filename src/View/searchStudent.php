<?php
/**
 * Front end de la page de recherche d'étudiant
 */

global $dataView;
?>

<?php require __DIR__ . "/Composants/header.php"; ?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <!-- Barre de recherche -->
    <nav class="navbar navbar-light search-bar">
        <form class="d-flex w-100" method="POST">
            <!-- rendre l'input flexible pour céder de la place au select compact -->
            <input class="form-control me-2" style="flex:1 1 auto; min-width:0;" type="search" placeholder="Rechercher un étudiant" name="search" aria-label="Rechercher un étudiant" value="<?= $dataView->filter['search'] ?? '' ?>">
            <!-- select plus compact : small + largeur auto limité -->
            <select class="form-select form-select-sm me-2 w-auto" name="groupStudent"
                    style="flex:0 0 auto; width:auto; min-width:120px; max-width:200px;" aria-label="groupStudent">
                <option value="" <?= !isset($dataView->filter['groupStudent']) ? 'selected' : '' ?>>Groupe</option>
                <?php foreach ($dataView->listGroup as $groupOption): ?>
                    <option
                            value="<?= $groupOption['idGroup'] ?>"
                            <?= isset($dataView->filter['groupStudent']) && $dataView->filter['groupStudent'] == $groupOption['idGroup'] ? 'selected' : '' ?>
                    ><?= $groupOption['label'] ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-uphf btn-outline-success" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </nav>

    <!-- Zone défilante contenant les cartes : occupe le reste de la hauteur du conteneur -->
    <div class="cards-list flex-fill overflow-y-auto" style="min-height: 0">
        <?php if (empty($dataView->students)): ?>
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class='fs-1 text-body-secondary p-3'>Pas d'étudiant</p>
            </div>
        <?php else: ?>
            <?php
            foreach ($dataView->students as $student) {
                require __DIR__ . "/Composants/lineStudent.php";
            }
            ?>
        <?php endif; ?>
    </div>
</div>