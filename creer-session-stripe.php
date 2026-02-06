<?php
// Charge l'autoloader Stripe
require __DIR__ . '/vendor/autoload.php';

// --------------------------------------------------
// CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
// --------------------------------------------------
$env = parse_ini_file(__DIR__ . '/.env');

// Clé secrète Stripe (mode test ou live selon ton .env)
\Stripe\Stripe::setApiKey($env['STRIPE_SECRET_KEY']);

// --------------------------------------------------
// VALIDATION DU FORMULAIRE
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

if (!isset($_POST['price_id'])) {
    http_response_code(400);
    exit('Paramètre manquant');
}

$price_id = $_POST['price_id'];

// --------------------------------------------------
// PRIX AUTORISÉS (depuis .env)
// --------------------------------------------------
$allowed_prices = explode(',', $env['STRIPE_ALLOWED_PRICES_TEST']);

if (!in_array($price_id, $allowed_prices)) {
    http_response_code(400);
    exit('Prix non autorisé');
}

// --------------------------------------------------
// CRÉATION DE LA SESSION CHECKOUT STRIPE
// --------------------------------------------------
try {

    $session = \Stripe\Checkout\Session::create([
        'mode' => 'payment',
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price' => $price_id,
            'quantity' => 1,
        ]],
        'success_url' => $env['STRIPE_SUCCESS_URL'],
        'cancel_url' => $env['STRIPE_CANCEL_URL'],
    ]);

    header('Location: ' . $session->url);
    exit;

} catch (Exception $e) {
    error_log('Stripe Checkout Error: ' . $e->getMessage());
    header('Location: ' . $env['STRIPE_CANCEL_URL']);
    exit;
}
