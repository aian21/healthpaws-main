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
    
    // Check if email is already registered
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM User WHERE email = ?");
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
    
    // Store verification code in database (temporary record)
    $stmt = $conn->prepare("
        INSERT INTO User (username, email, password, email_verified, verification_code, verification_expires) 
        VALUES (?, ?, 'TEMP_VERIFICATION', FALSE, ?, ?)
    ");
    $stmt->execute([$email, $email, $verification_code, $expiry_time]);
    
    $temp_user_id = $conn->lastInsertId();
    error_log("Temporary user created with ID: $temp_user_id");
    
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
