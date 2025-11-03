<?php
namespace Uphf\GestionAbsence\Model\Filter;

/**
 * Représente un filtre utilisé pour filtrer les etudiants
 */
class FilterStudent {
    private null | string $search;
    private null | string $groupStudent;

    public function __construct($search, $groupStudent) {
        $this->search = $search;
        $this->groupStudent = $groupStudent;
    }

    // Getter basic
    public function getSearch() : null | string { return $this->search; }
    public function getGroupStudent() : null | string { return $this->groupStudent; }
}
