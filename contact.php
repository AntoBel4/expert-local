<?php

require __DIR__ . '/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --------------------------------------------------
// 1. CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

$adminEmail   = $env['ADMIN_EMAIL'] ?? '';
$noReplyEmail = $env['NO_REPLY_EMAIL'] ?? '';
$successUrl   = $env['CONTACT_SUCCESS_URL'] ?? 'contact-success.html';
$senderName   = $env['SENDER_NAME'] ?? 'Expert Local';

$smtpHost = $env['SMTP_HOST'] ?? '';
$smtpPort = (int)($env['SMTP_PORT'] ?? 587);
$smtpUser = $env['SMTP_USER'] ?? 'apikey';
$smtpPass = $env['SMTP_PASS'] ?? '';

// --------------------------------------------------
// 2. VALIDATION & ANTI-SPAM
// --------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: index.html');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Honeypot anti-bots
if (!empty($_POST['website'] ?? '')) {
    exit;
}

// Email valide ?
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: $successUrl");
    exit;
}

// Champs requis
if ($name === '' || $message === '') {
    header("Location: $successUrl");
    exit;
}

// Vérif config minimale
if ($adminEmail === '' || $noReplyEmail === '' || $smtpHost === '' || $smtpPass === '') {
    header('Location: erreur-formulaire.html');
    exit;
}

// --------------------------------------------------
// 3. EMAIL ADMIN (texte) via SMTP Brevo
// --------------------------------------------------
$subjectAdmin = "📩 Nouveau message reçu via Expert Local";

$bodyAdmin =
"Nouvelle prise de contact :\n\n" .
"👤 Nom : $name\n" .
"📧 Email : $email\n\n" .
"💬 Message :\n$message\n";

try {
    $mailAdmin = new PHPMailer(true);
    $mailAdmin->CharSet = 'UTF-8';

    $mailAdmin->isSMTP();
    $mailAdmin->Host       = $smtpHost;
    $mailAdmin->SMTPAuth   = true;
    $mailAdmin->Username   = $smtpUser;   // "apikey"
    $mailAdmin->Password   = $smtpPass;   // clé API Brevo
    $mailAdmin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailAdmin->Port       = $smtpPort;

    $mailAdmin->setFrom($noReplyEmail, $senderName);
    $mailAdmin->addAddress($adminEmail);
    $mailAdmin->addReplyTo($email, $name);

    $mailAdmin->isHTML(false);
    $mailAdmin->Subject = $subjectAdmin;
    $mailAdmin->Body    = $bodyAdmin;

    $mailAdmin->send();
} catch (Exception $e) {
    header('Location: erreur-formulaire.html');
    exit;
}

// --------------------------------------------------
// 4. EMAIL HTML POUR LE PROSPECT via SMTP Brevo
// --------------------------------------------------
$subjectUser = "Votre message a bien été reçu ✔";

$messageHtml = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Message bien reçu</title>
</head>
<body style="margin:0; padding:40px 0; background:#f2e9d8; font-family:Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:white; padding:30px; border-radius:12px;">
        <tr>
            <td align="center" style="padding-bottom:20px;">
                <h1 style="margin:0; font-size:26px; color:#2C3E50;">Votre message a bien été reçu</h1>
            </td>
        </tr>
        <tr>
            <td style="font-size:16px; color:#2C3E50; line-height:1.6;">
                Bonjour,<br><br>
                Merci pour votre prise de contact via <strong>Expert Local</strong>.<br>
                Je reviendrai vers vous très rapidement avec une réponse personnalisée.<br><br>
                <br><br>
                À très bientôt,<br>
                L'équipe Expert Local
            </td>
        </tr>
        <tr>
            <td align="center" style="padding-top:30px; font-size:13px; color:#8a7e6d;">
                © 2026 • Expert Local • Tous droits réservés
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>
HTML;

try {
    $mailUser = new PHPMailer(true);
    $mailUser->CharSet = 'UTF-8';

    $mailUser->isSMTP();
    $mailUser->Host       = $smtpHost;
    $mailUser->SMTPAuth   = true;
    $mailUser->Username   = $smtpUser;
    $mailUser->Password   = $smtpPass;
    $mailUser->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailUser->Port       = $smtpPort;

    $mailUser->setFrom($noReplyEmail, $senderName);
    $mailUser->addAddress($email);
    $mailUser->addReplyTo($adminEmail, $senderName);

    $mailUser->isHTML(true);
    $mailUser->Subject = $subjectUser;
    $mailUser->Body    = $messageHtml;

    $mailUser->send();
} catch (Exception $e) {
    // On ne bloque pas la redirection si l'email prospect échoue
}

// --------------------------------------------------
// 5. REDIRECTION FINALE
// --------------------------------------------------
header("Location: $successUrl");
exit;
