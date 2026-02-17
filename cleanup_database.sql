-- CarGo Database Cleanup Script
-- This script removes all unused tables from travel_db_improved database

USE travel_db_improved;

-- Drop unused tables (tables that are not referenced in the current system)

-- Old/unused booking and payment tables
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `hotel_bookings`;
DROP TABLE IF EXISTS `tour_bookings`;
DROP TABLE IF EXISTS `flight_bookings`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `payment_methods`;
DROP TABLE IF EXISTS `payment_transactions`;

-- Old availability and pricing tables
DROP TABLE IF EXISTS `car_availability`;
DROP TABLE IF EXISTS `room_availability`;
DROP TABLE IF EXISTS `tour_availability`;

-- Old commission tables
DROP TABLE IF EXISTS `agent_commissions`;
DROP TABLE IF EXISTS `commission_payments`;

-- Old customer tables
DROP TABLE IF EXISTS `customer_favorites`;
DROP TABLE IF EXISTS `customer_preferences`;
DROP TABLE IF EXISTS `customer_addresses`;

-- Old review tables (if separate from car_reviews)
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `ratings`;

-- Old notification tables (if separate from notifications)
DROP TABLE IF EXISTS `email_notifications`;
DROP TABLE IF EXISTS `sms_notifications`;

-- Old rental tables
DROP TABLE IF EXISTS `rental_extensions`;
DROP TABLE IF EXISTS `rental_damages`;
DROP TABLE IF EXISTS `rental_insurance`;

-- Old document tables (if separate from customer_documents)
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `uploaded_files`;

-- Old system tables
DROP TABLE IF EXISTS `system_logs`;
DROP TABLE IF EXISTS `audit_trail`;
DROP TABLE IF EXISTS `sessions`;

-- Show remaining tables
SHOW TABLES;
