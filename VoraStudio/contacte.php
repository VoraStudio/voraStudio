<?php
session_start();
error_reporting(0);
header('Content-Type: application/json');

/* ==========================================================================
   CONFIGURACIÓ I SEGURETAT (Vora Studio)
   ========================================================================== */
// Clave secreta de TEST
$recaptcha_secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; // USAR EN LOCAL
// $recaptcha_secret = "6Le_-LssAAAAAJsVJzGcnkG-2m-M-beLPo77x0Jc"; // USAR EN PRODUCCIÓN
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
    echo json_encode(['ok' => false, 'error' => 'Error de conexió amb el servidor de Google (Verifica openssl en PHP).']);
    exit;
}

$response_keys = json_decode($response, true);

if (!$response_keys["success"]) {
    $error_msg = 'La verificació de seguretat ha fallat.';
    if (isset($response_keys["error-codes"])) {
        $error_msg .= ' Detalls: ' . implode(", ", $response_keys["error-codes"]);
    }
    echo json_encode(['ok' => false, 'error' => $error_msg]);
    exit;
}


$destinatari = 'info.vorastudio@gmail.com'; 
$asunto_web = 'Nou missatge des de VoraStudio';

// 1. RATE LIMITING (Sessió)
// Evitem que una mateixa sessió enviï massa formularis seguits (60 segons de marge)
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
// Comprovem que el token rebut coincideix amb el de la sessió
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['ok' => false, 'error' => 'Validació de seguretat (CSRF) fallida.']);
    exit;
}

// 3. HONEYPOT (Antispam per a bots)
// Si el camp 'honeypot' no està buit, és un bot
if (!empty($_POST['honeypot'])) {
    echo json_encode(['ok' => false, 'error' => 'Spam detectat.']);
    exit;
}

// 4. VALIDACIÓ LEGAL (RGPD)
if (!isset($_POST['privacy'])) {
    echo json_encode(['ok' => false, 'error' => 'Heu d’acceptar la política de privacitat.']);
    exit;
}

// 5. VALIDACIÓ RECAPTCHA (Opcional si ja es valida en client)
// Aquí aniria la lògica de verificació amb Google si s'utilitza reCAPTCHA v3 completament

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

// 8. CONSTRUCCIÓ DEL COS DEL CORREU
$contenido = "Has rebut un nou missatge des del formulari de Vora Studio:\n\n";
$contenido .= "Nom: $nombre\n";
$contenido .= "Email: $email\n";
$contenido .= "Tema: $topic\n";
$contenido .= "Assumpte: $asunto\n\n";
$contenido .= "Missatge:\n$mensaje\n";
$contenido .= "\n---\nL'usuari ha acceptat expressament la política de privacitat.\n";

// 9. CAPÇALERES SEGURES (Evita Email Injection)
$headers = "From: no-reply@vorastudio.cat\r\n"; 
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";

// 10. ENVIAMENT
if (@mail($destinatari, $asunto_web, $contenido, $headers)) { 
    echo json_encode(['ok' => true, 'message' => 'Missatge enviat correctament! Revisa la teva bústia.']);
} else {
    echo json_encode(['ok' => false, 'error' => 'Error al servidor de correu (Normal en XAMPP).']);
}

