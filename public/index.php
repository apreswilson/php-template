<?php
// Load vars
require_once __DIR__ . '/../src/vars.php';

// Connect to DB
require __DIR__ . '/../src/database.php';
$db = Database::getInstance();

$params = ["completed" => false];
var_dump($params);

$query = Database::query('SELECT * FROM todos WHERE completed = :completed', ["completed" => false]);