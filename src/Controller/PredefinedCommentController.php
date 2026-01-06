<?php
namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Delete\CommentDelete;
use Uphf\GestionAbsence\Model\DB\Insert\CommentInsertor;
use Uphf\GestionAbsence\Model\DB\Select\CommentSelector;
use Uphf\GestionAbsence\Model\DB\Update\CommentUpdater;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\CommentValidator;
use Uphf\GestionAbsence\ViewModel\JustificationListViewModel;
use Uphf\GestionAbsence\ViewModel\PredefinedCommentViewModel;


class PredefinedCommentController {

    public static function show(): ControllerData{
        // Vérifier que l'utilisateur est connecté
        if (!AuthManager::isLogin()) {
            header("location: /");
            exit();
        }

        // Vérifier que l'utilisateur est RP
        if (!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        // Si requête POST, faire l'action
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            self::handleAction();
        }
        // Récupérer tous les commentaires
        $comments = CommentSelector::getAllComments();


        return new ControllerData(
            "/View/predefinedComments.php",
            "Liste des commentaires prédéfinis",
            new PredefinedCommentViewModel(
                AuthManager::getRole(),
                $comments,

            )
        );

    }
    private static function handleAction(): void {

        $validator = new CommentValidator();
        $errors = $validator->checkAllGood();
        // Si erreurs de validation, afficher et arrêter
        if (!empty($errors)) {
            foreach ($errors as $e) {
                Notification::addNotification(NotificationType::Error, $e);
            }
            return;
        }

        $data = $validator->getData();
        $action = $data["action"];

        // Fais l'action sur le commentaire prédéfinis
        switch($action) {
            case "add":
                if(CommentInsertor::insert($data['textComment'])){
                    Notification::addNotification(NotificationType::Success, "Commentaire ajouté avec succès");
                } else {
                    Notification::addNotification(NotificationType::Error,"Erreur lors de l'ajout de la commentaire");
                }
                break;

            case "delete":
                if(CommentDelete::delete($data['idComment'])){
                    Notification::addNotification(NotificationType::Success,"Commentaire bien supprimé");
                } else {
                    Notification::addNotification(NotificationType::Error,"Erreur lors de la suppression de la commentaire");
                }
                break;

            case "edit":
                if(CommentUpdater::update($data['idComment'], $data['textComment'])){
                    Notification::addNotification(NotificationType::Success,"Commentaire bien modifié");
                } else {
                    Notification::addNotification(NotificationType::Error,"Erreur lors de la modification de la commentaire");
                }
                break;
        }

    }
}