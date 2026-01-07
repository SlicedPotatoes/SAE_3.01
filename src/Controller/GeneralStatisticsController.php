<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\GroupStudentSelector;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsBuilder;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Statistics\DataAdapter;
use Uphf\GestionAbsence\Model\Validation\FilterProportionStatisticsValidator;
use Uphf\GestionAbsence\ViewModel\GeneralStatisticsViewModel;

/**
 * Controller pour la page de statistique globale
 */
class GeneralStatisticsController
{
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'est pas RP => 403
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

        // Si l'utilisateur n'est pas un Responsable Pédagogique, il est redirigé vers une page 403
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
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
            $datas[$type] = DataAdapter::proportionAdapter(
                $builder->execute(),
                ProportionStatisticsType::from($type)->callableLabelFormat(),
                ProportionStatisticsType::from($type)->callableColorPie()
            );
        }

        $currTab = isset($_POST['currTab']) ? ProportionStatisticsType::tryFrom($_POST['currTab']) : null;
        $currTab ??= ProportionStatisticsType::getAll()[0];

        $groups = GroupStudentSelector::getAllGroup();

        //echo "<pre>"; var_export($_POST); echo "</pre>";
        //echo "<pre>"; var_export($filters); echo "</pre>";

        return new ControllerData(
            "/View/generalStatistics.php",
            "Statistiques",
            new GeneralStatisticsViewModel($datas, $currTab, $groups, $filters)
        );
    }
}