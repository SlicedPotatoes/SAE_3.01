<?php
/**
 * Modal qui permet de renvoyer vers la page contenant l'explication du fonctionnement du système d'absence du site et du règlement intérieur.
 */

?>

<div class="modal fade" id="ruleModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="logoutModalLabel">Souhaitez-vous découvrir le fonctionnement du site et consulter le règlement intérieur ?</h1>
                <!-- Bouton pour quitter le modal, pour plus de clarité pour l'user -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p>Nous vous proposons de consulter notre documentation qui explique en détail le fonctionnement du système de gestion des absences ainsi que le règlement intérieur de l'établissement, afin que vous soyez bien informé(e) de la façon dont fonctionne le système des absences et des règles à respecter.</p>
                <p>Vous pourrez retrouver cette documentation à tout moment grâce au lien situé en bas de chaque page du site.</p>
            </div>


            <div class="modal-footer">
                <!-- Checkbox pour ne plus afficher le modal -->
                <input type="checkbox" id="dontShowRuleAgain" class="form-check-input me-auto">
                <label for="dontShowRuleAgain" class="form-check-label me-auto">Ne plus afficher ce message</label>

                <!-- Bouton pour fermer le modal -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Fermer
                </button>

                <!-- Bouton pour aller vers la page de documentation -->
                <a href="/src/View/userManual.php" class="btn btn-uphf">
                    Consulter la documentation
                </a>
            </div>
        </div>
    </div>
</div>