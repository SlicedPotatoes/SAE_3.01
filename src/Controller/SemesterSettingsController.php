<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\SemesterSelector;
use Uphf\GestionAbsence\Model\DB\Update\SemesterUpdater;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\ViewModel\SemesterSettingsViewModel;

/**
 * Controller pour la gestion des paramètres de semestre
 */
class SemesterSettingsController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'a pas les permissions de voir la page => 403
     *
     * Si RP + Requête POST => Mise à jour du semestre
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

        // Si pas RP -> Rediriger 403
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        // Traitement du POST pour la mise à jour
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'update') {
                $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
                $startDate = $_POST['startDate'] ?? '';
                $endDate = $_POST['endDate'] ?? '';

                if (empty($startDate) || empty($endDate) || $id <= 0) {
                    Notification::addNotification(NotificationType::Error, "Un des champs obligatoire n'a pas été fourni");
                } else if ($startDate > $endDate) {
                    Notification::addNotification(NotificationType::Error, "La date de début doit être inférieure ou égale à la date de fin");
                } else {
                    $updated = SemesterUpdater::update($id, $startDate, $endDate);
                    if ($updated) {
                        Notification::addNotification(NotificationType::Success, "Le semestre a été mis à jour");
                    } else {
                        Notification::addNotification(NotificationType::Error, "Erreur lors de la mise à jour du semestre");
                    }
                }
            }
        }

        // Récupérer les semestres depuis la BDD
        $semesters = SemesterSelector::getCurrentSemesters();
        $viewModel = new SemesterSettingsViewModel($semesters);

        return new ControllerData(
            '/View/semesterSettings.php',
            'Définir les semestres',
            $viewModel,
        );
    }
}
