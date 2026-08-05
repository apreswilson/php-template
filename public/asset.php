<?php
// This is just a helper file for fetching static assets so we can keep all php, js, and css files inside same page folder.

const FILE_TYPE_WHITELIST = [
    'css' => 'text/css',
    'js'  => 'application/javascript',
];

foreach ([$_GET['type'] ?? '', $_GET['name'] ?? '', $_GET['file'] ?? ''] as $part) {
    if ($part === '' || !preg_match('/^[a-zA-Z0-9_\-.]+$/', $part)) {
        http_response_code(400);
        exit;
    }
}

$file_path = __DIR__ . '/../src/' . $_GET['type'] . '/' . $_GET['name'] . '/' . $_GET['file'];
$real_path = realpath($file_path);
$real_base = realpath(__DIR__ . '/../src/');

if ($real_path === false || $real_base === false || !str_starts_with($real_path, $real_base)) {
    http_response_code(404);
    exit("Asset not found");
}

$ext = pathinfo($real_path, PATHINFO_EXTENSION);

if (!array_key_exists($ext, FILE_TYPE_WHITELIST)) {
    http_response_code(403);
    exit("Not supported file type");
}

header('Content-Type: ' . FILE_TYPE_WHITELIST[$ext]);
echo file_get_contents($real_path);