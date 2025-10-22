<?php
/*
 * Ce script permet de gérer l'affichage
 * Il choisie quel vue afficher en fonction
 * de l'état de l'application.
 */

    // Pour le debug
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once "./Model/Account.php";
    require_once "./Model/Student.php";
    require_once "./Presentation/globalVariable.php";

    // Définition des routes
    $route = [
        "login" => "./View/login.php",
        "dashboard" => "./View/Dashboard/dashboard.php",
        "searchpage" => "./View/SearchPage/searchPageMain.php"
    ];
    $title = [
        "login" => "Connexion",
        "dashboard" => "Tableau de bord",
        "searchpage" => "Page de recherche"
    ];

    // Valeur par défault, si currPage n'est pas définie
    $currPage = $_GET['currPage'] ?? "dashboard";

    session_start();
    //var_dump($_SESSION);

    $role = null;

    // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion.
    if(isset($_SESSION['role'])) { $role = $_SESSION['role']; }
    else { $currPage = "login"; }
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            <?= array_key_exists($currPage, $title) ? $title[$currPage] : "404 Not Found" ?>
        </title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="style.css">
    </head>

    <body class="bg-light">
        <?php
            // Si l'utilisateur est connecté, afficher le bouton d'option
            if($role != null) {
                require "./View/ButtonSettings.php";
            }
        ?>

        <!-- Contenue de la page -->
        <div class="container mt-4">
            <?php
                if(array_key_exists($currPage, $title) && $role != null) {
                    require "./View/header.php";
                }

                // Gestion des messages de "notification"
                $typeNotifications = [
                    "successMessage",
                    "warningMessage",
                    "errorMessage"
                ];

                foreach ($typeNotifications as $type) {
                    if(isset($_GET[$type])) {
                        foreach ($_GET[$type] as $message) {
                            if($message != "") {
                                require "./View/alert.php";
                            }
                        }
                    }
                }
            ?>

            <div class="card p-3">
                <?php
                    // Afficher le contenue de la page
                    if(array_key_exists($currPage, $route)) { require $route[$currPage]; }
                    else { require "./View/404.html"; }
                ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Script pour initialiser les tooltips dans bootstrap.
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        </script>
    </body>
</html>