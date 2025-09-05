<?php
// HealthPaws - Login
$page_title = "Login — HealthPaws";
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
        <div class="auth-grid has-aside">
            <section class="auth-form">
                <h1>Welcome Back</h1>
                <p>Sign in to your clinic dashboard and manage your practice with ease.</p>
                
                <form id="login-form" novalidate>
                    <label>Email Address
                        <input type="email" name="email" required placeholder="Enter your email">
                    </label>
                    <label>Password
                        <input type="password" name="password" required minlength="8" placeholder="Enter your password">
                    </label>
                    
                    <div class="auth-actions">
                        <label class="inline-check">
                            <input type="checkbox" name="remember">
                            Remember me for 30 days
                        </label>
                        <a href="forgot-password.php" class="auth-meta">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                        Sign In to Dashboard
                    </button>
                </form>
                
                <div class="auth-actions center" style="margin-top: 32px;">
                    <a class="btn btn-ghost" href="index.php">← Back to Homepage</a>
                </div>
                
                <p class="auth-meta">
                    Don't have an account? <a href="register.php">Create your clinic account</a>
                </p>
            </section>
            <aside class="auth-aside">
                <h3>HealthPaws</h3>
                <p>Manage appointments, records, billing, and client communication from a single dashboard.</p>
            </aside>
        </div>
    </div>
    <script src="scripts/auth.js"></script>
</body>
</html>
