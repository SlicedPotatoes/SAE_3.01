<?php

namespace Uphf\GestionAbsence\Model;

use Exception;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use finfo;

/**
 * Classe permettant de gérer la sauvegarde de fichier
 */
class FileUpload {

    /**
     * Permet d'upload les fichiers réceptionner dans \$_FILES[\$key]
     *
     * Renvoie un tableau des fichiers uploader avec succes
     *
     * @param $key
     * @return array
     */
    public static function upload($key): array {
        $uploadDir = self::getUploadDir();

        if(!self::checkDir($uploadDir)) {
            Notification::addNotification(NotificationType::Error, "Une erreur interne est survenue lors de l'upload. Veuillez réessayer plus tard.");
            return [];
        }

        if(!isset($_FILES[$key])) {
            Notification::addNotification(NotificationType::Warning, "Aucun fichier fourni");
            return [];
        }

        // Permet de gérer les inputs types files multiples ou single
        $names = (array) ($_FILES[$key]['name'] ?? []);
        $tmp_names = (array) ($_FILES[$key]['tmp_name'] ?? []);
        $errors = (array) ($_FILES[$key]['error'] ?? []);
        $sizes = (array) ($_FILES[$key]['size'] ?? []);

        if(count($names) === 0 || (count($names) === 1 && $names[0] === '')) {
            Notification::addNotification(NotificationType::Warning, "Aucun fichier fourni");
            return [];
        }

        $fileUploaded = [];

        for($i = 0; $i < count($names); $i++) {
            $file = [
                "filename" => basename($names[$i]),
                "tmpname" => $tmp_names[$i],
                "error" => $errors[$i],
                "size" => $sizes[$i],
            ];
            $file['ext'] = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));

            if(!self::checkValidFile($file)) {
                continue;
            }

            $base = pathinfo($file['filename'], PATHINFO_FILENAME);
            $base = preg_replace('/[^A-Za-z0-9._-]/', '_', $base);

            try {
                $filename = $base . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $file['ext'];
            }
            catch (Exception $e) {
                error_log("random_butes error: " . $e->getMessage());
                Notification::addNotification(
                    NotificationType::Warning,
                    $file['filename'] . ": Erreur lors de la réception du fichier. Veuillez contacter le responsable pédagogique pour l'en informer."
                );
                continue;
            }

            $targetPath = $uploadDir . $filename;

            if(move_uploaded_file($file['tmpname'], $targetPath)) {
                chmod($targetPath, 0644);
                $fileUploaded[] = [
                    "originalName" => $file['filename'],
                    "name" => $filename
                ];
            }
            else {
                Notification::addNotification(
                    NotificationType::Warning,
                    $file['filename'] . ": Erreur lors de la réception du fichier. Veuillez contacter le responsable pédagogique pour l'en informer."
                );
                error_log("Impossible de déplacé le fichier depuis le dossier temporaire vers le dossier de destination");
            }
        }

        return $fileUploaded;
    }

    /**
     * Récupérer le path du dossier d'upload
     * @return string
     */
    private static function getUploadDir(): string {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR;
    }

    /**
     * Retourne vrai si le fichier respecte tous les critères pour être upload
     *
     * En cas d'erreur, ajoute une notification pour l'utilisateur
     *
     * @param array $file
     * @return bool
     */
    private static function checkValidFile(array $file): bool {
        // Vérification des erreurs d'upload générique
        if($file['error'] != UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_FORM_SIZE:
                case UPLOAD_ERR_INI_SIZE:
                    Notification::addNotification(
                        NotificationType::Warning,
                        $file['filename'] . ": Dépasse la taille maximale autorisé (" . GlobalVariable::LIMIT_FILE_SIZE_UPLOAD_MO() . " Mo)"
                    );
                    break;
                default:
                    Notification::addNotification(
                        NotificationType::Warning,
                        $file['filename'] . ": Une erreur interne est survenue lors de l'upload de ce fichier. Veuillez contacter le responsable pédagogique pour l'en informer."
                    );
                    error_log("Erreur interne lors de l'upload d'un fichier. Error Code: " . $file['error']);
                    break;
            }
            return false;
        }

        // Vérification de la taille
        if($file['size'] > GlobalVariable::LIMIT_FILE_SIZE_UPLOAD()) {
            Notification::addNotification(
                NotificationType::Warning,
                $file['filename'] . ": Dépasse la taille maximale autorisé (" . GlobalVariable::LIMIT_FILE_SIZE_UPLOAD_MO() . " Mo)"
            );

            return false;
        }

        // Vérification du fichier temporaire
        if(!is_uploaded_file($file['tmpname'])) {
            Notification::addNotification(
                NotificationType::Warning,
                $file['filename'] . ": Erreur lors de la réception du fichier. Veuillez contacter le responsable pédagogique pour l'en informer."
            );
            error_log("Erreur de réception d'un fichier filename: " . $file['filename'] . " tmpname: " . $file['tmpname']);

            return false;
        }

        // Vérification du MIME Type du fichier
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmpname']) ?: null;
        if($mime == null || !in_array($mime, GlobalVariable::ALLOWED_MIME_TYPE())) {
            Notification::addNotification(
                NotificationType::Warning,
                $file['filename'] . ": Format de fichier non autorisé."
            );
            return false;
        }

        // Vérification de l'extension du fichier
        if($file['ext'] == null || !in_array($file['ext'], GlobalVariable::ALLOWED_EXTENSIONS_FILE())) {
            Notification::addNotification(
                NotificationType::Warning,
                $file['filename'] . ": Format de fichier non autorisé."
            );
            return false;
        }

        return true;
    }

    /**
     * Méthode statique permettant de vérifier que le dossier d'upload est prêt
     *
     * Renvoie false en cas de problème avec ce dernier et en notifie l'utilisateur
     *
     * @param string $dir
     * @return bool
     */
    private static function checkDir(string $dir): bool {
        $isOk = true;

        // Si le dossier n'existe pas et qu'il est impossible de le créer
        if(!is_dir($dir) && !mkdir($dir, 0755, true)) {
            $isOk = false;
            error_log("Impossible de créer le dossier upload sur le serveur");
        }
        // Si le dossier est présent, mais qu'il n'est pas accéssible en écriture
        else if(!is_writable($dir)) {
            $isOk = false;
            error_log("Le dossier upload n'est pas accéssible en écriture");
        }

        return $isOk;
    }

    /**
     * Supprimer les fichiers du dossier d'upload, passer en argument
     * @param array $files
     * @return void
     */
    public static function deleteFiles(array $files): void {
        $uploadDir = self::getUploadDir();

        foreach ($files as $file) {
            $fullPath = $uploadDir . $file['name'];

            if(file_exists($fullPath)) {
                if(!unlink($fullPath)) {
                    error_log("Erreur: inpossible de supprimer le fichier $fullPath");
                }
            }
        }
    }
}