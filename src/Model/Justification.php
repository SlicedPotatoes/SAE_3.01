<?php
class Justification
{
    private $id;
    private $cause;
    private $processd;
    private $start;
    private $end;
    private $justificationAbsence;
    public function __construct($id, $cause, $processd, $start, $end, $justificationAbsence){
        $this->id = $id;
        $this->cause = $cause;
        $this->processd = $processd;
        $this->start = $start;
        $this->end = $end;
        $this->justificationAbsence = $justificationAbsence;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCause()
    {
        return $this->cause;
    }

    public function getProcessd()
    {
        return $this->processd;
    }

    public function getStart()
    {
        return $this->start;
    }
    public function getEnd()
    {
        return $this->end;
    }

    public function getJustificationAbsence()
    {
        return $this->justificationAbsence;
    }
}