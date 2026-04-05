<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Service\GroupService;
use Uphf\GestionAbsence\Service\StatisticService;
use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller pour l'affichage des pages sur les statistiques
 */
class StatisticsController {

    /**
     * Méthode pour l'affichage de la page des statistiques générales
     *
     * @return void
     */
    public static function showGeneralStatistics(): void {
        $groups = GroupService::selectAllGroup();

        Renderer::render(
            'generalStatistics.php',
            'Statistiques',
            [
                'groups' => $groups
            ]
        );
    }

    /**
     * Méthode pour l'affichage des statistiques d'un étudiant
     *
     * @param array $params
     * @return void
     */
    public static function showStudentStatistics(array $params): void {
        try {
            $student = AccountService::getStudentAccount($params['id']);
            $groups = GroupService::selectAllGroup();

            Renderer::render(
                'studentStatistics.php',
                'Statistiques',
                [
                    'student' => $student,
                    'groups' => $groups
                ]
            );
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error,"L'étudiant demandé n'existe pas.");
            ErrorController::error404();
        }
    }
}