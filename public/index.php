<?php
// Uncomment or delete this if you want to use it for debugging.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Load bootstrap
require_once __DIR__ . '/../src/bootstrap.php';

$response = Router::route($_SERVER['REQUEST_URI']);

if ($response->method === 'GET') {
    $page  = $response->page;
    $route = $response->route;

    require_once __DIR__ . '/../src/layout/page/page.php';
}

if ($response->method === 'POST') {
    header('Content-Type: application/json');

    echo json_encode($response->data);
}