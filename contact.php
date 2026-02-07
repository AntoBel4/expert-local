<?php
// contact.php - Version corrigée avec PHPMailer

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --------------------------------------------------
// 1. CHARGEMENT ENV
// --------------------------------------------------
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

    }
}

// VALEURS PAR DÉFAUT (FALLBACK)
$admin_email = $env['ADMIN_EMAIL'] ?? 'contact@expert-local.fr';
$no_reply_email = $env['NO_REPLY_EMAIL'] ?? 'ne-pas-repondre@expert-local.fr';
$sender_name = $env['SENDER_NAME'] ?? 'Expert Local';

$smtpHost = $env['SMTP_HOST'] ?? 'smtp-relay.brevo.com';
$smtpPort = (int) ($env['SMTP_PORT'] ?? 587);
$smtpUser = $env['SMTP_USER'] ?? 'apikey';
$smtpPass = $env['SMTP_PASS'] ?? '';

// --------------------------------------------------
// 2. RÉCUPÉRATION DES DONNÉES
// --------------------------------------------------
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';
$honeypot = $_POST['firstname'] ?? ''; // Champ piège caché

// --------------------------------------------------
// 3. ANIT-SPAM
// --------------------------------------------------
if (!empty($honeypot)) {
    // Si le champ caché est rempli, c'est un robot.
    die('Erreur de validation.');
}

// --------------------------------------------------
// 4. VALIDATION
// --------------------------------------------------
if ($name === "" || $email === "" || $message === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Redirection page erreur
    header("Location: erreur-formulaire.html");
    exit;
}

// --------------------------------------------------
// 5. FONCTION D'ENVOI D'EMAIL (Helper local)
// --------------------------------------------------
function sendContactEmail($to, $subject, $body, $smtpConfig, $fromEmail, $fromName, $replyTo = null)
{
    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = 'UTF-8';

        if (!empty($smtpConfig['host'])) {
            $mail->isSMTP();
            $mail->Host = $smtpConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtpConfig['user'];
            $mail->Password = $smtpConfig['pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtpConfig['port'];
        }

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: {$mail->ErrorInfo}");
        return false;
    }
}

$smtpConfig = [
    'host' => $smtpHost,
    'port' => $smtpPort,
    'user' => $smtpUser,
    'pass' => $smtpPass
];

// --------------------------------------------------
// 6. EMAIL ADMIN
// --------------------------------------------------
$admin_subject = "Nouveau message contact - $name";
$admin_body = "
<html><body>
<h2>Nouveau message de contact</h2>
<p><strong>Nom :</strong> $name</p>
<p><strong>Email :</strong> $email</p>
<p><strong>Message :</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
</body></html>
";

sendContactEmail($admin_email, $admin_subject, $admin_body, $smtpConfig, $no_reply_email, $sender_name, $email);

// --------------------------------------------------
// 7. EMAIL CLIENT (Confirmation réception)
// --------------------------------------------------
// On utilise contact-success.html s'il existe, sinon un message simple
$client_body = "
<html><body>
<h2>Merci $name !</h2>
<p>Nous avons bien reçu votre message. Nous vous répondrons dans les plus brefs délais.</p>
<hr>
<p>Votre message :<br>" . nl2br(htmlspecialchars($message)) . "</p>
</body></html>
";

$subject_client = "Confirmation de réception - Expert Local";

sendContactEmail($email, $subject_client, $client_body, $smtpConfig, $no_reply_email, $sender_name);

// --------------------------------------------------
// 8. REDIRECTION SUCCÈS
// --------------------------------------------------
// Le client voulait une redirection vers contact-success.html ou similaire.
// Pour l'instant on redirige vers l'accueil ou une page de succès dédiée si elle existe.
// Comme contact-success.html existe dans la liste des fichiers, on l'utilise.

if (file_exists(__DIR__ . '/contact-success.html')) {
    header("Location: contact-success.html");
} else {
    // Fallback
    header("Location: index.html");
}
exit;
