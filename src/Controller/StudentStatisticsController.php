<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\GroupStudentSelector;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsBuilder;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Model\DB\Select\StudentSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Statistics\DataAdapter;
use Uphf\GestionAbsence\Model\Validation\FilterProportionStatisticsValidator;
use Uphf\GestionAbsence\ViewModel\StudentStatisticsViewModel;

/**
 * Controller pour la page de statistique etudiante
 */
class StudentStatisticsController
{

    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'est pas RP => 403
     *
     * Si l'étudiant n'existe pas => 404
     *
     * Gestion des filtres appliquée
     *
     * @return ControllerData
     */
    public static function show(array $params): ControllerData
    {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // Si l'utilisateur n'est pas un Responsable Pédagogique il est redirigé vers une page 403
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        $student = StudentSelector::getStudentById($params['id']);

        // Si il n'y a pas d'étudiant avec l'id, l'utilisateur est redigiré vers une page 404
        if($student === null) {
            Notification::addNotification(NotificationType::Error, "L'étudiant demandé n'existe pas");
            return ControllerData::get404();
        }

        // Création des builders pour chaque type de statistique
        $builders = [];
        foreach(ProportionStatisticsType::getAll() as $type) {
            $builders[$type->value] = new ProportionStatisticsBuilder()->type($type);
        }

        // Application des filtres
        $filters = new FilterProportionStatisticsValidator()->getData();

        $whiteListMethod = ['group', 'examen'];
        foreach($filters as $filter => $value) {
            if(isset($value) && in_array($filter, $whiteListMethod)) {
                foreach($builders as $type => $builder) {
                    call_user_func([$builder, $filter], $value);
                }
            }
        }

        // Récupération des données depuis la base
        $datas = [];
        foreach($builders as $type => $builder) {
            $datas['global'][$type] = DataAdapter::proportionAdapter(
                $builder->execute(),
                ProportionStatisticsType::from($type)->callableLabelFormat(),
                ProportionStatisticsType::from($type)->callableColorPie()
            );

            $datas['student'][$type] = DataAdapter::proportionAdapter(
                $builder->idStudent($params['id'])->execute(),
                ProportionStatisticsType::from($type)->callableLabelFormat(),
                ProportionStatisticsType::from($type)->callableColorPie()
            );
        }

        $currTab = isset($_POST['currTab']) ? ProportionStatisticsType::tryFrom($_POST['currTab']) : null;
        $currTab ??= ProportionStatisticsType::getAll()[0];

        $groups = GroupStudentSelector::getAllGroup();

        return new ControllerData(
            "/View/studentStatistics.php",
            "Statistiques",
            new StudentStatisticsViewModel($student, $datas, $currTab, $groups, $filters)
        );
    }
}