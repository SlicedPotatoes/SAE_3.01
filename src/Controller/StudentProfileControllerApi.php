<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\FileUpload;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Validation\CreateJustificationValidator;
use Uphf\GestionAbsence\Service\AbsenceService;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

/**
 * Controller api pour le profil étudiant
 */
class StudentProfileControllerApi
{
    /**
     * GET /api/absences
     * Récupérer les absences d'un étudiant avec des filtres
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

    /**
     * POST /api/justifications
     * Permet d'ajouter un nouveau justificatif pour l'étudiant connecté
     */
    public static function postJustification(): void {
        $validator = new CreateJustificationValidator();
        $errors = $validator->checkAllGood();

        if(!empty($errors))
        {
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array)$errors
            )->done();
        }

        $data = $validator->getData();
        $files = FileUpload::upload('files');

        try {
            JustificationService::addJustification(
                AuthManager::getAccount()->getIdAccount(),
                $data,
                $files
            );
            new ResponseApi(
                HttpStatus::NO_CONTENT,
            )->done();
        } catch (\Exception $e) {
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array)$e->getMessage()
            )->done();
        }
    }
}