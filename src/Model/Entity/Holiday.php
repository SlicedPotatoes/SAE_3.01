<?php

namespace Uphf\GestionAbsence\Model\Entity;

class Holiday{
    private $id;
    private $periodName;
    private $startDate;
    private $endDate;

    public function __construct($id, $periodName, $startDate, $endDate){
        $this->id = $id;
        $this->periodName = $periodName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getId(){
        return $this->id;
    }

    public function getPeriodName(){
        return $this->periodName;
    }

    public function getStartDate(){
        return $this->startDate;
    }

    public function getEndDate(){
        return $this->endDate;
    }

}