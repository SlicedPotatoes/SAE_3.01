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

    <!-- Zone défilante contenant les cartes : occupe le reste de la hauteur du conteneur -->
    <div class="cards-list flex-fill overflow-y-auto" style="min-height: 0">
        <?php if (empty($students)): ?>
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class='fs-1 text-body-secondary p-3'>Pas d'étudiant</p>
            </div>
        <?php else: ?>
            <?php foreach ($students as $student): ?>
                <div class="card mt-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-person-circle icon-uphf me-3 fs-2"></i>
                        <div class="p-2 flex-fill">
                            <h5 class="card-title mb-0"><?= $student->getFirstName() . ' ' . $student->getLastName() ?></h5>
                            <p class="card-text mb-0"><?= $student->getGroupStudent()->getLabel() ?></p>
                            <p class="card-text mb-0">Numéro étudiant: <?= $student->getStudentNumber() ?></p>
                        </div>
                        <div class="p-2 text-end">
                            <a href="index.php?currPage=studentProfile&studentId=<?= $student->getIdAccount() ?>" class="btn btn-uphf">Voir le profil</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>