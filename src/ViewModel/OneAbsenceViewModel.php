<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

/**
 * View model pour stocker une absence, utilisé dans d'autre view model
 */
readonly class OneAbsenceViewModel extends BaseViewModel {
    public int $idAccount;
    public string $time;

    public string $date;
    public string $duration;
    public StateAbs $state;
    public bool $examen;
    public bool $lock;

    public bool $haveTeacher;
    public string $fullnameTeacher;
    public string $courseType;
    public string $resource;
    public string $dateResit;

    public function __construct(Absence $abs) {
        $this->idAccount = $abs->getIdAccount();
        $this->time = $abs->getTime()->format('Y-m-d H:i:s');

        $this->date = $abs->getTime()->format("d/m/Y H:i");

        [$hours, $minutes] = explode(":", $abs->getDuration());
        $this->duration = intval($hours) . "h" . $minutes;

        $this->state = $abs->getCurrentState();
        $this->examen = $abs->getExamen();
        $this->lock = !$abs->getAllowedJustification() && $this->state === StateAbs::Refused;

        $this->haveTeacher = $abs->getTeacher() !== null;
        if($this->haveTeacher) {
            $this->fullnameTeacher = $abs->getTeacher()->getFirstName() . " " . $abs->getTeacher()->getLastName();
        }
        $this->courseType = $abs->getCourseType()->value;
        $this->resource = $abs->getResource()->getLabel();

        if($this->examen) {
            $this->dateResit = $abs->getDateResit() ? $abs->getDateResit()->format("d/m/Y H:i") : "Pas de date fixée";
        }
    }
}