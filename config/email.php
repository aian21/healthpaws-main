<?php
// Email configuration for HealthPaws
// Suppress error display to ensure clean output
error_reporting(0);
ini_set('display_errors', 0);

class EmailConfig {
    // SMTP Configuration
    public static $smtp_host = 'panel.freehosting.com'; // Updated to match certificate
    public static $smtp_port = 587; // 587 for TLS, 465 for SSL
    public static $smtp_username = 'applications@itsaian.tech'; // Your email
    public static $smtp_password = 'Aian2104'; // Your app password
    public static $smtp_encryption = 'tls'; // 'tls' or 'ssl'
    
    // Email settings
    public static $from_email = 'applications@itsaian.tech';
    public static $from_name = 'HealthPaws';
    public static $reply_to = 'applications@itsaian.tech';
    
    // Verification settings
    public static $verification_code_length = 6;
    public static $verification_code_expiry = 15; // minutes
}

// Alternative configuration for different email providers
class EmailProviders {
    public static function getGmailConfig() {
        return [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'auth' => true
        ];
    }
    
    public static function getOutlookConfig() {
        return [
            'host' => 'smtp-mail.outlook.com',
            'port' => 587,
            'encryption' => 'tls',
            'auth' => true
        ];
    }
    
    public static function getYahooConfig() {
        return [
            'host' => 'smtp.mail.yahoo.com',
            'port' => 587,
            'encryption' => 'tls',
            'auth' => true
        ];
    }
}
?>
