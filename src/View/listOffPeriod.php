<?php
/**
 * View de la page de gestion des périodes de vacances
 */

use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script type="module" src="/js/pages/listOffPeriod.js"></script>');
?>

<div class="card p-3 p-md-4 flex-fill d-flex flex-column" style="min-height:0">

    <!-- Bouton d'ajout -->
    <div class="d-flex justify-content-end mb-3">
        <button type="button"
                class="btn btn-uphf"
                data-bs-toggle="modal"
                data-bs-target="#modalAddOffPeriod">
            <i class="bi bi-plus-lg me-1"></i>Ajouter une période
        </button>
    </div>

    <!-- Liste des périodes -->
    <div id="offPeriodContainer"
         class="flex-fill overflow-y-auto"
         role="status"
         style="min-height:0">
        Chargement de données...
    </div>

</div>

<?php require __DIR__ . "/Component/Modal/modalAddOffPeriod.php"; ?>
