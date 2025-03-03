<?php
class GlobalDB {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Read DB connection parameters from the environment
        $host    = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname  = $_ENV['DB_NAME'] ?? 'test_db';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8';

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("DB Connection failed: " . $e->getMessage());
            $this->pdo = null;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>
