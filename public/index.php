<?php
// Load bootstrap
require_once __DIR__ . '/../src/bootstrap.php';

$page = Router::route($_SERVER['REQUEST_URI']);

// Load layout
require_once __DIR__ . '/../src/layout/page/page.php';