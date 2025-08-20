<?php
/**
 * CSA Website - Admin Events Management
 */

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Events Management - CSA Admin';
$pageDescription = 'Manage CSA events, workshops, and activities';

$success = '';
$error = '';

// Handle event actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                // Create new event
                $title = trim($_POST['title'] ?? '');
                $summary = trim($_POST['summary'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $startTime = $_POST['start_time'] ?? '';
                $endTime = $_POST['end_time'] ?? '';
                $location = trim($_POST['location'] ?? '');
                $rsvpUrl = trim($_POST['rsvp_url'] ?? '');
                $maxCapacity = intval($_POST['max_capacity'] ?? 0);
                
                // Validation
                if (empty($title) || empty($summary) || empty($startTime)) {
                    throw new Exception('Title, summary, and start time are required.');
                }
                
                if (!empty($endTime) && strtotime($endTime) <= strtotime($startTime)) {
                    throw new Exception('End time must be after start time.');
                }
                
                // Check if new columns exist before using them
                $stmt = Database::prepare("SHOW COLUMNS FROM events LIKE 'description'");
                $stmt->execute();
                $hasDescription = $stmt->rowCount() > 0;
                
                if ($hasDescription) {
                    // Full insert with new columns
                    $stmt = Database::prepare("
                        INSERT INTO events (title, summary, description, start_time, end_time, location, rsvp_url, max_capacity, created_by, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $title, $summary, $description, $startTime, 
                        $endTime ?: null, $location ?: null, $rsvpUrl ?: null, 
                        $maxCapacity > 0 ? $maxCapacity : null, $_SESSION['admin_id']
                    ]);
                } else {
                    // Basic insert with original columns only
                    $stmt = Database::prepare("
                        INSERT INTO events (title, summary, start_time, end_time, location, rsvp_url, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $title, $summary, $startTime, 
                        $endTime ?: null, $location ?: null, $rsvpUrl ?: null
                    ]);
                }
                
                $success = "Event '$title' created successfully!";
                break;
                
            case 'update':
                // Update existing event
                $eventId = intval($_POST['event_id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $summary = trim($_POST['summary'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $startTime = $_POST['start_time'] ?? '';
                $endTime = $_POST['end_time'] ?? '';
                $location = trim($_POST['location'] ?? '');
                $rsvpUrl = trim($_POST['rsvp_url'] ?? '');
                $maxCapacity = intval($_POST['max_capacity'] ?? 0);
                
                if ($eventId <= 0) {
                    throw new Exception('Invalid event ID.');
                }
                
                if (empty($title) || empty($summary) || empty($startTime)) {
                    throw new Exception('Title, summary, and start time are required.');
                }
                
                if (!empty($endTime) && strtotime($endTime) <= strtotime($startTime)) {
                    throw new Exception('End time must be after start time.');
                }
                
                // Check if new columns exist before using them
                $stmt = Database::prepare("SHOW COLUMNS FROM events LIKE 'description'");
                $stmt->execute();
                $hasDescription = $stmt->rowCount() > 0;
                
                if ($hasDescription) {
                    // Full update with new columns
                    $stmt = Database::prepare("
                        UPDATE events 
                        SET title = ?, summary = ?, description = ?, start_time = ?, end_time = ?, 
                            location = ?, rsvp_url = ?, max_capacity = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $title, $summary, $description, $startTime, 
                        $endTime ?: null, $location ?: null, $rsvpUrl ?: null, 
                        $maxCapacity > 0 ? $maxCapacity : null, $eventId
                    ]);
                } else {
                    // Basic update with original columns only
                    $stmt = Database::prepare("
                        UPDATE events 
                        SET title = ?, summary = ?, start_time = ?, end_time = ?, location = ?, rsvp_url = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $title, $summary, $startTime, 
                        $endTime ?: null, $location ?: null, $rsvpUrl ?: null, $eventId
                    ]);
                }
                
                $success = "Event '$title' updated successfully!";
                break;
                
            case 'delete':
                // Delete event
                $eventId = intval($_POST['event_id'] ?? 0);
                
                if ($eventId <= 0) {
                    throw new Exception('Invalid event ID.');
                }
                
                // Get event title for confirmation message
                $stmt = Database::prepare("SELECT title FROM events WHERE id = ?");
                $stmt->execute([$eventId]);
                $event = $stmt->fetch();
                
                if (!$event) {
                    throw new Exception('Event not found.');
                }
                
                $stmt = Database::prepare("DELETE FROM events WHERE id = ?");
                $stmt->execute([$eventId]);
                
                $success = "Event '{$event['title']}' deleted successfully!";
                break;
                
            case 'duplicate':
                // Duplicate event
                $eventId = intval($_POST['event_id'] ?? 0);
                
                if ($eventId <= 0) {
                    throw new Exception('Invalid event ID.');
                }
                
                // Get original event data
                $stmt = Database::prepare("SELECT * FROM events WHERE id = ?");
                $stmt->execute([$eventId]);
                $originalEvent = $stmt->fetch();
                
                if (!$originalEvent) {
                    throw new Exception('Event not found.');
                }
                
                // Prepare insert query based on available columns
                $stmt = Database::prepare("SHOW COLUMNS FROM events LIKE 'description'");
                $stmt->execute();
                $hasDescription = $stmt->fetch() !== false;
                
                $stmt = Database::prepare("SHOW COLUMNS FROM events LIKE 'max_capacity'");
                $stmt->execute();
                $hasMaxCapacity = $stmt->fetch() !== false;
                
                $stmt = Database::prepare("SHOW COLUMNS FROM events LIKE 'created_by'");
                $stmt->execute();
                $hasCreatedBy = $stmt->fetch() !== false;
                
                // Create duplicate with "Copy of" prefix
                $newTitle = 'Copy of ' . $originalEvent['title'];
                
                if ($hasDescription && $hasMaxCapacity && $hasCreatedBy) {
                    $stmt = Database::prepare("
                        INSERT INTO events (title, summary, description, start_time, end_time, location, rsvp_url, max_capacity, created_by, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $newTitle,
                        $originalEvent['summary'],
                        $originalEvent['description'] ?? '',
                        $originalEvent['start_time'],
                        $originalEvent['end_time'],
                        $originalEvent['location'],
                        $originalEvent['rsvp_url'],
                        $originalEvent['max_capacity'] ?? 0,
                        $_SESSION['admin_id']
                    ]);
                } else {
                    // Fallback for basic schema
                    $stmt = Database::prepare("
                        INSERT INTO events (title, summary, start_time, end_time, location, rsvp_url, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $newTitle,
                        $originalEvent['summary'],
                        $originalEvent['start_time'],
                        $originalEvent['end_time'],
                        $originalEvent['location'],
                        $originalEvent['rsvp_url']
                    ]);
                }
                
                $success = "Event duplicated successfully! New event: '{$newTitle}'";
                break;

                
            default:
                throw new Exception('Invalid action.');
        }
        
        // Log admin action
        error_log("Admin {$_SESSION['admin_email']} performed action '$action' on events");
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log('Events management error: ' . $e->getMessage());
    }
}

// Auto cleanup functionality removed - events are kept for historical purposes

// Get filter parameters
$timeFilter = $_GET['time'] ?? 'all';
$searchTerm = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$whereConditions = [];
$params = [];

if ($timeFilter === 'upcoming') {
    $whereConditions[] = "start_time > NOW()";
} elseif ($timeFilter === 'past') {
    $whereConditions[] = "start_time <= NOW()";
}

if (!empty($searchTerm)) {
    $whereConditions[] = "(title LIKE ? OR summary LIKE ? OR location LIKE ?)";
    $searchPattern = "%$searchTerm%";
    $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern]);
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
try {
    $countQuery = "SELECT COUNT(*) as total FROM events $whereClause";
    $stmt = Database::prepare($countQuery);
    $stmt->execute($params);
    $totalEvents = $stmt->fetch()['total'];
    $totalPages = ceil($totalEvents / $perPage);
} catch (Exception $e) {
    $totalEvents = 0;
    $totalPages = 1;
}

// Get events (check for column existence first)
try {
    // Check if new columns exist
    $stmt = Database::prepare("SHOW COLUMNS FROM events LIKE 'description'");
    $stmt->execute();
    $hasNewColumns = $stmt->rowCount() > 0;
    
    if ($hasNewColumns) {
        $query = "
            SELECT id, title, summary, description, start_time, end_time, location, 
                   rsvp_url, max_capacity, created_at, updated_at
            FROM events 
            $whereClause
            ORDER BY start_time DESC 
            LIMIT ? OFFSET ?
        ";
    } else {
        $query = "
            SELECT id, title, summary, start_time, end_time, location, 
                   rsvp_url, created_at,
                   NULL as description, NULL as max_capacity, NULL as updated_at
            FROM events 
            $whereClause
            ORDER BY start_time DESC 
            LIMIT ? OFFSET ?
        ";
    }
    
    $stmt = Database::prepare($query);
    $stmt->execute(array_merge($params, [$perPage, $offset]));
    $events = $stmt->fetchAll();
    
} catch (Exception $e) {
    $events = [];
    error_log('Error fetching events: ' . $e->getMessage());
}

// Get event counts for filters
try {
    $stmt = Database::prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN start_time > NOW() THEN 1 ELSE 0 END) as upcoming,
            SUM(CASE WHEN start_time <= NOW() THEN 1 ELSE 0 END) as past
        FROM events
    ");
    $stmt->execute();
    $eventCounts = $stmt->fetch();
    
    // Ensure counts are integers (MySQL SUM can return NULL)
    $eventCounts['total'] = (int)($eventCounts['total'] ?? 0);
    $eventCounts['upcoming'] = (int)($eventCounts['upcoming'] ?? 0);
    $eventCounts['past'] = (int)($eventCounts['past'] ?? 0);
    
} catch (Exception $e) {
    $eventCounts = ['total' => 0, 'upcoming' => 0, 'past' => 0];
}

// Handle edit request
$editEvent = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    try {
        $stmt = Database::prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$editId]);
        $editEvent = $stmt->fetch();
    } catch (Exception $e) {
        $error = "Event not found.";
    }
}

include __DIR__ . '/../partials/meta.php';
?>

<style>
/* Admin layout styles */
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

.events-grid {
    display: grid;
    gap: var(--spacing-4);
    margin-bottom: var(--spacing-6);
}

.event-card {
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-4);
    box-shadow: var(--shadow-sm);
    border-left: 4px solid var(--primary-color);
}

.event-card.past {
    border-left-color: var(--text-muted);
    opacity: 0.8;
}

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-3);
}

.event-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    margin: 0;
    color: var(--text-primary);
}

.event-time {
    color: var(--text-muted);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-2);
}

.event-location {
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-3);
}

.event-summary {
    color: var(--text-secondary);
    margin-bottom: var(--spacing-3);
    line-height: 1.5;
}

.event-actions {
    display: flex;
    gap: var(--spacing-2);
}

.btn-xs {
    padding: var(--spacing-1) var(--spacing-2);
    font-size: var(--font-size-xs);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-4);
}

.form-grid-full {
    grid-column: 1 / -1;
}



/* Toggle switch styles removed - auto cleanup functionality removed */

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
    
    .form-grid {
        grid-template-columns: 1fr;
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
                    <li><a href="members.php">Members</a></li>
                    <li><a href="events.php" class="active">Events</a></li>
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
                    <h1>Events Management</h1>
                    <p class="text-muted">Create, edit, and manage CSA events</p>
                </div>
                <div>
                    <button onclick="toggleEventForm()" class="btn btn-primary">
                        <?php echo $editEvent ? 'Cancel Edit' : 'Add Event'; ?>
                    </button>
                </div>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            

            
            <!-- Event Form -->
            <div id="event-form" class="card mb-6" style="<?php echo $editEvent ? '' : 'display: none;'; ?>">
                <div class="card-header">
                    <h3 class="card-title">
                        <?php echo $editEvent ? "Edit Event: " . htmlspecialchars($editEvent['title']) : 'Create New Event'; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $editEvent ? 'update' : 'create'; ?>">
                        <?php if ($editEvent): ?>
                            <input type="hidden" name="event_id" value="<?php echo $editEvent['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-grid">
                            <div>
                                <label for="title" class="form-label">Event Title *</label>
                                <input type="text" id="title" name="title" class="form-control" required
                                       value="<?php echo htmlspecialchars($editEvent['title'] ?? ''); ?>">
                            </div>
                            
                            <div>
                                <label for="location" class="form-label">Location</label>
                                <input type="text" id="location" name="location" class="form-control"
                                       value="<?php echo htmlspecialchars($editEvent['location'] ?? ''); ?>">
                            </div>
                            
                            <div>
                                <label for="start_time" class="form-label">Start Date & Time *</label>
                                <input type="datetime-local" id="start_time" name="start_time" class="form-control" required
                                       value="<?php echo $editEvent ? date('Y-m-d\TH:i', strtotime($editEvent['start_time'])) : ''; ?>">
                            </div>
                            
                            <div>
                                <label for="end_time" class="form-label">End Date & Time</label>
                                <input type="datetime-local" id="end_time" name="end_time" class="form-control"
                                       value="<?php echo $editEvent && $editEvent['end_time'] ? date('Y-m-d\TH:i', strtotime($editEvent['end_time'])) : ''; ?>">
                            </div>
                            
                            <div>
                                <label for="max_capacity" class="form-label">Max Capacity</label>
                                <input type="number" id="max_capacity" name="max_capacity" class="form-control" min="0"
                                       value="<?php echo $editEvent['max_capacity'] ?? ''; ?>">
                            </div>
                            
                            <div>
                                <label for="rsvp_url" class="form-label">RSVP URL</label>
                                <input type="url" id="rsvp_url" name="rsvp_url" class="form-control"
                                       value="<?php echo htmlspecialchars($editEvent['rsvp_url'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-grid-full">
                                <label for="summary" class="form-label">Summary *</label>
                                <textarea id="summary" name="summary" class="form-control" rows="3" required><?php echo htmlspecialchars($editEvent['summary'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-grid-full">
                                <label for="description" class="form-label">Full Description</label>
                                <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($editEvent['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" onclick="toggleEventForm()" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <?php echo $editEvent ? 'Update Event' : 'Create Event'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters-bar">
                <div class="filter-tabs">
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['time' => 'all'])); ?>" 
                       class="filter-tab <?php echo $timeFilter === 'all' ? 'active' : ''; ?>">
                        All (<?php echo $eventCounts['total']; ?>)
                    </a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['time' => 'upcoming'])); ?>" 
                       class="filter-tab <?php echo $timeFilter === 'upcoming' ? 'active' : ''; ?>">
                        Upcoming (<?php echo $eventCounts['upcoming']; ?>)
                    </a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['time' => 'past'])); ?>" 
                       class="filter-tab <?php echo $timeFilter === 'past' ? 'active' : ''; ?>">
                        Past (<?php echo $eventCounts['past']; ?>)
                    </a>
                </div>
                
                <form method="GET" class="flex gap-2">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if ($key !== 'search'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <input type="text" name="search" placeholder="Search events..." 
                           value="<?php echo htmlspecialchars($searchTerm); ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            
            <!-- Events List -->
            <?php if (!empty($events)): ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <?php 
                        $isPast = strtotime($event['start_time']) <= time();
                        $startDate = new DateTime($event['start_time']);
                        $endDate = $event['end_time'] ? new DateTime($event['end_time']) : null;
                        ?>
                        <div class="event-card <?php echo $isPast ? 'past' : ''; ?>">
                            <div class="event-header">
                                <div>
                                    <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <div class="event-time">
                                        <?php echo $startDate->format('l, F j, Y'); ?> at <?php echo $startDate->format('g:i A'); ?>
                                        <?php if ($endDate): ?>
                                            - <?php echo $endDate->format('g:i A'); ?>
                                        <?php endif; ?>
                                        <?php if ($isPast): ?>
                                            <span class="text-muted">(Past Event)</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($event['location']): ?>
                                        <div class="event-location"><?php echo htmlspecialchars($event['location']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="event-actions">
                                    <a href="?edit=<?php echo $event['id']; ?>" class="btn btn-outline btn-xs">Edit</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="duplicate">
                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                        <button type="submit" class="btn btn-secondary btn-xs" 
                                                onclick="return confirm('Duplicate this event?')">
                                            Duplicate
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                        <button type="submit" class="btn btn-error btn-xs" 
                                                onclick="return confirm('Delete this event? This cannot be undone!')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="event-summary">
                                <?php echo nl2br(htmlspecialchars($event['summary'])); ?>
                            </div>
                            
                            <?php if ($event['description']): ?>
                                <div class="event-description" style="color: var(--text-muted); font-size: var(--font-size-sm);">
                                    <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 200))); ?>
                                    <?php if (strlen($event['description']) > 200): ?>...<?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between items-center mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                                <div class="text-muted" style="font-size: var(--font-size-xs);">
                                    <?php if ($event['max_capacity']): ?>
                                        Max: <?php echo $event['max_capacity']; ?> people
                                    <?php endif; ?>
                                    <?php if ($event['rsvp_url']): ?>
                                        | <a href="<?php echo htmlspecialchars($event['rsvp_url']); ?>" target="_blank">RSVP Link</a>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted" style="font-size: var(--font-size-xs);">
                                    Created: <?php echo date('M j, Y', strtotime($event['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center p-8">
                    <h3>No events found</h3>
                    <p class="text-muted">
                        <?php if (!empty($searchTerm) || $timeFilter !== 'all'): ?>
                            No events match your current filters.
                        <?php else: ?>
                            No events have been created yet.
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($searchTerm) || $timeFilter !== 'all'): ?>
                        <a href="events.php" class="btn btn-primary">Clear Filters</a>
                    <?php else: ?>
                        <button onclick="toggleEventForm()" class="btn btn-primary">Create Your First Event</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: var(--spacing-2); margin-top: var(--spacing-6);">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                           class="btn btn-outline btn-sm">← Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="btn btn-primary btn-sm"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                               class="btn btn-outline btn-sm"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                           class="btn btn-outline btn-sm">Next →</a>
                    <?php endif; ?>
                </div>
                
                <div class="text-center mt-4 text-muted">
                    Showing <?php echo min(($page - 1) * $perPage + 1, $totalEvents); ?> to 
                    <?php echo min($page * $perPage, $totalEvents); ?> of <?php echo $totalEvents; ?> events
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
    function toggleEventForm() {
        const form = document.getElementById('event-form');
        const isVisible = form.style.display !== 'none';
        form.style.display = isVisible ? 'none' : 'block';
        
        // If canceling edit, remove edit parameter from URL
        if (isVisible && window.location.search.includes('edit=')) {
            const url = new URL(window.location);
            url.searchParams.delete('edit');
            window.history.replaceState({}, '', url);
            window.location.reload();
        }
    }
    
    // Track admin activity
    if (window.CSA && window.CSA.Analytics) {
        window.CSA.Analytics.trackCustomEvent('admin', 'events_view', {
            admin_role: '<?php echo htmlspecialchars($_SESSION['admin_role']); ?>',
            filter_time: '<?php echo htmlspecialchars($timeFilter); ?>',
            total_events: <?php echo $totalEvents; ?>
        });
    }
    </script>
</body>
</html>
