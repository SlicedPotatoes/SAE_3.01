<?php

namespace Uphf\GestionAbsence\Model\Entity\Absence;

/**
 * Classe Resource, basé sur la base de données.
 */
class Resource implements \JsonSerializable
{
    private int $idResource;
    private string $label;

    public function __construct(int $idResource, string $label)
    {
        $this->idResource = $idResource;
        $this->label = $label;
    }

    // Getter de base
    public function getIdResource(): int { return $this->idResource; }
    public function getLabel(): string { return $this->label; }

    public function jsonSerialize(): mixed
    {
        return [
            'idResource' => $this->idResource,
            'label' => $this->label,
        ];
    }
}