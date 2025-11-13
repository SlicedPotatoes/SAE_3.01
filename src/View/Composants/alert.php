<?php
/**
 * Front end d'une alerte
 */

global $notification;
?>

<div class="alert alert-<?= $notification->getType()->color() ?> d-flex align-items-center alert-dismissible" role="alert">
    <i class="bi <?= $notification->getType()->icon() ?> flex-shrink-0 me-2 text-<?= $notification->getType()->color() ?>"></i>
    <div class="text-<?= $notification->getType()->color() ?>">
        <?= $notification->getMessage() ?>
    </div>
    <button id="notification-<?= $notification->getId() ?>" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>