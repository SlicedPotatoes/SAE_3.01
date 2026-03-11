<?php

namespace Uphf\GestionAbsence\Utils\ResponseApi;

/**
 * Représente les status HTTP d'une réponse
 */
enum HttpStatus: int {
    CASE OK = 200;
    CASE CREATED = 201;
    CASE NO_CONTENT = 204;
    CASE BAD_REQUEST = 400;
    CASE FORBIDDEN = 403;
    CASE NOT_FOUND = 404;
}