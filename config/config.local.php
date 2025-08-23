<?php
/**
 * Local Development Configuration
 * Copy this to config.php for local development
 */

return [
    'db' => [
        'driver' => 'mysql',
        'host'   => 'localhost',
        'name'   => 'csa',
        'user'   => 'root',
        'pass'   => '',
        'charset'=> 'utf8mb4',
    ],
    
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'user' => 'no-reply@example.com',
        'pass' => 'REPLACE_ME',
        'from_email' => 'no-reply@example.com',
        'from_name'  => 'CSA at HCC'
    ],
    
    'security' => [
        'captcha_provider' => 'recaptcha',
        'captcha_site_key' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
        'captcha_secret'   => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
        'token_ttl_days'   => 7,
        'rate_limit_window' => 3600,
        'rate_limit_max_attempts' => 50
    ],
    
    'app' => [
        'name' => 'Computer Science Association',
        'short_name' => 'CSA',
        'domain' => 'localhost',
        'base_url' => 'http://localhost/hcc-csa-website',
        'admin_email' => 'admin@test.local',
        'timezone' => 'America/Chicago'
    ],
    
    'features' => [
        'email_verification' => false,
        'admin_notifications' => false,
        'rsvp_system' => true,
        'analytics' => true
    ]
];
?>
