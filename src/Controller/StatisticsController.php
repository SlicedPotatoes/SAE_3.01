<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Database\Select\StudentSelector;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\FilterProportionStatisticsValidator;
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
        $data = [];
        $filters = new FilterProportionStatisticsValidator()->getData();
        $groups = GroupService::selectAllGroup();
        $currTab ??= ProportionStatisticsType::getAll()[0];

        $satistics = StatisticService::getStatistic($data);

        return new ControllerData(
            "/View/generalStatistics.php",
            "Statistiques",
            new GeneralStatisticsViewModel($satistics,$currTab, $groups, $filters)
        );
    }

    /**
     * Méthode pour l'affichage des statistiques d'un étudiant
     *
     * @param array $params
     * @return ControllerData
     * @throws EntityNotFoundException
     */
    public static function showStudentStatistics(array $params): ControllerData {
        $EmptyData = [];
        $datas = [];
        $student = StudentSelector::getStudentById($params['id']);
        $filters = new FilterProportionStatisticsValidator()->getData();
        $groups = GroupService::selectAllGroup();
        $currTab ??= ProportionStatisticsType::getAll()[0];

        try{
            $statisticsStudent = StatisticService::getStatistic((array)$student);
        }catch (EntityNotFoundException $e){
            Notification::addNotification(NotificationType::Error,"L'étudiant demandé n'existe pas.");
            return ControllerData::get404();
        }
        $statisticsGeneral = StatisticService::getStatistic($EmptyData);

        $datas['global'] = $statisticsGeneral;
        $datas['student'] = $statisticsStudent;

        return new ControllerData(
            "/View/studentStatistics.php",
            "Statistiques",
            new StudentStatisticsViewModel($student, $datas, $currTab, $groups, $filters )
        );
    }
}