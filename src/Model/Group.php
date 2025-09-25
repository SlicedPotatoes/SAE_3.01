<?php

class Group
{
    private $id;
    private $label;

    function __construct($id, $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }
}