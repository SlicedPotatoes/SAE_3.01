<?php
namespace Uphf\GestionAbsence\Model\mail;

use DateTime;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Uphf\GestionAbsence\Model\Absence\Resource;
use Uphf\GestionAbsence\Model\Account\Student;
use Uphf\GestionAbsence\Model\Account\Teacher;

/**
 * Classe permettant d'envoyer des mails avec différentes méthodes pour différents context
 */

class Mailer
{
    /**
     * Permet d'envoyer un mail à un étudiant lorsqu'il justifie d'une absence
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param DateTime $dateDebut
     * @param DateTime $dateFin
     * @return void
     */
    static public function sendAccRecpJustification(string $lastname, string $firstname, string $email, DateTime $dateDebut, DateTime $dateFin): void
    {
        $subject = 'Accusé de réception de votre demande de justificatif d\'absence';
        $dateJour = date('d/m/Y');
        $body = "Bonjour " . $firstname . " " . $lastname . ",<br><br>
    Votre demande de justification d'absence du " . $dateDebut . " au " . $dateFin . " a bien été reçue le " . $dateJour . ".<br>
    Un email vous sera envoyé dès que votre demande aura été traitée.<br>
    Vous pouvez également consulter l'état de votre demande dans votre espace personnel.<br><br>
    Cordialement,<br>
    Le service des absences.";

        self::sendMail($firstname, $lastname, $email, $body, $subject);
    }

    /**
     * Permet d'envoyer un mail à un étudiant lorsque l'un de ses justificatifs à été traité par le responsable pédagogique
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param DateTime $dateDebut
     * @param DateTime $dateFin
     * @return void
     */
    static public function sendProcessedJustification(string $lastname, string $firstname, string $email, DateTime $dateDebut, DateTime $dateFin): void
    {
        $subject = 'Traitement de votre justificatif d\'absence';
        $dateJour = date('d/m/Y');
        $body = "Bonjour " . $firstname . " " . $lastname . ",<br><br>
    Votre demande de justification d'absence du " . $dateDebut . " au " . $dateFin . " a bien été traité par le responsable pédagogique le " . $dateJour . ".<br>
    Nous vous invitons à consulter la décision du responsable pédagogique dans votre espace personnel.<br><br>
    Cordialement,<br>
    Le service des absences.";

        self::sendMail($firstname, $lastname, $email, $body, $subject);
    }

    /**
     *
     * Permet d'envoyer un mail à un étudiant lorsqu'il a une absence durant un examen qui est validé
     * Et d'envoyer un mail au professeur responsable de la ressource qu'un étudiant absent durant un examen a validé une absence et doit repasser l'examen
     *
     * @param DateTime $dateExam
     * @param Student $student
     * @param Teacher $teacher
     * @param Resource $ressource
     * @return void
     */
    static public function sendAlertExam(DateTime $dateExam, Student $student, Teacher $teacher, Resource $ressource): void
    {
        $lastnameTeacher = $teacher->getLastName();
        $firstnameTeacher = $teacher->getFirstName();
        $emailTeacher = $teacher->getEmail();
        $subjectTeacher = 'Un étudiant a justifié son absence pour un examen';

        $lastnameStudent = $student->getLastName();
        $firstnameStudent = $student->getFirstName();
        $emailStudent = $student->getEmail();
        $subjectStudent = 'Vous avez justifié une absence pour un examen';

        $dateJour = date('d/m/Y');

        $bodyTeacher = "Bonjour " . $firstnameTeacher . " " . $lastnameTeacher . ",<br><br>
    L'étudiant " . $firstnameStudent . " " . $lastnameStudent . " a justifié son absence pour l'examen qui à eu lieu le " . $dateExam->format('d/m/Y') . ' à ' . $dateExam->format('h:i') . " pour la ressource " . $ressource->getLabel() ."<br><br>
    Cordialement,<br>
    Le service des absences.";

        $bodyStudent = "Bonjour " . $firstnameStudent . " " . $lastnameStudent . ",<br><br>
    Votre demande de justification d'absence pour l'examen de " . $ressource->getLabel() ." du " . $dateExam->format('d/m/y') . ' à ' . $dateExam->format('h:i') . " a bien été prise en compte le " . $dateJour . ".<br>
    Vous pouvez contacter votre professeur " . $firstnameTeacher . " " . $lastnameTeacher . " pour vous renseigner sur les modalités de rattrapage de cet examen.<br><br>
    Cordialement,<br>
    Le service des absences.";

        self::sendMail($firstnameStudent, $lastnameStudent, $emailStudent, $bodyStudent, $subjectStudent);
        self::sendMail($emailTeacher, $lastnameTeacher, $emailTeacher, $bodyTeacher, $subjectTeacher);
    }

    /**
     * Fonction utilisé pour envoyer le mail en utlisant l'api PHPMailer
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $body
     * @param string $subject
     * @return void
     */
    static private function sendMail(string $firstname, string $lastname, string $email, string $body, string $subject): void
    {
        $mailer = new PHPMailer(true);
        try
        {
            // Configuration SMTP
            $mailer->isSMTP();
            $mailer->Host = 'smtp.gmail.com';                     // Serveur SMTP Gmail - on ne touche pas
            $mailer->SMTPAuth = true;                             // Activer authentification SMTP - on ne touche pas
            $mailer->Username = 'suivi.absences@gmail.com';       // Adresse Gmail - mail d'envoi
            $mailer->Password = 'utah jvpz ehui nhvk';            // Mot de passe d’application Google - mot de passe d'application, ici "utah jvpz ehui nhvk"
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer chiffrement TLS - pas touche
            $mailer->Port = 587;                                  // Port TLS - pas touche

            // Destinataires
            $mailer->setFrom('suivi.absences@gmail.com', 'Suivi Absences');
            $mailer->addAddress($email, $lastname . ' ' . $firstname); // le `name` est le nom qui s'affichera dans le mail au lieu de l'adresse mail

            // Contenu du message
            $mailer->CharSet = 'UTF-8';
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = '<meta charset="UTF-8">' . $body;
            $mailer->AltBody = strip_tags($body);

            $mailer->send();
            echo 'Message envoyé avec succès';
        }
        catch (Exception $e)
        {
            echo "L'envoi du message a échoué: {$mailer->ErrorInfo}";
        }
    }
}