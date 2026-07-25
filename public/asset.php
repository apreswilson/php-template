<?php
// This is just a helper file for fetching static assets so we can keep all php, js, and css files inside same page folder.
$file      = $_GET['file'];
$page      = $_GET['page'] ?? null;
$component = $_GET['component'] ?? null;
$file_path = $component 
                ? __DIR__ . '/../src/components/' . $component . '/' . $file
                : __DIR__ . '/../src/pages/' . $page . '/' . $file;

if (!file_exists($file_path)) {
    http_response_code(404);
    exit("Asset not found");
}

if (str_contains($file_path, '.js')) {
    header('Content-Type: application/javascript');
}

else if (str_contains($file_path, '.css')) {
    header('Content-Type: text/css');
}

echo file_get_contents($file_path);