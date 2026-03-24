<?php
/**
 * Ligne d'un profil étudiant pour la fonction de recherche.
 */
?>

<div class="card mt-2">
    <div class="card-body">

        <!-- Infos étudiant -->
        <div class="d-flex align-items-center">
            <i class="bi bi-person-circle icon-uphf me-3 fs-2 flex-shrink-0"></i>
            <div class="flex-fill">
                <h5 class="card-title mb-0"><?= $student->getFirstName() . " " . $student->getLastName() ?></h5>
                <p class="card-text mb-0"><?= $student->getGroupStudent()->getLabel() ?></p>
                <p class="card-text mb-0">Numéro étudiant: <?= $student->getStudentNumber() ?></p>
            </div>
            <!-- Boutons desktop : à droite côte à côte -->
            <div class="d-none d-md-flex gap-2 ms-3 flex-shrink-0">
                <a href="/statistique-etudiant/<?= $student->getIdAccount() ?>" class="btn btn-success btn-no-border">Voir les statistiques</a>
                <a href="/StudentProfile/<?= $student->getIdAccount() ?>" class="btn btn-uphf">Voir le profil</a>
            </div>
        </div>

        <!-- Boutons mobile : en dessous, pleine largeur -->
        <div class="d-flex d-md-none gap-2 mt-2">
            <a href="/statistique-etudiant/<?= $student->getIdAccount() ?>" class="btn btn-success btn-no-border flex-fill">Voir les statistiques</a>
            <a href="/StudentProfile/<?= $student->getIdAccount() ?>" class="btn btn-uphf flex-fill">Voir le profil</a>
        </div>

    </div>
</div>
