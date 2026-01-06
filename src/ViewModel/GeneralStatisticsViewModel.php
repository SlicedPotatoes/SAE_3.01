<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsType;

/**
 * View model pour la vue GeneralStatistics
 */
readonly class GeneralStatisticsViewModel extends BaseViewModel
{
    public HeaderViewModel $headerVM;
    public array $datas;
    public ProportionStatisticsType $currTab;
    public array $groups;
    public array $filters;

    public function __construct(
        array $datas,
        ProportionStatisticsType $currTab,
        array $groups,
        array $filters
    ) {
        $this->headerVM = new HeaderViewModel(false, "Vous pouvez observer les différentes", "Statistiques", "");
        $this->datas = $datas;
        $this->currTab = $currTab;
        $this->groups = array_map(fn($g) => [
            'id' => $g->getIdGroupStudent(),
            'label' => $g->getLabel()
        ], $groups);
        $this->filters = $filters;
    }

}