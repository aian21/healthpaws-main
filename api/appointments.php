<?php
// HealthPaws Appointments API (GET list, POST create/update/cancel)
// Suppress error display to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-HTTP-Method-Override');

// Handle CORS preflight
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

// Require authenticated session
if (!$auth || !$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Resolve clinic scope from session
$currentUser = $auth->getCurrentUser();
if (!$currentUser || empty($currentUser['clinic_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Clinic scope not found']);
    exit;
}
$clinicId = (int)$currentUser['clinic_id'];

// Utility: open DB connection
function db_conn() {
    $db = new Database();
    return $db->getConnection();
}

// Determine effective method (support overrides)
$method = $_SERVER['REQUEST_METHOD'];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper(trim($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']));
}

try {
    if ($method === 'GET') {
        $conn = db_conn();

        // Return metadata for form selects
        if (!empty($_GET['meta'])) {
            $stmt = $conn->prepare("SELECT p.pet_id, p.pet_name, CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name
                                    FROM Pet p
                                    JOIN Owner o ON p.owner_id = o.owner_id
                                    WHERE p.default_clinic_id = ?");
            $stmt->execute([$clinicId]);
            $pets = $stmt->fetchAll();

            $stmt = $conn->prepare("SELECT v.vet_id, CONCAT(v.vet_fname, ' ', v.vet_lname) AS vet_name
                                    FROM Veterinarian v
                                    WHERE v.clinic_id = ?");
            $stmt->execute([$clinicId]);
            $vets = $stmt->fetchAll();

            echo json_encode(['success' => true, 'meta' => ['pets' => $pets, 'vets' => $vets]]);
            exit;
        }

        // Single appointment
        if (!empty($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare("SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.reason,
                                           a.pet_id, p.pet_name,
                                           a.vet_id, CONCAT(v.vet_fname, ' ', v.vet_lname) AS vet_name,
                                           CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name
                                    FROM Appointment a
                                    JOIN Pet p ON a.pet_id = p.pet_id
                                    JOIN Owner o ON p.owner_id = o.owner_id
                                    LEFT JOIN Veterinarian v ON a.vet_id = v.vet_id
                                    WHERE a.clinic_id = ? AND a.appointment_id = ?");
            $stmt->execute([$clinicId, $id]);
            $row = $stmt->fetch();
            if (!$row) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Appointment not found']);
                exit;
            }
            echo json_encode(['success' => true, 'data' => $row]);
            exit;
        }

        // List appointments
        $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 20;
        $fromDate = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');
        $status = isset($_GET['status']) ? $_GET['status'] : null; // optional filter

        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.reason,
                       p.pet_id, p.pet_name,
                       CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name,
                       v.vet_id, CONCAT(v.vet_fname, ' ', v.vet_lname) AS vet_name
                FROM Appointment a
                JOIN Pet p ON a.pet_id = p.pet_id
                JOIN Owner o ON p.owner_id = o.owner_id
                LEFT JOIN Veterinarian v ON a.vet_id = v.vet_id
                WHERE a.clinic_id = ? AND a.appointment_date >= ?";
        $params = [$clinicId, $fromDate];
        if ($status) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY a.appointment_date, a.appointment_time LIMIT " . (int)$limit;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        echo json_encode(['success' => true, 'data' => $rows]);
        exit;

    } else if ($method === 'POST' || $method === 'PATCH') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) { $input = []; }
        $action = isset($input['action']) ? strtolower($input['action']) : 'create';

        $conn = db_conn();

        if ($action === 'create') {
            // Required: pet_id, vet_id (nullable), appointment_date, appointment_time, reason
            if (empty($input['pet_id']) || empty($input['appointment_date']) || empty($input['appointment_time'])) {
                throw new Exception('Missing required fields: pet_id, appointment_date, appointment_time');
            }

            $stmt = $conn->prepare("INSERT INTO Appointment (pet_id, vet_id, clinic_id, appointment_date, appointment_time, status, reason)
                                     VALUES (?, ?, ?, ?, ?, 'Pending', ?)");
            $stmt->execute([
                (int)$input['pet_id'],
                !empty($input['vet_id']) ? (int)$input['vet_id'] : null,
                $clinicId,
                $input['appointment_date'],
                $input['appointment_time'],
                $input['reason'] ?? ''
            ]);

            $id = $conn->lastInsertId();
            echo json_encode(['success' => true, 'appointment_id' => $id]);
            exit;

        } else if ($action === 'update') {
            if (empty($input['appointment_id'])) {
                throw new Exception('Missing appointment_id');
            }
            $appointmentId = (int)$input['appointment_id'];

            // Build dynamic update for allowed fields
            $fields = [];
            $params = [];
            $allowed = ['appointment_date', 'appointment_time', 'status', 'reason', 'vet_id', 'pet_id'];
            foreach ($allowed as $key) {
                if (array_key_exists($key, $input)) {
                    $fields[] = "$key = ?";
                    $params[] = $input[$key];
                }
            }
            if (empty($fields)) {
                throw new Exception('No valid fields to update');
            }
            $params[] = $clinicId;
            $params[] = $appointmentId;

            $sql = "UPDATE Appointment SET " . implode(', ', $fields) . " WHERE clinic_id = ? AND appointment_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true]);
            exit;

        } else if ($action === 'cancel') {
            if (empty($input['appointment_id'])) {
                throw new Exception('Missing appointment_id');
            }
            $appointmentId = (int)$input['appointment_id'];
            $stmt = $conn->prepare("UPDATE Appointment SET status = 'Cancelled' WHERE clinic_id = ? AND appointment_id = ?");
            $stmt->execute([$clinicId, $appointmentId]);
            echo json_encode(['success' => true]);
            exit;
        } else {
            throw new Exception('Unsupported action');
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


