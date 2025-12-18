<?php
// Contact form configuration
return [
    // Destination email
    'to_email' => 'nettcoin@gmail.com',

    // PHPMailer settings (Composer is preferred: run `composer install` in repo root)
    'use_phpmailer' => true,
    // Try Composer autoload by default (vendor/autoload.php at project root)
    'phpmailer_autoload' => dirname(__DIR__) . '/vendor/autoload.php',

    // SMTP configuration (used when PHPMailer is enabled and host is set)
    'smtp' => [
        'host' => '', // e.g. 'smtp.gmail.com'
        'username' => '',
        'password' => '',
        'port' => 587,
        'secure' => 'tls', // 'ssl' or 'tls'
        'from_email' => 'no-reply@yourdomain.com',
        'from_name' => 'Nett Solutions'
    ],

    // reCAPTCHA (optional) - obtain keys from https://www.google.com/recaptcha/admin
    'recaptcha' => [
        'site_key' => '',
        'secret_key' => ''
    ],

    // Rate limit in seconds per session/IP
    'rate_limit_seconds' => 30
];
