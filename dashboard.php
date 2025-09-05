<?php
// HealthPaws - Dashboard
session_start();

// Enable verbose errors on localhost only to help debugging
$__host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$__remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$__isLocal = stripos($__host, 'localhost') !== false || $__host === '127.0.0.1' || $__remote === '127.0.0.1' || $__remote === '::1';
if ($__isLocal) {
    @ini_set('display_errors', 1);
    @error_reporting(E_ALL);
}

// Include authentication functions
require_once 'includes/auth_functions.php';

// Re-enable verbose errors on localhost (included files may disable it)
if ($__isLocal) {
    @ini_set('display_errors', 1);
    @error_reporting(E_ALL);
}

// Check if user is logged in (ensure $auth is initialized)
if (!$auth || !$auth->isLoggedIn()) {
    // Redirect to login if not authenticated
    header('Location: login.php');
    exit;
}

// Get current user data
$current_user = $auth->getCurrentUser();
if (!$current_user) {
    // Redirect to login if user data not found
    header('Location: login.php');
    exit;
}

// Include database connection
require_once 'config/database.php';

// Determine clinic subdomain and name
$session_subdomain = isset($_SESSION['clinic_subdomain']) ? $_SESSION['clinic_subdomain'] : '';
$session_clinic_name = isset($_SESSION['clinic_name']) ? $_SESSION['clinic_name'] : '';
$get_subdomain = isset($_GET['subdomain']) ? $_GET['subdomain'] : '';
$get_clinic = isset($_GET['clinic']) ? $_GET['clinic'] : '';

// Prefer explicit GET params, otherwise fall back to session
$subdomain = htmlspecialchars($get_subdomain ?: $session_subdomain ?: 'demo');
$clinic_name = htmlspecialchars($get_clinic ?: $session_clinic_name ?: $current_user['clinic_name']);

// Canonicalize URL on localhost: ensure query params present for mock subdomain
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$isLocal = stripos($host, 'localhost') !== false || $host === '127.0.0.1' || $remoteAddr === '127.0.0.1' || $remoteAddr === '::1';
if ($isLocal) {
    $needsRedirect = (!isset($_GET['subdomain']) || !isset($_GET['clinic'])) && !headers_sent();
    if ($needsRedirect) {
        $redirectUrl = 'dashboard.php?subdomain=' . urlencode($subdomain) . '&clinic=' . urlencode($clinic_name);
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// On production, derive subdomain from host if not provided
if (!$isLocal && !$get_subdomain && preg_match('/^([a-z0-9-]+)\.healthpaws\.co$/i', $host, $m)) {
    $subdomain = htmlspecialchars($m[1]);
}

// Enforce canonical subdomain: if current URL subdomain differs from session clinic's subdomain, redirect
if (!empty($session_subdomain) && strcasecmp($subdomain, $session_subdomain) !== 0 && !headers_sent()) {
    if ($isLocal) {
        $redirectUrl = 'dashboard.php?subdomain=' . urlencode($session_subdomain) . '&clinic=' . urlencode($clinic_name);
        header('Location: ' . $redirectUrl);
        exit;
    } else if (preg_match('/healthpaws\\.co$/i', $host)) {
        $redirectUrl = 'https://' . $session_subdomain . '.healthpaws.co/dashboard.php';
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// Initialize database connection
$conn = null;
try {
$database = new Database();
$conn = $database->getConnection();
} catch (Exception $e) {
    // If DB connection fails, continue with mock data
    $conn = null;
}

// Get clinic data from database if available, else fallback
$current_clinic = null;
if ($conn instanceof PDO) {
    try {
$clinic_query = "SELECT * FROM Clinic WHERE clinic_id = ?";
$clinic_stmt = $conn->prepare($clinic_query);
$clinic_stmt->execute([$current_user['clinic_id']]);
$current_clinic = $clinic_stmt->fetch();
    } catch (Exception $e) {
        $current_clinic = null;
    }
}

if (!$current_clinic) {
    // Fallback to demo data if clinic not found
    $current_clinic = [
        'clinic_id' => $current_user['clinic_id'],
        'clinic_name' => $current_user['clinic_name'] ?? 'Demo Veterinary Clinic',
        'address' => '123 Main Street, Demo City',
        'clinic_phone' => '(555) 123-4567',
        'clinic_email' => $current_user['email']
    ];
}

// Get dashboard statistics
$stats = [];
if ($conn instanceof PDO) {
try {
    // Today's appointments
    $today_appts = $conn->prepare("SELECT COUNT(*) as count FROM Appointment WHERE clinic_id = ? AND appointment_date = CURDATE()");
    $today_appts->execute([$current_clinic['clinic_id']]);
    $stats['today_appointments'] = $today_appts->fetch()['count'];

    // Check-ins (confirmed appointments)
    $checkins = $conn->prepare("SELECT COUNT(*) as count FROM Appointment WHERE clinic_id = ? AND status = 'Confirmed' AND appointment_date = CURDATE()");
    $checkins->execute([$current_clinic['clinic_id']]);
    $stats['checkins'] = $checkins->fetch()['count'];

    // Outstanding invoices
    $outstanding = $conn->prepare("SELECT COUNT(*) as count FROM Payment WHERE payment_status = 'Pending'");
    $outstanding->execute();
    $stats['outstanding_invoices'] = $outstanding->fetch()['count'];

    // Messages (placeholder)
    $stats['messages'] = 3;
} catch (Exception $e) {
    // Fallback stats if database query fails
        $stats = ['today_appointments' => 18, 'checkins' => 12, 'outstanding_invoices' => 7, 'messages' => 3];
    }
} else {
    // Fallback stats if no DB connection
    $stats = ['today_appointments' => 18, 'checkins' => 12, 'outstanding_invoices' => 7, 'messages' => 3];
}

// Get upcoming appointments
$appointments = [];
if ($conn instanceof PDO) {
try {
    $appts_query = "SELECT a.*, p.pet_name, CONCAT(o.owner_fname, ' ', o.owner_lname) as owner_name 
                    FROM Appointment a 
                    JOIN Pet p ON a.pet_id = p.pet_id 
                    JOIN Owner o ON p.owner_id = o.owner_id 
                    WHERE a.clinic_id = ? AND a.appointment_date >= CURDATE() 
                    ORDER BY a.appointment_date, a.appointment_time 
                    LIMIT 5";
    $appts_stmt = $conn->prepare($appts_query);
    $appts_stmt->execute([$current_clinic['clinic_id']]);
    $appointments = $appts_stmt->fetchAll();
} catch (Exception $e) {
    // Fallback appointments if database query fails
        $appointments = [
            ['appointment_time' => '9:00', 'pet_name' => 'Bella', 'owner_name' => 'Maria R.', 'reason' => 'Wellness exam', 'status' => 'Confirmed'],
            ['appointment_time' => '9:30', 'pet_name' => 'Max', 'owner_name' => 'Jamal K.', 'reason' => 'Vaccination', 'status' => 'Confirmed'],
            ['appointment_time' => '10:15', 'pet_name' => 'Luna', 'owner_name' => 'Sam T.', 'reason' => 'Dental consult', 'status' => 'Pending']
        ];
    }
} else {
    // Fallback appointments if no DB connection
    $appointments = [
        ['appointment_time' => '9:00', 'pet_name' => 'Bella', 'owner_name' => 'Maria R.', 'reason' => 'Wellness exam', 'status' => 'Confirmed'],
        ['appointment_time' => '9:30', 'pet_name' => 'Max', 'owner_name' => 'Jamal K.', 'reason' => 'Vaccination', 'status' => 'Confirmed'],
        ['appointment_time' => '10:15', 'pet_name' => 'Luna', 'owner_name' => 'Sam T.', 'reason' => 'Dental consult', 'status' => 'Pending']
    ];
}

$page_title = $current_clinic['clinic_name'] . " - HealthPaws Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/landing.css">
    <style>
        /* Modern sticky header for dashboard */
        .dash-header{position:sticky; top:14px; z-index:60}
        .dash-header .inner{display:flex; align-items:center; justify-content:space-between; gap:12px; background:rgba(255,255,255,.9); border:1px solid rgba(42,140,130,.14); border-radius:16px; padding:10px 14px; backdrop-filter:saturate(160%) blur(10px); -webkit-backdrop-filter:saturate(160%) blur(10px); box-shadow:0 10px 30px rgba(22,46,46,.10)}
        .dash-brand{display:flex; align-items:center; gap:10px}
        .dash-brand .logo{width:30px; height:30px; display:grid; place-items:center; border-radius:8px; background:linear-gradient(135deg, var(--brand-50), #fff); border:1px solid rgba(42,140,130,.18)}
        .dash-brand .name{font-family:Poppins, system-ui, sans-serif; font-weight:700; letter-spacing:.2px; color:var(--ink-900)}
        .dash-brand .sub{font-size:12px; color:var(--ink-600); font-weight:600}
        .dash-brand .sub .subdomain{color:var(--brand-600); font-family:monospace}
        .dash-nav{display:flex; gap:8px}
        .dash-nav a{display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border-radius:999px; color:var(--ink-700); text-decoration:none; border:1px solid transparent}
        .dash-nav a:hover{background:var(--brand-50); text-decoration:none}
        .dash-nav a[aria-current]{background:var(--brand-50); border-color:rgba(42,140,130,.20)}
        .dash-actions{display:flex; align-items:center; gap:8px}

        .dash-layout{min-height:100vh; background:radial-gradient(1200px 400px at 60% -100px, rgba(42,140,130,.08), transparent 60%)}
        .dash-main{padding:16px; padding-bottom:120px}
        .dash-shell{display:grid; grid-template-columns:240px 1fr; gap:16px; padding:16px}
        .dash-sidebar{position:sticky; top:14px; height:calc(100vh - 28px)}
        .dash-sidebar .inner{display:flex; flex-direction:column; gap:10px; height:100%; background:rgba(255,255,255,.9); border:1px solid rgba(42,140,130,.14); border-radius:16px; padding:14px; backdrop-filter:saturate(160%) blur(10px); -webkit-backdrop-filter:saturate(160%) blur(10px); box-shadow:0 10px 30px rgba(22,46,46,.08)}
        .sidebar-brand{display:flex; align-items:center; gap:10px; padding:4px 6px; border-radius:12px}
        .sidebar-brand .logo{width:30px; height:30px; display:grid; place-items:center; border-radius:8px; background:linear-gradient(135deg, var(--brand-50), #fff); border:1px solid rgba(42,140,130,.18)}
        .sidebar-brand .name{font-family:Poppins, system-ui, sans-serif; font-weight:700; color:var(--ink-900)}
        .sidebar-brand .sub{font-size:12px; color:var(--ink-600); font-weight:600}
        .sidebar-brand .sub .subdomain{color:var(--brand-600); font-family:monospace}
        .sidebar-nav{display:flex; flex-direction:column; gap:4px}
        .sidebar-nav a{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:12px; color:var(--ink-700); text-decoration:none; border:1px solid transparent}
        .sidebar-nav a:hover{background:var(--brand-50); text-decoration:none}
        .sidebar-nav a[aria-current]{background:var(--brand-50); border-color:rgba(42,140,130,.20)}
        .dash-icon{width:18px; height:18px; flex:0 0 18px; color:var(--ink-700)}
        .stat-cards{display:grid; grid-template-columns:repeat(4,1fr); gap:12px}
        .stat{background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:16px; padding:14px}
        .stat .label{color:var(--ink-500); font-size:12px}
        .stat .value{font-size:24px; font-weight:700; color:var(--ink-900)}
        .panel{margin-top:14px; background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:16px; padding:14px}
        table{width:100%; border-collapse:collapse}
        th, td{padding:10px; border-bottom:1px solid rgba(42,140,130,.12); text-align:left}
        .dash-top .actions{display:flex; gap:8px}
        
        /* Tab Views */
        .dash-view{display:none}
        .dash-view.active{display:block}
        
        /* Calendar View */
        .calendar-header{display:flex; justify-content:space-between; align-items:center; margin-bottom:20px}
        .calendar-nav{display:flex; align-items:center; gap:10px}
        .current-month{font-size:18px; font-weight:600}
        .calendar-grid{display:grid; grid-template-columns:repeat(7,1fr); gap:1px; background:#f0f0f0; border-radius:8px; overflow:hidden}
        .calendar-day-header{background:#f8f9fa; padding:10px; text-align:center; font-weight:600; color:var(--ink-700)}
        .calendar-day{background:#fff; padding:20px 10px; text-align:center; position:relative; min-height:60px; display:flex; align-items:center; justify-content:center}
        .calendar-day.has-appt{background:var(--brand-50)}
        .appt-count{position:absolute; top:5px; right:5px; background:var(--brand-600); color:#fff; border-radius:50%; width:20px; height:20px; font-size:12px; display:flex; align-items:center; justify-content:center}
        
        /* View Headers */
        .view-header{display:flex; justify-content:space-between; align-items:center; margin-bottom:20px}
        .search-bar{margin-bottom:20px}
        .search-input{width:100%; padding:10px; border:1px solid rgba(42,140,130,.2); border-radius:8px; font-size:14px}
        
        /* Patient Cards */
        .patients-list{display:grid; gap:12px}
        .patient-card{background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:12px; padding:16px; display:flex; justify-content:space-between; align-items:center}
        .patient-info h3{margin:0 0 4px 0; font-size:16px}
        .patient-info p{margin:0; color:var(--ink-600); font-size:14px}
        .patient-actions{display:flex; gap:8px}
        
        /* Billing */
        .billing-stats{display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px}
        .invoices-list{display:grid; gap:12px}
        .invoice-item{background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:12px; padding:16px; display:flex; justify-content:space-between; align-items:center}
        .invoice-details h3{margin:0 0 4px 0; font-size:16px}
        .invoice-details p{margin:0; color:var(--ink-600); font-size:14px}
        .invoice-amount{font-size:18px; font-weight:600; color:var(--ink-900)}
        .invoice-status{padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600}
        .invoice-status.paid{background:#dcfce7; color:#166534}
        .invoice-status.pending{background:#fef3c7; color:#92400e}
        
        /* Inventory */
        .inventory-grid{display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:16px}
        .inventory-item{background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:12px; padding:16px}
        .inventory-item h3{margin:0 0 8px 0; font-size:16px}
        .inventory-item p{margin:0 0 12px 0; color:var(--ink-600)}
        .stock-level{padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600; text-align:center}
        .stock-level.good{background:#dcfce7; color:#166534}
        .stock-level.low{background:#fef3c7; color:#92400e}
        .stock-level.critical{background:#fee2e2; color:#991b1b}
        
        /* Reports */
        .reports-grid{display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px}
        .report-card{background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:12px; padding:20px}
        .report-card h3{margin:0 0 12px 0; font-size:16px; color:var(--ink-700)}
        .report-value{font-size:32px; font-weight:700; color:var(--ink-900); margin-bottom:8px}
        .report-change{font-size:14px; padding:4px 8px; border-radius:6px; display:inline-block}
        .report-change.positive{background:#dcfce7; color:#166534}
        .report-change.negative{background:#fee2e2; color:#991b1b}
        
        /* User Menu */
        .user-menu{position:relative; display:inline-block}
        .user-menu-btn{background:none; border:none; display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:8px; cursor:pointer; color:var(--ink-700)}
        .user-menu-btn:hover{background:rgba(42,140,130,.08)}
        .user-menu-dropdown{position:absolute; top:100%; right:0; background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); min-width:200px; z-index:1000; display:none}
        .user-menu-dropdown.show{display:block}
        .user-menu-item{padding:12px 16px; border-bottom:1px solid rgba(42,140,130,.08); cursor:pointer; transition:background 0.2s}
        .user-menu-item:hover{background:rgba(42,140,130,.04)}
        .user-menu-item:last-child{border-bottom:none}
        .user-menu-item.logout{color:#dc3545}
        .user-menu-item.logout:hover{background:#fee2e2}
        
        /* Floating bottom nav */
        .dash-bottom-nav{position:fixed; left:50%; bottom:18px; transform:translateX(-50%); background:rgba(255,255,255,.85); border:1px solid rgba(42,140,130,.18); backdrop-filter:saturate(150%) blur(10px); -webkit-backdrop-filter:saturate(150%) blur(10px); box-shadow:0 20px 50px rgba(22,46,46,.15); border-radius:20px; padding:8px}
        .dash-bottom-nav .items{display:flex; align-items:center; gap:6px}
        .dash-bottom-nav a{display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; min-width:84px; padding:8px 10px; border-radius:14px; color:var(--ink-700); text-decoration:none; border:1px solid transparent; cursor:pointer}
        .dash-bottom-nav a[aria-current]{background:var(--brand-50); border-color:rgba(42,140,130,.20)}
        
        /* Clinic info styling */
        .clinic-info .clinic-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--ink-600);
        }
        
        .clinic-info .subdomain {
            color: var(--brand-600);
            font-weight: 600;
            font-family: monospace;
        }
        
        .clinic-info .separator {
            color: var(--ink-400);
        }
        
        .clinic-info .clinic-address {
            color: var(--ink-500);
        }
        
        /* Status indicators */
        .status-confirmed {
            background: #dcfce7;
            color: #166534;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Toasts */
        .toast-container{position:fixed; right:16px; bottom:18px; display:flex; flex-direction:column; gap:8px; z-index:400}
        .toast{display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; border:1px solid rgba(42,140,130,.14); background:#fff; box-shadow:var(--shadow-sm); color:var(--ink-800); animation:toast-in .18s ease-out}
        .toast.success{border-color:#16a34a33}
        .toast.error{border-color:#dc262633}
        .toast.info{border-color:#2563eb33}
        .toast .icon{width:18px; height:18px}
        @keyframes toast-in{from{transform:translateY(8px); opacity:.0} to{transform:translateY(0); opacity:1}}

        /* Responsive nav visibility */
        @media(min-width: 721px){
            .dash-bottom-nav{display:none}
        }

        @media(max-width: 980px){
            .stat-cards{grid-template-columns:1fr 1fr}
            .billing-stats{grid-template-columns:1fr 1fr}
            .dash-shell{grid-template-columns:1fr}
            .dash-sidebar{display:none}
            .dash-main{padding:16px; padding-bottom:120px}
        }
        @media(max-width: 720px){
            .dash-nav{display:none}
            .stat-cards{grid-template-columns:1fr}
            .billing-stats{grid-template-columns:1fr}
            .dash-bottom-nav a{min-width:68px}
            .calendar-grid{grid-template-columns:repeat(7,1fr)}
            .calendar-day{padding:10px 5px; min-height:40px}
        }
    </style>
</head>
<body>
    <div class="dash-layout">
        <div class="toast-container" id="toastContainer"></div>
        <div class="dash-shell">
            <aside class="dash-sidebar" aria-label="Sidebar navigation">
                <div class="inner">
                    <div class="sidebar-brand">
                        <div class="logo" aria-hidden="true">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M8.5 13c.8 0 1.5-.7 1.5-1.5S9.3 10 8.5 10 7 10.7 7 11.5 7.7 13 8.5 13z"/>
                                <path d="M15.5 13c.8 0 1.5-.7 1.5-1.5S16.3 10 15.5 10 14 10.7 14 11.5s.7 1.5 1.5 1.5z"/>
                                <path d="M12 13.5c-2.2 0-4 1.8-4 4 0 1.4 1.3 2.5 4 2.5s4-1.1 4-2.5c0-2.2-1.8-4-4-4z"/>
                                <path d="M6.5 9.5c.9 0 1.5-.7 1.5-1.5S7.4 6.5 6.5 6.5 5 7.2 5 8s.6 1.5 1.5 1.5z"/>
                                <path d="M17.5 9.5c.9 0 1.5-.7 1.5-1.5s-.6-1.5-1.5-1.5S16 7.2 16 8s.6 1.5 1.5 1.5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="name">HealthPaws</div>
                            <div class="sub"><span class="subdomain"><?php echo $subdomain; ?></span>.healthpaws.co</div>
                        </div>
                    </div>
                    <nav class="sidebar-nav">
                        <a href="#" onclick="showTab('view-overview', this)" aria-current="page">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10l9-7 9 7"/><path d="M9 22V12h6v10"/></svg>
                            <span>Overview</span>
                        </a>
                        <a href="#" onclick="showTab('view-appointments', this)">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            <span>Appointments</span>
                        </a>
                        <a href="#" onclick="showTab('view-calendar', this)">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            <span>Calendar</span>
                        </a>
                        <a href="#" onclick="showTab('view-patients', this)">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 13.5c-2.6 0-4.5 1.8-4.5 4.2 0 1.6 1.9 2.8 4.5 2.8s4.5-1.2 4.5-2.8c0-2.4-1.9-4.2-4.5-4.2z"/><path d="M8.3 9.5a1.7 1.7 0 1 0 0-3.4 1.7 1.7 0 0 0 0 3.4zM15.7 9.5a1.7 1.7 0 1 0 0-3.4 1.7 1.7 0 0 0 0 3.4z"/></svg>
                            <span>Patients</span>
                        </a>
                        <a href="#" onclick="showTab('view-billing', this)">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            <span>Billing</span>
                        </a>
                        <a href="#" onclick="showTab('view-inventory', this)">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M21 10v6a2 2 0 0 1-1.2 1.8l-7.8 3.2-7.8-3.2A2 2 0 0 1 3 16v-6"/></svg>
                            <span>Inventory</span>
                        </a>
                        <a href="#" onclick="showTab('view-reports', this)">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="M7 15l3-3 2 2 5-5"/></svg>
                            <span>Reports</span>
                        </a>
                    </nav>
                    <div style="margin-top:auto"></div>
                    <nav class="sidebar-nav" aria-label="Account">
                        <a href="account.php">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4z"/><path d="M4 20c0-3.3 3.6-6 8-6s8 2.7 8 6"/></svg>
                            <span>Account Settings</span>
                        </a>
                        <a href="#" onclick="logout()">
                            <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                            <span>Logout</span>
                        </a>
                    </nav>
                </div>
            </aside>
            <main class="dash-main">
            <!-- Overview Tab (Default) -->
            <div id="view-overview" class="dash-view active">
                <div class="clinic-info" style="margin-bottom:10px">
                    <h1 style="font-family:Poppins, system-ui, sans-serif; font-size:22px; margin-bottom:4px"><?php echo $current_clinic['clinic_name']; ?></h1>
                    <div class="clinic-meta">
                        <span class="subdomain"><?php echo $subdomain; ?>.healthpaws.co</span>
                        <span class="separator">•</span>
                        <span class="clinic-address"><?php echo $current_clinic['address']; ?></span>
                    </div>
                </div>
                
                <section class="stat-cards">
                    <div class="stat">
                        <div class="label">Today appointments</div>
                        <div class="value" id="stat-today-appointments"><?php echo $stats['today_appointments']; ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Check-ins</div>
                        <div class="value" id="stat-checkins"><?php echo $stats['checkins']; ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Outstanding invoices</div>
                        <div class="value" id="stat-outstanding"><?php echo $stats['outstanding_invoices']; ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Messages</div>
                        <div class="value" id="stat-messages"><?php echo $stats['messages']; ?></div>
                    </div>
                </section>
                
                <section class="panel" aria-labelledby="appt-title">
                    <h2 id="appt-title" style="font-size:18px; margin-bottom:8px">Upcoming appointments</h2>
                    <table id="appointments-table">
                        <thead>
                            <tr><th>Time</th><th>Patient</th><th>Owner</th><th>Reason</th><th>Status</th></tr>
                        </thead>
                        <tbody id="appointments-tbody">
                            <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?php echo isset($appt['appointment_time']) ? $appt['appointment_time'] : $appt[0]; ?></td>
                                <td><?php echo isset($appt['pet_name']) ? $appt['pet_name'] : $appt[1]; ?></td>
                                <td><?php echo isset($appt['owner_name']) ? $appt['owner_name'] : $appt[2]; ?></td>
                                <td><?php echo isset($appt['reason']) ? $appt['reason'] : $appt[3]; ?></td>
                                <td><span class="status-<?php echo strtolower(str_replace(' ', '-', isset($appt['status']) ? $appt['status'] : $appt[4])); ?>"><?php echo isset($appt['status']) ? $appt['status'] : $appt[4]; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </div>

            <!-- Appointments Tab -->
            <div id="view-appointments" class="dash-view">
                <div class="view-header">
                    <h2>Appointments</h2>
                    <button class="btn btn-primary" onclick="showNewAppointmentForm()">New Appointment</button>
                </div>
                
                <div class="search-bar">
                    <input type="text" placeholder="Search appointments..." class="search-input">
                </div>
                
                <div class="panel">
                    <table>
                        <thead>
                            <tr><th>Date</th><th>Time</th><th>Patient</th><th>Owner</th><th>Vet</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?php echo isset($appt['appointment_date']) ? $appt['appointment_date'] : date('Y-m-d'); ?></td>
                                <td><?php echo isset($appt['appointment_time']) ? $appt['appointment_time'] : $appt[0]; ?></td>
                                <td><?php echo isset($appt['pet_name']) ? $appt['pet_name'] : $appt[1]; ?></td>
                                <td><?php echo isset($appt['owner_name']) ? $appt['owner_name'] : $appt[2]; ?></td>
                                <td>Dr. Smith</td>
                                <td><?php echo isset($appt['reason']) ? $appt['reason'] : $appt[3]; ?></td>
                                <td><span class="status-<?php echo strtolower(str_replace(' ', '-', isset($appt['status']) ? $appt['status'] : $appt[4])); ?>"><?php echo isset($appt['status']) ? $appt['status'] : $appt[4]; ?></span></td>
                                <td>
                                    <button class="btn btn-ghost" onclick="editAppointment(<?php echo isset($appt['appointment_id']) ? $appt['appointment_id'] : '1'; ?>)">Edit</button>
                                    <button class="btn btn-ghost" onclick="cancelAppointment(<?php echo isset($appt['appointment_id']) ? $appt['appointment_id'] : '1'; ?>)">Cancel</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Calendar View -->
            <div id="view-calendar" class="dash-view">
                <div class="view-header">
                    <h2>Calendar</h2>
                    <div class="calendar-nav">
                        <button class="btn btn-ghost" onclick="previousMonth()">← Previous</button>
                        <span class="current-month" id="currentMonth">December 2024</span>
                        <button class="btn btn-ghost" onclick="nextMonth()">Next →</button>
                    </div>
                </div>
                <div class="calendar-grid" id="calendarGrid">
                    <!-- Calendar will be populated by JavaScript -->
                </div>
            </div>

            <!-- Patients View -->
            <div id="view-patients" class="dash-view">
                <div class="view-header">
                    <h2>Patients</h2>
                    <button class="btn btn-primary" onclick="openPatientModal()">Add Patient</button>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Search patients..." class="search-input" id="patientsSearch" oninput="loadPatientsList(this.value)">
                </div>
                <div class="patients-list" id="patientsList"></div>
            </div>

            <!-- Billing View -->
            <div id="view-billing" class="dash-view">
                <div class="view-header">
                    <h2>Billing & Payments</h2>
                    <button class="btn btn-primary" onclick="showNewInvoiceForm()">New Invoice</button>
                </div>
                <div class="billing-stats">
                    <div class="stat"><div class="label">Outstanding</div><div class="value">$2,450</div></div>
                    <div class="stat"><div class="label">This month</div><div class="value">$8,920</div></div>
                    <div class="stat"><div class="label">Overdue</div><div class="value">$890</div></div>
                </div>
                <div class="invoices-list">
                    <div class="invoice-item">
                        <div class="invoice-details">
                            <h3>INV-001</h3>
                            <p>Bella - Wellness Exam</p>
                        </div>
                        <div class="invoice-amount">$125</div>
                        <div class="invoice-status paid">Paid</div>
                    </div>
                    <div class="invoice-item">
                        <div class="invoice-details">
                            <h3>INV-002</h3>
                            <p>Max - Vaccination</p>
                        </div>
                        <div class="invoice-amount">$85</div>
                        <div class="invoice-status pending">Pending</div>
                    </div>
                </div>
            </div>

            <!-- Inventory View -->
            <div id="view-inventory" class="dash-view">
                <div class="view-header">
                    <h2>Inventory</h2>
                    <button class="btn btn-primary" onclick="showAddInventoryForm()">Add Item</button>
                </div>
                <div class="inventory-grid">
                    <div class="inventory-item">
                        <h3>Vaccines</h3>
                        <p>Stock: 45</p>
                        <div class="stock-level good">Good</div>
                    </div>
                    <div class="inventory-item">
                        <h3>Bandages</h3>
                        <p>Stock: 12</p>
                        <div class="stock-level low">Low</div>
                    </div>
                    <div class="inventory-item">
                        <h3>Antibiotics</h3>
                        <p>Stock: 8</p>
                        <div class="stock-level critical">Critical</div>
                    </div>
                </div>
            </div>

            <!-- Reports View -->
            <div id="view-reports" class="dash-view">
                <div class="view-header">
                    <h2>Reports & Analytics</h2>
                    <select class="report-period" onchange="changeReportPeriod(this.value)">
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">This year</option>
                    </select>
                </div>
                <div class="reports-grid">
                    <div class="report-card">
                        <h3>Revenue</h3>
                        <div class="report-value">$12,450</div>
                        <div class="report-change positive">+12% vs last month</div>
                    </div>
                    <div class="report-card">
                        <h3>Appointments</h3>
                        <div class="report-value">156</div>
                        <div class="report-change positive">+8% vs last month</div>
                    </div>
                    <div class="report-card">
                        <h3>New Patients</h3>
                        <div class="report-value">23</div>
                        <div class="report-change positive">+15% vs last month</div>
                    </div>
                    <div class="report-card">
                        <h3>Average Visit Cost</h3>
                        <div class="report-value">$89</div>
                        <div class="report-change negative">-3% vs last month</div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Floating bottom navigation -->
        <nav class="dash-bottom-nav" aria-label="Primary">
            <div class="items">
                <a href="#" onclick="showTab('view-overview', this)" aria-current="page">
                    <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10l9-7 9 7"/><path d="M9 22V12h6v10"/></svg>
                    <span>Overview</span>
                </a>
                <a href="#" onclick="showTab('view-appointments', this)">
                    <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span>Appts</span>
                </a>
                <a href="#" onclick="showTab('view-calendar', this)">
                    <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span>Calendar</span>
                </a>
                <a href="#" onclick="showTab('view-patients', this)">
                    <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 13.5c-2.6 0-4.5 1.8-4.5 4.2 0 1.6 1.9 2.8 4.5 2.8s4.5-1.2 4.5-2.8c0-2.4-1.9-4.2-4.5-4.2z"/><path d="M8.3 9.5a1.7 1.7 0 1 0 0-3.4 1.7 1.7 0 0 0 0 3.4zM15.7 9.5a1.7 1.7 0 1 0 0-3.4 1.7 1.7 0 0 0 0 3.4z"/></svg>
                    <span>Patients</span>
                </a>
                <a href="#" onclick="showTab('view-billing', this)">
                    <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    <span>Billing</span>
                </a>
                <a href="#" onclick="showTab('view-inventory', this)">
                    <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M21 10v6a2 2 0 0 1-1.2 1.8l-7.8 3.2-7.8-3.2A2 2 0 0 1 3 16v-6"/></svg>
                    <span>Inventory</span>
                </a>
                <a href="#" onclick="showTab('view-reports', this)">
                    <svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="M7 15l3-3 2 2 5-5"/></svg>
                    <span>Reports</span>
                </a>
            </div>
        </nav>
    </div>

    <script>
        function showToast(message, type='success'){
            const container = document.getElementById('toastContainer');
            if(!container) return;
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const icon = type==='success' ? '✅' : (type==='error' ? '⚠️' : 'ℹ️');
            toast.innerHTML = `<span class="icon">${icon}</span><span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(()=>{
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(6px)';
                setTimeout(()=>toast.remove(), 180);
            }, 2600);
        }
        // Simple modal for creating/editing appointments
        function openAppointmentModal(appt){
            const modalId = 'apptModal';
            let modal = document.getElementById(modalId);
            if(!modal){
                modal = document.createElement('div');
                modal.id = modalId;
                modal.className = 'modal';
                modal.setAttribute('aria-hidden','false');
                modal.innerHTML = `
                    <div class="modal-backdrop" onclick="closeAppointmentModal()"></div>
                    <div class="modal-dialog">
                        <button class="modal-close" onclick="closeAppointmentModal()">✕</button>
                        <div class="modal-header">
                            <h3 id="apptModalTitle">New Appointment</h3>
                            <p>Fill in appointment details below.</p>
                        </div>
                        <div class="modal-form">
                            <div class="form-row">
                                <div>
                                    <label>Patient</label>
                                    <select id="apptPet"></select>
                                </div>
                                <div>
                                    <label>Veterinarian</label>
                                    <select id="apptVet"></select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Date</label>
                                    <input type="date" id="apptDate" />
                                </div>
                                <div>
                                    <label>Time</label>
                                    <input type="time" id="apptTime" />
                                </div>
                            </div>
                            <div>
                                <label>Reason</label>
                                <textarea id="apptReason" rows="3"></textarea>
                            </div>
                            <div class="form-actions">
                                <button class="btn btn-ghost" onclick="closeAppointmentModal()">Cancel</button>
                                <button class="btn btn-primary" id="apptSubmitBtn">Save</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            } else {
                modal.setAttribute('aria-hidden','false');
            }

            // Load metadata (pets, vets)
            loadAppointmentMeta().then(meta=>{
                const petSelect = document.getElementById('apptPet');
                const vetSelect = document.getElementById('apptVet');
                petSelect.innerHTML = meta.pets.map(p=>`<option value="${p.pet_id}">${p.pet_name} — ${p.owner_name}</option>`).join('');
                vetSelect.innerHTML = meta.vets.map(v=>`<option value="${v.vet_id}">${v.vet_name}</option>`).join('');

                // Populate if editing
                const title = document.getElementById('apptModalTitle');
                const dateEl = document.getElementById('apptDate');
                const timeEl = document.getElementById('apptTime');
                const reasonEl = document.getElementById('apptReason');
                const submitBtn = document.getElementById('apptSubmitBtn');

                if(appt && appt.appointment_id){
                    title.textContent = 'Edit Appointment';
                    petSelect.value = appt.pet_id || '';
                    vetSelect.value = appt.vet_id || '';
                    dateEl.value = (appt.appointment_date||'').slice(0,10);
                    timeEl.value = (appt.appointment_time||'').slice(0,5);
                    reasonEl.value = appt.reason || '';
                    submitBtn.onclick = ()=>submitAppointment('update', {
                        appointment_id: appt.appointment_id,
                        pet_id: petSelect.value,
                        vet_id: vetSelect.value,
                        appointment_date: dateEl.value,
                        appointment_time: timeEl.value,
                        reason: reasonEl.value
                    });
                } else {
                    title.textContent = 'New Appointment';
                    dateEl.value = new Date().toISOString().slice(0,10);
                    timeEl.value = '09:00';
                    reasonEl.value = '';
                    submitBtn.onclick = ()=>submitAppointment('create', {
                        pet_id: petSelect.value,
                        vet_id: vetSelect.value,
                        appointment_date: dateEl.value,
                        appointment_time: timeEl.value,
                        reason: reasonEl.value
                    });
                }
            });
        }

        function closeAppointmentModal(){
            const modal = document.getElementById('apptModal');
            if(modal){ modal.setAttribute('aria-hidden','true'); }
        }

        async function loadAppointmentMeta(){
            const res = await fetch('api/appointments.php?meta=1');
            const j = await res.json();
            return (j.success && j.meta) ? j.meta : {pets:[],vets:[]};
        }

        async function submitAppointment(action, payload){
            try{
                const res = await fetch('api/appointments.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, ...payload })
                });
                const j = await res.json();
                if(!j.success) throw new Error(j.error||'Request failed');
                closeAppointmentModal();
                // Refresh data
                loadDashboardStats();
                loadUpcomingAppointments();
                if (document.getElementById('view-appointments').classList.contains('active')) {
                    loadAppointmentsTable();
                }
                showToast(action==='cancel' ? 'Appointment cancelled' : (action==='update' ? 'Appointment updated' : 'Appointment created'), 'success');
            }catch(e){ alert('Error: ' + e.message); }
        }

        async function loadAppointmentsTable(){
            try{
                const res = await fetch('api/appointments.php?limit=50&from=' + new Date().toISOString().slice(0,10));
                const json = await res.json();
                if(json.success){
                    const tbody = document.querySelector('#view-appointments .panel tbody');
                    tbody.innerHTML = '';
                    json.data.forEach(a=>{
                        const tr = document.createElement('tr');
                        const statusClass = ('status-' + String(a.status||'').toLowerCase().replace(/\s+/g,'-'));
                        tr.innerHTML = `
                            <td>${(a.appointment_date||'').toString().slice(0,10)}</td>
                            <td>${(a.appointment_time||'').toString().slice(0,5)}</td>
                            <td>${a.pet_name||''}</td>
                            <td>${a.owner_name||''}</td>
                            <td>${a.vet_name||''}</td>
                            <td>${a.reason||''}</td>
                            <td><span class="${statusClass}">${a.status||''}</span></td>
                            <td>
                                <button class="btn btn-ghost" data-appt-id="${a.appointment_id}" onclick="onEditAppointment(${a.appointment_id})">Edit</button>
                                <button class="btn btn-ghost" data-appt-id="${a.appointment_id}" onclick="onCancelAppointment(${a.appointment_id})">Cancel</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            }catch(e){ console.error('Appointments table load error', e); }
        }

        async function onEditAppointment(id){
            try{
                const res = await fetch('api/appointments.php?id=' + id);
                const j = await res.json();
                if(!j.success) throw new Error(j.error||'Failed to load appointment');
                openAppointmentModal(j.data);
            }catch(e){ alert('Error: ' + e.message); }
        }

        async function onCancelAppointment(id){
            if(!confirm('Are you sure you want to cancel this appointment?')) return;
            submitAppointment('cancel', { appointment_id: id });
        }
        // Patients CRUD
        async function loadPatientsList(q=''){
            try{
                const res = await fetch('api/patients.php?limit=100' + (q?('&q='+encodeURIComponent(q)):'') );
                const j = await res.json();
                if(!j.success) return;
                const list = document.getElementById('patientsList');
                list.innerHTML = '';
                j.data.forEach(p=>{
                    const div = document.createElement('div');
                    div.className = 'patient-card';
                    div.innerHTML = `
                        <div class="patient-info">
                            <h3>${p.pet_name}</h3>
                            <p>${p.species||''} ${p.breed?('• '+p.breed):''} • ${p.owner_name||''}</p>
                        </div>
                        <div class="patient-actions">
                            <button class="btn btn-ghost" onclick="openPatientModal(${p.pet_id})">Edit</button>
                            <button class="btn btn-ghost" onclick="deletePatient(${p.pet_id})">Delete</button>
                        </div>
                    `;
                    list.appendChild(div);
                });
            }catch(e){ console.error('Patients load error', e); }
        }

        async function openPatientModal(petId){
            const modalId = 'patientModal';
            let modal = document.getElementById(modalId);
            if(!modal){
                modal = document.createElement('div');
                modal.id = modalId;
                modal.className = 'modal';
                modal.setAttribute('aria-hidden','false');
                modal.innerHTML = `
                    <div class="modal-backdrop" onclick="closePatientModal()"></div>
                    <div class="modal-dialog">
                        <button class="modal-close" onclick="closePatientModal()">✕</button>
                        <div class="modal-header">
                            <h3 id="patModalTitle">New Patient</h3>
                            <p>Manage patient details.</p>
                        </div>
                        <div class="modal-form">
                            <div class="form-row">
                                <div>
                                    <label>Owner</label>
                                    <select id="patOwner"></select>
                                </div>
                                <div>
                                    <label>Name</label>
                                    <input type="text" id="patName" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Species</label>
                                    <input type="text" id="patSpecies" />
                                </div>
                                <div>
                                    <label>Breed</label>
                                    <input type="text" id="patBreed" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Gender</label>
                                    <input type="text" id="patGender" />
                                </div>
                                <div>
                                    <label>Birthday</label>
                                    <input type="date" id="patBirthday" />
                                </div>
                            </div>
                            <div>
                                <label>Weight (lbs)</label>
                                <input type="number" step="0.1" id="patWeight" />
                            </div>
                            <div class="form-actions">
                                <button class="btn btn-ghost" onclick="closePatientModal()">Cancel</button>
                                <button class="btn btn-primary" id="patSubmitBtn">Save</button>
                            </div>
                        </div>
                    </div>`;
                document.body.appendChild(modal);
            } else {
                modal.setAttribute('aria-hidden','false');
            }

            // Load meta owners
            const meta = await (await fetch('api/patients.php?meta=1')).json();
            const owners = (meta.success && meta.meta) ? meta.meta.owners : [];
            const ownerSel = document.getElementById('patOwner');
            ownerSel.innerHTML = owners.map(o=>`<option value="${o.owner_id}">${o.owner_name} ${o.owner_phone?('('+o.owner_phone+')'):''}</option>`).join('');

            const title = document.getElementById('patModalTitle');
            const nameEl = document.getElementById('patName');
            const speciesEl = document.getElementById('patSpecies');
            const breedEl = document.getElementById('patBreed');
            const genderEl = document.getElementById('patGender');
            const bdayEl = document.getElementById('patBirthday');
            const weightEl = document.getElementById('patWeight');
            const submitBtn = document.getElementById('patSubmitBtn');

            if(petId){
                // edit
                const res = await fetch('api/patients.php?id='+petId);
                const j = await res.json();
                if(!j.success){ alert('Failed to load patient'); return; }
                const p = j.data;
                title.textContent = 'Edit Patient';
                ownerSel.value = p.owner_id;
                nameEl.value = p.pet_name||'';
                speciesEl.value = p.species||'';
                breedEl.value = p.breed||'';
                genderEl.value = p.gender||'';
                bdayEl.value = (p.birthday||'').slice(0,10);
                weightEl.value = p.weight||'';
                submitBtn.onclick = ()=>submitPatient('update', {
                    pet_id: petId,
                    owner_id: ownerSel.value,
                    pet_name: nameEl.value.trim(),
                    species: speciesEl.value.trim(),
                    breed: breedEl.value.trim(),
                    gender: genderEl.value.trim(),
                    birthday: bdayEl.value,
                    weight: weightEl.value
                });
            } else {
                // create
                title.textContent = 'New Patient';
                nameEl.value = speciesEl.value = breedEl.value = genderEl.value = '';
                bdayEl.value = ''; weightEl.value = '';
                submitBtn.onclick = ()=>submitPatient('create', {
                    owner_id: ownerSel.value,
                    pet_name: nameEl.value.trim(),
                    species: speciesEl.value.trim(),
                    breed: breedEl.value.trim(),
                    gender: genderEl.value.trim(),
                    birthday: bdayEl.value,
                    weight: weightEl.value
                });
            }
        }

        function closePatientModal(){
            const modal = document.getElementById('patientModal');
            if(modal){ modal.setAttribute('aria-hidden', 'true'); }
        }

        async function submitPatient(action, payload){
            try{
                const res = await fetch('api/patients.php', {
                    method: action==='update' ? 'PATCH' : 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, ...payload })
                });
                const j = await res.json();
                if(!j.success) throw new Error(j.error||'Request failed');
                closePatientModal();
                loadPatientsList(document.getElementById('patientsSearch')?.value||'');
                showToast(action==='update' ? 'Patient updated' : 'Patient added', 'success');
            }catch(e){ alert('Error: ' + e.message); }
        }

        async function deletePatient(petId){
            if(!confirm('Delete this patient?')) return;
            try{
                const res = await fetch('api/patients.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', pet_id: petId })
                });
                const j = await res.json();
                if(!j.success) throw new Error(j.error||'Delete failed');
                loadPatientsList(document.getElementById('patientsSearch')?.value||'');
                showToast('Patient deleted', 'success');
            }catch(e){ alert('Error: ' + e.message); }
        }
        async function loadDashboardStats(){
            try{
                const res = await fetch('api/dashboard_stats.php');
                const json = await res.json();
                if(json.success){
                    const d = json.data;
                    const set = (id, val)=>{ const el = document.getElementById(id); if(el) el.textContent = val; };
                    set('stat-today-appointments', d.today_appointments);
                    set('stat-checkins', d.checkins);
                    set('stat-outstanding', d.outstanding_invoices);
                    set('stat-messages', d.messages);
                    showToast('Dashboard refreshed', 'info');
                }
            }catch(e){ console.error('Stats load error', e); }
        }

        async function loadUpcomingAppointments(){
            try{
                const res = await fetch('api/appointments.php?limit=5');
                const json = await res.json();
                if(json.success){
                    const tbody = document.getElementById('appointments-tbody');
                    if(!tbody) return;
                    tbody.innerHTML = '';
                    json.data.forEach(a=>{
                        const tr = document.createElement('tr');
                        const statusClass = ('status-' + String(a.status||'').toLowerCase().replace(/\s+/g,'-'));
                        tr.innerHTML = `
                            <td>${(a.appointment_time||'').toString().slice(0,5)}</td>
                            <td>${a.pet_name||''}</td>
                            <td>${a.owner_name||''}</td>
                            <td>${a.reason||''}</td>
                            <td><span class="${statusClass}">${a.status||''}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            }catch(e){ console.error('Upcoming appts load error', e); }
        }
        // Tab management
        function showTab(tabId, el) {
            // Hide all views
            document.querySelectorAll('.dash-view').forEach(view => {
                view.classList.remove('active');
            });
            
            // Show selected view
            document.getElementById(tabId).classList.add('active');
            
            // Update navigation (sidebar and bottom)
            document.querySelectorAll('.sidebar-nav a, .dash-bottom-nav a').forEach(link => {
                link.removeAttribute('aria-current');
            });
            // Determine the clicked element (supports inline onclick and programmatic)
            const targetLink = el && el.closest ? el.closest('a') : (event && event.target && event.target.closest ? event.target.closest('a') : null);
            if (targetLink) {
                targetLink.setAttribute('aria-current', 'page');
            } else {
                // Fallback: match by href containing tabId
                const match = document.querySelector(`.sidebar-nav a[onclick*="${tabId}"]`) || document.querySelector(`.dash-bottom-nav a[onclick*="${tabId}"]`);
                if (match) match.setAttribute('aria-current', 'page');
            }
            
            // Special handling for calendar
            if (tabId === 'view-calendar') {
                initializeCalendar();
            }
        }

        // User menu functions
        function toggleUserMenu() {
            const dropdown = document.getElementById('userMenuDropdown');
            dropdown.classList.toggle('show');
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            if (!userMenu.contains(event.target)) {
                const dropdown = document.getElementById('userMenuDropdown');
                dropdown.classList.remove('show');
            }
        });

        // Logout function
        async function logout() {
            try {
                const response = await fetch('api/logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = result.redirect_url;
                } else {
                    alert('Logout failed: ' + result.error);
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Force redirect even if API fails
                window.location.href = 'index.php';
            }
        }

        // Calendar functionality
        let currentDate = new Date();
        
        function initializeCalendar() {
            updateCalendar();
        }
        
        function updateCalendar() {
            const monthNames = ["January", "February", "March", "April", "May", "June",
                              "July", "August", "September", "October", "November", "December"];
            
            document.getElementById('currentMonth').textContent = 
                monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
            
            // Generate calendar grid
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';
            
            // Add day headers
            ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.textContent = day;
                grid.appendChild(header);
            });
            
            // Get first day of month and number of days
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            // Generate calendar days
            for (let i = 0; i < 42; i++) {
                const day = new Date(startDate);
                day.setDate(startDate.getDate() + i);
                
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                
                if (day.getMonth() === currentDate.getMonth()) {
                    dayElement.textContent = day.getDate();
                    
                    // Add appointment indicators (mock data)
                    if (Math.random() > 0.7) {
                        dayElement.classList.add('has-appt');
                        const count = Math.floor(Math.random() * 3) + 1;
                        const countElement = document.createElement('span');
                        countElement.className = 'appt-count';
                        countElement.textContent = count;
                        dayElement.appendChild(countElement);
                    }
                }
                
                grid.appendChild(dayElement);
            }
        }
        
        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
        }
        
        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
        }

        // Form functions (placeholders)
        function showNewAppointmentForm() {
            openAppointmentModal();
        }
        
        function showNewPatientForm() {
            alert('New patient form would open here');
        }
        
        function showNewInvoiceForm() {
            alert('New invoice form would open here');
        }
        
        function showAddInventoryForm() {
            alert('Add inventory form would open here');
        }
        
        function editAppointment(id) {
            onEditAppointment(id);
        }
        
        function cancelAppointment(id) {
            onCancelAppointment(id);
        }
        
        function viewPatient(id) {
            alert('View patient ' + id + ' would open here');
        }
        
        function editPatient(id) {
            alert('Edit patient ' + id + ' would open here');
        }
        
        function changeReportPeriod(period) {
            alert('Report period changed to ' + period + ' days');
        }

        // Initialize calendar on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Calendar is initialized when tab is shown
            loadDashboardStats();
            loadUpcomingAppointments();
            loadPatientsList('');
        });
    </script>
</body>
</html>
