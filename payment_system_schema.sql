-- Payment System Database Schema

-- Payments Table
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_reference` varchar(100) NOT NULL UNIQUE,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_status` enum('Pending','Completed','Verified','Failed','Refunded') DEFAULT 'Pending',
  `payment_date` datetime NOT NULL,
  `payment_details` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `payment_reference` (`payment_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Invoices Table
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL UNIQUE,
  `booking_id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Draft','Sent','Paid','Overdue','Cancelled') DEFAULT 'Draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `payment_id` (`payment_id`),
  KEY `invoice_number` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Refunds Table
CREATE TABLE IF NOT EXISTS `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_reason` text NOT NULL,
  `refund_reference` varchar(100) NOT NULL UNIQUE,
  `refund_status` enum('Pending','Processing','Completed','Failed') DEFAULT 'Pending',
  `refund_date` datetime NOT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payment Logs Table (for audit trail)
CREATE TABLE IF NOT EXISTS `payment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update car_rentals table to add payment fields
ALTER TABLE `car_rentals` 
ADD COLUMN IF NOT EXISTS `payment_method` varchar(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `payment_reference` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `paid_at` datetime DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `refund_amount` decimal(10,2) DEFAULT 0.00;

-- Update customers table to add payment-related fields
ALTER TABLE `customers`
ADD COLUMN IF NOT EXISTS `birthdate` date DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `address` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `account_verified` tinyint(1) DEFAULT 0;

-- Create indexes for better performance
CREATE INDEX idx_payment_date ON payments(payment_date);
CREATE INDEX idx_payment_status ON payments(payment_status);
CREATE INDEX idx_invoice_date ON invoices(invoice_date);
CREATE INDEX idx_invoice_status ON invoices(status);

-- Sample data for testing (optional)
-- INSERT INTO payments (booking_id, customer_id, amount, payment_method, payment_reference, payment_status, payment_date)
-- VALUES (1, 1, 5000.00, 'gcash', 'PAY-20250101-ABC12345', 'Completed', NOW());
