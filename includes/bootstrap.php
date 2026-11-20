<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config/api.php';
date_default_timezone_set($config['app_timezone'] ?? 'Asia/Jakarta');

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('barengin_web_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/../api/ApiClient.php';
require_once __DIR__ . '/../api/BarenginService.php';

$apiClient = new ApiClient((string) $config['base_url'], (int) $config['timeout']);
$barengin = new BarenginService($apiClient, $_SESSION['barengin_auth']['token'] ?? null);
