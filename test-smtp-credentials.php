<?php
// test-smtp-credentials.php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// CONFIGURATION LOGIN (Changez ces valeurs pour tester)
$testUser = 'antoinestarellas@gmail.com'; // <--- METTEZ VOTRE EMAIL DE CONNEXION ICI
$testPass = 'xkeysib-......'; // <--- METTEZ VOTRE CLÉ API ICI POUR TESTER DANS LE FICHIER

$env = @parse_ini_file(__DIR__ . '/.env');
$host = $env['SMTP_HOST'] ?? 'smtp-relay.brevo.com';
$port = $env['SMTP_PORT'] ?? 587;

// Si formulaire soumis, on prend les valeurs postées
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testUser = $_POST['user'];
    $testPass = $_POST['pass'];
}
?>
<!DOCTYPE html>
<html>

<body style="font-family:sans-serif; max-width:600px; margin:20px auto;">
    <h2>Testeur d'Authentification SMTP</h2>
    <form method="POST">
        <label>Utilisateur SMTP (Login Brevo) :</label><br>
        <input type="text" name="user" value="<?php echo htmlspecialchars($env['SMTP_USER'] ?? ''); ?>"
            style="width:100%; padding:8px;"><br><br>

        <label>Mot de passe (Clé API) :</label><br>
        <input type="text" name="pass" value="<?php echo htmlspecialchars($env['SMTP_PASS'] ?? ''); ?>"
            style="width:100%; padding:8px;"><br><br>

        <button type="submit"
            style="padding:10px 20px; background:blue; color:white; border:none; cursor:pointer;">TESTER LA
            CONNEXION</button>
    </form>

    <hr>
    <h3>Résultat :</h3>
    <pre style="background:#f4f4f4; padding:15px; overflow:auto;">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $testUser;
        $mail->Password = $testPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $port;
        $mail->Timeout = 5;

        echo "Tentative de connexion à $host:$port...\n";
        echo "User: $testUser\n";

        if ($mail->smtpConnect()) {
            echo "✅ SUCCÈS ! Identifiants valides.";
            $mail->smtpClose();
        } else {
            echo "❌ ÉCHEC. Identifiants refusés.";
        }
    } catch (Exception $e) {
        echo "Erreur Technique : " . $e->getMessage();
    }
}
?>
    </pre>
</body>

</html>