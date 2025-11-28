<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Absence\TimeSlotAbsence;
use Uphf\GestionAbsence\Model\Hydrator\AbsenceHydrator;
use Uphf\GestionAbsence\Model\Hydrator\AccountHydrator;
use Uphf\GestionAbsence\Model\Hydrator\TimeSlotAbsenceHydrator;
use DateTime;

/**
 * Classe static permet de récupérer de créneau de cours ou il y a au moins une
 * absence
 **/
class TimeSlotAbsenceSelector
{

    /**
     * @param
     **/
    public static function selectTimeSlotAbsence(
      int|null $idTeacher,
      bool|null $exam,
      string|null $dateStart,
      string|null $dateEnd
    ): array {
        $conn = Connection::getInstance();

        $querry = 'select time,idresource,examen,count(*) as countStudentsAbsences,idteacher as idaccount,duration,coursetype,label,lastname,firstname,email,accounttype,groupe
                from absence join public.resource using(idresource)
                join account on absence.idteacher = account.idaccount
                group by time,idresource,examen,idteacher,duration,coursetype,label,lastname,firstname,email,accounttype,groupe';

        $having = [];

        if ($exam) {
            $having[] = 'examen';
        }
        if ($idTeacher !== null) {
            $having[] = 'idteacher = :idTeacher';
        }

        if ($dateStart !== null) {
            $having[] = 'time >= :dateStart';
        }

        if ($dateEnd !== null) {
            $having[] = "time <= cast(:dateEnd as date) + interval '1 day'";
        }

        if (count($having) > 0) {
            $querry .= ' having '.implode(' and ', $having);
        }

        $querry .= ' order by time, idresource desc';

        $sql = $conn->prepare($querry);

        if ($idTeacher !== null) {
            $sql->bindParam(':idTeacher', $idTeacher);
        }
        if ($dateStart !== null) {
            $sql->bindParam(':dateStart', $dateStart, PDO::PARAM_STR);
        }
        if ($dateEnd !== null) {
            $sql->bindParam(':dateEnd', $dateEnd, PDO::PARAM_STR);
        }
        $sql->execute();

        $results1 = $sql->fetchAll();
        $results2
          = TimeSlotAbsenceSelector::selectCountStudentAbsencesVaditated(
          $idTeacher,
          $exam,
          $dateStart,
          $dateEnd
        );
        $resultFinal = [];
        $j = 0;
        for ($i = 0; $i < count($results1); $i++) {
            if (count($results2) - $j !== 0 and $results1[$i]['time']
              == $results2[$j]['time'] and $results1[$i]['idresource']
              == $results2[$j]['idresource'] and $results1[$i]['groupe']
              == $results2[$j]['groupe']
            ) {
                $resultFinal[]
                  = TimeSlotAbsenceHydrator::unserializeTimeSlotAbsence(
                  $results1[$i],
                  $results2[$j]
                );
                $j++;
            } else {
                $resultFinal[]
                  = TimeSlotAbsenceHydrator::unserializeTimeSlotAbsence(
                  $results1[$i],
                  null
                );
            }
        }

        return $resultFinal;
    }

    private static function selectCountStudentAbsencesVaditated(
      ?int $idTeacher,
      ?bool $exam,
      ?string $dateStart,
      ?string $dateEnd
    ): array {
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
        if ( ! empty($dateStart)) {
            $where[] = 'time >= :dateStart';
            $params[':dateStart'] = $dateStart;
        }
        if ( ! empty($dateEnd)) {
            $where[] = 'time <= :dateEnd';
            $params[':dateEnd'] = $dateEnd;
        }

        $sqlStr = 'SELECT idresource, time, groupe, COUNT(*) AS countStudentsAbsencesJustified
               FROM absence';

        if (count($where) > 0) {
            $sqlStr .= ' WHERE '.implode(' AND ', $where);
        }

        $sqlStr .= ' GROUP BY idresource, time, groupe';
        $sqlStr .= ' order by time, idresource desc';

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

    public static function getStudentListByTimeSlotAbsence(
      TimeSlotAbsence $timeSlotAbsence,
      StateAbs|null $stateAbs
    ): array {
        $conn = Connection::getInstance();

        $querry = 'select distinct idstudent as studentid, lastname, firstname, email, studentnumber,accounttype,grouplabel, groupid 
                   from account join absence on idstudent = idaccount join student on absence.idstudent = student.idaccount
                   join groupstudent on groupid = idgroupstudent where idresource = :idresource and time = :time';

        if ($stateAbs !== null) {
            $querry .= ' and currentstate = :stateAbs';
        }

        $sql = $conn->prepare($querry);
        $idresource = $timeSlotAbsence->getResource()->getIdResource();
        $sql->bindParam(':idresource', $idresource, PDO::PARAM_INT);
        $time = $timeSlotAbsence->getTime()->format('Y-m-d H:i:s');
        $sql->bindParam(':time', $time, PDO::PARAM_STR);
        $curStateAbs = $stateAbs->value;
        if ($stateAbs !== null) {
            $sql->bindParam(':stateAbs', $curStateAbs, PDO::PARAM_STR);
        }
        $sql->execute();
        $results = $sql->fetchAll();
        $studentList = [];
        foreach ($results as $result) {
            $studentList[] = AccountHydrator::unserializeStudent($result);
        }

        return $studentList;
    }

    public static function getAbsenceListByTimeSlotAbsence(
      TimeSlotAbsence $timeSlotAbsence
    ) {
        $conn = Connection::getInstance();

        $query = 'SELECT idresource,idstudent as studentid,label,time,duration,examen,allowedjustification,currentstate,
                coursetype,studentnumber,lastname,firstname,email,accounttype,groupid,grouplabel
              from absence join student on idstudent = student.idaccount
                join resource using(idresource) join account using (idaccount)
                                   join groupstudent on groupid = idgroupstudent
              WHERE time = :time
                AND idresource = :idresource
                AND idteacher = :idTeacher';

        $sql = $conn->prepare($query);

        $time = $timeSlotAbsence->getTime()->format('Y-m-d H:i:s');
        $sql->bindValue(':time', $time, PDO::PARAM_STR);

        $idresource = $timeSlotAbsence->getResource()->getIdResource();
        $sql->bindValue(':idresource', $idresource, PDO::PARAM_INT);

        $idTeacher = $timeSlotAbsence->getTeacher()->getIdAccount();
        $sql->bindValue(':idTeacher', $idTeacher, PDO::PARAM_INT);

        $sql->execute();

        $results = $sql->fetchAll();
        $absenceList = [];
        foreach ($results as $result) {
            $absenceList[] = AbsenceHydrator::unserializeAbsence($result);
        }

        return $absenceList;
    }

    public static function getTimeSlot(
      DateTime $date,
      int $idResource,
      int $idTeacher,
      ?string $group = null
    ): TimeSlotAbsence | null
    {
        $conn = Connection::getInstance();

        $query = 'select time,idresource,examen,count(*) as countStudentsAbsences,idteacher as idaccount,duration,coursetype,label,lastname,firstname,email,accounttype,groupe
                from absence join public.resource using(idresource)
                join account on absence.idteacher = account.idaccount
                group by time,idresource,examen,idteacher,duration,coursetype,label,lastname,firstname,email,accounttype,groupe';

        $having = [];

        $having[] = 'time = :date';
        $having[] = 'idresource = :idResource';
        $having[] = 'idteacher = :idTeacher';

        if ($group !== null) {
            $having[] = 'groupe = :group';
        } else {
            $having[] = "(groupe IS NULL OR groupe = '')";
        }

        $query .= ' having '.implode(' and ', $having);
        $query .= ' order by time, idresource desc';

        $sql = $conn->prepare($query);

        $dateStr = $date->format('Y-m-d H:i:s');
        $sql->bindValue(':date', $dateStr, PDO::PARAM_STR);
        $sql->bindValue(':idResource', $idResource, PDO::PARAM_INT);
        $sql->bindValue(':idTeacher', $idTeacher, PDO::PARAM_INT);

        if ($group !== null) {
            $sql->bindValue(':group', $group, PDO::PARAM_STR);
        }

        $sql->execute();

        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        return TimeSlotAbsenceHydrator::unserializeTimeSlotAbsence($row, null);
    }
}