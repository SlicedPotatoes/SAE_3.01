<?php

use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script type="module" src="/js/pages/changePassword.js"></script>');
?>

<div class="row justify-content-center my-auto">
    <div class="col-12 col-lg-7 card p-3 rounded-end-0 flex-fill">
        <div class="p-4">
            <h5 class="mb-3">Prérequis pour le mot de passe :</h5>

            <ul class="mb-3 small">
                <li id="req-length">Entre 12 et 30 caractères</li>
                <li id="req-uppercase">Au moins une majuscule</li>
                <li id="req-lowercase">Au moins une minuscule</li>
                <li id="req-digit">Au moins un chiffre</li>
                <li id="req-special">Au moins un caractère spécial (ex: !@#...)</li>
                <li id="req-nospace">Ne doit pas contenir d'espace</li>
                <li id="req-match">Les mots de passe doivent correspondre</li>
            </ul>

            <form id="formModifMDP" name="ChangerMotDePasse" method="post">
                <?php if (isset($data['haveToken']) && !$data['haveToken']): ?>
                    <div class="mb-3">
                        <label for="lastPassword" class="form-label">Ancien mot de passe</label>
                        <input type="password" class="form-control border-secondary" id="lastPassword" name="lastPassword" required>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="inputNewMDP" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control border-secondary" id="inputNewMDP" name="newPassword" required>
                </div>

                <div class="mb-3">
                    <label for="inputConfirmMDP" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control border-secondary" id="inputConfirmMDP" name="confirmPassword" required>
                </div>

                <div class="d-flex flex-row gap-2 justify-content-between mt-4">
                    <a class="btn btn-secondary" href="/">Retour</a>
                    <button type="submit" class="btn btn-uphf px-4">Changer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-none d-lg-block col-lg-4 card-uphf rounded-end"></div>
</div>
