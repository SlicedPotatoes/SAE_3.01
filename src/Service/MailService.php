<?php

namespace Uphf\GestionAbsence\Service;

use DateTime;
use Exception;
use Uphf\GestionAbsence\Database\Update\MailAlertUpdater;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Mailer;

/**
 * Service responsable de l'envoi de mail
 *
 * Cette classe regroupe:
 * - L'envoie du mail ForgotPassword
 * - La modification des paramètres de notification des comptes RP et professeur
 * - L'envoie de mail à l'étudiant et au professeur lors de la justification d'une absence a un examen
 * - L'envoie d'un mail à l'étudiant lors d'un dépo de justificatif.
 */
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
     * Modifie les options d'alerte par mail d'un compte d'un professeur ou d'un responsable pédagogique actuellement connecté
     *
     * @param bool $mailAlertTeacher default true
     * @param bool $mailAlertEducationalManager default true
     * @return void
     * @throws Exception
     */
    public static function changeMailAlert(bool $mailAlertTeacher,
                                           bool $mailAlertEducationalManager) : void
    {
        $account = AuthManager::getAccount();

        if(!(AuthManager::isRole(AccountType::Teacher) || AuthManager::isRole(AccountType::EducationalManager))) {
            throw new Exception("Only teacher and educational manager can change mail alert");
        }

        $typeAccount = $account->getAccountType();
        $idAccount = $account->getIdAccount();

        MailAlertUpdater::updateMailAlert($typeAccount, $idAccount, $mailAlertTeacher, $mailAlertEducationalManager);

        AuthManager::setNotificationMail('teacher', $mailAlertTeacher);
        AuthManager::setNotificationMail("educationalManager", $mailAlertEducationalManager);
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
     * Permet d'envoyer un mail à un étudiant lorsqu'il justifie d'une absence
     *
     * @param Student $student
     * @param DateTime $dateDebut
     * @param DateTime $dateFin
     * @return void
     */
    public static function sendAccRecepJustification(Student $student, DateTime $dateDebut, DateTime $dateFin) : void
    {
        Mailer::sendAccRecpJustification(
            $student->getLastName(),
            $student->getFirstName(),
            $student->getEmail(),
            $dateDebut->format("d/m/Y"),
            $dateFin->format("d/m/Y")
        );
    }

    /**
     * Permet d'envoyer un mail lorsqu'un utilisateur à changer son mot de passe
     *
     * @param Account $account
     * @return void
     */
    public static function sendPasswordChangeNotification(Account $account): void {
        Mailer::sendPasswordChangeNotification(
            $account->getLastName(),
            $account->getFirstName(),
            $account->getEmail()
        );
    }

    /**
     * Permet d'envoyer un mail avec un token lors de mot de passe oublié
     *
     * @param Account $account
     * @param string $token
     * @return void
     */
    public static function sendPasswordToken(Account $account, string $token): void {
        Mailer::sendPasswordChanger(
            $account->getLastName(),
            $account->getFirstName(),
            $account->getEmail(),
            $token
        );
    }

    /**
     * Permet d'envoyer un mail à un étudiant lorsque l'un de ses justificatifs a été traité par le responsable pédagogique
     *
     * @param Student $student
     * @param Justification $justification
     * @return void
     */
    public static function sendProcessedJustification(Student $student, Justification $justification): void {
        Mailer::sendProcessedJustification(
            $student->getLastName(),
            $student->getFirstName(),
            $student->getEmail(),
            $justification->getStartDate(),
            $justification->getEndDate()
        );
    }
}