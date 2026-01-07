<?php

namespace Uphf\GestionAbsence\Model\Validation;

class CommentValidator
{
    private ?array $input = null;

    public function __construct()
    {
        $this->input = filter_input_array(INPUT_POST,
            [
                "action" => FILTER_SANITIZE_SPECIAL_CHARS,
                "idComment" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "flags" => FILTER_NULL_ON_FAILURE
                ],
                "textComment" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'stringOrNull']
               ]
            ]
        );

    }
    /**
     * Vérifie que les données soient valide selon l'action demandée
     *
     * @return array Tableau d'erreurs (vide si y'a R)
     */
    public function checkAllGood(): array
    {
        if (!isset($this->input)) {
            return ["Impossible de traiter votre demande, veuillez contacter l'administrateur"];
        }

        // Vérifier que l'action est présente et valide
        if (!isset($this->input['action']) || !in_array($this->input['action'], ['add', 'edit', 'delete'])) {
            return ["Action invalide ou manquante"];
        }

        $action = $this->input['action'];

        $errors = [];

        // Pour add et edit, le texte est obligatoire
        if (in_array($action, [ 'edit', 'add'])) {
            $errors = array_merge($errors, ValidationHelper::validateRequired($this->input,
                ['textComment'],
                ["Le commentaire est manquant"]
            ));
        }
        return $errors;
    }
    public function getData(): ?array
    {
        return $this->input;
    }
}
