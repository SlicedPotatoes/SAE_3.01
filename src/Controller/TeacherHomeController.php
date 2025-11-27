<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;
use Uphf\GestionAbsence\ViewModel\TeacherHomeViewModel;

class TeacherHomeController {

    public static function show(): ControllerData {
        // Vérification de la connection de l'utilisateur
        if (!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }
        // Vérification de la permission de l'utilisateur
        if (!AuthManager::isRole(AccountType::Teacher))
        {
            return ControllerData::get403();
        }

        // TODO: remplacer par récupération réelle depuis les modèles / AuthManager
        $courses = [];     // ex: CourseManager::getCoursesByTeacher(...)

        /** Placeholder pour l'array de period */
        $periods = [
          (object) [
            'idPeriod'       => 1,
            'date'           => '15/01/2025',
            'time'           => '08h00',
            'absencesCount'  => 2,
            'course'         => 'Programmation orientée objet',
            'group'          => 'BUT2 INFO A1',
            'isExam'         => false,
          ],
          (object) [
            'idPeriod'       => 2,
            'date'           => '15/01/2025',
            'time'           => '9h30',
            'absencesCount'  => 1,
            'course'         => 'Base de données avancées',
            'group'          => 'BUT2 INFO A2',
            'isExam'         => true,
          ],
          (object) [
            'idPeriod'       => 3,
            'date'           => '16/01/2025',
            'time'           => '14h00',
            'absencesCount'  => 5,
            'course'         => 'Réseaux et systèmes',
            'group'          => 'BUT2 INFO A1',
            'isExam'         => false,
          ],
        ];
        /** FIN du placeholder */

        return new ControllerData(
            "View/teacherHome.php",
            "Tableau de bord Professeur",
            new TeacherHomeViewModel(
              $periods,
              'absence',
              AuthManager::getAccount()->getFirstName() . " " . AuthManager::getAccount()->getLastName()
            )
        );
    }

}