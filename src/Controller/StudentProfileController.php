<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\FilterAbsenceValidator;
use Uphf\GestionAbsence\Model\Validation\FilterJustificationValidator;
use Uphf\GestionAbsence\Service\AbsenceService;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\ViewModel\StudentProfileViewModel;

class StudentProfileController
{
        public static function showStudentProfile(array $params)
        {
            if ($params['id'] === null) {
                // Afficher le profil de l'étudiant connecté
                $studentId = AuthManager::getAccount()->getIdAccount();
                $student = AuthManager::getAccount();
            } else {
                // Afficher le profil de l'étudiant spécifié
                $studentId = $params['id'];

                try {
                    $student = AccountService::getStudentAccount($studentId);
                } catch (\Exception $e) {
                    Notification::addNotification(NotificationType::Error, "L'étudiant demandé n'existe pas");
                    return ControllerData::get404();
                }
            }

            $currTab = $_POST['currTab'] ?? 'proof';
            $filters = ($currTab == 'proof' ?
                new FilterJustificationValidator()->getData() :
                new FilterAbsenceValidator()->getData()
            );

            $absences = AbsenceService::absenceSelectService($studentId, []);
            $justification = JustificationService::getJustificationsWithFilters([], $studentId);

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
                    $currTab,
                    $filters,
                    AuthManager::getRole(),
                )
            );
        }
}