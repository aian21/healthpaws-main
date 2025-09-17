<?php
// Verify email code API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error logging for debugging
error_log("=== VERIFY EMAIL API CALLED ===");

try {
    require_once __DIR__ . '/../config/database.php';
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    error_log("Received input: " . json_encode($input));
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    if (empty($input['email']) || empty($input['code'])) {
        throw new Exception('Email and verification code are required');
    }
    
    $email = strtolower(trim($input['email']));
    $code = trim($input['code']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Validate code format
    if (!preg_match('/^\d{6}$/', $code)) {
        throw new Exception('Verification code must be 6 digits');
    }
    
    // Check verification code in database
    $database = new Database();
    $conn = $database->getConnection();
    
    // Fetch the most recent TEMP_VERIFICATION user for this email
    $stmt = $conn->prepare("
        SELECT user_id, verification_code, verification_expires, email_verified, password 
        FROM User 
        WHERE email = ? 
        ORDER BY user_id DESC 
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('No verification record found for this email');
    }
    
    // If user already completed full registration (real password), block verification
    if (!empty($user['password']) && $user['password'] !== 'TEMP_VERIFICATION') {
        throw new Exception('Email is already registered');
    }
    
    // If already verified (e.g., duplicate submission), treat as success
    if ($user['email_verified']) {
        echo json_encode([
            'success' => true,
            'message' => 'Email verified successfully',
            'data' => [
                'email' => $email,
                'user_id' => $user['user_id']
            ]
        ]);
        return;
    }
    
    // Check if code has expired
    $now = date('Y-m-d H:i:s');
    if (!empty($user['verification_expires']) && $user['verification_expires'] < $now) {
        throw new Exception('Verification code has expired. Please request a new one.');
    }
    
    // Check if code matches
    if (empty($user['verification_code']) || $user['verification_code'] !== $code) {
        throw new Exception('Invalid verification code');
    }
    
    // Mark email as verified
    $stmt = $conn->prepare("
        UPDATE User 
        SET email_verified = TRUE, verification_code = NULL, verification_expires = NULL 
        WHERE user_id = ?
    ");
    $stmt->execute([$user['user_id']]);
    
    error_log("Email verified successfully for user ID: " . $user['user_id']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Email verified successfully',
        'data' => [
            'email' => $email,
            'user_id' => $user['user_id']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Verify email error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

error_log("=== VERIFY EMAIL API COMPLETED ===");
?>
