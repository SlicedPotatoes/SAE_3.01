<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller StatisticsControllerApi
 */
class StatisticsValidator {
    /**
     * Renvoie une exception, si les données ne sont pas au format suivant :
     *
     *  - Une clé "group", optionnel, contenant un entier
     *  - Une clé "examen", optionnel, contenant un boolean
     *  - Une clé "state", optionnel, contenant un string qui peut avoir comme valeur : "Validated", "Refused", "NotJustified", "Pendent"
     *  - Une clé "idStudent", optionnel, contenant un entier
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validationGetStatistics(array &$data): void {
        $validator = v::key('group', v::intVal(), false)
            ->key('examen', v::boolType(), false)
            ->key('state', v::in(['Validated', 'Refused', 'NotJustified', 'Pending']), false)
            ->key('idStudent', v::intVal(), false);

        $validator->assert($data);

        // Si présent, convertir le string state en StateAbs
        if(isset($data['state'])) {
            $data['state'] = StateAbs::from($data['state']);
        }
    }
}