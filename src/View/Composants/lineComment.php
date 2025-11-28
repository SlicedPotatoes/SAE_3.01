<?php
/**
 * Ligne d'un commentaire prédéfini
 */

global $comment;
?>

<div class="list-group-item">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Affichage du texte du commentaire -->
        <div class="flex-fill me-3" id="comment-text-<?= $comment['idComment'] ?>">
            <p class="mb-0"><?= htmlspecialchars($comment['label'], ENT_QUOTES) ?></p>
        </div>
        <!-- Formulaire de modification -->
        <div class="flex-fill me-3 d-none" id="comment-edit-<?= $comment['idComment'] ?>">
            <form method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="idComment" value="<?= $comment['idComment'] ?>">
                <div class="input-group">
                    <input type="text" name="textComment" class="form-control" value="<?= htmlspecialchars($comment['label'], ENT_QUOTES) ?>" required>
                    <button type="submit" class="btn btn-success" title="Valider">
                        <i class="bi bi-check-lg"></i>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="toggleEdit(<?= $comment['idComment'] ?>)" title="Annuler">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Boutons d'action -->
        <div class="btn-group" id="comment-actions-<?= $comment['idComment'] ?>">
            <!-- Bouton Modifier -->
            <button
                type="button"
                class="btn btn-outline-primary"
                onclick="toggleEdit(<?= $comment['idComment'] ?>)"
                title="Modifier">
                <i class="bi bi-pencil-square"></i>
            </button>

            <!-- Bouton Supprimer -->
            <form method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="idComment" value="<?= $comment['idComment'] ?>">
                <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>