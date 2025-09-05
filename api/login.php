<?php
// HealthPaws Login API
// Suppress error display to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Ensure no output before JSON
if (function_exists('ob_clean')) {
    ob_clean();
}

try {
    require_once '../includes/auth_functions.php';
    
    // Check if auth instance was created successfully
    if (!$auth) {
        throw new Exception('Authentication system not initialized');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'System configuration error: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    if (empty($input['email']) || empty($input['password'])) {
        throw new Exception('Email and password are required');
    }
    
    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Sanitize input
    $email = strtolower(trim($input['email']));
    $password = $input['password'];
    
    // Attempt to login
    $result = $auth->login($email, $password);
    
    if ($result['success']) {
        // Compute redirect URL based on environment and clinic subdomain
        $clinicSubdomain = $result['user']['clinic_subdomain'];
        $clinicName = $result['user']['clinic_name'];
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        $isLocal = stripos($host, 'localhost') !== false
            || $host === '127.0.0.1'
            || $remoteAddr === '127.0.0.1'
            || $remoteAddr === '::1';

        if ($isLocal) {
            // On localhost, mock subdomain via query params
            $redirectUrl = 'dashboard.php?subdomain=' . urlencode($clinicSubdomain) . '&clinic=' . urlencode($clinicName);
        } else if (preg_match('/healthpaws\\.co$/i', $host)) {
            // In production under healthpaws.co, redirect to subdomain
            $redirectUrl = 'https://' . $clinicSubdomain . '.healthpaws.co/dashboard.php';
        } else {
            // Fallback to query params
            $redirectUrl = 'dashboard.php?subdomain=' . urlencode($clinicSubdomain) . '&clinic=' . urlencode($clinicName);
        }

        // Login successful
        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'data' => [
                'user_id' => $result['user']['user_id'],
                'email' => $result['user']['email'],
                'role' => $result['user']['role_name'],
                'clinic_id' => $result['user']['clinic_id'],
                'clinic_name' => $result['user']['clinic_name'],
                'clinic_subdomain' => $result['user']['clinic_subdomain'],
                'redirect_url' => $redirectUrl
            ]
        ]);
    } else {
        // Login failed
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => $result['error']
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
