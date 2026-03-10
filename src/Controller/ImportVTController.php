<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Insert\AbsenceInsertor;
use Uphf\GestionAbsence\Database\Insert\NewAccountInsertor;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\ReaderCSV;
use Uphf\GestionAbsence\Model\Validation\ImportAbsenceValidator;
use Uphf\GestionAbsence\Service\ImportVTService;
use Uphf\GestionAbsence\ViewModel\ImportVTViewModel;

/**
 * Classe controlleur pour la page d'importation des fichiers .cvs venant de VT
 */
class ImportVTController
{
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'est pas RP ou secrétaire => 403
     *
     * Si requête POST => Traitement de l'import
     *
     * @return ControllerData
     */
    public static function showImportVT(): ControllerData
    {
        /**
         *  TODO : A RETIRER QUAND LES ROUTES SERONT REFAIT
         *
         * Si n'est pas RP ou Secrétaire redirection vers la page 403
         */
        if (!AuthManager::isRole(AccountType::EducationalManager) && !AuthManager::isRole(AccountType::Secretary)) {
            return ControllerData::get403();
        }

        return new ControllerData(
            '/View/importVT.php',
            'Importation de VT',
            new importVTViewModel());
    }

    public static function postImportVT(): ControllerData
    {
        /**
         * TODO : A RETIRER QUAND LES ROUTES SERONT REFAIT
         *
         * Si n'est pas RP ou Secrétaire redirection vers la page 403
         */
        if (!AuthManager::isRole(AccountType::EducationalManager) && !AuthManager::isRole(AccountType::Secretary)) {
            return ControllerData::get403();
        }

        try {
            ImportVTService::import($_FILES['vt_file']);

            Notification::addNotification(
                NotificationType::Success,
                'Le fichier a été importé avec succès.'
            );
        } catch (\Exception $e) {
            Notification::addNotification(
                NotificationType::Error,
                $e->getMessage()
            );
        }

        return new ControllerData(
            '/View/importVT.php',
            'Importation de VT',
            new importVTViewModel());
    }
}