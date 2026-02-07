<?php
// test-smtp.php
// Script pour tester la connexion SMTP isolément

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: text/plain');
echo "--- TEST SMTP BREVO ---\n\n";

// 1. Chargement manuel .env (copie de la méthode robuste)
$envPath = __DIR__ . '/.env';
$env = [];
if (file_exists($envPath)) {
    $env = @parse_ini_file($envPath);
    if (!$env || empty($env['ADMIN_EMAIL'])) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0)
                continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $env[trim($name)] = trim(trim($value), '"\'');
            }
        }
    }
}

$smtpHost = $env['SMTP_HOST'] ?? '';
$smtpPort = $env['SMTP_PORT'] ?? 587;
$smtpUser = $env['SMTP_USER'] ?? '';
$smtpPass = $env['SMTP_PASS'] ?? '';

echo "Configuration détectée :\n";
echo "Host: $smtpHost\n";
echo "Port: $smtpPort\n";
echo "User: $smtpUser\n";
echo "Pass: " . ($smtpPass ? '******** (Présent)' : 'VIDE') . "\n";

if (!$smtpHost || !$smtpUser || !$smtpPass) {
    die("\n❌ ERREUR : Données SMTP manquantes dans le .env\n");
}

$mail = new PHPMailer(true);

try {
    // Debug niveau 3 pour voir les échanges client/serveur
    $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
    $mail->Debugoutput = function ($str, $level) {
        echo "DEBUG: $str\n";
    };

    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtpPort;

    // Timeout plus long
    $mail->Timeout = 10;

    echo "\n--- TENTATIVE DE CONNEXION ---\n";

    if ($mail->smtpConnect()) {
        echo "\n✅ CONNEXION SMTP RÉUSSIE !\n";
        echo "Vos identifiants sont corrects.\n";
        $mail->smtpClose();
    } else {
        echo "\n❌ ECHEC DE CONNEXION.\n";
    }

} catch (Exception $e) {
    echo "\n❌ EXCEPTION : " . $e->getMessage() . "\n";
}
