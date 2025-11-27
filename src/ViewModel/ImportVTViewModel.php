<?php

namespace Uphf\GestionAbsence\ViewModel;

readonly class ImportVTViewModel extends BaseViewModel
{

    public HeaderViewModel $headerVM;

    public function __construct()
    {
        $this->headerVM = new HeaderViewModel(
          false,
          "Importation des",
          "absences",
          "depuis VT",
        );
    }

}