<?php

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Utils\Router\Router;

?>

<ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <?php if(AuthManager::isRole(AccountType::EducationalManager)): ?>
        <li class="nav-item">
            <a class="nav-link uphf <?= Router::isCurrRoute('justifications') ? 'active' : '' ?>" href="/justifications">Justificatifs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link uphf <?= Router::isCurrRoute('searchStudent') ? 'active' : '' ?>" href="/rechercher-un-etudiant">Recherche étudiant</a>
        </li>
        <li class="nav-item">
            <a class="nav-link uphf <?= Router::isCurrRoute('generalsStatistics') ? 'active' : '' ?>" href="/statistiques-generales">Statistiques</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">Absences</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item uphf <?= Router::isCurrRoute('timeslots') ? 'active' : '' ?>" href="/absences-a-mes-cours">Absences (mes cours)</a></li>
                <li><a class="dropdown-item uphf <?= Router::isCurrRoute('resitSession') ? 'active' : '' ?>" href="/rattrapage">Rattrapages</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">Administration</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item uphf <?= Router::isCurrRoute('importVT') ? 'active' : '' ?>" href="/televersement">Importer des données</a></li>
                <li><a class="dropdown-item uphf <?= Router::isCurrRoute('holidays') ? 'active' : '' ?>" href="/periode-de-vacances">Période de vacances</a></li>
                <li><a class="dropdown-item uphf" href="#">Routine (démo)</a></li>
            </ul>
        </li>
    <?php elseif(AuthManager::isRole(AccountType::Secretary)): ?>
        <li class="nav-item">
            <a class="nav-link uphf <?= Router::isCurrRoute('importVT') ? 'active' : '' ?>" href="/televersement">Importer des données</a>
        </li>
        <li class="nav-item">
            <a class="nav-link uphf <?= Router::isCurrRoute('resitSession') ? 'active' : '' ?>" href="/rattrapage">Rattrapages</a>
        </li>
        <li class="nav-item">
            <a class="nav-link uphf <?= Router::isCurrRoute('holidays') ? 'active' : '' ?>" href="/periode-de-vacances">Période de vacances</a>
        </li>
    <?php endif; ?>
</ul>