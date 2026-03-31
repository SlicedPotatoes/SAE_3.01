<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\ImportVTService;
use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Classe controlleur pour la page d'importation des fichiers .cvs venant de VT
 */
class ImportVTController
{
    /**
     * Afficher la vue importVT.php
     *
     * @return void
     */
    public static function showImportVT(): void
    {
        Renderer::render('ImportVT.php', "Importation de VT");
    }

    /**
     * Traitement de l'import d'un export VT
     *
     * @return void
     */
    public static function postImportVT(): void
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

        Renderer::render('ImportVT.php', "Importation de VT");
    }
}