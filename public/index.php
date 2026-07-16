<?php
require __DIR__ . '/../src/database.php';

$db = new Database();

print_r($db->query("SELECT * FROM todos"));

echo "Connected!";
?>