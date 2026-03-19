<?php
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
?>

<footer class="container-fluid bg-white py-3 border-top">
    <div class="row align-items-center gy-1">
        <div class="col-lg-5">
            <p class="mb-0">Application interne de l'IUT de Maubeuge<br>
                © <?= date("Y") ?> Université Polytechnique Hauts‑de‑France
            </p>
        </div>

        <?php if (AuthManager::isRole(AccountType::Student) || !AuthManager::isLogin()): ?>
            <div class="col-md-auto me-3">
                <a href="/reglement-interieur">Règlement intérieur de l’établissement</a>
            </div>

            <div class="col-md-auto me-3">
                <a href="/manuel-d-utilisation">Manuel d’utilisation du site</a>
            </div>
        <?php endif; ?>
    </div>
</footer>