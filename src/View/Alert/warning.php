<?php
global $message;
?>

<div class="alert alert-warning d-flex align-items-center alert-dismissible" role="alert">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 text-warning"></i>
    <div class="text-warning">
        <?= $message ?>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>