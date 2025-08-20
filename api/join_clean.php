<?php
// Clean API version - only JSON output
header('Content-Type: application/json');

// Prevent any HTML output
ob_start();

try {
    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Check AJAX
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    if (!$isAjax) {
        throw new Exception('AJAX request required');
    }
    
    // Start session
    session_start();
    
    // Include config
    require_once __DIR__ . '/../config/database.php';
    
    // Get and validate input
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $yearLevel = trim($_POST['year_level'] ?? '');
    $major = trim($_POST['major'] ?? '');
    $campus = trim($_POST['campus'] ?? '');
    $consentComms = isset($_POST['consent_comms']) ? 1 : 0;
    
    // Basic validation
    if (empty($firstName) || strlen($firstName) < 2) {
        throw new Exception('First name is required (min 2 characters)');
    }
    
    if (empty($lastName) || strlen($lastName) < 2) {
        throw new Exception('Last name is required (min 2 characters)');
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Valid email address is required');
    }
    
    // Check if email exists
    $stmt = Database::prepare("SELECT id, status FROM members WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing && $existing['status'] === 'VERIFIED') {
        throw new Exception('This email is already registered');
    }
    
    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));
    
    if ($existing && $existing['status'] === 'PENDING') {
        // Update existing pending registration
        $stmt = Database::prepare("
            UPDATE members SET 
                first_name = ?, last_name = ?, year_level = ?, major = ?, campus = ?, 
                consent_comms = ?, verification_token = ?, created_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([
            $firstName, $lastName, $yearLevel, $major, $campus, 
            $consentComms, $verificationToken, $existing['id']
        ]);
        $memberId = $existing['id'];
    } else {
        // Create new member
        $stmt = Database::prepare("
            INSERT INTO members (
                first_name, last_name, email, year_level, major, campus, 
                consent_comms, verification_token, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', NOW())
        ");
        $stmt->execute([
            $firstName, $lastName, $email, $yearLevel, $major, $campus, 
            $consentComms, $verificationToken
        ]);
        $memberId = Database::getConnection()->lastInsertId();
    }
    
    // Clear any output buffer
    ob_clean();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => "Welcome to CSA, $firstName! Your membership is now pending admin approval.",
        'member_id' => $memberId
    ]);
    
} catch (Exception $e) {
    // Clear any output buffer
    ob_clean();
    
    // Return error
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

ob_end_flush();
?>
