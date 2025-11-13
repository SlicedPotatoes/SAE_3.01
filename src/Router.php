<?php

namespace Uphf\GestionAbsence;

use Uphf\GestionAbsence\Controller\ControllerData;
use RuntimeException;

/**
 * Routeur basique permettant de rediriger vers le bon controller
 */
class Router {
    //Représente la route par défault, devrais être changé en prod par / si server bien configuré
    private array $routes = [];

    /**
     * Permet de définir des routes
     *
     * Une route est une paire composée d'un chemin et d'un handler
     *
     * Un handler est une chaine qui représente la classe d'un controller et la méthode au format suivant:
     * "MonController@Method"
     *
     * @param string $path
     * @param string $handler
     * @return void
     */
    public function addRoute(string $path, string $handler): void {
        $this->routes[$this->normalizePath($path)] = $handler;
    }

    /**
     * Prend un chemin en paramètre, et exécute le bon handler de ce chemin
     * @param $path
     * @return ControllerData
     * @throws RuntimeException Si le format de l'handler correspondant à la route est invalide
     * @throws RuntimeException Si la classe du controller n'a pas été trouvé
     * @throws RuntimeException Si la méthode du controller n'a pas été trouvé
     */
    public function launch($path): ControllerData {
        $path = $this->normalizePath($path);

        // Parcours de l'ensemble des routes pour chercher une correspondance avec path
        foreach($this->routes as $pattern => $handler) {
            $params = $this->matchPattern($pattern, $path);

            if(!$params && !is_array($params)) { continue; }

            // Il y a un match, appel de l'handler

            if(!is_string($handler) || !str_contains($handler, '@')) {
                throw new RuntimeException("Le format de l'handler est invalide pour cette route: $handler");
            }

            [$class, $method] = explode('@', $handler);
            $class = "Uphf\\GestionAbsence\\Controller\\" . $class;

            if(!class_exists($class)) {
                throw new RuntimeException("La classe Controller $class n'a pas été trouvé.");
            }
            if(!method_exists($class, $method)) {
                throw new RuntimeException("La méthode $method n'exite pas dans le controller $class.");
            }

            return call_user_func([$class, $method], $params);
        }

        return ControllerData::get404();
    }

    /**
     * Compare un pattern (par exemple /DetailJustificationViewModel/{id})
     * avec un path (par exemple /DetailJustificationViewModel/10).
     *
     * Retourne un tableau de paramètre s'il y a match, false sinon
     *
     * @param string $pattern
     * @param string $path
     * @return array|bool
     */
    private function matchPattern(string $pattern, string $path): array | bool {
        $patternSegment = explode('/', $pattern);
        $pathSegment = explode('/', $path);

        // Le nombre de segments pour le pattern et path est différent, donc pas match
        if(count($patternSegment) != count($pathSegment)) { return false; }

        $params = [];

        for($i = 0; $i < count($pathSegment); $i++) {
            $patternToken = $patternSegment[$i];
            $pathToken = $pathSegment[$i];

            // Si le token de pattern est un paramètre
            if(str_starts_with($patternToken, '{') && str_ends_with($patternToken, '}')) {
                $paramName = substr($patternToken, 1, -1);

                // Le token de path n'est pas un nombre, donc pas match
                if(filter_var($pathToken, FILTER_VALIDATE_INT) === false) { return false; }

                $params[$paramName] = $pathToken;
            }
            // Si token de pattern n'est pas un paramètre et qu'il est différent du token de path, il n'y a pas match
            else if($patternToken !== $pathToken) {
                return false;
            }
        }

        return $params;
    }

    /**
     * Enlever les espaces et le / à la fin d'un path
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string {
        if($path != '/') {
            $path = rtrim($path, '/');
        }
        return $path;
    }
}