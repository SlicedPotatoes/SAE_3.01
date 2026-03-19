<?php
use Uphf\GestionAbsence\Model\Notification\Notification;

/**
 * Ce composant est responsable de l'affichage de toutes les notifications
 */
?>

<div id="notificationsContainer" class="container mt-3">
    <?php
    $notifications = Notification::getNotifications();
    foreach ($notifications as $notification) {
        require __DIR__ . "/notification.php";
    }
    ?>
</div>