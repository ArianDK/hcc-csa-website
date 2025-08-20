<?php
/**
 * CSA Website Configuration
 * 
 * IMPORTANT: Update all REPLACE_ME values before deployment
 */

return [
    'db' => [
        'driver' => 'mysql',        // or 'sqlite'
        'host'   => 'localhost',
        'name'   => 'csa',
        'user'   => 'root',         // XAMPP default MySQL user
        'pass'   => '',             // XAMPP default (empty password)
        'charset'=> 'utf8mb4',
        // For SQLite, use 'path' instead of host/user/pass:
        // 'path' => __DIR__ . '/../data/csa.sqlite'
    ],
    
    'smtp' => [
        'host' => 'smtp.gmail.com',           // or your SMTP provider
        'port' => 587,
        'user' => 'no-reply@example.com',    // Change this!
        'pass' => 'REPLACE_ME',              // Change this!
        'from_email' => 'no-reply@example.com',
        'from_name'  => 'CSA at HCC'
    ],
    
    'security' => [
        'captcha_provider' => 'recaptcha',    // 'recaptcha' or 'hcaptcha'
        'captcha_site_key' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',   // Test key
        'captcha_secret'   => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',   // Test key
        'token_ttl_days'   => 7,
        'rate_limit_window' => 3600,          // 1 hour in seconds
        'rate_limit_max_attempts' => 50      // Higher limit for testing
    ],
    
    'app' => [
        'name' => 'Computer Science Association',
        'short_name' => 'CSA',
        'domain' => 'localhost',              // Local development
        'base_url' => 'http://localhost/hcc-csa-website', // Local development URL
        'admin_email' => 'admin@test.local',  // Local testing email
        'timezone' => 'America/Chicago'
    ],
    
    'features' => [
        'email_verification' => false,       // Disabled for easier testing
        'admin_notifications' => false,      // Disabled for easier testing
        'rsvp_system' => true,
        'analytics' => true
    ]
];
?>
