<?php

namespace Uphf\GestionAbsence\Model;

/**
 * Classe statique permettant d'accéder facilement dans l'application aux différents éléments contenue dans $_COOKIE
 */
class CookieManager {
    private static bool $cardOpen;
    private static string $lastPath;
    private static string $lastPath2;
    private static bool $hideRuleModal = false;

    /**
     * Initialisation
     *
     * @return void
     */
    public static function init(): void {
        self::$cardOpen = ($_COOKIE['cardOpen'] ?? 'true') === 'true';
        self::$lastPath = $_COOKIE['lastPath'] ?? '/';
        self::$lastPath2 = $_COOKIE['lastPath2'] ?? '/';
        self::$hideRuleModal = (isset($_COOKIE['hideRuleModal']) && $_COOKIE['hideRuleModal'] === '1');

        if(!str_starts_with(self::$lastPath, '/')) {
            self::$lastPath = '/';
        }
        if(!str_starts_with(self::$lastPath2, '/')) {
            self::$lastPath2 = '/';
        }
    }

    /**
     * Prend une clé et une valeur et l'attribue au cookie
     *
     * @param string $key
     * @param $value
     * @return void
     */
    private static function setCookie(string $key, $value): void {
        $options = [
            'expires' => time() + 24 * 60 * 60,
            'path' => '/',
            'domain' => '',
            'secure' => GlobalVariable::PROD(),
            'httponly' => false,
            'samesite' => 'Lax'
        ];

        setcookie($key, $value, $options);
    }

    /**
     * Définit ou supprime le cookie hideRuleModal via le serveur.
     * @param bool $hide true pour définir le cookie (valeur '1'), false pour le supprimer
     * @return void
     */
    public static function setHideRuleModal(bool $hide): void {
        if ($hide) {
            $options = [
                'expires' => time() + 365 * 24 * 60 * 60,
                'path' => '/',
                'domain' => '',
                'secure' => GlobalVariable::PROD(),
                'httponly' => false,
                'samesite' => 'Lax'
            ];
            setcookie('hideRuleModal', '1', $options);
            self::$hideRuleModal = true;
        } else {
            $options = [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => GlobalVariable::PROD(),
                'httponly' => false,
                'samesite' => 'Lax'
            ];
            setcookie('hideRuleModal', '', $options);
            self::$hideRuleModal = false;
        }
    }

    /**
     * Indique si la modale de règlement doit être masquée (lu depuis le cookie)
     *
     * @return bool
     */
    public static function getHideRuleModal(): bool {
        return self::$hideRuleModal;
    }

    /**
     * Changer le dernier url visité
     *
     * @param $string
     * @return void
     */
    public static function setLastPath($string): void {
        if($string !== self::$lastPath) {
            self::setCookie('lastPath2', self::$lastPath);
            self::setCookie('lastPath', $string);
        }
    }

    /**
     * Récupérer le dernier url visité
     *
     * @return string
     */
    public static function getLastPath(): string {
        if(self::$lastPath == parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)) {
            return htmlspecialchars(self::$lastPath2, ENT_QUOTES);
        }

        return htmlspecialchars(self::$lastPath, ENT_QUOTES);
    }

    /**
     * Récupérer si les cards du profil étudiant sont ouverte
     *
     * @return bool
     */
    public static function getCardOpen(): bool {
        return self::$cardOpen == "true";
    }
}