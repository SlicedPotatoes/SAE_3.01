<?php
/**
 * Dernière slide de detail-justification
 *
 * Visible seulement pour le traitement d'un justificatif par le RP
 * Permet au RP de définir un commentaire.
 */
if($isEducationManager && !$isProcessed):
?>
<div class="scroll-parent gap-3 slide scroll-parent" style="display: none!important;">
    <div class="d-flex flex-column">
        <label class="h4 mb-0" for="predefinedComments">Commentaire prédéfini</label>
        <span class="fs-6">Le commentaire sélectionné sera ajouté à la fin de votre saisie, sans écraser le texte existant.</span>
        <select id="predefinedComments" class="form-select" style="max-width: 300px">
            <option value="">Sélectionner un commentaire</option>
            <?php foreach($data['comments'] as $comment): ?>
                <option value="<?= $comment->getTextComment() ?>"><?= $comment->getTextComment() ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="scroll-parent">
        <label class="h4 mb-0" for="comment">Commentaire</label>
        <span id="requiredCommentIndicator" class="fs-6 text-danger d-none">En cas de refus d'au moins une absence, la présence d'un commentaire de votre part est obligatoire.</span>
        <form class="scroll-parent">
            <textarea id="comment" class="form-control scrollable" style="resize: none"></textarea>
            <input id="idJustification" type="hidden" value="<?= $justification->getIdJustification() ?>"/>
        </form>
    </div>

    <!-- actions -->
    <?php
    $currSlide = 2;

    $prevLabel = 'Retour';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 1;

    $nextLabel = 'Valider';
    $nextIcon = 'bi-send-fill';
    $nextAction = 'processJustification';
    $nextParam = '';

    require 'actionsBar.php';
    ?>
</div>
<?php endif; ?>