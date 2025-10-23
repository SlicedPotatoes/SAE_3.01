<?php
/**
 * Front end d'une alerte
 */

    global $message, $type;

    $color = $type == "successMessage" ? "success" : ($type == "warningMessage" ? "warning" : "danger");
    $icon = $type == "successMessage" ? "bi-check-circle-fill" : "bi-exclamation-triangle-fill";
?>

<div class="alert alert-<?= $color ?> d-flex align-items-center alert-dismissible" role="alert">
    <i class="bi <?= $icon ?> flex-shrink-0 me-2 text-<?= $color ?>"></i>
    <div class="text-<?= $color ?>">
        <?= $message ?>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>