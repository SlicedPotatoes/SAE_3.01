<?php
?>

<div class="border p-4 rounded  w-50 mx-auto my-auto"  ">



<form name="ChangerMotDePasse" method="post" >

    <div class="mb-3">
        <label for="lastPassword" class="form-label">Ancien Mot de Passe : </label>
        <input type="password" class="form-control" id="id" name="id" required>
    </div>

    <div class="mb-3">
        <label for="NewPassword" class="form-label">Nouveau Mot de passe</label>
        <input type="password" class="form-control" id="id" name="id" required>
    </div>

    <div class="mb-3">
        <label for="ConfirmPassword" class="form-label">Confirmer le mot de passe </label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary float-end" >Changer</button>

</form>
</div>
