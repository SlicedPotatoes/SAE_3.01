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
            $having[] = "time <= cast(:dateEnd as date) + interval '1 day'";
        }

        if (count($having) > 0) {
            $querry .= ' having ' . implode(' and ', $having);
        }

        $querry .= ' order by time,idresource,groupe';

        $sql = $conn->prepare($querry);

        if ($idTeacher !== null) {
            $sql->bindParam(':idTeacher', $idTeacher);
        }
        if($dateStart !== null){
            $sql->bindParam(':dateStart', $dateStart, PDO::PARAM_STR);
        }
        if($dateEnd !== null){
            $sql->bindParam(':dateEnd', $dateEnd, PDO::PARAM_STR);
        }
        $sql->execute();

        $results1 = $sql->fetchAll();
        $results2 = TimeSlotAbsenceSelector::selectCountStudentAbsencesVaditated($idTeacher, $exam,$dateStart,$dateEnd);
        $resultFinal = array();
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

    private static function selectCountStudentAbsencesVaditated(?int $idTeacher, ?bool $exam, ?string $dateStart, ?string $dateEnd): array
    {
        $conn = Connection::getInstance();

        // Construire les clauses WHERE (conditions non agrégées)
        $where = [];
        $params = [];

        // État (toujours présent)
        $where[] = 'currentstate = :State';
        $params[':State'] = StateAbs::Validated->value;

        // Filtre examen si demandé
        if ($exam === true) {
            // Test explicite
            $where[] = 'examen = TRUE';
        }

        // Filtre prof si fourni
        if ($idTeacher !== null) {
            $where[] = 'idTeacher = :idTeacher';
            $params[':idTeacher'] = $idTeacher;
        }

        // (optionnel) filtre par date si fourni
        if (!empty($dateStart)) {
            $where[] = 'time >= :dateStart';
            $params[':dateStart'] = $dateStart;
        }
        if (!empty($dateEnd)) {
            $where[] = 'time <= :dateEnd';
            $params[':dateEnd'] = $dateEnd;
        }

        $sqlStr = 'SELECT idresource, time, groupe, COUNT(*) AS countStudentsAbsencesJustified
               FROM absence';

        if (count($where) > 0) {
            $sqlStr .= ' WHERE ' . implode(' AND ', $where);
        }

        $sqlStr .= ' GROUP BY idresource, time, groupe';
        $sqlStr .= ' ORDER BY time, idresource, groupe';

        $sql = $conn->prepare($sqlStr);

        // bindValues (plus simple et moins d'effets de bord que bindParam)
        foreach ($params as $name => $value) {
            // typer correctement : int/str
            if (is_int($value)) {
                $sql->bindValue($name, $value, PDO::PARAM_INT);
            } else {
                $sql->bindValue($name, $value, PDO::PARAM_STR);
            }
        }

        $sql->execute();

        return $sql->fetchAll();
    }
}