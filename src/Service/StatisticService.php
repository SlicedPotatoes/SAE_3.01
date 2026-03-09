<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsBuilder;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Statistics\DataAdapter;
use Uphf\GestionAbsence\Model\Validation\FilterProportionStatisticsValidator;

class StatisticService {
    /**
     * @param $idStudent
     * @return array
     * @throws EntityNotFoundException, elle se déclenche lorsque l'étudiant recherché n'est pas dans la base de donnée
     */


    public static function getStudentStatistic ($idStudent){
        $student = StudentSelector::getStudentById($idStudent);

        //Véridication de la présencde de l'étudiant dans la base de donné
        if($student == null){
            throw new EntityNotFoundException();
        }

        // Création des builders pour chaque type de statistique
        $builders = [];
        foreach (ProportionStatisticsType::getAll() as $type){
            $builders[$type->value] = new ProportionStatisticsBuilder()->type($type);
        }

        // Application des filtres
        $filters = new FilterProportionStatisticsValidator()->getData();

        $whiteListMethode = ['group', 'examen'];
        foreach ($filters as $filter => $value){
            if(isset($value) && in_array($value, $whiteListMethode)){
                foreach ($builders as $type => $builders){
                    call_user_func([$builders, $filter], $value);
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
                $builder->idStudent($idStudent)->execute(),
                ProportionStatisticsType::from($type)->callableLabelFormat(),
                ProportionStatisticsType::from($type)->callableColorPie()
            );
        }

        return $datas;
    }
}