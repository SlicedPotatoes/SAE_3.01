<?php

namespace Uphf\GestionAbsence\ViewModel;

/**
 * Classe de base pour un ViewModel
 * - Il y a trois découpage pour avoir la possibilité d'avoir une partie du texte de l'header de la couleur de l'uphf
 */
readonly class HeaderViewModel extends BaseViewModel {
    public String $firstPartMessage;
    public String $secondPartMessage;
    public String $thirdPartMessage;

    /**
     * @var bool
     * Permets ou non d'afficher les cards du profile étudiant
     * @false N'affiche pas les cards du profile étudiant
     * @true Affiche les cards du profile étudiant
     */
    public bool $showCards;

    public function __construct($showCards, $firstPartMessage, $secondPartMessage, $thirdPartMessage)
    {
        $this->firstPartMessage = $firstPartMessage;
        $this->secondPartMessage = $secondPartMessage;
        $this->thirdPartMessage = $thirdPartMessage;
        $this->showCards = $showCards;
    }
}