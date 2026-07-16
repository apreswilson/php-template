<?php

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/config.php';

        $this->pdo = new PDO(
            "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }


    public function query(string $sql): array
    {
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}