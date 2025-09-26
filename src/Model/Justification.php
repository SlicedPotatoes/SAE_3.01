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

    function getJustificationsStudentFiltred ($startDate=null, $endDate=null, $examen=null, $stateId=null)
    {
        global $connection;
        $studentID = $this->getStudentId();

        $parameters = array("studentID" => $studentID);
        $query = "select * from absences where idStudent = :studentID";

        $hasDateDebut = (isset($startDate));
        $hasDateFin = (isset($endDate));
        $hasStateId = (isset($stateId));

        if ($hasDateDebut)
        {
            $parameters["dateDebut"] = $startDate;
            $query .= "INTERSECT select * from absences where date_debut >= :dateDebut";
        }

        if ($hasDateFin)
        {
            $parameters["dateFin"] = $endDate;
            $query .= "INTERSECT select * from absences where date_fin >= :dateFin";
        }

        if ($examen)
        {
            $query .= "INTERSECT select * from absences where examen = true";
        }

        if ($hasStateId)
        {
            $parameters["stateId"] = $stateId;
            $query .= "INTERSECT select * from absences where state_id = :stateId";
        }

        $request = $connection->prepare($query);

        foreach ($parameters as $key => $value)
        {
            $request->bindParam($key, $value);
        }

        $request->execute();
        $result = $request->fetch();
        return $result;
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