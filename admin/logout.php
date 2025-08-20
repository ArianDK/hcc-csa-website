<?php
/**
 * CSA Website - Admin Logout
 */

session_start();

// Check if user is actually logged in
if (!isset($_SESSION['admin_id'])) {
    // Already logged out, redirect to login
    header('Location: index.php');
    exit;
}

// Destroy admin session
$adminEmail = $_SESSION['admin_email'] ?? 'unknown';

// Log the logout for security
error_log("Admin logout: $adminEmail from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

// Clear all session data
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login with success message
header('Location: index.php?logged_out=1');
exit;
?>
