<?php
session_start();

// Carreguem l'autoload de Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Carreguem les variables d'entorn
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

error_reporting(0);
header('Content-Type: application/json');

/* ==========================================================================
   CONFIGURACIÓ I SEGURETAT (Vora Studio)
   ========================================================================== */
$recaptcha_secret = $_ENV['RECAPTCHA_SECRET'] ?? '';
$recaptcha_response = $_POST['recaptcha_response'] ?? '';

$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = [
    'secret'   => $recaptcha_secret,
    'response' => $recaptcha_response
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexió amb el servidor de Google.']);
    exit;
}

$response_keys = json_decode($response, true);

if (!$response_keys["success"]) {
    $error_msg = 'La verificació de seguretat ha fallat.';
    echo json_encode(['ok' => false, 'error' => $error_msg]);
    exit;
}

// 1. RATE LIMITING (Sessió)
$temps_espera = 10; 
if (isset($_SESSION['last_submit_time'])) {
    $temps_transcorregut = time() - $_SESSION['last_submit_time'];
    if ($temps_transcorregut < $temps_espera) {
        $restant = $temps_espera - $temps_transcorregut;
        echo json_encode(['ok' => false, 'error' => "Has d'esperar $restant segons."]);
        exit;
    }
}

// 2. VALIDACIÓ CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['ok' => false, 'error' => 'Validació de seguretat (CSRF) fallida.']);
    exit;
}

// 3. HONEYPOT
if (!empty($_POST['honeypot'])) {
    echo json_encode(['ok' => false, 'error' => 'Spam detectat.']);
    exit;
}

// 4. VALIDACIÓ LEGAL
if (!isset($_POST['privacy'])) {
    echo json_encode(['ok' => false, 'error' => 'Heu d’acceptar la política de privacitat.']);
    exit;
}

// 6. SANEJAMENT DE DADES
$nombre  = htmlspecialchars(trim($_POST['name'] ?? 'Sense nom'));
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$topic   = htmlspecialchars(trim($_POST['topic'] ?? 'General'));
$asunto  = htmlspecialchars(trim($_POST['subject'] ?? 'Sense assumpte'));
$mensaje = htmlspecialchars(trim($_POST['message'] ?? 'Sense missatge'));

// 7. VALIDACIÓ DE CAMPS CRÍTICS
if (empty($nombre) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($mensaje)) {
    echo json_encode(['ok' => false, 'error' => 'Per favor, omple tots els camps correctament.']);
    exit;
}

// 8. CONSTRUCCIÓ I ENVIAMENT AMB PHPMAILER
$mail = new PHPMailer(true);

try {
    // Configuració del servidor
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'];
    $mail->Password   = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $_ENV['SMTP_PORT'];
    $mail->CharSet    = 'UTF-8';

    // Destinataris
    $mail->setFrom($_ENV['SMTP_USER'], 'Vora Studio Web');
    $mail->addAddress($_ENV['SMTP_USER']); // Ho enviem al mateix correu de contacte
    $mail->addReplyTo($email, $nombre);

    // Contingut
    $mail->isHTML(false); // Enviament com a text pla per ara
    $mail->Subject = 'Nou missatge des de VoraStudio: ' . $asunto;
    
    $contenido = "Has rebut un nou missatge des del formulari de Vora Studio:\n\n";
    $contenido .= "Nom: $nombre\n";
    $contenido .= "Email: $email\n";
    $contenido .= "Tema: $topic\n";
    $contenido .= "Assumpte: $asunto\n\n";
    $contenido .= "Missatge:\n$mensaje\n";
    $contenido .= "\n---\nL'usuari ha acceptat expressament la política de privacitat.\n";
    
    $mail->Body = $contenido;

    $mail->send();
    
    $_SESSION['last_submit_time'] = time(); // Actualitzem el temps de l'últim enviament
    echo json_encode(['ok' => true, 'message' => 'Missatge enviat correctament!']);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => "El missatge no s'ha pogut enviar. Error: {$mail->ErrorInfo}"]);
}

