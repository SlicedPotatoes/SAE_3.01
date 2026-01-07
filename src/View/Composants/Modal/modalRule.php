<?php
/**
 * Modal qui permet de renvoyer vers la page contenant l'explication du fonctionnement du système d'absence du site et du règlement intérieur.
 */

use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
$hide = CookieManager::getHideRuleModal();

// N'afficher la modale que pour les étudiants
if (!AuthManager::isRole(AccountType::Student)) {
    return;
}

?>

<div class="modal fade" id="ruleModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h1 class="modal-title fs-5" id="ruleModalLabel">Souhaitez-vous découvrir le fonctionnement du site et consulter le règlement intérieur ?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid" style="font-family: var(--bs-body-font-family), sans-serif;">
                    <p class="lead mb-3">Nous vous proposons de consulter notre documentation qui explique en détail le fonctionnement du système de gestion des absences ainsi que le règlement intérieur de l'établissement, afin que vous soyez bien informé(e) de la façon dont fonctionne le système des absences et des règles à respecter.</p>
                    <p class="mb-0 small text-muted">Vous pourrez retrouver cette documentation à tout moment grâce au lien situé en bas de chaque page du site.</p>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-between align-items-center border-0">
                <div>
                    <div class="form-check">
                        <!-- Checkbox pour ne plus afficher le modal -->
                        <input type="checkbox" id="dontShowRuleAgain" class="form-check-input" <?php echo $hide ? 'checked' : ''; ?> />
                        <label for="dontShowRuleAgain" class="form-check-label mb-0 ms-2">Ne plus afficher ce message</label>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center">
                        <!-- Bouton pour fermer le modal -->
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            Fermer
                        </button>

                        <!-- Bouton pour aller vers la page de documentation -->
                        <button onclick="openBoth() " type="button" class="btn btn-primary">
                            Consulter la documentation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire POST caché pour envoyer la préférence au contrôleur StudentProfile -->
<form id="hideRuleForm" method="POST" style="display:none">
    <input type="hidden" name="action" value="setHideRuleModal" />
    <input type="hidden" name="hide" id="hideInput" value="<?php echo $hide ? '1' : '0'; ?>" />
</form>

<script>

    function openBoth() {
        window.open('/rules', '_blank');
        window.location.href = '/userManual';
    }

document.addEventListener('DOMContentLoaded', function(){
    const hideServer = <?php echo $hide ? 'true' : 'false'; ?>;
    const modalEl = document.getElementById('ruleModal');
    if (!modalEl) return;

    // Initialisation du modal
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.warn('Bootstrap modal non disponible');
    } else {
        const modalInstance = new bootstrap.Modal(modalEl);
        if (!hideServer) {
            // afficher la modale automatiquement
            setTimeout(() => modalInstance.show(), 20);
        }
    }

    // Envoie de la préférence via fetch
    async function submitHidePref(val) {
        try {
            const form = document.getElementById('hideRuleForm');
            const fd = new FormData(form);
            fd.set('hide', val ? '1' : '0');

            await fetch(window.location.pathname, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            // pas besoin de retourner la réponse pour notre usage
        } catch (e) {
            console.warn('Échec de l\'envoi de la préférence', e);
        }
    }

    // Quand la checkbox change : envoyer la préférence immédiatement
    const cb = document.getElementById('dontShowRuleAgain');
    const cbLabel = document.querySelector('label[for="dontShowRuleAgain"]');
    if (cb) {
        // Empêcher le clic sur la checkbox/label de fermer la modale
        function stopClick(e){ e.stopPropagation(); }
        cb.addEventListener('click', stopClick, true);
        if (cbLabel) cbLabel.addEventListener('click', stopClick, true);

        cb.addEventListener('change', function(){
            submitHidePref(cb.checked);
        });
    }


});
</script>
