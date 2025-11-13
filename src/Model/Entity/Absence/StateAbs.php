<?php

namespace Uphf\GestionAbsence\Model\Entity\Absence;

/**
 * Énumération représentant les différents états d'une absence
 */
enum StateAbs: string {
    case Validated = 'Validated';
    case Refused = 'Refused';
    case NotJustified = 'NotJustified';
    case Pending = 'Pending';

    /**
     * Récupérer le label associé à une énumération
     * @return string
     */
    public function label(): string {
        return match($this) {
            self::Validated => 'Validé',
            self::Refused => 'Refusé',
            self::NotJustified => 'Non justifié',
            self::Pending => 'En attente'
        };
    }

    /**
     * Récupérer la catégorie de couleur associée à une énumération
     * @return string
     */
    public function colorBadge(): string {
        return match($this) {
            self::Validated => 'success',
            self::Refused, self::NotJustified => 'danger',
            self::Pending => 'secondary'
        };
    }

    /**
     * Récupérer l'ensemble des énumérations de type StateAbs
     * @return StateAbs[]
     */
    public static function getAll(): array {
        return [
            self::Validated,
            self::Refused,
            self::NotJustified,
            self::Pending
        ];
    }
}