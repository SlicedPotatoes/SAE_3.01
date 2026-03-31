<?php

namespace Uphf\GestionAbsence\Controller;

use DateTime;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Service\TimeslotService;
use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller pour les timeslots d'absence, notamment pour afficher les détails d'un timeslot et pour afficher la liste des sessions de rattrapage.
 */
class TimeslotController
{
    /**
     * Affiche les détails d'un timeslot d'absence à partir de la date, de l'id de la ressource, de l'id du professeur et éventuellement du groupe. Si aucun timeslot n'est trouvé, une page 404 est retournée.
     *
     * @param array $params
     * @return void
     */
    public static function showDetailTimeslot(array $params): void
    {
        $time = DateTime::createFromFormat('Y-m-d H:i:s', $params['datetime']);
        $resourceId = (int)$params['idRessource'];
        $teacherId = (int)$params['idTeacher'];
        $group = ($params['group'] === 'nogroup' || $params['group'] === null) ? null : urldecode($params['group']);

        try {
            $timeslot = TimeslotService::getTimeSlot($time, $resourceId, $teacherId, $group);
        } catch (EntityNotFoundException $e) {
            ErrorController::error404();
            return;
        }

        $absences = TimeslotService::getListAbsenceWithTimeSlot($timeslot);
        Renderer::render(
            '../ViewOLD/detailPeriod.php',
            'Détail crénaux',
            [
                'absences' => $absences,
                'timeslot' => $timeslot,
            ]
        );
    }

    /**
     * Affiche la liste des sessions de rattrapage.
     *
     * @return void
     */
    public static function showResitSession(): void
    {
        $timeslots = TimeslotService::getListTimeSlotWithFilter(
            null,
            true,
            null,
            null
        );

        Renderer::render(
            '../ViewOLD/resitSessionList.php',
            'Rattrapage',
            ['timeslots' => $timeslots]
        );
    }

    /**
     * Affiche le tableau de bord du professeur.
     *
     * @return void
     */
    public static function showTeacherHome(): void {
        $filters = [
            'examFilter' => null,
            'dateStartFilter' => null,
            'dateEndFilter' => null
        ];

        $account = AuthManager::getAccount();
        $timeslots = TimeslotService::getListTimeSlotWithFilter(
            $account->getIdAccount(),
            $filters['examFilter'],
            $filters['dateStartFilter'],
            $filters['dateEndFilter']
        );

        Renderer::render(
            '/teacherDashboard.php',
            "Tableau de bord Professeur",
            ['timeslots' => $timeslots]
        );
    }
}