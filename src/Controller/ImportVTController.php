<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\ReaderCSV;
use Uphf\GestionAbsence\ViewModel\ImportVTViewModel;

/**
 * Classe controlleur pour la page d'importation des fichiers .cvs venant de VT
 */

class ImportVTController
{

    /**
     * @return \Uphf\GestionAbsence\Controller\ControllerData
     */
    public static function show() : ControllerData
    {
        /**
         * Si l'utilisateur n'est pas connecté, redirection vers /
         */
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        /**
         * Si n'est pas RP ou Secrétaire redirection vers la page 403
         */
        if (!AuthManager::isRole(AccountType::EducationalManager) && !AuthManager::isRole(AccountType::Secretary))
          {
                      return ControllerData::get403();
          }

        /**
         * Si il y a eu une méthode POST, cela signifie que l'utilisateur à importer un fichier et la valider
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            /**
             * Si le fichier n'est pas au format .csv ou si il y a eu une erreur de lecture
             */
            if (!isset($_FILES["vt_file"]) || $_FILES["vt_file"]["error"] !== UPLOAD_ERR_OK || !ReaderCSV::isCSV($_FILES["vt_file"]['name']))
            {
                Notification::addNotification(
                  NotificationType::Error,
                  "Aucun fichier CSV valide fourni."
                );
            } else
            {
                $tempPath = $_FILES["vt_file"]["tmp_name"];
                $data = ReaderCSV::readCSV($tempPath);
//                var_dump($data);

                // TODO : Faire la requête SQL depuis une classe dans Model/DB

                Notification::addNotification(
                  NotificationType::Success,
                  "Le fichier a été importé avec succès."
                );
            }
        }

        return new ControllerData(
          '/View/importVT.php',
          "Importation de VT",
          new importVTViewModel());
    }
}