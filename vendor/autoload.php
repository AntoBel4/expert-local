<?php
// Custom autoloader for PHPMailer (since Composer is not available)

spl_autoload_register(function ($class) {
    // Only handle PHPMailer classes
    if (strpos($class, 'PHPMailer\\PHPMailer\\') !== 0) {
        return;
    }

    // Map namespace to file path
    $relativeClass = substr($class, strlen('PHPMailer\\PHPMailer\\'));
    $file = __DIR__ . '/phpmailer/phpmailer/src/' . $relativeClass . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
