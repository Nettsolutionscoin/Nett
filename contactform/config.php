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
    // Values are taken from environment variables when available. For local testing with Apache/XAMPP,
    // add SetEnv lines to a local `.htaccess` (see project README or instructions below).
    'smtp' => [
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'username' => getenv('SMTP_USERNAME') ?: 'nettcoin@gmail.com',
        'password' => getenv('SMTP_PASSWORD') ?: '',
        'port' => getenv('SMTP_PORT') ? (int) getenv('SMTP_PORT') : 587,
        'secure' => getenv('SMTP_SECURE') ?: 'tls',
        'from_email' => getenv('SMTP_FROM_EMAIL') ?: 'nettcoin@gmail.com',
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'Nett Solutions'
    ],

    // reCAPTCHA (optional) - obtain keys from https://www.google.com/recaptcha/admin
    'recaptcha' => [
        'site_key' => '',
        'secret_key' => ''
    ],

    // Rate limit in seconds per session/IP
    'rate_limit_seconds' => 30
];
