<?php
// HealthPaws Registration API
// Suppress error display to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error logging for debugging
error_log("=== REGISTRATION API CALLED ===");

try {
    require_once '../includes/auth_functions.php';
    error_log("✓ Auth functions loaded successfully");
} catch (Exception $e) {
    error_log("✗ Failed to load auth functions: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'System configuration error: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("✗ Invalid method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    error_log("✓ Received input: " . json_encode($input));
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $required_fields = ['clinic_name', 'business_email', 'subdomain', 'owner_password', 'plan'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    error_log("✓ Required fields validated");
    
    // Validate email format
    if (!filter_var($input['business_email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Validate password strength
    if (strlen($input['owner_password']) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    
    // Validate subdomain format
    if (!preg_match('/^[a-z0-9-]{3,20}$/', $input['subdomain'])) {
        throw new Exception('Subdomain must be 3-20 characters, lowercase letters, numbers, and hyphens only');
    }
    
    // Sanitize input
    $data = [
        'clinic_name' => htmlspecialchars(trim($input['clinic_name'])),
        'business_email' => strtolower(trim($input['business_email'])),
        'subdomain' => strtolower(trim($input['subdomain'])),
        'owner_password' => $input['owner_password'],
        'plan' => $input['plan'],
        'address' => htmlspecialchars(trim($input['address'] ?? '')),
        'clinic_phone' => htmlspecialchars(trim($input['clinic_phone'] ?? '')),
        'owner_fname' => htmlspecialchars(trim($input['owner_fname'] ?? '')),
        'owner_lname' => htmlspecialchars(trim($input['owner_lname'] ?? '')),
        'owner_phone' => htmlspecialchars(trim($input['owner_phone'] ?? '')),
        'vet_fname' => htmlspecialchars(trim($input['vet_fname'] ?? '')),
        'vet_lname' => htmlspecialchars(trim($input['vet_lname'] ?? '')),
        'specialization' => htmlspecialchars(trim($input['specialization'] ?? '')),
        'license_number' => htmlspecialchars(trim($input['license_number'] ?? ''))
    ];
    
    error_log("✓ Sanitized data: " . json_encode($data));
    
    // Check if auth instance exists
    if (!isset($auth) || !$auth) {
        throw new Exception('Authentication system not initialized');
    }
    error_log("✓ Auth instance verified");
    
    error_log("🚀 About to call registerClinic method...");
    
    // Attempt to register the clinic
    $result = $auth->registerClinic($data);
    
    error_log("📋 Registration result: " . json_encode($result));
    
    if ($result['success'] && isset($result['clinic_id']) && isset($result['user_id'])) {
        error_log("✅ Registration reported success, verifying data...");
        
        // Registration successful - verify data was actually created
        try {
            require_once '../config/database.php';
            $database = new Database();
            $conn = $database->getConnection();
            
            // Verify clinic was created
            $stmt = $conn->prepare("SELECT clinic_id, clinic_name, clinic_subdomain FROM Clinic WHERE clinic_id = ?");
            $stmt->execute([$result['clinic_id']]);
            $clinic = $stmt->fetch();
            
            if (!$clinic) {
                error_log("❌ Clinic verification failed - clinic not found in database");
                throw new Exception('Clinic creation verification failed');
            }
            error_log("✅ Clinic verified: " . json_encode($clinic));
            
            // Verify user was created
            $stmt = $conn->prepare("SELECT user_id, email FROM User WHERE user_id = ?");
            $stmt->execute([$result['user_id']]);
            $user = $stmt->fetch();
            
            if (!$user) {
                error_log("❌ User verification failed - user not found in database");
                throw new Exception('User creation verification failed');
            }
            error_log("✅ User verified: " . json_encode($user));
            
            // Verify UserClinic relationship
            $stmt = $conn->prepare("SELECT * FROM UserClinic WHERE user_id = ? AND clinic_id = ?");
            $stmt->execute([$result['user_id'], $result['clinic_id']]);
            $userClinic = $stmt->fetch();
            
            if (!$userClinic) {
                error_log("❌ UserClinic verification failed - relationship not found");
                throw new Exception('UserClinic relationship verification failed');
            }
            error_log("✅ UserClinic relationship verified: " . json_encode($userClinic));
            
            error_log("🎉 Registration verification successful - all data confirmed!");
            
            // Registration successful and verified
            echo json_encode([
                'success' => true,
                'message' => 'Clinic registered successfully!',
                'data' => [
                    'clinic_id' => $result['clinic_id'],
                    'user_id' => $result['user_id'],
                    'subdomain' => $result['subdomain'],
                    'clinic_name' => $clinic['clinic_name'],
                    'login_url' => "dashboard.php?subdomain=" . urlencode($result['subdomain'])
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("❌ Registration verification failed: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Registration verification failed: ' . $e->getMessage()
            ]);
        }
        
    } else {
        error_log("❌ Registration failed: " . ($result['error'] ?? 'Unknown error'));
        // Registration failed
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $result['error'] ?? 'Registration failed - unknown error'
        ]);
    }
    
} catch (Exception $e) {
    error_log("❌ Registration error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

error_log("=== REGISTRATION API COMPLETED ===");
?>
