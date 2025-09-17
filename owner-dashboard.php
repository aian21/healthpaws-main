<?php
// HealthPaws - Pet Owner Dashboard (Placeholder)
$page_title = "Pet Owner Dashboard — HealthPaws";
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
    <link rel="stylesheet" href="styles/base.css?v=1.0">
</head>
<body>
    <div class="container" style="padding: 40px 20px; text-align: center;">
        <h1>🎉 Welcome to HealthPaws!</h1>
        <p style="font-size: 18px; margin: 20px 0;">Your pet owner account has been created successfully!</p>
        
        <div style="background: linear-gradient(135deg, var(--brand-50), var(--brand-100)); border-radius: 12px; padding: 30px; margin: 30px auto; max-width: 600px;">
            <h2>🐾 Your Pet's Digital Health Profile is Ready</h2>
            <p>Your pet's digital card has been generated and is ready to use. You can now:</p>
            
            <ul style="text-align: left; margin: 20px 0; padding-left: 20px;">
                <li>Access your pet's health records from anywhere</li>
                <li>Share information with any HealthPaws-registered veterinary clinic</li>
                <li>Receive vaccination and appointment reminders</li>
                <li>Keep emergency contact information up to date</li>
            </ul>
        </div>
        
        <div style="margin: 30px 0;">
            <a href="index.php" class="btn btn-primary" style="margin: 10px;">Return to Homepage</a>
            <a href="login.php" class="btn btn-ghost" style="margin: 10px;">Login to Your Account</a>
        </div>
        
        <div style="margin-top: 40px; padding: 20px; background: rgba(42,140,130,.05); border-radius: 8px;">
            <p><strong>Next Steps:</strong></p>
            <p>This is a placeholder dashboard. In the full implementation, you would see:</p>
            <ul style="text-align: left; margin: 10px 0; padding-left: 20px;">
                <li>Your pet's digital health card with QR code</li>
                <li>Vaccination schedule and reminders</li>
                <li>Medical record access and sharing controls</li>
                <li>Emergency contact management</li>
                <li>Notification preferences</li>
            </ul>
        </div>
    </div>
</body>
</html>

