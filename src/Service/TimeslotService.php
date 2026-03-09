<?php

namespace Uphf\GestionAbsence\Service;

use DateTime;
use Uphf\GestionAbsence\Database\Select\TimeSlotAbsenceSelector;
use Uphf\GestionAbsence\Model\Entity\Absence\TimeSlotAbsence;

/**
 * Ce service permet de gérer les timeslots d'absence, notamment de les récupérer à partir de la base de données en fonction de différents critères.
 */
class TimeslotService
{
    /**
     * Cette méthode permet de récupérer un timeslot d'absence à partir de la date, de l'id de la ressource, de l'id du professeur et éventuellement du groupe. Si aucun timeslot n'est trouvé, une exception est levée.
     *
     * @param DateTime $date
     * @param int $idResource
     * @param int $idTeacher
     * @param string|null $group
     * @return TimeSlotAbsence
     * @throws \Exception dans le cas où aucun timeslot n'est trouvé pour les critères donnés
     */
    public static function getTimeSlot(DateTime $date, int $idResource, int $idTeacher, ?string $group = null): TimeSlotAbsence
    {
        $timeSlot = TimeSlotAbsenceSelector::getTimeSlot($date, $idResource, $idTeacher, $group);

        if ($timeSlot === null) {
            throw new \Exception("Timeslot not found");
        }

        return $timeSlot;
    }


    /**
     * Cette méthode permet de récupérer une liste de timeslots d'absence à partir de différents critères : l'id du professeur, si le timeslot est un examen ou non, la date de début et la date de fin. Les critères sont optionnels, ce qui signifie que si un critère n'est pas fourni, il ne sera pas pris en compte dans la recherche. La méthode retourne une liste de timeslots d'absence correspondant aux critères donnés.
     *
     * @param int|null $idTeacher
     * @param bool|null $exam
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return array
     */
    public static function getListTimeSlotWithFilter(int|null $idTeacher, bool|null $exam, string|null $dateStart, string|null $dateEnd): array
    {
        return TimeSlotAbsenceSelector::selectTimeSlotAbsence($idTeacher, $exam, $dateStart, $dateEnd);
    }

}