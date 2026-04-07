<?php
/**
 * View de la page de gestion des commentaires prédéfinis
 */

use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script type="module" src="/js/pages/predefinedComments.js"></script>');
?>

<div class="card p-3 p-md-4 flex-fill d-flex flex-column" style="min-height:0">

    <!-- Formulaire d'ajout -->
    <div class="mb-4">
        <h5 class="mb-3">Ajouter un commentaire</h5>
        <div class="input-group">
            <input type="text"
                   id="newCommentInput"
                   class="form-control"
                   placeholder="Saisissez le texte du commentaire...">
            <button id="addCommentBtn" class="btn btn-uphf">Ajouter</button>
        </div>
    </div>

    <hr class="my-0">

    <!-- Liste des commentaires -->
    <div id="commentContainer"
         class="flex-fill overflow-y-auto mt-3"
         role="status"
         style="min-height:0">
        Chargement de données...
    </div>

</div>
