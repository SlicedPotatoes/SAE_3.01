<?php
namespace Uphf\GestionAbsence\Model\Entity\Account;

use JsonSerializable;

/**
 * Représente les groupes des étudiants, basé sur la base de données.
 */
class GroupStudent implements JsonSerializable{
    private int $idGroupStudent;
    private string $label;

    function __construct($idGroupStudent, $label) {
        $this->idGroupStudent = $idGroupStudent;
        $this->label = $label;
    }

    // Getter de base
    public function getIdGroupStudent(): int { return $this->idGroupStudent; }
    public function getLabel(): string { return $this->label; }

    public function jsonSerialize(): mixed
    {
        return array(
            "idGroupStudent" => $this->idGroupStudent,
            "label" => $this->label,
        );
    }
}