<?php
// HealthPaws - Email Verification Success
$page_title = "Email Verified — HealthPaws";
$user_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : 'your@email.com';

// Check if user has completed full registration
require_once 'config/database.php';
$registration_completed = false;

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Check if user has a real password (not TEMP_VERIFICATION)
    $stmt = $conn->prepare("
        SELECT user_id, email, password 
        FROM User 
        WHERE email = ? AND email_verified = TRUE
        LIMIT 1
    ");
    $stmt->execute([$user_email]);
    $user = $stmt->fetch();
    
    if ($user && $user['password'] !== 'TEMP_VERIFICATION' && !empty($user['password'])) {
        $registration_completed = true;
    }
} catch (Exception $e) {
    error_log("Verification success page error: " . $e->getMessage());
}
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
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body class="auth-shell">
    <div class="auth-card">
        <div class="auth-grid">
            <section class="auth-form">
                <div class="field-card is-active" style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 16px;">✅</div>
                    <div class="field-title">Email Verified Successfully!</div>
                    <div class="field-help">
                        Your email <strong><?php echo $user_email; ?></strong> has been verified.
                        <?php if ($registration_completed): ?>
                            <br>You can now access your HealthPaws dashboard.
                        <?php else: ?>
                            <br>Please complete your clinic registration to access your dashboard.
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="auth-actions" style="justify-content:center; margin-top:20px">
                    <?php if ($registration_completed): ?>
                        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        <a href="login.php" class="btn btn-ghost">Sign In</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary">Complete Registration</a>
                        <a href="login.php" class="btn btn-ghost">Sign In</a>
                    <?php endif; ?>
                </div>
                
                <div class="field-help" style="margin-top:20px; text-align: center; color: #666;">
                    <?php if ($registration_completed): ?>
                        Welcome to HealthPaws! 🐾
                    <?php else: ?>
                        Complete your clinic setup to get started! 🏥
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>


