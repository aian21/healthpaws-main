<?php
// HealthPaws Subdomain Availability Check API
// Suppress error display to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once '../includes/auth_functions.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'System configuration error']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get subdomain from query parameter
    $subdomain = $_GET['subdomain'] ?? '';
    
    if (empty($subdomain)) {
        throw new Exception('Subdomain parameter is required');
    }
    
    // Sanitize subdomain
    $subdomain = strtolower(trim($subdomain));
    
    // Check if subdomain is available
    $isAvailable = $auth->isSubdomainAvailable($subdomain);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'subdomain' => $subdomain,
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Subdomain is available' : 'Subdomain is not available'
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
