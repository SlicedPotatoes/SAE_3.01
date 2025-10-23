<?php
require_once "StudentPresentation.php";

/**
 * Classe JustificationPresentation, permettant de faire la liaison entre Model et View pour tout ce qui concerne les Justificatifs
 */
class JustificationPresentation
{
    /**
     * Récupération d'une liste de justificatifs, en fonction du contexte dans lequel ils sont demandées
     * @param $filter
     * @return Justification[]
     */
    public static function getJustifications($filter): array
    {
        return Justification::selectJustification
        (
                        StudentPresentation::getStudentAccountDashboard()->getIdAccount(),
                        $filter['proof']['DateStart'],
                        $filter['proof']['DateEnd'],
                        $filter['proof']['State'],
                        $filter['proof']['Exam']
        );
    }
}