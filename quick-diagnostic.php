<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// --------------------------------------------------
// 1. CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

$adminEmail = $env['ADMIN_EMAIL'] ?? '';
$noReply = $env['NO_REPLY_EMAIL'] ?? '';
$senderName = $env['SENDER_NAME'] ?? 'Expert Local';

$smtpHost = $env['SMTP_HOST'] ?? '';
$smtpPort = (int) ($env['SMTP_PORT'] ?? 587);
$smtpUser = $env['SMTP_USER'] ?? 'apikey';
$smtpPass = $env['SMTP_PASS'] ?? '';

// --------------------------------------------------
// 2. ANTI-SPAM SIMPLE
// --------------------------------------------------
$email = trim($_POST['email'] ?? '');
$department = trim($_POST['department'] ?? '');
$reviews = trim($_POST['reviews'] ?? '');

$spam_words = ['http://', 'https://', '[url', 'viagra', 'casino', 'lottery'];
foreach ($spam_words as $word) {
    if (stripos($email, $word) !== false) {
        header('Location: erreur-formulaire.html');
        exit;
    }
}

// --------------------------------------------------
// 3. VALIDATION
// --------------------------------------------------
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: erreur-formulaire.html');
    exit;
}

if ($department === '' || $reviews === '') {
    header('Location: erreur-formulaire.html');
    exit;
}

// --------------------------------------------------
// 4. LIMITATION PAR IP
// --------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$cacheDir = __DIR__ . '/cache';
$cacheFile = $cacheDir . '/' . md5($ip) . '.json';

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

$cacheData = ['count' => 0, 'timestamp' => time()];

if (file_exists($cacheFile)) {
    $cacheData = json_decode(file_get_contents($cacheFile), true) ?? $cacheData;
    if (time() - $cacheData['timestamp'] < 3600 && $cacheData['count'] >= 5) {
        header('Location: erreur-formulaire.html');
        exit;
    }
}

// --------------------------------------------------
// 5. TRADUCTION DES VALEURS
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

// --------------------------------------------------
// 7. FONCTION LOG (DEBUG)
// --------------------------------------------------
function log_debug($message)
{
    file_put_contents(__DIR__ . '/log_email_debug.txt', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

log_debug("--- Nouvelle soumission Mini-Diagnostic ---");
log_debug("Email: $email, Dept: $department");

// --------------------------------------------------
// 8. EMAIL ADMIN
// --------------------------------------------------
$admin_template = file_get_contents(__DIR__ . '/email-admin-diagnostic-rapide.html');

$priority = 'Moyenne';
$potentiel_avis = '10-15';

if ($reviews === '0-5') {
    $priority = 'Élevée';
    $potentiel_avis = '15-20';
} elseif ($reviews === '21+') {
    $priority = 'Basse';
    $potentiel_avis = '5-10';
}

$admin_template = str_replace(
    ['{EMAIL}', '{DEPARTEMENT}', '{NB_AVIS}', '{DATE_SOUMISSION}', '{DATE_COMPLETE}', '{POTENTIEL_AVIS}', '{PRIORITE}', '{ID_UNIQUE}'],
    [htmlspecialchars($email), htmlspecialchars($department_text), htmlspecialchars($reviews_text_value), date('H:i'), $date_complete, $potentiel_avis, $priority, $unique_id],
    $admin_template
);

try {
    $mailAdmin = new PHPMailer(true);
    $mailAdmin->CharSet = 'UTF-8';

    // DEBUG SMTP
    $mailAdmin->SMTPDebug = SMTP::DEBUG_SERVER;
    $mailAdmin->Debugoutput = function ($str, $level) {
        log_debug("SMTP Admin: $str");
    };

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
        log_debug("ERREUR Envoi Admin: " . $mailAdmin->ErrorInfo);
    } else {
        log_debug("SUCCES Envoi Admin");
    }

} catch (Exception $e) {
    log_debug("EXCEPTION Envoi Admin: " . $e->getMessage());
}

// --------------------------------------------------
// 9. EMAIL CLIENT
// --------------------------------------------------
$client_template = file_get_contents(__DIR__ . '/email-client-confirmation.html');
$client_template = str_replace(['{PRENOM}', '{DATE}'], ['', date('d/m/Y')], $client_template);

try {
    $mailClient = new PHPMailer(true);
    $mailClient->CharSet = 'UTF-8';

    // DEBUG SMTP
    $mailClient->SMTPDebug = SMTP::DEBUG_SERVER;
    $mailClient->Debugoutput = function ($str, $level) {
        log_debug("SMTP Client: $str");
    };

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
        log_debug("ERREUR Envoi Client: " . $mailClient->ErrorInfo);
    } else {
        log_debug("SUCCES Envoi Client");
    }

} catch (Exception $e) {
    log_debug("EXCEPTION Envoi Client: " . $e->getMessage());
}

// --------------------------------------------------
// 10. CSV & CACHE
// --------------------------------------------------
$cacheData = [
    'timestamp' => time(),
    'count' => ($cacheData['count'] ?? 0) + 1,
    'ip' => $ip
];
file_put_contents($cacheFile, json_encode($cacheData));

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
// 11. REDIRECTION
// --------------------------------------------------
header("Location: merci-diagnostic.html");
exit;