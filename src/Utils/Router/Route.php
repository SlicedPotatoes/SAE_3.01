<?php

namespace Uphf\GestionAbsence\Utils\Router;

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

/**
 * Classe définissant une route avec les permissions et le handler.
 */
class Route {
    private string $name;
    private string $handler;
    private array $authorization = [];
    private bool $requireNotLogin = false;
    private bool $requireLogin = false;

    /**
     * Constructeur avec le handler de la route.
     *
     * @param string $handler
     */
    public function __construct(string $name, string $handler) {
        $this->name = $name;
        $this->handler = $handler;
    }

    /**
     * Ajouter un type de compte autorisé à accéder à la route
     *
     * Dans le cas ou pour une route aucun type de compte n'a été déclaré comme autorisé, alors tous les comptes peuvent accéder à celle-ci.
     *
     * @param AccountType $at
     * @return $this
     */
    public function addAuthorization(AccountType $at): Route {
        $this->authorization[] = $at;
        return $this;
    }

    /**
     * Impose à l'utilisateur de ne pas être connecté pour accéder à la route.
     * @return $this
     */
    public function requireNotLogin(): Route {
        if($this->requireLogin) {
            throw new \RuntimeException("requireNotLogin is incompatible with requireLogin");
        }

        $this->requireNotLogin = true;
        return $this;
    }

    /**
     * Impose à l'utilisateur d'être connecté pour accéder à la route.
     * @return $this
     */
    public function requireLogin(): Route {
        if($this->requireNotLogin) {
            throw new \RuntimeException("requireNotLogin is incompatible with requireLogin");
        }

        $this->requireLogin = true;
        return $this;
    }

    /**
     * Récupérer le nom de la route
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Récupérer le handler de la route
     * @return string
     */
    public function getHandler(): string {
        return $this->handler;
    }

    /**
     * Récupérer les roles autorisés à accéder à la route.
     * @return array
     */
    public function getAuthorization(): array {
        return $this->authorization;
    }

    /**
     * Récupérer si la route requis la non connection de l'utilisateur.
     * @return bool
     */
    public function getRequireNotLogin(): bool {
        return $this->requireNotLogin;
    }

    /**
     * Récupérer si la route requis la connection de l'utilisateur.
     * @return bool
     */
    public function getRequireLogin(): bool {
        return $this->requireLogin;
    }
}