<?php

namespace Uphf\GestionAbsence\Router;

/**
 * Énumération définissant les différents types de request, utilisé pour définir les routes proprement *
 */
enum RequestMethod {
    case GET;
    case POST;
    case PUT;
    case DELETE;
}
