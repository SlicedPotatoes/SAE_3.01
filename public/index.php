<?php
/**
 * Point d'entrée de l'application
 *
 * Gére l'affichage de la page en fonction de l'état de l'application
 */

require_once __DIR__ . "/../vendor/autoload.php";

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\GlobalVariable;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Router;

if(!GlobalVariable::PROD()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

AuthManager::init();

// Création des routes
$router = new Router();
$router->addRoute("/", "HomeController@home");
$router->addRoute("/login", "AuthentificationController@login");
$router->addRoute("/logout", "AuthentificationController@logout");
$router->addRoute("/StudentProfile", "StudentProfileController@show");
$router->addRoute("/StudentProfile/{id}", "StudentProfileController@show");
$router->addRoute("/JustificationList", "JustificationsListController@show");
$router->addRoute("/SearchStudent", "SearchStudentController@show");
$router->addRoute("/DetailJustification/{id}", "DetailJustificationController@show");
$router->addRoute("/ShowFile/{}", "FileController@show");

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

//echo $path;

$dataRoute = $router->launch($path);
$dataView = $dataRoute->data;
$srcFolder = __DIR__ . '/../src';

Connection::close();
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= $dataRoute->title ?>
    </title>

    <link rel="stylesheet" href="/style/bootstrap.min.css">
    <link rel="stylesheet" href="/style/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/style/style.css">
</head>

<body class="bg-light d-flex flex-column m-0">
<?php
// Si l'utilisateur est connecté, afficher le bouton d'option
if(AuthManager::isLogin()) {
    require $srcFolder . "/View/Composants/buttonSettings.php";

    if (AuthManager::isRole(AccountType::EducationalManager)) {
        require $srcFolder . "/View/Composants/burgerMenu.php";
    }
}
?>
<div id="notificationsContainer" class="container mt-3">
    <?php
    // Gestion des messages de "notification"
    $notifications = Notification::getNotifications();
    foreach ($notifications as $notification) {
        require $srcFolder . "/View/Composants/alert.php";
    }
    ?>
</div>
<!-- Contenue de la page -->
<div class="container d-flex flex-column gap-3 flex-fill" style="min-height: 0">
    <?php
    require_once $srcFolder . $dataRoute->view;
    ?>
</div>

<footer class="p-3">
    Le footer
</footer>

<script src="/script/bootstrap.bundle.min.js"></script>
<script src="/script/alert.js"></script>
<script src="/script/tooltip.js"></script>
</body>
</html>