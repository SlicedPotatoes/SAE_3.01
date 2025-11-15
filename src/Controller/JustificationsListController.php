<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\JustificationSelectBuilder;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Validation\FilterJustificationValidator;
use Uphf\GestionAbsence\ViewModel\JustificationListViewModel;

/**
 * Controller pour la liste des justificatifs coté RP
 */
class JustificationsListController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'est pas RP => 403
     *
     * Gestion des filtres appliquée
     *
     * @return ControllerData
     */
    public static function show(): ControllerData {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // L'utilisateur n'est pas RP => 403
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        // Builder pour récupérer les justificatifs
        $justificationToDoBuilder = new JustificationSelectBuilder()->state(StateJustif::NotProcessed);
        $justificationDoneBuilder = new JustificationSelectBuilder()->state(StateJustif::Processed);

        // Récupération des filtres
        $filters = new FilterJustificationValidator()->getData() ?? [];
        $currTab = $_POST['currTab'] ?? 'proofToDo';

        // Application des filtres
        $whiteListMethod = ['dateStart', 'dateEnd', 'examen'];
        $builderCurrTab = $currTab == 'proofToDo' ? $justificationToDoBuilder : $justificationDoneBuilder;
        foreach($filters as $filter => $value) {
            if(isset($value) && in_array($filter, $whiteListMethod)) {
                call_user_func([$builderCurrTab, $filter], $value);
            }
        }

        $justificationToDoBuilder->orderBy(["sendDate"], SortOrder::ASC);
        $justificationDoneBuilder->orderBy(["sendDate"], SortOrder::DESC);

        $justificationsToDo = $justificationToDoBuilder->execute();
        $justificationsDone = $justificationDoneBuilder->execute();

        return new ControllerData(
            "/View/justificationList.php",
            "Liste des justifications",
            new JustificationListViewModel(
                $currTab,
                AuthManager::getRole(),
                $justificationsToDo,
                $justificationsDone,
                $filters,
                AuthManager::getAccount()->getFirstName() . " " . AuthManager::getAccount()->getLastName()
            )
        );
    }
}