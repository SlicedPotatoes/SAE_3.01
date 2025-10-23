<?php
/**
 * Point d'entrée de l'application
 *
 * Gére l'affichage de la page en fonction de l'état de l'application
 */

// Pour le debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "./Model/Account/Account.php";
require_once "./Model/Account/Student.php";
require_once "./Presentation/globalVariable.php";

// Définition des routes
$route = [
        "login" => "./View/login.php",
        "dashboard" => "./View/Dashboard/studentDashboard.php",
        "searchpage" => "./View/SearchPage/searchPageMain.php",
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

    <body class="bg-light d-flex flex-column gap-3 m-0">
        <?php
        // Si l'utilisateur est connecté, afficher le bouton d'option
        if($role != null) {
            require "./View/ButtonSettings.php";
        }
        ?>
        <div class="container">
            <?php
            // Gestion des messages de "notification"
            $typeNotifications = [
                    "errorMessage",
                    "warningMessage",
                    "successMessage"
            ];
            $idNotification = 0;
            foreach ($typeNotifications as $type) {
                if(isset($_GET[$type])) {
                    foreach ($_GET[$type] as $message) {
                        if($message != "") {
                            require "./View/alert.php";
                            $idNotification++;
                        }
                    }
                }
            }
            ?>
        </div>
        <!-- Contenue de la page -->
        <div class="container d-flex flex-column gap-3 flex-fill" style="min-height: 0">
            <?php
                // Afficher le contenu de la page
                if(array_key_exists($currPage, $route)) { require $route[$currPage]; }
                else { require "./View/404.html"; }
            ?>
        </div>

        <footer class="p-3">
            Le footer
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./Script/alert.js"></script>
        <script>
            // Script pour initialiser les tooltips dans bootstrap.
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        </script>
    </body>
</html>