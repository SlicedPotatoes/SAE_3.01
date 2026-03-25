<?php
    use Uphf\GestionAbsence\Model\AuthManager;
    use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
?>

<?php if (AuthManager::isRole(AccountType::EducationalManager) || AuthManager::isRole(AccountType::Secretary)): ?>
<nav class="bg-white navbar navbar-expand-lg border-bottom pt-3">
    <div class="container-fluid">
        <!-- Button settings, gauche sur mobile, droite sur pc -->
        <div class="d-flex order-0 order-lg-2 ms-lg-auto">
            <?php require 'buttonSettings.php'; ?>
        </div>

        <!-- Bouton burger affiché que sur mobile, a droite -->
        <button class="navbar-toggler ms-auto d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Desktop -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarMain">
            <?php require 'elementsMenu.php' ?>
        </div>
    </div>
</nav>
<!-- Menu Mobile -->
<div class="offcanvas offcanvas-end w-100" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <?php require 'elementsMenu.php'; ?>
    </div>
</div>

<?php elseif (AuthManager::isLogin()): ?>
<div class="d-grid align-items-center pt-2 pb-3 bg-white" style="grid-template-columns: minmax(44px, 1fr) minmax(auto, 1320px) minmax(44px, 1fr)">
    <div></div>
    <div class="container m-0" style="justify-self: center">
        <?php if(AuthManager::isRole(AccountType::Student) || AuthManager::isRole(AccountType::Teacher)): ?>
        <p class="h3">
            Bonjour
            <span class='text-uphf fw-bold'> <?= AuthManager::getAccount()->getFirstName() . ' ' . AuthManager::getAccount()->getLastName() ?> </span>
            !
        </p>
        <div class="header-line-brand-color"></div>
        <?php endif; ?>
    </div>
    <div style="justify-self: end">
        <?php require 'buttonSettings.php'; ?>
    </div>
</div>

<?php endif; ?>
