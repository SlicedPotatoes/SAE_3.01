<?php
/**
 * Envoyer un mail d'accusé au professeur et à l'étudiant lorsqu'une absence de ce dernier est justifiée et qu'un examen a eu lieu durant ce cours.
 * Afin de les prévenir qu'un rattrapage est possible.
 */

// Inclure la bibliothèque PHPMailer se trouvant dans le dossier /lib/PHPMailer/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../../lib/PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../../lib/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../../lib/PHPMailer-master/src/SMTP.php';

function mailAccRecpJusti($Tname, $Tfirstname, $emailT, $Ename, $Efirstname, $emailE, $dateExam): void
{
    $dateJour = date('d/m/Y');

    // Mail à destination du professeur
    $Tmail = "Bonjour " . $Tfirstname . " " . $Tname . ",<br><br>
    L'étudiant " . $Efirstname . " " . $Ename . " a justifié son absence pour l'examen qui du " . $dateExam . ".<br><br>
    Cordialement,<br>
    Le service des absences.";

    // Envoi du mail au professeur
    $Temail = new PHPMailer(true);
    try {
        // Configuration SMTP
        $Temail->isSMTP();
        $Temail->Host = 'smtp.gmail.com';                     // Serveur SMTP Gmail - on ne touche pas
        $Temail->SMTPAuth = true;                             // Activer authentification SMTP - on ne touche pas
        $Temail->Username = 'suivi.absences@gmail.com';       // Adresse Gmail - mail d'envoi
        $Temail->Password = 'utah jvpz ehui nhvk';            // Mot de passe d’application Google - mot de passe d'application, ici "utah jvpz ehui nhvk"
        $Temail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer chiffrement TLS - pas touche
        $Temail->Port = 587;                                  // Port TLS - pas touche

        // Destinataires
        $Temail->setFrom('suivi.absences@gmail.com', 'Suivi Absences');
        $Temail->addAddress($emailT, $Tname . ' ' . $Tfirstname); // Addresse mail et nom affiché du professeur

        // Contenu du message
        $Temail->CharSet = 'UTF-8';
        $Temail->isHTML(true);
        $Temail->Subject = 'Un étudiant a justifié son absence pour un examen';
        $Temail->Body = '<meta charset="UTF-8">' . $Tmail;
        $Temail->AltBody = strip_tags($Tmail);

        $Temail->send();
        echo 'Message envoyé avec succès';
    } catch (Exception $e) {
        echo "L'envoi du message a échoué: {$Temail->ErrorInfo}";
    }


    // Mail à destination de l'étudiant
    $Email = "Bonjour " . $Efirstname . " " . $Ename . ",<br><br>
    Votre demande de justification d'absence pour l'examen du " . $dateExam . " a bien été prise en compte le " . $dateJour . ".<br>
    Vous pouvez contacter votre professeur " . $Tfirstname . " " . $Tname . " pour vous renseigner sur les modalités de rattrapage de cet examen.<br><br>
    Cordialement,<br>
    Le service des absences.";

    // Envoi du mail à l'étudiant
    $Eemail = new PHPMailer(true);
    try {
        // Configuration SMTP
        $Eemail->isSMTP();
        $Eemail->Host = 'smtp.gmail.com';                     // Serveur SMTP Gmail - on ne touche pas
        $Eemail->SMTPAuth = true;                             // Activer authentification SMTP - on ne touche pas
        $Eemail->Username = 'suivi.absences@gmail.com';       // Adresse Gmail - mail d'envoi
        $Eemail->Password = 'utah jvpz ehui nhvk';            // Mot de passe d’application Google - mot de passe d'application, ici "utah jvpz ehui nhvk"
        $Eemail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer chiffrement TLS - pas touche
        $Eemail->Port = 587;                                  // Port TLS - pas touche

        // Destinataires
        $Eemail->setFrom('suivi.absences@gmail.com', 'Suivi Absences');
        $Eemail->addAddress($emailE, $Ename . ' ' . $Efirstname); // Addresse mail et nom affiché de l'étudiant

        // Contenu du message
        $Eemail->CharSet = 'UTF-8';
        $Eemail->isHTML(true);
        $Eemail->Subject = 'Vous avez justifié une absence pour un examen';
        $Eemail->Body = '<meta charset="UTF-8">' . $Email;
        $Eemail->AltBody = strip_tags($Email);

        $Eemail->send();
        echo 'Message envoyé avec succès';
    } catch (Exception $e) {
        echo "L'envoi du message a échoué: {$Eemail->ErrorInfo}";

    }

}