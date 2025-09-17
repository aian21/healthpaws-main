<?php
// Email configuration for HealthPaws
// Suppress error display to ensure clean output
error_reporting(0);
ini_set('display_errors', 0);

class EmailConfig {
    // SMTP Configuration for Namecheap Shared Hosting
    public static $smtp_host = 'mail.healthpaws.app'; // Use mail.yourdomain.com for Namecheap
    public static $smtp_port = 587; // 587 for TLS, 465 for SSL (Namecheap supports both)
    public static $smtp_username = 'no-reply@healthpaws.app'; // Your full email address
    public static $smtp_password = 'S.!L#s,MElh$'; // Your email password (not app password)
    public static $smtp_encryption = 'tls'; // 'ssl' is more reliable for Namecheap
    
    // Email settings
    public static $from_email = 'no-reply@healthpaws.app';
    public static $from_name = 'HealthPaws';
    public static $reply_to = 'no-reply@healthpaws.app';
    
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
    
    public static function getNamecheapConfig($domain) {
        return [
            'host' => "mail.{$domain}", // e.g., mail.yourdomain.com
            'port' => 465, // or 587
            'encryption' => 'ssl', // or 'tls' for port 587
            'auth' => true,
            'notes' => 'For Namecheap shared hosting with webmail/Roundcube'
        ];
    }
}
?>
