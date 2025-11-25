<?php

namespace Uphf\GestionAbsence\ViewModel;

use DateTime;
use Uphf\GestionAbsence\Model\Entity\Holiday;

readonly class OneHolidayPeriodViewModel extends BaseViewModel{
    public int $holidaysid;
    public string $HolidayName;
    public string $startDate;
    public string $endDate;

    public function __construct(Holiday $holiday) {
        $this->holidaysid = $holiday->getId();
        $this->HolidayName = $holiday->getPeriodName();
        $this->startDate = $holiday->getStartDate()->format("d/m/Y");
        $this->endDate = $holiday->getEndDate()->format("d/m/Y");
    }

}