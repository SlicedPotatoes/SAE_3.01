<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Insert\AbsenceInsertor;
use Uphf\GestionAbsence\Model\DB\Insert\NewAccountInsertor;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\ReaderCSV;
use Uphf\GestionAbsence\Model\Validation\ImportAbsenceValidator;
use Uphf\GestionAbsence\ViewModel\ImportVTViewModel;

/**
 * Classe controlleur pour la page d'importation des fichiers .cvs venant de VT
 */
class ImportVTController
{
    private static array $absenceColumns = array(
        'Nom',
        'Prénom',
        'Prénom 2',
        'Date de naissance',
        'Identifiant',
        'Diplômes',
        'Composante',
        'Public',
        'Date',
        'Heure',
        'Durée',
        'Type',
        'Matière',
        'Identifiant matière',
        'Enseignement',
        "Identifiant de l'enseignement",
        'Absent/Présent',
        'Justification',
        'Motif absence',
        'Commentaire',
        'Groupes',
        'Salles',
        'Profs',
        'Contrôle');

    private static array $studentColumns = array(
        'Nom',
        'Prénom',
        'Prénom 2',
        'Date de naissance',
        'Identifiant',
        'Diplômes',
        'Public',
        'Email',
        'Identifiant national');

    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'est pas RP ou secrétaire => 403
     *
     * Si requête POST => Traitement de l'import
     *
     * @return ControllerData
     */
    public static function show(): ControllerData
    {
        /**
         * Si l'utilisateur n'est pas connecté, redirection vers /
         */
        if (!AuthManager::isLogin())
        {
            header(('Location: /'));
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
         * Si il y a eu une méthode POST, cela signifie que l'utilisateur à importer un fichier
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            /**
             * Si le fichier n'est pas au format .csv ou si il y a eu une erreur de lecture
             */
            if (!isset($_FILES['vt_file']) || $_FILES['vt_file']['error'] !== UPLOAD_ERR_OK || !ReaderCSV::isCSV($_FILES['vt_file']['name']))
            {
                Notification::addNotification(
                    NotificationType::Error,
                    'Aucun fichier CSV valide fourni.'
                );
            } else
            {
                $tempPath = $_FILES['vt_file']['tmp_name'];
                $data = ReaderCSV::readCSV($tempPath);
                /**
                 * Check des columns pour un import d'absence
                 */
                if (ReaderCSV::haveCollum($data, ImportVTController::$absenceColumns))
                {
                    $validator = new ImportAbsenceValidator($data);
                    $nbAbsBeforeValidator = count($data);

                    $data = $validator->getData();

                    /**
                     * Si apres validation du format de données, le fichier est vide
                     */
                    if(count($data) === 0) {
                        Notification::addNotification(NotificationType::Error, "Aucune données valide dans le fichier.");
                    }
                    /**
                     * Apres validation des données, il reste des lignes
                     */
                    else {
                        $corruptLine = $nbAbsBeforeValidator - count($data);
                        // Notification précisant que des lignes n'ont pas le format valide
                        // Ne cancel pas l'import pour autant, juste ignore les lignes en question
                        if($corruptLine != 0) {
                            Notification::addNotification(NotificationType::Warning, "$corruptLine lignes ont des données invalides, seules les lignes valides seront traitées");
                        }

                        [$nbAbs, $nbAbsWithoutDuplication] = AbsenceInsertor::addAbsences($data);

                        // Notification différente en cas de doublons
                        if($nbAbs != $nbAbsWithoutDuplication) {
                            Notification::addNotification(
                                NotificationType::Warning,
                                "$nbAbsWithoutDuplication sur $nbAbs Absences importé avec succès. (Cause doublon)"
                            );
                        }
                        else {
                            Notification::addNotification(
                                NotificationType::Success,
                                'Le fichier a été importé avec succès.'
                            );
                        }
                    }
                }
                /**
                 * Check les columns pour un import d'étudiant
                 */
                else if (ReaderCSV::haveCollum($data, ImportVTController::$studentColumns))
                {
                    NewAccountInsertor::insertStudentAccount($data);

                    Notification::addNotification(
                        NotificationType::Success,
                        'Le fichier a été importé avec succès.');
                }
                else
                {
                    Notification::addNotification(
                        NotificationType::Error,
                        'Le fichier csv ne correspond pas au critère.'
                    );
                }
            }
        }

        return new ControllerData(
            '/View/importVT.php',
            'Importation de VT',
            new importVTViewModel());
    }
}