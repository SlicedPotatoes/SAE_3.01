<?php

namespace Uphf\GestionAbsence\Model\Validation;

use DateTime;
use InvalidArgumentException;

/**
 *
 */
class ValidationHelper {
    /**
     * Permet de vérifier dans \$data, si les champs \$requiredFields sont présents
     *
     * Renvoie un tableau d'erreurs, avec les messages défini par \$errorMessages
     *
     * @param array $data
     * @param array $requiredFields
     * @param array $errorMessages
     * @return array
     * @throws InvalidArgumentException Si \$requiredFields n'a pas la même longueur que \$errorMessages
     */
    public static function validateRequired(array $data, array $requiredFields, array $errorMessages): array {
        if(count($requiredFields) != count($errorMessages)) {
            throw new InvalidArgumentException("requiredFields et errorMessage ne contiennent pas le même nombre de valeur");
        }

        $errors = [];

        for($i = 0; $i < count($requiredFields); $i++) {
            $field = $requiredFields[$i];
            if(!array_key_exists($field, $data) || $data[$field] === null) {
                $errors[] = $errorMessages[$i];
            }
        }

        return $errors;
    }

    /**
     * Enlèves les espaces au début et fin de chaine
     *
     * Si la chaine résultante est vide, renvoie null
     *
     * @param string $val
     * @return string|null
     */
    public static function stringOrNull(string $val): string | null {
        $v = trim($val);

        if($v === '') { return null; }

        return $v;
    }

    /**
     * Prend une chaine de caractères
     *
     * Renvoie la chaine si celle-ci respecte le \$format de date
     *
     * Sinon null
     *
     * @param string $val
     * @param string $format
     * @return string|null
     */
    public static function validateDate(string $val, string $format): string | null {
        $date = DateTime::createFromFormat($format, $val);

        if(!$date || $date->format($format) !== $val) {
            return null;
        }

        return $date->format($format);
    }

    /**
     * Prend une chaine de caractères représentant un mot de passe
     *
     * Renvoie null si le mot de passe ne respecte pas les critères de sécurité
     *
     * Un mot de passe est valide si:
     * - Contient entre 12 et 30 caractères
     * - Contient au moins une majuscule
     * - Contient au moins une minuscule
     * - Contient au moins un chiffre
     * - Contient au moins un caractère spécial
     * - Ne contient pas d'espaces
     *
     * @param $val
     * @return string|null
     */
    public static function validatePassword($val): string | null {
        // Vérifier le nombre de caractères
        if(strlen($val) < 12 || strlen($val) > 30) { return null; }

        // Présence d'une majuscule
        if(preg_match("/[A-Z]/", $val) === 0) { return null; }

        // Présence d'une minuscule
        if(preg_match("/[a-z]/", $val) === 0) { return null; }

        // Présence d'un chiffre
        if(preg_match("/[0-9]/", $val) === 0) { return null; }

        // Check la présence d'un espace
        if(preg_match("/\s/", $val) !== 0) { return null; }

        // Check la présence d'un caractère spécial
        if(preg_match("/[^0-9A-Za-zÀ-ÖØ-öø-ÿ]/", $val) === 0) { return null; }

        return $val;
    }

    /**
     * Prend une chaine de caractère
     *
     * Renvoie la chaine si celle-ci respecte le \$format G:i pour un dateTime
     *
     * Sinon null
     *
     * @param $val
     * @return string|null
     */
    public static function valideHours($val): string | null {
        if(!str_contains($val, 'H')) { return null; }

        [$h, $m] = explode("H", $val);

        if(self::validateDate("$h:$m", "G:i") !== null) {
            return $val;
        }

        return null;
    }
}
/*
echo "<pre> 1 - " . ValidationHelper::validatePassword("") . "</pre>";
echo "<pre> 2 - " . ValidationHelper::validatePassword("aaaaaaaaaaaa") . "</pre>";
echo "<pre> 3 - " . ValidationHelper::validatePassword("AAAAAAAAAAAA") . "</pre>";
echo "<pre> 4 - " . ValidationHelper::validatePassword("Aaaaaaaaaaaa") . "</pre>";
echo "<pre> 5 - " . ValidationHelper::validatePassword("Aaaaaaaaaa0 ") . "</pre>";
echo "<pre> 6 - " . ValidationHelper::validatePassword("Aaaaaaaaaa00") . "</pre>";
echo "<pre> 7 - " . ValidationHelper::validatePassword("Aaaaa1aa@aaa") . "</pre>";
*/