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
use Uphf\GestionAbsence\Utils\Renderer;

class StudentProfileController
{
        public static function showStudentProfile(array $params): void {
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
                $justifications = JustificationService::getJustificationsWithFilters(
                    ['idStudent' => $studentId],
                    [
                        "columns" => ['sendDate'],
                        "sortOrder" => SortOrder::DESC
                    ]
                );

                //TODO: FAIRE UN SERVICE POUR CA:
                // Actuellement c'est des méthodes du model, les déplacés dans BDD
                $studentAbsInfos = [
                    'absTotal' => $student->getAbsTotal(),
                    'halfdaysAbsences' => $student->getHalfdaysAbsences(),
                    'absCanBeJustified' => $student->getAbsCanBeJustified(),
                    'malusPoints' => $student->getMalusPoints(),
                    'malusPointsWithoutPending' => $student->getMalusPointsWithoutPending(),
                    'penalizingAbsence' => $student->getPenalizingAbsence(),
                    'halfdayPenalizingAbsence' => $student->getHalfdayPenalizingAbsence()
                ];

                Renderer::render(
                    'studentProfile.php',
                    'Profil étudiant',
                    [
                        'student' => $student,
                        'absences' => $absences,
                        'justifications' => $justifications,
                        'studentAbsInfos' => $studentAbsInfos
                    ]
                );
            }
            catch (EntityNotFoundException $e){
                Notification::addNotification(NotificationType::Error, "L'étudiant demandé n'existe pas");
                Renderer::render404();
            }
        }
}