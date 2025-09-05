<?php
// HealthPaws Email Service
// Handles sending emails via SMTP

require_once __DIR__ . '/../config/email.php';

class EmailService {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $smtp_encryption;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        $this->smtp_host = EmailConfig::$smtp_host;
        $this->smtp_port = EmailConfig::$smtp_port;
        $this->smtp_username = EmailConfig::$smtp_username;
        $this->smtp_password = EmailConfig::$smtp_password;
        $this->smtp_encryption = EmailConfig::$smtp_encryption;
        $this->from_email = EmailConfig::$from_email;
        $this->from_name = EmailConfig::$from_name;
    }
    
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
     * Send email using PHP's mail() function with SMTP headers
     */
    private function sendEmail($to_email, $subject, $html_body, $text_body = '') {
        try {
            // Validate email
            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address');
            }
            
            // Create boundary for multipart email
            $boundary = md5(uniqid(time()));
            
            // Email headers
            $headers = [
                'From: ' . $this->from_name . ' <' . $this->from_email . '>',
                'Reply-To: ' . EmailConfig::$reply_to,
                'MIME-Version: 1.0',
                'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
                'X-Mailer: HealthPaws Email Service'
            ];
            
            // Email body
            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $text_body . "\r\n\r\n";
            
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $html_body . "\r\n\r\n";
            
            $body .= "--$boundary--\r\n";
            
            // Send email
            $result = mail($to_email, $subject, $body, implode("\r\n", $headers));
            
            if ($result) {
                error_log("Email sent successfully to: $to_email");
                return [
                    'success' => true,
                    'message' => 'Email sent successfully'
                ];
            } else {
                throw new Exception('Failed to send email');
            }
            
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
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
                .button { display: inline-block; background: #2a8c82; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
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
                
                <p>If you have any questions, please contact our support team.</p>
                
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
                
                <p>Need help getting started? Check out our <a href='#'>Getting Started Guide</a> or contact our support team.</p>
                
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

Need help getting started? Check out our Getting Started Guide or contact our support team.

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
