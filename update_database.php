<?php
/**
 * Database Schema Update Script
 * Run this once to add missing columns to events table and create admin_settings table
 * DELETE THIS FILE AFTER RUNNING FOR SECURITY
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>Database Schema Update</h2>";

try {
    $pdo = Database::getConnection();
    echo "✅ Database connection: OK<br><br>";
    
    // Check current events table structure
    echo "<h3>Current Events Table Structure:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM events");
    $currentColumns = [];
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $currentColumns[] = $row['Field'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // Add missing columns to events table
    $columnsToAdd = [
        'description' => "ALTER TABLE events ADD COLUMN description TEXT",
        'max_capacity' => "ALTER TABLE events ADD COLUMN max_capacity INT",
        'created_by' => "ALTER TABLE events ADD COLUMN created_by INT",
        'updated_at' => "ALTER TABLE events ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    echo "<h3>Adding Missing Columns:</h3>";
    foreach ($columnsToAdd as $column => $sql) {
        if (!in_array($column, $currentColumns)) {
            try {
                $pdo->exec($sql);
                echo "✅ Added column: <strong>$column</strong><br>";
            } catch (Exception $e) {
                echo "❌ Error adding column $column: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "⚠️ Column <strong>$column</strong> already exists<br>";
        }
    }
    
    echo "<br>";
    
    // Create admin_settings table
    echo "<h3>Creating Admin Settings Table:</h3>";
    try {
        $sql = "CREATE TABLE IF NOT EXISTS admin_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT,
            updated_by INT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_admin_settings_key (setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "✅ Admin settings table created successfully<br>";
        
        // Insert default setting
        $stmt = $pdo->prepare("INSERT IGNORE INTO admin_settings (setting_key, setting_value, updated_by) VALUES (?, ?, ?)");
        $stmt->execute(['events_auto_cleanup', '0', 1]);
        echo "✅ Default auto cleanup setting added<br>";
        
    } catch (Exception $e) {
        echo "❌ Error creating admin_settings table: " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
    
    // Verify updates
    echo "<h3>Updated Events Table Structure:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM events");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    echo "<h3>✅ Database Update Complete!</h3>";
    echo "<p>You can now try adding events again. The admin events page should work properly.</p>";
    echo "<p><strong>🚨 IMPORTANT: Delete this file (update_database.php) for security!</strong></p>";
    echo "<p><a href='admin/events.php'>🔗 Go to Events Management</a></p>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>
