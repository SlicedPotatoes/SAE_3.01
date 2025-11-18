<?php

namespace Uphf\GestionAbsence\Model;
use DateTime;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;

/**
 * Classe permettant de vérifier si des paramètres passés en GET ou POST sont valides
 */
class CheckValidity {

    /**
     * Méthode interne, permettant de récupérer une valeur fournie par une requête HTTP
     *
     * @param string $method
     * @param string $key
     * @return string|null
     */
    private static function getValue(string $method, string $key): string | null {
        if($method === "POST" && isset($_POST[$key])) { return $_POST[$key]; }
        if($method === "GET" && isset($_GET[$key])) { return $_GET[$key]; }

        return null;
    }

    /**
     * Return vrai si une valeur a été fournis pour une requête HTTP/S de type \$method pour \$key
     *
     * @param string $method
     * @param string $key
     * @return bool
     */
    public static function haveValue(string $method, string $key): bool {
        $value = self::getValue($method, $key);

        return isset($value) && $value != '';
    }

    /**
     * Renvoie vrai si une requête HTTP fournis une date au format \$format
     *
     * @param string $method
     * @param string $key
     * @param string $format
     * @return bool
     */
    public static function isValidDate(string $method, string $key, string $format): bool {
        if(self::haveValue($method, $key)) {
            $value = self::getValue($method, $key);
            $date = DateTime::createFromFormat($format, $value);

            return $date && $date->format($format) === $value;
        }

        return false;
    }

    public static function isValidInt(string $method, string $key): bool {
        if(self::haveValue($method, $key)) {
            $value = self::getValue($method, $key);

            return filter_var($value, FILTER_VALIDATE_INT) !== false;
        }

        return false;
    }

    /**
     * Renvoie vrai si une requête fournis un StateAbsence valide
     *
     * @param string $method
     * @param string $key
     * @return bool
     */
    public static function isValidStateAbsence(string $method, string $key):bool {
        if(self::haveValue($method, $key)) {
            return StateAbs::tryFrom(self::getValue($method, $key)) !== null;
        }

        return false;
    }

    /**
     * Renvoie vrai si une requête fournis un StateJustif valide
     *
     * @param string $method
     * @param string $key
     * @return bool
     */
    public static function isValidStateJustification(string $method, string $key):bool {
        if(self::haveValue($method, $key)) {
            return StateJustif::tryFrom(self::getValue($method, $key)) !== null;
        }

        return false;
    }
}
/*
require_once "../../vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$_POST['test'] = "2025-10-10 08:20:00";
$_POST['test2'] = "abs";
$_POST['test3'] = "2025-50-20 08:30:00";
$_POST['test4'] = "Validated";
$_POST['test5'] = "blabla";
$_POST['test6'] = "42";
$_POST['test7'] = "-42";
$_POST['test8'] = "42.5";

//$val = CheckValidity::isValidDate("POST", "test", "Y-m-d H:i:s");
//$val = CheckValidity::isValidDate("POST", "test2", "Y-m-d H:i:s");
//$val = CheckValidity::isValidDate("POST", "test3", "Y-m-d H:i:s");
//$val = CheckValidity::isValidStateAbsence("POST", "test4");
//$val = CheckValidity::isValidStateAbsence("POST", "test5");
//$val = CheckValidity::isValidInt("POST", "test6");
//$val = CheckValidity::isValidInt("POST", "test7");
//$val = CheckValidity::isValidInt("POST", "test8");
$val = CheckValidity::isValidInt("POST", "test5");


if($val) {
    echo "OK";
}
else {
    echo "NON";
}
*/