<?php
/**
 * CSA Website - Admin Dashboard
 */

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Admin Dashboard - CSA at HCC';
$pageDescription = 'CSA admin dashboard for member management and statistics';

// Get dashboard statistics
try {
    // Member counts
    $stmt = Database::prepare("SELECT status, COUNT(*) as count FROM members GROUP BY status");
    $stmt->execute();
    $memberStats = [];
    while ($row = $stmt->fetch()) {
        $memberStats[$row['status']] = $row['count'];
    }
    
    // Recent registrations (last 30 days)
    $stmt = Database::prepare("
        SELECT COUNT(*) as count 
        FROM members 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $recentRegistrations = $stmt->fetch()['count'] ?? 0;
    
    // Popular majors
    $stmt = Database::prepare("
        SELECT major, COUNT(*) as count 
        FROM members 
        WHERE major IS NOT NULL AND major != '' AND status = 'VERIFIED'
        GROUP BY major 
        ORDER BY count DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $popularMajors = $stmt->fetchAll();
    
    // Campus distribution
    $stmt = Database::prepare("
        SELECT campus, COUNT(*) as count 
        FROM members 
        WHERE campus IS NOT NULL AND campus != '' AND status = 'VERIFIED'
        GROUP BY campus 
        ORDER BY count DESC
    ");
    $stmt->execute();
    $campusStats = $stmt->fetchAll();
    
    // Recent members
    $stmt = Database::prepare("
        SELECT id, first_name, last_name, email, major, campus, status, created_at 
        FROM members 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recentMembers = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log('Dashboard stats error: ' . $e->getMessage());
    $memberStats = [];
    $recentRegistrations = 0;
    $popularMajors = [];
    $campusStats = [];
    $recentMembers = [];
}

$totalMembers = array_sum($memberStats);
$verifiedMembers = $memberStats['VERIFIED'] ?? 0;
$pendingMembers = $memberStats['PENDING'] ?? 0;
$blockedMembers = $memberStats['BLOCKED'] ?? 0;

include __DIR__ . '/../partials/meta.php';
?>

<style>
/* Admin dashboard styles */
.admin-layout {
    display: flex;
    min-height: 100vh;
    background: var(--bg-secondary);
}

.admin-sidebar {
    width: 250px;
    background: var(--bg-primary);
    border-right: 1px solid var(--border-color);
    padding: var(--spacing-6);
    position: fixed;
    height: 100vh;
    overflow-y: auto;
}

.admin-main {
    flex: 1;
    margin-left: 250px;
    padding: var(--spacing-6);
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-6);
    background: var(--bg-primary);
    padding: var(--spacing-4);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
}

.admin-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.admin-nav li {
    margin-bottom: var(--spacing-2);
}

.admin-nav a {
    display: block;
    padding: var(--spacing-3);
    border-radius: var(--border-radius-md);
    color: var(--text-secondary);
    text-decoration: none;
    transition: all var(--transition-fast);
}

.admin-nav a:hover,
.admin-nav a.active {
    background: var(--secondary-color);
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-4);
    margin-bottom: var(--spacing-6);
}

.stat-card {
    background: var(--bg-primary);
    padding: var(--spacing-4);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
    text-align: center;
}

.stat-number {
    font-size: var(--font-size-3xl);
    font-weight: var(--font-weight-bold);
    color: var(--primary-color);
    margin-bottom: var(--spacing-2);
}

.stat-label {
    color: var(--text-muted);
    font-size: var(--font-size-sm);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--spacing-6);
}

@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
        transition: transform var(--transition-normal);
    }
    
    .admin-main {
        margin-left: 0;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.member-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.member-table th,
.member-table td {
    padding: var(--spacing-3);
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.member-table th {
    background: var(--bg-tertiary);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.status-badge {
    padding: var(--spacing-1) var(--spacing-2);
    border-radius: var(--border-radius-sm);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-medium);
    text-transform: uppercase;
}

.status-verified {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-blocked {
    background: #f8d7da;
    color: #721c24;
}

.chart-container {
    height: 200px;
    position: relative;
}

.back-to-site {
    background: var(--secondary-color) !important;
    border-color: var(--secondary-color) !important;
    color: white !important;
    font-weight: var(--font-weight-semibold);
    transition: all var(--transition-fast);
}

.back-to-site:hover {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="text-center mb-6">
                <h2>CSA Admin</h2>
                <p class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
            </div>
            
            <nav>
                <ul class="admin-nav">
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="members.php">Members</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="export.php">Export Data</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
            
            <div class="mt-8">
                <a href="../" class="btn btn-primary btn-sm btn-block back-to-site">Back to Site</a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Dashboard</h1>
                    <p class="text-muted">Overview of CSA membership and activity</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="refreshData()">Refresh</button>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalMembers; ?></div>
                    <div class="stat-label">Total Members</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $verifiedMembers; ?></div>
                    <div class="stat-label">Verified Members</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $pendingMembers; ?></div>
                    <div class="stat-label">Pending Verification</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $recentRegistrations; ?></div>
                    <div class="stat-label">New This Month</div>
                </div>
            </div>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Members -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Members</h3>
                        <a href="members.php" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <?php if (!empty($recentMembers)): ?>
                            <table class="member-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Major</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMembers as $member): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                                            <td><?php echo htmlspecialchars($member['major'] ?: 'Not specified'); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($member['status']); ?>">
                                                    <?php echo $member['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($member['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center p-6">
                                <p class="text-muted">No members found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Statistics Sidebar -->
                <div>
                    <!-- Popular Majors -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">Popular Majors</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($popularMajors)): ?>
                                <?php foreach ($popularMajors as $major): ?>
                                    <div class="flex justify-between items-center mb-2">
                                        <span><?php echo htmlspecialchars($major['major']); ?></span>
                                        <span class="text-muted"><?php echo $major['count']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Campus Distribution -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Campus Distribution</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($campusStats)): ?>
                                <?php foreach ($campusStats as $campus): ?>
                                    <div class="flex justify-between items-center mb-2">
                                        <span><?php echo htmlspecialchars($campus['campus']); ?></span>
                                        <span class="text-muted"><?php echo $campus['count']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-4">
                        <a href="members.php?status=pending" class="btn btn-outline">
                            📧 Review Pending (<?php echo $pendingMembers; ?>)
                        </a>
                        <a href="export.php" class="btn btn-outline">
                            📊 Export Members
                        </a>
                        <a href="events.php" class="btn btn-outline">
                            📅 Manage Events
                        </a>
                        <a href="bulk-email.php" class="btn btn-outline">
                            📬 Send Announcement
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
    
    <script>
    function refreshData() {
        window.location.reload();
    }
    
    // Auto-refresh every 5 minutes
    setInterval(refreshData, 5 * 60 * 1000);
    
    // Track admin activity
    if (window.CSA && window.CSA.Analytics) {
        window.CSA.Analytics.trackCustomEvent('admin', 'dashboard_view', {
            admin_role: '<?php echo htmlspecialchars($_SESSION['admin_role']); ?>'
        });
    }
    </script>
</body>
</html>
