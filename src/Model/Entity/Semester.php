<?php

namespace Uphf\GestionAbsence\Model\Entity;

use DateTime;

/**
 * Classe représentant un semestre
 */
class Semester {
    private int $id;
    private int $idAcademicYear;
    private string $label;
    private DateTime $startDate;
    private DateTime $endDate;

    public function __construct(int $id, int $idAcademicYear, string $label, DateTime $startDate, DateTime $endDate) {
        $this->id = $id;
        $this->idAcademicYear = $idAcademicYear;
        $this->label = $label;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getIdAcademicYear(): int {
        return $this->idAcademicYear;
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function getStartDate(): DateTime {
        return $this->startDate;
    }

    public function getEndDate(): DateTime {
        return $this->endDate;
    }
}
