<?php

namespace Uphf\GestionAbsence\ViewModel;

readonly class ChangePasswordViewModel extends BaseViewModel {
    public bool $haveToken;
    public HeaderViewModel $headerVM;

    public function __construct(bool $haveToken) {
        $this->haveToken = $haveToken;
        $this->headerVM = new HeaderViewModel(false, "Changer votre", "mot de passe", "!");
    }
}