<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\TimeSlotAbsenceSelector;
use Uphf\GestionAbsence\ViewModel\ResitSessionListViewModel;


/**
 * Controller pour la liste des sessions de rattrapage
 */
class ResitSessionListController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'est pas enseignant, RP ou Secretaire => 403
     *
     * @return ControllerData
     */
    public static function show(): ControllerData {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // L'utilisateur n'est pas enseignant, RP ou Secretaire => 403
        if(!AuthManager::isRole(\Uphf\GestionAbsence\Model\Entity\Account\AccountType::Teacher) &&
           !AuthManager::isRole(\Uphf\GestionAbsence\Model\Entity\Account\AccountType::EducationalManager) &&
           !AuthManager::isRole(\Uphf\GestionAbsence\Model\Entity\Account\AccountType::Secretary)) {
            return ControllerData::get403();
        }

        // Si l'utilisateur est enseignant, récupérer ses sessions de rattrapage
        $Sessions = [];
        $NbJustfiedAbsencesPerSession = [];
        if(AuthManager::isRole(\Uphf\GestionAbsence\Model\Entity\Account\AccountType::Teacher)) {
            $teacher = AuthManager::getCurrentUser();
            $Sessions = TimeSlotAbsenceSelector::selectTimeSlotAbsence($teacher->getIdAccount(), true, null, null);
            foreach ($Sessions as $key => $session) {
                if ($session->getCountStudentsAbsences() > 0) {
                    $NbJustfiedAbsencesPerSession[$session->getTime()->format('Y-m-d H:i:s')] = $session->countJustifiedAbsences();
                }
            }
        }

        // Si l'utilisateur est RP ou Secretaire, récupérer toutes les sessions de rattrapage
        else {
            $Sessions = TimeSlotAbsenceSelector::selectTimeSlotAbsence(null, true, null, null);

            foreach ($Sessions as $key => $session) {
                if ($session->getCountStudentsAbsences() > 0) {
                    $NbJustfiedAbsencesPerSession[$session->getTime()->format('Y-m-d H:i:s')] = $session->countJustifiedAbsences();
                }
            }

        }

        return new ControllerData(
            "/View/resitSessionList.php",
            "Liste des sessions de rattrapage",
            new ResitSessionListViewModel(
                AuthManager::getRole(),
                $Sessions,
                $NbJustfiedAbsencesPerSession
            )
        );
    }
}
