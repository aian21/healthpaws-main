<?php
// Local Database configuration for HealthPaws (XAMPP Development)
// Use this configuration when developing locally with XAMPP

class Database {
    private $host = 'localhost';
    private $db_name = 'healthpaws'; // Local database name
    private $username = 'root';      // Default XAMPP MySQL username
    private $password = '';          // Default XAMPP MySQL password (empty)
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=3306;dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            // More detailed error reporting for local development
            error_log("Database connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }

        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }
}
?>

