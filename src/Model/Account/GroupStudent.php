<?php
/**
 * Représente les groupes des étudiants, basé sur la base de données.
 */
class GroupStudent {
    private int $idGroupStudent;
    private string $label;

    function __construct($idGroupStudent, $label) {
        $this->idGroupStudent = $idGroupStudent;
        $this->label = $label;
    }

    // Getter de base
    public function getIdGroupStudent(): int { return $this->idGroupStudent; }
    public function getLabel(): string { return $this->label; }
}