<?php
// Vue de la page d'erreur

use Uphf\GestionAbsence\Model\CookieManager;
global $dataView;
?>

<!-- Page d'erreur -->
<div class="bg-light my-auto">
    <div class="d-flex align-items-center justify-content-center px-2">
        <div class="text-center">
            <h1 class="display-1 fw-bold"><?= $dataView->errorCode ?></h1>
            <p class="fs-2 fw-medium mt-4"><?= $dataView->errorMessage1 ?></p>
            <p class="mt-4 mb-5"><?= $dataView->errorMessage2 ?></p>
            <a href="<?= CookieManager::getLastPath() ?>" class="btn btn-light fw-semibold rounded-pill px-4 py-2 btn-uphf">
                Retour
            </a>
        </div>
    </div>
</div>