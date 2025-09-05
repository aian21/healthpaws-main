<?php
// Database structure check script
// This script checks if all required tables and columns exist

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
    
    $checks = [];
    
    // Check if all required tables exist
    $required_tables = ['User', 'Role', 'UserRole', 'Clinic', 'UserClinic', 'Owner', 'Pet', 'Veterinarian', 'Vaccine', 'Vaccination', 'MedicalRecord', 'Appointment', 'Payment', 'Subscription'];
    
    foreach ($required_tables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        $checks['tables'][$table] = $exists ? 'exists' : 'missing';
    }
    
    // Check specific columns in Clinic table
    $clinic_columns = ['clinic_id', 'clinic_name', 'address', 'clinic_phone', 'clinic_email', 'clinic_subdomain', 'created_at'];
    foreach ($clinic_columns as $column) {
        $stmt = $conn->prepare("SHOW COLUMNS FROM Clinic LIKE ?");
        $stmt->execute([$column]);
        $exists = $stmt->fetch();
        $checks['clinic_columns'][$column] = $exists ? 'exists' : 'missing';
    }
    
    // Check specific columns in User table
    $user_columns = ['user_id', 'username', 'email', 'password', 'user_photo', 'email_verified', 'verification_code', 'verification_expires', 'created_at'];
    foreach ($user_columns as $column) {
        $stmt = $conn->prepare("SHOW COLUMNS FROM User LIKE ?");
        $stmt->execute([$column]);
        $exists = $stmt->fetch();
        $checks['user_columns'][$column] = $exists ? 'exists' : 'missing';
    }
    
    // Check if roles exist
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Role");
    $stmt->execute();
    $role_count = $stmt->fetch()['count'];
    $checks['roles'] = $role_count > 0 ? "exists ($role_count roles)" : 'missing';
    
    // Check sample data
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Clinic");
    $stmt->execute();
    $clinic_count = $stmt->fetch()['count'];
    $checks['sample_data']['clinics'] = $clinic_count;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM User");
    $stmt->execute();
    $user_count = $stmt->fetch()['count'];
    $checks['sample_data']['users'] = $user_count;
    
    echo json_encode([
        'success' => true,
        'database_check' => $checks
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>