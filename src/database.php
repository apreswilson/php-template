<?php

// This is a basic singleton implementation of a database classs
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    /**
     * Creates a new database connection instance.
     *
     * Private to enforce the singleton pattern. Use getInstance()
     * to retrieve the shared Database instance.
     */
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

    /**
     * Gets the singleton Database instance.
     *
     * Creates the instance if it does not already exist.
     *
     * @return Database The shared Database instance.
     */
    public static function getInstance() : Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Executes a prepared SQL query with bound parameters.
     *
     * Automatically determines parameter types and binds values
     * before execution to ensure values such as booleans and integers
     * are sent with the correct PDO types.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params Named parameters to bind to the query.
     *
     * @return array The query results as an associative array.
     */
    public static function query(string $sql, array $params = []): array {
        $db = self::getInstance();

        $statement = $db->pdo->prepare($sql);

        // Need to bind param values to send stuff like bool over the wire since executes default binding wasn't working for me.
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
    }
};