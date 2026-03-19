<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Validator as v;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

class TimeslotValidator {
    public static function validationGetTimeslots(array &$data): void {
        $validator = v::key('examFilter', v::boolVal(), false)
            ->key('dateStartFilter', v::date('Y-m-d'), false)
            ->key('dateEndFilter', v::date('Y-m-d'), false);

        $validator->assert($data);

        if(AuthManager::isRole(AccountType::Teacher)) {
            $data['idTeacher'] = AuthManager::getAccount()->getIdAccount();
        }
        if(AuthManager::isRole(AccountType::EducationalManager)) {
            $data['idTeacher'] = null;
            $data['examFilter'] = true;
        }

        foreach (['examFilter', 'dateStartFilter', 'dateEndFilter'] as $filter) {
            if(!isset($data[$filter])) {
                $data[$filter] = null;
            }
        }
    }
}