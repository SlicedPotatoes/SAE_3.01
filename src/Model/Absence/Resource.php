<?php
/**
 * Classe Ressource, basé sur la base de données.
 */
class Resource
{
    private int $idResource;
    private string $label;

    public function __construct($idResource, $label)
    {
        $this->idResource = $idResource;
        $this->label = $label;
    }

    // Getter de base
    public function getIdResource(): int { return $this->idResource; }
    public function getLabel(): string { return $this->label; }

}