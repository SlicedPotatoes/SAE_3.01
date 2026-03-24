<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Service\PredifinedCommentService;
use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller de vue pour les justificatifs
 *
 *  - GET /justifications -> showJustificationList()
 *  - GET /detail-justification/{id} -> showDetailJustification()
 */
class JustificationController
{
    /**
     * Affiche la vue avec la liste des justificatifs pour le RP
     *
     * @return void
     */
    public static function showJustificationList(): void {
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

        Renderer::render(
            '/justificationDashboard.php',
            'Liste des justifications',
            [
                'listToDo' => $justificationsToDo,
                'listDone' => $justificationsDone,
                'showState' => false
            ]
        );
    }

    /**
     * Affiche le détail d'un justificatif
     *
     * Un étudiant ne peut voir que ses propres justificatifs.
     *
     * @param array $params
     * @return void
     */
    public static function showDetailJustification(array $params): void {
        try {
            $justification = JustificationService::getJustificationById((int) $params['id']);

            // Un étudiant ne peut voir que ses propres justificatifs
            if (!AuthManager::isRole(AccountType::EducationalManager)
                && JustificationService::isJustificationOwnedByStudent(AuthManager::getAccount(), $justification)) {
                throw new \Exception();
            }

            $absences = $justification->getAbsences();
            $files = $justification->getFiles();
            $comments = [];

            // Récupération des commentaires prédéfinie seulement quand RP
            if(AuthManager::isRole(AccountType::EducationalManager)) {
                $comments = PredifinedCommentService::commentSelectorAll();
            }

            /*
            // DEBUG
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($_POST);
                exit();
            }*/

            Renderer::render(
                '../ViewOLD/detailJustification.php',
                'Détails de la justification',
                [
                    'justification' => $justification,
                    'absences' => $absences,
                    'files' => $files,
                    'comments' => $comments
                ]
            );
        }
        catch (EntityNotFoundException) {
            Notification::addNotification(NotificationType::Error, "Le justificatif demandé n'existe pas");
            Renderer::render404();
        }
        catch (\Exception $e) {
            Notification::addNotification(NotificationType::Error, "Vous n'avez pas l'autorisation de voir ce justificatif");
            Renderer::render403();
        }
    }
}
