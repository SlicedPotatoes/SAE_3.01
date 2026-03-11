<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Service\StatisticService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;


class StatisticsControllerApi {

    /**
     * @param $filter
     * @return void
     * @throws \Uphf\GestionAbsence\Exception\EntityNotFoundException
     */
    public static function getStatistics($filters): void {

        $statistics = StatisticService::getStatistic($filters);

        new ResponseApi(
            HttpStatus::OK,
            ['result' => $statistics],
        )->done();

    }

}