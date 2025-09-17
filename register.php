<?php
// HealthPaws - Register
$page_title = "Register Clinic — HealthPaws";
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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Inter", system-ui, sans-serif;
            background: linear-gradient(180deg, #f0f9ff, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 800px;
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
        }
        h1 {
            font-family: "Poppins", sans-serif;
            font-size: 28px;
            color: #0f172a;
            margin-bottom: 8px;
            text-align: center;
        }
        .subtitle {
            color: #64748b;
            text-align: center;
            margin-bottom: 30px;
        }
        .progress {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        .progress-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.3s;
        }
        .progress-dot.active {
            background: #0ea5e9;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .step-title {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .step-description {
            color: #64748b;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 640px) {
            .form-row { grid-template-columns: 1fr; }
        }
        label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #0ea5e9;
        }
        .input-affix {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-affix input {
            padding-right: 140px;
        }
        .suffix {
            position: absolute;
            right: 16px;
            color: #64748b;
            font-size: 14px;
        }
        .verification-boxes {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 20px 0;
        }
        .verification-box {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
        }
        .demo-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px;
            margin: 16px 0;
            font-size: 14px;
            color: #1e40af;
        }
        .terms-container {
            height: 200px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            overflow-y: auto;
            background: #f9fafb;
            margin: 16px 0;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        .btn-primary {
            background: #0ea5e9;
            color: white;
        }
        .btn-primary:hover {
            background: #0284c7;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mock-pay {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 20px 0;
        }
        .mock-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .mock-input {
            height: 40px;
            background: #f1f5f9;
            border-radius: 6px;
            margin: 8px 0;
        }
        .mock-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .loading-overlay, .account-creation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .loader, .creation-loader {
            text-align: center;
        }
        .ring, .spinner-ring {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #0ea5e9;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .label, .progress-text {
            color: #64748b;
            font-size: 14px;
        }
        .typing-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
            min-height: 24px;
        }
        .typing-text {
            color: #0f172a;
            font-weight: 500;
        }
        .typing-cursor {
            animation: blink 1s infinite;
            margin-left: 2px;
        }
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        .progress-bar {
            width: 200px;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin: 0 auto 8px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #0ea5e9;
            width: 0%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
                <h1>Create Your Clinic Account</h1>
        <p class="subtitle">Join thousands of veterinary practices using HealthPaws. Start your free 14-day trial.</p>
        
        <div class="progress">
            <div class="progress-dot active"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
            <div class="progress-dot"></div>
        </div>
        
        <form id="register-form" novalidate>
            <!-- Step 1: Clinic Name -->
            <div class="step active" id="step1">
                <div class="step-title">🏥 What's your clinic's name?</div>
                <div class="step-description">Use your public-facing name. You can change this later.</div>
                
                <div class="form-group">
                    <label>Clinic name</label>
                    <input type="text" name="clinicName" placeholder="Ex: Happy Tails Veterinary Clinic" required>
                </div>
            </div>

            <!-- Step 2: Business Email -->
            <div class="step" id="step2">
                <div class="step-title">📧 What's your business email?</div>
                <div class="step-description">This becomes the Owner/Admin account. We'll send verification here.</div>
                
                <div class="form-group">
                    <label>Business email</label>
                    <input type="email" name="businessEmail" placeholder="you@yourclinic.com" required>
                </div>
                            </div>

            <!-- Step 3: Email Verification -->
            <div class="step" id="step3">
                <div class="step-title">📧 Verify your email</div>
                <div class="step-description">We've sent a verification code to your email. Enter it below to continue.</div>
                
                <div class="verification-boxes">
                    <input type="text" class="verification-box" maxlength="1" data-index="0" placeholder="1">
                    <input type="text" class="verification-box" maxlength="1" data-index="1" placeholder="2">
                    <input type="text" class="verification-box" maxlength="1" data-index="2" placeholder="3">
                    <input type="text" class="verification-box" maxlength="1" data-index="3" placeholder="4">
                    <input type="text" class="verification-box" maxlength="1" data-index="4" placeholder="5">
                    <input type="text" class="verification-box" maxlength="1" data-index="5" placeholder="6">
                            </div>
                
                <div class="verification-status" style="text-align: center; margin: 16px 0; font-size: 14px; color: #64748b;">
                    <span id="verification-message">Sending verification code...</span>
                            </div>
                
                <div style="text-align: center; margin-top: 16px;">
                    <button type="button" id="resend-code-btn" class="btn btn-secondary" style="font-size: 14px; padding: 8px 16px;">Resend Code</button>
                                </div>
                
                
                            </div>

            <!-- Step 4: Clinic Address -->
            <div class="step" id="step4">
                <div class="step-title">📍 Where is your clinic located?</div>
                <div class="step-description">Provide your clinic's address for client information.</div>
                
                <div class="form-group">
                    <label>Clinic address</label>
                    <input type="text" name="address" placeholder="123 Main Street, City, State" required>
                </div>
                                    </div>
                                    
            <!-- Step 5: Clinic Phone -->
            <div class="step" id="step5">
                <div class="step-title">📞 What's your clinic's phone number?</div>
                <div class="step-description">This will be displayed to clients for appointments and inquiries.</div>
                
                <div class="form-group">
                    <label>Clinic phone</label>
                    <input type="tel" name="clinicPhone" placeholder="(555) 123-4567" required>
                                    </div>
                                </div>
                                
            <!-- Step 6: Owner First Name -->
            <div class="step" id="step6">
                <div class="step-title">👋 What's your first name?</div>
                <div class="step-description">Tell us about the clinic owner/administrator.</div>
                
                <div class="form-group">
                    <label>First name</label>
                    <input type="text" name="ownerFname" placeholder="John" required>
                </div>
                                </div>
                                
            <!-- Step 7: Owner Last Name -->
            <div class="step" id="step7">
                <div class="step-title">And your last name?</div>
                <div class="step-description">We'll use this for your admin account.</div>
                
                <div class="form-group">
                    <label>Last name</label>
                    <input type="text" name="ownerLname" placeholder="Doe" required>
                                    </div>
            </div>

            <!-- Step 8: Owner Phone -->
            <div class="step" id="step8">
                <div class="step-title">📱 What's your personal phone number?</div>
                <div class="step-description">This is for your admin account, separate from the clinic phone.</div>
                
                <div class="form-group">
                    <label>Your phone number</label>
                    <input type="tel" name="ownerPhone" placeholder="(555) 987-6543" required>
                                </div>
                            </div>

            <!-- Step 9: Plan Selection -->
            <div class="step" id="step9">
                <div class="step-title">📋 Choose your plan</div>
                <div class="step-description">You can change plans anytime. Trial applies automatically.</div>
                
                <div class="form-group">
                    <label>Plan</label>
                                    <select name="plan" required>
                        <option value="">Select a plan</option>
                        <option value="starter">Starter - $29/month</option>
                        <option value="pro" selected>Pro - $79/month</option>
                        <option value="enterprise">Enterprise - $149/month</option>
                                    </select>
                </div>
                            </div>

            <!-- Step 10: Subdomain -->
            <div class="step" id="step10">
                <div class="step-title">🌐 Pick your subdomain</div>
                <div class="step-description">Letters, numbers, and dashes only. Used for your login URL.</div>
                
                <div class="form-group">
                    <label>Preferred subdomain</label>
                                    <div class="input-affix">
                        <input type="text" name="subdomain" placeholder="yourclinic" required>
                                        <span class="suffix">.healthpaws.co</span>
                                    </div>
                </div>
                            </div>

            <!-- Step 11: Password -->
            <div class="step" id="step11">
                <div class="step-title">🔒 Create your password</div>
                <div class="step-description">Minimum 8 characters. Use a strong, unique password for your admin account.</div>
                
                <div class="form-group">
                    <label>Owner/Admin password</label>
                                    <input type="password" name="ownerPassword" minlength="8" required placeholder="••••••••">
                </div>
                            </div>

            <!-- Step 12: Payment -->
            <div class="step" id="step12">
                <div class="step-title">💳 Complete your setup</div>
                <div class="step-description">Add payment information to activate your account.</div>
                
                <div class="form-group">
                    <label>Payment method</label>
                                    <select name="paymentMethod" required>
                        <option value="">Select payment method</option>
                                        <option value="card" selected>Credit/Debit card</option>
                                        <option value="ach">Bank transfer</option>
                                    </select>
                            </div>
                
                            <div class="mock-pay">
                                <div class="mock-card">
                                    <div>Card number</div>
                                    <div class="mock-input"></div>
                                    <div class="mock-row">
                                        <div>MM/YY</div>
                                        <div>CVC</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" data-mock-pay>Complete payment (mock)</button>
                    <div style="color: #64748b; font-size: 12px; margin-top: 8px;">This is a mock payment for demo purposes. No real charges.</div>
                            </div>
                            <input type="hidden" name="paid" value="false">
                            </div>
                            
            <div class="buttons">
                <button type="button" id="backBtn" class="btn btn-secondary" style="display:none;">Back</button>
                <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
                <button type="submit" id="submitBtn" class="btn btn-primary" style="display:none;">Create Clinic Account</button>
                            </div>
        </form>
        
        <p style="text-align: center; margin-top: 20px; color: #64748b; font-size: 14px;">
            Already have an account? <a href="login.php" style="color: #0ea5e9;">Sign in here</a>
        </p>
                                    </div>
                                    
    <!-- Loading Overlays -->
        <div class="loading-overlay" aria-hidden="true">
            <div class="loader">
                <div class="ring"></div>
                <div class="label">Just a moment…</div>
            </div>
        </div>
        
        <div class="account-creation-overlay" aria-hidden="true">
            <div class="creation-loader">
                <div class="creation-animation">
                    <div class="loading-spinner">
                        <div class="spinner-ring"></div>
                        <div class="spinner-paw">🐾</div>
                    </div>
                </div>
                <div class="typing-container">
                    <div class="typing-text" id="typing-text"></div>
                    <div class="typing-cursor">|</div>
                </div>
                <div class="creation-progress">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div class="progress-text">Setting up your veterinary practice...</div>
                </div>
            </div>
        </div>
    
    <script>
        let currentStep = 1;
        const totalSteps = 12;
        let emailVerified = false;
        
        // Utility functions
        function serialize(form) {
            const data = {};
            new FormData(form).forEach((v, k) => { 
                data[k] = typeof v === 'string' ? v.trim() : v; 
            });
            return data;
        }

        function showToast(msg, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = msg;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#17a2b8'};
                color: white;
                padding: 12px 20px;
                border-radius: 6px;
                z-index: 10000;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }

        // API functions
        async function apiCall(url, data = null, method = 'POST', signal = null) {
            try {
                console.log('🔄 API Call Started:', method, url, data);
                
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                };
                if (signal) { options.signal = signal; }
                
                if (data && method !== 'GET') {
                    options.body = JSON.stringify(data);
                }
                
                console.log('📡 Sending request with options:', options);
                
                const response = await fetch(url, options);
                console.log('📥 Response received:', response.status, response.statusText);
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('❌ Response is not JSON:', contentType);
                    throw new Error(`Server returned ${response.status}: ${response.statusText}. Expected JSON but got ${contentType}`);
                }
                
                const result = await response.json();
                console.log('✅ Parsed JSON result:', result);
                
                if (!response.ok) {
                    throw new Error(result.error || `HTTP ${response.status}: ${response.statusText}`);
                }
                
                return result;
            } catch (error) {
                console.error('❌ API call error:', error);
                
                // Provide more specific error messages
                if (error instanceof TypeError && error.message.includes('fetch')) {
                    throw new Error('Network error: Cannot connect to server. Check if the server is running.');
                } else if (error.name === 'SyntaxError') {
                    throw new Error('Server response error: Invalid JSON received');
                } else {
                    throw error;
                }
            }
        }

        // Check email availability
        async function checkEmailAvailability(email) {
            try {
                const result = await apiCall(`api/check-email.php?email=${encodeURIComponent(email)}`, null, 'GET');
                return result.data;
            } catch (error) {
                console.error('Email check failed:', error);
                return { exists: false, message: 'Email check failed' };
            }
        }

        // Check subdomain availability
        async function checkSubdomainAvailability(subdomain) {
            try {
                const result = await apiCall(`api/check-subdomain.php?subdomain=${encodeURIComponent(subdomain)}`, null, 'GET');
                return result.data.available;
            } catch (error) {
                console.error('Subdomain check failed:', error);
                return false;
            }
        }
        
        function updateProgress() {
            // Update progress dots
            document.querySelectorAll('.progress-dot').forEach((dot, index) => {
                dot.classList.toggle('active', index < currentStep);
            });
            
            // Update step visibility
            document.querySelectorAll('.step').forEach((step, index) => {
                step.classList.toggle('active', index === currentStep - 1);
            });
            
            // Update button visibility
            document.getElementById('backBtn').style.display = currentStep === 1 ? 'none' : 'block';
            document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'block';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';
            
            // Auto-focus on the input field
            setTimeout(() => {
                const currentStepElement = document.getElementById(`step${currentStep}`);
                const input = currentStepElement.querySelector('input, select');
                if (input) {
                    input.focus();
                }
            }, 100);
        }
        
        async function validateStep() {
            const currentStepElement = document.getElementById(`step${currentStep}`);
            const requiredFields = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            for (const field of requiredFields) {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e5e7eb';
                }
            }
            
            // Special validation for email step
            if (currentStep === 2) {
                const emailInput = document.querySelector('input[name="businessEmail"]');
                if (emailInput && emailInput.value) {
                    const emailStatus = emailInput.parentNode.querySelector('.email-status');
                    if (emailStatus && emailStatus.classList.contains('unavailable')) {
                        showToast('Please use a different email address or login to your existing account.', 'error');
                        return false;
                    }
                }
            }
            
            // Special validation for subdomain step
            if (currentStep === 11) {
                const subdomainInput = document.querySelector('input[name="subdomain"]');
                if (subdomainInput && subdomainInput.value) {
                    const subdomain = subdomainInput.value;
                    if (!/^[a-z0-9-]{3,20}$/.test(subdomain)) {
                        showToast('Subdomain must be 3-20 characters, lowercase letters, numbers, and hyphens only.', 'error');
                        return false;
                    }
                    
                    const subdomainStatus = subdomainInput.parentNode.querySelector('.subdomain-status');
                    if (subdomainStatus && subdomainStatus.classList.contains('unavailable')) {
                        showToast('This subdomain is already taken. Please choose a different one.', 'error');
                        return false;
                    }
                }
            }
            
            // Special validation for verification step
            if (currentStep === 3) {
                const verificationBoxes = currentStepElement.querySelectorAll('.verification-box');
                const code = Array.from(verificationBoxes).map(box => box.value).join('');
                if (code.length === 6) {
                    try {
                        const email = document.querySelector('input[name="businessEmail"]').value.trim();
                        const result = await apiCall('api/verify-email.php', {
                            email: email,
                            code: code
                        });
                        
                        if (result.success) {
                            emailVerified = true;
                            verificationBoxes.forEach(box => {
                                box.style.borderColor = '#28a745';
                            });
                            showToast('Email verified successfully!', 'success');
                            return true;
                        } else {
                            emailVerified = false;
                            verificationBoxes.forEach(box => {
                                box.style.borderColor = '#ef4444';
                            });
                            showToast(result.error || 'Invalid verification code', 'error');
                            return false;
                        }
                    } catch (error) {
                        console.error('Verification error:', error);
                        emailVerified = false;
                        verificationBoxes.forEach(box => {
                            box.style.borderColor = '#ef4444';
                        });
                        showToast('Verification failed. Please try again.', 'error');
                        return false;
                    }
                } else {
                    verificationBoxes.forEach(box => box.style.borderColor = '#ef4444');
                    return false;
                }
            }
            
            return isValid;
        }
        
        // Allow Enter key to proceed to next step
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (currentStep < totalSteps) {
                    document.getElementById('nextBtn').click();
                } else {
                    document.getElementById('submitBtn').click();
                }
            }
        });
        
        document.getElementById('nextBtn').addEventListener('click', async () => {
            if (await validateStep() && currentStep < totalSteps) {
                currentStep++;
                updateProgress();
                
                // Auto-send verification code when reaching step 3
                if (currentStep === 3) {
                    sendVerificationCode();
                }
            }
        });
        
        document.getElementById('backBtn').addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateProgress();
            }
        });
        
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!emailVerified) {
                showToast('Please complete email verification before creating your account.', 'error');
                return;
            }
            
            if (await validateStep()) {
                const data = serialize(document.getElementById('register-form'));
                
                try {
                    // Show loading overlay
                    document.querySelector('.account-creation-overlay').style.display = 'flex';
                    startTypingAnimation();
                    
                    const result = await apiCall('api/register.php', {
                        clinic_name: data.clinicName,
                        business_email: data.businessEmail,
                        subdomain: data.subdomain,
                        owner_password: data.ownerPassword,
                        plan: data.plan,
                        address: data.address || '',
                        clinic_phone: data.clinicPhone || '',
                        owner_fname: data.ownerFname || 'Owner',
                        owner_lname: data.ownerLname || 'Admin',
                        owner_phone: data.ownerPhone || ''
                    });
                    
                    if (result.success && result.data && result.data.clinic_id) {
                        showToast('Account created successfully!', 'success');
                        window.registrationData = result.data;
                    } else {
                        throw new Error(result.error || 'Registration failed');
                    }
                } catch (error) {
                    console.error('Registration error:', error);
                    document.querySelector('.account-creation-overlay').style.display = 'none';
                    showToast(error.message || 'Registration failed', 'error');
                }
            }
        });
        
        // Email availability check
        const emailInput = document.querySelector('input[name="businessEmail"]');
        if (emailInput) {
            emailInput.addEventListener('input', () => {
                const email = emailInput.value.trim();
                
                // Clear any existing status
                const existingStatus = emailInput.parentNode.querySelector('.email-status');
                if (existingStatus) {
                    existingStatus.remove();
                }
                
                // Check email availability after a delay
                clearTimeout(emailInput.availabilityTimer);
                emailInput.availabilityTimer = setTimeout(async () => {
                    if (email && emailInput.checkValidity()) {
                        try {
                            const emailCheck = await checkEmailAvailability(email);
                            const statusElement = document.createElement('div');
                            statusElement.className = 'email-status';
                            statusElement.style.cssText = 'font-size: 12px; margin-top: 4px;';
                            
                            if (emailCheck.exists) {
                                statusElement.innerHTML = `
                                    <div style="color: #dc3545;">
                                        ❌ Email already registered
                                        <br>
                                        <a href="login.php" style="color: #007bff; text-decoration: underline;">Login to your account</a>
                                    </div>
                                `;
                                statusElement.className += ' unavailable';
                            } else {
                                statusElement.textContent = '✅ Email is available';
                                statusElement.className += ' available';
                                statusElement.style.color = '#28a745';
                            }
                            
                            emailInput.parentNode.appendChild(statusElement);
                        } catch (error) {
                            console.error('Email validation error:', error);
                        }
                    }
                }, 500);
            });
        }

        // Subdomain sanitization and availability check
        const subInput = document.querySelector('input[name="subdomain"]');
        if (subInput) {
            subInput.addEventListener('input', () => {
                const clean = subInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-{2,}/g, '-').replace(/^-+|-+$/g, '');
                subInput.value = clean;
                
                // Clear any existing status
                const existingStatus = subInput.parentNode.querySelector('.subdomain-status');
                if (existingStatus) {
                    existingStatus.remove();
                }
                
                // Check availability after a delay
                clearTimeout(subInput.availabilityTimer);
                subInput.availabilityTimer = setTimeout(async () => {
                    if (clean.length >= 3) {
                        try {
                            const isAvailable = await checkSubdomainAvailability(clean);
                            const statusElement = document.createElement('div');
                            statusElement.className = `subdomain-status ${isAvailable ? 'available' : 'unavailable'}`;
                            statusElement.style.cssText = `font-size: 12px; margin-top: 4px; color: ${isAvailable ? '#28a745' : '#dc3545'};`;
                            statusElement.textContent = isAvailable ? '✅ Available' : '❌ Not available';
                            
                            subInput.parentNode.appendChild(statusElement);
                        } catch (error) {
                            console.error('Subdomain validation error:', error);
                        }
                    }
                }, 500);
            });
        }
        
        // Verification code handling
        document.querySelectorAll('.verification-box').forEach((box, index) => {
            box.addEventListener('input', (e) => {
                const value = e.target.value;
                
                // Only allow numbers
                if (!/^[0-9]$/.test(value)) {
                    e.target.value = '';
                    return;
                }
                
                if (e.target.value && index < 5) {
                    document.querySelectorAll('.verification-box')[index + 1].focus();
                }
                
                // Auto-advance if all boxes are filled
                const allBoxes = document.querySelectorAll('.verification-box');
                const allFilled = Array.from(allBoxes).every(b => b.value.trim() !== '');
                if (allFilled && currentStep === 3) {
                    setTimeout(async () => {
                        if (await validateStep()) {
                            document.getElementById('nextBtn').click();
                        }
                    }, 500);
                }
            });
            
            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && index > 0) {
                    document.querySelectorAll('.verification-box')[index - 1].focus();
                }
            });
        });
        
        // Mock payment handling
        document.querySelector('[data-mock-pay]')?.addEventListener('click', () => {
            document.querySelector('input[name="paid"]').value = 'true';
            showToast('Mock payment completed!', 'success');
            setTimeout(() => {
                document.getElementById('nextBtn').click();
            }, 1000);
        });

        // Verification code sending function
        async function sendVerificationCode() {
            const emailInput = document.querySelector('input[name="businessEmail"]');
            const clinicNameInput = document.querySelector('input[name="clinicName"]');
            const verificationMessage = document.querySelector('#verification-message');
            const resendBtn = document.querySelector('#resend-code-btn');
            
            if (!emailInput || !emailInput.value.trim()) {
                verificationMessage.textContent = 'Error: No email address found';
                verificationMessage.style.color = '#dc3545';
                return;
            }
            
            const email = emailInput.value.trim();
            const clinicName = clinicNameInput?.value.trim() || 'Your Clinic';
            
            // Set a timeout for the email sending
            const timeoutId = setTimeout(() => {
                verificationMessage.textContent = '⚠️ Email sending is taking longer than expected. Using demo mode instead.';
                verificationMessage.style.color = '#f59e0b';
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend Code';
                showToast('Email service timeout - Using demo code: 123456', 'info');
                
                // Focus first verification box
                const firstBox = document.querySelector('.verification-box');
                if (firstBox) {
                    firstBox.focus();
                }
            }, 25000); // 25 second UI fallback timeout
            
            try {
                verificationMessage.textContent = 'Sending verification code...';
                verificationMessage.style.color = '#64748b';
                resendBtn.disabled = true;
                resendBtn.textContent = 'Sending...';
                
                console.log('Attempting to send verification code to:', email);
                
                // Add timeout to the API call
                const controller = new AbortController();
                const timeoutApiId = setTimeout(() => controller.abort(), 20000); // 20 second API timeout
                
                const result = await apiCall('api/send-verification.php', {
                    email: email,
                    clinic_name: clinicName
                }, 'POST', controller.signal);
                
                clearTimeout(timeoutApiId);
                clearTimeout(timeoutId);
                
                if (result.success) {
                    verificationMessage.textContent = `✅ Verification code sent to ${email}! Check your inbox.`;
                    verificationMessage.style.color = '#28a745';
                    showToast('Verification code sent!', 'success');
                    
                    // Focus first verification box
                    const firstBox = document.querySelector('.verification-box');
                    if (firstBox) {
                        firstBox.focus();
                    }
                } else {
                    throw new Error(result.error || 'Failed to send verification code');
                }
            } catch (error) {
                clearTimeout(timeoutId);
                console.error('Send verification error:', error);
                
                let errorMessage = error.message;
                let fallbackMessage = '';
                
                // Provide specific error messages based on error type
                if (error.name === 'AbortError') {
                    errorMessage = 'Email service timeout';
                    fallbackMessage = ' - Using demo code: 123456';
                } else if (error.message.includes('fetch')) {
                    errorMessage = 'Network error - Cannot reach email service';
                    fallbackMessage = ' - Using demo code: 123456';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Connection failed - Email API unreachable';
                    fallbackMessage = ' - Using demo code: 123456';
                }
                
                verificationMessage.innerHTML = `
                    <div style="color: #dc3545; margin-bottom: 8px;">❌ ${errorMessage}</div>
                    <div style="color: #f59e0b; font-size: 12px;">🧪 <strong>Demo Mode:</strong> Use verification code: <strong>123456</strong></div>
                `;
                
                showToast(errorMessage + fallbackMessage, 'error');
                
                // Focus first verification box for demo mode
                const firstBox = document.querySelector('.verification-box');
                if (firstBox) {
                    firstBox.focus();
                }
            } finally {
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend Code';
            }
        }
        
        // Resend code button handler
        document.querySelector('#resend-code-btn')?.addEventListener('click', () => {
            sendVerificationCode();
        });

        // Typing animation and completion functions
        window.startTypingAnimation = function() {
            const typingText = document.querySelector('#typing-text');
            const progressFill = document.querySelector('.progress-fill');
            
            const clinicName = document.querySelector('input[name="clinicName"]').value || 'Vet Shop';
            const subdomain = document.querySelector('input[name="subdomain"]').value || 'vetname';
            
            const phrases = [
                `Preparing your ${clinicName}! 🏥`,
                `Creating ${subdomain}.healthpaws.co 🌐`,
                "Setting up patient database 📊",
                "Configuring appointment system 📅",
                "Initializing medical records 📋",
                "Setting up billing system 💳",
                "Finalizing your clinic setup ✨"
            ];
            
            let currentPhraseIndex = 0;
            let currentCharIndex = 0;
            
            function typeNextChar() {
                const currentPhrase = phrases[currentPhraseIndex];
                
                typingText.textContent = currentPhrase.substring(0, currentCharIndex + 1);
                currentCharIndex++;
                
                const totalProgress = ((currentPhraseIndex + (currentCharIndex / currentPhrase.length)) / phrases.length) * 100;
                progressFill.style.width = totalProgress + '%';
                
                if (currentCharIndex === currentPhrase.length) {
                    currentPhraseIndex++;
                    currentCharIndex = 0;
                    
                    if (currentPhraseIndex >= phrases.length) {
                        progressFill.style.width = '100%';
                        setTimeout(showCompletionScreen, 1000);
                        return;
                    }
                    
                    setTimeout(typeNextChar, 1000);
                    return;
                }
                
                setTimeout(typeNextChar, 100);
            }
            
            typeNextChar();
        };

        window.showCompletionScreen = function() {
            const creationLoader = document.querySelector('.creation-loader');
            
            creationLoader.innerHTML = `
                <div class="completion-message" style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 16px;">🎉</div>
                    <h2 style="color: #0f172a; margin-bottom: 8px;">Your clinic is now ready!</h2>
                    <p style="color: #64748b; margin-bottom: 24px;">Welcome to HealthPaws! Your veterinary practice has been successfully set up.</p>
                    <button class="btn btn-primary" onclick="goToDashboard()" style="padding: 12px 24px; background: #0ea5e9; color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">Go to Dashboard</button>
    </div>
            `;
        };

        window.goToDashboard = function() {
            const clinicName = document.querySelector('input[name="clinicName"]').value || 'Demo Veterinary Clinic';
            const subdomain = document.querySelector('input[name="subdomain"]').value || 'demo';
            
            window.location.href = `dashboard.php?subdomain=${encodeURIComponent(subdomain)}&clinic=${encodeURIComponent(clinicName)}`;
        };
        
        // Initialize
        updateProgress();
    </script>
</body>
</html>
