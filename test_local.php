<?php
require_once './config/database_local.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();
    echo json_encode([
        'status' => 'success', 
        'message' => 'Local database connection successful',
        'server_info' => $conn->getAttribute(PDO::ATTR_SERVER_VERSION)
    ]);
    $db->closeConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage(),
        'suggestion' => 'Make sure XAMPP MySQL service is running and healthpaws database exists'
    ]);
}
?>

