<?php

/**
 * Représente un filtre utilisé pour filtrer les justificatifs
 */
class FilterAbsence {
    private null | string $dateStart;
    private null | string $dateEnd;
    private null | string $state;
    private bool $examen;
    private bool $locked;

    public function __construct($dateStart, $dateEnd, $state, $examen, $locked) {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->state = $state;
        $this->examen = $examen;
        $this->locked = $locked;
    }

    // Getter basic
    public function getDateStart(): null | string { return $this->dateStart; }
    public function getDateEnd(): null | string { return $this->dateEnd; }
    public function getState(): null | string { return $this->state; }
    public function getExamen(): bool { return $this->examen; }
    public function getLocked(): bool { return $this->locked; }
}
