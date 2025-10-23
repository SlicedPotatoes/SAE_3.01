<?php
require_once __DIR__ . "/StudentPresentation.php";
require_once __DIR__ . "/../Model/Filter/FilterAbsence.php";

/**
 * Classe AbsencePresentation, permettant de faire la liaison entre Model et View pour tout ce qui concerne les absences
 */
class AbsencePresentation
{
    /**
     * Récupération d'une liste d'absence, en fonction du contexte dans lequel elles sont demandées
     * @param FilterAbsence $filter
     * @return Absence[]
     */
    public static function getAbsences(FilterAbsence $filter): array
    {
        return Absence::getAbsencesStudentFiltered(StudentPresentation::getStudentAccountDashboard()->getIdAccount(), $filter);
    }


}
