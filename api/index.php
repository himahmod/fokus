<?php
// Front controller: routes all PHP requests to the correct file in the app.

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = '/' . ltrim($uri ?? '/', '/');

if ($uri === '/' || $uri === '') {
    $uri = '/index.php';
}

// Only serve .php files
if (pathinfo($uri, PATHINFO_EXTENSION) !== 'php') {
    http_response_code(404);
    exit;
}

// Block path traversal attempts
if (strpos($uri, '..') !== false) {
    http_response_code(403);
    exit;
}

// App root is one level up from api/
$baseDir = realpath(__DIR__ . '/..');
$filePath = $baseDir . str_replace('/', DIRECTORY_SEPARATOR, $uri);

if (!file_exists($filePath)) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><body><h1>404 - Page not found</h1></body></html>';
    exit;
}

include $filePath;
