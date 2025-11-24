<?php
// FILE: src/View/changePassword.php
?>
<div class="border p-4 rounded w-50 mx-auto my-auto">
    <div>
        <h5>Pré-requis Pour le mot de passe :</h5>
        <ul class="mb-3">
            <li id="req-length" class="text-danger">Entre 12 et 30 caractères</li>
            <li id="req-uppercase" class="text-danger">Au moins une majuscule</li>
            <li id="req-lowercase" class="text-danger">Au moins une minuscule</li>
            <li id="req-digit" class="text-danger">Au moins un chiffre</li>
            <li id="req-special" class="text-danger">Au moins un caractère spécial (ex: !@#...)</li>
            <li id="req-nospace" class="text-danger">Ne doit pas contenir d'espace</li>
            <li id="req-match" class="text-danger">Les mots de passe doivent correspondre</li>
        </ul>

        <div id="alertModifMDP" class="alert alert-danger d-none" role="alert"></div>

        <form id="formModifMDP" name="ChangerMotDePasse" method="post" >
            <div class="mb-3">
                <label for="lastPassword" class="form-label">Ancien Mot de Passe :</label>
                <input type="password" class="form-control" id="lastPassword" name="lastPassword" required>
            </div>

            <div class="mb-3">
                <label for="inputNewMDP" class="form-label">Nouveau Mot de passe</label>
                <input type="password" class="form-control" id="inputNewMDP" name="newPassword" required>
            </div>

            <div class="mb-3">
                <label for="inputConfirmMDP" class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="inputConfirmMDP" name="confirmPassword" required>
            </div>

            <button type="submit" class="btn btn-primary float-end">Changer</button>
        </form>
    </div>

    <script src="/script/verifPrerequiMDP.js"></script>
