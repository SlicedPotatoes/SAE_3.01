<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

class TimeSlotAbsenceSelector
{
    public static function selectTimeSlotAbsence(int|null $idTeacher, bool|null $exam,string|null $dateStart,string|null $dateEnd  )
    {
        $conn = Connection::getInstance();

        $querry = 'select time,idresource,examen,count(*) as countStudentAbsences,idteacher,duration,coursetype,label,lastname,firstname,email,accounttype,groupe
                from absence join public.resource using(idresource)
                join account on absence.idteacher = account.idaccount
                group by time,idresource,examen,idteacher,duration,coursetype,label,lastname,firstname,email,accounttype,groupe';

        $having = array();

        if ($exam) {
            $having[] = 'examen';
        }
        if ($idTeacher !== null) {
            $having[] = 'idteacher = :idTeacher';
        }

        if($dateStart !== null){
            $having[] = 'time >= :dateStart';
        }

        if($dateEnd !== null){
            $having[] = "time <= cast(:endDate as date) + interval '1 day'";
        }

        if (count($having) > 0) {
            $querry .= ' where ' . implode(' and ', $having);
        }

        $sql = $conn->prepare($querry);

        if ($idTeacher !== null) {
            $sql->bindValue(':idTeacher', $idTeacher);
        }
        $sql->execute();

        $results1 = $sql->fetchAll();
        $results2 = TimeSlotAbsenceSelector::selectCountStudentAbsencesVaditated($idTeacher, $exam);


        foreach ($results1 as $result) {

        }
    }

    private static function selectCountStudentAbsencesVaditated(int|null $idTeacher, bool|null $exam)
    {
        $conn = Connection::getInstance();

        $querry = 'select idresource,time,groupe, count(*) as countStudentsAbsencesJustified from absence where currentstate = :State
                   group by idresource,time,groupe';

        $having = array();

        if ($exam) {
            $having[] = 'examen';
        }
        if ($idTeacher !== null) {
            $having[] = 'idteacher = :idTeacher';
        }

        if (count($having) > 0) {
            $querry .= ' where ' . implode(' and ', $having);
        }

        $sql = $conn->prepare($querry);

        if ($idTeacher !== null) {
            $sql->bindValue(':idTeacher', $idTeacher);
        }
        $curState =StateAbs::Validated->value;
        $sql->bindParam(':State', $curState);
        $sql->execute();

        return $sql->fetchAll();
    }
}
