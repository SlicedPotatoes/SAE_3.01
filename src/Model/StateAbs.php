<?php
enum StateAbs: string {
    case Validated = 'Validated';
    case Refused = 'Refused';
    case NotJustified = 'NotJustified';
    case Pending = 'Pending';

    public function label(): string {
        return match($this) {
            self::Validated => 'Validé',
            self::Refused => 'Refusé',
            self::NotJustified => 'Non justifié',
            self::Pending => 'En attente'
        };
    }

    public function colorBadge(): string {
        return match($this) {
            self::Validated => 'success',
            self::Refused, self::NotJustified => 'danger',
            self::Pending => 'secondary'
        };
    }

    public static function getAll(): array {
        return [
          self::Validated,
          self::Refused,
          self::NotJustified,
          self::Pending
        ];
    }
}