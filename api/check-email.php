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
    
    // Check if email exists and get detailed status
    $stmt = $conn->prepare("
        SELECT user_id, email, password, email_verified 
        FROM User 
        WHERE email = ? 
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    $exists = false;
    $verified_but_incomplete = false;
    $message = 'Email is available';
    
    if ($user) {
        $exists = true;
        
        // Check if user has completed full registration (has real password)
        if ($user['password'] !== 'TEMP_VERIFICATION' && !empty($user['password'])) {
            $message = 'Email already registered';
        } else if ($user['email_verified'] == 1) {
            // Email is verified but registration is incomplete
            $verified_but_incomplete = true;
            $message = 'Email verified but registration incomplete';
        } else {
            $message = 'Email verification pending';
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'email' => $email,
            'exists' => $exists,
            'verified_but_incomplete' => $verified_but_incomplete,
            'message' => $message
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
