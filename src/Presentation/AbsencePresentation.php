<?php
require_once __DIR__ . "/StudentPresentation.php";

/**
 * Classe AbsencePresentation, permettant de faire la liaison entre Model et View pour tout ce qui concerne les absences
 */
class AbsencePresentation
{
    /**
     * Récupération d'une liste d'absence, en fonction du contexte dans lequel elles sont demandées
     * @param $filter
     * @return Absence[]
     */
    public static function getAbsences($filter): array
    {
        return Absence::getAbsencesStudentFiltered
        (
            StudentPresentation::getStudentAccountDashboard()->getIdAccount(),
            $filter['abs']['DateStart'],
            $filter['abs']['DateEnd'],
            $filter['abs']['Exam'],
            $filter['abs']['Locked'],
            $filter['abs']['State']
        );
    }


}
