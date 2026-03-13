<?php

namespace Uphf\GestionAbsence\Model\Entity;

use DateTime;
use JsonSerializable;
use stdClass;

/**
 * Classe représentant une période de congé
 */
class OffPeriod implements JsonSerializable{
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

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'periodName' => $this->periodName,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ];
    }
}