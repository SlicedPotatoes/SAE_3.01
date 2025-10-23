<?php
require_once "StudentPresentation.php";
class AbsencePresentation
{
    public static function getAbsences($filter)
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
