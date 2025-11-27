<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;


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
        $teacherSessions = [];
        if(AuthManager::isRole(\Uphf\GestionAbsence\Model\Entity\Account\AccountType::Teacher)) {
            $teacher = AuthManager::getCurrentUser();
            $teacherSessions = $teacher->getResitSessions(); // à modifier pour récupérer les sessions de rattrapage
        }


    }
}
