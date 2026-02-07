<?php
// test-env.php
// Script de diagnostic pour le fichier .env

header('Content-Type: text/plain');

echo "--- DIAGNOSTIC .ENV ---\n";

$envPath = __DIR__ . '/.env';

// 1. Check existence
if (file_exists($envPath)) {
    echo "✅ Le fichier .env existe.\n";
} else {
    echo "❌ Le fichier .env est INTROUVABLE à : $envPath\n";
    exit;
}

// 2. Check permissions
if (is_readable($envPath)) {
    echo "✅ Le fichier .env est lisible.\n";
} else {
    echo "❌ Impossible de lire le fichier .env (problème de permissions).\n";
    exit;
}

// 3. Test parse_ini_file
echo "\n--- Test parse_ini_file() ---\n";
$envIni = @parse_ini_file($envPath);
if ($envIni === false) {
    echo "⚠️ parse_ini_file a échoué.\n";
} else {
    echo "✅ parse_ini_file a réussi.\n";
    echo "Clés trouvées : " . implode(', ', array_keys($envIni)) . "\n";
    echo "ADMIN_EMAIL : '" . ($envIni['ADMIN_EMAIL'] ?? 'NON DÉFINI') . "'\n";
}

// 4. Test lecture brute (Manual Parsing)
echo "\n--- Test Lecture Brute ---\n";
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$envManual = [];

foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0)
        continue;
    if (strpos($line, '=') !== false) {
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        // Remove quotes
        $value = trim($value, '"\'');
        $envManual[$name] = $value;
    }
}

echo "Clés trouvées (Manuel) : " . implode(', ', array_keys($envManual)) . "\n";
echo "ADMIN_EMAIL : '" . ($envManual['ADMIN_EMAIL'] ?? 'NON DÉFINI') . "'\n";

echo "\n--- CONCLUSION ---\n";
if (empty($envManual['ADMIN_EMAIL'])) {
    echo "❌ PROBLÈME : Les variables semblent vides. Vérifiez la syntaxe de votre fichier .env.\n";
    echo "Format attendu :\nCLÉ=valeur\n";
} else {
    echo "✅ Tout semble OK. Si les scripts ne marchent pas, copiez la méthode 'Lecture Brute'.\n";
}
