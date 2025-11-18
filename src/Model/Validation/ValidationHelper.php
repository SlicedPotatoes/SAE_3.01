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
}