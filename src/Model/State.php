<?php
class State {
    private $id;
    private $label;

    public function __construct($id, $label) {
        $this->id = $id;
        $this->label = $label;
    }

    public function getId() { return $this->id; }
    public function getLabel() { return $this->label; }

    /*
     * TODO : Connecter a la base de données
     * Actuellement données factices
     * Important de faire que le 1er élément qui est renvoyé dans le tableau soit "new State(-1, "Tous")"
    */
    public static function getAbsenceStates() {
        return array(new State(-1, "Tous"), new State(0, "Justifié"), new State(1, "Non-justifié"), new State(2, "En attente"));
    }

    public static function getJustificationStates() {
        return array(new State(-1, "Tous"), new State(0, "Traité"), new State(1, "En attente"));
    }
}