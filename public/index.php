<?php
/**
 * Point d'entrée de l'application
 *
 * Gére l'affichage de la page en fonction de l'état de l'application
 */

require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use Uphf\GestionAbsence\Database\Connection;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\GlobalVariable;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Router\Router;
use Uphf\GestionAbsence\Router\RequestMethod;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

if (!GlobalVariable::PROD()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

AuthManager::init();
CookieManager::init();

// Création des routes
$router = new Router();

// HomeController
$router->addRoute(RequestMethod::GET, '/', 'HomeController@home');

// AuthentificationController
$router->addRoute(RequestMethod::GET, '/connexion', 'AuthentificationController@login')
        ->requireNotLogin();
$router->addRoute(RequestMethod::POST, '/connexion', 'AuthentificationController@postLogin')
        ->requireNotLogin();
$router->addRoute(RequestMethod::GET, '/deconnexion', 'AuthentificationController@logout')
        ->requireLogin();

// ChangePasswordController
$router->addRoute(RequestMethod::GET, '/changement-de-mot-de-passe', 'ChangePasswordController@showConnectedChangePassword')
        ->requireLogin();
$router->addRoute(RequestMethod::POST, '/changement-de-mot-de-passe', 'ChangePasswordController@postConnectedChangePassword')
        ->requireLogin();
$router->addRoute(RequestMethod::GET, '/mot-de-passe-oublie', 'ChangePasswordController@showLostPassword')
        ->requireNotLogin();
$router->addRoute(RequestMethod::POST, '/mot-de-passe-oublie', 'ChangePasswordController@postLostPassword')
        ->requireNotLogin();
$router->addRoute(RequestMethod::GET, '/mot-de-passe-oublie/{token}', 'ChangePasswordController@showTokenChangePassword')
        ->requireNotLogin();
$router->addRoute(RequestMethod::POST, '/mot-de-passe-oublie/{token}', 'ChangePasswordController@postTokenChangePassword')
        ->requireNotLogin();

// ChangerMailAlertControllerApi
//$router->addRoute("/changement-notification-rp", "ChangerMailAlertControllerApi@changerMailAlertEducationalManager");
//$router->addRoute("/changement-notification-enseignant", "ChangerMailAlertControllerApi@changerMailAlertTeacher");
$router->addRoute(RequestMethod::POST, '/api/mailAlert', 'ChangerMailAlertControllerApi@update')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager);

// JustificationController
$router->addRoute(RequestMethod::GET, '/justifications', 'JustificationController@showJustificationList')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
//$router->addRoute("/DetailJustification/{id:int}", "DetailJustificationController@show");
$router->addRoute(RequestMethod::GET, '/detail-justification/{id:int}', 'JustificationController@detailJustificationGet')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Student);

$router->addRoute(RequestMethod::GET, '/api/justifications', 'JustificationControllerApi@getJustificationList')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Student);
$router->addRoute(RequestMethod::PUT, '/api/justifications/{id:int}', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// TimeslotController
//$router->addRoute("/teacherHome", "TeacherHomeController@show");
$router->addRoute(RequestMethod::GET, '/absences-a-mes-cours', 'TimeslotController@showTeacherHome')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager);
//$router->addRoute("/detailPeriod/", "DetailPeriodController@show");
$router->addRoute(RequestMethod::GET, '/absences-a-un-cours/{idTeacher:int}/{idRessource:int}/{group}/{datetime}', 'TimeslotController@showDetailTimeslot')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
//$router->addRoute("/resitSession", "ResitSessionController@show");
$router->addRoute(RequestMethod::GET, '/rattrapage', 'TimeslotController@showResitSession')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

$router->addRoute(RequestMethod::GET, '/api/timeslots', '')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

// StatistiqueController
//$router->addRoute("/statistique-general", "GeneralStatisticsController@show");
$router->addRoute(RequestMethod::GET, '/statistiques-generales', 'StatisticsController@showGeneralStatistics')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
//$router->addRoute("/statistique-etudiant/{id:int}", "StudentStatisticsController@show");
$router->addRoute(RequestMethod::GET, '/statistiques-etudiant/{id:int}', 'StatisticsController@showStudentStatistics')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

$router->addRoute(RequestMethod::GET, '/api/statistics', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// ImportVTController
$router->addRoute(RequestMethod::GET, '/televersement', 'ImportVTController@showImportVT')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::POST, '/televersement', 'ImportVTController@postImportVT')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

// HolidayController
//$router->addRoute("/listOffPeriod", "OffPeriodController@show");
$router->addRoute(RequestMethod::GET, '/periode-de-vacances', 'HolidayController@showHoliday')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::POST, '/periode-de-vacances', 'HolidayController@postHoliday')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::PUT, '/periode-de-vacances', 'HolidayController@putHoliday')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::DELETE, '/periode-de-vacances', 'HolidayController@deleteHoliday')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

// PredefinedCommentController
//$router->addRoute("/PredefinedComments", "PredefinedCommentController@show");
$router->addRoute(RequestMethod::GET, '/commentaire-predefini', 'PredefinedCommentController@predefinedCommentGet')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::POST, '/commentaire-predefini', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::PUT, '/commentaire-predefini', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::DELETE, '/commentaire-predefini', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// RulesController
$router->addRoute(RequestMethod::GET, '/reglement-interieur', 'InformationController@rules');

// UserManualController
$router->addRoute(RequestMethod::GET, '/manuel-d-utilisation', 'InformationController@userManual');

// SearchStudentController
//$router->addRoute("/SearchStudent", "SearchStudentController@show");
$router->addRoute(RequestMethod::GET, '/rechercher-un-etudiant', 'SearchStudentController@showSearchStudent')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

$router->addRoute(RequestMethod::GET, '/api/students', 'SearchStudentControllerApi@getSearchStudent')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// SemesterSettingsController
//$router->addRoute("/SemesterSettings", "SemesterSettingsController@show");
$router->addRoute(RequestMethod::GET, '/configuration-des-semestres', 'SemesterSettingsController@showSemesterSettings')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::POST, '/configuration-des-semestres', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::PUT, '/configuration-des-semestres', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::DELETE, '/configuration-des-semestres', '')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

// StudentProfilController
//$router->addRoute("/StudentProfile", "StudentProfileController@show");
$router->addRoute(RequestMethod::GET, '/profil-etudiant', 'StudentProfileController@showStudentProfile')
        ->requireLogin()
        ->addAuthorization(AccountType::Student);
//$router->addRoute("/StudentProfile/{id:int}", "StudentProfileController@show");
$router->addRoute(RequestMethod::GET, '/profil-etudiant/{id:int}', 'StudentProfileController@showStudentProfile')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

$router->addRoute(RequestMethod::GET, '/api/absences', '')
        ->requireLogin()
        ->addAuthorization(AccountType::Student)
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::PUT, '/api/hideRuleModal', '')
        ->requireLogin()
        ->addAuthorization(AccountType::Student);


// TODO: Route de test, à delete
$router->addRoute(RequestMethod::GET, '/api/test', 'TestApiController@getTest');
$router->addRoute(RequestMethod::POST, '/api/test', 'TestApiController@postTest');
$router->addRoute(RequestMethod::PUT, '/api/test/{id:int}', 'TestApiController@putTest');
$router->addRoute(RequestMethod::DELETE, '/api/test/{id:int}', 'TestApiController@deleteTest');

/*
TODO: A voir plus tard
$router->addRoute("/routine", "Routine@launch");
*/

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

//echo $path;

$dataRoute = $router->launch($path);
$dataView = $dataRoute->data;
$srcFolder = __DIR__ . '/../src';

if ($dataRoute->view != '/View/error.php') {
    CookieManager::setLastPath($path);
}

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
if (AuthManager::isLogin()) {
    require $srcFolder . "/View/Composants/buttonSettings.php";

    if (AuthManager::isRole(AccountType::EducationalManager) || AuthManager::isRole(AccountType::Secretary)) {
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

<footer class="footer bg-light">
    <div class="container d-flex flex-row flex-wrap justify-content-between align-items-start py-3">
        <div class="footer-row me-3">
            <p class="mb-0">Application interne de l'IUT de Maubeuge<br>
                © <?php echo date("Y") ?> Université Polytechnique Hauts‑de‑France
            </p>
        </div>

        <?php if (AuthManager::isRole(AccountType::Student) || !AuthManager::isLogin()): ?>

            <div class=" footer-row me-3">
                <a href="/reglement-interieur">Règlement intérieur de l’établissement</a>
            </div>

            <div class="footer-row me-3">
                <a href="/manuel-d-utilisation">Manuel d’utilisation du site</a>
            </div>
        <?php endif; ?>
    </div>

</footer>


<script src="/script/bootstrap.bundle.min.js"></script>
<script src="/script/alert.js"></script>
<script src="/script/tooltip.js"></script>
</body>
</html>