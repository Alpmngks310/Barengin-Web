<?php
declare(strict_types=1);

/**
 * Local website -> Laravel API configuration.
 * Default assumes Laravel runs on port 8000 and this PHP website on port 8080.
 * Override through environment variables or create config/api.local.php (gitignored).
 */
$config = [
    'base_url' => rtrim((string) (getenv('BARENGIN_API_BASE_URL') ?: 'http://127.0.0.1:8000/api'), '/'),
    'timeout' => (int) (getenv('BARENGIN_API_TIMEOUT') ?: 20),
    'device_name' => 'barengin-web',
    'app_timezone' => 'Asia/Jakarta',
    'demo_fallback' => filter_var(getenv('BARENGIN_DEMO_FALLBACK') ?: 'false', FILTER_VALIDATE_BOOL),
];

$localOverride = __DIR__ . '/api.local.php';
if (is_file($localOverride)) {
    $local = require $localOverride;
    if (is_array($local)) $config = array_replace($config, $local);
}
return $config;
