<?php
namespace Uphf\GestionAbsence\Model\Entity\Justification;

use Uphf\GestionAbsence\Model\DB\Select\JustificationSelector;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use DateTime;


/**
 * Classe Justification, basé sur la base de données.
 */
class Justification
{

    // Attributs de base de la classe
    private int $idJustification;
    private string $cause;
    private StateJustif $currentState;
    private DateTime $startDate;
    private DateTime $endDate;
    private DateTime $sendDate;
    private DateTime|null $processedDate;
    private string|null $refusalReason;
    private array $files;
    private array $absences;
    private Student|null $student;

    private int $idComment;

    public function __construct(
        int $idJustification,
        string $cause,
        StateJustif $currentState,
        DateTime $startDate,
        DateTime $endDate,
        DateTime $sendDate,
        int $idComment,
        DateTime | null $processedDate,
        string | null $refusalReason = null,
        Student | null $student = null,

    ){
        $this->idJustification = $idJustification;
        $this->cause = $cause;
        $this->currentState = $currentState;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->sendDate = $sendDate;
        $this->processedDate = $processedDate;
        $this->refusalReason = $refusalReason;
        $this->student = $student;
        $this->idComment = $idComment;

        $this->files = [];
        $this->absences = [];
    }

    // Getter de base
    public function getIdJustification(): int
    {
        return $this->idJustification;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function getCause(): string
    {
        return $this->cause;
    }

    public function getCurrentState(): StateJustif
    {
        return $this->currentState;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function getSendDate(): DateTime
    {
        return $this->sendDate;
    }

    public function getProcessedDate(): DateTime|null
    {
        return $this->processedDate;
    }

    public function getRefusalReason(): ?string
    {
        return $this->refusalReason;
    }

    public function getIdComment(): int
    {
        return $this->idComment;
    }

    /**
     * Récupérer les fichiers liés à un justificatif
     *
     * Si c'est le premier appel de cette méthode, récupère les données depuis la base
     *
     * @return File[]
     */
    public function getFiles(): array
    {
        if (count($this->files) == 0) {
            $this->files = JustificationSelector::getFiles($this->idJustification);
        }
        return $this->files;
    }

    /**
     * Récupérer les absences liées aux justificatifs
     *
     * Si c'est le premier appel de cette méthode, récupère les données depuis la base
     *
     * @return Absence[]
     */
    public function getAbsences(): array
    {
        if (count($this->absences) == 0) {
            $this->absences = JustificationSelector::getAbsences($this->idJustification);
        }
        return $this->absences;
    }

    public function setRefusalReason($message) { $this->refusalReason = $message; }
    public function setState(StateJustif $state) { $this->currentState = $state; }
    public function setProcessedDate() { $this->processedDate = new DateTime(); }
}