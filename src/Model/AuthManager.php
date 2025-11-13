<?php

namespace Uphf\GestionAbsence\Model;

use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\Student;

/**
 * Singleton permettant d'accéder facilement dans l'application aux différents éléments contenue dans $_SESSION
 *
 * Gére la connexion et déconnexion dans $_SESSION
 */
class AuthManager {
    private static bool $isLogin;
    private static AccountType $role;
    private static Account $account;

    /**
     * Initialisation du singleton
     *
     * @return void
     */
    public static function init(): void {
        session_start();
        self::loadFromSession();
    }

    private static function loadFromSession(): void {
        if(isset($_SESSION['role'])) {
            self::$isLogin = true;
            self::$role = $_SESSION['role'];
            self::$account = $_SESSION['account'];
        }
        else {
            self::$isLogin = false;
        }
    }

    /**
     * Récupérer si l'utilisateur est connecté ou non
     *
     * @return bool
     */
    public static function isLogin(): bool {
        return self::$isLogin;
    }

    /**
     * Récupérer le type de compte de l'utilisateur connecté
     *
     * @return AccountType
     */
    public static function getRole(): AccountType {
        return self::$role;
    }

    /**
     * Récupérer le compte de l'utilisateur connecté
     *
     * @return Account|Student
     */
    public static function getAccount(): Account|Student {
        return self::$account;
    }

    /**
     * Renvoie true, si l'utilisateur est du role passé en paramètre
     *
     * @param AccountType $role
     * @return bool
     */
    public static function isRole(AccountType $role): bool {
        return self::$isLogin && self::$role === $role;
    }

    /**
     * Remplir $_SESSION quand un utilisateur se connecte
     *
     * @param AccountType $role
     * @param Account $account
     * @return void
     */
    public static function login(AccountType $role, Account $account): void {
        $_SESSION['role'] = $role;
        $_SESSION['account'] = $account;

        self::loadFromSession();
    }

    /**
     * Détruit la session
     *
     * @return void
     */
    public static function logout(): void {
        session_destroy();
    }
}