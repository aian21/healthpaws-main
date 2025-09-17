<?php
require_once './config/database.php';

header('Content-Type: application/json'); // Ensure JSON response

try {
    $db = new Database();
    $conn = $db->getConnection();
    echo json_encode(['status' => 'success', 'message' => 'Database connection successful']);
    $db->closeConnection();
} catch (Exception $e) {
    http_response_code(500); // Set error status code
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>