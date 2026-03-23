<?php

use Uphf\GestionAbsence\Utils\Renderer;

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= $title ?>
    </title>

    <link rel="stylesheet" href="/style/bootstrap.min.css">
    <link rel="stylesheet" href="/style/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/style/style.css">
    <?= implode('', Renderer::$assets['head'] ?? []) ?>
</head>
<body class="bg-light d-flex flex-column gap-3">
<?php
require __DIR__ . '/../Component/fullScreenLoader.html';
require __DIR__ . '/../Component/Navbar/navbar.php';
require __DIR__ . "/../Component/Notification/notificationContainer.php";
?>

<div class="container scroll-parent gap-3 px-3 px-md-4 px-lg-5">
    <?= $content ?>
</div>

<?php require __DIR__ . "/../Component/footer.php"; ?>

<script src="/script/bootstrap.bundle.min.js"></script>
<script type="module" src="/js/core/notifications.js"></script>
<script src="/script/tooltip.js"></script>
<?= implode('', Renderer::$assets['script'] ?? []) ?>
</body>
</html>