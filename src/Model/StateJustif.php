<?php
enum StateJustif: string {
    case Processed = 'Processed';
    case NotProcessed = 'NotProcessed';

    public function label(): string {
        return match($this) {
            self::Processed => 'Traité',
            self::NotProcessed => 'En cour',
        };
    }

    public function colorBadge(): string {
        return match($this) {
            self::Processed => 'success',
            self::NotProcessed => 'secondary'
        };
    }

    public static function getAll(): array {
        return [
            self::Processed,
            self::NotProcessed,
        ];
    }
}