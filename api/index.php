<?php
// Front controller: routes all PHP requests to the correct file in the app.

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = '/' . ltrim($uri ?? '/', '/');

// Prevent self-include when this file is accessed directly
if ($uri === '/api/index.php' || $uri === '/api/') {
    $uri = '/index.php';
}

if ($uri === '/' || $uri === '') {
    $uri = '/index.php';
}

// Only serve .php files — static assets are handled by Vercel routes
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
$baseDir = dirname(__DIR__);
$filePath = $baseDir . str_replace('/', DIRECTORY_SEPARATOR, $uri);

if (!file_exists($filePath)) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><body><h1>404 - Page not found</h1></body></html>';
    exit;
}

// Register DB-backed session handler before any session_start() call.
// File-based sessions break on Vercel: concurrent requests hit different
// serverless containers with separate /tmp, so sessions are invisible to each other.
require_once $baseDir . '/BDD/bdd.php';
require_once $baseDir . '/php/DBSessionHandler.php';
session_set_save_handler(new DBSessionHandler(), true);

include $filePath;
