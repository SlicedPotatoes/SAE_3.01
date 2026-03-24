<!-- View de la page de connexion -->
<div class="text-center p-4 pb-3">
    <img src="/img/Logo-UPHF.png" class="img-fluid" style="max-width: 280px;" alt="Logo UPHF">
</div>

<!-- Carte de connexion -->
<div class="row mx-auto my-sm-auto rounded overflow-hidden" style="max-width: 850px;">
    <!-- Bloc de gauche blanc -->
    <div class="col-12 col-md-7 border p-4 bg-white">
        <h4 class="text-center mb-4">Identification</h4>

        <form name="login" method="post">
            <div class="mb-3">
                <label class="mb-0 form-label" for="email" id="emailLabel">Adresse e-mail :</label>
                <input type="text" class="form-control opacity-75" id="email" name="email" placeholder="" required>
            </div>

            <div class="mb-3">
                <label class="mb-0 form-label" for="password" id="passwordLabel">Mot de passe :</label>

                <!-- div pour avoir un bouton show/hide password -->
                <div class="input-group">
                    <input type="password" class="form-control opacity-75" id="password" name="password" placeholder="********" required>
                    <button type="button" class="btn btn-outline-secondary bi bi-eye-slash" id="togglePassword"></button>
                </div>
            </div>

            <!-- Lien mot de passe oublié sous le champ (mobile uniquement) -->
            <div class="d-flex justify-content-end d-md-none mb-1">
                <a href="/mot-de-passe-oublie" class="small text-uphf">Mot de passe oublié</a>
            </div>

            <!-- Bouton : pleine largeur sur mobile, centré sur desktop -->
            <div class="d-grid d-md-flex justify-content-md-center mt-4 mb-3">
                <button type="submit" class="btn btn-uphf px-5">Se connecter</button>
            </div>
        </form>
    </div>

    <!-- Bloc de droite avec fond bleu -->
    <div class="col-12 col-md-5 card-uphf d-flex flex-column">

        <!-- Texte de sécurité -->
        <div class="p-4">
            <p class="text-white small">
                Pour des raisons de sécurité, veuillez vous déconnecter et fermer votre navigateur Web une fois que vous avez terminé d'accéder aux services nécessitant une authentification !
            </p>
            <p class="text-white small fw-bold mb-0">
                Vos identifiants sont strictement confidentiels et ne doivent en aucun cas être communiqués à un tiers.
            </p>
        </div>

        <!-- Lien Mot de Passe oublié (desktop uniquement) -->
        <a href="/mot-de-passe-oublie"
           class="d-none d-md-block text-white text-decoration-underline ms-auto mb-3 me-3 mt-auto">
            Mot de passe oublié
        </a>

    </div>

</div>