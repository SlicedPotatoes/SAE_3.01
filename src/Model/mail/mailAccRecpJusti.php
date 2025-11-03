<?php
/**
 * Envoyer un mail d'accusé de réception à l'étudiant lorsque celui-ci soumet une demande de justificatifs d'absence.
 */

// Inclure la bibliothèque PHPMailer se trouvant dans le dossier /lib/PHPMailer/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../../lib/PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../../lib/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../../lib/PHPMailer-master/src/SMTP.php';

function mailAccRecpJusti($name, $firstname, $emailEtu, $dateDebut, $dateFin): void
{
    $dateJour = date('d/m/Y');
    $mail = "Bonjour " . $firstname . " " . $name . ",<br><br>
    Votre demande de justification d'absence du " . $dateDebut . " au " . $dateFin . " a bien été reçue le " . $dateJour . ".<br>
    Un email vous sera envoyé dès que votre demande aura été traitée.<br>
    Vous pouvez également consulter l'état de votre demande dans votre espace personnel.<br><br>
    Cordialement,<br>
    Le service des absences.";

    $email = new PHPMailer(true);
    try {
        // Configuration SMTP
        $email->isSMTP();
        $email->Host = 'smtp.gmail.com';                     // Serveur SMTP Gmail - on ne touche pas
        $email->SMTPAuth = true;                             // Activer authentification SMTP - on ne touche pas
        $email->Username = 'suivi.absences@gmail.com';       // Adresse Gmail - mail d'envoi
        $email->Password = 'utah jvpz ehui nhvk';            // Mot de passe d’application Google - mot de passe d'application, ici "utah jvpz ehui nhvk"
        $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer chiffrement TLS - pas touche
        $email->Port = 587;                                  // Port TLS - pas touche

        // Destinataires
        $email->setFrom('suivi.absences@gmail.com', 'Suivi Absences');
        $email->addAddress($emailEtu, $name . ' ' . $firstname); // le `name` est le nom qui s'affichera dans le mail au lieu de l'adresse mail (ici `à Louis Picouleau`)

        // Contenu du message
        $email->CharSet = 'UTF-8';
        $email->isHTML(true);
        $email->Subject = 'Accusé de réception de votre demande de justificatif d\'absence';
        $email->Body = '<meta charset="UTF-8">' . $mail;
        $email->AltBody = strip_tags($mail);

        $email->send();
        echo 'Message envoyé avec succès';
    } catch (Exception $e) {
        echo "L'envoi du message a échoué: {$email->ErrorInfo}";
    }
}

//mailAccRecpJusti("Picouleau", "Louis", "Louis.Picouleau@uphf.fr", "01/01/2024", "05/01/2024");