<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\DB\Select\JustificationSelector;
use Uphf\GestionAbsence\Model\DB\Update\processJustificatif;
use Uphf\GestionAbsence\Model\DB\Update\UpdateBuilder\AbsenceUpdateBuilder;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Mailer;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\ProcessJustificationValidator;
use Uphf\GestionAbsence\ViewModel\DetailJustificationViewModel;
use BadMethodCallException;

/**
 *  Controller pour le détail d'un justificatif
 */
class DetailJustificationController {
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
    public static function show($params): ControllerData {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // L'url ne spécifie pas de justificatif, alors affiché 404
        if(!isset($params['id'])) {
            Notification::addNotification(NotificationType::Error, "Aucun justificatif spécifié");
            return ControllerData::get404();
        }

        $justification = JustificationSelector::getJustificationById($params['id']);

        // Le justificatif n'existe pas
        if($justification === null) {
            Notification::addNotification(NotificationType::Error, "Le justificatif demandé n'existe pas");
            return ControllerData::get404();
        }

        // Si l'utilisateur n'est pas RP et que le justificatif ne lui appartient pas => 403
        if(!AuthManager::isRole(AccountType::EducationalManager) && $justification->getStudent()->getIdAccount() != AuthManager::getAccount()->getIdAccount()) {
            Notification::addNotification(NotificationType::Error, "Vous n'avez pas l'autorisation de voir ce justificatif");
            return ControllerData::get403();
        }

        // Récupérer les absences et les fichiers du justificatif
        $absences = $justification->getAbsences();
        $files = $justification->getFiles();

        // Si l'utilisateur est le RP, et que la requête est de type POST => Traitement du justificatif par le RP
        if(AuthManager::isRole(AccountType::EducationalManager) && $_SERVER['REQUEST_METHOD'] === "POST") {
            self::processJustification($justification, $absences);
        }

        return new ControllerData(
            "/View/detailJustification.php",
            "Détails de la justification",
            new DetailJustificationViewModel(
                $justification,
                $absences,
                $files,
                AuthManager::getRole(),
            )
        );
    }

    /**
     * Process un justificatif par le RP
     *
     * @param Justification $justification
     * @param array $absences
     * @return void
     */
    private static function processJustification(Justification $justification, array $absences): void {
        // Récupération des données POST
        $validator = new ProcessJustificationValidator();

        if(!$validator->checkAllGood()) {
            Notification::addNotification(NotificationType::Error, "Impossible de traiter votre demande, veuillez contacter l'administrateur");
            return;
        }

        $data = $validator->getData();

        // Données récupérer en post apres application des filtres
        $comment = $data['rejectionReason'];
        $absencesDataPost = $data['absences'];

        // Liste des absences lors d'examen pour le justificatif
        $absencesExemens = [];

        Connection::beginTransaction();

        // Mise à jour du justificatif
        try {
            ProcessJustificatif::execute($justification, $comment);
        }
        catch(BadMethodCallException $e) {
            Connection::rollback();
            Notification::addNotification(NotificationType::Error, "Le justificatif a déjà été traité");
            return;
        }

        $absencesUpdater = new AbsenceUpdateBuilder();

        // Parcours des absences
        foreach($absences as $abs) {
            // Construction de la clé et récupération des valeurs envoyée en POST pour cette absence
            $key = $abs->getIdAccount() . "_" . $abs->getTime()->format('Y-m-d H:i:s');

            // Cas ou l'absence n'a pas été fournis en POST, ne devrait jamais arriver dans une utilisation normale de l'application
            if(!array_key_exists($key, $absencesDataPost)) {
                Connection::rollback();
                Notification::addNotification(NotificationType::Error, "Impossible de traiter votre demande, veuillez contacter l'administrateur");
                return;
            }

            // Récupération du traitement à effectuer sur cette absence
            $values = $absencesDataPost[$key];

            // Remplir une liste avec les absences justifiée lors d'examen
            if($values['state'] == StateAbs::Validated && $abs->getExamen()){
                $absencesExemens[] = $abs;
            }

            // Si l'absence est refusé et que le commentaire du RP est vide
            // On annule le traitement du justificatif (Le RP doit préciser un motif de refus dans le cas ou au moins une abs est refusé).
            if($values['state'] == StateAbs::Refused && $comment === '') {
                Notification::addNotification(NotificationType::Error, "Vous n'avez pas fournis de motif de refus !");
                Connection::rollback();
                return;
            }

            // On charge l'absence dans l'updater et on effectue son traitement
            $absencesUpdater->loadAbsence($abs)->state($values['state'])->allowedJustification(!$values['lock']);
        }

        $absencesUpdater->execute();
        Connection::commit();

        // Pours chacune des absences justifiées lors d'examen, on envoie un mail au professeur et l'étudiant
        foreach ($absencesExemens as $absExem) {
            Mailer::sendAlertExam(
                $absExem->getTime(),
                $absExem->getStudent(),
                $absExem->getTeacher(),
                $absExem->getResource()
            );
        }

        // Envoie du mail à l'étudiant pour le prévenir du traitement de son justificatif
        $student = $absences[0]->getStudent();
        Mailer::sendProcessedJustification(
            $student->getLastName(),
            $student->getFirstName(),
            $student->getEmail(),
            $justification->getStartDate(),
            $justification->getEndDate()
        );

        Notification::addNotification(NotificationType::Success, "Justificatif traité avec succès");
    }
}