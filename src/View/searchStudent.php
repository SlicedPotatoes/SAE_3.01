<?php
/**
 * Front end de la page de recherche d'étudiant
 */

use Uphf\GestionAbsence\Presentation\StudentPresentation;
use Uphf\GestionAbsence\Model\Filter\FilterStudent;

$filter = new FilterStudent(
        isset($_GET['studentSearchValue']) && $_GET['studentSearchValue'] != '' ? $_GET['studentSearchValue'] : null,
        isset($_GET['studentSearchGroup']) && $_GET['studentSearchGroup'] != '' ? $_GET['studentSearchGroup'] : null
);

$listGroupes = StudentPresentation::getAllGroupsStudent();
$students = StudentPresentation::getAllStudents($filter);

?>

<div class="card p-3 flex-fill d-flex flex-column mt-3" style="min-height: 0">
    <!-- Barre de recherche -->
    <nav class="navbar navbar-light search-bar">
        <form class="d-flex w-100" method="GET" action="index.php">
            <input type="hidden" name="currPage" value="searchpage"/>

            <!-- rendre l'input flexible pour céder de la place au select compact -->
            <input class="form-control me-2" style="flex:1 1 auto; min-width:0;" type="search" placeholder="Rechercher un étudiant" name="studentSearchValue" aria-label="Rechercher un étudiant" value="<?= $filter->getSearch() ?? '' ?>">
            <!-- select plus compact : small + largeur auto limité -->
            <select class="form-select form-select-sm me-2 w-auto" name="studentSearchGroup"
                    style="flex:0 0 auto; width:auto; min-width:120px; max-width:200px;" aria-label="groupStudent">
                <option value="" <?= $filter->getGroupStudent() == null ? 'selected' : '' ?>>Groupe</option>
                <?php foreach ($listGroupes as $groupeOption): ?>
                    <option
                            value="<?= $groupeOption->getIdGroupStudent() ?>"
                            <?= $filter->getGroupStudent() == $groupeOption->getIdGroupStudent() ? 'selected' : '' ?>
                    ><?= $groupeOption->getLabel() ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-uphf btn-outline-success" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </nav>

    <?php require __DIR__ . '/Composants/lineStudent.php'; ?>


</div>