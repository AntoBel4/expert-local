<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// --------------------------------------------------
// 0. CONFIG LOGS
// --------------------------------------------------
$logFile = __DIR__ . '/log_email_debug.txt';
function log_debug($message)
{
    global $logFile;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

log_debug("--- NOUVELLE SOUMISSION ---");

// --------------------------------------------------
// 1. CHARGEMENT ROBUSTE DU .ENV
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
} else {
    log_debug("ERREUR CRITIQUE: Fichier .env introuvable.");
}

$adminEmail = $env['ADMIN_EMAIL'] ?? '';
$noReply = $env['NO_REPLY_EMAIL'] ?? '';
$senderName = $env['SENDER_NAME'] ?? 'Expert Local';

$smtpHost = $env['SMTP_HOST'] ?? '';
$smtpPort = (int) ($env['SMTP_PORT'] ?? 587);
$smtpUser = $env['SMTP_USER'] ?? 'apikey';
$smtpPass = $env['SMTP_PASS'] ?? '';

log_debug("Env chargé. Admin: $adminEmail, Host: $smtpHost");

// --------------------------------------------------
// 2. ANTI-SPAM SIMPLE
// --------------------------------------------------
$email = trim($_POST['email'] ?? '');
$department = trim($_POST['department'] ?? '');
$reviews = trim($_POST['reviews'] ?? '');

log_debug("Données reçues - Email: $email, Dept: $department, Reviews: $reviews");

$spam_words = ['http://', 'https://', '[url', 'viagra', 'casino', 'lottery'];
foreach ($spam_words as $word) {
    if (stripos($email, $word) !== false) {
        log_debug("SPAM DETECTÉ: Mot interdit '$word'");
        header('Location: erreur-formulaire.html');
        exit;
    }
}

// --------------------------------------------------
// 3. VALIDATION
// --------------------------------------------------
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    log_debug("ERREUR VALIDATION: Email invalide ou vide");
    header('Location: erreur-formulaire.html');
    exit;
}

if ($department === '' || $reviews === '') {
    log_debug("ERREUR VALIDATION: Champs manquants (Dept ou Reviews)");
    header('Location: erreur-formulaire.html');
    exit;
}

// --------------------------------------------------
// 4. LIMITATION PAR IP (DÉSACTIVÉ POUR DEBUG)
// --------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
log_debug("IP Client: $ip");

// --------------------------------------------------
// 5. TRADUCTION DES VALEURS & OFFRE
// --------------------------------------------------
$departments = [
    '28' => 'Eure-et-Loir (28)',
    '45' => 'Loiret (45)',
    '78' => 'Yvelines (78)'
];

$reviews_text = [
    '0-5' => '0-5 avis (Je commence)',
    '6-20' => '6-20 avis (Je veux progresser)',
    '21+' => '21+ avis (Je veux dominer)'
];

$department_text = $departments[$department] ?? $department;
$reviews_text_value = $reviews_text[$reviews] ?? $reviews;

$unique_id = uniqid('DIA_', true);
$date_complete = date('d/m/Y H:i:s');

// DÉTERMINATION DE L'OFFRE (LOGIQUE V3: TOUJOURS PRO)
$priority = 'Moyenne';
$potentiel_avis = '10-15';

if ($reviews === '0-5') {
    $priority = 'Élevée';
    $potentiel_avis = '15-20';
} elseif ($reviews === '6-20') {
    $priority = 'Moyenne';
    $potentiel_avis = '10-15';
} elseif ($reviews === '21+') {
    $priority = 'Basse';
    $potentiel_avis = '5-10';
}

// --------------------------------------------------
// 8. EMAIL ADMIN
// --------------------------------------------------
$admin_template = file_get_contents(__DIR__ . '/email-admin-diagnostic-rapide.html');

$admin_template = str_replace(
    ['{EMAIL}', '{DEPARTEMENT}', '{NB_AVIS}', '{DATE_SOUMISSION}', '{DATE_COMPLETE}', '{POTENTIEL_AVIS}', '{PRIORITE}', '{ID_UNIQUE}'],
    [htmlspecialchars($email), htmlspecialchars($department_text), htmlspecialchars($reviews_text_value), date('H:i'), $date_complete, $potentiel_avis, $priority, $unique_id],
    $admin_template
);

try {
    $mailAdmin = new PHPMailer(true);
    $mailAdmin->CharSet = 'UTF-8';

    $mailAdmin->isSMTP();
    $mailAdmin->Host = $smtpHost;
    $mailAdmin->SMTPAuth = true;
    $mailAdmin->Username = $smtpUser;
    $mailAdmin->Password = $smtpPass;
    $mailAdmin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailAdmin->Port = $smtpPort;

    $mailAdmin->setFrom($noReply, $senderName);
    $mailAdmin->addAddress($adminEmail);

    $mailAdmin->isHTML(true);
    $mailAdmin->Subject = "Nouveau diagnostic rapide - $priority - $department_text";
    $mailAdmin->Body = $admin_template;

    if (!$mailAdmin->send()) {
        log_debug("FATAL EMAIL ADMIN: " . $mailAdmin->ErrorInfo);
    } else {
        log_debug("SUCCES Email Admin envoye");
    }

} catch (Exception $e) {
    log_debug("EXCEPTION Email Admin: " . $e->getMessage());
}

// --------------------------------------------------
// 9. EMAIL CLIENT
// --------------------------------------------------
$client_template = file_get_contents(__DIR__ . '/email-client-confirmation.html');
$client_template = str_replace(['{PRENOM}', '{DATE}'], ['', date('d/m/Y')], $client_template);

try {
    $mailClient = new PHPMailer(true);
    $mailClient->CharSet = 'UTF-8';

    $mailClient->isSMTP();
    $mailClient->Host = $smtpHost;
    $mailClient->SMTPAuth = true;
    $mailClient->Username = $smtpUser;
    $mailClient->Password = $smtpPass;
    $mailClient->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailClient->Port = $smtpPort;

    $mailClient->setFrom($noReply, $senderName);
    $mailClient->addAddress($email);
    $mailClient->addReplyTo($noReply);

    $mailClient->isHTML(true);
    $mailClient->Subject = 'Votre diagnostic Expert Local - En préparation';
    $mailClient->Body = $client_template;

    if (!$mailClient->send()) {
        log_debug("FATAL EMAIL CLIENT: " . $mailClient->ErrorInfo);
    } else {
        log_debug("SUCCES Email Client envoye");
    }

} catch (Exception $e) {
    log_debug("EXCEPTION Email Client: " . $e->getMessage());
}

// --------------------------------------------------
// 10. CSV & CACHE
// --------------------------------------------------
$csv_data = [
    date('Y-m-d H:i:s'),
    $unique_id,
    $email,
    $department_text,
    $reviews,
    'diagnostic_rapide',
    $priority,
    $ip
];

$csv_line = '"' . implode('","', $csv_data) . '"' . PHP_EOL;

$fp = @fopen(__DIR__ . '/contacts.csv', 'a');
if ($fp) {
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $csv_line);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

// --------------------------------------------------
// 11. REDIRECTION (STANDARDISATION OFFRE PRO)
// --------------------------------------------------
header("Location: merci-diagnostic.html?offer=pro");
exit;