<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\StudentSelector;
use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;

/**
 * Authentification Controller
 *
 * Gère le login et logout
 */
class AuthentificationController {

    /**
     * Renvoie l'utilisateur vers la page de connexion
     *
     * Si celui-ci est déjà connecté, alors il est renvoyé vers sa page par défault selon son role.
     *
     * Si celui-ci tente de s'authentifier avec des identifiants valide, il est redirigé vers sa page par défault.
     *
     * @return ControllerData
     */
    public static function login(): ControllerData {
        // Si l'utilisateur est connecté, il est redirigé vers sa page par défault
        if(AuthManager::isLogin()) {
            return HomeController::home();
        }

        // Utilisateur tante de s'authentifier
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $accounts = Account::getAllAccount();

            // Récupérer le compte de l'utilisateur
            $account = $accounts[$_POST['id']];
            if($account->getAccountType() === AccountType::Student) {
                $account = StudentSelector::getStudentById($_POST['id']);
            }

            // Connexion au niveau de la session
            AuthManager::login(
                $account->getAccountType(),
                $account
            );

            header("Location: /");
            exit();
        }

        // Utilisateur n'est pas connecté et ne tente pas de s'authentifier, on affiche la view d'authentification
        return new ControllerData(
            "/View/login.php",
            "Connexion",
            new BaseViewModel()
        );
    }

    /**
     * Permet de déconnecter l'utilisateur et le renvoie sur la page de connexion
     * @return void
     */
    public static function logout(): void {
        AuthManager::logout();
        header("Location: /");
        exit();
    }
}