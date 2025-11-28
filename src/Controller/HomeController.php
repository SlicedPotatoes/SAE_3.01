<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;

/**
 * Controller de redirection pour la route /
 *
 * Gère la redirection en fonction de différents critères:
 * - Si l'utilisateur est connecté ou non
 * - Le type de compte
 */
class HomeController {

    /**
     * Redirige l'utilisateur vers sa page par défaut selon son role
     *
     * Si l'utilisateur n'est pas connecté, il est redirigé vers le login
     * @return ControllerData
     */
    public static function home(): ControllerData {
        if(AuthManager::isLogin()) {
            switch(AuthManager::getRole()) {
                case AccountType::Student:
                    header("Location: /StudentProfile");
                    exit();
                case AccountType::EducationalManager:
                    header("Location: /JustificationList");
                    exit();
                case AccountType::Teacher:
                    header("Location: /teacherHome");
                    exit();
                default:
                    Notification::addNotification(NotificationType::Error, "Ce type de compte n'est pas encore fonctionnel");
                    return ControllerData::get403();
            }
        }
        else {
            header("Location: /login");
            exit();
        }
    }
}