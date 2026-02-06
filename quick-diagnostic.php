<?php
header('Content-Type: application/json');

// --------------------------------------------------
// 1. CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

$admin_email   = $env['ADMIN_EMAIL'];
$site_url      = $env['SITE_URL'];
$no_reply      = $env['NO_REPLY_EMAIL'];
$sender_name   = $env['SENDER_NAME'];

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
$email      = $_POST['email'] ?? '';
$department = $_POST['department'] ?? '';
$reviews    = $_POST['reviews'] ?? '';

// --------------------------------------------------
// 4. VALIDATION
// --------------------------------------------------
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

if (empty($department) || empty($reviews)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez compléter toutes les étapes']);
    exit;
}

// --------------------------------------------------
// 5. LIMITATION PAR IP (5 / heure)
// --------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$cache_file = 'cache/' . md5($ip) . '.json';

if (!file_exists('cache')) {
    mkdir('cache', 0755, true);
}

if (file_exists($cache_file)) {
    $cache_data = json_decode(file_get_contents($cache_file), true);
    if (time() - $cache_data['timestamp'] < 3600 && $cache_data['count'] >= 5) {
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
    '0-5' => '0-5 avis (Je commence)',
    '6-20' => '6-20 avis (Je veux progresser)',
    '21+' => '21+ avis (Je veux dominer)'
];

$department_text = $departments[$department] ?? $department;
$reviews_text_value = $reviews_text[$reviews] ?? $reviews;

// --------------------------------------------------
// 7. GÉNÉRATION D'ID UNIQUE
// --------------------------------------------------
$unique_id = uniqid('DIA_', true);
$date_complete = date('d/m/Y H:i:s');

// --------------------------------------------------
// 8. EMAIL CLIENT (HTML)
// --------------------------------------------------
$client_template = file_get_contents('email-client-confirmation.html');
$client_template = str_replace('{PRENOM}', 'Cher client', $client_template);
$client_template = str_replace('{DATE}', date('d/m/Y'), $client_template);

$headers_client  = "From: $sender_name <$no_reply>\r\n";
$headers_client .= "Reply-To: $no_reply\r\n";
$headers_client .= "Return-Path: $no_reply\r\n";
$headers_client .= "MIME-Version: 1.0\r\n";
$headers_client .= "Content-Type: text/html; charset=UTF-8\r\n";

$subject_client = "Votre diagnostic Expert Local - En préparation";

mail($email, $subject_client, $client_template, $headers_client);

// --------------------------------------------------
// 9. EMAIL ADMIN
// --------------------------------------------------
$admin_template = file_get_contents('email-admin-diagnostic-rapide.html');

$priority = 'Moyenne';
$potentiel_avis = '10-15';

if ($reviews == '0-5') {
    $priority = 'Élevée';
    $potentiel_avis = '15-20';
} elseif ($reviews == '21+') {
    $priority = 'Basse';
    $potentiel_avis = '5-10';
}

$admin_template = str_replace('{EMAIL}', htmlspecialchars($email), $admin_template);
$admin_template = str_replace('{DEPARTEMENT}', htmlspecialchars($department_text), $admin_template);
$admin_template = str_replace('{NB_AVIS}', htmlspecialchars($reviews), $admin_template);
$admin_template = str_replace('{DATE_SOUMISSION}', date('H:i'), $admin_template);
$admin_template = str_replace('{DATE_COMPLETE}', $date_complete, $admin_template);
$admin_template = str_replace('{POTENTIEL_AVIS}', $potentiel_avis, $admin_template);
$admin_template = str_replace('{PRIORITE}', $priority, $admin_template);
$admin_template = str_replace('{ID_UNIQUE}', $unique_id, $admin_template);

$subject_admin = "Nouveau diagnostic rapide - $priority - $department_text";

$headers_admin  = "From: $sender_name <$no_reply>\r\n";
$headers_admin .= "MIME-Version: 1.0\r\n";
$headers_admin .= "Content-Type: text/html; charset=UTF-8\r\n";

mail($admin_email, $subject_admin, $admin_template, $headers_admin);

// --------------------------------------------------
// 10. MISE À JOUR DU CACHE
// --------------------------------------------------
$cache_data = [
    'timestamp' => time(),
    'count' => isset($cache_data['count']) ? $cache_data['count'] + 1 : 1,
    'ip' => $ip
];
file_put_contents($cache_file, json_encode($cache_data));

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
@file_put_contents('contacts.csv', $csv_line, FILE_APPEND);

// --------------------------------------------------
// 12. RÉPONSE JSON
// --------------------------------------------------
echo json_encode([
    'success' => true,
    'message' => '✅ Demande envoyée ! Vérifiez votre email (pensez à vérifier vos spams).'
]);
