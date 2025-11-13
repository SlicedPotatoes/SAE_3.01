<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\JustificationSelectBuilder;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\CheckValidity;
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

        $currTab = 'proofToDo'; // CurrTab par défaut

        // Builder pour récupérer les justificatifs
        $justificationToDoBuilder = new JustificationSelectBuilder()->state(StateJustif::NotProcessed);
        $justificationDoneBuilder = new JustificationSelectBuilder()->state(StateJustif::Processed);

        $filter = []; // Filtre appliqué a la currTab

        // Gestion des filtres pour la currTab, vérifier s'ils sont envoyé via POST, que les valeurs sont correctes et les appliqués au builder
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            if(isset($_POST['currTab']) && $_POST['currTab'] == 'proofDone') { $currTab = 'proofDone'; }
            $builderCurrTab = $currTab == 'proofToDo' ? $justificationToDoBuilder : $justificationDoneBuilder;

            if(CheckValidity::isValidDate("POST", "dateStart", "Y-m-d")) {
                $builderCurrTab->dateStart($_POST['dateStart']);
                $filter['dateStart'] = $_POST['dateStart'];
            }
            if(CheckValidity::isValidDate("POST", "dateEnd", "Y-m-d")) {
                $builderCurrTab->dateEnd($_POST["dateEnd"]);
                $filter['dateEnd'] = $_POST['dateEnd'];
            }
            if(isset($_POST["examen"]) && $_POST["examen"] === "on") {
                $builderCurrTab->examen();
                $filter['examen'] = true;
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
                $filter,
                AuthManager::getAccount()->getFirstName() . " " . AuthManager::getAccount()->getLastName()
            )
        );
    }
}