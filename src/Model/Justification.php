<?php

class Justification
{
    private $id;
    private $cause;
    private $processed;
    private $start;
    private $end;
    private $justificationAbsence;
    private $justificationFile;

    public function __construct($id, $cause, $processed, $start, $end, $justificationAbsence, $justificationFile)
    {
        $this->id = $id;
        $this->cause = $cause;
        $this->processd = $processed;
        $this->start = $start;
        $this->end = $end;
        $this->justificationAbsence = $justificationAbsence;
        $this->justificationFile = $justificationFile;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCause()
    {
        return $this->cause;
    }

    public function getProcessed()
    {
        return $this->processed;
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

    public function getJustificationFile()
    {
        return $this->justificationFile;
    }
}