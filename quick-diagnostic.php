<?php

require __DIR__ . '/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// --------------------------------------------------
// 1. CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

$adminEmail = $env['ADMIN_EMAIL'] ?? '';
$noReply    = $env['NO_REPLY_EMAIL'] ?? '';
$senderName = $env['SENDER_NAME'] ?? 'Expert Local';

$smtpHost = $env['SMTP_HOST'] ?? '';
$smtpPort = (int)($env['SMTP_PORT'] ?? 587);
$smtpUser = $env['SMTP_USER'] ?? 'apikey';
$smtpPass = $env['SMTP_PASS'] ?? '';

// --------------------------------------------------
// 2. ANTI-SPAM SIMPLE
// --------------------------------------------------
$spam_words = ['http://', 'https://', '[url', 'viagra', 'casino', 'lottery'];
foreach ($spam_words as $word) {
    if (stripos($_POST['email'] ?? '', $word) !== false) {
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        exit;
    }
}

// --------------------------------------------------
// 3. RÉCUPÉRATION DES DONNÉES
// --------------------------------------------------
$email      = trim($_POST['email'] ?? '');
$department = trim($_POST['department'] ?? '');
$reviews    = trim($_POST['reviews'] ?? '');

// --------------------------------------------------
// 4. VALIDATION
// --------------------------------------------------
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

if ($department === '' || $reviews === '') {
    echo json_encode(['success' => false, 'message' => 'Veuillez compléter toutes les étapes']);
    exit;
}

// --------------------------------------------------
// 5. LIMITATION PAR IP (5 / heure)
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
        echo json_encode(['success' => false, 'message' => 'Trop de demandes. Veuillez réessayer dans 1 heure.']);
        exit;
    }
}

// --------------------------------------------------
// 6. TRADUCTION DES VALEURS
// --------------------------------------------------
$departments = [
    '28' => 'Eure-et-Loir (28)',
    '45' => 'Loiret (45)',
    '78' => 'Yvelines (78)'
];

$reviews_text = [
    '0-5'  => '0-5 avis (Je commence)',
    '6-20' => '6-20 avis (Je veux progresser)',
    '21+'  => '21+ avis (Je veux dominer)'
];

$department_text = $departments[$department] ?? $department;
$reviews_text_value = $reviews_text[$reviews] ?? $reviews;

// --------------------------------------------------
// 7. GÉNÉRATION D'ID UNIQUE
// --------------------------------------------------
$unique_id = uniqid('DIA_', true);
$date_complete = date('d/m/Y H:i:s');

// --------------------------------------------------
// 8. EMAIL CLIENT (HTML) via SMTP Brevo
// --------------------------------------------------
$client_template = file_get_contents(__DIR__ . '/email-client-confirmation.html');
$client_template = str_replace('{PRENOM}', 'Cher client', $client_template);
$client_template = str_replace('{DATE}', date('d/m/Y'), $client_template);

try {
    $mailClient = new PHPMailer(true);
    $mailClient->CharSet = 'UTF-8';

    $mailClient->isSMTP();
    $mailClient->Host       = $smtpHost;
    $mailClient->SMTPAuth   = true;
    $mailClient->Username   = $smtpUser;
    $mailClient->Password   = $smtpPass;
    $mailClient->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailClient->Port       = $smtpPort;

    $mailClient->setFrom($noReply, $senderName);
    $mailClient->addAddress($email);
    $mailClient->addReplyTo($noReply);

    $mailClient->isHTML(true);
    $mailClient->Subject = 'Votre diagnostic Expert Local - En préparation';
    $mailClient->Body    = $client_template;

    $mailClient->send();
} catch (Exception $e) {
    // On continue même si l'email client échoue
}

// --------------------------------------------------
// 9. EMAIL ADMIN via SMTP Brevo
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

    $mailAdmin->isSMTP();
    $mailAdmin->Host       = $smtpHost;
    $mailAdmin->SMTPAuth   = true;
    $mailAdmin->Username   = $smtpUser;
    $mailAdmin->Password   = $smtpPass;
    $mailAdmin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailAdmin->Port       = $smtpPort;

    $mailAdmin->setFrom($noReply, $senderName);
    $mailAdmin->addAddress($adminEmail);

    $mailAdmin->isHTML(true);
    $mailAdmin->Subject = "Nouveau diagnostic rapide - $priority - $department_text";
    $mailAdmin->Body    = $admin_template;

    $mailAdmin->send();
} catch (Exception $e) {
    // On continue
}

// --------------------------------------------------
// 10. MISE À JOUR DU CACHE
// --------------------------------------------------
$cacheData = [
    'timestamp' => time(),
    'count'     => ($cacheData['count'] ?? 0) + 1,
    'ip'        => $ip
];
file_put_contents($cacheFile, json_encode($cacheData));

// --------------------------------------------------
// 11. SAUVEGARDE CSV (backup)
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
@file_put_contents(__DIR__ . '/contacts.csv', $csv_line, FILE_APPEND);

// --------------------------------------------------
// 12. RÉPONSE JSON
// --------------------------------------------------
echo json_encode([
    'success' => true,
    'message' => '✅ Demande envoyée ! Vérifiez votre email (pensez à vérifier vos spams).'
]);
