<?php
/**
 * Ligne d'un profil étudiant pour la fonction de recherche.
 */

global $students;
?>

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
                        <div class="p-0 flex-fill">
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