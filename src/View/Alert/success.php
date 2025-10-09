<?php
global $message;
?>

<div class="alert alert-success d-flex align-items-center alert-dismissible" role="alert">
    <i class="bi bi-check-circle-fill flex-shrink-0 me-2 text-success"></i>
    <div class="text-success">
        <?= $message ?>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>