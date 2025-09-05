<?php
// HealthPaws - Forgot Password
$page_title = "Forgot Password — HealthPaws";
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
                <h1>Reset your password</h1>
                <p>Enter your email and we'll send you a reset link.</p>
                
                <form id="forgot-form" novalidate>
                    <div class="field-card is-active">
                        <div class="field-title">Enter your email address</div>
                        <div class="field-help">We'll send a password reset link to this email.</div>
                        <label>Email
                            <input type="email" name="email" required placeholder="you@yourclinic.com">
                        </label>
                    </div>
                    
                    <div class="auth-actions" style="justify-content:center; margin-top:20px">
                        <button type="submit" class="btn btn-primary">Send reset link</button>
                    </div>
                </form>
                
                <div class="auth-actions" style="justify-content:center; margin-top:20px">
                    <a href="login.php" class="btn btn-ghost">Back to login</a>
                </div>
            </section>
        </div>
    </div>
    
    <script>
        document.getElementById('forgot-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const email = e.target.email.value;
            if(email) {
                alert('Password reset link sent! Check your email.');
                // In real app, would redirect to confirmation page
                window.location.href = 'login.php';
            }
        });
    </script>
</body>
</html>
