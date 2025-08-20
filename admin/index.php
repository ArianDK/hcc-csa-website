<?php
/**
 * CSA Website - Admin Login Page
 */

session_start();

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../partials/captcha.php';

$pageTitle = 'Admin Login - CSA at HCC';
$pageDescription = 'Secure login for CSA administrators';

// Generate CSRF token
$csrfToken = Security::generateCSRFToken();

$error = '';
$success = '';

// Check for logout success message
if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
    $success = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF Protection
        $submittedToken = $_POST['csrf_token'] ?? '';
        if (!Security::verifyCSRFToken($submittedToken)) {
            throw new Exception('Invalid security token. Please refresh and try again.');
        }
        
        // Get user IP with fallback for local development
        $userIP = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '127.0.0.1';
        
        // CAPTCHA Verification - Skip in local development
        $captchaToken = $_POST['captcha_token'] ?? '';
        $isLocalhost = in_array($userIP, ['127.0.0.1', '::1', 'localhost']);
        
        if (!$isLocalhost && !CaptchaVerifier::verify($captchaToken, $userIP)) {
            throw new Exception('CAPTCHA verification failed. Please try again.');
        }
        
        // Rate Limiting with proper IP handling
        if (!Security::checkRateLimit($userIP, $_POST['email'] ?? '', 'admin_login')) {
            throw new Exception('Too many login attempts. Please wait before trying again.');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            throw new Exception('Please enter both email and password.');
        }
        
        if (!Security::validateEmail($email)) {
            throw new Exception('Please enter a valid email address.');
        }
        
        // Find admin
        $stmt = Database::prepare("SELECT id, email, pass_hash, role FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if (!$admin || !Security::verifyPassword($password, $admin['pass_hash'])) {
            throw new Exception('Invalid email or password.');
        }
        
        // Successful login
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['admin_login_time'] = time();
        
        header('Location: dashboard.php');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log('Admin login error: ' . $e->getMessage());
    }
}

include __DIR__ . '/../partials/meta.php';
?>

<style>
/* Admin-specific styles */
.admin-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    padding: var(--spacing-4);
}

.admin-login-card {
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-xl);
    padding: var(--spacing-8);
    width: 100%;
    max-width: 400px;
}

.admin-header {
    text-align: center;
    margin-bottom: var(--spacing-6);
}

.admin-logo {
    font-size: 3rem;
    margin-bottom: var(--spacing-2);
}

.admin-title {
    margin: 0 0 var(--spacing-2) 0;
    color: var(--text-primary);
}

.admin-subtitle {
    margin: 0;
    color: var(--text-muted);
    font-size: var(--font-size-sm);
}

.back-to-site {
    position: fixed;
    top: var(--spacing-4);
    left: var(--spacing-4);
    z-index: 100;
}
</style>

<body>
    <a href="../" class="back-to-site btn btn-outline">← Back to Site</a>
    
    <div class="admin-container">
        <div class="admin-login-card">
            <div class="admin-header">
                <div class="admin-logo">🔐</div>
                <h1 class="admin-title">CSA Admin</h1>
                <p class="admin-subtitle">Sign in to access the dashboard</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" data-validate data-requires-captcha data-captcha-action="admin_login">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           data-validate="required|email"
                           required 
                           autocomplete="email"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           data-validate="required"
                           required 
                           autocomplete="current-password">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        Sign In
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-6">
                <p class="text-muted">
                    <small>
                        For security, all admin logins are logged and monitored.<br>
                        Forgot your password? Contact the system administrator.
                    </small>
                </p>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../partials/captcha.php'; ?>
    
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/form-validate.js"></script>
    
    <script>
    // Enhanced security for admin login
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[data-validate]');
        
        if (form) {
            // Add extra protection against automated attacks
            let startTime = Date.now();
            
            form.addEventListener('submit', function(e) {
                // Prevent too-fast submissions (likely bots)
                if (Date.now() - startTime < 2000) {
                    e.preventDefault();
                    alert('Please wait a moment before submitting.');
                    return false;
                }
            });
            
            // Clear password on page visibility change (security)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    const passwordField = document.getElementById('password');
                    if (passwordField && passwordField.value) {
                        setTimeout(() => {
                            passwordField.value = '';
                        }, 60000); // Clear after 1 minute of being hidden
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
