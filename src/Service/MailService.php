<?php

namespace Uphf\GestionAbsence\Service;

use DateTime;
use Uphf\GestionAbsence\Database\Update\MailAlertUpdater;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Mailer;

class MailService
{
    /**
     * Envoie d'un mail lors d'un mot de passe oublié
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param string $token
     *
     * @return void
     */
    public static function sendForgotPasswordMail(string $lastname, string $firstname, string $email, string $token): void
    {
        Mailer::sendPasswordChanger($lastname, $firstname, $email, $token);
    }

    /**
     * Modifie les options d'alerte par mail d'un compte d'un professeur ou d'un responsable pédagogique
     *
     * @param Account $account
     * @param bool $mailAlertTeacher default true
     * @param bool $mailAlertEducationalManager default true
     * @return void
     */
    public static function changeMailAlert(Account  $account,
                                           bool $mailAlertTeacher = true,
                                           bool $mailAlertEducationalManager = true) : void
    {
        $typeAccount = $account->getAccountType();
        $idAccount = $account->getIdAccount();

        MailAlertUpdater::updateMailAlert($typeAccount, $idAccount, $mailAlertTeacher, $mailAlertEducationalManager);
    }

    /**
     * Permet d'envoyer un mail à un étudiant lorsqu'il a une absence durant un examen qui est validé
     * Et d'envoyer un mail au professeur responsable de la ressource qu'un étudiant absent durant un examen a validé une absence et doit repasser l'examen
     *
     * @param Absence $absence
     *
     * @return void
     */
    public static function sendMailExam(Absence $absence) : void
    {
        $student = $absence->getStudent();
        $teacher = $absence->getTeacher();
        $resource = $absence->getResource();
        $dateExam = $absence->getTime();

        Mailer::sendAlertExam($dateExam, $student, $teacher, $resource);
    }

    /**
     *  Permet d'envoyer un mail à un étudiant lorsqu'il justifie d'une absence
     *
     * @param Justification $justification
     * @return void
     */
    public static function sendAccRecepJustification(Justification $justification) : void
    {
        $lastname = $justification->getStudent()->getLastname();
        $firstname = $justification->getStudent()->getFirstname();
        $email = $justification->getStudent()->getEmail();
        $dateDebut = $justification->getStartDate()->format('d/m/Y');
        $dateFin = $justification->getEndDate()->format('d/m/Y');

        Mailer::sendAccRecpJustification($lastname, $firstname, $email, $dateDebut, $dateFin);
    }

}