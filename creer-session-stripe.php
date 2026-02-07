<?php

require __DIR__ . '/vendor/autoload.php';

// Charger l'environnement
$env = parse_ini_file(__DIR__ . '/.env');

// Clés Stripe depuis le .env
$stripeSecret = $env['STRIPE_SECRET_KEY'] ?? '';
$stripePublishable = $env['STRIPE_PUBLISHABLE_KEY'] ?? '';
$siteUrl = $env['SITE_URL'] ?? 'https://expert-local.fr'; // Fallback

if (empty($stripeSecret)) {
    die("Erreur de configuration : Clé Stripe manquante.");
}

\Stripe\Stripe::setApiKey($stripeSecret);

// Récupérer l'offre choisie
$offerType = $_POST['offer_type'] ?? 'starter';

// Définir les produits (Prix en centimes)
$products = [
    'starter' => [
        'name' => 'Kit de Démarrage Google Avis',
        'price' => 4900, // 49.00 EUR
    ],
    'pro' => [
        'name' => 'Pack PRO Domination Locale',
        'price' => 17900, // 179.00 EUR
    ],
    'vip' => [
        'name' => 'Accompagnement VIP 2026',
        'price' => 49700, // 497.00 EUR
    ]
];

$product = $products[$offerType] ?? $products['starter'];

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    // ⚠️ IMPORTANT : Vérifiez la TVA, ici c'est montant TTC
                    'unit_amount' => $product['price'],
                ],
                'quantity' => 1,
            ]
        ],
        'mode' => 'payment',
        'success_url' => $siteUrl . '/merci-commande.html?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $siteUrl . '/merci-diagnostic.html?offer=' . $offerType,
    ]);

    // Redirection vers Stripe
    header("Location: " . $session->url);
    exit;

} catch (Exception $e) {
    echo "Erreur lors de la création de la session Stripe : " . $e->getMessage();
}
