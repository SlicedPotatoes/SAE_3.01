<?php

namespace Uphf\GestionAbsence\Controller;

use DateTime;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Service\TimeslotService;
use Uphf\GestionAbsence\ViewModel\DetailPeriodViewModel;
use Uphf\GestionAbsence\ViewModel\ResitSessionListViewModel;
use Uphf\GestionAbsence\ViewModel\TeacherHomeViewModel;

/**
 * Controller pour les timeslots d'absence, notamment pour afficher les détails d'un timeslot et pour afficher la liste des sessions de rattrapage.
 */
class TimeslotController
{
    /**
     * Affiche les détails d'un timeslot d'absence à partir de la date, de l'id de la ressource, de l'id du professeur et éventuellement du groupe. Si aucun timeslot n'est trouvé, une page 404 est retournée.
     *
     * @param array $params
     * @return ControllerData
     */
    public static function showDetailTimeslot(array $params): ControllerData
    {
        $time = DateTime::createFromFormat('Y-m-d-H-i', $params['datetime']);
        $resourceId = (int)$params['idRessource'];
        $teacherId = (int)$params['idTeacher'];
        $group = ($params['group'] === 'nogroup' || $params['group'] === null) ? null : urldecode($params['group']);

        try {
            $timeslot = TimeslotService::getTimeSlot($time, $resourceId, $teacherId, $group);
        } catch (EntityNotFoundException $e) {
            return ControllerData::get404();
        }

        $absences = TimeslotService::getListAbsenceWithTimeSlot($timeslot);

        return new ControllerData(
            "/View/detailPeriod.php",
            "Détail crénaux",
            new DetailPeriodViewModel(
                $absences,
                $timeslot->getTime(),
                $timeslot->isExamen(),
                $timeslot->getCourseType(),
                $timeslot->getResource(),
                $timeslot->getGroup(),
                $timeslot->getTeacher()->getLastName() . ", " . $timeslot->getTeacher()->getFirstName(),
                AuthManager::isRole(AccountType::Teacher)
            )
        );
    }

    /**
     * Affiche la liste des sessions de rattrapage.
     *
     * @return ControllerData
     */
    public static function showResitSession(): ControllerData
    {
        $filters = [
            'examFilter' => true,
            'dateStartFilter' => null,
            'dateEndFilter' => null,
        ];

        $timeslots = TimeslotService::getListTimeSlotWithFilter(
            null,
            $filters['examFilter'],
            $filters['dateStartFilter'],
            $filters['dateEndFilter']
        );

        return new ControllerData(
            "/View/resitSessionList.php",
            "Rattrapage",
            new ResitSessionListViewModel(
                $timeslots,
                $filters
            )
        );
    }

    /**
     * Affiche le tableau de bord du professeur.
     *
     * @return ControllerData
     */
    public static function showTeacherHome(): ControllerData {
        $filters = [
            'examFilter' => null,
            'dateStartFilter' => null,
            'dateEndFilter' => null
        ];

        $account = AuthManager::getAccount();
        $timeslot = TimeslotService::getListTimeSlotWithFilter(
            $account->getIdAccount(),
            $filters['examFilter'],
            $filters['dateStartFilter'],
            $filters['dateEndFilter']
        );

        return new ControllerData(
            "/View/teacherHome.php",
            "Tableau de bord Professeur",
            new TeacherHomeViewModel(
                $timeslot,
                $filters,
                AuthManager::getAccount()->getFirstName() . " " . AuthManager::getAccount()->getLastName()
            )
        );
    }
}