<?php

namespace Uphf\GestionAbsence\Model\Entity;

use DateTime;

/**
 * Classe représentant une période de congé
 */
class OffPeriod{
    private int $id;
    private string $periodName;
    private DateTime $startDate;
    private DateTime $endDate;

    public function __construct($id, $periodName, $startDate, $endDate){
        $this->id = $id;
        $this->periodName = $periodName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getPeriodName():string {
        return $this->periodName;
    }

    public function getStartDate():DateTime {
        return $this->startDate;
    }

    public function getEndDate():DateTime {
        return $this->endDate;
    }

}