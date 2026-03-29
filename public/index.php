<?php
/**
 * Point d'entrée de l'application
 *
 * - Initialisation de AuthManager et CookieManager
 * - Définition des routes
 * - Lancement du routeur
 */

require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use Uphf\GestionAbsence\Database\Connection;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\GlobalVariable;
use Uphf\GestionAbsence\Utils\Router\RequestMethod;
use Uphf\GestionAbsence\Utils\Router\Router;

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
$router->addRoute(RequestMethod::GET, '/changement-de-mot-de-passe', 'ChangePasswordController@showConnectedChangePassword', 'changePassword')
        ->requireLogin();
$router->addRoute(RequestMethod::POST, '/changement-de-mot-de-passe', 'ChangePasswordController@postConnectedChangePassword', 'changePassword')
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
$router->addRoute(RequestMethod::PUT, '/api/mailAlert', 'ChangerMailAlertControllerApi@putMailAlert')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager);

// JustificationController
$router->addRoute(RequestMethod::GET, '/justifications', 'JustificationController@showJustificationList', 'justifications')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::GET, '/detail-justification/{id:int}', 'JustificationController@showDetailJustification')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Student);

$router->addRoute(RequestMethod::GET, '/api/justifications', 'JustificationControllerApi@getJustificationList')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Student);
$router->addRoute(RequestMethod::PUT, '/api/justifications/{id:int}', 'JustificationControllerApi@putDetailJustification')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// TimeslotController
$router->addRoute(RequestMethod::GET, '/absences-a-mes-cours', 'TimeslotController@showTeacherHome', 'timeslots')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::GET, '/absences-a-un-cours/{idTeacher:int}/{idRessource:int}/{group}/{datetime}', 'TimeslotController@showDetailTimeslot')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::GET, '/rattrapage', 'TimeslotController@showResitSession', 'resitSession')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

$router->addRoute(RequestMethod::GET, '/api/timeslots', 'TimeslotControllerApi@getTimeslots')
        ->requireLogin()
        ->addAuthorization(AccountType::Teacher)
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

// StatistiqueController
$router->addRoute(RequestMethod::GET, '/statistiques-generales', 'StatisticsController@showGeneralStatistics', 'generalsStatistics')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::GET, '/statistiques-etudiant/{id:int}', 'StatisticsController@showStudentStatistics')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

$router->addRoute(RequestMethod::GET, '/api/statistics', 'StatisticsControllerApi@getStatistics')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// ImportVTController
$router->addRoute(RequestMethod::GET, '/televersement', 'ImportVTController@showImportVT', 'importVT')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::POST, '/televersement', 'ImportVTController@postImportVT', 'importVT')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

// HolidayController
$router->addRoute(RequestMethod::GET, '/periode-de-vacances', 'HolidayController@showHoliday', 'holidays')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::GET, '/api/holidays', 'HolidayControllerApi@getHolidays')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::POST, '/api/holidays', 'HolidayControllerApi@postHoliday')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::PUT, '/api/holidays/{id:int}', 'HolidayControllerApi@putHoliday')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);
$router->addRoute(RequestMethod::DELETE, '/api/holidays/{id:int}', 'HolidayControllerApi@deleteHoliday')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager)
        ->addAuthorization(AccountType::Secretary);

// PredefinedCommentController
$router->addRoute(RequestMethod::GET, '/commentaire-predefini', 'PredefinedCommentController@showPredefinedComment', 'predefinedComment')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::POST, '/api/predefinedComment', 'PredefinedCommentControllerApi@postPredefinedComment')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::PUT, '/api/predefinedComment/{id:int}', 'PredefinedCommentControllerApi@putPredefinedComment')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::DELETE, '/api/predefinedComment/{id:int}', 'PredefinedCommentControllerApi@deletePredefinedComment')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// RulesController
$router->addRoute(RequestMethod::GET, '/reglement-interieur', 'InformationController@rules');

// UserManualController
$router->addRoute(RequestMethod::GET, '/manuel-d-utilisation', 'InformationController@userManual');

// SearchStudentController
$router->addRoute(RequestMethod::GET, '/rechercher-un-etudiant', 'SearchStudentController@showSearchStudent', 'searchStudent')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::GET, '/api/students', 'SearchStudentControllerApi@getSearchStudent')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

// SemesterSettingsController
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
$router->addRoute(RequestMethod::GET, '/profil-etudiant', 'StudentProfileController@showStudentProfile')
        ->requireLogin()
        ->addAuthorization(AccountType::Student);
$router->addRoute(RequestMethod::GET, '/profil-etudiant/{id:int}', 'StudentProfileController@showStudentProfile')
        ->requireLogin()
        ->addAuthorization(AccountType::EducationalManager);

$router->addRoute(RequestMethod::GET, '/api/absences', 'StudentProfileControllerApi@getAbsences')
        ->requireLogin()
        ->addAuthorization(AccountType::Student)
        ->addAuthorization(AccountType::EducationalManager);
$router->addRoute(RequestMethod::POST, '/api/justifications', 'StudentProfileControllerApi@postJustification')
        ->requireLogin()
        ->addAuthorization(AccountType::Student);

// TODO: Route de test, à delete
$router->addRoute(RequestMethod::GET, '/api/test', 'TestApiController@getTest');
$router->addRoute(RequestMethod::POST, '/api/test', 'TestApiController@postTest');
$router->addRoute(RequestMethod::PUT, '/api/test/{id:int}', 'TestApiController@putTest');
$router->addRoute(RequestMethod::DELETE, '/api/test/{id:int}', 'TestApiController@deleteTest');
$router->addRoute(RequestMethod::GET, '/test', 'TestApiController@viewTest');

/*
TODO: A voir plus tard
$router->addRoute("/routine", "Routine@launch");
*/

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
//echo $path;
$router->launch($path);

// TODO: Gestion bouton return
/*if ($dataRoute->view != '/View/error.php') {
    CookieManager::setLastPath($path);
}*/

Connection::close();