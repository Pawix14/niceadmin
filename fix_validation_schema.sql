-- Fix Missing Database Columns for Validation System
-- Run this file to add required columns for BookingValidator to work properly

-- 1. Add missing columns to customers table
ALTER TABLE customers 
ADD COLUMN IF NOT EXISTS birthdate DATE NULL COMMENT 'Required for age validation',
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Active' COMMENT 'Active/Inactive status',
ADD COLUMN IF NOT EXISTS account_verified TINYINT(1) DEFAULT 1 COMMENT 'Account verification status';

-- 2. Add missing columns to car_rentals table
ALTER TABLE car_rentals
ADD COLUMN IF NOT EXISTS customer_id INT NULL COMMENT 'Link to customers table',
ADD COLUMN IF NOT EXISTS return_date DATE NULL COMMENT 'Alias for dropoff_date',
ADD COLUMN IF NOT EXISTS cancellation_fee DECIMAL(10,2) DEFAULT 0 COMMENT 'Fee charged for cancellation',
ADD COLUMN IF NOT EXISTS refund_amount DECIMAL(10,2) DEFAULT 0 COMMENT 'Amount refunded after cancellation',
ADD COLUMN IF NOT EXISTS cancelled_at TIMESTAMP NULL COMMENT 'Timestamp when booking was cancelled';

-- 3. Update return_date to match dropoff_date for existing records
UPDATE car_rentals SET return_date = dropoff_date WHERE return_date IS NULL;

-- 4. Add indexes for better query performance
ALTER TABLE car_rentals ADD INDEX IF NOT EXISTS idx_customer_email (customer_email);
ALTER TABLE car_rentals ADD INDEX IF NOT EXISTS idx_customer_id (customer_id);
ALTER TABLE car_rentals ADD INDEX IF NOT EXISTS idx_status (status);
ALTER TABLE car_rentals ADD INDEX IF NOT EXISTS idx_pickup_date (pickup_date);
ALTER TABLE car_rentals ADD INDEX IF NOT EXISTS idx_return_date (return_date);

-- 5. Add foreign key constraint (optional - only if customer IDs are properly set)
-- ALTER TABLE car_rentals 
-- ADD CONSTRAINT fk_customer 
-- FOREIGN KEY (customer_id) REFERENCES customers(id) 
-- ON DELETE SET NULL;

-- 6. Update existing customers with default values
UPDATE customers SET status = 'Active' WHERE status IS NULL OR status = '';
UPDATE customers SET account_verified = 1 WHERE account_verified IS NULL;

-- 7. Set sample birthdates for existing customers (for testing)
-- UPDATE customers SET birthdate = DATE_SUB(CURDATE(), INTERVAL 25 YEAR) WHERE birthdate IS NULL;

SELECT 'Database schema updated successfully for validation system!' as message;
