<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config/api.php';
require __DIR__ . '/../api/ApiClient.php';
$client = new ApiClient((string) $config['base_url'], (int) $config['timeout']);
try {
    $response = $client->request('GET', '/health', [], null);
    echo "Laravel API response:\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit(!empty($response['success']) ? 0 : 1);
} catch (ApiException $exception) {
    fwrite(STDERR, 'API check failed: ' . $exception->getMessage() . PHP_EOL);
    if ($exception->statusCode > 0) fwrite(STDERR, 'HTTP status: ' . $exception->statusCode . PHP_EOL);
    exit(1);
}
