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
$senderName   = $env['SENDER_NAME'] ?? 'Expert Local';
$successUrl   = 'merci-diagnostic.html';

$smtpHost = $env['SMTP_HOST'] ?? '';
$smtpPort = (int)($env['SMTP_PORT'] ?? 587);
$smtpUser = $env['SMTP_USER'] ?? 'apikey';
$smtpPass = $env['SMTP_PASS'] ?? '';

// --------------------------------------------------
// 2. VALIDATION & ANTI-SPAM
// --------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: erreur-formulaire.html');
    exit;
}

$businessName = trim($_POST['business_name'] ?? '');
$firstName    = trim($_POST['first_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$activityType = trim($_POST['activity_type'] ?? '');
$googleLink   = trim($_POST['google_link'] ?? '');
$challenge    = trim($_POST['challenge'] ?? '');

// Honeypot
if (!empty($_POST['website'] ?? '')) {
    exit;
}

// Validation
if (
    $businessName === '' ||
    $firstName === '' ||
    $activityType === '' ||
    $email === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    header('Location: erreur-formulaire.html');
    exit;
}

// Vérif config SMTP minimale
if ($adminEmail === '' || $noReplyEmail === '' || $smtpHost === '' || $smtpPass === '') {
    header('Location: erreur-formulaire.html');
    exit;
}

// --------------------------------------------------
// 3. EMAIL ADMIN (texte)
// --------------------------------------------------
$subjectAdmin = "📊 Nouvelle demande de diagnostic – $businessName";

$bodyAdmin =
"Nouvelle demande de diagnostic\n\n" .
"👤 Prénom : $firstName\n" .
"🏪 Commerce : $businessName\n" .
"📧 Email : $email\n" .
"🏷️ Activité : $activityType\n" .
"🔗 Google : $googleLink\n\n" .
"🎯 Défi principal :\n$challenge\n";

try {
    $mailAdmin = new PHPMailer(true);
    $mailAdmin->CharSet = 'UTF-8';

    $mailAdmin->isSMTP();
    $mailAdmin->Host       = $smtpHost;
    $mailAdmin->SMTPAuth   = true;
    $mailAdmin->Username   = $smtpUser;
    $mailAdmin->Password   = $smtpPass;
    $mailAdmin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailAdmin->Port       = $smtpPort;

    $mailAdmin->setFrom($noReplyEmail, $senderName);
    $mailAdmin->addAddress($adminEmail);
    $mailAdmin->addReplyTo($email, $firstName);

    $mailAdmin->isHTML(false);
    $mailAdmin->Subject = $subjectAdmin;
    $mailAdmin->Body    = $bodyAdmin;

    $mailAdmin->send();
} catch (Exception $e) {
    header('Location: erreur-formulaire.html');
    exit;
}

// --------------------------------------------------
// 4. EMAIL CLIENT (HTML)
// --------------------------------------------------
$subjectUser = "Votre diagnostic Expert Local est en cours ✔";

$messageHtml = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Diagnostic en cours</title>
</head>
<body style="margin:0; padding:40px 0; background:#f2e9d8; font-family:Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:white; padding:30px; border-radius:12px;">
        <tr>
            <td align="center" style="padding-bottom:20px;">
                <h1 style="margin:0; font-size:26px; color:#2C3E50;">Merci $firstName !</h1>
            </td>
        </tr>
        <tr>
            <td style="font-size:16px; color:#2C3E50; line-height:1.6;">
                Votre demande de diagnostic pour <strong>$businessName</strong> a bien été reçue.<br><br>
                Je vais analyser votre visibilité locale et revenir vers vous très rapidement avec des recommandations concrètes.
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
    // On ne bloque pas la redirection si l'email client échoue
}

// --------------------------------------------------
// 5. REDIRECTION FINALE
// --------------------------------------------------
header("Location: $successUrl");
exit;
