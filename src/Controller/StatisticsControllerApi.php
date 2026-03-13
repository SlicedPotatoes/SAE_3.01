<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Service\StatisticService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;


class StatisticsControllerApi {
    public static function getStatistics(): void {
        try {
            $filters = $_GET;

            $statistics = StatisticService::getStatistic($filters);

            new ResponseApi(
                HttpStatus::OK,
                $statistics
            )->done();
        } catch (\Exception $e) {
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array)$e->getMessage()
            )->done();
        }
    }

}