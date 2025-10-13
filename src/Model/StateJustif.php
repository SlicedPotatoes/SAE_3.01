<?php
//Cette énumération représente les différents états dans lequel les justificatifs peuvent être
enum StateJustif: string {
    case Processed = 'Processed';
    case NotProcessed = 'NotProcessed';

    //Cette fonction sert à récupérer le label associé à l'énumération
    public function label(): string {
        return match($this) {
            self::Processed => 'Traité',
            self::NotProcessed => 'En cours',
        };
    }
    //Cette fonction sert à récupérer la catégorie de couleur de l'énumération associée
    public function colorBadge(): string {
        return match($this) {
            self::Processed => 'success',
            self::NotProcessed => 'secondary'
        };
    }

    //Cette fonction sert à récupérer toutes les énumérations créées
    public static function getAll(): array {
        return [
            self::Processed,
            self::NotProcessed,
        ];
    }
}