<?php
// Custom autoloader for PHPMailer (since Composer is not available)

spl_autoload_register(function ($class) {
    // Only handle PHPMailer classes
    if (strpos($class, 'PHPMailer\\PHPMailer\\') === 0) {
        $relativeClass = substr($class, strlen('PHPMailer\\PHPMailer\\'));
        $file = __DIR__ . '/phpmailer/phpmailer/src/' . $relativeClass . '.php';
        if (file_exists($file)) {
            require $file;
        }
        return;
    }

    // Handle Stripe classes
    if (strpos($class, 'Stripe\\') === 0) {
        $relativeClass = substr($class, strlen('Stripe\\'));
        // Stripe uses standard PSR-4 mapping in lib/
        $file = __DIR__ . '/stripe/stripe-php/lib/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
        return;
    }
});
