<?php
/**
 * CSA Website - Statistics API Endpoint
 * Provides member statistics for admin dashboard
 */

header('Content-Type: application/json');
header('X-Robots-Tag: noindex');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $stats = [];
    
    // Member counts by status
    $stmt = Database::prepare("SELECT status, COUNT(*) as count FROM members GROUP BY status");
    $stmt->execute();
    $statusCounts = [];
    while ($row = $stmt->fetch()) {
        $statusCounts[$row['status']] = (int)$row['count'];
    }
    $stats['member_counts'] = $statusCounts;
    
    // Registration trend (last 30 days)
    $stmt = Database::prepare("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM members 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $registrationTrend = [];
    while ($row = $stmt->fetch()) {
        $registrationTrend[] = [
            'date' => $row['date'],
            'count' => (int)$row['count']
        ];
    }
    $stats['registration_trend'] = $registrationTrend;
    
    // Major distribution
    $stmt = Database::prepare("
        SELECT major, COUNT(*) as count 
        FROM members 
        WHERE major IS NOT NULL AND major != '' AND status = 'VERIFIED'
        GROUP BY major 
        ORDER BY count DESC
    ");
    $stmt->execute();
    $majorDistribution = [];
    while ($row = $stmt->fetch()) {
        $majorDistribution[] = [
            'major' => $row['major'],
            'count' => (int)$row['count']
        ];
    }
    $stats['major_distribution'] = $majorDistribution;
    
    // Campus distribution
    $stmt = Database::prepare("
        SELECT campus, COUNT(*) as count 
        FROM members 
        WHERE campus IS NOT NULL AND campus != '' AND status = 'VERIFIED'
        GROUP BY campus 
        ORDER BY count DESC
    ");
    $stmt->execute();
    $campusDistribution = [];
    while ($row = $stmt->fetch()) {
        $campusDistribution[] = [
            'campus' => $row['campus'],
            'count' => (int)$row['count']
        ];
    }
    $stats['campus_distribution'] = $campusDistribution;
    
    // Email consent rate
    $stmt = Database::prepare("
        SELECT 
            SUM(CASE WHEN consent_comms = 1 THEN 1 ELSE 0 END) as consented,
            COUNT(*) as total
        FROM members 
        WHERE status = 'VERIFIED'
    ");
    $stmt->execute();
    $consentData = $stmt->fetch();
    $stats['email_consent'] = [
        'consented' => (int)($consentData['consented'] ?? 0),
        'total' => (int)($consentData['total'] ?? 0),
        'rate' => $consentData['total'] > 0 ? round(($consentData['consented'] / $consentData['total']) * 100, 1) : 0
    ];
    
    // Recent activity
    $stmt = Database::prepare("
        SELECT COUNT(*) as count 
        FROM members 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $stmt->execute();
    $stats['recent_registrations'] = [
        'last_7_days' => (int)$stmt->fetch()['count']
    ];
    
    $stmt = Database::prepare("
        SELECT COUNT(*) as count 
        FROM members 
        WHERE verified_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $stmt->execute();
    $stats['recent_verifications'] = [
        'last_7_days' => (int)$stmt->fetch()['count']
    ];
    
    // HCC email statistics
    $stmt = Database::prepare("
        SELECT 
            SUM(CASE WHEN email LIKE '%@hccs.edu' THEN 1 ELSE 0 END) as hcc_emails,
            COUNT(*) as total
        FROM members 
        WHERE status = 'VERIFIED'
    ");
    $stmt->execute();
    $emailData = $stmt->fetch();
    $stats['hcc_email_usage'] = [
        'hcc_emails' => (int)($emailData['hcc_emails'] ?? 0),
        'total' => (int)($emailData['total'] ?? 0),
        'rate' => $emailData['total'] > 0 ? round(($emailData['hcc_emails'] / $emailData['total']) * 100, 1) : 0
    ];
    
    // Add metadata
    $stats['generated_at'] = date('c');
    $stats['generated_by'] = $_SESSION['admin_email'];
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
    
} catch (Exception $e) {
    error_log('Stats API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to generate statistics'
    ]);
}
?>
