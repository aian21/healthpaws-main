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
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body class="auth-shell">
    <div class="auth-card">
        <div class="auth-grid">
            <section class="auth-form">
                <h1>Create Your Clinic Account</h1>
                <p>Join thousands of veterinary practices using HealthPaws. Start your free 14-day trial.</p>
                <div class="stepper" aria-hidden="false" aria-label="Progress">
                    <div class="dot is-active"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
                <div class="progress" aria-hidden="true"><div class="bar" style="width: 0%"></div></div>
                <form id="register-form" novalidate>
                    <div class="steps">
                        <section class="step is-active" data-step="0" aria-label="Clinic details">
                            <div class="field-card is-active" data-card="0-0">
                                <div class="field-title">Please enter your clinic's name</div>
                                <div class="field-help">Use your public-facing name. You can change this later.</div>
                                <label>Clinic name
                                    <input type="text" name="clinicName" placeholder="Ex: Happy Tails Veterinary Clinic">
                                </label>
                            </div>
                            <div class="field-card" data-card="0-1">
                                <div class="field-title">Please input your login email</div>
                                <div class="field-help">This becomes the Owner/Admin account. We'll send verification here.</div>
                                <label>Business email
                                    <input type="email" name="businessEmail" placeholder="you@yourclinic.com">
                                </label>
                                <div class="field-help">By continuing, you agree to receive critical account emails.</div>
                            </div>
                            <div class="field-card" data-card="0-2">
                                <div class="field-title">Clinic contact information</div>
                                <div class="field-help">Provide your clinic's contact details.</div>
                                <label>Clinic address
                                    <input type="text" name="address" placeholder="123 Main Street, City, State">
                                </label>
                                <label>Clinic phone
                                    <input type="tel" name="clinicPhone" placeholder="(555) 123-4567">
                                </label>
                            </div>
                            <div class="field-card" data-card="0-3">
                                <div class="field-title">Owner information</div>
                                <div class="field-help">Tell us about the clinic owner/administrator.</div>
                                <div class="row">
                                    <label>First name
                                        <input type="text" name="ownerFname" placeholder="John">
                                    </label>
                                    <label>Last name
                                        <input type="text" name="ownerLname" placeholder="Doe">
                                    </label>
                                </div>
                                <label>Phone number
                                    <input type="tel" name="ownerPhone" placeholder="(555) 987-6543">
                                </label>
                            </div>
                            <div class="field-card" data-card="0-4">
                                <div class="field-title">📧 Verify your email</div>
                                <div class="field-help">Enter the verification code sent to your email to continue.</div>
                                
                                <div class="verification-input-group" style="margin-top: 16px;">
                                    <div class="verification-boxes" style="display: flex; gap: 8px; justify-content: center; margin-bottom: 16px;">
                                        <input type="text" class="verification-box" maxlength="1" pattern="[0-9]" data-index="0" placeholder="1">
                                        <input type="text" class="verification-box" maxlength="1" pattern="[0-9]" data-index="1" placeholder="2">
                                        <input type="text" class="verification-box" maxlength="1" pattern="[0-9]" data-index="2" placeholder="3">
                                        <input type="text" class="verification-box" maxlength="1" pattern="[0-9]" data-index="3" placeholder="4">
                                        <input type="text" class="verification-box" maxlength="1" pattern="[0-9]" data-index="4" placeholder="5">
                                        <input type="text" class="verification-box" maxlength="1" pattern="[0-9]" data-index="5" placeholder="6">
                                    </div>
                                    
                                    <div class="verification-actions" style="display: flex; justify-content: center;">
                                        <button type="button" class="btn btn-ghost" id="send-code-btn">Resend Code</button>
                                    </div>
                                </div>
                                
                                <div class="verification-status" style="margin-top: 8px; font-size: 12px;">
                                    <span id="verification-message" style="color: #666;">Enter your email above and click "Send Code" to receive a verification code.</span>
                                </div>
                                
                                <!-- Demo mode indicator -->
                                <div class="mock-verification-info" style="margin-top: 12px; padding: 8px; background: #e3f2fd; border-radius: 6px; border-left: 3px solid #2196f3;">
                                    <div style="font-weight: 600; font-size: 12px; margin-bottom: 4px;">🧪 Demo Mode</div>
                                    <div style="font-size: 11px; color: #1976d2;">
                                        <strong>Mock verification code:</strong> 123456<br>
                                        Use this code to test the verification process.
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="step" data-step="1" aria-label="Plan & terms">
                            <div class="field-card is-active" data-card="1-0">
                                <div class="field-title">Choose your plan</div>
                                <div class="field-help">You can change plans anytime. Trial applies automatically.</div>
                                <label>Plan
                                    <select name="plan" required>
                                        <option value="starter">Starter</option>
                                        <option value="pro" selected>Pro</option>
                                        <option value="enterprise">Enterprise</option>
                                    </select>
                                </label>
                            </div>
                            <div class="field-card" data-card="1-1">
                                <div class="field-title">Pick your subdomain</div>
                                <div class="field-help">Letters, numbers, and dashes only. Used for your login URL.</div>
                                <label>Preferred subdomain
                                    <div class="input-affix">
                                        <input type="text" name="subdomain" placeholder="yourclinic">
                                        <span class="suffix">.healthpaws.co</span>
                                    </div>
                                </label>
                            </div>
                            <div class="field-card" data-card="1-2">
                                <div class="field-title">Create your Owner/Admin password</div>
                                <div class="field-help">Minimum 8 characters. Use a strong, unique password.</div>
                                <label>Password
                                    <input type="password" name="ownerPassword" minlength="8" required placeholder="••••••••">
                                </label>
                            </div>
                            <div class="plan-card" aria-live="polite"></div>
                        </section>
                        <section class="step" data-step="2" aria-label="Payment">
                            <div class="row">
                                <label>Payment method
                                    <select name="paymentMethod" required>
                                        <option value="card" selected>Credit/Debit card</option>
                                        <option value="ach">Bank transfer</option>
                                    </select>
                                </label>
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
                                <div class="auth-meta">This is a mock payment for demo purposes. No real charges.</div>
                            </div>
                            <input type="hidden" name="paid" value="false">
                        </section>
                        <section class="step" data-step="3" aria-label="Add staff">
                            <p>Add staff members now. Your business email will be the Owner/Admin account by default.</p>
                            
                            <!-- Primary Veterinarian Information -->
                            <div class="field-card is-active" data-card="3-0">
                                <div class="field-title">Primary Veterinarian Information</div>
                                <div class="field-help">Add your primary veterinarian (optional - can be added later).</div>
                                <div class="row">
                                    <label>First name
                                        <input type="text" name="vetFname" placeholder="Dr. Jane">
                                    </label>
                                    <label>Last name
                                        <input type="text" name="vetLname" placeholder="Smith">
                                    </label>
                                </div>
                                <label>Specialization
                                    <input type="text" name="specialization" placeholder="General Practice, Surgery, etc.">
                                </label>
                                <label>License number
                                    <input type="text" name="licenseNumber" placeholder="VET123456">
                                </label>
                            </div>
                            
                            <div id="staff-list"></div>
                            <div class="auth-actions" style="justify-content:flex-start">
                                <button type="button" class="btn btn-ghost" data-add-staff>Add more staff</button>
                            </div>
                        </section>
                        <section class="step" data-step="4" aria-label="Confirmation">
                            <div class="field-card is-active" data-card="4-0">
                                <div class="field-title">Terms and Conditions</div>
                                <div class="field-help">Please read and accept our terms before creating your account.</div>
                                
                                <!-- Terms and Conditions -->
                                <div class="terms-section" style="margin-top: 20px;">
                                    
                                    <div class="terms-container" style="height: 200px; border: 1px solid rgba(42,140,130,.16); border-radius: 8px; padding: 16px; overflow-y: auto; background: #f8f9fa; margin-top: 12px;">
                                        <div class="terms-content">
                                            <h4>HealthPaws Terms of Service</h4>
                                            <p><strong>Last updated:</strong> December 2024</p>
                                            
                                            <h5>1. Acceptance of Terms</h5>
                                            <p>By accessing and using HealthPaws, you accept and agree to be bound by the terms and provision of this agreement.</p>
                                            
                                            <h5>2. Description of Service</h5>
                                            <p>HealthPaws provides veterinary practice mana I agree to the Terms and Privacygement software including appointment scheduling, patient records, billing, and communication tools.</p>
                                            
                                            <h5>3. User Accounts</h5>
                                            <p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>
                                            
                                            <h5>4. Privacy Policy</h5>
                                            <p>Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the Service, to understand our practices.</p>
                                            
                                            <h5>5. Data Security</h5>
                                            <p>We implement appropriate security measures to protect your data. However, no method of transmission over the internet is 100% secure.</p>
                                            
                                            <h5>6. Service Availability</h5>
                                            <p>We strive to maintain high service availability but cannot guarantee uninterrupted access. We may perform maintenance that could temporarily affect service.</p>
                                            
                                            <h5>7. Payment Terms</h5>
                                            <p>Subscription fees are billed in advance on a monthly or annual basis. You may cancel your subscription at any time.</p>
                                            
                                            <h5>8. Termination</h5>
                                            <p>Either party may terminate this agreement at any time. Upon termination, your access to the Service will cease immediately.</p>
                                            
                                            <h5>9. Limitation of Liability</h5>
                                            <p>HealthPaws shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of the Service.</p>
                                            
                                            <h5>10. Governing Law</h5>
                                            <p>This agreement shall be governed by and construed in accordance with the laws of the jurisdiction in which HealthPaws operates.</p>
                                            
                                            <h5>11. Changes to Terms</h5>
                                            <p>We reserve the right to modify these terms at any time. We will notify users of any material changes via email or through the Service.</p>
                                            
                                            <h5>12. Contact Information</h5>
                                            <p>If you have any questions about these Terms of Service, please contact us at legal@healthpaws.com</p>
                                            
                                            <p><strong>By creating an account, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.</strong></p>
                                        </div>
                                    </div>
                                    
                                    <div class="terms-status" style="margin-top: 12px; font-size: 12px; color: #666;">
                                        <span id="terms-status">Please scroll to the bottom of the terms to see the Create Account button</span>
                                    </div>
                                </div>
                                
                                <div class="auth-actions" style="justify-content:center; margin-top:20px">
                                    <button type="button" class="btn btn-ghost" data-add-more-staff style="display: none;">Invite More Staff</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="wizard-actions">
                        <button type="button" class="btn btn-ghost" data-step-back>Back</button>
                        <div style="display:flex; gap:10px">
                            <button type="button" class="btn btn-ghost" data-step-next>Next</button>
                            <button type="submit" class="btn btn-primary" data-step-submit style="display:none">Create account</button>
                        </div>
                    </div>
                </form>
                <p class="auth-meta">Already have an account? <a href="login.php">Sign in here</a></p>
            </section>
        </div>
        <div class="loading-overlay" aria-hidden="true">
            <div class="loader">
                <div class="ring"></div>
                <div class="label">Just a moment…</div>
            </div>
        </div>
        
        <!-- Account Creation Loading Screen -->
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
    </div>
    <script src="scripts/auth.js"></script>
</body>
</html>
