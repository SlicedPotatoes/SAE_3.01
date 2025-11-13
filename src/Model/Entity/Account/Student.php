<?php
namespace Uphf\GestionAbsence\Model\Entity\Account;

use Uphf\GestionAbsence\Model\DB\Select\StudentSelector;
use Uphf\GestionAbsence\Model\Hydrator\AccountHydrator;

/**
 * Classe Student, basé sur la BDD
 */
class Student extends Account {
    // Attribut de base de la classe
    private int $studentNumber;
    private GroupStudent $groupStudent;

    // Attribut métrique, si null pas encore calculé
    private NULL | int $absTotal = null;
    private NULL | int $absCanBeJustified = null;
    private NULL | int $halfdaysAbsences = null;
    private NULL | float $malusPoints = null;
    private NULL | float $malusPointsWithoutPending = null;

    private NULL | int $absPenalizing = null;
    private NULL | int $halfdayPenalizing = null;

    // Constante de la classe : évitement des valeurs hasardeuses
    public const MALUS_THRESHOLD = 5; // Utilisé pour la limite d'affichage du malus
    public const MALUS_POINTS = 0.1; // Utilisé pour la multiplication du malus


    public function __construct(
        int $idAccount,
        string $lastName,
        string $firstName,
        string $email,
        AccountType $accountType,
        int $studentNumber,
        GroupStudent $groupStudent
    ) {
        parent::__construct($idAccount, $lastName, $firstName, $email, $accountType);
        $this->studentNumber = $studentNumber;
        $this->groupStudent = $groupStudent;
    }

    /**
     * Serialization
     * Utilisé quand on met un objet dans $_SESSION
     * @return array
     */
    public function __serialize(): array { return AccountHydrator::serializeStudent($this); }

    /**
     * Unserialization
     * Utilisé par session_start pour récupérer un objet stocké dans la session
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void {
        parent::__unserialize($data);
        $this->studentNumber = $data['studentnumber'];
        $this->groupStudent = new GroupStudent(
            $data["idgroupstudent"],
            $data["groupstudent"]
        );
    }

    // getter basique
    public function getStudentNumber(): int{ return $this->studentNumber; }
    public function getGroupStudent(): GroupStudent { return $this->groupStudent; }

    /**
     * Récupérer le nombre d'absences total d'un étudiant
     * @return int
     */
    public function getAbsTotal(): int
    {
        if ($this->absTotal == null) {
            $this->absTotal = StudentSelector::getAbsTotal($this->idAccount);
        }

        return $this->absTotal;
    }

    /**
     * Récupérer le nombre d'absences pouvant être justifiées (allowedJustification = true)
     * @return int
     */
    public function getAbsCanBeJustified(): int {
        if($this->absCanBeJustified == null) {
            $this->absCanBeJustified = StudentSelector::getAbsCanBeJustified($this->idAccount);
        }

        return $this->absCanBeJustified;
    }

    /**
     * Récupérer le nombre de demi-journées d'absence (matin < 12h30 ; après-midi ≥ 12h30)
     *
     * Peut ainsi comptabiliser deux demi-journées d'absence le même jour.
     * @return int
     */
    public function getHalfdaysAbsences(): int
    {
        if ($this->halfdaysAbsences == null) {
            $this->halfdaysAbsences = StudentSelector::getHalfdaysAbsences($this->idAccount);
        }

        return $this->halfdaysAbsences;
    }

    /**
     * Récupérer le malus cosé par les demi-journées d'absence.
     *
     * Le malus est calculé sur les demi-journées ayant des absences avec les états suivants:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * 0 si malus < seuil, sinon demiJournees * taux
     * @return float
     */
    public function getMalusPoints(): float
    {
        if ($this->malusPoints == null) {
            $this->malusPoints = StudentSelector::getMalusPoints($this->idAccount);
        }

        return $this->malusPoints;
    }

    /**
     * Récupérer le malus cosé par les demi-journées d'absence.
     *
     * Le malus est calculé sur les mêmes états que la méthode getMalusPoints()
     * en excluant l'état Pending.
     *
     * Utilisé pour afficher l'impacte de la validation des absences en attente
     *
     * @return float
     */
    public function getMalusPointsWithoutPending(): float
    {
        if ($this->malusPointsWithoutPending == null) {
            $this->malusPointsWithoutPending = StudentSelector::getMalusPointsWithoutPending($this->idAccount);
        }

        return $this->malusPointsWithoutPending;
    }

    /**
     * Récupérer le nombre d'absences "Pénalisante"
     *
     * Cela inclut toutes les absences avec l'état suivant:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * @return int
     */
    public function getPenalizingAbsence(): int
    {
        if($this->absPenalizing == null) {
            $this->absPenalizing = StudentSelector::getPenalizingAbsence($this->idAccount);
        }

        return $this->absPenalizing;
    }

    /**
     * Récupérer le nombre de demi-journée d'absences "Pénalisante"
     *
     * Cela inclut toutes les absences avec l'état suivant:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * @return int
     */
    public function getHalfdayPenalizingAbsence(): int
    {
        if($this->halfdayPenalizing == null) {
            $this->halfdayPenalizing = StudentSelector::getHalfdayPenalizingAbsence($this->idAccount);
        }

        return $this->halfdayPenalizing;
    }
}