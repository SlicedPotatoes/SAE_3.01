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
     * @return ControllerData
     * @throws EntityNotFoundException dans le cas où aucun timeslot n'est trouvé pour les critères donnés
     */
    public static function showDetailTimeslot(): ControllerData
    {
        if (
            !isset($_GET['time']) ||
            !isset($_GET['resourceId']) ||
            !isset($_GET['teacher']) ||
            !isset($_GET['group'])
        ) {
            return ControllerData::get404();
        }

        $time = DateTime::createFromFormat('Y-m-d-H-i', $_GET['time']);
        $resourceId = (int)$_GET['resourceId'];
        $teacherId = (int)$_GET['teacher'];
        $group = ($_GET['group'] === 'nogroup') ? null : $_GET['group'];

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
            'dateStartFilter' => $dateStartFilter = null,
            'dateEndFilter' => $dateEndFilter = null,
        ];

        $timeslots = TimeslotService::getListTimeSlotWithFilter(
            null,
            true,
            $dateStartFilter,
            $dateEndFilter
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
     * Renvoi un JSON des sessions de rattrapage trouvé avec les filtres, permet ainsi leurs affichages via Ajax JavaScript.
     *
     * @return void
     */
    public static function getResitSession() : void
    {
        $dateStartFilter = $_GET['dateStartFilter'] ?? null;
        $dateEndFilter = $_GET['dateEndFilter'] ?? null;
        $dateStartFilter = ($dateStartFilter !== '') ? $dateStartFilter : null;
        $dateEndFilter = ($dateEndFilter !== '') ? $dateEndFilter : null;

        $timeSlots = TimeslotService::getListTimeSlotWithFilter(
            null,
            true,
            $dateStartFilter,
            $dateEndFilter
        );

        echo json_encode($timeSlots);
    }

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


    /**
     * Renvoi un JSON des périodes trouvé avec les filtres, permet ainsi leurs affichages via Ajax JavaScript
     *
     * @return void
     */
    public static function getTeacherHome(): void {

        $examFilter      = isset($_GET['examFilter']);
        $dateStartFilter = $_GET['dateStartFilter'] ?? null;
        $dateEndFilter   = $_GET['dateEndFilter'] ?? null;

        $dateStartFilter = ($dateStartFilter !== '') ? $dateStartFilter : null;
        $dateEndFilter   = ($dateEndFilter !== '') ? $dateEndFilter : null;

        $filters = [
            'examFilter' => $examFilter,
            'dateStartFilter' => $dateStartFilter,
            'dateEndFilter' => $dateEndFilter
        ];


        $account = AuthManager::getAccount();
        $timeslot = TimeslotService::getListTimeSlotWithFilter(
            $account->getIdAccount(),
            $filters['examFilter'],
            $filters['dateStartFilter'],
            $filters['dateEndFilter']
        );

        echo json_encode($timeslot);
    }
}