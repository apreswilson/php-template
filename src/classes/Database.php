<?php

// This is a basic singleton implementation of a database classs
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                $_ENV['db_type'] . ":host=" . $_ENV['host'] . ";port=" . $_ENV['port']. ";dbname=" . $_ENV['db'],
                $_ENV['username'],
                $_ENV['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public static function getInstance() : Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public static function query(string $sql, array $params = []): array | null {
        $sql = ltrim($sql);

        if (
            preg_match('/^(INSERT|UPDATE|DELETE|MERGE)\b/i', $sql)
            && !str_contains(strtoupper($sql), 'RETURNING')
        ) {
            $sql = rtrim($sql) . "\nRETURNING *";
        }

        try {
            $db = self::getInstance();

            $statement = $db->pdo->prepare($sql);

            foreach($params as $param_name => $param_value) {
                $type = gettype($param_value);

                switch($type) {
                    case "boolean":
                        $statement->bindValue(':' . $param_name, $param_value, PDO::PARAM_BOOL);
                        break; 
                    case "integer":
                        $statement->bindValue(':' . $param_name, $param_value, PDO::PARAM_INT);
                        break;
                    case "NULL":
                        $statement->bindValue(':' . $param_name, $param_value, PDO::PARAM_NULL);
                        break;
                    default:
                        $statement->bindValue(':' . $param_name, $param_value, PDO::PARAM_STR);
                }
            }

            $statement->execute();

            return $statement->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
};