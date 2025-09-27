<?php

class Resource
{
    private int $idResource;
    private string $label;

    public function __construct($idResource, $label)
    {
        $this->idResource = $idResource;
        $this->label = $label;
    }

    public function getIdResource(): int
    {
        return $this->idResource;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

}