<?php

namespace Uphf\GestionAbsence\Controller;

use DateTime;
use Exception;
use InvalidArgumentException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Insert\JustificationInsertor;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\AbsenceSelectBuilder;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\JustificationSelectBuilder;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Model\DB\Select\StudentSelector;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\FileUpload;
use Uphf\GestionAbsence\Model\Mailer;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\CheckValidity;
use Uphf\GestionAbsence\ViewModel\StudentProfileViewModel;

/**
 * Controller pour le profil étudiant
 */
class StudentProfileController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si compte étudiant => afficher son profil
     *
     * Si compte RP:
     * - Et pas d'étudiant spécifié => 404
     * - Et que l'étudiant sélectionné n'existe pas => 404
     * - Et que l'étudiant existe => Profil de l'étudiant
     *
     * Si compte pas RP et pas Etu => 403
     *
     * Utilisé par un étudient et le RP
     *
     * @param array $params
     * @return ControllerData
     */
    public static function show(array $params): ControllerData {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // Si compte étudiant, afficher son profil
        if(AuthManager::isRole(AccountType::Student)) {
            self::studentCreateJustification(AuthManager::getAccount());
            return self::showHelper(AuthManager::getAccount());
        }
        // Si RP
        elseif(AuthManager::isRole(AccountType::EducationalManager)) {
            // Et que l'url ne spécifie pas d'étudiant, alors affiché 404
            if(!isset($params['id'])) {
                Notification::addNotification(NotificationType::Error, "Aucun étudiant spécifié");
                return ControllerData::get404();
            }

            $student = StudentSelector::getStudentById($params['id']);

            // Et que l'étudiant spécifié n'existe pas, affiché 404
            if($student === null) {
                Notification::addNotification(NotificationType::Error, "L'étudiant demandé n'existe pas");
                return ControllerData::get404();
            }

            // Afficher le profil de l'étudiant spécifié
            return self::showHelper($student);
        }

        // Si autre type de compte => 403
        return ControllerData::get403();
    }

    /**
     * Permet de gérer la création d'un justificatif au niveau de la BDD + Fichier upload sur le serveur
     *
     * @param Student $student
     * @return void
     */
    private static function studentCreateJustification(Student $student): void {
        // L'utilisateur tente de créer un justificatif
        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] == 'createJustification') {
            // Vérifier si tous les champs obligatoires ont été fournis avec un format valide
            if(!CheckValidity::isValidDate("POST", "startDate", "Y-m-d")) {
                Notification::addNotification(NotificationType::Error, "Créer Justification: La date de début a un format invalide.");
                return;
            }
            if(!CheckValidity::isValidDate("POST", "endDate", "Y-m-d")) {
                Notification::addNotification(NotificationType::Error, "Créer Justification: La date de fin a un format invalide.");
                return;
            }
            if(!CheckValidity::haveValue("POST", "absenceReason")) {
                Notification::addNotification(NotificationType::Error, "Créer Justification: Veuillez fournir une raison.");
                return;
            }
            if(DateTime::createFromFormat("Y-m-d", $_POST['startDate']) > DateTime::createFromFormat("Y-m-d", $_POST['endDate'])) {
                Notification::addNotification(NotificationType::Error, "Créer Justification: La date de début doit être inférieur ou égal a la date de fin");
                return;
            }

            $files = FileUpload::upload('files');

            try {
                // Créer le justificatif dans la BDD
                JustificationInsertor::insert(
                    $student->getIdAccount(),
                    $_POST['absenceReason'],
                    $_POST['startDate'],
                    $_POST['endDate'],
                    $files
                );

                // Envoyer le mail d'accusé de réception
                Mailer::sendAccRecpJustification(
                    $student->getLastName(),
                    $student->getFirstName(),
                    $student->getEmail(),
                    DateTime::createFromFormat("Y-m-d", $_POST['startDate'])->format("d/m/Y"),
                    DateTime::createFromFormat("Y-m-d", $_POST['endDate'])->format("d/m/Y")
                );

                Notification::addNotification(NotificationType::Success, "Justificatif envoyé avec succès");

                return;
            }
            // Exception levée par JustificationInsertor::insert, quand il n'y a pas d'absence justifiable sur la période sélectionnée
            catch (InvalidArgumentException $e) {
                Notification::reset();
                Notification::addNotification(NotificationType::Error, "Créer Justification: Il n'y a pas d'absence justifiable sur la période sélectionnée");
            }
            catch (Exception $e) {
                Notification::reset();
                Notification::addNotification(NotificationType::Error, "Créer Justification: Une erreur interne est survenue lors de l'upload. Veuillez réessayer plus tard.");
                error_log("Créer Justification: " . $e->getMessage());
            }

            // S'il y a eu une erreur critique pendent la création du justificatif, supprimer les fichiers du dossier upload
            FileUpload::deleteFiles($files);
        }
    }

    /**
     * Renvoie le ControllerData d'un profile étudiant, pour un étudiant passé en paramètre
     *
     * @param Student $student
     * @return ControllerData
     */
    private static function showHelper(Student $student): ControllerData {
        $currTab = 'proof'; // CurrTab par défaut

        // Builder pour récupérer les justificatifs et absences
        $justificationSelectBuilder = new JustificationSelectBuilder()->idStudent($student->getIdAccount());
        $absenceSelectBuilder = new AbsenceSelectBuilder()->idStudent($student->getIdAccount());

        $filter = []; // Filtre appliqué a la currTab

        // Gestion des filtres pour la currTab, vérifier s'ils sont envoyé via POST, que les valeurs sont correctes et les appliqués au builder
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            if(isset($_POST['currTab']) && $_POST['currTab'] == 'abs') { $currTab = 'abs'; }
            $builderCurrTab = $currTab == 'proof' ? $justificationSelectBuilder : $absenceSelectBuilder;

            if(CheckValidity::isValidDate("POST", "dateStart", "Y-m-d")) {
                $builderCurrTab->dateStart($_POST['dateStart']);
                $filter['dateStart'] = $_POST['dateStart'];
            }
            if(CheckValidity::isValidDate("POST", "dateEnd", "Y-m-d")) {
                $builderCurrTab->dateEnd($_POST["dateEnd"]);
                $filter['dateEnd'] = $_POST['dateEnd'];
            }
            if($currTab === "abs" && CheckValidity::isValidStateAbsence("POST", "state")) {
                $builderCurrTab->state(StateAbs::from($_POST["state"]));
                $filter['state'] = $_POST['state'];
            }
            if($currTab === "proof" && CheckValidity::isValidStateJustification("POST", "state")) {
                $builderCurrTab->state(StateJustif::from($_POST["state"]));
                $filter['state'] = $_POST['state'];
            }
            if(isset($_POST["examen"]) && $_POST["examen"] === "on") {
                $builderCurrTab->examen(true);
                $filter['examen'] = true;
            }
            if($currTab === "abs" && isset($_POST["lock"]) && $_POST["lock"] === "on") {
                $builderCurrTab->lock(true);
                $filter['lock'] = true;
            }
        }

        $justificationSelectBuilder->orderBy(["sendDate"], SortOrder::DESC);
        $absenceSelectBuilder->orderBy(["time"], SortOrder::DESC);

        $absences = $absenceSelectBuilder->execute();
        $justifications = $justificationSelectBuilder->execute();

        return new ControllerData(
            "/View/studentProfile.php",
            "Profil étudiant",
            new StudentProfileViewModel(
                $student,
                $absences,
                $justifications,
                $student->getAbsTotal(),
                $student->getHalfdaysAbsences(),
                $student->getAbsCanBeJustified(),
                $student->getMalusPoints(),
                $student->getMalusPointsWithoutPending(),
                $student->getPenalizingAbsence(),
                $student->getHalfdayPenalizingAbsence(),
                $currTab,
                $filter,
                AuthManager::getRole(),
            )
        );
    }

}