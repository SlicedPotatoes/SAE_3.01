<?php
namespace Uphf\GestionAbsence\Model;

use DateTime;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Account\Teacher;

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
     * @param string $dateDebut
     * @param string $dateFin
     * @return void
     */
    static public function sendAccRecpJustification(string $lastname, string $firstname, string $email, string $dateDebut, string $dateFin): void
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
    Votre demande de justification d'absence du " . $dateDebut->format('d/m/Y') . " au " . $dateFin->format('d/m/Y') . " a bien été traité par le responsable pédagogique le " . $dateJour . ".<br>
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
    L'étudiant " . $firstnameStudent . " " . $lastnameStudent . " a justifié son absence pour l'examen qui à eu lieu le " . $dateExam->format('d/m/Y') . ' à ' . $dateExam->format('h:i') . " pour la ressource " . $ressource->getLabel() . "<br><br>
    Cordialement,<br>
    Le service des absences.";

        $bodyStudent = "Bonjour " . $firstnameStudent . " " . $lastnameStudent . ",<br><br>
    Votre demande de justification d'absence pour l'examen de " . $ressource->getLabel() . " du " . $dateExam->format('d/m/y') . ' à ' . $dateExam->format('h:i') . " a bien été prise en compte le " . $dateJour . ".<br>
    Vous pouvez contacter votre professeur " . $firstnameTeacher . " " . $lastnameTeacher . " pour vous renseigner sur les modalités de rattrapage de cet examen.<br><br>
    Cordialement,<br>
    Le service des absences.";

        self::sendMail($firstnameStudent, $lastnameStudent, $emailStudent, $bodyStudent, $subjectStudent);
        self::sendMail($firstnameTeacher, $lastnameTeacher, $emailTeacher, $bodyTeacher, $subjectTeacher);
    }


    /**
     * Permet d'envoyer un mail de notification à un compte lorsque de la modification du mot de passe a été effectuée
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @return void
     */
    static public function sendPasswordChangeNotification(string $lastname, string $firstname, string $email): void
    {
        $subject = 'Modification de votre mot de passe';
        $body = "Bonjour " . $firstname . " " . $lastname . ",<br><br>
                Votre mot de passe a été modifié avec succès.<br>
                Si vous n'êtes pas à l'origine de cette modification, veuillez contacter le support technique.<br><br>
                Cordialement,<br>
                Le service des absences.";

        self::sendMail($firstname, $lastname, $email, $body, $subject);
    }

    /**
     * Permet d'envoyer un mail lors d'un mot de passe oublié, afin de faire une modification de mot de passe
     *
     * @param  string  $lastname
     * @param  string  $firstname
     * @param  string  $email
     * @param  string  $token
     *
     * @return void
     */
    static public function sendPasswordChanger(string $lastname, string $firstname, string $email, string $token): void
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];
        $url = $protocol . $domain . "/ChangePassword/" . $token ;
        $subject = 'Mot de passe oublier';
        $body = "Bonjour " . $firstname . " " . $lastname . ",<br><br>
                Vous avez demandé la réinitialisation de votre mot de passe.<br>
                Pour définir un nouveau mot de passe, cliquez sur le lien suivant : <br>
                " . $url . "<br><br>
                Pour des raisons de sécurité, ce lien est valable pendant une durée limitée. <br>
                Si vous n'êtes pas à l'origine de cette modification, vous pouvez ignorer ce message.<br><br>
                Cordialement,<br>
                Le service des absences.";

        self::sendMail($firstname, $lastname, $email, $body, $subject);
    }


    /**
     * Permet d'envoyer un mail à un nouvel utilisateur avec son mot de passe temporaire
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param string $temporaryPassword
     * @return void
     */

    static public function sendNewAccount(string $lastname, string $firstname, string $email, string $temporaryPassword): void
    {
        $subject = 'Création de votre compte sur le portail de gestion des absences';
        $body = "Bonjour " . $firstname . " " . $lastname . ",<br><br>
                Votre compte sur le portail de gestion des absences a été créé avec succès.<br>
                Pour vous connecter, veuillez utiliser votre adresse e-mail UPHF : " . $email . "<br>
                Mot de passe temporaire : " . $temporaryPassword . "<br><br>
                Veuillez vous connecter et changer votre mot de passe dès que possible.<br><br>
                Cordialement,<br>
                Le service des absences.";

        self::sendMail($firstname, $lastname, $email, $body, $subject);
    }

    /**
     * Permet d'envoyer un mail à un étudiant lorsqu'il revient pour l'informer qu'il doit justifier son absence dans les 48h, puis un autre mail de rappel 24h avant la fin du délai.
     * Le mail lui précise également s'il a été absent durant un examen et si un malus lui sera appliqué en cas de non justification de son absence.
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param DateTime $dateDebutAbsence
     * @param DateTime $dateFinAbsence
     * @param bool $isExam
     * @param bool $isReminder
     * @param int $nbMalus
     * @param int $nbMalusPrevision
     * @return void
     */
    static public function sendReturnAlert(string $lastname, string $firstname, string $email, DateTime $dateDebutAbsence, DateTime $dateFinAbsence, bool $isExam = false, bool $isReminder = false, int $nbMalus = 0, int $nbMalusPrevision = 0): void
    {
        $mail = "Bonjour " . $firstname . " " . $lastname . ",<br><br>";

        if ($dateDebutAbsence->format('Y-m-d') === $dateFinAbsence->format('Y-m-d')) {
            $datePhrase = "le " . $dateDebutAbsence->format('d/m/Y');
        } else {
            $datePhrase = "du " . $dateDebutAbsence->format('d/m/Y') . " au " . $dateFinAbsence->format('d/m/Y');
        }

        if ($isReminder) {
            $subject = 'Rappel : Justification d\'absence à fournir sous 24h';
            $mail .= "Ceci est un rappel que vous avez été absent " . $datePhrase . ".<br>
Vous avez encore 24 heures pour justifier cette absence.<br><br>";
        } else {
            $subject = 'Justification d\'absence à fournir sous 48h';
            $mail .= "Nous avons constaté que vous avez été absent " . $datePhrase . ".<br>
Vous disposez de 48 heures pour justifier cette absence en soumettant un justificatif via votre espace personnel.<br><br>";
        }
        if ($isExam) {
            $mail .= "Veuillez noter que durant cette période d'absence, un examen a eu lieu. Si vous ne justifiez pas cette absence, vous ne pourrez pas rattraper l'examen et la note attribuée d'office sera de 0.<br><br>";
        }

        if ($nbMalusPrevision > 0) {
            $mail .= "De plus, en cas de non justification de cette absence, votre malus passera à " . $nbMalusPrevision . " (actuellement " . $nbMalus . ").<br><br>";
        }

        $mail .= "Cordialement,<br>
        Le service des absences.";

        self::sendMail($firstname, $lastname, $email, $mail, $subject);
    }



    /**
     * Permet d'envoyer un mail à un étudiant lorsqu'il revient pour l'informer qu'il doit justifier son absence dans les 48h, puis un autre mail de rappel 24h avant la fin du délai.
     * Le mail lui précise également s'il a été absent durant un examen et si un malus lui sera appliqué en cas de non justification de son absence.
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param DateTime $dateDebutAbsence
     * @param DateTime $dateFinAbsence
     * @param bool $isExam
     * @param bool $isReminder
     * @param float $nbMalus
     * @param float $nbMalusPrevision
     * @return void
     */
    static public function sendReturnAlert(string $lastname, string $firstname, string $email, DateTime $dateDebutAbsence, DateTime $dateFinAbsence, bool $isExam = false, bool $isReminder = false, float $nbMalus = 0, float $nbMalusPrevision = 0): void
    {
        $mail = "Bonjour " . $firstname . " " . $lastname . ",<br><br>";

        if ($dateDebutAbsence->format('Y-m-d') === $dateFinAbsence->format('Y-m-d')) {
            $datePhrase = "le " . $dateDebutAbsence->format('d/m/Y');
        } else {
            $datePhrase = "du " . $dateDebutAbsence->format('d/m/Y') . " au " . $dateFinAbsence->format('d/m/Y');
        }

        if ($isReminder) {
            $subject = 'Rappel : Justification d\'absence à fournir sous 24h';
            $mail .= "Ceci est un rappel que vous avez été absent " . $datePhrase . ".<br>
Vous avez encore 24 heures pour justifier cette absence.<br><br>";
        } else {
            $subject = 'Justification d\'absence à fournir sous 48h';
            $mail .= "Nous avons constaté que vous avez été absent " . $datePhrase . ".<br>
Vous disposez de 48 heures pour justifier cette absence en soumettant un justificatif via votre espace personnel.<br><br>";
        }
        if ($isExam) {
            $mail .= "Veuillez noter que durant cette période d'absence, un examen a eu lieu. Si vous ne justifiez pas cette absence, vous ne pourrez pas rattraper l'examen et la note attribuée d'office sera de 0.<br><br>";
        }

        if ($nbMalus > 0) {
            $mail .= "De plus, en cas de justification de cette absence, votre malus passera à " . $nbMalusPrevision . " (actuellement " . $nbMalus . ").<br><br>";
        }

        $mail .= "Cordialement,<br>
        Le service des absences.";

        self::sendMail($firstname, $lastname, $email, $mail, $subject);
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
        try {
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
            //echo 'Message envoyé avec succès';
        } catch (Exception $e) {
            error_log("L'envoi du message a échoué: {$mailer->ErrorInfo}");
        }
    }
    /**
     * Permet d'envoyer un mail au Responsable Pédagogique lorsqu'un étudiant a été absent plus d'une semaine de manière consécutive
     *
     * @param string $rpLastname Nom du Responsable Pédagogique
     * @param string $rpFirstname Prénom du Responsable Pédagogique
     * @param string $rpEmail Email du Responsable Pédagogique
     * @param string $studentLastname Nom de l'étudiant
     * @param string $studentFirstname Prénom de l'étudiant
     * @param int $studentNumber Numéro étudiant
     * @param int $consecutiveDays Nombre de jours consécutifs d'absence
     * @return void
     */
    static public function sendLongAbsenceAlert(
        string $rpLastname,
        string $rpFirstname,
        string $rpEmail,
        string $studentLastname,
        string $studentFirstname,
        int $studentNumber,
        int $consecutiveDays
    ): void
    {
        $subject = 'Alerte : Absence prolongée d\'un étudiant';

        $body = "Bonjour " . $rpFirstname . " " . $rpLastname . ",<br><br>
    Nous vous informons que l'étudiant " . $studentFirstname . " " . $studentLastname . " (n°" . $studentNumber . ") 
    est absent depuis " . $consecutiveDays . " jours consécutifs.<br><br>
    Nous vous invitons donc à prendre contact avec cet étudiant afin de vous assurer de sa situation.<br><br>
    Vous pouvez consulter le détail de ses absences dans votre espace personnel.<br><br>
    Cordialement,<br>
    Le service des absences.";
        self::sendMail($rpFirstname, $rpLastname, $rpEmail, $body, $subject);
    }
}