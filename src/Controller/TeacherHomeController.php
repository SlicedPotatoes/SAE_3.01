<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;
use Uphf\GestionAbsence\ViewModel\TeacherHomeViewModel;

class TeacherHomeController {

    public static function show(): ControllerData {
        // Vérification d'authentification active
        if (!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // TODO: remplacer par récupération réelle depuis les modèles / AuthManager
        $teacherName = ''; // ex: AuthManager::getCurrentUserName() si disponible
        $courses = [];     // ex: CourseManager::getCoursesByTeacher(...)

        return new ControllerData(
            "View/TeacherHome.php",
            "Tableau de bord Proffesseur",
            new TeacherHomeViewModel()
        );
    }

}