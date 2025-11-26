<?php

use Uphf\GestionAbsence\Model\Entity\Comment\Comment;
global $dataView;
require_once __DIR__ . "/Composants/Headers/commentHeader.php";

$comments = $dataView-> Comment


?>

<div class="card p-3 rounded w-100 ">
    <div class="d-flex justify-content-between ms-auto mb-3">
    </div>
    <?php if (empty($comments)): ?>
        <div>
            <p colspan="5" class="text-center">Aucune commentaire pré-enregistrée.</p>
        </div>
    <?php else: ?>

        <?php foreach ($comments as $comment) : ?>
        <tr>
            <td><?= htmlspecialchars($comment->textComment,ENT_QUOTES) ?></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-primary bi bi-pencil-square me-1"></a>
                    <form id="deletePredefinedComments<?= htmlspecialchars($comment->idComment) ?>" method="post" class="d-inline" onsubmit="return confirm('Voulez-vous supprimer cette période ?'); ">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($comment->idComment) ?>">
                        <input type="hidden" name="action" value="delete">
                    </form>
                    <button form="deleteComments<?= htmlspecialchars($comment->idComment) ?>"  type="submit" class="btn btn-outline-danger bi bi-trash"></button>
                </div>
            </td>
        </tr>

        <?php endforeach; ?>
    <?php endif; ?>

</div>


