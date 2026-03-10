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
     * Afficher la vue importVT.php
     *
     * @return ControllerData
     */
    public static function showImportVT(): ControllerData
    {
        return new ControllerData(
            '/View/importVT.php',
            'Importation de VT',
            new importVTViewModel());
    }

    /**
     * Traitement de l'import d'un export VT
     *
     * @return ControllerData
     */
    public static function postImportVT(): ControllerData
    {
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