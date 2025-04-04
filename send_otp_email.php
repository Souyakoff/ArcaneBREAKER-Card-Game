<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'db_connect.php';
require 'vendor/autoload.php';

function sendOtpEmail($toEmail, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';  // Remplacez par le serveur SMTP de votre fournisseur
        $mail->SMTPAuth = true;
        $mail->Username = 'votre_email@example.com'; // Remplacez par votre adresse email
        $mail->Password = 'votre_mot_de_passe'; // Remplacez par votre mot de passe
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataire
        $mail->setFrom('Arcane-BREAKER');
        $mail->addAddress($toEmail); // L'email de l'utilisateur

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Votre code OTP';
        $mail->Body    = "Voici votre code OTP : <strong>$otp</strong>";

        // Envoi de l'email
        $mail->send();
        echo 'Email envoyé avec succès.';
    } catch (Exception $e) {
        echo "Erreur d'envoi de l'email: {$mail->ErrorInfo}";
    }
}
?>
