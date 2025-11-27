<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

class DetailPeriodController
{
    public static function show($params): ControllerData {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }
        // Vérification de la permission de l'utilisateur
        if (!AuthManager::isRole(AccountType::Teacher))
        {
            return ControllerData::get403();
        }

        $absences = $justification->getAbsences();



}