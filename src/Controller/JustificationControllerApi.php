<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

class JustificationControllerApi {
    public static function getJustificationList(): void {
        $filters = $_GET['filters'] ?? [];
        $orderOptions = $_GET['orderOptions'] ?? [];
        if(isset($filters['state']) && StateJustif::tryFrom($filters['state']) !== null) {
            $filters['state'] = StateJustif::from($filters['state']);
        }
        if(isset($orderOptions['sortOrder']) && SortOrder::tryFrom($orderOptions['sortOrder']) !== null) {
            $orderOptions['sortOrder'] = SortOrder::from($orderOptions['sortOrder']);
        }

        try {
            $justifications = JustificationService::getJustificationsWithFilters($filters, $orderOptions);
            new ResponseApi(HttpStatus::OK, $justifications)->done();
        }
        catch (\InvalidArgumentException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
            new ResponseApi(HttpStatus::BAD_REQUEST, [])->done();
        }


    }
}