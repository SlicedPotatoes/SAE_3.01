<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Insert\OffPeriodInsertor;
use Uphf\GestionAbsence\Database\Select\OffPeriodSelector;
use Uphf\GestionAbsence\Database\Update\OffPeriodUpdater;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;

class OffPeriodService {

    public static function insert ($start, $end, $name) {
        $start = $_POST["startDate"] ?? '';
        $end = $_POST["endDate"] ?? '';
        $name = $_POST["periodName"] ?? '';

        if(!isset($start) || !isset($end) || !isset($name)) {
            Notification::addNotification(NotificationType::Error, "Un des champs obligatoire n'a pas été fournis");
        }
        else if(DateTime::createFromFormat("Y-m-d", $end) < DateTime::createFromFormat("Y-m-d", $start)) {
            Notification::addNotification(NotificationType::Error, "La date de début doit être inférieur ou égal a la date de fin");
        }
        else {
            OffPeriodInsertor::insert($start, $end, $name);
        }
    }
    public static function delete ($id) {
        if(isset($id)){
            OffPeriodUpdater::delete($id);
        }
    }
    public static function update ($id) {
        $id = isset($id) ? (int)$id : 0;
        $start = $_POST["startDate"] ?? '';
        $end = $_POST["endDate"] ?? '';
        $name = $_POST["periodName"] ?? '';

        if(!isset($start) || !isset($end) || !isset($name) || !isset($id)) {
            Notification::addNotification(NotificationType::Error, "Un des champs obligatoire n'a pas été fournis");
        }
        else if(DateTime::createFromFormat("Y-m-d", $end) > DateTime::createFromFormat("Y-m-d", $start)) {
            Notification::addNotification(NotificationType::Error, "La date de début doit être inférieur ou égal a la date de fin");
        }
        else {
            OffPeriodUpdater::update($id, $start, $end, $name);
        }
    }
    public static function selectAll () {
        $periods = OffPeriodSelector::getOffPeriod();
    }

}