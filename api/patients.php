<?php
// HealthPaws Patients (Pets) API
// Suppress error display for clean JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-HTTP-Method-Override');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

if (function_exists('ob_clean')) { ob_clean(); }

try { require_once '../includes/auth_functions.php'; }
catch (Exception $e) {
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

function db_conn_pat() {
    $db = new Database();
    return $db->getConnection();
}

$method = $_SERVER['REQUEST_METHOD'];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper(trim($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']));
}

try {
    if ($method === 'GET') {
        $conn = db_conn_pat();

        if (!empty($_GET['meta'])) {
            // Owners for this clinic (any owner with at least one pet for clinic, plus generic list)
            $stmt = $conn->prepare("SELECT o.owner_id, CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name, o.owner_phone
                                    FROM Owner o
                                    ORDER BY o.owner_fname, o.owner_lname");
            $stmt->execute();
            $owners = $stmt->fetchAll();
            echo json_encode(['success' => true, 'meta' => ['owners' => $owners]]);
            exit;
        }

        if (!empty($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare("SELECT p.pet_id, p.pet_name, p.species, p.breed, p.gender, p.birthday, p.weight,
                                           p.owner_id, CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name,
                                           p.default_clinic_id
                                    FROM Pet p
                                    JOIN Owner o ON p.owner_id = o.owner_id
                                    WHERE p.default_clinic_id = ? AND p.pet_id = ?");
            $stmt->execute([$clinicId, $id]);
            $row = $stmt->fetch();
            if (!$row) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Patient not found']);
                exit;
            }
            echo json_encode(['success' => true, 'data' => $row]);
            exit;
        }

        $limit = isset($_GET['limit']) ? max(1, min(200, (int)$_GET['limit'])) : 50;
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $sql = "SELECT p.pet_id, p.pet_name, p.species, p.breed, p.gender, p.birthday, p.weight,
                       CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name
                FROM Pet p
                JOIN Owner o ON p.owner_id = o.owner_id
                WHERE p.default_clinic_id = ?";
        $params = [$clinicId];
        if ($q !== '') {
            $sql .= " AND (p.pet_name LIKE ? OR o.owner_fname LIKE ? OR o.owner_lname LIKE ?)";
            $like = '%' . $q . '%';
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        $sql .= " ORDER BY p.pet_name LIMIT " . (int)$limit;
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    if ($method === 'POST' || $method === 'PATCH' || $method === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) { $input = []; }
        $action = isset($input['action']) ? strtolower($input['action']) : ($method === 'PATCH' ? 'update' : ($method === 'DELETE' ? 'delete' : 'create'));
        $conn = db_conn_pat();

        if ($action === 'create') {
            if (empty($input['owner_id']) || empty($input['pet_name'])) {
                throw new Exception('owner_id and pet_name are required');
            }
            $stmt = $conn->prepare("INSERT INTO Pet (owner_id, default_clinic_id, pet_name, species, breed, gender, birthday, weight)
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                (int)$input['owner_id'],
                $clinicId,
                $input['pet_name'],
                $input['species'] ?? '',
                $input['breed'] ?? '',
                $input['gender'] ?? '',
                !empty($input['birthday']) ? $input['birthday'] : null,
                isset($input['weight']) && $input['weight'] !== '' ? $input['weight'] : null
            ]);
            echo json_encode(['success' => true, 'pet_id' => $conn->lastInsertId()]);
            exit;
        }

        if ($action === 'update') {
            if (empty($input['pet_id'])) { throw new Exception('pet_id is required'); }
            $fields = [];
            $params = [];
            foreach (['owner_id','pet_name','species','breed','gender','birthday','weight'] as $key) {
                if (array_key_exists($key, $input)) {
                    $fields[] = "$key = ?";
                    $params[] = $input[$key] === '' ? null : $input[$key];
                }
            }
            if (empty($fields)) { throw new Exception('No fields to update'); }
            $params[] = $clinicId;
            $params[] = (int)$input['pet_id'];
            $sql = "UPDATE Pet SET " . implode(', ', $fields) . " WHERE default_clinic_id = ? AND pet_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action === 'delete') {
            if (empty($input['pet_id'])) { throw new Exception('pet_id is required'); }
            $stmt = $conn->prepare("DELETE FROM Pet WHERE default_clinic_id = ? AND pet_id = ?");
            try {
                $stmt->execute([$clinicId, (int)$input['pet_id']]);
            } catch (Exception $e) {
                throw new Exception('Unable to delete patient (it may be referenced by other records)');
            }
            echo json_encode(['success' => true]);
            exit;
        }

        throw new Exception('Unsupported action');
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>



