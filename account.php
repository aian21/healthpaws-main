<?php
// HealthPaws - Account Management
session_start();

// Include authentication functions
require_once 'includes/auth_functions.php';

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get current user data
$current_user = $auth->getCurrentUser();
if (!$current_user) {
    header('Location: login.php');
    exit;
}

$page_title = "Account Settings — HealthPaws";
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
    <link rel="stylesheet" href="styles/auth.css?v=1.0">
    <style>
        .account-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .account-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .account-header h1 {
            font-family: Poppins, system-ui, sans-serif;
            font-size: 32px;
            margin-bottom: 8px;
            color: var(--ink-900);
        }
        
        .account-header p {
            color: var(--ink-600);
            font-size: 16px;
        }
        
        .account-sections {
            display: grid;
            gap: 24px;
        }
        
        .account-section {
            background: #fff;
            border: 1px solid rgba(42,140,130,.14);
            border-radius: 16px;
            padding: 24px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(42,140,130,.08);
        }
        
        .section-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--ink-900);
            margin: 0;
        }
        
        .section-edit-btn {
            background: none;
            border: 1px solid rgba(42,140,130,.2);
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            color: var(--brand-600);
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .section-edit-btn:hover {
            background: var(--brand-50);
            border-color: var(--brand-300);
        }
        
        .profile-info {
            display: grid;
            gap: 16px;
        }
        
        .info-row {
            display: grid;
            grid-template-columns: 120px 1fr;
            align-items: center;
            gap: 16px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--ink-700);
            font-size: 14px;
        }
        
        .info-value {
            color: var(--ink-900);
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        .back-to-dashboard {
            text-align: center;
            margin-top: 32px;
        }
        
        .back-to-dashboard .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        @media (max-width: 768px) {
            .account-container {
                margin: 20px auto;
                padding: 0 16px;
            }
            
            .info-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body class="auth-shell">
    <div class="account-container">
        <div class="account-header">
            <h1>Account Settings</h1>
            <p>Manage your HealthPaws account and profile information</p>
        </div>
        
        <div class="account-sections">
            <!-- Profile Information -->
            <div class="account-section">
                <div class="section-header">
                    <h2>Profile Information</h2>
                    <button class="section-edit-btn" onclick="toggleEditMode('profile')" id="profileEditBtn">Edit</button>
                </div>
                
                <div class="profile-info" id="profileInfo">
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value"><?php echo htmlspecialchars($current_user['email']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Username:</div>
                        <div class="info-value"><?php echo htmlspecialchars($current_user['username']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Role:</div>
                        <div class="info-value"><?php echo htmlspecialchars($current_user['role_name'] ?? 'User'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Clinic:</div>
                        <div class="info-value"><?php echo htmlspecialchars($current_user['clinic_name'] ?? 'Not assigned'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Member Since:</div>
                        <div class="info-value"><?php echo date('F j, Y', strtotime($current_user['created_at'] ?? 'now')); ?></div>
                    </div>
                </div>
                
                <div class="profile-edit-form" id="profileEditForm" style="display: none;">
                    <form id="profileForm">
                        <div class="form-row">
                            <label>Email Address
                                <input type="email" name="email" value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
                            </label>
                        </div>
                        <div class="form-row">
                            <label>Username
                                <input type="text" name="username" value="<?php echo htmlspecialchars($current_user['username']); ?>" required>
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-ghost" onclick="cancelEdit('profile')">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div class="account-section">
                <div class="section-header">
                    <h2>Security</h2>
                    <button class="section-edit-btn" onclick="toggleEditMode('security')" id="securityEditBtn">Change Password</button>
                </div>
                
                <div class="security-info" id="securityInfo">
                    <div class="info-row">
                        <div class="info-label">Password:</div>
                        <div class="info-value">••••••••</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Last Changed:</div>
                        <div class="info-value"><?php echo date('F j, Y', strtotime($current_user['created_at'] ?? 'now')); ?></div>
                    </div>
                </div>
                
                <div class="security-edit-form" id="securityEditForm" style="display: none;">
                    <form id="passwordForm">
                        <div class="form-row">
                            <label>Current Password
                                <input type="password" name="currentPassword" required>
                            </label>
                        </div>
                        <div class="form-row">
                            <label>New Password
                                <input type="password" name="newPassword" minlength="8" required>
                            </label>
                        </div>
                        <div class="form-row">
                            <label>Confirm New Password
                                <input type="password" name="confirmPassword" minlength="8" required>
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-ghost" onclick="cancelEdit('security')">Cancel</button>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Account Actions -->
            <div class="account-section">
                <div class="section-header">
                    <h2>Account Actions</h2>
                </div>
                
                <div class="account-actions">
                    <button class="btn btn-ghost" onclick="logout()" style="color: #dc3545; border-color: #dc3545;">
                        🚪 Logout
                    </button>
                </div>
            </div>
        </div>
        
        <div class="back-to-dashboard">
            <a href="dashboard.php" class="btn btn-primary">
                ← Back to Dashboard
            </a>
        </div>
    </div>
    
    <script>
        // Toggle edit mode for different sections
        function toggleEditMode(section) {
            const info = document.getElementById(section + 'Info');
            const form = document.getElementById(section + 'EditForm');
            const editBtn = document.getElementById(section + 'EditBtn');
            
            if (form.style.display === 'none') {
                info.style.display = 'none';
                form.style.display = 'block';
                editBtn.textContent = 'Cancel';
                editBtn.onclick = () => cancelEdit(section);
            } else {
                cancelEdit(section);
            }
        }
        
        // Cancel edit mode
        function cancelEdit(section) {
            const info = document.getElementById(section + 'Info');
            const form = document.getElementById(section + 'EditForm');
            const editBtn = document.getElementById(section + 'EditBtn');
            
            info.style.display = 'grid';
            form.style.display = 'none';
            editBtn.textContent = section === 'profile' ? 'Edit' : 'Change Password';
            editBtn.onclick = () => toggleEditMode(section);
        }
        
        // Handle profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                email: formData.get('email'),
                username: formData.get('username')
            };
            
            try {
                // In a real app, you'd send this to an API endpoint
                alert('Profile update functionality would be implemented here');
                cancelEdit('profile');
            } catch (error) {
                alert('Error updating profile: ' + error.message);
            }
        });
        
        // Handle password form submission
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const newPassword = formData.get('newPassword');
            const confirmPassword = formData.get('confirmPassword');
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                return;
            }
            
            if (newPassword.length < 8) {
                alert('Password must be at least 8 characters long');
                return;
            }
            
            try {
                // In a real app, you'd send this to an API endpoint
                alert('Password change functionality would be implemented here');
                cancelEdit('security');
            } catch (error) {
                alert('Error changing password: ' + error.message);
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
    </script>
</body>
</html>


