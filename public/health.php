<?php

header('Content-Type: application/json');

echo json_encode([
    'status' => 'ok',
    'timestamp' => date('c'),
    'environment' => getenv('APP_ENV') ?: 'production',
    'php_version' => PHP_VERSION,
]);
