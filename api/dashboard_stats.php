<?php
// HealthPaws Dashboard Stats API
// Suppress error display to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

// Ensure no output before JSON
if (function_exists('ob_clean')) {
    ob_clean();
}

try {
    require_once '../includes/auth_functions.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'System configuration error']);
    exit;
}

if (!$auth || !$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$currentUser = $auth->getCurrentUser();
if (!$currentUser || empty($currentUser['clinic_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Clinic scope not found']);
    exit;
}
$clinicId = (int)$currentUser['clinic_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Today's appointments
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM Appointment WHERE clinic_id = ? AND appointment_date = CURDATE()");
    $stmt->execute([$clinicId]);
    $todayAppointments = (int)$stmt->fetch()['count'];

    // Check-ins (confirmed)
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM Appointment WHERE clinic_id = ? AND status = 'Confirmed' AND appointment_date = CURDATE()");
    $stmt->execute([$clinicId]);
    $checkins = (int)$stmt->fetch()['count'];

    // Outstanding invoices
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM Payment WHERE payment_status = 'Pending'");
    $stmt->execute();
    $outstandingInvoices = (int)$stmt->fetch()['count'];

    // Messages placeholder (if you add a messages table, replace this)
    $messages = 3;

    echo json_encode([
        'success' => true,
        'data' => [
            'today_appointments' => $todayAppointments,
            'checkins' => $checkins,
            'outstanding_invoices' => $outstandingInvoices,
            'messages' => $messages
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>






