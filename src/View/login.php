<?php
/**
 * View de la page de connexion
 */

use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
?>

<div class='container w-50 p-5 pb-5 mb-0'>
  <img src="/img/Logo-UPHF.png" class="img-fluid" alt="...">
</div>

<div class="d-flex w-75 m-auto align-content-center mt-0 pt-0">
  <!-- Bloc de gauche blanc -->
  <div class="border p-4 rounded-start w-50 ms-auto bg-white">
      <h4 class="text-center mb-4 ">Identification</h4>

      <form name="login" method="post" >
        <div class="mb-3">
          <label class="mb-0" for="id" class="form-label" id="idLabel">Identifiant :</label>
          <input type="text" class="form-control opacity-75" id="id" name="id" placeholder="" required>
        </div>

        <div class="mb-3">
          <label class="mb-0" for="password" class="form-label" id="passwordLabel">Mot de passe :</label>

          <!-- div pour avoir un bouton show/hide password -->
          <div class="input-group">
            <input type="password" class="form-control opacity-75" id="password" name="password" placeholder="********" required>
            <button type="button" class="btn btn-outline-secondary bi bi-eye-slash" id="togglePassword"></button>
          </div>
        </div>

        <div class="d-flex justify-content-center mt-4 mb-3">
          <button type="submit" class="btn btn-uphf px-5" >Se connecter</button>
        </div>

    </form>
  </div>

  <!-- Bloc de droite avec fond bleu -->
  <div class="rounded-end card-uphf me-auto w-50 d-flex flex-column">

    <!-- Text de sécurité -->
    <div class="p-4">
      <p class="text-white small">
        Pour des raisons de sécurité, veuillez vous déconnecter et fermer votre navigateur Web une fois que vous avez terminé d'accéder aux services nécessitant une authentification !
      </p>
      <p class="text-white small fw-bold mb-0">
        Vos identifiants sont strictement confidentiels et ne doivent en aucun cas être communiqués à un tiers.
      </p>
    </div>

    <!-- Lien Mot de Passe oublié -->
    <a href="/PasswordLost"
       class="text-white text-decoration-underline ms-auto mb-3 me-3 mt-auto">
      Mot de passe oublié
    </a>

  </div>

</div>

<script src="/script/loginScript.js"></script>