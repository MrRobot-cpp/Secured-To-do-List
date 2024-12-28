<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'secured_todo_list';
    private $username = 'root';
    private $password = '';
    private static $instance = null;
    public $conn;


    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Method to get the single instance of the class
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
