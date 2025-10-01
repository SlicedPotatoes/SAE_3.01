<?php

class GroupStudent {
    private int $idGroupStudent;
    private string $label;

    function __construct($idGroupStudent, $label) {
        $this->idGroupStudent = $idGroupStudent;
        $this->label = $label;
    }

    public function getIdGroupStudent(): int { return $this->idGroupStudent; }
    public function getLabel(): string { return $this->label; }
}