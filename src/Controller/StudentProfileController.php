<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AbsenceService;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\ViewModel\StudentProfileViewModel;

class StudentProfileController
{
        public static function showStudentProfile(array $params): ControllerData {
            try {
                if (AuthManager::isRole(AccountType::Student)) {
                    $studentId = AuthManager::getAccount()->getIdAccount();
                    $student = AuthManager::getAccount();
                }
                else {
                    $studentId = $params['id'];
                    $student = AccountService::getStudentAccount($studentId);
                }

                $absences = AbsenceService::absenceSelectService($studentId, []);
                $justification = JustificationService::getJustificationsWithFilters(
                    ['idStudent' => $studentId],
                    [
                        "columns" => ['sendDate'],
                        "sortOrder" => SortOrder::DESC
                    ]
                );

                return new ControllerData(
                    "/View/studentProfile.php",
                    "Profil étudiant",
                    new StudentProfileViewModel(
                        $student,
                        $absences,
                        $justification,
                        $student->getAbsTotal(),
                        $student->getHalfdaysAbsences(),
                        $student->getAbsCanBeJustified(),
                        $student->getMalusPoints(),
                        $student->getMalusPointsWithoutPending(),
                        $student->getPenalizingAbsence(),
                        $student->getHalfdayPenalizingAbsence(),
                        'proof',
                        [],
                        AuthManager::getRole(),
                    )
                );
            }
            catch (EntityNotFoundException $e){
                Notification::addNotification(NotificationType::Error, "L'étudiant demandé n'existe pas");
                return ControllerData::get404();
            }
        }
}