<?php
// Debug version of PHPMailer Email Service with detailed logging
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PHPMailerDebugService {
    
    /**
     * Send verification email with detailed debugging
     */
    public function sendVerificationEmail($to_email, $verification_code, $clinic_name = '') {
        $subject = 'Verify Your HealthPaws Account';
        
        $html_body = $this->getVerificationEmailTemplate($verification_code, $clinic_name);
        $text_body = $this->getVerificationEmailText($verification_code, $clinic_name);
        
        return $this->sendEmailWithDebug($to_email, $subject, $html_body, $text_body);
    }
    
    /**
     * Send email with detailed debugging enabled
     */
    private function sendEmailWithDebug($to_email, $subject, $html_body, $text_body = '') {
        $debug_log = [];
        
        try {
            // Validate email
            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address');
            }
            $debug_log[] = "✅ Email validation passed: $to_email";
            
            $mail = new PHPMailer(true);
            
            // Enable verbose debug output
            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION; // Very detailed debug
            $mail->Debugoutput = function($str, $level) use (&$debug_log) {
                $debug_log[] = "SMTP Debug: $str";
            };
            
            // Use SMTP
            $mail->isSMTP();
            $mail->Host = EmailConfig::$smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = EmailConfig::$smtp_username;
            $mail->Password = EmailConfig::$smtp_password;
            $mail->SMTPSecure = EmailConfig::$smtp_encryption;
            $mail->Port = EmailConfig::$smtp_port;
            
            $debug_log[] = "📧 SMTP Configuration:";
            $debug_log[] = "  Host: " . EmailConfig::$smtp_host;
            $debug_log[] = "  Port: " . EmailConfig::$smtp_port;
            $debug_log[] = "  Username: " . EmailConfig::$smtp_username;
            $debug_log[] = "  Encryption: " . EmailConfig::$smtp_encryption;
            
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
            $debug_log[] = "🔒 SSL verification disabled for compatibility";
            
            // Recipients
            $mail->setFrom(EmailConfig::$from_email, EmailConfig::$from_name);
            $mail->addAddress($to_email);
            $mail->addReplyTo(EmailConfig::$reply_to, EmailConfig::$from_name);
            
            $debug_log[] = "📮 Recipients configured:";
            $debug_log[] = "  From: " . EmailConfig::$from_email;
            $debug_log[] = "  To: $to_email";
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_body;
            $mail->AltBody = $text_body;
            $mail->CharSet = 'UTF-8';
            
            $debug_log[] = "📝 Email content configured";
            $debug_log[] = "  Subject: $subject";
            $debug_log[] = "  HTML Body Length: " . strlen($html_body) . " chars";
            
            // Test SMTP connection first
            $debug_log[] = "🔌 Testing SMTP connection...";
            if (!$mail->smtpConnect()) {
                throw new Exception('SMTP connection failed');
            }
            $debug_log[] = "✅ SMTP connection successful";
            
            // Send the email
            $debug_log[] = "📤 Sending email...";
            $result = $mail->send();
            $debug_log[] = "✅ Email sent successfully!";
            
            // Close connection
            $mail->smtpClose();
            
            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'debug_log' => $debug_log
            ];
            
        } catch (Exception $e) {
            $debug_log[] = "❌ Error: " . $e->getMessage();
            
            // Additional error analysis
            $error_msg = $e->getMessage();
            if (strpos($error_msg, 'Connection refused') !== false) {
                $debug_log[] = "🔍 Analysis: Server is refusing connections on port " . EmailConfig::$smtp_port;
                $debug_log[] = "💡 Suggestion: Check if XAMPP/firewall is blocking outbound SMTP";
            } elseif (strpos($error_msg, 'Authentication failed') !== false) {
                $debug_log[] = "🔍 Analysis: SMTP authentication failed";
                $debug_log[] = "💡 Suggestion: Verify username/password at webmail.privateemail.com";
            } elseif (strpos($error_msg, 'timed out') !== false) {
                $debug_log[] = "🔍 Analysis: Connection timeout";
                $debug_log[] = "💡 Suggestion: Try different port (587 instead of 465)";
            } elseif (strpos($error_msg, 'Could not instantiate mail function') !== false) {
                $debug_log[] = "🔍 Analysis: PHP mail function issue";
                $debug_log[] = "💡 Suggestion: Check XAMPP SMTP configuration";
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'debug_log' => $debug_log
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
     * Test different SMTP configurations
     */
    public function testMultipleConfigurations($to_email) {
        $configurations = [
            'current' => [
                'host' => EmailConfig::$smtp_host,
                'port' => EmailConfig::$smtp_port,
                'encryption' => EmailConfig::$smtp_encryption,
                'username' => EmailConfig::$smtp_username,
                'password' => EmailConfig::$smtp_password
            ],
            'namecheap_tls' => [
                'host' => 'mail.privateemail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => EmailConfig::$smtp_username,
                'password' => EmailConfig::$smtp_password
            ],
            'namecheap_domain' => [
                'host' => 'mail.healthpaws.app', // If you have domain-specific SMTP
                'port' => 465,
                'encryption' => 'ssl',
                'username' => EmailConfig::$smtp_username,
                'password' => EmailConfig::$smtp_password
            ]
        ];
        
        $results = [];
        
        foreach ($configurations as $name => $config) {
            $results[$name] = $this->testConfiguration($config, $to_email);
        }
        
        return $results;
    }
    
    private function testConfiguration($config, $to_email) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['encryption'];
            $mail->Port = $config['port'];
            $mail->Timeout = 10;
            
            // Test connection
            if ($mail->smtpConnect()) {
                $mail->smtpClose();
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'config' => $config
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Connection failed',
                    'config' => $config
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'config' => $config
            ];
        }
    }
    
    /**
     * Get verification email HTML template (simplified for debugging)
     */
    private function getVerificationEmailTemplate($code, $clinic_name) {
        $clinic_text = $clinic_name ? " for $clinic_name" : '';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Verify Your Email - HealthPaws</title>
        </head>
        <body>
            <h1>🐾 HealthPaws</h1>
            <h2>Verify Your Email Address</h2>
            <p>Thank you for registering$clinic_text!</p>
            <p><strong>Verification Code: $code</strong></p>
            <p>This is a test email from the debug service.</p>
        </body>
        </html>";
    }
    
    /**
     * Get verification email text template (simplified for debugging)
     */
    private function getVerificationEmailText($code, $clinic_name) {
        $clinic_text = $clinic_name ? " for $clinic_name" : '';
        
        return "
HealthPaws - Email Verification

Thank you for registering$clinic_text!

Verification Code: $code

This is a test email from the debug service.
        ";
    }
}
?>

