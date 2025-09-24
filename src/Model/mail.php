<?php
// Inclure la bibliothèque PHPMailer se trouvant dans le dossier /lib/PHPMailer/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../lib/PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../lib/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../lib/PHPMailer-master/src/SMTP.php';
// Créer une instance de PHPMailer


$mail = new PHPMailer(true);

try {
    // Configuration SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';                   // Serveur SMTP Gmail - on ne touche pas
    $mail->SMTPAuth = true;                            // Activer authentification SMTP - on ne touche pas
    $mail->Username = 'suivi.absences@gmail.com';      // Adresse Gmail - mail d'envoi
    $mail->Password = 'utah jvpz ehui nhvk';   // Mot de passe d’application Google - mot de passe d'application, ici "utah jvpz ehui nhvk"
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer chiffrement TLS - pas touche
    $mail->Port = 587;                                 // Port TLS - pas touche

    // Destinataires
    $mail->setFrom('suivi.absences@gmail.com', 'Suivi Absences');
    $mail->addAddress('Louis.Picouleau@uphf.fr', 'Louis Picouleau'); // Ajouter un destinataire

    // Contenu du message
    $mail->isHTML(true);
    $mail->Subject = 'Test';
    $mail->Body    = 'Ceci est un message envoyé automatiquement depuis votre site web.';
    $mail->AltBody = 'Ceci est un message en texte brut pour les clients qui ne lisent pas le HTML.';

    $mail->send();
    echo 'Message envoyé avec succès';
} catch (Exception $e) {
    echo "L'envoi du message a échoué: {$mail->ErrorInfo}";
}
?>