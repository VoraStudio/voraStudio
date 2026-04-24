<?php
/**
 * TEST MAIL SCRIPT
 * Prova de l'enviament de correu amb PHPMailer i .env
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Dotenv\Dotenv;

// Carregar configuració - pugem un nivell perquè el .env és a l'arrel
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$mail = new PHPMailer(true);

try {
    // Activar debug per veure el detall de la connexió
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'];
    $mail->Password   = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $_ENV['SMTP_PORT'];
    $mail->CharSet    = 'UTF-8';

    // Destinataris
    $mail->setFrom($_ENV['SMTP_USER'], 'Test Vora Studio');
    $mail->addAddress($_ENV['SMTP_USER']); // Ens enviem el test a nosaltres mateixos

    // Contingut
    $mail->isHTML(true);
    $mail->Subject = 'PROVA DE CORREU - Vora Studio';
    $mail->Body    = '<h1>Funciona!</h1><p>Aquest és un correu de prova enviat el ' . date('d/m/Y H:i:s') . ' via SMTP.</p>';
    $mail->AltBody = 'Aquest és un correu de prova via SMTP.';

    echo "<pre>Iniciant enviament de prova...\n\n";
    $mail->send();
    echo "\n\n[OK] El missatge s'ha enviat correctament!</pre>";

} catch (Exception $e) {
    echo "\n\n[ERROR] El missatge no s'ha pogut enviar. Mailer Error: {$mail->ErrorInfo}</pre>";
}
