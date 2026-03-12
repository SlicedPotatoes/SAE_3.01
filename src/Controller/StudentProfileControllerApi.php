<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Service\AbsenceService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

/**
 * Controller api pour le profil étudiant
 */
class StudentProfileControllerApi
{
    /**
     * Récupérer les absences d'un étudiant avec des filtres
     *
     * @return void
     */
    public static function getAbsences(): void
    {
        if (AuthManager::isRole(AccountType::EducationalManager) && isset($_GET['idStudent'])) {
            $idStudent = (int) $_GET['idStudent'];
        } elseif (AuthManager::isRole(AccountType::Student)) {
            $idStudent = AuthManager::getAccount()->getIdAccount();
        } else {
            new ResponseApi(HttpStatus::FORBIDDEN)->done();
        }

        if(isset($_GET['state']) && StateAbs::tryFrom($_GET['state']) !== null) {
            $_GET['state'] = StateAbs::from($_GET['state']);
        }


        $absences = AbsenceService::absenceSelectService($idStudent, $_GET);

        new ResponseApi(HttpStatus::OK, $absences)->done();
    }
}