<?php

use Uphf\GestionAbsence\Model\Entity\Comment\Comment;
global $dataView;
require_once __DIR__ . "/Composants/header.php";

$comments = $dataView->comments;

?>

<div class="card p-4 rounded w-100 mt-4 ">

    <!-- Formule d'ajout -->
    <div class="mb-4">
        <h4 class="mb-3">Ajouter un commentaire prédéfini</h4>
        <form method="post" class ="d-flex gap-2 align-items-start">
            <input type="hidden" name="action" value="add">
            <div class="input-group">
                <input type="text" name="textComment" class="form-control" placeholder="Saisissez le texte du commentaire...">
            </div>
            <button type="submit" class="btn btn-uphf">Ajouter</button>
        </form>
    </div>


    <!-- Liste des commentaires existants -->
    <div class="mt-4">

        <?php if (count($comments) === 0): ?>
          <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <p class='fs-1 text-body-secondary p-3'>Aucun commentaire prédéfini enregistré.</p>
          </div>

        <?php else: ?>
          <h4 class="mb-3">Liste des commentaires prédéfinis</h4>
            <div class="list-group">
                <?php foreach ($comments as $comment): ?>
                    <?php require __DIR__ . "/Composants/lineComment.php"; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="/script/predefinedComments.js"></script>

