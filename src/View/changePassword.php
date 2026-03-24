<div class="container-fluid scroll-parent d-md-flex align-items-md-center justify-content-center pb-4 mt-3 main-content-wrapper">
    <div class="row justify-content-center g-0 shadow-sm w-100 rounded overflow-hidden">

        <div class="col-12 col-lg-7 bg-white p-3 scrollable">
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

                <div id="alertModifMDP" class="card border-0 p-2 mb-3 small" style="background: var(--bs-danger-border-subtle)" role="alert">
                    Le mot de passe ne respecte pas tous les prérequis.
                </div>

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
</div>

<script src="/script/checkPassword.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formModifMDP');
        if (!form) return;

        const inputs = Array.from(form.querySelectorAll('input[type="password"]'));

        // Reprise exacte de ta logique de style d'origine
        function applyBorderStyle(el, cssClass, color) {
            try {
                el.classList.remove('border-secondary', 'border-info', 'border-uphf');
                if (cssClass) el.classList.add(cssClass);
                el.style.border = '2px solid ' + color;
            } catch (e) {
                console.error('[MDP] erreur applyBorderStyle', e);
            }
        }

        function setGrey(el) { applyBorderStyle(el, 'border-secondary', '#6c757d'); }
        function setInfo(el) { applyBorderStyle(el, 'border-info', '#0dcaf0'); }
        function setFinal(el) { applyBorderStyle(el, 'border-uphf', '#0d6efd'); }

        inputs.forEach(input => {
            // État initial
            if (!input.value) setGrey(input);
            else setFinal(input);

            input.addEventListener('input', function () {
                this.value ? setInfo(this) : setGrey(this);
            });

            input.addEventListener('blur', function () {
                this.value ? setFinal(this) : setGrey(this);
            });
        });
    });
</script>
