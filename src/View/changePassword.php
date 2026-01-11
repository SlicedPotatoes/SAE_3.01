<?php
//Front de la page de changement de mot de passe

require_once __DIR__ . "/Composants/header.php";
?>

<div class="d-flex w-100 my-auto">
    <div class="border p-3 rounded-start w-50 ms-auto bg-white">
        <div>
            <h5>Prérequis pour le mot de passe :</h5>
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
                Le mot de passe ne respecte pas tous les prérequis.
            </div>

            <!-- Formulaire pour changer de mdp -->
            <form id="formModifMDP" name="ChangerMotDePasse" method="post">
                <?php if (!(isset($dataView) && $dataView->haveToken)): ?>
                    <div class="mb-3">
                        <label for="lastPassword" class="form-label">Ancien mot de passe</label>
                        <input type="password" class="form-control border-secondary" id="lastPassword" name="lastPassword" required >
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

                <div class="d-flex flex-row gap-2 justify-content-between">
                    <a class="btn btn-secondary" href="/">Retour</a>
                    <button type="submit" class="btn btn-uphf">Changer</button>
                </div>

            </form>
        </div>
    </div>
    <div class="rounded-end card-uphf me-auto" style="width: 30%"></div>
</div>

<script src="/script/checkPassword.js"></script>

<script>

    // javascript
    document.addEventListener('DOMContentLoaded', function () {
        console.log('[MDP] DOMContentLoaded');

        const form = document.getElementById('formModifMDP');
        console.log('[MDP] form:', form);
        if (!form) {
            console.error('[MDP] formModifMDP introuvable');
            return;
        }

        const inputs = Array.from(form.querySelectorAll('input[type="password"]'));
        console.log('[MDP] inputs trouvés:', inputs.length, inputs);

        function applyBorderStyle(el, cssClass, color) {
            try {
                el.classList.remove('border-secondary', 'border-info', 'border-uphf');
                if (cssClass) el.classList.add(cssClass);
                // force la bordure inline pour debug (évite override CSS)
                el.style.border = '2px solid ' + color;
            } catch (e) {
                console.error('[MDP] erreur applyBorderStyle', e);
            }
        }

        function setGrey(el) { console.log('[MDP] setGrey', el.id); applyBorderStyle(el, 'border-secondary', '#6c757d'); }
        function setInfo(el) { console.log('[MDP] setInfo', el.id); applyBorderStyle(el, 'border-info', '#0dcaf0'); }
        function setFinal(el) { console.log('[MDP] setFinal', el.id); applyBorderStyle(el, 'border-uphf', '#0d6efd'); }

        function updateInitial(el) {
            console.log('[MDP] updateInitial', el.id, 'value:', !!el.value);
            if (!el.value) setGrey(el);
            else setFinal(el);
        }

        inputs.forEach(input => {
            updateInitial(input);

            input.addEventListener('input', function () {
                if (this.value) setInfo(this);
                else setGrey(this);
            });

            input.addEventListener('blur', function () {
                if (this.value) setFinal(this);
                else setGrey(this);
            });

            input.addEventListener('focus', function () {
                console.log('[MDP] focus', this.id);
            });
        });

        // Vérifie si le fichier externe est accessible (utile si tu gardes <script src="...">)
        const externalSrc = '/script/checkPassword.js';
        fetch(externalSrc, { method: 'HEAD' })
            .then(res => {
                console.log('[MDP] HEAD', externalSrc, res.status);
                if (!res.ok) console.warn('[MDP] script externe non trouvé, essaye src="script/checkPassword.js" ou inline');
            })
            .catch(err => console.warn('[MDP] fetch HEAD error (expected si chemin non accessible):', err));
    });

</script>
