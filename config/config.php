<?php
/**
 * CSA Website Configuration
 * 
 * IMPORTANT: Update all REPLACE_ME values before deployment
 */

// Auto-detect environment and set appropriate base URL
if (!function_exists('getBaseUrl')) {
    function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        
        // Remove the filename from the path to get the directory
        $path = dirname($scriptName);
        
        // If we're in the root directory, use empty path
        if ($path === '/') {
            $path = '';
        }
        
        return $protocol . '://' . $host . $path;
    }
}

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
        'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'base_url' => getBaseUrl(),           // Auto-detected base URL
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
