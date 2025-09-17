<?php
// Send verification email API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error logging for debugging
error_log("=== SEND VERIFICATION EMAIL API CALLED ===");

try {
    // Ensure API doesn't hang too long
    set_time_limit(30);
    ini_set('default_socket_timeout', '15');

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/phpmailer_email_service.php';
    
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
    if (empty($input['email'])) {
        throw new Exception('Email is required');
    }
    
    $email = strtolower(trim($input['email']));
    $clinic_name = $input['clinic_name'] ?? '';
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Check if email is already registered (with real password, not just verification)
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM User 
        WHERE email = ? AND password IS NOT NULL AND password != '' AND password != 'TEMP_VERIFICATION'
    ");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        throw new Exception('Email is already registered');
    }
    
    // Generate verification code
    $emailService = new PHPMailerEmailService();
    $verification_code = $emailService->generateVerificationCode();
    
    // Calculate expiry time
    $expiry_time = date('Y-m-d H:i:s', strtotime('+' . EmailConfig::$verification_code_expiry . ' minutes'));
    
    // Check if there's already a temporary verification record for this email
    $stmt = $conn->prepare("
        SELECT user_id FROM User 
        WHERE email = ? AND password = 'TEMP_VERIFICATION'
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $existing_temp_user = $stmt->fetch();
    
    if ($existing_temp_user) {
        // Update existing temporary record with new verification code
        $stmt = $conn->prepare("
            UPDATE User 
            SET verification_code = ?, verification_expires = ?, email_verified = FALSE 
            WHERE user_id = ?
        ");
        $stmt->execute([$verification_code, $expiry_time, $existing_temp_user['user_id']]);
        $temp_user_id = $existing_temp_user['user_id'];
        error_log("Updated existing temporary user with ID: $temp_user_id");
    } else {
        // Create new temporary record
        $stmt = $conn->prepare("
            INSERT INTO User (username, email, password, email_verified, verification_code, verification_expires) 
            VALUES (?, ?, 'TEMP_VERIFICATION', FALSE, ?, ?)
        ");
        $stmt->execute([$email, $email, $verification_code, $expiry_time]);
        
        $temp_user_id = $conn->lastInsertId();
        error_log("Temporary user created with ID: $temp_user_id");
    }
    
    // Send verification email
    $email_result = $emailService->sendVerificationEmail($email, $verification_code, $clinic_name);
    
    if ($email_result['success']) {
        error_log("Verification email sent successfully to: $email");
        
        echo json_encode([
            'success' => true,
            'message' => 'Verification code sent to your email',
            'data' => [
                'email' => $email,
                'temp_user_id' => $temp_user_id,
                'expires_in' => EmailConfig::$verification_code_expiry
            ]
        ]);
    } else {
        // Clean up temporary user if email sending failed
        $stmt = $conn->prepare("DELETE FROM User WHERE user_id = ?");
        $stmt->execute([$temp_user_id]);
        
        throw new Exception('Failed to send verification email: ' . $email_result['error']);
    }
    
} catch (Exception $e) {
    error_log("Send verification error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

error_log("=== SEND VERIFICATION EMAIL API COMPLETED ===");
?>
