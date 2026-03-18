<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Utils\Renderer;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

class TestApiController {
    public static function getTest(): void {
        if(isset($_GET['test'])) {
            new ResponseApi(HttpStatus::OK, ["result" => $_GET['test']])->done();
        }
        else {
            new ResponseApi(HttpStatus::BAD_REQUEST, ["result" => "bad_request"])->done();
        }
    }

    public static function postTest(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        Notification::addNotification(NotificationType::Success, "Saucisse");

        new ResponseApi(HttpStatus::OK, ["receive" => $data])->done();
    }

    public static function putTest($params): void {
        $data = json_decode(file_get_contents('php://input'), true);
        new ResponseApi(HttpStatus::OK, ["id" => $params['id'], "receive" => $data])->done();
    }

    public static function deleteTest($params): void {
        new ResponseApi(HttpStatus::OK, ["id" => $params['id']])->done();
    }

    public static function viewTest(): void {
        Notification::addNotification(NotificationType::Error, 'test');
        Renderer::render('test.php', 'titre');
    }
}