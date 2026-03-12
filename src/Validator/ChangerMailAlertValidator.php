<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Respect\Validation\Validator as v;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller ChangeMailAlertControllerApi
 */
class ChangerMailAlertValidator {

    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "mailAlertTeacher", avec une valeur booléenne
     * - Une clé "mailAlertEducationManager", avec une valeur booléenne dans le cas ou le compte est de type EducationalManager
     *
     * Dans le cas d'un compte teacher "mailAlertEducationalManager" est défini sur false.
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validationMailAlert(array &$data): void {
        $validator = v::key('mailAlertTeacher', v::boolType());

        if(AuthManager::isRole(AccountType::Teacher)) {
            $data['mailAlertEducationalManager'] = false;
        }
        if(AuthManager::isRole(AccountType::EducationalManager)) {
            $validator = $validator->key('mailAlertEducationalManager', v::boolType());
        }

        $validator->assert($data);
    }
}