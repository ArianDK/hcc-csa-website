-- Update events table with additional fields needed for admin management
-- Run this to add missing columns and create admin_settings table

-- Add missing columns to events table
ALTER TABLE events 
ADD COLUMN IF NOT EXISTS description TEXT,
ADD COLUMN IF NOT EXISTS max_capacity INT,
ADD COLUMN IF NOT EXISTS created_by INT,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Create admin_settings table for storing configuration
CREATE TABLE IF NOT EXISTS admin_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_by INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admin_settings_key (setting_key),
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default auto cleanup setting (disabled by default)
INSERT IGNORE INTO admin_settings (setting_key, setting_value, updated_by) 
VALUES ('events_auto_cleanup', '0', 1);
