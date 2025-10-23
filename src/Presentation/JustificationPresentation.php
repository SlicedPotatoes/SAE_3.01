<?php
require_once "StudentPresentation.php";

class JustificationPresentation
{
    public static function getJustifications($filter)
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