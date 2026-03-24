<?php

namespace Uphf\GestionAbsence\Utils\ResponseApi;

use Uphf\GestionAbsence\Model\Notification\Notification;

/**
 * Classe utilitaire permettant de définir la réponse de l'api REST
 */
class ResponseApi {
    private HttpStatus $status;
    private mixed $data;

    public function __construct(HttpStatus $status, mixed $data = []) {
        $this->status = $status;
        $this->data = $data;
    }

    public function done(): void {
        http_response_code($this->status->value);
        header('Content-Type: application/json; charset=utf-8');

        $responseArray = ["data" => $this->data];

        if(!empty(Notification::getNotifications())) {
            $responseArray["messages"] = Notification::jsonSerializeAll();
        }

        echo json_encode($responseArray);
    }
}