<?php

namespace Uphf\GestionAbsence\Controller;

use Exception;
use finfo;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\FileService;
use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller de vue pour les fichiers
 *
 *  - GET /fichier/{id} -> showFile()
 */
class FileController {
    /**
     * Affiche un fichier, s'il existe et que l'utilisateur a l'autorisation de le voir.
     *
     * @param $params
     * @return void
     */
    public static function showFile($params): void {
        try {
            $file = FileService::getFile($params["id"]);

            if(!FileService::userCanSeeFile($file, AuthManager::getAccount())) {
                throw new Exception("Vous n'avez pas l'autorisation de voir ce fichier.");
            }

            $filePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $file->getFileName();
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($filePath);

            Renderer::render(
                null,
                '',
                [
                    'fileName' => $file->getFileName(),
                    'filePath' => $filePath,
                    'mimeType' => $mimeType
                ],
                'fileLayout.php'
            );
        }
        catch(Exception $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
            ErrorController::error404();
        }
    }
}