<?php
// FILE: src/View/changePassword.php
?>
<div class="border p-4 rounded w-50 mx-auto my-auto">
    <div>
        <h5>Pré-requis pour le mot de passe :</h5>
        <!-- Prérequis dynamique (couleur en fonction de si valide ou non) -->
        <ul class="mb-3">
            <li id="req-length">Entre 12 et 30 caractères</li>
            <li id="req-uppercase">Au moins une majuscule</li>
            <li id="req-lowercase">Au moins une minuscule</li>
            <li id="req-digit">Au moins un chiffre</li>
            <li id="req-special">Au moins un caractère spécial (ex: !@#...)</li>
            <li id="req-nospace">Ne doit pas contenir d'espace</li>
            <li id="req-match">Les mots de passe doivent correspondre</li>
        </ul>

        <div id="alertModifMDP" class="card border-0 p-2" style="background: var(--bs-danger-border-subtle)" role="alert">
            Le mot de passe ne respecte pas tous les pré-requis.
        </div>

        <!-- Formulaire pour changer de mdp -->
        <form id="formModifMDP" name="ChangerMotDePasse" method="post" >
            <div class="mb-3">
                <label for="lastPassword" class="form-label">Ancien mot de passe</label>
                <input type="password" class="form-control" id="lastPassword" name="lastPassword" required>
            </div>

            <div class="mb-3">
                <label for="inputNewMDP" class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" id="inputNewMDP" name="newPassword" required>
            </div>

            <div class="mb-3">
                <label for="inputConfirmMDP" class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" class="form-control" id="inputConfirmMDP" name="confirmPassword" required>
            </div>

            <button type="submit" class="btn btn-uphf float-end">Changer</button>
        </form>
    </div>
</div>
<script src="/script/checkPassword.js"></script>
