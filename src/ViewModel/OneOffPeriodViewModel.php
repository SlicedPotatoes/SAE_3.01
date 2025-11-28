<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\OffPeriod;

/**
 * View model pour stocker une OffPeriod, utilisé dans d'autre view model
 */
readonly class OneOffPeriodViewModel extends BaseViewModel{
    public int $id;
    public string $label;
    public string $startDate;
    public string $endDate;

    public function __construct(OffPeriod $offPeriod) {
        $this->id = $offPeriod->getId();
        $this->label = $offPeriod->getPeriodName();
        $this->startDate = $offPeriod->getStartDate()->format("d/m/Y");
        $this->endDate = $offPeriod->getEndDate()->format("d/m/Y");
    }

}