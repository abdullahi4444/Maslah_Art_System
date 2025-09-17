<?php
// Database connection helper using PDO.
// This file reads the configuration from config.php and exports a PDO instance.

class Database
{
    /**
     * @var \PDO
     */
    private static $pdo;

    /**
     * Return a PDO connection. Creates a new one if it doesn't exist.
     *
     * @return \PDO
     */
    public static function getConnection()
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/config.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['db_host'],
                $config['db_name'],
                $config['db_charset']
            );
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$pdo = new \PDO($dsn, $config['db_user'], $config['db_pass'], $options);
            } catch (\PDOException $e) {
                // In a production environment you might want to log this error instead of displaying it.
                exit('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}