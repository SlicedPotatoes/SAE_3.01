<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Service\TimeslotService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

class TimeslotControllerApi {

    public static function getTimeslots(): void {
        $filters = [
            'examFilter' => $_GET['examFilter'] ?? null,
            'dateStartFilter' => $_GET['dateStartFilter'] ?? null,
            'dateEndFilter' => $_GET['dateEndFilter'] ?? null
        ];

        if(AuthManager::isRole(AccountType::Teacher)) {
            $filters['idTeacher'] = AuthManager::getAccount()->getIdAccount();
        }
        if(AuthManager::isRole(AccountType::EducationalManager)) {
            $filters['idTeacher'] = null;
            $filters['examFilter'] = true;
        }

        try {
            $timeslots = TimeslotService::getListTimeSlotWithFilter(
                $filters['idTeacher'],
                $filters['examFilter'],
                $filters['dateStartFilter'],
                $filters['dateEndFilter']
            );
            new ResponseApi(
                HttpStatus::OK,
                $timeslots
            )->done();
        } catch (\Exception $e) {
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array) $e->getMessage()
            )->done();
        }

        exit();
    }
}