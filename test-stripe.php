<?php
// test-stripe.php
// Script pour tester la connexion API Stripe

require __DIR__ . '/vendor/autoload.php';

echo "--- TEST CONNEXION STRIPE ---\n\n";

// 1. Chargement manuel .env (Méthode Robuste)
$envPath = __DIR__ . '/.env';
$env = [];
if (file_exists($envPath)) {
    $env = @parse_ini_file($envPath);
    if (!$env || empty($env['STRIPE_SECRET_KEY'])) {
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
}

    }
}

$stripeSecret = $env['STRIPE_SECRET_KEY'] ?? '';
$stripePublishable = $env['STRIPE_PUBLISHABLE_KEY'] ?? '';

echo "Clé Secrète détectée : " . (empty($stripeSecret) ? "NON ❌" : "OUI ✅ (" . substr($stripeSecret, 0, 8) . "...)") . "\n";
echo "Clé Publique détectée : " . (empty($stripePublishable) ? "NON ❌" : "OUI ✅") . "\n";

if (empty($stripeSecret)) {
    die("\n❌ ERREUR : Clés manquantes. Vérifiez votre .env\n");
}

// 2. Test Appel API
try {
    \Stripe\Stripe::setApiKey($stripeSecret);

    // On essaie de récupérer les infos du compte (appel léger)
    $account = \Stripe\Account::retrieve();

    echo "\n✅ CONNEXION RÉUSSIE !\n";
    echo "Compte Stripe ID : " . $account->id . "\n";
    echo "Email du compte : " . $account->email . "\n";
    echo "Mode : " . ($account->charges_enabled ? "LIVE (Charges Enabled)" : "TEST (ou incomplet)") . "\n";

    // 3. Test Création Session (Dummy)
    echo "\n--- Test Création Session (Simulation) ---\n";
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Test Produit'],
                    'unit_amount' => 100, // 1.00€
                ],
                'quantity' => 1,
            ]
        ],
        'mode' => 'payment',
        'success_url' => 'https://example.com/success',
        'cancel_url' => 'https://example.com/cancel',
    ]);

    echo "✅ Session créée avec succès !\n";
    echo "URL de paiement (ne pas cliquer, c'est un test) : " . $session->url . "\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR STRIPE : " . $e->getMessage() . "\n";
}
