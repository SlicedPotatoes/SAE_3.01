<?php
namespace Uphf\GestionAbsence\Model\Filter;


/**
 * Représente un filtre utilisé pour filtrer les justificatifs
 */
class FilterJustification {
    private null | string $dateStart;
    private null | string $dateEnd;
    private null | string $state;
    private bool $examen;
    private bool $asc;

    public function __construct($dateStart, $dateEnd, $state, $examen, $asc = false) {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->state = $state;
        $this->examen = $examen;
        $this->asc = $asc;
    }

    // Getter basic
    public function getDateStart(): null | string { return $this->dateStart; }
    public function getDateEnd(): null | string { return $this->dateEnd; }
    public function getState(): null | string { return $this->state; }
    public function getExamen(): bool { return $this->examen; }
    public function getAsc(): bool { return $this->asc; }
}
