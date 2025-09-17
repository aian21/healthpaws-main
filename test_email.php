<?php
// Quick Email Test Script
header('Content-Type: application/json');

try {
    require_once './includes/phpmailer_email_service.php';
    
    // Test email address (you can change this)
    $test_email = 'terrenalisaacian@gmail.com'; // Change this to your email for testing
    
    if (isset($_GET['email']) && !empty($_GET['email'])) {
        $test_email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
        if (!$test_email) {
            throw new Exception('Invalid email address');
        }
    }
    
    $emailService = new PHPMailerEmailService();
    
    // Generate a test verification code
    $verification_code = $emailService->generateVerificationCode();
    
    // Send test email
    $result = $emailService->sendVerificationEmail($test_email, $verification_code, 'Test Clinic');
    
    if ($result['success']) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Test email sent successfully!',
            'details' => [
                'email' => $test_email,
                'verification_code' => $verification_code,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to send email',
            'error' => $result['error']
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Email test failed',
        'error' => $e->getMessage()
    ]);
}
?>

