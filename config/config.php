<?php
// Load environment variables from .env file
require_once __DIR__ . '/../core/env.php';
loadEnv();

// Database configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'uoc-sports'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

define('APP_ROOT', dirname(__DIR__));

// Email configuration (Brevo API)
$smtpKey = env('BREVO_API_KEY', '');
$senderEmail = env('BREVO_SENDER_EMAIL', '');
