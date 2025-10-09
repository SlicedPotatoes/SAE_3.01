<?php
    global $message;
?>

<div class="alert alert-danger d-flex align-items-center alert-dismissible" role="alert">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 text-danger"></i>
    <div class="text-danger">
        <?= $message ?>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>