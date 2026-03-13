<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Service\GroupService;
use Uphf\GestionAbsence\Service\StatisticService;
use Uphf\GestionAbsence\ViewModel\GeneralStatisticsViewModel;
use Uphf\GestionAbsence\ViewModel\StudentStatisticsViewModel;

/**
 * Controller pour l'affichage des pages sur les statistiques
 */
class StatisticsController {

    /**
     * Méthode pour l'affichage de la page des statistiques générales
     *
     * @return ControllerData
     * @throws EntityNotFoundException
     */
    public static function showGeneralStatistics(): ControllerData {
        $groups = GroupService::selectAllGroup();
        $currTab = ProportionStatisticsType::getAll()[0];

        $satistics = StatisticService::getStatistic([]);

        return new ControllerData(
            "/View/generalStatistics.php",
            "Statistiques",
            new GeneralStatisticsViewModel($satistics,$currTab, $groups, [])
        );
    }

    /**
     * Méthode pour l'affichage des statistiques d'un étudiant
     *
     * @param array $params
     * @return ControllerData
     */
    public static function showStudentStatistics(array $params): ControllerData {
        try {
            $currTab = ProportionStatisticsType::getAll()[0];
            $student = AccountService::getStudentAccount($params['id']);
            $groups = GroupService::selectAllGroup();

            $datas['global'] = StatisticService::getStatistic([]);
            $datas['student'] = StatisticService::getStatistic(['idStudent' => $params['id']]);

            return new ControllerData(
                "/View/studentStatistics.php",
                "Statistiques",
                new StudentStatisticsViewModel($student, $datas, $currTab, $groups, [])
            );
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error,"L'étudiant demandé n'existe pas.");
            return ControllerData::get404();
        }
    }
}