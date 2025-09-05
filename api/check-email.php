<?php
// Check if email already exists
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/../config/database.php';
    
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Get email from query parameter or POST data
    $email = $_GET['email'] ?? $_POST['email'] ?? '';
    
    if (empty($email)) {
        throw new Exception('Email parameter is required');
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM User WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    $exists = $result['count'] > 0;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'email' => $email,
            'exists' => $exists,
            'message' => $exists ? 'Email already registered' : 'Email is available'
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
