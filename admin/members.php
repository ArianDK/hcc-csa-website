<?php
/**
 * CSA Website - Admin Members Management
 */

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Members Management - CSA Admin';
$pageDescription = 'Manage CSA member registrations, verification, and status';

// Handle member actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $memberId = intval($_POST['member_id'] ?? 0);
        $action = $_POST['action'];
        
        if ($memberId <= 0) {
            throw new Exception('Invalid member ID');
        }
        
        switch ($action) {
            case 'verify':
                $stmt = Database::prepare("UPDATE members SET status = 'VERIFIED' WHERE id = ?");
                $stmt->execute([$memberId]);
                $success = "Member verified successfully!";
                break;
                
            case 'block':
                $stmt = Database::prepare("UPDATE members SET status = 'BLOCKED' WHERE id = ?");
                $stmt->execute([$memberId]);
                $success = "Member blocked successfully!";
                break;
                
            case 'unblock':
                $stmt = Database::prepare("UPDATE members SET status = 'VERIFIED' WHERE id = ?");
                $stmt->execute([$memberId]);
                $success = "Member unblocked successfully!";
                break;
                
            case 'delete':
                $stmt = Database::prepare("DELETE FROM members WHERE id = ?");
                $stmt->execute([$memberId]);
                $success = "Member deleted successfully!";
                break;
                
            default:
                throw new Exception('Invalid action');
        }
        
        // Log admin action
        error_log("Admin {$_SESSION['admin_email']} performed action '$action' on member ID $memberId");
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log('Member management error: ' . $e->getMessage());
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';
$searchTerm = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Build query
$whereConditions = [];
$params = [];

if ($statusFilter !== 'all') {
    $whereConditions[] = "status = ?";
    $params[] = strtoupper($statusFilter);
}

if (!empty($searchTerm)) {
    $whereConditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR major LIKE ?)";
    $searchPattern = "%$searchTerm%";
    $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern, $searchPattern]);
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
try {
    $countQuery = "SELECT COUNT(*) as total FROM members $whereClause";
    $stmt = Database::prepare($countQuery);
    $stmt->execute($params);
    $totalMembers = $stmt->fetch()['total'];
    $totalPages = ceil($totalMembers / $perPage);
} catch (Exception $e) {
    $totalMembers = 0;
    $totalPages = 1;
}

// Get members
try {
    // Check if year_level column exists
    $stmt = Database::prepare("SHOW COLUMNS FROM members LIKE 'year_level'");
    $stmt->execute();
    $hasYearLevel = $stmt->rowCount() > 0;
    
    if ($hasYearLevel) {
        $query = "
            SELECT id, first_name, last_name, email, major, campus, year_level, 
                   phone, status, created_at, email_verified_at
            FROM members 
            $whereClause
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ";
    } else {
        $query = "
            SELECT id, first_name, last_name, email, major, campus, 
                   phone, status, created_at, email_verified_at,
                   NULL as year_level
            FROM members 
            $whereClause
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ";
    }
    
    $stmt = Database::prepare($query);
    $stmt->execute(array_merge($params, [$perPage, $offset]));
    $members = $stmt->fetchAll();
    
} catch (Exception $e) {
    $members = [];
    error_log('Error fetching members: ' . $e->getMessage());
}

// Get status counts for filters
try {
    $stmt = Database::prepare("SELECT status, COUNT(*) as count FROM members GROUP BY status");
    $stmt->execute();
    $statusCounts = [];
    while ($row = $stmt->fetch()) {
        $statusCounts[$row['status']] = $row['count'];
    }
} catch (Exception $e) {
    $statusCounts = [];
}

include __DIR__ . '/../partials/meta.php';
?>

<style>
/* Include the same admin styles from dashboard */
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

.filters-bar {
    display: flex;
    gap: var(--spacing-4);
    align-items: center;
    margin-bottom: var(--spacing-6);
    padding: var(--spacing-4);
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
}

.filter-tabs {
    display: flex;
    gap: var(--spacing-2);
}

.filter-tab {
    padding: var(--spacing-2) var(--spacing-3);
    border: 1px solid var(--border-color);
    background: var(--bg-secondary);
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-sm);
    transition: all var(--transition-fast);
}

.filter-tab:hover,
.filter-tab.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.search-box {
    flex: 1;
    max-width: 300px;
}

.members-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.members-table th,
.members-table td {
    padding: var(--spacing-3);
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.members-table th {
    background: var(--bg-tertiary);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    position: sticky;
    top: 0;
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

.action-buttons {
    display: flex;
    gap: var(--spacing-1);
}

.btn-xs {
    padding: var(--spacing-1) var(--spacing-2);
    font-size: var(--font-size-xs);
    line-height: 1.2;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: var(--spacing-2);
    margin-top: var(--spacing-6);
}

.pagination a,
.pagination span {
    padding: var(--spacing-2) var(--spacing-3);
    border: 1px solid var(--border-color);
    background: var(--bg-primary);
    color: var(--text-primary);
    text-decoration: none;
    border-radius: var(--border-radius-md);
}

.pagination a:hover {
    background: var(--bg-secondary);
}

.pagination .current {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
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

@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
    }
    
    .admin-main {
        margin-left: 0;
    }
    
    .filters-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .members-table {
        font-size: var(--font-size-sm);
    }
    
    .members-table th,
    .members-table td {
        padding: var(--spacing-2);
    }
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
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="members.php" class="active">Members</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="export.php">Export Data</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
            
            <div class="mt-8">
                <a href="../" class="btn btn-primary btn-sm btn-block back-to-site">← Back to Site</a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Members Management</h1>
                    <p class="text-muted">Manage member registrations and verification status</p>
                </div>
                <div>
                    <a href="export.php" class="btn btn-secondary">Export</a>
                </div>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Filters -->
            <div class="filters-bar">
                <div class="filter-tabs">
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'all'])); ?>" 
                       class="filter-tab <?php echo $statusFilter === 'all' ? 'active' : ''; ?>">
                        All (<?php echo array_sum($statusCounts); ?>)
                    </a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'verified'])); ?>" 
                       class="filter-tab <?php echo $statusFilter === 'verified' ? 'active' : ''; ?>">
                        Verified (<?php echo $statusCounts['VERIFIED'] ?? 0; ?>)
                    </a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'pending'])); ?>" 
                       class="filter-tab <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>">
                        Pending (<?php echo $statusCounts['PENDING'] ?? 0; ?>)
                    </a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'blocked'])); ?>" 
                       class="filter-tab <?php echo $statusFilter === 'blocked' ? 'active' : ''; ?>">
                        Blocked (<?php echo $statusCounts['BLOCKED'] ?? 0; ?>)
                    </a>
                </div>
                
                <form method="GET" class="search-box">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if ($key !== 'search'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <input type="text" 
                           name="search" 
                           placeholder="Search members..." 
                           value="<?php echo htmlspecialchars($searchTerm); ?>"
                           class="form-control">
                </form>
                
                <button type="submit" form="search-form" class="btn btn-primary">Search</button>
            </div>
            
            <!-- Members Table -->
            <div class="card">
                <div class="card-body" style="padding: 0; overflow-x: auto;">
                    <?php if (!empty($members)): ?>
                        <table class="members-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Year Level</th>
                                    <th>Major</th>
                                    <th>Campus</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></strong>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>">
                                                <?php echo htmlspecialchars($member['email']); ?>
                                            </a>
                                            <?php if ($member['email_verified_at']): ?>
                                                <span title="Email verified">✅</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($member['year_level'] ?: 'Not specified'); ?></td>
                                        <td><?php echo htmlspecialchars($member['major'] ?: 'Not specified'); ?></td>
                                        <td><?php echo htmlspecialchars($member['campus'] ?: 'Not specified'); ?></td>
                                        <td>
                                            <?php if ($member['phone']): ?>
                                                <a href="tel:<?php echo htmlspecialchars($member['phone']); ?>">
                                                    <?php echo htmlspecialchars($member['phone']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Not provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($member['status']); ?>">
                                                <?php echo $member['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span title="<?php echo date('Y-m-d H:i:s', strtotime($member['created_at'])); ?>">
                                                <?php echo date('M j, Y', strtotime($member['created_at'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($member['status'] === 'PENDING'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                        <input type="hidden" name="action" value="verify">
                                                        <button type="submit" class="btn btn-success btn-xs" 
                                                                onclick="return confirm('Verify this member?')">
                                                            ✅ Verify
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($member['status'] === 'VERIFIED'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                        <input type="hidden" name="action" value="block">
                                                        <button type="submit" class="btn btn-warning btn-xs" 
                                                                onclick="return confirm('Block this member?')">
                                                            🚫 Block
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($member['status'] === 'BLOCKED'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                        <input type="hidden" name="action" value="unblock">
                                                        <button type="submit" class="btn btn-success btn-xs" 
                                                                onclick="return confirm('Unblock this member?')">
                                                            ✅ Unblock
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-error btn-xs" 
                                                            onclick="return confirm('Permanently delete this member? This cannot be undone!')">
                                                        🗑️ Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center p-8">
                            <h3>No members found</h3>
                            <p class="text-muted">
                                <?php if (!empty($searchTerm) || $statusFilter !== 'all'): ?>
                                    No members match your current filters.
                                <?php else: ?>
                                    No members have registered yet.
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($searchTerm) || $statusFilter !== 'all'): ?>
                                <a href="members.php" class="btn btn-primary">Clear Filters</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">← Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next →</a>
                    <?php endif; ?>
                </div>
                
                <div class="text-center mt-4 text-muted">
                    Showing <?php echo min(($page - 1) * $perPage + 1, $totalMembers); ?> to 
                    <?php echo min($page * $perPage, $totalMembers); ?> of <?php echo $totalMembers; ?> members
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
    // Auto-submit search form on Enter
    document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });
    
    // Track admin activity
    if (window.CSA && window.CSA.Analytics) {
        window.CSA.Analytics.trackCustomEvent('admin', 'members_view', {
            admin_role: '<?php echo htmlspecialchars($_SESSION['admin_role']); ?>',
            filter_status: '<?php echo htmlspecialchars($statusFilter); ?>',
            total_members: <?php echo $totalMembers; ?>
        });
    }
    </script>
</body>
</html>
