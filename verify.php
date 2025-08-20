<?php
/**
 * CSA Website - Email Verification Page
 */

session_start();

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Email Verification - Computer Science Association at HCC';
$pageDescription = 'Complete your CSA membership by verifying your email address.';

$success = false;
$error = '';
$memberName = '';
$isExpired = false;
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = 'No verification token provided.';
} else {
    try {
        Database::beginTransaction();
        
        // Find member with this token
        $stmt = Database::prepare("
            SELECT id, first_name, last_name, email, status, created_at 
            FROM members 
            WHERE verification_token = ? AND status = 'PENDING'
        ");
        $stmt->execute([$token]);
        $member = $stmt->fetch();
        
        if (!$member) {
            $error = 'Invalid or already used verification link.';
        } else {
            // Check if token is expired (7 days)
            $createdTime = strtotime($member['created_at']);
            $expiryTime = $createdTime + (7 * 24 * 60 * 60); // 7 days
            
            if (time() > $expiryTime) {
                $isExpired = true;
                $error = 'This verification link has expired. Please request a new one.';
                $memberName = $member['first_name'];
            } else {
                // Verify the member
                $stmt = Database::prepare("
                    UPDATE members 
                    SET status = 'VERIFIED', 
                        verified_at = NOW(), 
                        verification_token = NULL 
                    WHERE id = ?
                ");
                $stmt->execute([$member['id']]);
                
                $success = true;
                $memberName = $member['first_name'];
                
                // Send welcome notification to admins (optional)
                $config = require __DIR__ . '/config/config.php';
                if ($config['features']['admin_notifications']) {
                    require_once __DIR__ . '/vendor/phpmailer/PHPMailer.php';
                    
                    try {
                        EmailService::sendAdminNotification([
                            'first_name' => $member['first_name'],
                            'last_name' => $member['last_name'],
                            'email' => $member['email'],
                            'major' => '',
                            'campus' => ''
                        ]);
                    } catch (Exception $e) {
                        error_log('Failed to send admin notification: ' . $e->getMessage());
                        // Don't fail verification if notification fails
                    }
                }
            }
        }
        
        Database::commit();
        
    } catch (Exception $e) {
        Database::rollback();
        error_log('Verification error: ' . $e->getMessage());
        $error = 'An error occurred during verification. Please try again or contact us for help.';
    }
}

include __DIR__ . '/partials/meta.php';
include __DIR__ . '/partials/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container text-center" style="max-width: 600px;">
            
            <?php if ($success): ?>
                <!-- Success State -->
                <div style="font-size: 5rem; margin-bottom: 2rem;">🎉</div>
                <h1>Welcome to CSA, <?php echo htmlspecialchars($memberName); ?>!</h1>
                <div class="alert alert-success">
                    <strong>Email verified successfully!</strong> Your CSA membership is now active.
                </div>
                
                <div class="card mt-6">
                    <div class="card-header">
                        <h2 class="card-title">What's Next?</h2>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-1 gap-4">
                            <div class="text-left">
                                <h3>📅 Attend Your First Event</h3>
                                <p>Check out our upcoming workshops, hackathons, and social events. Your first event is always the most important!</p>
                                <a href="/events.php" class="btn btn-primary">View Events</a>
                            </div>
                            
                            <div class="text-left">
                                <h3>💬 Join Our Discord</h3>
                                <p>Connect with fellow members, ask questions, share resources, and stay updated on last-minute announcements.</p>
                                <a href="https://discord.gg/hcc-csa" class="btn btn-secondary" target="_blank">Join Discord</a>
                            </div>
                            
                            <div class="text-left">
                                <h3>🤝 Get Involved</h3>
                                <p>Learn about committees, volunteer opportunities, and ways to contribute to our growing community.</p>
                                <a href="/involved.php" class="btn btn-outline">Get Involved</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h3>Questions?</h3>
                    <p>
                        We're here to help! Reach out to us at 
                        <a href="mailto:president@hccs.edu">president@hccs.edu</a>
                        or find us on Discord.
                    </p>
                </div>
                
            <?php elseif ($isExpired): ?>
                <!-- Expired Token State -->
                <div style="font-size: 5rem; margin-bottom: 2rem;">⏰</div>
                <h1>Verification Link Expired</h1>
                <div class="alert alert-warning">
                    <strong>Sorry <?php echo htmlspecialchars($memberName); ?>!</strong> This verification link has expired.
                </div>
                
                <div class="card mt-6">
                    <div class="card-header">
                        <h2 class="card-title">Get a New Verification Link</h2>
                    </div>
                    <div class="card-body">
                        <p>Verification links expire after 7 days for security. Don't worry - you can get a new one!</p>
                        
                        <div class="mt-4">
                            <h4>Option 1: Re-register</h4>
                            <p>The quickest way is to <a href="/join.php">join again</a> with the same email address. This will generate a new verification link.</p>
                            <a href="/join.php" class="btn btn-primary">Re-register for CSA</a>
                        </div>
                        
                        <div class="mt-4">
                            <h4>Option 2: Contact Us</h4>
                            <p>Email us and we'll manually verify your account or send a new link.</p>
                            <a href="mailto:president@hccs.edu?subject=Verification Help - <?php echo urlencode($memberName); ?>" class="btn btn-secondary">Email for Help</a>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Error State -->
                <div style="font-size: 5rem; margin-bottom: 2rem;">❌</div>
                <h1>Verification Failed</h1>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                
                <div class="card mt-6">
                    <div class="card-header">
                        <h2 class="card-title">What Can You Do?</h2>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-1 gap-4">
                            <div class="text-left">
                                <h3>🔍 Check Your Email</h3>
                                <p>Make sure you clicked the most recent verification link. Check for newer emails from CSA.</p>
                            </div>
                            
                            <div class="text-left">
                                <h3>🔄 Try Again</h3>
                                <p>If you think this is an error, you can register again or contact us for help.</p>
                                <div class="flex gap-2 flex-wrap">
                                    <a href="/join.php" class="btn btn-primary">Register Again</a>
                                    <a href="mailto:president@hccs.edu?subject=Verification Problem" class="btn btn-outline">Contact Support</a>
                                </div>
                            </div>
                            
                            <div class="text-left">
                                <h3>💬 Get Help</h3>
                                <p>Our team is friendly and responsive. We'll get this sorted out quickly!</p>
                                <a href="https://discord.gg/hcc-csa" class="btn btn-secondary" target="_blank">Ask on Discord</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Additional Resources -->
            <div class="mt-8">
                <h3>While You're Here...</h3>
                <div class="flex justify-center gap-4 flex-wrap">
                    <a href="/" class="btn btn-outline">Back to Home</a>
                    <a href="/about.php" class="btn btn-outline">About CSA</a>
                    <a href="/events.php" class="btn btn-outline">Upcoming Events</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php if ($success): ?>
<script>
// Track successful verification
if (window.CSA && window.CSA.Analytics) {
    window.CSA.Analytics.trackCustomEvent('membership', 'email_verified', {
        member_name: '<?php echo htmlspecialchars($memberName); ?>'
    });
}

// Show a subtle celebration animation
document.addEventListener('DOMContentLoaded', function() {
    const celebration = document.querySelector('div[style*="font-size: 5rem"]');
    if (celebration) {
        celebration.style.animation = 'bounce 2s ease-in-out';
        
        // Add the CSS animation if it doesn't exist
        if (!document.querySelector('#celebration-style')) {
            const style = document.createElement('style');
            style.id = 'celebration-style';
            style.textContent = `
                @keyframes bounce {
                    0%, 20%, 60%, 100% { transform: translateY(0); }
                    40% { transform: translateY(-30px); }
                    80% { transform: translateY(-15px); }
                }
            `;
            document.head.appendChild(style);
        }
    }
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
