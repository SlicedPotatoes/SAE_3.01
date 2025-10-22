<?php
require_once __DIR__ . "/../../Model/Student.php";
require_once __DIR__ . "/../../Model/Absence.php";
require_once __DIR__ . "/../../Model/GroupStudent.php";
?>

<?php
// Données temporaires
$groupe = new GroupStudent(-1, "Groupe -1");
$groupe2 = new GroupStudent(-2, "Groupe -2");
$groupe3 = new GroupStudent(-3, "Groupe -3");
$groupe4 = new GroupStudent(-4, "Groupe -4");

$students = [
        new Student(-1, "Vansteekiste", "Dimitri", null, "Dimitri.Vansteekiste@uphf.fr", $groupe),
        new Student(-2, "Godisiabois", "Isaac", null, "Isaac.Godisiabois@uphf.fr", $groupe),
        new Student(-3, "Helin", "Esteban", null, "Esteban.helin@uphf.fr", $groupe),
        new Student(-4, "Dascotte", "Yann", null, "Yann.Dascotte@uphf.fr", $groupe),
        new Student(-5, "Masmejean", "Kevin", null, "Kevin.Masmejean@uphf.fr", $groupe),
        new Student(-6, "Picouleau", "Louis", null, "Louis.Picouleau@uphf.fr", $groupe)
];
?>

<!-- Conteneur principal: colonne flex, la hauteur peut être contrôlée par le parent; ici on fixe une hauteur raisonnable pour demo -->
<div class="search-panel" style="max-height:60vh; display:flex; flex-direction:column; border-radius:6px;">

    <!-- Barre de recherche collée en haut du conteneur -->
    <nav class="navbar navbar-light search-bar" style="position:sticky; top:0; z-index:10; background:var(#fff);">
        <div class="container-fluid">
            <form class="d-flex w-100" role="search" method="GET" action="recherche.php">
                <input class="form-control me-2" type="search" placeholder="Rechercher un étudiant" name="q">
                <select class="form-select me-2" name="groupe">
                    <option selected>Groupe</option>
                    <option value="name">

                <button class="btn btn-uphf btn-outline-success" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </nav>


    <!-- Zone défilante contenant les cartes : occupe le reste de la hauteur du conteneur -->
    <div class="cards-list" style="overflow:auto; flex:1 1 auto; padding:0.5rem;">
        <?php if (empty($students)): ?>
            <p class='fs-3 text-body-secondary text-center p-3 m-0'>Pas d'absences</p>
        <?php else: ?>
            <?php foreach ($students as $index => $student): ?>
                <div class="card mt-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-person-circle icon-uphf me-3 fs-2"></i>
                        <div class="p-2 flex-fill">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($student->getFirstName() . ' ' . $student->getLastName(), ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="card-text mb-0"><?= htmlspecialchars($student->getGroupStudent() ? $student->getGroupStudent()->getLabel() : '', ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="p-2 text-end">
                            <a href="index.php?currPage=studentProfile&studentId=<?= urlencode((string)$student->getStudentId()) ?>"
                               class="btn btn-primary">Voir le profil</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
