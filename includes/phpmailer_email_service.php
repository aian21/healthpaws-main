<?php
// PHPMailer-based Email Service for HealthPaws
// More reliable than custom SMTP implementation

require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PHPMailerEmailService {
    
    /**
     * Send verification email with code
     */
    public function sendVerificationEmail($to_email, $verification_code, $clinic_name = '') {
        $subject = 'Verify Your HealthPaws Account';
        
        $html_body = $this->getVerificationEmailTemplate($verification_code, $clinic_name);
        $text_body = $this->getVerificationEmailText($verification_code, $clinic_name);
        
        return $this->sendEmail($to_email, $subject, $html_body, $text_body);
    }
    
    /**
     * Send welcome email after successful registration
     */
    public function sendWelcomeEmail($to_email, $clinic_name, $subdomain) {
        $subject = 'Welcome to HealthPaws!';
        
        $html_body = $this->getWelcomeEmailTemplate($clinic_name, $subdomain);
        $text_body = $this->getWelcomeEmailText($clinic_name, $subdomain);
        
        return $this->sendEmail($to_email, $subject, $html_body, $text_body);
    }
    
    /**
     * Send email using PHPMailer
     */
    private function sendEmail($to_email, $subject, $html_body, $text_body = '') {
        try {
            // Validate email
            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address');
            }
            
            $mail = new PHPMailer(true);
            
            // Enable verbose debug output (remove in production)
            $mail->SMTPDebug = 0; // Set to 2 for detailed debug info
            
            // Use SMTP
            $mail->isSMTP();
            $mail->Host = EmailConfig::$smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = EmailConfig::$smtp_username;
            $mail->Password = EmailConfig::$smtp_password;
            $mail->SMTPSecure = EmailConfig::$smtp_encryption;
            $mail->Port = EmailConfig::$smtp_port;
            
            // Set timeouts
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = false;
            
            // Fix certificate verification issues
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Recipients
            $mail->setFrom(EmailConfig::$from_email, EmailConfig::$from_name);
            $mail->addAddress($to_email);
            $mail->addReplyTo(EmailConfig::$reply_to, EmailConfig::$from_name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_body;
            $mail->AltBody = $text_body;
            
            // Set charset
            $mail->CharSet = 'UTF-8';
            
            // Send the email
            $result = $mail->send();
            
            error_log("PHPMailer: Email sent successfully to: $to_email");
            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];
            
        } catch (Exception $e) {
            error_log("PHPMailer error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate verification code
     */
    public function generateVerificationCode() {
        return str_pad(rand(0, 999999), EmailConfig::$verification_code_length, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get verification email HTML template
     */
    private function getVerificationEmailTemplate($code, $clinic_name) {
        $clinic_text = $clinic_name ? " for $clinic_name" : '';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Verify Your Email - HealthPaws</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2a8c82; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
                .code { background: #2a8c82; color: white; font-size: 24px; font-weight: bold; padding: 15px 30px; border-radius: 6px; text-align: center; margin: 20px 0; letter-spacing: 3px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🐾 HealthPaws</h1>
                <p>Veterinary Practice Management</p>
            </div>
            <div class='content'>
                <h2>Verify Your Email Address</h2>
                <p>Thank you for registering$clinic_text! To complete your account setup, please verify your email address using the code below:</p>
                
                <div class='code'>$code</div>
                
                <p><strong>Important:</strong></p>
                <ul>
                    <li>This code will expire in " . EmailConfig::$verification_code_expiry . " minutes</li>
                    <li>If you didn't request this verification, please ignore this email</li>
                    <li>For security, never share this code with anyone</li>
                </ul>
                
                <p>Once verified, you'll have full access to your HealthPaws dashboard and can start managing your veterinary practice.</p>
                
                <p>Best regards,<br>The HealthPaws Team</p>
            </div>
            <div class='footer'>
                <p>This email was sent from HealthPaws. If you didn't request this verification, please ignore this email.</p>
                <p>&copy; 2024 HealthPaws. All rights reserved.</p>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get verification email text template
     */
    private function getVerificationEmailText($code, $clinic_name) {
        $clinic_text = $clinic_name ? " for $clinic_name" : '';
        
        return "
HealthPaws - Email Verification

Thank you for registering$clinic_text!

To complete your account setup, please verify your email address using this code:

$code

This code will expire in " . EmailConfig::$verification_code_expiry . " minutes.

If you didn't request this verification, please ignore this email.

Once verified, you'll have full access to your HealthPaws dashboard.

Best regards,
The HealthPaws Team

---
This email was sent from HealthPaws.
© 2024 HealthPaws. All rights reserved.
        ";
    }
    
    /**
     * Get welcome email HTML template
     */
    private function getWelcomeEmailTemplate($clinic_name, $subdomain) {
        $dashboard_url = "https://$subdomain.healthpaws.co/dashboard.php";
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Welcome to HealthPaws!</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2a8c82; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
                .button { display: inline-block; background: #2a8c82; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🎉 Welcome to HealthPaws!</h1>
                <p>Your veterinary practice is ready</p>
            </div>
            <div class='content'>
                <h2>Congratulations, $clinic_name!</h2>
                <p>Your HealthPaws account has been successfully created and verified. You can now start managing your veterinary practice with our comprehensive suite of tools.</p>
                
                <p><strong>Your clinic dashboard:</strong> <a href='$dashboard_url'>$dashboard_url</a></p>
                
                <a href='$dashboard_url' class='button'>Go to Dashboard</a>
                
                <h3>What's Next?</h3>
                <ul>
                    <li>Set up your clinic profile and services</li>
                    <li>Add your veterinary staff members</li>
                    <li>Import or add your patient records</li>
                    <li>Schedule your first appointments</li>
                    <li>Configure your billing settings</li>
                </ul>
                
                <p>Welcome to the HealthPaws family!</p>
                
                <p>Best regards,<br>The HealthPaws Team</p>
            </div>
            <div class='footer'>
                <p>This email was sent from HealthPaws.</p>
                <p>&copy; 2024 HealthPaws. All rights reserved.</p>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Get welcome email text template
     */
    private function getWelcomeEmailText($clinic_name, $subdomain) {
        $dashboard_url = "https://$subdomain.healthpaws.co/dashboard.php";
        
        return "
Welcome to HealthPaws!

Congratulations, $clinic_name!

Your HealthPaws account has been successfully created and verified. You can now start managing your veterinary practice.

Your clinic dashboard: $dashboard_url

What's Next?
- Set up your clinic profile and services
- Add your veterinary staff members
- Import or add your patient records
- Schedule your first appointments
- Configure your billing settings

Welcome to the HealthPaws family!

Best regards,
The HealthPaws Team

---
This email was sent from HealthPaws.
© 2024 HealthPaws. All rights reserved.
        ";
    }
}
?>
