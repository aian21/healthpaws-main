<?php
// HealthPaws Pet Owner Registration API
// Suppress error display to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error logging for debugging
error_log("=== PET OWNER REGISTRATION API CALLED ===");

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
    
    // Validate required fields for pet owner
    $required_fields = ['firstName', 'lastName', 'email', 'phone', 'address', 'password', 'petName', 'species'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    error_log("✓ Required fields validated");
    
    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Validate password strength
    if (strlen($input['password']) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    
    // Check password confirmation
    if ($input['password'] !== $input['confirmPassword']) {
        throw new Exception('Password confirmation does not match');
    }
    
    // Sanitize input data
    $data = [
        // Owner information
        'firstName' => htmlspecialchars(trim($input['firstName'])),
        'lastName' => htmlspecialchars(trim($input['lastName'])),
        'email' => strtolower(trim($input['email'])),
        'phone' => htmlspecialchars(trim($input['phone'])),
        'address' => htmlspecialchars(trim($input['address'])),
        'password' => $input['password'],
        
        // Pet information
        'petName' => htmlspecialchars(trim($input['petName'])),
        'species' => htmlspecialchars(trim($input['species'])),
        'breed' => htmlspecialchars(trim($input['breed'] ?? '')),
        'gender' => htmlspecialchars(trim($input['gender'] ?? '')),
        'birthday' => $input['birthday'] ?? null,
        'weight' => $input['weight'] ?? null,
        'microchipNumber' => htmlspecialchars(trim($input['microchipNumber'] ?? '')),
        'insuranceProvider' => htmlspecialchars(trim($input['insuranceProvider'] ?? '')),
        'insurancePolicyNumber' => htmlspecialchars(trim($input['insurancePolicyNumber'] ?? '')),
        'specialNeeds' => htmlspecialchars(trim($input['specialNeeds'] ?? '')),
        
        // Notification preferences
        'notifications' => $input['notifications'] ?? [],
        'notificationMethod' => $input['notificationMethod'] ?? 'email',
        
        // Emergency contact
        'emergencyName' => htmlspecialchars(trim($input['emergencyName'] ?? '')),
        'emergencyRelationship' => htmlspecialchars(trim($input['emergencyRelationship'] ?? '')),
        'emergencyPhone' => htmlspecialchars(trim($input['emergencyPhone'] ?? '')),
        
        // Agreement
        'agreeTerms' => isset($input['agreeTerms']) && $input['agreeTerms']
    ];
    
    // Validate terms agreement
    if (!$data['agreeTerms']) {
        throw new Exception('You must agree to the terms and conditions');
    }
    
    error_log("✓ Sanitized data: " . json_encode($data));
    
    // Check if auth instance exists
    if (!isset($auth) || !$auth) {
        throw new Exception('Authentication system not initialized');
    }
    error_log("✓ Auth instance verified");
    
    error_log("🚀 About to call registerPetOwner method...");
    
    // Attempt to register the pet owner
    $result = $auth->registerPetOwner($data);
    
    error_log("📋 Registration result: " . json_encode($result));
    
    if ($result['success'] && isset($result['owner_id']) && isset($result['user_id'])) {
        error_log("✅ Registration reported success, verifying data...");
        
        // Registration successful - verify data was actually created
        try {
            require_once '../config/database.php';
            $database = new Database();
            $conn = $database->getConnection();
            
            // Verify user was created
            $stmt = $conn->prepare("SELECT user_id, email FROM User WHERE user_id = ?");
            $stmt->execute([$result['user_id']]);
            $user = $stmt->fetch();
            
            if (!$user) {
                error_log("❌ User verification failed - user not found in database");
                throw new Exception('User creation verification failed');
            }
            error_log("✅ User verified: " . json_encode($user));
            
            // Verify owner was created
            $stmt = $conn->prepare("SELECT owner_id, owner_fname, owner_lname FROM Owner WHERE owner_id = ?");
            $stmt->execute([$result['owner_id']]);
            $owner = $stmt->fetch();
            
            if (!$owner) {
                error_log("❌ Owner verification failed - owner not found in database");
                throw new Exception('Owner creation verification failed');
            }
            error_log("✅ Owner verified: " . json_encode($owner));
            
            // Verify pet was created
            if (isset($result['pet_id'])) {
                $stmt = $conn->prepare("SELECT pet_id, pet_name, species FROM Pet WHERE pet_id = ?");
                $stmt->execute([$result['pet_id']]);
                $pet = $stmt->fetch();
                
                if (!$pet) {
                    error_log("❌ Pet verification failed - pet not found in database");
                    throw new Exception('Pet creation verification failed');
                }
                error_log("✅ Pet verified: " . json_encode($pet));
            }
            
            error_log("🎉 Pet owner registration verification successful - all data confirmed!");
            
            // Registration successful and verified
            echo json_encode([
                'success' => true,
                'message' => 'Pet owner account created successfully!',
                'data' => [
                    'user_id' => $result['user_id'],
                    'owner_id' => $result['owner_id'],
                    'pet_id' => $result['pet_id'] ?? null,
                    'owner_name' => $owner['owner_fname'] . ' ' . $owner['owner_lname'],
                    'pet_name' => $pet['pet_name'] ?? null,
                    'digital_card_code' => $result['digital_card_code'] ?? null,
                    'redirect_url' => "owner-dashboard.php"
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
    error_log("❌ Pet owner registration error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

error_log("=== PET OWNER REGISTRATION API COMPLETED ===");
?>

