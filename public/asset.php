<?php
// This is just a helper file for fetching static assets, so we can keep all php, js, and css files inside same page folder.
$file      = $_GET['file'];
$page      = $_GET['page'];
$file_path = __DIR__ . '/../src/pages/' . $page . '/' . $file;

if (str_contains($file_path, '.js')) {
    header('Content-Type: application/javascript');
}

else if (str_contains($file_path, '.css')) {
    header('Content-Type: text/css');
}

echo file_get_contents($file_path);
?>