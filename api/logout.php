<?php
// HealthPaws Logout API
// Suppress error display to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once '../includes/auth_functions.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'System configuration error']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Attempt to logout
    $result = $auth->logout();
    
    if ($result['success']) {
        // Logout successful
        echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully!',
            'redirect_url' => 'index.php'
        ]);
    } else {
        // Logout failed
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Logout failed'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
