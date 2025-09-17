<?php
class Database {
    private $host = "localhost";  // Standard for Namecheap shared hosting
    private $dbname = "healglso_healthpaws";  // Replace with your full DB name (e.g., healglso_mydb)
    private $username = "healglso_checker";  // Replace with your full username (e.g., healglso_dbuser)
    private $password = "N9hMR2Skafj6S7T";  // Replace with the password you set
    private $conn = null;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $this->conn;
        } catch (PDOException $e) {
            // Temporarily display error for debugging (REMOVE IN PRODUCTION)
            echo "<p style='color: red;'>PDO Connection Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            // Log for server records
            error_log("Connection failed: " . $e->getMessage());
            return null;
        }
    }

    // Backwards-compatible with existing code
    public function getConnection() {
        return $this->connect();
    }

    public function checkConnection() {
        if ($this->conn !== null) {
            try {
                $this->conn->query("SELECT 1");
                return true;
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Connection Check Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                error_log("Connection check failed: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    public function closeConnection() {
        $this->conn = null;
    }
}
?>
