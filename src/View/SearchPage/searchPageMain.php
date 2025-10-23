<?php
/**
 * Front end de la page de recherche d'étudiant
 */

require_once __DIR__ . "/../../Model/Account/Student.php";
require_once __DIR__ . "/../../Model/Absence/Absence.php";
require_once __DIR__ . "/../../Model/Account/GroupStudent.php";

// Données temporaires
$groupe = new GroupStudent(-1, "Groupe -1");
$groupe2 = new GroupStudent(-2, "Groupe -2");
$groupe3 = new GroupStudent(-3, "Groupe -3");
$groupe4 = new GroupStudent(-4, "Groupe -4");

$listGroupes = [$groupe, $groupe2, $groupe3, $groupe4];

$students = [
        new Student(-1,
                "Vansteekiste",
                "Dimitri",
                "Dimitri.Vansteekiste@uphf.fr",
                AccountType::Student,
                -12,
                $groupe),
        new Student(-2,
                "Godisiabois",
                "Isaac",
                "Isaac.Godisiabois@uphf.fr",
                AccountType::Student,
                -8,
                $groupe2),
        new Student(-3,
                "Helin",
                "Esteban",
                "Esteban.helin@uphf.fr",
                AccountType::Student,
                -10,
                $groupe3),
        new Student(-4,
                "Dascotte",
                "Yann",
                "Yann.Dascotte@uphf.fr",
                AccountType::Student,
                -7,
                $groupe4),
        new Student(-5,
                "Masmejean",
                "Kevin",
                "Kevin.Masmejean@uphf.fr",
                AccountType::Student,
                -9,
                $groupe),
        new Student(-6,
                "Picouleau",
                "Louis",
                "Louis.Picouleau@uphf.fr",
                AccountType::Student,
                -11,
                $groupe2),
];

//echo '<pre>' . htmlspecialchars(print_r($students, true), ENT_QUOTES, 'UTF-8') . '</pre>';
?>

<div class="card p-3">
<!-- Conteneur principal: colonne flex, la hauteur peut être contrôlée par `max-height` -->
<div class="search-panel" style="height:70vh; display:flex; flex-direction:column; border-radius:6px; min-height:0;">

    <!-- Barre de recherche collée en haut du conteneur -->
    <nav class="navbar navbar-light search-bar" style="position:sticky; top:0; z-index:10; background:#fff;">
        <div class="container-fluid">
            <form class="d-flex w-100" role="search" method="GET" action="#">
                <!-- rendre l'input flexible pour céder de la place au select compact -->
                <input class="form-control me-2" style="flex:1 1 auto; min-width:0;" type="search"
                       placeholder="Rechercher un étudiant" name="q" aria-label="Rechercher un étudiant">
                <!-- select plus compact : small + largeur auto limité -->
                <select class="form-select form-select-sm me-2 w-auto" name="groupe"
                        style="flex:0 0 auto; width:auto; min-width:120px; max-width:200px;">
                    <option value="" disabled selected>Groupe</option>
                    <?php foreach ($listGroupes as $groupeOption): ?>
                        <option value="<?= htmlspecialchars($groupeOption->getLabel(), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($groupeOption->getLabel(), ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-uphf btn-outline-success" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </nav>


    <!-- Zone défilante contenant les cartes : occupe le reste de la hauteur du conteneur -->
    <div class="cards-list" style="overflow-y:auto; overflow-x:hidden; flex:1 1 auto; padding:0.5rem; min-height:0;">
        <?php if (empty($students)): ?>
            <p class='fs-3 text-body-secondary text-center p-3 m-0'>Pas d'absences</p>
        <?php else: ?>
            <?php foreach ($students as $student): ?>
                <div class="card mt-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-person-circle icon-uphf me-3 fs-2"></i>
                        <div class="p-2 flex-fill">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($student->getFirstName() . ' ' . $student->getLastName(), ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="card-text mb-0"><?= htmlspecialchars($student->getGroupStudent() ? $student->getGroupStudent()->getLabel() : '', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="card-text mb-0">Numéro
                                étudiant: <?= htmlspecialchars($student->getStudentNumber(), ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="p-2 text-end">
                            <a href="index.php?currPage=studentProfile&studentId=<?= urlencode((string)$student->getIdAccount()) ?>"
                               class="btn btn-uphf">Voir le profil</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
</div>