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
</head>
<body class="bg-light d-flex flex-column m-0">
<?php
require __DIR__ . "/../Component/buttonSettings.php";
require __DIR__ . "/../Component/burgerMenu.php";
require __DIR__ . "/../Component/Notification/notificationContainer.php";
?>

<div class="container d-flex flex-column gap-3 flex-fill" style="min-height: 0">
    <?= $content ?>
</div>

<?php require __DIR__ . "/../Component/footer.php"; ?>

<script src="/script/bootstrap.bundle.min.js"></script>
<script src="/script/alert.js"></script>
<script src="/script/tooltip.js"></script>
</body>
</html>