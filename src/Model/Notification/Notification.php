<?php

namespace Uphf\GestionAbsence\Model\Notification;

use stdClass;

/**
 * Système de notification
 *
 * Utilisé dans le point d'entrée de l'application
 * pour afficher toutes les notifications faite par l'application à l'utilisateur.
 */
class Notification {
    private static array $arr = [];
    private static int $currId = 0;
    private int $id;
    private NotificationType $type;
    private string $message;

    private function __construct(int $id, NotificationType $type, string $message) {
        $this->id = $id;
        $this->type = $type;
        $this->message = $message;
    }

    public function getId(): int { return $this->id; }
    public function getType(): NotificationType { return $this->type; }
    public function getMessage(): string { return $this->message; }

    /**
     * Créer une Notification
     * @param NotificationType $type
     * @param string $message
     * @return void
     */
    public static function addNotification(NotificationType $type, string $message): void {
        self::$arr[] = new Notification(self::$currId++, $type, $message);
    }

    public static function reset(): void {
        self::$arr = [];
    }

    /**
     * Récupérer les notifications
     * @return Notification[]
     */
    public static function getNotifications(): array {
        return self::$arr;
    }

    public function jsonSerialize() {
        $json = new StdClass();
        $json->type = $this->type->name;
        $json->message = $this->message;

        return $json;
    }

    public static function jsonSerializeStudent(): array {
        $result = [];

        foreach (self::$arr as $notification) {
            $result[] = $notification->jsonSerialize();
        }

        return $result;
    }
}