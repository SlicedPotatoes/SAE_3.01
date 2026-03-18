<?php
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
?>

<footer class="footer bg-light">
    <div class="container d-flex flex-row flex-wrap justify-content-between align-items-start py-3">
        <div class="footer-row me-3">
            <p class="mb-0">Application interne de l'IUT de Maubeuge<br>
                © <?= date("Y") ?> Université Polytechnique Hauts‑de‑France
            </p>
        </div>

        <?php if (AuthManager::isRole(AccountType::Student) || !AuthManager::isLogin()): ?>

            <div class=" footer-row me-3">
                <a href="/reglement-interieur">Règlement intérieur de l’établissement</a>
            </div>

            <div class="footer-row me-3">
                <a href="/manuel-d-utilisation">Manuel d’utilisation du site</a>
            </div>
        <?php endif; ?>
    </div>

</footer>