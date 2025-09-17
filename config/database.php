<?php
// Database configuration for HealthPaws
// Suppress error display to ensure clean output
error_reporting(0);
ini_set('display_errors', 0);

class Database {
    private $host = '127.0.0.1'; // Correct for external connections
    private $db_name = 'healglso_healthpaws';
    private $username = 'healglso_checker';
    private $password = 'f7mqGuS7#uv('; // (REMOVED FOR PRIVACY)
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
            // Log error but don't display it
            error_log("Database connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed");
        }

        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }
}
?>
