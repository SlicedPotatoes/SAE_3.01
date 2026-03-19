<?php

namespace Uphf\GestionAbsence\Utils\Router;

use RuntimeException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Routeur basique permettant de rediriger vers la méthode d'un controller
 */
class Router {
    private static Route $currRoute;
    private array $routes = [];

    /**
     * Permet de définir des routes
     *
     * Une route est composée d'une méthode, d'un chemin et d'un handler
     *
     * Un handler est une chaine qui représente la classe d'un controller et la méthode au format suivant :
     * "MonController@Method".
     *
     * @param RequestMethod $requestMethod
     * @param string $path
     * @param string $handler
     * @param string $name
     * @return Route
     */
    public function addRoute(RequestMethod $requestMethod, string $path, string $handler, string $name = ''): Route {
        $route = new Route($name, $handler);
        $this->routes[$requestMethod->name][$this->normalizePath($path)] = $route;

        return $route;
    }

    /**
     * Prend un chemin en paramètre, et exécute le bon handler de ce chemin
     * @param $path
     * @return void
     */
    public function launch($path): void {
        $path = $this->normalizePath($path);

        // Parcours de l'ensemble des routes REQUEST_METHOD pour chercher une correspondance avec path
        foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $pattern => $route) {
            $params = $this->matchPattern($pattern, $path);

            if(!$params && !is_array($params)) { continue; }

            // Il y a un match, vérification des authorisations
            if(!$this->checkAuthorization($route)) {
                Renderer::render403();
                return;
            }

            self::$currRoute = $route;

            // Appel de la méthode du controller
            $handler = $route->getHandler();
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

            $class::$method($params);
            return;
        }

        Renderer::render404();
    }

    /**
     * Vérifie les autorisations pour accéder à la route demandée
     *
     * Dans le cas où la route requis un utilisateur non connecté et que celui-ci l'est, renvoie vers sa page Home
     *
     * Dans le cas où la route requis un utilisateur connecté et que celui-ci ne l'est pas, renvoie vers le login
     *
     * Dans le cas où un login est requis et qu'aucun type de compte n'a été définie dans la route, alors tout type de compte est autorisé à y accéder.
     *
     * Renvoie true si l'utilisateur peut accéder à la route, sinon false
     *
     * @param $route
     * @return bool
     */
    private function checkAuthorization($route): bool {
        // Route ne requis pas de login, mais utilisateur connecté
        if($route->getRequireNotLogin() && AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        if($route->getRequireLogin()) {
            // Route requis un login, mais utilisateur non connecté
            if(!AuthManager::isLogin()) {
                header("Location: /");
                exit();
            }

            $authorization = $route->getAuthorization();
            // Tout type de compte peut accéder à cette page
            if(empty($authorization)) {
                return true;
            }

            // Check si l'utilisateur à un role autorisant l'accès à cette page
            foreach ($authorization as $role) {
                if(AuthManager::isRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return true;
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
                $paramName = null;
                $paramType = null;

                // Si un type est spécifié pour le paramètre
                if(str_contains($patternToken, ':')) {
                    $splitedToken = explode(':', $patternToken);

                    $paramName = substr($splitedToken[0], 1);
                    $paramType = substr($splitedToken[1], 0, -1);
                }
                else {
                    $paramName = substr($patternToken, 1, -1);
                }


                // Le token a un type spécifier, on check si le paramètre correspond au type
                if(isset($paramType)) {
                    if($paramType === 'int' && filter_var($pathToken, FILTER_VALIDATE_INT) === false) {
                        return false;
                    }
                }

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

    /**
     * Renvoie true si la route courante porte le nom passé en paramètre
     * sinon false
     *
     * @param $name
     * @return bool
     */
    public static function isCurrRoute($name): bool {
        if(self::$currRoute === null) { return false; }
        return self::$currRoute->getName() === $name;
    }
}