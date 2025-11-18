<?php
/**
 * Ligne d'un profil étudiant pour la fonction de recherche.
 */

global $student;
?>


<div class="card mt-2">
    <div class="card-body d-flex align-items-center">
        <i class="bi bi-person-circle icon-uphf me-3 fs-2"></i>
        <div class="p-0 flex-fill">
            <h5 class="card-title mb-0"><?= $student->fullname ?></h5>
            <p class="card-text mb-0"><?= $student->groupStudent ?></p>
            <p class="card-text mb-0">Numéro étudiant: <?= $student->studentNumber ?></p>
        </div>
        <div class="p-2 text-end">
            <a href="/StudentProfile/<?= $student->idAccount ?>" class="btn btn-uphf">Voir le profil</a>
        </div>
    </div>
</div>