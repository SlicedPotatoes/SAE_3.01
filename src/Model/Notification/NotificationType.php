<?php

namespace Uphf\GestionAbsence\Model\Notification;

/**
 * Énumération représentant les différents types d'alerte géré par la classe Notification
 */
enum NotificationType {
    case Error;
    case Warning;
    case Success;

    /**
     * Récupérer la couleur associée à une énumération
     * @return string
     */
    public function color(): string {
        return match($this) {
            self::Error => 'danger',
            self::Warning => 'warning',
            self::Success => 'success',
        };
    }

    /**
     * Récupérer l'icône associée à une énumération
     * @return string
     */
    public function icon(): string {
        return match($this) {
            self::Error, self::Warning => 'bi-exclamation-triangle-fill',
            self::Success => 'bi-check-circle-fill'
        };
    }

    /**
     * Récupérer l'ensemble des énumérations de type NotificationType
     * @return NotificationType[]
     */
    public static function getAll(): array {
        return [
            self::Error,
            self::Warning,
            self::Success
        ];
    }
}
