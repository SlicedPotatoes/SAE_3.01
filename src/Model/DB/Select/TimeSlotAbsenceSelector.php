<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Absence\TimeSlotAbsence;
use Uphf\GestionAbsence\Model\Hydrator\TimeSlotAbsenceHydrator;

class TimeSlotAbsenceSelector
{
    public static function selectTimeSlotAbsence(int|null $idTeacher, bool|null $exam,string|null $dateStart,string|null $dateEnd): array
    {
        $conn = Connection::getInstance();

        $querry = 'select time,idresource,examen,count(*) as countStudentsAbsences,idteacher as idaccount,duration,coursetype,label,lastname,firstname,email,accounttype,groupe
                from absence join public.resource using(idresource)
                join account on absence.idteacher = account.idaccount
                group by time,idresource,examen,idteacher,duration,coursetype,label,lastname,firstname,email,accounttype,groupe
                order by time,idresource,groupe';

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
            $having[] = "time <= cast(:dateEnd as date) + interval '1 day'";
        }

        if (count($having) > 0) {
            $querry .= ' where ' . implode(' and ', $having);
        }

        $sql = $conn->prepare($querry);

        if ($idTeacher !== null) {
            $sql->bindValue(':idTeacher', $idTeacher);
        }
        if($dateStart !== null){
            $sql->bindValue(':dateStart', $dateStart, PDO::PARAM_STR);
        }
        if($dateEnd !== null){
            $sql->bindValue(':dateEnd', $dateEnd, PDO::PARAM_STR);
        }
        $sql->execute();

        $results1 = $sql->fetchAll();
        $results2 = TimeSlotAbsenceSelector::selectCountStudentAbsencesVaditated($idTeacher, $exam,$dateStart,$dateEnd);
        $resultFinal = array();
        var_export($results1);
        var_export($results2);
        $j=0;
        for ($i=0;$i < count($results1);$i++) {
            if(count($results2)-$j!==0 and $results1[$i]['time'] == $results2[$j]['time'] and $results1[$i]['idresource'] == $results2[$j]['idresource'] and $results1[$i]['groupe'] == $results2[$j]['groupe']) {
                $resultFinal[] = TimeSlotAbsenceHydrator::unserializeTimeSlotAbsence($results1[$i],$results2[$j]);
                $j++;
            }else{
                $resultFinal[] = TimeSlotAbsenceHydrator::unserializeTimeSlotAbsence($results1[$i],null);
            }
        }
        return $resultFinal;
    }

    private static function selectCountStudentAbsencesVaditated(int|null $idTeacher, bool|null $exam,string|null $dateStart,string|null $dateEnd): array
    {
        $conn = Connection::getInstance();

        $querry = 'select idresource,time,groupe, count(*) as countStudentsAbsencesJustified from absence where currentstate = :State
                   group by idresource,time,groupe
                   order by time,idresource,groupe';

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
require_once __DIR__ . "/../../../../vendor/autoload.php";
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(dirname(__DIR__,4));
$dotenv->load();

var_export(TimeSlotAbsenceSelector::selectTimeSlotAbsence(null,null,null,null));