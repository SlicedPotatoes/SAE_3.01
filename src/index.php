<?php
    // Pour le debug
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $route = [
        "login" => "./View/login.php",
        "dashboard" => "./View/Dashboard/dashboard.php",
    ];
    $title = [
        "login" => "Connexion",
        "dashboard" => "Tableau de bord"
    ];

    $currPage = "dashboard";
    if(isset($_GET['currPage'])) {
        $currPage = $_GET['currPage'];
    }

    session_start();
    //var_dump($_SESSION);

    $role = null;

    if(isset($_SESSION['role'])) { $role = $_SESSION['role']; }
    else { $currPage = "login"; }
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?php
                if(array_key_exists($currPage, $title)) {
                    echo $title[$currPage];
                }
                else {
                    echo "404 Not Found";
                }
            ?>
        </title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="style.css">
    </head>

    <body class="bg-light">
        <?php
            if($role != null) {
                require "./View/buttonSettings.html";
            }
        ?>

        <!-- Contenue de la page -->
        <div class="container mt-4">
            <?php
                if(array_key_exists($currPage, $title) && $role != null) {
                    require "./View/header.php";
                }
            ?>

            <div class="card p-3">
                <?php
                    if(array_key_exists($currPage, $route)) {
                        require $route[$currPage];
                    }
                    else {
                        require "./View/404.html";
                    }
                ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Script pour activer les tooltips de bootstrap
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        </script>
    </body>
</html>