<?php

namespace Uphf\GestionAbsence\ViewModel;

readonly class GeneralStatisticsViewModel extends BaseViewModel
{
    public HeaderViewModel $headerVM;

    public function __construct() {
        $this->headerVM = new HeaderViewModel(false, "Vous pouvez observer les différentes", "Statistiques", "");
    }

}