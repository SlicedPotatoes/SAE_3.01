<?php
namespace Uphf\GestionAbsence\Model\Justification;

/**
 * Enumération représentant les différents états des justificatifs
 */
enum StateJustif: string {
    case Processed = 'Processed';
    case NotProcessed = 'NotProcessed';

    /**
     * Récupérer le label associé à l'énumération
     * @return string
     */
    public function label(): string {
        return match($this) {
            self::Processed => 'Traité',
            self::NotProcessed => 'En cours',
        };
    }

    /**
     * Récupérer la catégorie de couleur associée à l'énumération
     * @return string
     */
    public function colorBadge(): string {
        return match($this) {
            self::Processed => 'success',
            self::NotProcessed => 'secondary'
        };
    }

    /**
     * Récupérer l'ensemble des énumérations de type StateJustif
     * @return StateJustif[]
     */
    public static function getAll(): array {
        return [
            self::Processed,
            self::NotProcessed,
        ];
    }
}