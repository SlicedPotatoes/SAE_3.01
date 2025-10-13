<?php
//Cette énumération représente les différents états dans lequel les absences peuvent être
enum StateAbs: string {
    case Validated = 'Validated';
    case Refused = 'Refused';
    case NotJustified = 'NotJustified';
    case Pending = 'Pending';

    //Cette fonction sert à récupérer le label associé à l'énumération
    public function label(): string {
        return match($this) {
            self::Validated => 'Validé',
            self::Refused => 'Refusé',
            self::NotJustified => 'Non justifié',
            self::Pending => 'En attente'
        };
    }
    //Cette fonction sert à récupérer la catégorie de couleur de l'énumération associée
    public function colorBadge(): string {
        return match($this) {
            self::Validated => 'success',
            self::Refused, self::NotJustified => 'danger',
            self::Pending => 'secondary'
        };
    }
    //Cette fonction sert à récupérer toutes les énumérations créées
    public static function getAll(): array {
        return [
          self::Validated,
          self::Refused,
          self::NotJustified,
          self::Pending
        ];
    }
}