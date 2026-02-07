<?php
// generate-diagnostic.php - Version corrigée avec PHPMailer + Debug

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// --------------------------------------------------
// 1. CHARGEMENT ENV
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

$admin_email = $env['ADMIN_EMAIL'];
$site_url = $env['SITE_URL'];
$no_reply_email = $env['NO_REPLY_EMAIL'];
$sender_name = $env['SENDER_NAME'];
$default_department = $env['DEFAULT_DEPARTMENT'] ?? '28';

$smtpHost = $env['SMTP_HOST'] ?? '';
$smtpPort = (int) ($env['SMTP_PORT'] ?? 587);
$smtpUser = $env['SMTP_USER'] ?? '';
$smtpPass = $env['SMTP_PASS'] ?? '';

// --------------------------------------------------
// 2. LOG FUNCTION
// --------------------------------------------------
function log_debug($message)
{
    file_put_contents(__DIR__ . '/log_email_debug.txt', date('[Y-m-d H:i:s] FULL: ') . $message . PHP_EOL, FILE_APPEND);
}

// --------------------------------------------------
// 3. RÉCUPÉRATION DES DONNÉES
// --------------------------------------------------
$business_name = $_POST['business_name'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$email = $_POST['email'] ?? '';
$activity_type = $_POST['activity_type'] ?? '';
$google_link = $_POST['google_link'] ?? '';
$challenge = $_POST['challenge'] ?? '';

// --------------------------------------------------
// 4. VALIDATION
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
    header("Location: erreur-formulaire.html");
    exit;
}

// --------------------------------------------------
// 5. FONCTION D'ENVOI D'EMAIL (Helper local)
// --------------------------------------------------
function sendEmail($to, $subject, $body, $smtpConfig, $fromEmail, $fromName, $replyTo = null)
{
    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = 'UTF-8';

        // DEBUG
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        // $mail->Debugoutput = function($str, $level) { log_debug("SMTP: $str"); };

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

        if (!$mail->send()) {
            log_debug("ERREUR envoi à $to: " . $mail->ErrorInfo);
            return false;
        }
        log_debug("SUCCES envoi à $to");
        return true;
    } catch (Exception $e) {
        log_debug("EXCEPTION envoi à $to: " . $e->getMessage());
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
// 6. ENVOIS
// --------------------------------------------------
log_debug("--- Nouvelle demande FULL ---");
log_debug("Commerce: $business_name, Email: $email");

// A. Notification Admin (Simple)
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
sendEmail($admin_email, $admin_subject, $admin_message, $smtpConfig, $no_reply_email, $sender_name, $email);

// B. Confirmation Client (Simple)
$client_subject = "Votre demande de diagnostic – Expert Local";
$client_message = "
<html><body>
<h2>Merci $first_name !</h2>
<p>Nous avons bien reçu votre demande de diagnostic pour <strong>$business_name</strong>.</p>
<p>Nous revenons vers vous très vite.</p>
</body></html>
";
sendEmail($email, $client_subject, $client_message, $smtpConfig, $no_reply_email, $sender_name);

// --------------------------------------------------
// 7. PREPARATION DIAGNOSTIC COMPLET
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

$unique_id = uniqid('FULL_', true);
$date_complete = date('d/m/Y H:i:s');

// C. Email Client (Détaillé)
$client_template = file_get_contents('email-client-diagnostic-complet.html');
$client_template = str_replace('{PRENOM}', htmlspecialchars($first_name), $client_template);
$client_template = str_replace('{NOM_COMMERCE}', htmlspecialchars($business_name), $client_template);
$client_template = str_replace('{DATE}', date('d/m/Y'), $client_template);
$subject_client = "🔍 Votre diagnostic Expert Local pour " . $business_name;
sendEmail($email, $subject_client, $client_template, $smtpConfig, $no_reply_email, $sender_name, $admin_email);

// D. Email Admin (Détaillé)
$admin_template = file_get_contents('email-admin-diagnostic-complet.html');

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
sendEmail($admin_email, $subject_admin, $admin_template, $smtpConfig, $no_reply_email, $sender_name);


// --------------------------------------------------
// 8. REDIRECTION FINALE
// --------------------------------------------------
header("Location: merci-diagnostic.html");
exit;
