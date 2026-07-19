<?php

// This is a basic singleton implementation of a database classs
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $this->pdo = new PDO(
            "pgsql:host=" . $_ENV['host'] . ";port=" . $_ENV['port']. ";dbname=" . $_ENV['db'],
            $_ENV['username'],
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function getInstance() : Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public static function query(string $sql, array $params = []): array | null {
        try {
            $db = self::getInstance();

            $statement = $db->pdo->prepare($sql);

            // Need to bind param values to send stuff like bool over the wire since PDO API isn't a fan of sending raw bool types.
            foreach($params as $param_name => $param_value) {
                $type = gettype($param_value);

                switch($type) {
                    case "boolean":
                        $statement->bindValue(':' . $param_name, $param_value, PDO::PARAM_BOOL);
                        break; 
                    case "integer":
                        $statement->bindValue(':' . $param_name, $param_value, PDO::PARAM_INT);
                        break;
                    default:
                        $statement->bindValue(':' . $param_name, $param_value, PDO::PARAM_STR);
                }
            }

            $statement->execute();

            return $statement->fetchAll();
        } catch (PDOException $e) {
            echo new Error($e);
            return null;
        }
    }
};