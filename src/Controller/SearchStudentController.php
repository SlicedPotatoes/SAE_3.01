<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Service\GroupService;
use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller pour la recherche d'étudiant coté RP
 */
class SearchStudentController {
    /**
     * Page de recherche étudiant
     *
     * @return void
     */
    public static function showSearchStudent(): void {
        $groupsStudent = GroupService::selectAllGroup();

        Renderer::render('searchStudent.php',
            'Recherche étudiant',
            ["groupStudent" => $groupsStudent]
        );
    }
}