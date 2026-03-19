<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller HolidayControllerApi
 */
class HolidayValidator {

    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "startDate", de type string, représentant une date valide au format "YYYY-mm-dd"
     * - Une clé "endDate", de type string, représentant une date valide au format "YYYY-mm-dd"
     * - Une clé "label", de type string, non null
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validationHoliday(array $data): void {
        $validator =
            v::key("startDate", v::date("Y-m-d"))
            ->key("endDate", v::date("Y-m-d"))
            ->key("label", v::stringType()->notEmpty());

        $validator->assert($data);
    }
}