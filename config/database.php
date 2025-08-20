<?php
/**
 * Database Connection Factory
 * Supports both MySQL/MariaDB and SQLite
 */

class Database {
    private static $pdo = null;
    private static $config = null;
    
    public static function getConnection() {
        if (self::$pdo === null) {
            self::connect();
        }
        return self::$pdo;
    }
    
    private static function connect() {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/config.php';
        }
        
        $config = self::$config['db'];
        
        try {
            if ($config['driver'] === 'sqlite') {
                $dsn = 'sqlite:' . $config['path'];
                self::$pdo = new PDO($dsn);
                // Enable foreign keys for SQLite
                self::$pdo->exec('PRAGMA foreign_keys = ON');
            } else {
                // MySQL/MariaDB
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    $config['host'],
                    $config['name'],
                    $config['charset']
                );
                
                self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']} COLLATE {$config['charset']}_unicode_ci"
                ]);
            }
            
            // Common PDO options
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed. Please try again later.');
        }
    }
    
    public static function beginTransaction() {
        return self::getConnection()->beginTransaction();
    }
    
    public static function commit() {
        return self::getConnection()->commit();
    }
    
    public static function rollback() {
        return self::getConnection()->rollback();
    }
    
    public static function prepare($sql) {
        return self::getConnection()->prepare($sql);
    }
    
    public static function lastInsertId() {
        return self::getConnection()->lastInsertId();
    }
    
    public static function query($sql) {
        return self::getConnection()->query($sql);
    }
}

/**
 * Security helper functions
 */
class Security {
    
    public static function generateToken($length = 64) {
        return bin2hex(random_bytes($length / 2));
    }
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = self::generateToken(32);
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public static function checkRateLimit($ip, $email, $endpoint) {
        $config = require __DIR__ . '/config.php';
        $window = $config['security']['rate_limit_window'];
        $maxAttempts = $config['security']['rate_limit_max_attempts'];
        
        // Ensure IP is not null
        $ip = $ip ?: '127.0.0.1';
        $email = $email ?: '';
        
        $pdo = Database::getConnection();
        
        // Ensure rate_limits table exists (for fresh installs)
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS rate_limits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                email VARCHAR(255),
                endpoint VARCHAR(100) NOT NULL,
                attempts INT DEFAULT 1,
                last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_rate_limits_ip_endpoint (ip_address, endpoint),
                INDEX idx_rate_limits_email_endpoint (email, endpoint),
                INDEX idx_rate_limits_last_attempt (last_attempt)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (Exception $e) {
            error_log('Failed to create rate_limits table: ' . $e->getMessage());
        }
        
        // Clean old entries
        $stmt = $pdo->prepare("DELETE FROM rate_limits WHERE last_attempt < DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$window]);
        
        // Check current attempts
        $stmt = $pdo->prepare("
            SELECT attempts FROM rate_limits 
            WHERE (ip_address = ? OR email = ?) AND endpoint = ?
            ORDER BY attempts DESC LIMIT 1
        ");
        $stmt->execute([$ip, $email, $endpoint]);
        $result = $stmt->fetch();
        
        if ($result && $result['attempts'] >= $maxAttempts) {
            return false; // Rate limited
        }
        
        // Record this attempt
        $stmt = $pdo->prepare("
            INSERT INTO rate_limits (ip_address, email, endpoint, attempts, last_attempt) 
            VALUES (?, ?, ?, 1, NOW())
            ON DUPLICATE KEY UPDATE 
            attempts = attempts + 1, 
            last_attempt = NOW()
        ");
        $stmt->execute([$ip, $email, $endpoint]);
        
        return true; // Allow request
    }
}
?>
