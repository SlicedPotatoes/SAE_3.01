<?php
//Cette de classe représente les ressources qui sont stockées dans la base de données
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