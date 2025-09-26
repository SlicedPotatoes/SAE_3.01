<?php

class Justification
{
    private $id;
    private $cause;
    private $processed;
    private $start;
    private $end;
    private $justificationAbsence;
    private $justificationFile;

    public function __construct($id, $cause, $processed, $start, $end, $justificationAbsence, $justificationFile)
    {
        $this->id = $id;
        $this->cause = $cause;
        $this->processed = $processed;
        $this->start = $start;
        $this->end = $end;
        $this->justificationAbsence = $justificationAbsence;
        $this->justificationFile = $justificationFile;
    }

    public function getId() { return $this->id; }
    public function getCause() { return $this->cause; }
    public function getProcessed() { return $this->processed; }
    public function getStart() { return $this->start; }
    public function getEnd() { return $this->end; }
    public function getJustificationAbsence() { return $this->justificationAbsence; }
    public function getJustificationFile() { return $this->justificationFile; }

    /*
     * TODO: Connecter a la base de données, faire en sorte qu'elle sois paramétrique, avec systeme de filtre / trie (?)
     *
    */
    public static function getJustifications() {
        return array(
            new Justification(
                0,
                "",
                false,
                (new DateTime)->setDate(2025, 9, 24),
                (new DateTime)->setDate(2025, 9, 25),
                "",
                []
            ),
            new Justification(
                1,
                "",
                true,
                (new DateTime)->setDate(2025, 9, 24),
                (new DateTime)->setDate(2025, 9, 25),
                "",
                []
            )
        );
    }

    static public function getJustificationsStudentFiltred($filters = [],$studentId=null, $startDate=null, $endDate=null, $examen=null, $allowedJustification=null, $stateId=null)
    {
        global $connection;
        $parameters = array();


        $query = "SELECT justification.*, absence.*, file.url
              FROM justification
              LEFT JOIN absenceJustification ON justification.idJustification = absenceJustification.idJustification
              LEFT JOIN absence ON absenceJustification.idAbsence = absence.idAbsence
              LEFT JOIN file ON file.idStudentProof = justification.idJustification
              ";


        $hasStudentId = (isset($studentId));
        $hasStartDate = (isset($startDate));
        $hasEndDate = (isset($endDate));
        $hasStateId = (isset($stateId));
        $hasExamen = (isset($examen));


        if ($hasStudentId) {
            $parameters['idStudent'] = $filters['idStudent'];
            $query .= " where idStudent = :studentId";
        }

        if ($hasStartDate) {
            $parameters['startDate'] = $filters['startDate'];
            $query .= " INTERSECT select * from absence where time >= :dateDebut";
        }

        if ($hasEndDate) {
            $parameters['endDate'] = $filters['endDate'];
            $query .= " INTERSECT select * from absence where time <= :dateFin";
        }


        if ($hasExamen) {
            $parameters['examen'] = $filters['examen'];
            $query .= " INTERSECT select * from absence where examen = true";
        }

        if ($allowedJustification) {
            $query .= " INTERSECT select * from absence where allowedJustification = true";
        }

        if ($hasStateId) {
            $parameters['stateId'] = $filters['stateId'];
            $query .= " INTERSECT select * from absence where idstate = :stateId";
        }
        $request = $connection->prepare($query);

        foreach ($parameters as $key => $value) {
            $request->bindValue(':' . $key, $value);
        }

        $request->execute();
        $result = $request->fetchAll();

        var_dump($result);
        echo $query;

        echo "\n proute";
    }

    public function sendJustification($idStudent, $cause, $startDate, $endDate)
    {
        global $connection;
        $query = "INSERT INTO justification(idStudent, cause, start, end, processed) VALUES (:idStudent, :cause, :startDate, :endDate, false) RETURNING idJustification;";

        $row = $connection->prepare($query);
        $row->bindParam('idStudent', $idStudent);
        $row->execute();
        $idJustification = $row->fetchColumn();

        $absences = Absence::getAbsencesStudentFiltred($idStudent, $startDate, $endDate, null, true, null);

        foreach ($absences as $absence)
        {
            $query = "INSERT INTO absenceJustification VALUES(:studentID" .", " .$idJustification .");";
            $statement = $connection->prepare($query);

            $connection->exec($query);
        }

        /*

        TO DO : GESTION DE FICHER DE SES MORTS AVEC LE FAIT QUE ON AJOUTE DANS LA BASE
        DE DONNEE LES LIGNES POUR CHAQUE FICHIERS
        JE NE SAIS PAS SI C'EST MIEUX DE LE FAIRE DIRECTEMENT ICI OU DANS UNE AUTRE FONCTION

        */
    }

}