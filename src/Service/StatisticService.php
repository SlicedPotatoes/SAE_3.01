<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsBuilder;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Database\Select\StudentSelector;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Statistics\DataAdapter;

class StatisticService {
    /**
     * @param array $filters filtre au format "key" => "value", les keys acceptés sont : "group", "examen" et "idStudent"
     * @return array
     * @throws EntityNotFoundException Dans le cas ou un filtre sur l'étudiant est appliqué, ce déclanche si celui-ci n'existe pas
     */
    public static function getStatistic (array $filters): array {
        // Si un filtre est appliqué sur l'étudiant, vérifie ça présence dans la BDD
        if(isset($filters['idStudent'])) {
            $student = StudentSelector::getStudentById($filters['idStudent']);
            if($student === null){
                throw new EntityNotFoundException();
            }
        }

        // Création des builders pour chaque type de statistique
        $builders = [];
        foreach (ProportionStatisticsType::getAll() as $type){
            $builders[$type->value] = new ProportionStatisticsBuilder()->type($type);
        }

        // Application des filtres
        $whiteListMethode = ['group', 'examen', 'idStudent', 'state'];
        foreach ($filters as $filter => $value){
            if($value !== null && in_array($filter, $whiteListMethode)){
                foreach ($builders as $type => $builder){
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

        return $datas;
    }
}