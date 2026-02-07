<?php
// generate-diagnostic.php - Version corrigée (sans JSON)

// --------------------------------------------------
// 1. CHARGEMENT ENV
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

$admin_email = $env['ADMIN_EMAIL'];
$site_url = $env['SITE_URL'];
$no_reply_email = $env['NO_REPLY_EMAIL'];
$sender_name = $env['SENDER_NAME'];
$default_department = $env['DEFAULT_DEPARTMENT'] ?? '28';

// --------------------------------------------------
// 2. RÉCUPÉRATION DES DONNÉES
// --------------------------------------------------
$business_name = $_POST['business_name'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$email = $_POST['email'] ?? '';
$activity_type = $_POST['activity_type'] ?? '';
$google_link = $_POST['google_link'] ?? '';
$challenge = $_POST['challenge'] ?? '';

// --------------------------------------------------
// 3. VALIDATION
// --------------------------------------------------
$errors = [];
if (empty($first_name))
    $errors[] = 'Prénom requis';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Email invalide';
if (empty($business_name))
    $errors[] = 'Nom du commerce requis';
if (empty($activity_type))
    $errors[] = 'Type de commerce requis';

if (!empty($errors)) {
    // Redirection vers une page d'erreur simple
    header("Location: erreur-formulaire.html");
    exit;
}

// --------------------------------------------------
// 4. EMAIL ADMIN
// --------------------------------------------------
$admin_subject = "Nouvelle demande de diagnostic – $business_name";

$admin_message = "
<html><body>
<h2>Nouvelle demande de diagnostic</h2>
<p><strong>Prénom :</strong> $first_name</p>
<p><strong>Email :</strong> $email</p>
<p><strong>Commerce :</strong> $business_name</p>
<p><strong>Type :</strong> $activity_type</p>
<p><strong>Google :</strong> $google_link</p>
<p><strong>Défi :</strong> $challenge</p>
</body></html>
";

$headers_admin = "From: $sender_name <$no_reply_email>\r\n";
$headers_admin .= "Reply-To: $email\r\n";
$headers_admin .= "MIME-Version: 1.0\r\n";
$headers_admin .= "Content-Type: text/html; charset=UTF-8\r\n";

mail($admin_email, $admin_subject, $admin_message, $headers_admin);

// --------------------------------------------------
// 5. EMAIL CLIENT
// --------------------------------------------------
$client_subject = "Votre demande de diagnostic – Expert Local";

$client_message = "
<html><body>
<h2>Merci $first_name !</h2>
<p>Nous avons bien reçu votre demande de diagnostic pour <strong>$business_name</strong>.</p>
<p>Nous revenons vers vous très vite.</p>
</body></html>
";

$headers_client = "From: $sender_name <$no_reply_email>\r\n";
$headers_client .= "Reply-To: $no_reply_email\r\n";
$headers_client .= "MIME-Version: 1.0\r\n";
$headers_client .= "Content-Type: text/html; charset=UTF-8\r\n";

mail($email, $client_subject, $client_message, $headers_client);

// --------------------------------------------------
// 6. REDIRECTION (Après envoi)
// --------------------------------------------------
// Ce bloc était mal placé (exit prématuré). Déplacé à la fin du script.

// --------------------------------------------------
// 4. TRADUCTION TYPE D'ACTIVITÉ
// --------------------------------------------------
$activity_types = [
    'coiffure' => 'Coiffeur / Salon de beauté / Onglerie',
    'restauration' => 'Restaurant / Bar / Café',
    'artisan' => 'Artisan (plombier, électricien, etc.)',
    'garage' => 'Garage automobile / Mécanique',
    'boutique' => 'Boutique / Commerce de détail',
    'liberal' => 'Profession libérale',
    'autre' => 'Autre'
];

$activity_text = $activity_types[$activity_type] ?? $activity_type;

// --------------------------------------------------
// 5. GÉNÉRATION D'ID
// --------------------------------------------------
$unique_id = uniqid('FULL_', true);
$date_complete = date('d/m/Y H:i:s');

// --------------------------------------------------
// 6. EMAIL CLIENT
// --------------------------------------------------
$client_template = file_get_contents('email-client-diagnostic-complet.html');
$client_template = str_replace('{PRENOM}', htmlspecialchars($first_name), $client_template);
$client_template = str_replace('{NOM_COMMERCE}', htmlspecialchars($business_name), $client_template);
$client_template = str_replace('{DATE}', date('d/m/Y'), $client_template);

$headers_client = "From: $sender_name <{$no_reply_email}>\r\n";
$headers_client .= "Reply-To: {$admin_email}\r\n";
$headers_client .= "MIME-Version: 1.0\r\n";
$headers_client .= "Content-Type: text/html; charset=UTF-8\r\n";

$subject_client = "🔍 Votre diagnostic Expert Local pour " . $business_name;

mail($email, $subject_client, $client_template, $headers_client);

// --------------------------------------------------
// 7. EMAIL ADMIN
// --------------------------------------------------
$admin_template = file_get_contents('email-admin-diagnostic-complet.html');

// Scoring
$priority = 'Moyenne';
$offre_recommandee = 'Pack PRO 2026';
$prix_potentiel = '179';
$potentiel_conversion = '40%';
$niveau_priorite = 'medium';

if (in_array($activity_type, ['coiffure', 'restauration'])) {
    $priority = 'Élevée';
    $offre_recommandee = 'Pack PRO 2026';
    $prix_potentiel = '179';
    $potentiel_conversion = '60%';
    $niveau_priorite = 'high';
} elseif (in_array($activity_type, ['artisan', 'garage'])) {
    $priority = 'Très élevée';
    $offre_recommandee = 'Accompagnement VIP';
    $prix_potentiel = '497';
    $potentiel_conversion = '70%';
    $niveau_priorite = 'high';
}

$defi_raccourci = strlen($challenge) > 100 ? substr($challenge, 0, 100) . '...' : $challenge;
$date_proposee = date('d/m', strtotime('+2 days')) . ' à 10h ou 16h';

$admin_template = str_replace('{PRIORITE}', $priority, $admin_template);
$admin_template = str_replace('{NIVEAU_PRIORITE}', $niveau_priorite, $admin_template);
$admin_template = str_replace('{POTENTIEL_CONVERSION}', $potentiel_conversion, $admin_template);
$admin_template = str_replace('{NOM_COMMERCE}', htmlspecialchars($business_name), $admin_template);
$admin_template = str_replace('{TYPE_COMMERCE}', htmlspecialchars($activity_text), $admin_template);
$admin_template = str_replace('{PRENOM}', htmlspecialchars($first_name), $admin_template);
$admin_template = str_replace('{EMAIL}', htmlspecialchars($email), $admin_template);
$admin_template = str_replace('{DEPARTEMENT}', $default_department, $admin_template);
$admin_template = str_replace('{LIEN_GOOGLE}', htmlspecialchars($google_link), $admin_template);
$admin_template = str_replace('{DEFI}', htmlspecialchars($challenge), $admin_template);
$admin_template = str_replace('{DEFI_RACCOURCI}', htmlspecialchars($defi_raccourci), $admin_template);
$admin_template = str_replace('{DATE_COMPLETE}', $date_complete, $admin_template);
$admin_template = str_replace('{PRIX_POTENTIEL}', $prix_potentiel, $admin_template);
$admin_template = str_replace('{OFFRE_RECOMMANDEE}', $offre_recommandee, $admin_template);
$admin_template = str_replace('{DATE_PROPOSEE}', $date_proposee, $admin_template);
$admin_template = str_replace('{ID_UNIQUE}', $unique_id, $admin_template);

$subject_admin = "🎯 NOUVEAU DIAGNOSTIC - " . $business_name . " - " . $priority;

$headers_admin = "From: Site Expert Local <{$no_reply_email}>\r\n";
$headers_admin .= "MIME-Version: 1.0\r\n";
$headers_admin .= "Content-Type: text/html; charset=UTF-8\r\n";

mail($admin_email, $subject_admin, $admin_template, $headers_admin);

// --------------------------------------------------
// 8. REDIRECTION FINALE
// --------------------------------------------------
header("Location: merci-diagnostic.html");
exit;
