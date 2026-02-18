-- Notification System Upgrade Schema
-- Run this to add new features to notifications table

-- Add new columns for enhanced notifications
ALTER TABLE notifications 
ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'general' COMMENT 'booking, payment, document, message, alert',
ADD COLUMN IF NOT EXISTS priority VARCHAR(20) DEFAULT 'normal' COMMENT 'critical, important, normal',
ADD COLUMN IF NOT EXISTS icon VARCHAR(50) DEFAULT 'bell' COMMENT 'Icon identifier',
ADD COLUMN IF NOT EXISTS action_url VARCHAR(255) NULL COMMENT 'URL for action button',
ADD COLUMN IF NOT EXISTS action_label VARCHAR(50) NULL COMMENT 'Label for action button',
ADD COLUMN IF NOT EXISTS expires_at DATETIME NULL COMMENT 'Auto-dismiss after this date',
ADD COLUMN IF NOT EXISTS dismissed_at DATETIME NULL COMMENT 'When user dismissed notification';

-- Add indexes for better performance
ALTER TABLE notifications ADD INDEX IF NOT EXISTS idx_category (category);
ALTER TABLE notifications ADD INDEX IF NOT EXISTS idx_priority (priority);
ALTER TABLE notifications ADD INDEX IF NOT EXISTS idx_is_read (is_read);
ALTER TABLE notifications ADD INDEX IF NOT EXISTS idx_created_at (created_at);
ALTER TABLE notifications ADD INDEX IF NOT EXISTS idx_user_type_id (user_type, user_id);

-- Update existing notifications with categories
UPDATE notifications SET category = 'booking' WHERE title LIKE '%Booking%' OR title LIKE '%booking%';
UPDATE notifications SET category = 'payment' WHERE title LIKE '%Payment%' OR title LIKE '%payment%';
UPDATE notifications SET category = 'document' WHERE title LIKE '%Document%' OR title LIKE '%document%';
UPDATE notifications SET category = 'message' WHERE title LIKE '%Message%' OR title LIKE '%message%';

-- Set priorities for existing notifications
UPDATE notifications SET priority = 'critical' WHERE title LIKE '%Overdue%' OR title LIKE '%Urgent%';
UPDATE notifications SET priority = 'important' WHERE title LIKE '%Reminder%' OR title LIKE '%Due%';

SELECT 'Notification table upgraded successfully!' as message;
