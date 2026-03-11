<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\CommentSelector;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\JustificationSelectBuilder;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\ViewModel\DetailJustificationViewModel;
use Uphf\GestionAbsence\ViewModel\JustificationListViewModel;

/**
 * Controller de vue pour les justificatifs
 *
 *  - GET /justifications -> showJustificationList()
 */
class JustificationController
{
    /**
     * Affiche la vue avec la liste des justificatifs pour le RP
     *
     * @return ControllerData
     */
    public static function showJustificationList(): ControllerData
    {
        $currTab = $_GET['currTab'] ?? 'proofToDo';

        $justificationsToDo = JustificationService::getJustificationsWithFilters(
            ['state' => StateJustif::NotProcessed],
            [
                "columns" => ['sendDate'],
                "sortOrder" => SortOrder::ASC
            ]
        );
        $justificationsDone = JustificationService::getJustificationsWithFilters(
            ['state' => StateJustif::Processed],
            [
                "columns" => ['sendDate'],
                "sortOrder" => SortOrder::DESC
            ]
        );

        return new ControllerData(
            '/View/justificationList.php',
            'Liste des justifications',
            new JustificationListViewModel(
                $currTab,
                AuthManager::getRole(),
                $justificationsToDo,
                $justificationsDone,
                [],
                AuthManager::getAccount()->getFirstName() . ' ' . AuthManager::getAccount()->getLastName()
            )
        );
    }

    /**
     * Affiche le détail d'un justificatif
     *
     * Un étudiant ne peut voir que ses propres justificatifs.
     *
     * @param array $params
     * @return ControllerData
     */
    public static function detailJustificationGet(array $params): ControllerData
    {
        try {
            $justification = JustificationService::getJustificationById((int) $params['id']);
        } catch (EntityNotFoundException) {
            Notification::addNotification(NotificationType::Error, "Le justificatif demandé n'existe pas");
            return ControllerData::get404();
        }

        // Un étudiant ne peut voir que ses propres justificatifs
        if (!AuthManager::isRole(AccountType::EducationalManager)
            && $justification->getStudent()->getIdAccount() != AuthManager::getAccount()->getIdAccount()) {
            Notification::addNotification(NotificationType::Error, "Vous n'avez pas l'autorisation de voir ce justificatif");
            return ControllerData::get403();
        }

        $absences = $justification->getAbsences();
        $files = $justification->getFiles();
        $comments = CommentSelector::getAllComments();

        return new ControllerData(
            '/View/detailJustification.php',
            'Détails de la justification',
            new DetailJustificationViewModel(
                $justification,
                $absences,
                $files,
                AuthManager::getRole(),
                $comments
            )
        );
    }
}
