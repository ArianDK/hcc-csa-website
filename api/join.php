<?php
/**
 * CSA Website - Join API Endpoint
 * Handles new member registration with security and validation
 */

// Output buffering to catch any unexpected output
ob_start();

// Turn off error display for clean JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Check if this is an AJAX request
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');

// Set content type for AJAX requests
if ($isAjax) {
    // Clear any previous output and set JSON header
    ob_clean();
    header('Content-Type: application/json');
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    } else {
        echo 'Method not allowed';
    }
    exit;
}

session_start();

// Wrap everything in try-catch for clean error handling
try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
    require_once __DIR__ . '/../partials/captcha.php';
    // CSRF Protection
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!Security::verifyCSRFToken($csrfToken)) {
        throw new Exception('Invalid security token. Please refresh the page and try again.');
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
    if (!Security::checkRateLimit($userIP, $_POST['email'] ?? '', 'join')) {
        throw new Exception('Too many registration attempts. Please wait before trying again.');
    }
    
    // Input Validation
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $yearLevel = trim($_POST['year_level'] ?? '');
    $major = trim($_POST['major'] ?? '');
    $campus = trim($_POST['campus'] ?? '');
    $consentComms = isset($_POST['consent_comms']) ? 1 : 0;
    $acceptedCode = isset($_POST['accepted_code']) ? 1 : 0;
    $consentPrivacy = isset($_POST['consent_privacy']) ? 1 : 0;
    
    // Required field validation
    if (empty($firstName) || strlen($firstName) < 2 || strlen($firstName) > 80) {
        throw new Exception('First name must be between 2 and 80 characters.');
    }
    
    if (empty($lastName) || strlen($lastName) < 2 || strlen($lastName) > 80) {
        throw new Exception('Last name must be between 2 and 80 characters.');
    }
    
    if (empty($email) || !Security::validateEmail($email)) {
        throw new Exception('Please enter a valid email address.');
    }
    
    if (!$acceptedCode) {
        throw new Exception('You must agree to follow the Code of Conduct.');
    }
    
    if (!$consentPrivacy) {
        throw new Exception('You must agree to the Privacy Policy.');
    }
    
    // Sanitize inputs
    $firstName = Security::sanitizeInput($firstName);
    $lastName = Security::sanitizeInput($lastName);
    $email = strtolower($email);
    $yearLevel = Security::sanitizeInput($yearLevel);
    $major = Security::sanitizeInput($major);
    $campus = Security::sanitizeInput($campus);
    
    // Check if email already exists
    $stmt = Database::prepare("SELECT id, status FROM members WHERE email = ?");
    $stmt->execute([$email]);
    $existingMember = $stmt->fetch();
    
    if ($existingMember) {
        if ($existingMember['status'] === 'VERIFIED') {
            throw new Exception('This email is already registered. If you forgot your login, please contact us.');
        } elseif ($existingMember['status'] === 'PENDING') {
            // Update existing pending registration
            $verificationToken = Security::generateToken(64);
            
            // Check if year_level column exists for update
            $stmt = Database::prepare("SHOW COLUMNS FROM members LIKE 'year_level'");
            $stmt->execute();
            $hasYearLevel = $stmt->rowCount() > 0;
            
            if ($hasYearLevel) {
                $stmt = Database::prepare("
                    UPDATE members SET 
                        first_name = ?, 
                        last_name = ?, 
                        year_level = ?, 
                        major = ?, 
                        campus = ?, 
                        consent_comms = ?, 
                        verification_token = ?, 
                        created_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([
                    $firstName, $lastName, $yearLevel, $major, $campus, 
                    $consentComms, $verificationToken, 
                    $existingMember['id']
                ]);
            } else {
                $stmt = Database::prepare("
                    UPDATE members SET 
                        first_name = ?, 
                        last_name = ?, 
                        major = ?, 
                        campus = ?, 
                        consent_comms = ?, 
                        verification_token = ?, 
                        created_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([
                    $firstName, $lastName, $major, $campus, 
                    $consentComms, $verificationToken, 
                    $existingMember['id']
                ]);
            }
            
            $memberId = $existingMember['id'];
        } else {
            throw new Exception('This email is blocked from registration. Please contact us for assistance.');
        }
    } else {
        // Create new member
        $verificationToken = Security::generateToken(64);
        
        // Check if year_level column exists
        $stmt = Database::prepare("SHOW COLUMNS FROM members LIKE 'year_level'");
        $stmt->execute();
        $hasYearLevel = $stmt->rowCount() > 0;
        
        if ($hasYearLevel) {
            $stmt = Database::prepare("
                INSERT INTO members (
                    first_name, last_name, email, year_level, major, campus, 
                    consent_comms, verification_token, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')
            ");
            $stmt->execute([
                $firstName, $lastName, $email, $yearLevel, $major, $campus, 
                $consentComms, $verificationToken
            ]);
        } else {
            $stmt = Database::prepare("
                INSERT INTO members (
                    first_name, last_name, email, major, campus, 
                    consent_comms, verification_token, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING')
            ");
            $stmt->execute([
                $firstName, $lastName, $email, $major, $campus, 
                $consentComms, $verificationToken
            ]);
        }
        
        $memberId = Database::lastInsertId();
    }
    
    // Send verification email (simplified for now)
    $emailSent = true; // Skip email sending for local testing
    
    // TODO: Implement email verification when SMTP is configured
    // $emailSent = EmailService::sendVerificationEmail($email, $firstName, $verificationToken);
    
    if (!$emailSent) {
        // Log error but don't fail registration (email is disabled for local testing)
        error_log("Failed to send verification email to: $email");
        
        // Don't exit here - continue to success message
    }
    
    // Track successful registration
    if ($isAjax) {
        // AJAX request - return JSON
        echo json_encode([
            'success' => true,
            'message' => "Welcome to CSA, $firstName! Your membership is now pending admin approval.",
            'redirect' => false,
            'member_id' => $memberId
        ]);
        ob_end_flush();
    } else {
        // Regular form submission - redirect
        $_SESSION['join_success'] = "Thanks for joining CSA, $firstName! Your membership is now pending. An admin will verify your account soon.";
        ob_end_clean();
        header('Location: ../join.php');
        exit;
    }
    
} catch (Exception $e) {
    error_log('Join API error: ' . $e->getMessage());
    
    if ($isAjax) {
        // AJAX request - return JSON error
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        ob_end_flush();
    } else {
        // Regular form submission - redirect with error
        $_SESSION['join_error'] = $e->getMessage();
        ob_end_clean();
        header('Location: ../join.php');
        exit;
    }
} catch (Error $e) {
    // Catch fatal errors and return JSON
    error_log('Join API fatal error: ' . $e->getMessage());
    
    if ($isAjax) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'A server error occurred. Please try again.'
        ]);
        ob_end_flush();
    } else {
        $_SESSION['join_error'] = 'A server error occurred. Please try again.';
        ob_end_clean();
        header('Location: ../join.php');
        exit;
    }
} catch (Throwable $e) {
    // Catch any other throwable errors
    error_log('Join API throwable error: ' . $e->getMessage());
    
    if ($isAjax) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred. Please try again.'
        ]);
        ob_end_flush();
    } else {
        $_SESSION['join_error'] = 'An unexpected error occurred. Please try again.';
        ob_end_clean();
        header('Location: ../join.php');
        exit;
    }
}
?>
