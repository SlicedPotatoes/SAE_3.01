<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;

/**
 * View model pour stocker un justificatif, utilisé dans d'autre view model
 */
readonly class OneJustificationViewModel extends BaseViewModel {
    public int $idJustification;
    public string $startDate;
    public string $endDate;
    public string|null $processedDate;
    public string $sendDate;
    public string $cause;
    public string|null $commentEM;
    public string $studentFullName;
    public StateJustif $state;
    public AccountType $roleUser;

    public function __construct(Justification $j, AccountType $roleUser) {
        $this->idJustification = $j->getIdJustification();
        $this->startDate = $j->getStartDate()->format("d/m/Y");
        $this->endDate = $j->getEndDate()->format("d/m/Y");
        $this->processedDate = $j->getProcessedDate() === null ? null : $j->getProcessedDate()->format("d/m/Y");
        $this->sendDate = $j->getSendDate()->format('d/m/Y');
        $this->cause = $j->getCause();
        $this->commentEM = $j->getRefusalReason();
        $this->state = $j->getCurrentState();

        $this->studentFullName = $j->getStudent()->getFirstName() . " " . $j->getStudent()->getLastName();
        $this->roleUser = $roleUser;
    }
}