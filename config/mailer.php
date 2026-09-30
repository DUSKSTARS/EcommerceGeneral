<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';

/**
 * Envoie un email HTML via Gmail SMTP
 */
function envoyerEmail(string $destinataire, string $sujet, string $corpsHtml): bool
{
    $mail = new PHPMailer(true);

    try {
        // ---------- CONFIG SMTP ----------
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'duskaudace98@gmail.com';
        $mail->Password   = 'uvvs llbi qkyj muqf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // ---------- EXPÉDITEUR / DESTINATAIRE ----------
        $mail->setFrom('duskaudace98@gmail.com', 'Ma Boutique');
        $mail->addAddress($destinataire);

        // ---------- CONTENU ----------
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $corpsHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi email : ' . $mail->ErrorInfo);
        return false;
    }
}