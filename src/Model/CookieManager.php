<?php

namespace Uphf\GestionAbsence\Model;

/**
 * Classe statique permettant d'accéder facilement dans l'application aux différents éléments contenue dans $_COOKIE
 */
class CookieManager {
    private static bool $cardOpen;
    private static string $lastPath;

    /**
     * Initialisation
     *
     * @return void
     */
    public static function init(): void {
        self::$cardOpen = ($_COOKIE['cardOpen'] ?? 'true') === 'true';
        self::$lastPath = $_COOKIE['lastPath'] ?? '/';

        if(!str_starts_with(self::$lastPath, '/')) {
            self::$lastPath = '/';
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
     * Changer le dernier url visité
     *
     * @param $string
     * @return void
     */
    public static function setLastPath($string): void {
        self::setCookie('lastPath', $string);
    }

    /**
     * Récupérer le dernier url visité
     *
     * @return string
     */
    public static function getLastPath(): string {
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