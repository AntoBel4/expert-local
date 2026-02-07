<?php

// --------------------------------------------------
// 1. CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

$adminEmail   = $env['ADMIN_EMAIL'];
$noReplyEmail = $env['NO_REPLY_EMAIL'];
$successUrl   = $env['CONTACT_SUCCESS_URL'];
$senderName   = $env['SENDER_NAME'];

// --------------------------------------------------
// 2. VALIDATION & ANTI-SPAM
// --------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

$name    = trim($_POST["name"] ?? "");
$email   = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

// Honeypot anti-bots
if (!empty($_POST["website"] ?? "")) {
    exit;
}

// Email valide ?
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: $successUrl");
    exit;
}

// Champs requis
if ($name === "" || $email === "" || $message === "") {
    header("Location: erreur-formulaire.html");
    exit;
}

// --------------------------------------------------
// 3. EMAIL ADMIN (texte)
// --------------------------------------------------
$subjectAdmin = "📩 Nouveau message reçu via Expert Local";

$bodyAdmin = 
"Nouvelle prise de contact :\n\n" .
"👤 Nom : $name\n" .
"📧 Email : $email\n\n" .
"💬 Message :\n$message\n";

$headersAdmin  = "From: $senderName <$noReplyEmail>\r\n";
$headersAdmin .= "Reply-To: $email\r\n";
$headersAdmin .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headersAdmin .= "Content-Transfer-Encoding: 8bit\r\n";

mail($adminEmail, $subjectAdmin, $bodyAdmin, $headersAdmin);

// --------------------------------------------------
// 4. EMAIL HTML POUR LE PROSPECT
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

$headersUser  = "From: $senderName <$noReplyEmail>\r\n";
$headersUser .= "Reply-To: $adminEmail\r\n";
$headersUser .= "MIME-Version: 1.0\r\n";
$headersUser .= "Content-Type: text/html; charset=UTF-8\r\n";
$headersUser .= "Content-Transfer-Encoding: 8bit\r\n";

mail($email, $subjectUser, $messageHtml, $headersUser);

// --------------------------------------------------
// 5. REDIRECTION FINALE
// --------------------------------------------------
header("Location: $successUrl");
exit;
