<?php
// FILE: src/Controller/OffPeriodController.php
namespace Uphf\GestionAbsence\Controller;

use DateTime;
use Uphf\GestionAbsence\Model\DB\Update\OffPeriodUpdater;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Insert\OffPeriodInsertor;
use Uphf\GestionAbsence\Model\DB\Select\OffPeriodSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\ViewModel\OffPeriodViewModel;

/**
 *  Controller pour la gestion des périodes de congé
 */
class OffPeriodController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'a pas les permissions de voir la page => 403
     *
     * Si le justificatif sélectionné n'existe pas => 404
     *
     * Si RP + Requête POST => Traitement du justificatif par le RP
     *
     * @param $params
     * @return ControllerData
     */

    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'a pas les permissions de voir la page => 403
     *
     * Gestion de l'ajout / suppression / modification d'une période de congé
     *
     * @param array $params
     * @return ControllerData
     */
    public static function show(array $params = []): ControllerData {
        // Si pas login -> Rediriger login
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // Si pas RP ou Secretaire -> Rediriger 403
        if(!(AuthManager::isRole(AccountType::EducationalManager) ||
            AuthManager::isRole(AccountType::Secretary))) {
            return ControllerData::get403();
        }

        // --- Traiter les POST d'abord (insert / delete / update), puis rediriger (PRG)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'insert') {
                $start = $_POST["startDate"] ?? '';
                $end = $_POST["endDate"] ?? '';
                $name = $_POST["periodName"] ?? '';

                if(!isset($start) || !isset($end) || !isset($name)) {
                    Notification::addNotification(NotificationType::Error, "Un des champs obligatoire n'a pas été fournis");
                }
                else if(DateTime::createFromFormat("Y-m-d", $end) > DateTime::createFromFormat("Y-m-d", $start)) {
                    Notification::addNotification(NotificationType::Error, "La date de début doit être inférieur ou égal a la date de fin");
                }
                else {
                    OffPeriodInsertor::insert($start, $end, $name);
                }
            }

            if ($action === 'delete') {
                $id = $_POST["id"];

                if(isset($id)){
                    OffPeriodUpdater::delete($id);
                }
            }

            if ($action === 'update') {
                $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
                $start = $_POST["startDate"] ?? '';
                $end = $_POST["endDate"] ?? '';
                $name = $_POST["periodName"] ?? '';

                if(!isset($start) || !isset($end) || !isset($name) || !isset($id)) {
                    Notification::addNotification(NotificationType::Error, "Un des champs obligatoire n'a pas été fournis");
                }
                else if(DateTime::createFromFormat("Y-m-d", $end) > DateTime::createFromFormat("Y-m-d", $start)) {
                    Notification::addNotification(NotificationType::Error, "La date de début doit être inférieur ou égal a la date de fin");
                }
                else {
                    OffPeriodUpdater::update($id, $start, $end, $name);
                }
            }
        }

        // --- Maintenant récupérer les périodes pour l'affichage
        $periods = OffPeriodSelector::getOffPeriod();

        return new ControllerData(
            '/View/listOffPeriod.php',
            'Liste des périodes de congé',
            new OffPeriodViewModel($periods),
        );
    }
}
