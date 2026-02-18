-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 02:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_db_improved`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'Admin',
  `status` varchar(20) DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `admin_id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'ADM001', 'admin', '$2y$10$e5kfnM6EOQxR/PF7ikmFeuCkfB25rREF9NM58w5bXCativDz7ZDKu', 'System Administrator', 'admin@paradise.com', NULL, 'Super Admin', 'Active', '2026-02-18 09:19:04', '2026-02-09 04:19:37', '2026-02-18 01:19:04'),
(5, 'ADM943', 'staff@gmail.com', '$2y$10$eqwobAO5oJY9ad/HLIeTJOg7/wS11NQ1ttKHpd458qD01yHhmsI/u', 'Gabriel', 'staff@gmail.com', '09940213443', 'Staff', 'Active', '2026-02-18 09:35:26', '2026-02-17 03:37:26', '2026-02-18 01:35:26');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `fuel_type` varchar(30) DEFAULT 'Gasoline',
  `transmission` varchar(20) DEFAULT 'Automatic',
  `seating_capacity` int(11) DEFAULT 5,
  `mileage_limit` int(11) DEFAULT 200,
  `car_year` int(11) DEFAULT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `vin` varchar(50) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `type`, `daily_rate`, `image`, `features`, `fuel_type`, `transmission`, `seating_capacity`, `mileage_limit`, `car_year`, `license_plate`, `vin`, `color`, `status`, `is_featured`, `created_at`) VALUES
(1, 'Kia Rio or Similar', 'Economy', 2850.00, 'assets/img/cars/kia-rio.jpg', '4-5 seats, Air Conditioning, Automatic, Fuel Efficient', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:09:52'),
(2, 'Toyota Corolla or Similar', 'Economy', 3150.00, 'assets/img/cars/toyota-corolla.jpg', '5 seats, Air Conditioning, Automatic, Spacious Trunk', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:09:52'),
(3, 'Honda Civic or Similar', 'Compact', 3650.00, 'assets/img/cars/honda-civic.jpg', '5 seats, Premium Sound, Automatic, GPS Navigation', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:09:52'),
(4, 'Ford Mustang or Similar', 'SUV', 5108.00, 'assets/img/cars/ford-mustang.jpg', '4 seats, Sports Car, Automatic, Premium Features', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 1, '2026-02-09 05:09:52'),
(5, 'BMW X7 or Similar', 'Luxury', 5878.00, 'assets/img/cars/bmw-x7.jpg', '7 seats, Leather Seats, Automatic, Premium Package', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:09:52'),
(6, 'Tesla Model 3 or Similar', 'Electric', 4250.00, 'assets/img/cars/tesla-model3.jpg', '5 seats, Electric, Autopilot, Premium Interior', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:09:52'),
(7, 'Kia Rio or Similar', 'Economy', 2850.00, 'assets/img/cars/kia-rio.jpg', '4-5 seats, Air Conditioning, Automatic, Fuel Efficient', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:12:20'),
(8, 'Toyota Corolla or Similar', 'Economy', 3150.00, 'assets/img/cars/toyota-corolla.jpg', '5 seats, Air Conditioning, Automatic, Spacious Trunk', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:12:20'),
(11, 'Honda Civic 1', 'Compact', 3650.00, 'assets/img/cars/honda-civic.jpg', '5 seats, Premium Sound, Automatic, GPS Navigation', 'Gasoline', 'Automatic', 5, 200, NULL, '', '', '', 'Active', 1, '2026-02-09 05:12:20'),
(20, 'Kia Rio 1', 'Economy', 2850.00, 'assets/img/cars/kia-rio.jpg', '4-5 seats, Air Conditioning, Automatic, Fuel Efficient', 'Gasoline', 'Automatic', 5, 200, NULL, '', '', '', 'Active', 1, '2026-02-09 05:13:22'),
(21, 'Toyota Corolla ', 'Economy', 3150.00, 'assets/img/cars/toyota-corolla.jpg', '5 seats, Air Conditioning, Automatic, Spacious Trunk', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(22, 'Mitsubishi Mirage', 'Economy', 2650.00, 'assets/img/cars/mitsubishi-mirage.jpg', '4 seats, Air Conditioning, Manual, Compact', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(23, 'Hyundai Accent', 'Economy', 2950.00, 'assets/img/cars/hyundai-accent.jpg', '5 seats, Air Conditioning, Automatic, Bluetooth', 'Gasoline', 'Automatic', 5, 200, NULL, '', '', '', 'Active', 1, '2026-02-09 05:13:22'),
(24, 'Honda Civic', 'Compact', 3650.00, 'assets/img/cars/honda-civic.jpg', '5 seats, Premium Sound, Automatic, GPS Navigation', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 1, '2026-02-09 05:13:22'),
(25, 'Mazda 3', 'Compact', 3850.00, 'assets/img/cars/mazda-3.jpg', '5 seats, Leather Seats, Automatic, Sunroof', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(26, 'Volkswagen Jetta ', 'Compact', 3950.00, 'assets/img/cars/volkswagen-jetta.jpg', '5 seats, Turbo Engine, Automatic, Premium Audio', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(27, 'Ford Mustang or Similar', 'SUV', 5108.00, 'assets/img/cars/ford-mustang.jpg', '4 seats, Sports Car, Automatic, Premium Features', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(30, 'BMW X7 or Similar', 'Luxury', 5878.00, 'assets/img/cars/bmw-x7.jpg', '7 seats, Leather Seats, Automatic, Premium Package', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(33, 'Tesla Model 4', 'Electric', 4250.00, 'assets/img/cars/tesla-model3.jpg', '5 seats, Electric, Autopilot, Premium Interior', 'Gasoline', 'Automatic', 5, 200, NULL, '', '', '', 'Active', 0, '2026-02-09 05:13:22'),
(35, 'Hyundai Ioniq 5 ', 'Electric', 4500.00, 'assets/img/cars/hyundai-ioniq-5.jpg', '5 seats, Electric, V2L, Ultra Fast Charging', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 1, '2026-02-09 05:13:22'),
(36, 'Test', 'Luxury', 4500.00, 'uploads/cars/69950e2217d0b.png', 'TEST', 'Electric', 'Automatic', 2, 3000, 2026, 'ABC-2313', '203230204202030402', 'Black', 'Active', 0, '2026-02-18 00:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `car_availability`
--

CREATE TABLE `car_availability` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Blocked',
  `reason` varchar(255) DEFAULT NULL,
  `price_multiplier` decimal(3,2) DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_blocked_dates`
--

CREATE TABLE `car_blocked_dates` (
  `id` int(11) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `block_start` date NOT NULL,
  `block_end` date NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_blocked_dates`
--

INSERT INTO `car_blocked_dates` (`id`, `car_model`, `block_start`, `block_end`, `reason`, `created_by`, `created_at`) VALUES
(1, 'Honda Civic', '2026-02-17', '2026-02-18', 'Repair', 'staff@gmail.com', '2026-02-17 04:21:42'),
(2, 'Honda Civic', '2026-02-17', '2026-03-27', 'Inspection', 'staff@gmail.com', '2026-02-17 04:45:29');

-- --------------------------------------------------------

--
-- Table structure for table `car_maintenance`
--

CREATE TABLE `car_maintenance` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `maintenance_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `maintenance_date` date NOT NULL,
  `next_service_date` date DEFAULT NULL,
  `performed_by` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_maintenance`
--

INSERT INTO `car_maintenance` (`id`, `car_id`, `maintenance_type`, `description`, `cost`, `maintenance_date`, `next_service_date`, `performed_by`, `status`, `created_at`) VALUES
(1, 7, 'Tire Change', 'wewqe', 2340.00, '2026-02-13', '2026-02-21', 'admin', 'Completed', '2026-02-13 03:12:01'),
(2, 30, 'Inspection', 'wdrwr', 2500.00, '2026-02-17', '2026-02-17', 'admin', 'Completed', '2026-02-17 03:50:01');

-- --------------------------------------------------------

--
-- Table structure for table `car_rentals`
--

CREATE TABLE `car_rentals` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_age` int(11) NOT NULL,
  `pickup_date` date NOT NULL,
  `dropoff_date` date NOT NULL,
  `pickup_time` time NOT NULL,
  `dropoff_time` time NOT NULL,
  `pickup_location` varchar(200) NOT NULL,
  `dropoff_location` varchar(200) NOT NULL,
  `car_type` varchar(50) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `car_image` varchar(255) DEFAULT NULL,
  `rental_days` int(11) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `insurance_fee` decimal(10,2) DEFAULT 0.00,
  `additional_fees` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `agent_id` varchar(50) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Pending',
  `admin_notes` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `license_number` varchar(50) DEFAULT '',
  `license_expiry` date DEFAULT NULL,
  `actual_return_date` datetime DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `receipt_file` varchar(255) DEFAULT NULL,
  `payment_verified_by` varchar(50) DEFAULT NULL,
  `payment_verified_at` datetime DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT 'Full Payment',
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `remaining_balance` decimal(10,2) DEFAULT 0.00,
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `actual_rental_days` int(11) DEFAULT 0,
  `pickup_status` varchar(20) DEFAULT 'Pending',
  `pickup_confirmed_at` datetime DEFAULT NULL,
  `return_pickup_status` varchar(20) DEFAULT 'Pending',
  `return_pickup_confirmed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_rentals`
--

INSERT INTO `car_rentals` (`id`, `booking_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_age`, `pickup_date`, `dropoff_date`, `pickup_time`, `dropoff_time`, `pickup_location`, `dropoff_location`, `car_type`, `car_model`, `car_image`, `rental_days`, `daily_rate`, `subtotal`, `insurance_fee`, `additional_fees`, `total_amount`, `promo_code`, `discount_amount`, `agent_id`, `agent_commission`, `status`, `admin_notes`, `payment_method`, `payment_status`, `created_at`, `updated_at`, `license_number`, `license_expiry`, `actual_return_date`, `payment_proof`, `invoice_number`, `receipt_file`, `payment_verified_by`, `payment_verified_at`, `payment_type`, `amount_paid`, `remaining_balance`, `refund_amount`, `actual_rental_days`, `pickup_status`, `pickup_confirmed_at`, `return_pickup_status`, `return_pickup_confirmed_at`) VALUES
(103, 'CAR-20260115-A1B2C3', 'John Doe', 'john.doe@email.com', '+63 917 123 4567', 35, '2026-01-20', '2026-01-25', '10:00:00', '10:00:00', 'MNL - Manila Ninoy Aquino International Airport', 'MNL - Manila Ninoy Aquino International Airport', 'Sedan', 'Toyota Camry', NULL, 5, 2500.00, 12500.00, 1500.00, 0.00, 14000.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-01-15 02:30:00', '2026-02-17 07:06:12', 'N01-23-456789', '2028-12-31', NULL, NULL, 'INV-20260115-0001', 'REC-20260115-0001', 'Gabriel', '2026-01-15 11:00:00', 'Full Payment', 14000.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(104, 'CAR-20260116-D4E5F6', 'Maria Santos', 'maria.santos@email.com', '+63 918 234 5678', 28, '2026-02-18', '2026-02-22', '09:00:00', '09:00:00', 'Makati City Center', 'Makati City Center', 'SUV', 'Honda CR-V', NULL, 4, 3500.00, 14000.00, 1200.00, 0.00, 15200.00, NULL, 0.00, NULL, 0.00, 'Active', NULL, 'PayPal', 'Paid', '2026-01-16 03:00:00', '2026-02-17 07:06:12', 'M02-34-567890', '2029-06-30', NULL, NULL, 'INV-20260116-0002', 'REC-20260116-0002', 'Gabriel', '2026-01-16 12:00:00', 'Downpayment', 7600.00, 7600.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(105, 'CAR-20260117-G7H8I9', 'Pedro Cruz', 'pedro.cruz@email.com', '+63 919 345 6789', 42, '2026-02-20', '2026-02-23', '14:00:00', '14:00:00', 'Bonifacio Global City (BGC)', 'Bonifacio Global City (BGC)', 'Luxury', 'BMW 5 Series', NULL, 3, 5000.00, 15000.00, 2700.00, 0.00, 17700.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'Credit Card', 'Paid', '2026-01-17 01:15:00', '2026-02-17 07:07:43', 'P03-45-678901', '2027-11-15', NULL, NULL, 'INV-20260117-0003', 'REC-20260117-0003', 'Gabriel', '2026-01-17 10:00:00', 'Full Payment', 17700.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(106, 'CAR-20260118-J1K2L3', 'Ana Reyes', 'ana.reyes@email.com', '+63 920 456 7890', 31, '2026-01-22', '2026-01-27', '11:00:00', '11:00:00', 'Quezon City - Cubao', 'Quezon City - Cubao', 'Compact', 'Toyota Vios', NULL, 5, 1800.00, 9000.00, 1500.00, 0.00, 10500.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-01-18 06:20:00', '2026-02-17 07:06:12', 'A04-56-789012', '2028-08-20', NULL, NULL, 'INV-20260118-0004', 'REC-20260118-0004', 'Gabriel', '2026-01-18 15:00:00', 'Downpayment', 10500.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(107, 'CAR-20260119-M4N5O6', 'Carlos Garcia', 'carlos.garcia@email.com', '+63 921 567 8901', 38, '2026-02-19', '2026-02-24', '08:00:00', '08:00:00', 'Pasay City - Mall of Asia', 'Pasay City - Mall of Asia', 'Van', 'Toyota Hiace', NULL, 5, 4000.00, 20000.00, 1500.00, 0.00, 21500.00, NULL, 0.00, NULL, 0.00, 'Active', NULL, 'Debit Card', 'Paid', '2026-01-19 00:45:00', '2026-02-17 07:06:12', 'C05-67-890123', '2029-03-10', NULL, NULL, 'INV-20260119-0005', 'REC-20260119-0005', 'Gabriel', '2026-01-19 09:30:00', 'Full Payment', 21500.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(108, 'CAR-20260120-P7Q8R9', 'Lisa Tan', 'lisa.tan@email.com', '+63 922 678 9012', 29, '2026-02-21', '2026-02-25', '13:00:00', '13:00:00', 'CEB - Mactan-Cebu International Airport', 'CEB - Mactan-Cebu International Airport', 'Electric', 'Tesla Model 3 or Similar', NULL, 4, 3800.00, 15200.00, 1200.00, 0.00, 16400.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'PayPal', 'Paid', '2026-01-20 05:30:00', '2026-02-17 07:07:41', 'L06-78-901234', '2028-05-25', NULL, NULL, 'INV-20260120-0006', 'REC-20260120-0006', 'Gabriel', '2026-01-20 14:00:00', 'Downpayment', 8200.00, 8200.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(109, 'CAR-20260121-S1T2U3', 'Miguel Lopez', 'miguel.lopez@email.com', '+63 923 789 0123', 45, '2026-01-25', '2026-01-28', '10:30:00', '10:30:00', 'Mandaluyong City - Ortigas Center', 'Mandaluyong City - Ortigas Center', 'Sedan', 'Honda Accord', NULL, 3, 2800.00, 8400.00, 900.00, 0.00, 9300.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-01-21 02:00:00', '2026-02-17 07:06:12', 'M07-89-012345', '2027-12-31', NULL, NULL, 'INV-20260121-0007', 'REC-20260121-0007', 'Gabriel', '2026-01-21 11:00:00', 'Full Payment', 9300.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(110, 'CAR-20260122-V4W5X6', 'Sofia Martinez', 'sofia.martinez@email.com', '+63 924 890 1234', 33, '2026-02-22', '2026-02-26', '15:00:00', '15:00:00', 'MRT Ayala Station', 'MRT Ayala Station', 'SUV', 'Mitsubishi Montero Sport', NULL, 4, 3600.00, 14400.00, 1200.00, 0.00, 15600.00, NULL, 0.00, NULL, 0.00, 'Active', NULL, 'Credit Card', 'Paid', '2026-01-22 07:45:00', '2026-02-17 07:06:12', 'S08-90-123456', '2029-07-15', NULL, NULL, 'INV-20260122-0008', 'REC-20260122-0008', 'Gabriel', '2026-01-22 16:00:00', 'Downpayment', 7800.00, 7800.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(111, 'CAR-20260123-Y7Z8A9', 'David Wong', 'david.wong@email.com', '+63 925 901 2345', 40, '2026-02-23', '2026-02-27', '09:30:00', '09:30:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Luxury', 'Mercedes-Benz E-Class', NULL, 4, 5500.00, 22000.00, 2400.00, 0.00, 24400.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'PayPal', 'Paid', '2026-01-23 01:30:00', '2026-02-17 07:07:39', 'D09-01-234567', '2028-10-20', NULL, NULL, 'INV-20260123-0009', 'REC-20260123-0009', 'Gabriel', '2026-01-23 10:00:00', 'Full Payment', 24400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(112, 'CAR-20260124-B1C2D3', 'Elena Rivera', 'elena.rivera@email.com', '+63 926 012 3456', 27, '2026-01-28', '2026-02-02', '12:00:00', '12:00:00', 'CRK - Clark International Airport', 'CRK - Clark International Airport', 'Compact', 'Mazda 3', NULL, 5, 2200.00, 11000.00, 1500.00, 0.00, 12500.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-01-24 03:15:00', '2026-02-17 07:06:12', 'E10-12-345678', '2029-04-30', NULL, NULL, 'INV-20260124-0010', 'REC-20260124-0010', 'Gabriel', '2026-01-24 12:00:00', 'Downpayment', 12500.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(113, 'CAR-20260125-E4F5G6', 'Robert Kim', 'robert.kim@email.com', '+63 927 123 4567', 36, '2026-02-24', '2026-02-28', '14:30:00', '14:30:00', 'Shangri-La Hotel', 'Shangri-La Hotel', 'Van', 'Nissan Urvan', NULL, 4, 3800.00, 15200.00, 1200.00, 0.00, 16400.00, NULL, 0.00, NULL, 0.00, 'Active', NULL, 'Debit Card', 'Paid', '2026-01-25 06:00:00', '2026-02-17 07:06:12', 'R11-23-456789', '2028-09-15', NULL, NULL, 'INV-20260125-0011', 'REC-20260125-0011', 'Gabriel', '2026-01-25 15:00:00', 'Full Payment', 16400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(114, 'CAR-20260126-H7I8J9', 'Carmen Flores', 'carmen.flores@email.com', '+63 928 234 5678', 32, '2026-02-25', '2026-03-01', '11:00:00', '11:00:00', 'DVO - Francisco Bangoy International Airport', 'DVO - Francisco Bangoy International Airport', 'Sedan', 'Hyundai Elantra', NULL, 4, 2400.00, 9600.00, 1200.00, 0.00, 10800.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'PayPal', 'Paid', '2026-01-26 02:45:00', '2026-02-17 07:07:36', 'C12-34-567890', '2029-01-20', NULL, NULL, 'INV-20260126-0012', 'REC-20260126-0012', 'Gabriel', '2026-01-26 11:30:00', 'Downpayment', 5400.00, 5400.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(115, 'CAR-20260127-K1L2M3', 'James Lee', 'james.lee@email.com', '+63 929 345 6789', 41, '2026-01-30', '2026-02-03', '13:45:00', '13:45:00', 'Okada Manila', 'Okada Manila', 'SUV', 'Ford Everest', NULL, 4, 3700.00, 14800.00, 1200.00, 0.00, 16000.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'Credit Card', 'Paid', '2026-01-27 05:20:00', '2026-02-17 07:06:12', 'J13-45-678901', '2027-10-31', NULL, NULL, 'INV-20260127-0013', 'REC-20260127-0013', 'Gabriel', '2026-01-27 14:00:00', 'Full Payment', 16000.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(116, 'CAR-20260128-N4O5P6', 'Isabel Ramos', 'isabel.ramos@email.com', '+63 930 456 7890', 30, '2026-02-26', '2026-03-02', '09:15:00', '09:15:00', 'Solaire Resort & Casino', 'Solaire Resort & Casino', 'Electric', 'Nissan Leaf', NULL, 4, 3500.00, 14000.00, 1200.00, 0.00, 15200.00, NULL, 0.00, NULL, 0.00, 'Active', NULL, 'GCash', 'Paid', '2026-01-28 01:00:00', '2026-02-17 07:06:12', 'I14-56-789012', '2028-11-30', NULL, NULL, 'INV-20260128-0014', 'REC-20260128-0014', 'Gabriel', '2026-01-28 10:00:00', 'Downpayment', 7600.00, 7600.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(117, 'CAR-20260129-Q7R8S9', 'Daniel Chen', 'daniel.chen@email.com', '+63 931 567 8901', 37, '2026-02-27', '2026-03-03', '16:45:00', '16:45:00', 'LRT Buendia Station', 'LRT Buendia Station', 'Luxury', 'Audi A6', NULL, 4, 5200.00, 20800.00, 2400.00, 0.00, 23200.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'PayPal', 'Paid', '2026-01-29 08:30:00', '2026-02-17 07:07:34', 'D15-67-890123', '2029-02-28', NULL, NULL, 'INV-20260129-0015', 'REC-20260129-0015', 'Gabriel', '2026-01-29 17:00:00', 'Full Payment', 23200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(118, 'CAR-20251105-A1A1A1', 'Mark Johnson', 'mark.johnson@email.com', '+63 932 111 2222', 34, '2025-11-10', '2025-11-15', '10:00:00', '10:00:00', 'MNL - Manila Ninoy Aquino International Airport', 'MNL - Manila Ninoy Aquino International Airport', 'Sedan', 'Toyota Camry', NULL, 5, 2500.00, 12500.00, 1500.00, 0.00, 14000.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2025-11-05 01:30:00', '2026-02-17 07:06:12', 'M16-11-111111', '2028-12-31', NULL, NULL, 'INV-20251105-0016', 'REC-20251105-0016', 'Gabriel', '2025-11-05 10:00:00', 'Full Payment', 14000.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(119, 'CAR-20251112-B2B2B2', 'Sarah Williams', 'sarah.williams@email.com', '+63 933 222 3333', 29, '2025-11-18', '2025-11-22', '11:00:00', '11:00:00', 'Makati City Center', 'Makati City Center', 'SUV', 'Honda CR-V', NULL, 4, 3500.00, 14000.00, 1200.00, 0.00, 15200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'Credit Card', 'Paid', '2025-11-12 03:00:00', '2026-02-17 07:06:12', 'S17-22-222222', '2029-05-15', NULL, NULL, 'INV-20251112-0017', 'REC-20251112-0017', 'Gabriel', '2025-11-12 11:30:00', 'Downpayment', 15200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(120, 'CAR-20251118-C3C3C3', 'Kevin Brown', 'kevin.brown@email.com', '+63 934 333 4444', 41, '2025-11-25', '2025-11-28', '14:00:00', '14:00:00', 'Bonifacio Global City (BGC)', 'Bonifacio Global City (BGC)', 'Compact', 'Toyota Vios', NULL, 3, 1800.00, 5400.00, 900.00, 0.00, 6300.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'PayPal', 'Paid', '2025-11-18 06:00:00', '2026-02-17 07:06:12', 'K18-33-333333', '2027-09-20', NULL, NULL, 'INV-20251118-0018', 'REC-20251118-0018', 'Gabriel', '2025-11-18 14:30:00', 'Full Payment', 6300.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(121, 'CAR-20251122-D4D4D4', 'Amanda Davis', 'amanda.davis@email.com', '+63 935 444 5555', 26, '2025-11-28', '2025-12-02', '09:00:00', '09:00:00', 'Quezon City - Cubao', 'Quezon City - Cubao', 'Van', 'Toyota Hiace', NULL, 4, 4000.00, 16000.00, 1200.00, 0.00, 17200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2025-11-22 01:00:00', '2026-02-17 07:06:12', 'A19-44-444444', '2029-03-10', NULL, NULL, 'INV-20251122-0019', 'REC-20251122-0019', 'Gabriel', '2025-11-22 09:30:00', 'Downpayment', 17200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(122, 'CAR-20251126-E5E5E5', 'Ryan Miller', 'ryan.miller@email.com', '+63 936 555 6666', 38, '2025-11-29', '2025-12-03', '13:00:00', '13:00:00', 'Pasay City - Mall of Asia', 'Pasay City - Mall of Asia', 'Luxury', 'BMW 5 Series', NULL, 4, 5000.00, 20000.00, 2400.00, 0.00, 22400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'Debit Card', 'Paid', '2025-11-26 05:00:00', '2026-02-17 07:06:12', 'R20-55-555555', '2028-07-25', NULL, NULL, 'INV-20251126-0020', 'REC-20251126-0020', 'Gabriel', '2025-11-26 13:30:00', 'Full Payment', 22400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(123, 'CAR-20251203-F6F6F6', 'Jessica Moore', 'jessica.moore@email.com', '+63 937 666 7777', 31, '2025-12-08', '2025-12-12', '10:30:00', '10:30:00', 'CEB - Mactan-Cebu International Airport', 'CEB - Mactan-Cebu International Airport', 'Sedan', 'Honda Accord', NULL, 4, 2800.00, 11200.00, 1200.00, 0.00, 12400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'PayPal', 'Paid', '2025-12-03 02:30:00', '2026-02-17 07:06:12', 'J21-66-666666', '2028-11-30', NULL, NULL, 'INV-20251203-0021', 'REC-20251203-0021', 'Gabriel', '2025-12-03 11:00:00', 'Full Payment', 12400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(124, 'CAR-20251210-G7G7G7', 'Brian Taylor', 'brian.taylor@email.com', '+63 938 777 8888', 35, '2025-12-15', '2025-12-19', '12:00:00', '12:00:00', 'Mandaluyong City - Ortigas Center', 'Mandaluyong City - Ortigas Center', 'SUV', 'Mitsubishi Montero Sport', NULL, 4, 3600.00, 14400.00, 1200.00, 0.00, 15600.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'Credit Card', 'Paid', '2025-12-10 04:00:00', '2026-02-17 07:06:12', 'B22-77-777777', '2029-01-15', NULL, NULL, 'INV-20251210-0022', 'REC-20251210-0022', 'Gabriel', '2025-12-10 12:30:00', 'Downpayment', 15600.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(125, 'CAR-20251215-H8H8H8', 'Nicole Anderson', 'nicole.anderson@email.com', '+63 939 888 9999', 28, '2025-12-20', '2025-12-24', '15:00:00', '15:00:00', 'MRT Ayala Station', 'MRT Ayala Station', 'Electric', 'Tesla Model 3 or Similar', NULL, 4, 3800.00, 15200.00, 1200.00, 0.00, 16400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2025-12-15 07:00:00', '2026-02-17 07:06:12', 'N23-88-888888', '2028-10-20', NULL, NULL, 'INV-20251215-0023', 'REC-20251215-0023', 'Gabriel', '2025-12-15 15:30:00', 'Full Payment', 16400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(126, 'CAR-20251220-I9I9I9', 'Christopher Thomas', 'chris.thomas@email.com', '+63 940 999 0000', 43, '2025-12-25', '2025-12-29', '09:30:00', '09:30:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Van', 'Nissan Urvan', NULL, 4, 3800.00, 15200.00, 1200.00, 0.00, 16400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'PayPal', 'Paid', '2025-12-20 01:30:00', '2026-02-17 07:06:12', 'C24-99-999999', '2027-08-31', NULL, NULL, 'INV-20251220-0024', 'REC-20251220-0024', 'Gabriel', '2025-12-20 10:00:00', 'Downpayment', 16400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(127, 'CAR-20251228-J1J1J1', 'Michelle Jackson', 'michelle.jackson@email.com', '+63 941 000 1111', 33, '2025-12-30', '2026-01-03', '14:00:00', '14:00:00', 'CRK - Clark International Airport', 'CRK - Clark International Airport', 'Luxury', 'Mercedes-Benz E-Class', NULL, 4, 5500.00, 22000.00, 2400.00, 0.00, 24400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'Debit Card', 'Paid', '2025-12-28 06:00:00', '2026-02-17 07:06:12', 'M25-00-000000', '2029-04-15', NULL, NULL, 'INV-20251228-0025', 'REC-20251228-0025', 'Gabriel', '2025-12-28 14:30:00', 'Full Payment', 24400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(128, 'CAR-20260105-K2K2K2', 'Steven White', 'steven.white@email.com', '+63 942 111 2222', 39, '2026-01-08', '2026-01-12', '11:00:00', '11:00:00', 'Shangri-La Hotel', 'Shangri-La Hotel', 'Sedan', 'Hyundai Elantra', NULL, 4, 2400.00, 9600.00, 1200.00, 0.00, 10800.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-01-05 03:00:00', '2026-02-17 07:06:12', 'S26-11-111112', '2028-06-30', NULL, NULL, 'INV-20260105-0026', 'REC-20260105-0026', 'Gabriel', '2026-01-05 11:30:00', 'Full Payment', 10800.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(129, 'CAR-20260108-L3L3L3', 'Rachel Harris', 'rachel.harris@email.com', '+63 943 222 3333', 27, '2026-01-12', '2026-01-16', '13:00:00', '13:00:00', 'DVO - Francisco Bangoy International Airport', 'DVO - Francisco Bangoy International Airport', 'SUV', 'Ford Everest', NULL, 4, 3700.00, 14800.00, 1200.00, 0.00, 16000.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'Credit Card', 'Paid', '2026-01-08 05:00:00', '2026-02-17 07:06:12', 'R27-22-222223', '2029-02-28', NULL, NULL, 'INV-20260108-0027', 'REC-20260108-0027', 'Gabriel', '2026-01-08 13:30:00', 'Downpayment', 16000.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(130, 'CAR-20260110-M4M4M4', 'Andrew Martin', 'andrew.martin@email.com', '+63 944 333 4444', 36, '2026-01-14', '2026-01-18', '10:00:00', '10:00:00', 'Okada Manila', 'Okada Manila', 'Compact', 'Mazda 3', NULL, 4, 2200.00, 8800.00, 1200.00, 0.00, 10000.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'PayPal', 'Paid', '2026-01-10 02:00:00', '2026-02-17 07:06:12', 'A28-33-333334', '2028-09-15', NULL, NULL, 'INV-20260110-0028', 'REC-20260110-0028', 'Gabriel', '2026-01-10 10:30:00', 'Full Payment', 10000.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(131, 'CAR-20260112-N5N5N5', 'Laura Thompson', 'laura.thompson@email.com', '+63 945 444 5555', 30, '2026-01-16', '2026-01-20', '15:30:00', '15:30:00', 'Solaire Resort & Casino', 'Solaire Resort & Casino', 'Electric', 'Nissan Leaf', NULL, 4, 3500.00, 14000.00, 1200.00, 0.00, 15200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-01-12 07:30:00', '2026-02-17 07:06:12', 'L29-44-444445', '2029-06-20', NULL, NULL, 'INV-20260112-0029', 'REC-20260112-0029', 'Gabriel', '2026-01-12 16:00:00', 'Downpayment', 15200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(132, 'CAR-20260114-O6O6O6', 'Patrick Garcia', 'patrick.garcia@email.com', '+63 946 555 6666', 44, '2026-01-18', '2026-01-22', '09:00:00', '09:00:00', 'LRT Buendia Station', 'LRT Buendia Station', 'Luxury', 'Audi A6', NULL, 4, 5200.00, 20800.00, 2400.00, 0.00, 23200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, 'Debit Card', 'Paid', '2026-01-14 01:00:00', '2026-02-17 07:06:12', 'P30-55-555556', '2027-12-31', NULL, NULL, 'INV-20260114-0030', 'REC-20260114-0030', 'Gabriel', '2026-01-14 09:30:00', 'Full Payment', 23200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(133, 'CAR-20260305-P7P7P7', 'Jennifer Martinez', 'jennifer.martinez@email.com', '+63 947 666 7777', 32, '2026-03-10', '2026-03-14', '10:00:00', '10:00:00', 'MNL - Manila Ninoy Aquino International Airport', 'MNL - Manila Ninoy Aquino International Airport', 'Sedan', 'Toyota Camry', NULL, 4, 2500.00, 10000.00, 1200.00, 0.00, 11200.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'GCash', 'Paid', '2026-03-05 02:00:00', '2026-02-17 07:07:31', 'J31-66-666667', '2028-08-25', NULL, NULL, 'INV-20260305-0031', 'REC-20260305-0031', 'Gabriel', '2026-03-05 10:30:00', 'Full Payment', 11200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(134, 'CAR-20260310-Q8Q8Q8', 'Timothy Robinson', 'timothy.robinson@email.com', '+63 948 777 8888', 37, '2026-03-15', '2026-03-19', '12:00:00', '12:00:00', 'Makati City Center', 'Makati City Center', 'SUV', 'Honda CR-V', NULL, 4, 3500.00, 14000.00, 1200.00, 0.00, 15200.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'Credit Card', 'Paid', '2026-03-10 04:00:00', '2026-02-17 07:07:29', 'T32-77-777778', '2029-05-10', NULL, NULL, 'INV-20260310-0032', 'REC-20260310-0032', 'Gabriel', '2026-03-10 12:30:00', 'Downpayment', 7600.00, 7600.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(135, 'CAR-20260315-R9R9R9', 'Samantha Clark', 'samantha.clark@email.com', '+63 949 888 9999', 29, '2026-03-20', '2026-03-24', '14:00:00', '14:00:00', 'Bonifacio Global City (BGC)', 'Bonifacio Global City (BGC)', 'Van', 'Toyota Hiace', NULL, 4, 4000.00, 16000.00, 1200.00, 0.00, 17200.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'PayPal', 'Paid', '2026-03-15 06:00:00', '2026-02-17 07:07:26', 'S33-88-888889', '2028-11-15', NULL, NULL, 'INV-20260315-0033', 'REC-20260315-0033', 'Gabriel', '2026-03-15 14:30:00', 'Full Payment', 17200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(136, 'CAR-20260320-S1S1S1', 'Gregory Lewis', 'gregory.lewis@email.com', '+63 950 999 0000', 40, '2026-03-25', '2026-03-29', '11:30:00', '11:30:00', 'Quezon City - Cubao', 'Quezon City - Cubao', 'Luxury', 'BMW 5 Series', NULL, 4, 5000.00, 20000.00, 2400.00, 0.00, 22400.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'GCash', 'Paid', '2026-03-20 03:30:00', '2026-02-17 07:07:24', 'G34-99-999990', '2027-10-20', NULL, NULL, 'INV-20260320-0034', 'REC-20260320-0034', 'Gabriel', '2026-03-20 12:00:00', 'Downpayment', 11200.00, 11200.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(137, 'CAR-20260325-T2T2T2', 'Kimberly Walker', 'kimberly.walker@email.com', '+63 951 000 1111', 26, '2026-03-28', '2026-04-01', '16:00:00', '16:00:00', 'Pasay City - Mall of Asia', 'Pasay City - Mall of Asia', 'Electric', 'Tesla Model 3 or Similar', NULL, 4, 3800.00, 15200.00, 1200.00, 0.00, 16400.00, NULL, 0.00, NULL, 0.00, 'Cancelled', NULL, 'Debit Card', 'Paid', '2026-03-25 08:00:00', '2026-02-17 07:07:20', 'K35-00-000001', '2029-07-30', NULL, NULL, 'INV-20260325-0035', 'REC-20260325-0035', 'Gabriel', '2026-03-25 16:30:00', 'Full Payment', 16400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(138, 'CAR-20260217-AC3C8C', 'test2', 'test@gmail.com', '09546131252', 21, '2026-03-28', '2026-03-31', '10:00:00', '10:00:00', 'MRT Taft Station', 'PITX - Parañaque Integrated Terminal Exchange', 'Compact', 'Honda Civic', 'http://localhost/NiceAdmin/assets/img/cars/honda-civic.jpg', 3, 3650.00, 10950.00, 900.00, 0.00, 11850.00, '', 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-02-17 07:09:46', '2026-02-17 07:24:34', 'A01-30-620434', '2028-06-25', '2026-02-17 15:24:34', 'uploads/payments/payment_138_1771312346.png', 'INV-20260217-0138', NULL, 'Gabriel', '2026-02-17 15:12:37', 'Downpayment', 11850.00, 0.00, 0.00, 38, 'Pending', NULL, 'Pending', NULL),
(139, 'CAR-20260217-FCA551', 'test3', 'Paolo@gmail.com', '09940213443', 21, '2026-03-28', '2026-03-30', '10:00:00', '10:00:00', 'MNL - Manila Ninoy Aquino International Airport', 'MNL - Manila Ninoy Aquino International Airport', 'Compact', 'Honda Civic', 'http://localhost/NiceAdmin/assets/img/cars/honda-civic.jpg', 2, 3650.00, 7300.00, 1800.00, 0.00, 9100.00, '', 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-02-17 07:27:11', '2026-02-17 07:28:31', 'P01-23-233432', '2026-10-21', '2026-02-17 15:28:31', 'uploads/payments/payment_139_1771313240.png', 'INV-20260217-0139', NULL, 'Gabriel', '2026-02-17 15:28:15', 'Full Payment', 9100.00, 0.00, 0.00, 38, 'Pending', NULL, 'Pending', NULL),
(140, 'CAR-20260217-88F8D0', 'p', 'p@gmail.com', '09619490469', 22, '2026-04-14', '2026-04-21', '10:00:00', '10:00:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Compact', 'Honda Civic', 'http://localhost/NiceAdmin/assets/img/cars/honda-civic.jpg', 7, 3650.00, 25550.00, 0.00, 1400.00, 26950.00, '', 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-02-17 07:52:24', '2026-02-17 07:53:58', 'A01-20-201303', '2030-07-03', '2026-02-17 15:53:58', 'uploads/payments/payment_140_1771314757.png', 'INV-20260217-0140', NULL, 'Gabriel', '2026-02-17 15:53:25', 'Downpayment', 13475.00, 13475.00, 0.00, 56, 'Picked Up', '2026-02-17 15:53:51', 'Ready', NULL),
(141, 'BK-JDC-001', 'Juan Dela Cruz', 'juan.delacruz@email.com', '09171234567', 0, '2025-01-10', '2025-01-13', '09:00:00', '18:00:00', 'Manila Office', 'Manila Office', 'Sedan', 'Toyota Corolla', 'uploads/cars/corolla.jpg', 3, 1500.00, 0.00, 300.00, 0.00, 4800.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-01-05 02:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 4800.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(142, 'BK-JDC-002', 'Juan Dela Cruz', 'juan.delacruz@email.com', '09171234567', 0, '2025-02-15', '2025-02-18', '10:00:00', '17:00:00', 'Quezon City Branch', 'Quezon City Branch', 'Sedan', 'Honda Civic', 'uploads/cars/civic.jpg', 3, 1800.00, 0.00, 300.00, 0.00, 5700.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-02-10 03:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 5700.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(143, 'BK-JDC-003', 'Juan Dela Cruz', 'juan.delacruz@email.com', '09171234567', 0, '2025-03-20', '2025-03-24', '08:00:00', '18:00:00', 'Makati Branch', 'Makati Branch', 'SUV', 'Mazda CX-5', 'uploads/cars/cx5.jpg', 4, 2500.00, 0.00, 400.00, 0.00, 10400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-03-15 01:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 10400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(144, 'BK-JDC-004', 'Juan Dela Cruz', 'juan.delacruz@email.com', '09171234567', 0, '2025-04-12', '2025-04-15', '09:00:00', '17:00:00', 'Manila Office', 'Manila Office', 'Sedan', 'Toyota Camry', 'uploads/cars/camry.jpg', 3, 2000.00, 0.00, 300.00, 0.00, 6300.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-04-08 02:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 6300.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(145, 'BK-JDC-005', 'Juan Dela Cruz', 'juan.delacruz@email.com', '09171234567', 0, '2025-05-18', '2025-05-21', '10:00:00', '18:00:00', 'Pasig Branch', 'Pasig Branch', 'Sedan', 'Nissan Altima', 'uploads/cars/altima.jpg', 3, 1700.00, 0.00, 300.00, 0.00, 5400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-05-14 03:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 5400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(146, 'BK-MS-001', 'Maria Santos', 'maria.santos@email.com', '09181234567', 0, '2025-01-08', '2025-01-12', '08:00:00', '17:00:00', 'Quezon City Branch', 'Quezon City Branch', 'Sedan', 'Hyundai Elantra', 'uploads/cars/elantra.jpg', 4, 1600.00, 0.00, 400.00, 0.00, 6800.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-01-03 01:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 6800.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(147, 'BK-MS-002', 'Maria Santos', 'maria.santos@email.com', '09181234567', 0, '2025-02-10', '2025-02-12', '09:00:00', '18:00:00', 'Manila Office', 'Manila Office', 'Sports', 'Ford Mustang', 'uploads/cars/mustang.jpg', 2, 3500.00, 0.00, 200.00, 0.00, 7200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-02-05 02:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 7200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(148, 'BK-MS-003', 'Maria Santos', 'maria.santos@email.com', '09181234567', 0, '2025-03-15', '2025-03-18', '10:00:00', '17:00:00', 'Makati Branch', 'Makati Branch', 'Luxury', 'BMW 3 Series', 'uploads/cars/bmw3.jpg', 3, 3000.00, 0.00, 300.00, 0.00, 9300.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-03-10 03:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 9300.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(149, 'BK-MS-004', 'Maria Santos', 'maria.santos@email.com', '09181234567', 0, '2025-04-20', '2025-04-23', '08:00:00', '18:00:00', 'Pasig Branch', 'Pasig Branch', 'Sedan', 'Chevrolet Malibu', 'uploads/cars/malibu.jpg', 3, 1900.00, 0.00, 300.00, 0.00, 6000.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-04-16 01:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 6000.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(150, 'BK-MS-005', 'Maria Santos', 'maria.santos@email.com', '09181234567', 0, '2025-05-25', '2025-05-28', '09:00:00', '17:00:00', 'Taguig Branch', 'Taguig Branch', 'Sedan', 'Volkswagen Jetta', 'uploads/cars/jetta.jpg', 3, 1800.00, 0.00, 300.00, 0.00, 5700.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-05-20 02:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 5700.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(151, 'BK-PR-001', 'Pedro Reyes', 'pedro.reyes@email.com', '09191234567', 0, '2025-01-12', '2025-01-16', '07:00:00', '18:00:00', 'Makati Branch', 'Makati Branch', 'SUV', 'Jeep Wrangler', 'uploads/cars/wrangler.jpg', 4, 2800.00, 0.00, 400.00, 0.00, 11600.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-01-07 00:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 11600.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(152, 'BK-PR-002', 'Pedro Reyes', 'pedro.reyes@email.com', '09191234567', 0, '2025-02-18', '2025-02-20', '10:00:00', '17:00:00', 'Manila Office', 'Manila Office', 'Electric', 'Tesla Model 3', 'uploads/cars/tesla3.jpg', 2, 4000.00, 0.00, 200.00, 0.00, 8200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-02-13 01:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 8200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(153, 'BK-PR-003', 'Pedro Reyes', 'pedro.reyes@email.com', '09191234567', 0, '2025-03-22', '2025-03-25', '09:00:00', '18:00:00', 'Quezon City Branch', 'Quezon City Branch', 'Luxury', 'Mercedes-Benz C-Class', 'uploads/cars/mercedesc.jpg', 3, 3500.00, 0.00, 300.00, 0.00, 10800.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-03-17 02:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 10800.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(154, 'BK-PR-004', 'Pedro Reyes', 'pedro.reyes@email.com', '09191234567', 0, '2025-04-14', '2025-04-17', '08:00:00', '17:00:00', 'Pasig Branch', 'Pasig Branch', 'Luxury', 'Audi A4', 'uploads/cars/audia4.jpg', 3, 3200.00, 0.00, 300.00, 0.00, 9900.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-04-10 01:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 9900.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(155, 'BK-PR-005', 'Pedro Reyes', 'pedro.reyes@email.com', '09191234567', 0, '2025-05-20', '2025-05-24', '10:00:00', '18:00:00', 'Taguig Branch', 'Taguig Branch', 'Sedan', 'Kia Optima', 'uploads/cars/optima.jpg', 4, 1700.00, 0.00, 400.00, 0.00, 7200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-05-16 03:00:00', '2026-02-17 08:17:38', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 7200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(156, 'BK-AG-001', 'Ana Garcia', 'ana.garcia@email.com', '09201234567', 0, '2025-01-14', '2025-01-18', '09:00:00', '17:00:00', 'Pasig Branch', 'Pasig Branch', 'Sedan', 'Toyota Corolla', 'uploads/cars/corolla.jpg', 4, 1500.00, 0.00, 400.00, 0.00, 6400.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-01-09 02:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 6400.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(157, 'BK-AG-002', 'Ana Garcia', 'ana.garcia@email.com', '09201234567', 0, '2025-02-12', '2025-02-15', '08:00:00', '18:00:00', 'Manila Office', 'Manila Office', 'Sedan', 'Honda Civic', 'uploads/cars/civic.jpg', 3, 1800.00, 0.00, 300.00, 0.00, 5700.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-02-08 01:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 5700.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(158, 'BK-AG-003', 'Ana Garcia', 'ana.garcia@email.com', '09201234567', 0, '2025-03-18', '2025-03-21', '10:00:00', '17:00:00', 'Quezon City Branch', 'Quezon City Branch', 'SUV', 'Mazda CX-5', 'uploads/cars/cx5.jpg', 3, 2500.00, 0.00, 300.00, 0.00, 7800.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-03-13 03:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 7800.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(159, 'BK-AG-004', 'Ana Garcia', 'ana.garcia@email.com', '09201234567', 0, '2025-04-16', '2025-04-20', '09:00:00', '18:00:00', 'Makati Branch', 'Makati Branch', 'Sedan', 'Nissan Altima', 'uploads/cars/altima.jpg', 4, 1700.00, 0.00, 400.00, 0.00, 7200.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-04-12 02:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 7200.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(160, 'BK-AG-005', 'Ana Garcia', 'ana.garcia@email.com', '09201234567', 0, '2025-05-22', '2025-05-25', '08:00:00', '17:00:00', 'Taguig Branch', 'Taguig Branch', 'Sedan', 'Hyundai Elantra', 'uploads/cars/elantra.jpg', 3, 1600.00, 0.00, 300.00, 0.00, 5100.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-05-18 01:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 5100.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(161, 'BK-RC-001', 'Ramon Cruz', 'ramon.cruz@email.com', '09211234567', 0, '2025-01-16', '2025-01-19', '10:00:00', '18:00:00', 'Taguig Branch', 'Taguig Branch', 'Sedan', 'Toyota Camry', 'uploads/cars/camry.jpg', 3, 2000.00, 0.00, 300.00, 0.00, 6300.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-01-11 03:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 6300.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(162, 'BK-RC-002', 'Ramon Cruz', 'ramon.cruz@email.com', '09211234567', 0, '2025-02-20', '2025-02-23', '09:00:00', '17:00:00', 'Makati Branch', 'Makati Branch', 'Luxury', 'BMW 3 Series', 'uploads/cars/bmw3.jpg', 3, 3000.00, 0.00, 300.00, 0.00, 9300.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-02-15 02:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 9300.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(163, 'BK-RC-003', 'Ramon Cruz', 'ramon.cruz@email.com', '09211234567', 0, '2025-03-24', '2025-03-27', '08:00:00', '18:00:00', 'Manila Office', 'Manila Office', 'SUV', 'Jeep Wrangler', 'uploads/cars/wrangler.jpg', 3, 2800.00, 0.00, 300.00, 0.00, 8700.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-03-19 01:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 8700.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(164, 'BK-RC-004', 'Ramon Cruz', 'ramon.cruz@email.com', '09211234567', 0, '2025-04-18', '2025-04-21', '10:00:00', '17:00:00', 'Quezon City Branch', 'Quezon City Branch', 'Sedan', 'Chevrolet Malibu', 'uploads/cars/malibu.jpg', 3, 1900.00, 0.00, 300.00, 0.00, 6000.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-04-14 03:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 6000.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(165, 'BK-RC-005', 'Ramon Cruz', 'ramon.cruz@email.com', '09211234567', 0, '2025-05-26', '2025-05-29', '09:00:00', '18:00:00', 'Pasig Branch', 'Pasig Branch', 'Sedan', 'Volkswagen Jetta', 'uploads/cars/jetta.jpg', 3, 1800.00, 0.00, 300.00, 0.00, 5700.00, NULL, 0.00, NULL, 0.00, 'Completed', NULL, NULL, 'Paid', '2025-05-22 02:00:00', '2026-02-17 08:17:39', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 5700.00, 0.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(166, 'CAR-20260218-36D683', 'POPOP', 'Paolo@gmail.com', '09619490469', 21, '2026-02-18', '2026-02-19', '10:00:00', '10:00:00', 'Pasay City - Mall of Asia', 'Pasay City - Mall of Asia', 'Compact', 'Mazda 3', 'http://localhost/NiceAdmin/assets/img/cars/mazda-3.jpg', 1, 3388.00, 3388.00, 0.00, 500.00, 3888.00, '', 0.00, NULL, 0.00, 'Pending', NULL, 'Pay at Pickup', 'Pending', '2026-02-18 01:09:39', '2026-02-18 01:09:39', 'A01-02-030304', '2026-03-27', NULL, NULL, NULL, NULL, NULL, NULL, 'Full Payment', 0.00, 3888.00, 0.00, 0, 'Pending', NULL, 'Pending', NULL),
(167, 'CAR-20260218-6AE33B', 'Last', 'Last@gmail.com', '09619490469', 21, '2026-02-18', '2026-02-28', '10:00:00', '10:00:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Economy', 'Kia Rio or Similar', 'http://localhost/NiceAdmin/assets/img/cars/kia-rio.jpg', 10, 2850.00, 28500.00, 9000.00, 7000.00, 44500.00, '', 0.00, NULL, 0.00, 'Completed', NULL, 'Pay at Pickup', 'Paid', '2026-02-18 01:22:14', '2026-02-18 01:28:25', 'P01-30-620434', '2031-11-19', '2026-02-18 09:28:25', 'uploads/payments/payment_167_1771377794.png', 'INV-20260218-0167', 'REC-20260218-0167', 'Gabriel', '2026-02-18 09:23:48', 'Full Payment', 22250.00, 22250.00, 33750.00, 1, 'Picked Up', '2026-02-18 09:25:45', 'Ready', NULL),
(168, 'CAR-20260218-65474A', 'Paolo3', 'Paolo@gmail.com', '096555214545', 22, '2026-02-18', '2026-02-21', '10:00:00', '10:00:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Shangri-La Hotel', 'Compact', 'Honda Civic or Similar', 'http://localhost/NiceAdmin/assets/img/cars/honda-civic.jpg', 3, 3650.00, 10950.00, 2700.00, 0.00, 13650.00, '', 0.00, NULL, 0.00, 'Completed', NULL, 'GCash', 'Paid', '2026-02-18 01:29:42', '2026-02-18 01:32:00', 'A01-02-024303', '2031-10-22', '2026-02-18 09:32:00', 'uploads/payments/payment_168_1771378194.png', 'INV-20260218-0168', NULL, 'Gabriel', '2026-02-18 09:30:52', 'Downpayment', 6825.00, 6825.00, 9100.00, 1, 'Picked Up', '2026-02-18 09:31:58', 'Ready', NULL),
(169, 'CAR-20260218-B71464', 'POPOP', 'Last@gmail.com', '09619490469', 22, '2026-02-18', '2026-02-19', '10:00:00', '10:00:00', 'MRT Taft Station', 'MRT Ayala Station', 'Economy', 'Kia Rio or Similar', 'http://localhost/NiceAdmin/assets/img/cars/kia-rio.jpg', 1, 2850.00, 2850.00, 900.00, 0.00, 3750.00, '', 0.00, NULL, 0.00, 'Active', NULL, 'Pay at Pickup', 'Paid', '2026-02-18 01:34:19', '2026-02-18 01:37:23', 'P01-30-620434', '2044-06-07', NULL, 'uploads/payments/payment_169_1771378519.png', 'INV-20260218-0169', NULL, 'Gabriel', '2026-02-18 09:35:42', 'Downpayment', 1875.00, 1875.00, 0.00, 0, 'Picked Up', '2026-02-18 09:37:23', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `car_rental_bookings`
--

CREATE TABLE `car_rental_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `car_type` varchar(50) NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `daily_rate` decimal(10,2) DEFAULT 0.00,
  `rental_days` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `agent_id` varchar(50) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `booking_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `actual_return_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_reviews`
--

CREATE TABLE `car_reviews` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_reviews`
--

INSERT INTO `car_reviews` (`id`, `booking_id`, `car_model`, `customer_email`, `customer_name`, `rating`, `review_text`, `created_at`, `status`) VALUES
(19, 'CAR-20260115-A1B2C3', 'Toyota Camry', 'john.doe@email.com', 'John Doe', 4, 'Solid choice for daily driving. Good fuel efficiency and comfortable interior. No complaints!', '2026-01-25 16:00:00', 'Approved'),
(20, 'CAR-20260118-J1K2L3', 'Toyota Vios', 'ana.reyes@email.com', 'Ana Reyes', 4, 'Great experience overall. The car handled well and was perfect for city driving. Only minor issue was the AC took a while to cool down.', '2026-01-27 16:00:00', 'Approved'),
(21, 'CAR-20260121-S1T2U3', 'Honda Accord', 'miguel.lopez@email.com', 'Miguel Lopez', 3, 'Luxury at its finest. Comfortable seats, great sound system, and smooth ride. A bit pricey but worth it for special occasions.', '2026-01-28 16:00:00', 'Approved'),
(22, 'CAR-20260124-B1C2D3', 'Mazda 3', 'elena.rivera@email.com', 'Elena Rivera', 5, 'Reliable and comfortable sedan. Great for family trips. Good fuel economy and plenty of trunk space.', '2026-02-02 16:00:00', 'Approved'),
(23, 'CAR-20260127-K1L2M3', 'Ford Everest', 'james.lee@email.com', 'James Lee', 5, 'Excellent SUV! Perfect size for a small family. Great visibility and very comfortable on long drives.', '2026-02-03 16:00:00', 'Approved'),
(24, 'CAR-20251105-A1A1A1', 'Toyota Camry', 'mark.johnson@email.com', 'Mark Johnson', 4, 'Solid choice for daily driving. Good fuel efficiency and comfortable interior. No complaints!', '2025-11-15 16:00:00', 'Approved'),
(25, 'CAR-20251112-B2B2B2', 'Honda CR-V', 'sarah.williams@email.com', 'Sarah Williams', 3, 'Surprisingly great car! Modern features, smooth ride, and excellent customer service. Exceeded my expectations!', '2025-11-22 16:00:00', 'Approved'),
(26, 'CAR-20251118-C3C3C3', 'Toyota Vios', 'kevin.brown@email.com', 'Kevin Brown', 5, 'Excellent car! Very fuel efficient and comfortable for long drives. The pickup process was smooth and the car was in pristine condition.', '2025-11-28 16:00:00', 'Approved'),
(27, 'CAR-20251122-D4D4D4', 'Toyota Hiace', 'amanda.davis@email.com', 'Amanda Davis', 4, 'Great experience overall. The car handled well and was perfect for city driving. Only minor issue was the AC took a while to cool down.', '2025-12-02 16:00:00', 'Approved'),
(28, 'CAR-20251126-E5E5E5', 'BMW 5 Series', 'ryan.miller@email.com', 'Ryan Miller', 5, 'Amazing sports car! The power and handling are incredible. Perfect for a weekend getaway. Highly recommend!', '2025-12-03 16:00:00', 'Approved'),
(29, 'CAR-20251203-F6F6F6', 'Honda Accord', 'jessica.moore@email.com', 'Jessica Moore', 4, 'Love the electric experience! Silent, smooth, and eco-friendly. The autopilot feature made highway driving a breeze.', '2025-12-12 16:00:00', 'Approved'),
(30, 'CAR-20251210-G7G7G7', 'Mitsubishi Montero Sport', 'brian.taylor@email.com', 'Brian Taylor', 3, 'Luxury at its finest. Comfortable seats, great sound system, and smooth ride. A bit pricey but worth it for special occasions.', '2025-12-19 16:00:00', 'Approved'),
(31, 'CAR-20251215-H8H8H8', 'Tesla Model 3 or Similar', 'nicole.anderson@email.com', 'Nicole Anderson', 5, 'Absolutely stunning car! The interior is luxurious and the drive is incredibly smooth. Staff was very professional.', '2025-12-24 16:00:00', 'Approved'),
(32, 'CAR-20251220-I9I9I9', 'Nissan Urvan', 'chris.thomas@email.com', 'Christopher Thomas', 4, 'Perfect for off-road adventures! Took it to the mountains and it handled everything like a champ. Very spacious too.', '2025-12-29 16:00:00', 'Approved'),
(33, 'CAR-20251228-J1J1J1', 'Mercedes-Benz E-Class', 'michelle.jackson@email.com', 'Michelle Jackson', 5, 'Reliable and comfortable sedan. Great for family trips. Good fuel economy and plenty of trunk space.', '2026-01-03 16:00:00', 'Approved'),
(34, 'CAR-20260218-6AE33B', 'Kia Rio or Similar', 'last@gmail.com', 'Last', 5, '', '2026-02-18 01:34:32', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `car_sales`
--

CREATE TABLE `car_sales` (
  `id` int(11) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `discount_percentage` int(11) NOT NULL,
  `sale_start` date NOT NULL,
  `sale_end` date NOT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_sales`
--

INSERT INTO `car_sales` (`id`, `car_model`, `original_price`, `sale_price`, `discount_percentage`, `sale_start`, `sale_end`, `status`, `created_at`) VALUES
(5, 'Ford Mustang or Similar', 5108.00, 3831.00, 25, '2026-02-09', '2026-02-10', 'Active', '2026-02-09 04:52:25'),
(6, 'Volkswagen Jetta ', 3950.00, 3357.50, 15, '2026-02-09', '2026-02-21', 'Active', '2026-02-09 06:04:28'),
(7, 'BMW X7 or Similar', 5878.00, 4408.50, 25, '2026-02-09', '2026-02-11', 'Active', '2026-02-09 07:22:14'),
(8, 'Honda Civic', 3650.00, 2737.50, 25, '2026-02-09', '2026-02-11', 'Active', '2026-02-09 07:22:48'),
(9, 'Hyundai Ioniq 5 ', 4500.00, 3825.00, 15, '2026-02-09', '2026-02-19', 'Active', '2026-02-09 07:23:07'),
(10, 'Mitsubishi Mirage', 2650.00, 2040.50, 23, '2026-02-13', '2026-02-28', 'Active', '2026-02-13 03:02:59'),
(11, 'Mazda 3', 3850.00, 3388.00, 12, '2026-02-13', '2026-03-12', 'Active', '2026-02-13 03:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_id`, `username`, `password`, `full_name`, `email`, `phone`, `address`, `profile_picture`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'CUST8350', 'paolo123', '$2y$10$l2vwH.YGmjREiLtltR7aAu1HgLNn5ijFlEGa0HviScdBeVzfw257m', 'Gabriel Paolo Madridano', 'paolo@gmail.com', '09940213443', 'Km6 Upper Balulang', 'uploads/profiles/profile_CUST8350_1770611917.png', 'Active', '2026-02-18 09:31:54', '2026-02-09 04:31:55', '2026-02-18 01:31:54'),
(2, 'CUST3509', 'gabriel@gmail.com', '$2y$10$s60Km.iRTe1F6z31siNy7ubFCWV6EJfFRJMSqYuA1PzyQIiafq3Fe', 'Gabriel', 'g@gmail.com', '09619490469', NULL, NULL, 'Active', '2026-02-17 14:46:31', '2026-02-17 06:36:07', '2026-02-17 06:46:31'),
(18, 'CUST9227', 'test1', '$2y$10$7gqWt0/CQMdQiKP.9tIij.1EHObXG5xlQYPGoq43uJ3J6kcwxEZdC', 'Gabriel Paolo Madridano', 'test@gmail.com', '09232424222', NULL, NULL, 'Active', '2026-02-18 09:09:52', '2026-02-17 07:08:23', '2026-02-18 01:09:52'),
(19, 'CUST4541', 'test3', '$2y$10$ILZT4K46l2dbGxvHqpREneZG0t.Kab5UAqvs9wRdkNOfr0eV.Feuq', 'test3', 'test3@gmail.com', '09940213443', NULL, NULL, 'Active', '2026-02-17 15:50:44', '2026-02-17 07:50:28', '2026-02-17 07:50:44'),
(20, 'CUST3834', 'paolo1', '$2y$10$HzzKb9wPmXOpdLnCxKQob.Cm2gmmNsgWTlbZvwk65g4jvt8x7DGKS', 'gg', 'p@gmail.com', '09940213443', NULL, NULL, 'Active', '2026-02-17 15:54:48', '2026-02-17 07:51:25', '2026-02-17 07:54:48'),
(26, 'CUST-JDC', 'jdelacruz', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'Juan Dela Cruz', 'juan.delacruz@email.com', '09171234567', 'Manila, Philippines', NULL, 'Active', NULL, '2026-02-17 08:17:38', '2026-02-17 08:17:38'),
(27, 'CUST-MS', 'msantos', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'Maria Santos', 'maria.santos@email.com', '09181234567', 'Quezon City, Philippines', NULL, 'Active', NULL, '2026-02-17 08:17:38', '2026-02-17 08:17:38'),
(28, 'CUST-PR', 'preyes', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'Pedro Reyes', 'pedro.reyes@email.com', '09191234567', 'Makati, Philippines', NULL, 'Active', NULL, '2026-02-17 08:17:38', '2026-02-17 08:17:38'),
(29, 'CUST-AG', 'agarcia', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'Ana Garcia', 'ana.garcia@email.com', '09201234567', 'Pasig, Philippines', NULL, 'Active', NULL, '2026-02-17 08:17:38', '2026-02-17 08:17:38'),
(30, 'CUST-RC', 'rcruz', '$2y$10$abcdefghijklmnopqrstuvwxyz123456', 'Ramon Cruz', 'ramon.cruz@email.com', '09211234567', 'Taguig, Philippines', NULL, 'Active', NULL, '2026-02-17 08:17:38', '2026-02-17 08:17:38'),
(31, 'CUST1313', 'last123', '$2y$10$v6IuXWqXWvGNQ2kAaRY7J.PJEn2vN.8mdzDL7WEmZqA.JQTIDEjVy', 'last', 'last@gmail.com', '09619490469', NULL, NULL, 'Active', '2026-02-18 09:37:19', '2026-02-18 01:20:39', '2026-02-18 01:37:19');

-- --------------------------------------------------------

--
-- Table structure for table `customer_documents`
--

CREATE TABLE `customer_documents` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_by` varchar(100) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_status` varchar(20) DEFAULT 'Pending',
  `verification_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_documents`
--

INSERT INTO `customer_documents` (`id`, `booking_id`, `customer_email`, `document_type`, `file_path`, `uploaded_at`, `verified_by`, `verified_at`, `verification_status`, `verification_notes`) VALUES
(15, 'CAR-20260217-AC3C8C', 'test@gmail.com', 'License_Front', 'uploads/documents/License_Front_CAR-20260217-AC3C8C_1771312280.png', '2026-02-17 07:11:20', NULL, NULL, 'Pending', NULL),
(16, 'CAR-20260217-AC3C8C', 'test@gmail.com', 'License_Back', 'uploads/documents/License_Back_CAR-20260217-AC3C8C_1771312280.png', '2026-02-17 07:11:20', NULL, NULL, 'Pending', NULL),
(17, 'CAR-20260217-AC3C8C', 'test@gmail.com', 'Valid_ID', 'uploads/documents/Valid_ID_CAR-20260217-AC3C8C_1771312287.png', '2026-02-17 07:11:27', NULL, NULL, 'Pending', NULL),
(18, 'CAR-20260217-AC3C8C', 'test@gmail.com', 'Proof_of_Address', 'uploads/documents/Proof_of_Address_CAR-20260217-AC3C8C_1771312295.png', '2026-02-17 07:11:35', NULL, NULL, 'Pending', NULL),
(19, 'CAR-20260217-FCA551', 'paolo@gmail.com', 'Valid_ID', 'uploads/documents/Valid_ID_CAR-20260217-FCA551_1771313255.png', '2026-02-17 07:27:35', NULL, NULL, 'Pending', NULL),
(20, 'CAR-20260217-FCA551', 'paolo@gmail.com', 'License_Front', 'uploads/documents/License_Front_CAR-20260217-FCA551_1771313273.png', '2026-02-17 07:27:53', NULL, NULL, 'Pending', NULL),
(21, 'CAR-20260217-FCA551', 'paolo@gmail.com', 'License_Back', 'uploads/documents/License_Back_CAR-20260217-FCA551_1771313273.png', '2026-02-17 07:27:53', NULL, NULL, 'Pending', NULL),
(22, 'CAR-20260217-FCA551', 'paolo@gmail.com', 'Proof_of_Address', 'uploads/documents/Proof_of_Address_CAR-20260217-FCA551_1771313280.png', '2026-02-17 07:28:00', NULL, NULL, 'Pending', NULL),
(23, 'CAR-20260217-88F8D0', 'p@gmail.com', 'License_Front', 'uploads/documents/License_Front_CAR-20260217-88F8D0_1771314770.png', '2026-02-17 07:52:50', NULL, NULL, 'Pending', NULL),
(24, 'CAR-20260217-88F8D0', 'p@gmail.com', 'License_Back', 'uploads/documents/License_Back_CAR-20260217-88F8D0_1771314770.png', '2026-02-17 07:52:50', NULL, NULL, 'Pending', NULL),
(25, 'CAR-20260217-88F8D0', 'p@gmail.com', 'Valid_ID', 'uploads/documents/Valid_ID_CAR-20260217-88F8D0_1771314778.png', '2026-02-17 07:52:58', 'staff@gmail.com', '2026-02-17 07:54:34', 'Approved', ''),
(26, 'CAR-20260217-88F8D0', 'p@gmail.com', 'Proof_of_Address', 'uploads/documents/Proof_of_Address_CAR-20260217-88F8D0_1771314785.png', '2026-02-17 07:53:05', 'staff@gmail.com', '2026-02-17 07:54:30', 'Approved', ''),
(27, 'CAR-20260217-88F8D0', 'p@gmail.com', 'Contract', 'uploads/contracts/CONTRACT_CAR-20260217-88F8D0.html', '2026-02-17 07:54:36', NULL, NULL, 'Approved', NULL),
(28, 'CAR-20260217-88F8D0', 'p@gmail.com', 'Contract', 'uploads/contracts/CONTRACT_CAR-20260217-88F8D0.html', '2026-02-17 07:54:39', NULL, NULL, 'Approved', NULL),
(29, 'CAR-20260218-6AE33B', 'last@gmail.com', 'Valid_ID', 'uploads/documents/Valid_ID_CAR-20260218-6AE33B_1771377754.png', '2026-02-18 01:22:34', NULL, NULL, 'Pending', NULL),
(30, 'CAR-20260218-6AE33B', 'last@gmail.com', 'Proof_of_Address', 'uploads/documents/Proof_of_Address_CAR-20260218-6AE33B_1771377762.png', '2026-02-18 01:22:42', NULL, NULL, 'Pending', NULL),
(31, 'CAR-20260218-6AE33B', 'last@gmail.com', 'License_Front', 'uploads/documents/License_Front_CAR-20260218-6AE33B_1771377775.png', '2026-02-18 01:22:55', NULL, NULL, 'Pending', NULL),
(32, 'CAR-20260218-6AE33B', 'last@gmail.com', 'License_Back', 'uploads/documents/License_Back_CAR-20260218-6AE33B_1771377775.png', '2026-02-18 01:22:55', NULL, NULL, 'Pending', NULL),
(33, 'CAR-20260218-65474A', 'paolo@gmail.com', 'Valid_ID', 'uploads/documents/Valid_ID_CAR-20260218-65474A_1771378205.png', '2026-02-18 01:30:05', NULL, NULL, 'Pending', NULL),
(34, 'CAR-20260218-65474A', 'paolo@gmail.com', 'Proof_of_Address', 'uploads/documents/Proof_of_Address_CAR-20260218-65474A_1771378215.png', '2026-02-18 01:30:15', NULL, NULL, 'Pending', NULL),
(35, 'CAR-20260218-65474A', 'paolo@gmail.com', 'License_Front', 'uploads/documents/License_Front_CAR-20260218-65474A_1771378231.png', '2026-02-18 01:30:31', NULL, NULL, 'Pending', NULL),
(36, 'CAR-20260218-65474A', 'paolo@gmail.com', 'License_Back', 'uploads/documents/License_Back_CAR-20260218-65474A_1771378231.png', '2026-02-18 01:30:31', NULL, NULL, 'Pending', NULL),
(37, 'CAR-20260218-B71464', 'last@gmail.com', 'Valid_ID', 'uploads/documents/Valid_ID_CAR-20260218-B71464_1771378486.png', '2026-02-18 01:34:46', NULL, NULL, 'Pending', NULL),
(38, 'CAR-20260218-B71464', 'last@gmail.com', 'Proof_of_Address', 'uploads/documents/Proof_of_Address_CAR-20260218-B71464_1771378495.png', '2026-02-18 01:34:55', NULL, NULL, 'Pending', NULL),
(39, 'CAR-20260218-B71464', 'last@gmail.com', 'License_Front', 'uploads/documents/License_Front_CAR-20260218-B71464_1771378508.png', '2026-02-18 01:35:08', NULL, NULL, 'Pending', NULL),
(40, 'CAR-20260218-B71464', 'last@gmail.com', 'License_Back', 'uploads/documents/License_Back_CAR-20260218-B71464_1771378508.png', '2026-02-18 01:35:08', NULL, NULL, 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `favorite_cars`
--

CREATE TABLE `favorite_cars` (
  `id` int(11) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `car_model` varchar(255) NOT NULL,
  `car_type` varchar(100) DEFAULT NULL,
  `car_image` varchar(500) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorite_cars`
--

INSERT INTO `favorite_cars` (`id`, `customer_email`, `car_model`, `car_type`, `car_image`, `daily_rate`, `created_at`) VALUES
(1, 'p@gmail.com', 'Honda Civic or Similar', 'Compact', 'assets/img/cars/honda-civic.jpg', 0.00, '2026-02-17 07:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `booking_id` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) DEFAULT 'general' COMMENT 'booking, payment, document, message, alert',
  `priority` varchar(20) DEFAULT 'normal' COMMENT 'critical, important, normal',
  `icon` varchar(50) DEFAULT 'bell' COMMENT 'Icon identifier',
  `action_url` varchar(255) DEFAULT NULL COMMENT 'URL for action button',
  `action_label` varchar(50) DEFAULT NULL COMMENT 'Label for action button',
  `expires_at` datetime DEFAULT NULL COMMENT 'Auto-dismiss after this date',
  `dismissed_at` datetime DEFAULT NULL COMMENT 'When user dismissed notification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_type`, `user_id`, `title`, `message`, `booking_id`, `is_read`, `created_at`, `category`, `priority`, `icon`, `action_url`, `action_label`, `expires_at`, `dismissed_at`) VALUES
(1, 'admin', NULL, 'New Car Rental Booking', 'New booking from Paolo for Honda Civic or Similar', 'CAR-20260209-EAD63F', 1, '2026-02-09 05:53:50', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(2, 'customer', 'pmadridano@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-EAD63F has been Confirmed by admin. Note: Your car is ready', 'CAR-20260209-EAD63F', 0, '2026-02-09 05:54:20', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(3, 'admin', NULL, 'New Car Rental Booking', 'New booking from Gabrie for Ford Mustang or Similar - Pending Review', 'CAR-20260209-B5641C', 1, '2026-02-09 05:58:03', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(4, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-B5641C has been submitted and is pending admin review.', 'CAR-20260209-B5641C', 1, '2026-02-09 05:58:03', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(5, 'customer', 'Paolo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-B5641C has been Confirmed by admin. Note: ok', 'CAR-20260209-B5641C', 1, '2026-02-09 05:58:29', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(6, 'admin', NULL, 'Message from Customer: Gabriel Paolo Madridano', 'OK Sir - From: paolo@gmail.com', NULL, 1, '2026-02-09 06:03:04', 'message', 'normal', 'bell', NULL, NULL, NULL, NULL),
(7, 'admin', NULL, 'New Car Rental Booking', 'New booking from Madridano for BMW X7 or Similar - Pending Review', 'CAR-20260209-5EBDB0', 1, '2026-02-09 06:25:09', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(8, 'customer', 'Madridano@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-5EBDB0 has been submitted and is pending admin review.', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:25:09', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(9, 'customer', 'Madridano@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-5EBDB0 has been Confirmed by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:25:43', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(10, 'customer', 'Madridano@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-5EBDB0 has been Confirmed by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:27:52', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(11, 'customer', 'Madridano@gmail.com', 'Booking Cancelled', 'Your booking CAR-20260209-5EBDB0 has been Cancelled by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:27:57', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(12, 'customer', 'paolo@gmail.com', 'Message from Admin', 'rer', NULL, 1, '2026-02-09 06:29:41', 'message', 'normal', 'bell', NULL, NULL, NULL, NULL),
(13, 'customer', 'Madridano@gmail.com', 'Booking Active', 'Your booking CAR-20260209-5EBDB0 has been Active by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:29:45', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(14, 'customer', 'Madridano@gmail.com', 'Booking Completed', 'Your booking CAR-20260209-5EBDB0 has been Completed by admin. Note: 45', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:31:06', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(15, 'admin', NULL, 'New Car Rental Booking', 'New booking from Madridano Gabriel for Honda Civic - Pending Review', 'CAR-20260209-318224', 1, '2026-02-09 06:32:03', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(16, 'customer', 'Pa2lo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-318224 has been submitted and is pending admin review.', 'CAR-20260209-318224', 0, '2026-02-09 06:32:03', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(17, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:32:18', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(18, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:32:49', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(19, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:32:59', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(20, 'customer', 'Pa2lo@gmail.com', 'Booking Completed', 'Your booking CAR-20260209-318224 has been Completed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:35:29', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(21, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:36:43', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(22, 'admin', NULL, 'New Car Rental Booking', 'New booking from POPOP for Mazda 3 - Pending Review', 'CAR-20260209-992441', 1, '2026-02-09 06:38:17', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(23, 'customer', 'paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-992441 has been submitted and is pending admin review.', 'CAR-20260209-992441', 1, '2026-02-09 06:38:17', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(24, 'customer', 'paolo@gmail.com', 'Booking Completed', 'Your booking CAR-20260209-992441 has been Completed by admin. Note: qweqw', 'CAR-20260209-992441', 1, '2026-02-09 06:38:30', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(25, 'admin', NULL, 'Message from Customer: Gabriel Paolo Madridano', 'r - From: paolo@gmail.com', NULL, 1, '2026-02-13 03:17:43', 'message', 'normal', 'bell', NULL, NULL, NULL, NULL),
(26, 'admin', NULL, 'New Car Rental Booking', 'New booking from Paolo for Mitsubishi Mirage - Pending Review', 'CAR-20260217-80EC8B', 1, '2026-02-17 03:23:52', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(27, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-80EC8B has been submitted and is pending admin review.', 'CAR-20260217-80EC8B', 1, '2026-02-17 03:23:52', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(28, 'customer', 'Paolo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260217-80EC8B has been Confirmed by admin. Note: retert', 'CAR-20260217-80EC8B', 1, '2026-02-17 03:24:46', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(29, 'customer', 'pmadridano@gmail.com', 'Return Reminder', 'Reminder: Your rental for Kia Rio or Similar is ending on 0000-00-00. Please return the car on time.', 'CAR-20260107-457060', 0, '2026-02-17 03:26:44', 'general', 'important', 'bell', NULL, NULL, NULL, NULL),
(30, 'customer', 'pmadridano@gmail.com', 'Return Reminder', 'Reminder: Your rental for Kia Rio or Similar is ending on 0000-00-00. Please return the car on time.', 'CAR-20260107-457060', 0, '2026-02-17 03:28:22', 'general', 'important', 'bell', NULL, NULL, NULL, NULL),
(31, 'customer', 'Paolo@gmail.com', 'Return Reminder', 'Reminder: Your rental for Mitsubishi Mirage is ending on 2026-02-18. Please return the car on time.', 'CAR-20260217-80EC8B', 1, '2026-02-17 03:29:02', 'general', 'important', 'bell', NULL, NULL, NULL, NULL),
(32, 'customer', 'Paolo@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0029', 'CAR-20260217-80EC8B', 1, '2026-02-17 03:42:41', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(33, 'customer', 'Paolo@gmail.com', 'Receipt Ready', 'Your receipt REC-20260217-0029 is ready for download.', 'CAR-20260217-80EC8B', 1, '2026-02-17 03:42:42', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(34, 'admin', NULL, 'Message from Customer: Gabriel', 'w - From: ', NULL, 1, '2026-02-17 03:50:11', 'message', 'normal', 'bell', NULL, NULL, NULL, NULL),
(35, 'staff', 'all', 'New Review Submitted', 'Customer submitted a 5-star review for Mitsubishi Mirage', 'CAR-20260217-80EC8B', 1, '2026-02-17 04:12:15', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(36, 'admin', 'all', 'New Review Submitted', 'Customer submitted a 5-star review for Mitsubishi Mirage', 'CAR-20260217-80EC8B', 1, '2026-02-17 04:12:15', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(37, 'admin', NULL, 'New Car Rental Booking', 'New booking from Gabrie for Mazda 3 - Pending Review', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:53:01', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(38, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-D46BA7 has been submitted and is pending admin review.', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:53:01', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(39, 'customer', 'Paolo@gmail.com', 'Documents Required', 'Please submit the following documents: Driver\'s License (Front), Driver\'s License (Back), Valid ID, Proof of Address. Upload at: My Profile > Documents', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:58:26', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(40, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-D46BA7', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:59:03', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(41, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-D46BA7', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:59:03', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(42, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-D46BA7', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:59:12', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(43, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-D46BA7', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:59:12', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(44, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-D46BA7', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:59:21', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(45, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-D46BA7', 'CAR-20260217-D46BA7', 1, '2026-02-17 04:59:21', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(46, 'customer', 'Paolo@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-D46BA7 has been approved!', 'CAR-20260217-D46BA7', 1, '2026-02-17 05:02:25', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(47, 'admin', NULL, 'New Car Rental Booking', 'New booking from POPOP for Honda Civic - Pending Review', 'CAR-20260217-BA7F01', 1, '2026-02-17 05:06:51', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(48, 'staff', NULL, 'New Car Rental Booking', 'New booking from POPOP for Honda Civic - Review documents and approve', 'CAR-20260217-BA7F01', 1, '2026-02-17 05:06:51', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(49, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-BA7F01 has been submitted and is pending admin review.', 'CAR-20260217-BA7F01', 1, '2026-02-17 05:06:51', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(50, 'admin', NULL, 'New Car Rental Booking', 'New booking from POPOP for Honda Civic or Similar - Pending Review', 'CAR-20260217-CB9BFC', 1, '2026-02-17 05:09:48', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(51, 'staff', NULL, 'New Car Rental Booking', 'New booking from POPOP for Honda Civic or Similar - Review documents and approve', 'CAR-20260217-CB9BFC', 1, '2026-02-17 05:09:48', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(52, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-CB9BFC has been submitted and is pending admin review.', 'CAR-20260217-CB9BFC', 1, '2026-02-17 05:09:48', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(53, 'customer', 'Paolo@gmail.com', 'Documents Required', 'Please submit the following documents: Driver\'s License (Front), Driver\'s License (Back), Valid ID, Proof of Address. Upload at: My Profile > Documents', 'CAR-20260217-CB9BFC', 1, '2026-02-17 05:16:28', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(54, 'customer', 'Paolo@gmail.com', 'Documents Required', 'Please submit the following documents: Driver\'s License (Front), Driver\'s License (Back), Valid ID, Proof of Address. Upload at: My Profile > Documents', 'CAR-20260217-CB9BFC', 1, '2026-02-17 05:17:04', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(55, 'customer', 'Paolo@gmail.com', 'Booking Rejected', 'Your booking CAR-20260217-CB9BFC has been rejected. Reason: dqw', 'CAR-20260217-CB9BFC', 1, '2026-02-17 05:18:07', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(56, 'admin', NULL, 'New Car Rental Booking', 'New booking from Gabriel Paolo Madridano for Mazda 3 - Pending Review', 'CAR-20260217-860682', 1, '2026-02-17 05:21:12', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(57, 'staff', NULL, 'New Car Rental Booking', 'New booking from Gabriel Paolo Madridano for Mazda 3 - Review documents and approve', 'CAR-20260217-860682', 1, '2026-02-17 05:21:12', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(58, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-860682 has been submitted and is pending admin review.', 'CAR-20260217-860682', 1, '2026-02-17 05:21:12', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(59, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-860682', 'CAR-20260217-860682', 1, '2026-02-17 05:21:48', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(60, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-860682', 'CAR-20260217-860682', 1, '2026-02-17 05:21:48', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(61, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-860682', 'CAR-20260217-860682', 1, '2026-02-17 05:21:59', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(62, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-860682', 'CAR-20260217-860682', 1, '2026-02-17 05:21:59', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(63, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-860682', 'CAR-20260217-860682', 1, '2026-02-17 05:22:15', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(64, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-860682', 'CAR-20260217-860682', 1, '2026-02-17 05:22:15', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(65, 'customer', 'Paolo@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-BA7F01 has been approved!', 'CAR-20260217-BA7F01', 1, '2026-02-17 05:22:53', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(66, 'customer', 'Paolo@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-860682 has been approved!', 'CAR-20260217-860682', 1, '2026-02-17 05:25:46', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(67, 'customer', 'paolo@gmail.com', 'Document Verification', 'Your Proof_of_Address has been Approved.', 'CAR-20260217-860682', 1, '2026-02-17 05:26:25', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(68, 'customer', 'paolo@gmail.com', 'Document Verification', 'Your Valid_ID has been Approved.', 'CAR-20260217-860682', 1, '2026-02-17 05:26:27', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(69, 'customer', 'paolo@gmail.com', 'Document Verification', 'Your License_Front has been Approved.', 'CAR-20260217-860682', 1, '2026-02-17 05:26:28', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(70, 'customer', 'paolo@gmail.com', 'Document Verification', 'Your License_Back has been Approved.', 'CAR-20260217-860682', 1, '2026-02-17 05:26:30', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(71, 'staff', 'all', 'New Review Submitted', 'Customer submitted a 5-star review for Mazda 3', 'CAR-20260217-860682', 1, '2026-02-17 06:04:00', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(72, 'admin', 'all', 'New Review Submitted', 'Customer submitted a 5-star review for Mazda 3', 'CAR-20260217-860682', 1, '2026-02-17 06:04:00', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(73, 'customer', 'Paolo@gmail.com', 'Return Reminder', 'Reminder: Your rental for Honda Civic is ending on 2026-04-23. Please return the car on time.', 'CAR-20260217-BA7F01', 1, '2026-02-17 06:04:15', 'general', 'important', 'bell', NULL, NULL, NULL, NULL),
(74, 'admin', NULL, 'New Car Rental Booking', 'New booking from oy for Hyundai Accent - Pending Review', 'CAR-20260217-172BE3', 1, '2026-02-17 06:14:09', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(75, 'staff', NULL, 'New Car Rental Booking', 'New booking from oy for Hyundai Accent - Review documents and approve', 'CAR-20260217-172BE3', 1, '2026-02-17 06:14:09', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(76, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-172BE3 has been submitted and is pending admin review.', 'CAR-20260217-172BE3', 1, '2026-02-17 06:14:09', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(77, 'customer', 'Paolo@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0034', 'CAR-20260217-172BE3', 1, '2026-02-17 06:16:08', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(78, 'customer', 'Paolo@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0034', 'CAR-20260217-172BE3', 1, '2026-02-17 06:16:47', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(79, 'customer', 'Paolo@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-172BE3 has been approved!', 'CAR-20260217-172BE3', 1, '2026-02-17 06:16:53', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(80, 'admin', NULL, 'New Car Rental Booking', 'New booking from Madridano Gabriel for Ford Mustang or Similar - Pending Review', 'CAR-20260217-DF2F1B', 1, '2026-02-17 06:18:54', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(81, 'staff', NULL, 'New Car Rental Booking', 'New booking from Madridano Gabriel for Ford Mustang or Similar - Review documents and approve', 'CAR-20260217-DF2F1B', 1, '2026-02-17 06:18:54', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(82, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-DF2F1B has been submitted and is pending admin review.', 'CAR-20260217-DF2F1B', 1, '2026-02-17 06:18:54', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(83, 'admin', NULL, 'New Car Rental Booking', 'New booking from POPOP for BMW X7 or Similar - Pending Review', 'CAR-20260217-B2118B', 1, '2026-02-17 06:26:51', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(84, 'staff', NULL, 'New Car Rental Booking', 'New booking from POPOP for BMW X7 or Similar - Review documents and approve', 'CAR-20260217-B2118B', 1, '2026-02-17 06:26:51', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(85, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-B2118B has been submitted and is pending admin review.', 'CAR-20260217-B2118B', 1, '2026-02-17 06:26:51', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(86, 'customer', 'Paolo@gmail.com', 'Rental Extended', 'Your rental for booking CAR-20260217-172BE3 has been extended by 3 days. Additional payment: ₱9,750.00', 'CAR-20260217-172BE3', 1, '2026-02-17 06:35:16', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(87, 'staff', NULL, 'Rental Extension', 'Booking CAR-20260217-172BE3 extended by 3 days. Additional payment pending: ₱9,750.00', 'CAR-20260217-172BE3', 1, '2026-02-17 06:35:16', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(88, 'admin', NULL, 'Rental Extension', 'Booking CAR-20260217-172BE3 extended by 3 days. Additional payment pending: ₱9,750.00', 'CAR-20260217-172BE3', 1, '2026-02-17 06:35:16', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(89, 'admin', NULL, 'New Car Rental Booking', 'New booking from Test for Tesla Model 3 or Similar - Pending Review', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:37:18', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(90, 'staff', NULL, 'New Car Rental Booking', 'New booking from Test for Tesla Model 3 or Similar - Review documents and approve', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:37:18', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(91, 'customer', 'g@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-EE36FB has been submitted and is pending admin review.', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:37:18', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(92, 'customer', 'g@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0037', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:38:03', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(93, 'customer', 'g@gmail.com', 'Documents Required', 'Please submit the following documents: Driver\'s License (Front), Driver\'s License (Back), Valid ID, Proof of Address. Upload at: My Profile > Documents', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:38:11', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(94, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-EE36FB', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:38:46', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(95, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-EE36FB', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:38:46', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(96, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-EE36FB', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:38:56', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(97, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-EE36FB', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:38:56', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(98, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-EE36FB', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:39:05', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(99, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-EE36FB', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:39:05', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(100, 'customer', 'g@gmail.com', 'Payment Reminder', 'Reminder: You have a remaining balance of ₱3,867.50 for booking CAR-20260217-EE36FB. Please settle the balance before pickup.', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:39:33', 'payment', 'important', 'bell', NULL, NULL, NULL, NULL),
(101, 'customer', 'g@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-EE36FB has been approved!', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:39:36', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(102, 'customer', 'Paolo@gmail.com', 'Booking Rejected', 'Your booking CAR-20260217-172BE3 has been rejected. Reason: wew', 'CAR-20260217-172BE3', 1, '2026-02-17 06:39:46', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(103, 'customer', 'Paolo@gmail.com', 'Booking Rejected', 'Your booking CAR-20260217-BA7F01 has been rejected. Reason: ewqe', 'CAR-20260217-BA7F01', 1, '2026-02-17 06:39:49', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(104, 'customer', 'g@gmail.com', 'Rental Extended', 'Your rental for booking CAR-20260217-EE36FB has been extended by 3 days. Additional payment: ₱13,650.00', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:43:26', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(105, 'staff', NULL, 'Rental Extension', 'Booking CAR-20260217-EE36FB extended by 3 days. Additional payment pending: ₱13,650.00', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:43:26', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(106, 'admin', NULL, 'Rental Extension', 'Booking CAR-20260217-EE36FB extended by 3 days. Additional payment pending: ₱13,650.00', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:43:26', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(107, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260217-EE36FB. Amount: ₱17,517.50', 'CAR-20260217-EE36FB', 1, '2026-02-17 06:45:39', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(108, 'customer', 'g@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0037', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:46:05', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(109, 'customer', 'g@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-EE36FB has been approved!', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:46:18', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(110, 'customer', 'g@gmail.com', 'Receipt Ready', 'Your receipt REC-20260217-0037 is ready for download.', 'CAR-20260217-EE36FB', 0, '2026-02-17 06:46:22', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(111, 'customer', 'kimberly.walker@email.com', 'Documents Required', 'Please submit the following documents: Driver\'s License (Front), Driver\'s License (Back), Valid ID, Proof of Address. Upload at: My Profile > Documents', 'CAR-20260325-T2T2T2', 0, '2026-02-17 07:07:00', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(112, 'customer', 'kimberly.walker@email.com', 'Booking Rejected', 'Your booking CAR-20260325-T2T2T2 has been rejected. Reason: q', 'CAR-20260325-T2T2T2', 0, '2026-02-17 07:07:20', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(113, 'customer', 'gregory.lewis@email.com', 'Booking Rejected', 'Your booking CAR-20260320-S1S1S1 has been rejected. Reason: qwe', 'CAR-20260320-S1S1S1', 0, '2026-02-17 07:07:24', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(114, 'customer', 'samantha.clark@email.com', 'Booking Rejected', 'Your booking CAR-20260315-R9R9R9 has been rejected. Reason: qwe', 'CAR-20260315-R9R9R9', 0, '2026-02-17 07:07:26', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(115, 'customer', 'timothy.robinson@email.com', 'Booking Rejected', 'Your booking CAR-20260310-Q8Q8Q8 has been rejected. Reason: eqwe', 'CAR-20260310-Q8Q8Q8', 0, '2026-02-17 07:07:29', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(116, 'customer', 'jennifer.martinez@email.com', 'Booking Rejected', 'Your booking CAR-20260305-P7P7P7 has been rejected. Reason: qeqw', 'CAR-20260305-P7P7P7', 0, '2026-02-17 07:07:31', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(117, 'customer', 'daniel.chen@email.com', 'Booking Rejected', 'Your booking CAR-20260129-Q7R8S9 has been rejected. Reason: qweqweqw', 'CAR-20260129-Q7R8S9', 0, '2026-02-17 07:07:34', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(118, 'customer', 'carmen.flores@email.com', 'Booking Rejected', 'Your booking CAR-20260126-H7I8J9 has been rejected. Reason: qweqwe', 'CAR-20260126-H7I8J9', 0, '2026-02-17 07:07:36', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(119, 'customer', 'david.wong@email.com', 'Booking Rejected', 'Your booking CAR-20260123-Y7Z8A9 has been rejected. Reason: qweqw', 'CAR-20260123-Y7Z8A9', 0, '2026-02-17 07:07:39', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(120, 'customer', 'lisa.tan@email.com', 'Booking Rejected', 'Your booking CAR-20260120-P7Q8R9 has been rejected. Reason: qweqw', 'CAR-20260120-P7Q8R9', 0, '2026-02-17 07:07:41', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(121, 'customer', 'pedro.cruz@email.com', 'Booking Rejected', 'Your booking CAR-20260117-G7H8I9 has been rejected. Reason: qwewq', 'CAR-20260117-G7H8I9', 0, '2026-02-17 07:07:43', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(122, 'admin', NULL, 'New Car Rental Booking', 'New booking from test2 for Honda Civic - Pending Review', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:09:46', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(123, 'staff', NULL, 'New Car Rental Booking', 'New booking from test2 for Honda Civic - Review documents and approve', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:09:46', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(124, 'customer', 'test@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-AC3C8C has been submitted and is pending admin review.', 'CAR-20260217-AC3C8C', 0, '2026-02-17 07:09:46', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(125, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260217-AC3C8C. Amount: ₱1,975.00', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:10:15', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(126, 'customer', 'test@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0138', 'CAR-20260217-AC3C8C', 0, '2026-02-17 07:10:38', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(127, 'customer', 'test@gmail.com', 'Documents Required', 'Please submit the following documents: Driver\'s License (Front), Driver\'s License (Back), Valid ID, Proof of Address. Upload at: My Profile > Documents', 'CAR-20260217-AC3C8C', 0, '2026-02-17 07:10:43', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(128, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-AC3C8C', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:11:20', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(129, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-AC3C8C', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:11:20', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(130, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-AC3C8C', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:11:27', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(131, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-AC3C8C', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:11:27', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(132, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-AC3C8C', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:11:35', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(133, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-AC3C8C', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:11:35', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(134, 'customer', 'test@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-AC3C8C has been approved!', 'CAR-20260217-AC3C8C', 0, '2026-02-17 07:11:54', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(135, 'customer', 'test@gmail.com', 'Rental Extended', 'Your rental for booking CAR-20260217-AC3C8C has been extended by 2 days. Additional payment: ₱7,900.00', 'CAR-20260217-AC3C8C', 0, '2026-02-17 07:12:17', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(136, 'staff', NULL, 'Rental Extension', 'Booking CAR-20260217-AC3C8C extended by 2 days. Additional payment pending: ₱7,900.00', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:12:17', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(137, 'admin', NULL, 'Rental Extension', 'Booking CAR-20260217-AC3C8C extended by 2 days. Additional payment pending: ₱7,900.00', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:12:17', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(138, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260217-AC3C8C. Amount: ₱9,875.00', 'CAR-20260217-AC3C8C', 1, '2026-02-17 07:12:26', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(139, 'customer', 'test@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0138', 'CAR-20260217-AC3C8C', 0, '2026-02-17 07:12:37', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(140, 'admin', NULL, 'New Car Rental Booking', 'New booking from test3 for Honda Civic - Pending Review', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:11', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(141, 'staff', NULL, 'New Car Rental Booking', 'New booking from test3 for Honda Civic - Review documents and approve', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:11', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(142, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-FCA551 has been submitted and is pending admin review.', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:11', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(143, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260217-FCA551. Amount: ₱9,100.00', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:20', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(144, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-FCA551', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:35', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(145, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-FCA551', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:35', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(146, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-FCA551', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:53', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(147, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-FCA551', 'CAR-20260217-FCA551', 1, '2026-02-17 07:27:53', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(148, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-FCA551', 'CAR-20260217-FCA551', 1, '2026-02-17 07:28:00', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(149, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-FCA551', 'CAR-20260217-FCA551', 1, '2026-02-17 07:28:00', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(150, 'customer', 'Paolo@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0139', 'CAR-20260217-FCA551', 1, '2026-02-17 07:28:15', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(151, 'customer', 'Paolo@gmail.com', 'Booking Approved', 'Your booking CAR-20260217-FCA551 has been approved!', 'CAR-20260217-FCA551', 1, '2026-02-17 07:28:18', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(152, 'admin', NULL, 'New Car Rental Booking', 'New booking from p for Honda Civic - Pending Review', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:52:24', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(153, 'staff', NULL, 'New Car Rental Booking', 'New booking from p for Honda Civic - Review documents and approve', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:52:24', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(154, 'customer', 'p@gmail.com', 'Booking Submitted', 'Your booking CAR-20260217-88F8D0 has been submitted and is pending admin review.', 'CAR-20260217-88F8D0', 0, '2026-02-17 07:52:24', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(155, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260217-88F8D0. Amount: ₱13,475.00', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:52:37', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(156, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-88F8D0', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:52:50', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(157, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260217-88F8D0', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:52:50', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(158, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-88F8D0', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:52:58', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(159, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260217-88F8D0', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:52:58', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(160, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-88F8D0', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:53:05', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(161, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260217-88F8D0', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:53:05', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(162, 'customer', 'p@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260217-0140', 'CAR-20260217-88F8D0', 0, '2026-02-17 07:53:26', 'payment', 'normal', 'bell', NULL, NULL, NULL, NULL),
(163, 'customer', 'p@gmail.com', 'Payment Reminder', 'Reminder: You have a remaining balance of ₱13,475.00 for booking CAR-20260217-88F8D0. Please settle the balance before pickup.', 'CAR-20260217-88F8D0', 0, '2026-02-17 07:53:32', 'payment', 'important', 'bell', NULL, NULL, NULL, NULL),
(164, 'customer', 'p@gmail.com', 'Booking Approved - Ready for Pickup', 'Your booking CAR-20260217-88F8D0 has been approved! Please pick up your car at: Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260217-88F8D0', 0, '2026-02-17 07:53:34', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(165, 'staff', 'admin', 'Car Picked Up', 'Customer has picked up car for booking CAR-20260217-88F8D0 at Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:53:51', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(166, 'staff', 'admin', 'Car Ready for Return Pickup', 'Customer returned car for booking CAR-20260217-88F8D0. Pick up at: Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260217-88F8D0', 1, '2026-02-17 07:53:58', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(167, 'customer', 'p@gmail.com', 'Document Verification', 'Your Proof_of_Address has been Approved.', 'CAR-20260217-88F8D0', 0, '2026-02-17 07:54:30', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(168, 'customer', 'p@gmail.com', 'Document Verification', 'Your Valid_ID has been Approved.', 'CAR-20260217-88F8D0', 0, '2026-02-17 07:54:34', 'document', 'normal', 'bell', NULL, NULL, NULL, NULL),
(169, 'admin', NULL, 'New Car Rental Booking', 'New booking from POPOP for Mazda 3 - Pending Review', 'CAR-20260218-36D683', 1, '2026-02-18 01:09:39', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(170, 'staff', NULL, 'New Car Rental Booking', 'New booking from POPOP for Mazda 3 - Review documents and approve', 'CAR-20260218-36D683', 1, '2026-02-18 01:09:39', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(171, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260218-36D683 has been submitted and is pending admin review.', 'CAR-20260218-36D683', 1, '2026-02-18 01:09:39', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(172, 'admin', NULL, 'Message from Customer: Gabriel', 'esadasdasd - From: ', NULL, 1, '2026-02-18 01:18:57', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(173, 'admin', NULL, 'New Car Rental Booking', 'New booking from Last for Kia Rio or Similar - Pending Review', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:14', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(174, 'staff', NULL, 'New Car Rental Booking', 'New booking from Last for Kia Rio or Similar - Review documents and approve', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:14', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(175, 'customer', 'Last@gmail.com', 'Booking Submitted', 'Your booking CAR-20260218-6AE33B has been submitted and is pending admin review.', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:14', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(176, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260218-6AE33B', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:34', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(177, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260218-6AE33B', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:34', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(178, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260218-6AE33B', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:42', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(179, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260218-6AE33B', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:42', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(180, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260218-6AE33B', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:55', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(181, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260218-6AE33B', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:22:55', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(182, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260218-6AE33B. Amount: ₱22,250.00', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:23:14', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(183, 'customer', 'Last@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260218-0167', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:23:48', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(184, 'customer', 'Last@gmail.com', 'Receipt Ready', 'Your receipt REC-20260218-0167 is ready for download.', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:24:01', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(185, 'customer', 'Last@gmail.com', 'Booking Approved - Ready for Pickup', 'Your booking CAR-20260218-6AE33B has been approved! Please pick up your car at: Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:24:49', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(186, 'customer', 'Last@gmail.com', 'Payment Reminder', 'Reminder: You have a remaining balance of ₱22,250.00 for booking CAR-20260218-6AE33B. Please settle the balance before pickup.', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:25:01', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(187, 'staff', 'admin', 'Car Picked Up', 'Customer has picked up car for booking CAR-20260218-6AE33B at Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:25:45', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(188, 'staff', 'admin', 'Car Ready for Return Pickup', 'Customer returned car for booking CAR-20260218-6AE33B. Pick up at: Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:28:25', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(189, 'admin', NULL, 'New Car Rental Booking', 'New booking from Paolo3 for Honda Civic or Similar - Pending Review', 'CAR-20260218-65474A', 0, '2026-02-18 01:29:42', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(190, 'staff', NULL, 'New Car Rental Booking', 'New booking from Paolo3 for Honda Civic or Similar - Review documents and approve', 'CAR-20260218-65474A', 0, '2026-02-18 01:29:42', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(191, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260218-65474A has been submitted and is pending admin review.', 'CAR-20260218-65474A', 1, '2026-02-18 01:29:42', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(192, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260218-65474A. Amount: ₱6,825.00', 'CAR-20260218-65474A', 0, '2026-02-18 01:29:54', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(193, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260218-65474A', 'CAR-20260218-65474A', 0, '2026-02-18 01:30:05', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(194, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260218-65474A', 'CAR-20260218-65474A', 0, '2026-02-18 01:30:05', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(195, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260218-65474A', 'CAR-20260218-65474A', 0, '2026-02-18 01:30:15', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(196, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260218-65474A', 'CAR-20260218-65474A', 0, '2026-02-18 01:30:15', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(197, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260218-65474A', 'CAR-20260218-65474A', 0, '2026-02-18 01:30:31', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(198, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260218-65474A', 'CAR-20260218-65474A', 0, '2026-02-18 01:30:31', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(199, 'customer', 'Paolo@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260218-0168', 'CAR-20260218-65474A', 1, '2026-02-18 01:30:52', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(200, 'customer', 'Paolo@gmail.com', 'Payment Reminder', 'Reminder: You have a remaining balance of ₱6,825.00 for booking CAR-20260218-65474A. Please settle the balance before pickup.', 'CAR-20260218-65474A', 1, '2026-02-18 01:31:04', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(201, 'customer', 'Paolo@gmail.com', 'Booking Approved - Ready for Pickup', 'Your booking CAR-20260218-65474A has been approved! Please pick up your car at: Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260218-65474A', 1, '2026-02-18 01:31:06', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(202, 'staff', NULL, 'Car Picked Up', 'Customer has picked up car for booking CAR-20260218-65474A at Any Hotel in Metro Manila (Hotel Delivery)', 'CAR-20260218-65474A', 0, '2026-02-18 01:31:58', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL),
(203, 'staff', 'admin', 'Car Ready for Return Pickup', 'Customer returned car for booking CAR-20260218-65474A. Pick up at: Shangri-La Hotel', 'CAR-20260218-65474A', 0, '2026-02-18 01:32:00', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(204, 'admin', NULL, 'New Car Rental Booking', 'New booking from POPOP for Kia Rio or Similar - Pending Review', 'CAR-20260218-B71464', 0, '2026-02-18 01:34:19', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(205, 'staff', NULL, 'New Car Rental Booking', 'New booking from POPOP for Kia Rio or Similar - Review documents and approve', 'CAR-20260218-B71464', 0, '2026-02-18 01:34:19', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(206, 'customer', 'Last@gmail.com', 'Booking Submitted', 'Your booking CAR-20260218-B71464 has been submitted and is pending admin review.', 'CAR-20260218-B71464', 0, '2026-02-18 01:34:19', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(207, 'staff', 'all', 'New Review Submitted', 'Customer submitted a 5-star review for Kia Rio or Similar', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:34:32', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(208, 'admin', 'all', 'New Review Submitted', 'Customer submitted a 5-star review for Kia Rio or Similar', 'CAR-20260218-6AE33B', 0, '2026-02-18 01:34:32', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(209, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260218-B71464', 'CAR-20260218-B71464', 0, '2026-02-18 01:34:46', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(210, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Valid_ID for booking CAR-20260218-B71464', 'CAR-20260218-B71464', 0, '2026-02-18 01:34:46', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(211, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260218-B71464', 'CAR-20260218-B71464', 0, '2026-02-18 01:34:55', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(212, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Proof_of_Address for booking CAR-20260218-B71464', 'CAR-20260218-B71464', 0, '2026-02-18 01:34:55', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(213, 'staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260218-B71464', 'CAR-20260218-B71464', 0, '2026-02-18 01:35:08', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(214, 'admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking CAR-20260218-B71464', 'CAR-20260218-B71464', 0, '2026-02-18 01:35:08', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(215, 'staff', NULL, 'Payment Submitted', 'Customer submitted payment proof for booking CAR-20260218-B71464. Amount: ₱1,875.00', 'CAR-20260218-B71464', 0, '2026-02-18 01:35:19', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(216, 'customer', 'Last@gmail.com', 'Payment Reminder', 'Reminder: You have a remaining balance of ₱1,875.00 for booking CAR-20260218-B71464. Please settle the balance before pickup.', 'CAR-20260218-B71464', 0, '2026-02-18 01:35:34', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(217, 'customer', 'Last@gmail.com', 'Payment Approved', 'Your payment has been approved. Invoice: INV-20260218-0169', 'CAR-20260218-B71464', 0, '2026-02-18 01:35:42', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL);
INSERT INTO `notifications` (`id`, `user_type`, `user_id`, `title`, `message`, `booking_id`, `is_read`, `created_at`, `category`, `priority`, `icon`, `action_url`, `action_label`, `expires_at`, `dismissed_at`) VALUES
(218, 'customer', 'Last@gmail.com', 'Booking Approved - Ready for Pickup', 'Your booking CAR-20260218-B71464 has been approved! Please pick up your car at: MRT Taft Station', 'CAR-20260218-B71464', 0, '2026-02-18 01:37:07', 'general', 'normal', 'bell', NULL, NULL, NULL, NULL),
(219, 'staff', NULL, 'Car Picked Up', 'Customer has picked up car for booking CAR-20260218-B71464 at MRT Taft Station', 'CAR-20260218-B71464', 0, '2026-02-18 01:37:23', 'booking', 'normal', 'bell', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` varchar(20) NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `times_used` int(11) DEFAULT 0,
  `for_first_time_only` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_codes`
--

INSERT INTO `promo_codes` (`id`, `code`, `discount_type`, `discount_value`, `description`, `valid_from`, `valid_until`, `usage_limit`, `times_used`, `for_first_time_only`, `status`, `created_at`) VALUES
(1, 'WELCOME2024', 'percentage', 15.00, 'Welcome discount for first-time customers', '2026-02-09', '2026-05-10', NULL, 0, 1, 'Active', '2026-02-09 04:43:23'),
(2, 'SAVE10', 'percentage', 10.00, 'Save 10% on any rental', '2026-02-09', '2026-04-10', 100, 0, 0, 'Active', '2026-02-09 04:43:23'),
(3, 'FLAT500', 'fixed', 500.00, 'Flat ₱500 off on rentals', '2026-02-09', '2026-03-11', 50, 0, 0, 'Active', '2026-02-09 04:43:23'),
(4, 'PAOLO123', 'percentage', 15.00, 'SALAMAT ', '2026-02-09', '2026-02-10', 1, 0, 0, 'Active', '2026-02-09 04:52:58');

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_pricing`
--

CREATE TABLE `seasonal_pricing` (
  `id` int(11) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `season_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price_multiplier` decimal(3,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seasonal_pricing`
--

INSERT INTO `seasonal_pricing` (`id`, `car_model`, `season_name`, `start_date`, `end_date`, `price_multiplier`, `created_at`) VALUES
(1, 'Kia Rio or Similar', 'summer', '2026-02-17', '2026-02-18', 1.40, '2026-02-17 04:21:29');

-- --------------------------------------------------------

--
-- Table structure for table `travel_agents`
--

CREATE TABLE `travel_agents` (
  `id` int(11) NOT NULL,
  `agent_id` varchar(20) NOT NULL,
  `agent_name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `agency_name` varchar(150) DEFAULT NULL,
  `specialization` enum('Flights','Hotels','Tours','Packages','All') DEFAULT 'All',
  `commission_rate` decimal(5,2) DEFAULT 10.00,
  `total_bookings` int(11) DEFAULT 0,
  `total_commission` decimal(10,2) DEFAULT 0.00,
  `status` enum('Active','Inactive','On Leave') DEFAULT 'Active',
  `registered_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_agents`
--

INSERT INTO `travel_agents` (`id`, `agent_id`, `agent_name`, `email`, `phone`, `agency_name`, `specialization`, `commission_rate`, `total_bookings`, `total_commission`, `status`, `registered_date`) VALUES
(1, 'AGT001', 'Maria Santos', 'maria@travelph.com', '+639171234567', 'Philippine Travel Experts', 'All', 12.50, 0, 0.00, 'Active', '2025-12-29 06:10:37'),
(2, 'AGT002', 'John Lim', 'john@asiantravel.com', '+65981234567', 'Asian Travel Hub', 'Flights', 10.00, 0, 0.00, 'Active', '2025-12-29 06:10:37'),
(3, 'AGT003', 'Sarah Johnson', 'sarah@luxurytravel.com', '+441234567890', 'Luxury Travel Co.', 'Packages', 15.00, 0, 0.00, 'Active', '2025-12-29 06:10:37'),
(4, 'AGT9237', 'James Bond', 'James@gmail.com', '09940213443', 'Loko', 'All', 5.00, 0, 0.00, 'Active', '2026-01-03 03:15:35'),
(5, 'AGT2306', 'Paolo M', 'Pmadridano@gmail.com', '096134214123', 'KI', 'All', 20.00, 3, 0.00, 'Active', '2026-01-06 04:59:22'),
(6, 'AGT8711', 'LOKK', 'lok@gmail.co', '09564642123', 'Tours', 'All', 10.00, 1, 0.00, 'Active', '2026-01-06 05:56:37');

-- --------------------------------------------------------

--
-- Table structure for table `travel_bookings`
--

CREATE TABLE `travel_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `traveler_name` varchar(150) NOT NULL,
  `travel_type` enum('Airplane','Ship','Bus','Train','Car','Other') NOT NULL,
  `from_country` varchar(100) NOT NULL,
  `from_city` varchar(100) NOT NULL,
  `to_country` varchar(100) NOT NULL,
  `to_city` varchar(100) NOT NULL,
  `departure_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `passengers` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_amount` decimal(10,2) DEFAULT NULL,
  `agent_id` varchar(20) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_bookings`
--

INSERT INTO `travel_bookings` (`id`, `booking_id`, `customer_id`, `traveler_name`, `travel_type`, `from_country`, `from_city`, `to_country`, `to_city`, `departure_date`, `return_date`, `passengers`, `total_amount`, `booking_amount`, `agent_id`, `agent_commission`, `status`, `notes`, `booking_date`) VALUES
(1, 'TRV-20251229-835', 'CUST001', 'Paolo', 'Train', 'United Arab Emirates', 'gg', 'Philippines', 'gg', '2025-12-29', '2025-12-29', 6, 64500.00, 64500.00, 'AGT003', 0.00, 'Confirmed', '', '2025-12-29 06:12:24'),
(2, 'TRV-20260103-885', NULL, 'Paolo', 'Train', 'Australia', 'Melbourne', 'Canada', 'Toronto', '0000-00-00', NULL, 1, 1000.00, NULL, 'AGT001', 125.00, 'Completed', NULL, '2026-01-03 02:30:16'),
(3, 'TRV-20260107-982', NULL, 'Pawix', 'Train', 'Australia', 'Melbourne', 'Canada', 'Toronto', '0000-00-00', NULL, 1, 45000.00, NULL, 'AGT001', 5625.00, 'Completed', NULL, '2026-01-07 06:12:52'),
(4, 'TRV-20260107-411', NULL, 'Gabriel', 'Train', 'Canada', 'Montreal', 'France', 'Lyon', '0000-00-00', NULL, 1, 9200.00, NULL, 'AGT001', 1150.00, 'Completed', NULL, '2026-01-07 06:19:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_availability`
--
ALTER TABLE `car_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `car_blocked_dates`
--
ALTER TABLE `car_blocked_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_maintenance`
--
ALTER TABLE `car_maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `car_rentals`
--
ALTER TABLE `car_rentals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `car_rental_bookings`
--
ALTER TABLE `car_rental_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `car_reviews`
--
ALTER TABLE `car_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `car_sales`
--
ALTER TABLE `car_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `customer_documents`
--
ALTER TABLE `customer_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `favorite_cars`
--
ALTER TABLE `favorite_cars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`customer_email`,`car_model`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_user_type_id` (`user_type`,`user_id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `travel_agents`
--
ALTER TABLE `travel_agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `agent_id` (`agent_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `travel_bookings`
--
ALTER TABLE `travel_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `car_availability`
--
ALTER TABLE `car_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_blocked_dates`
--
ALTER TABLE `car_blocked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `car_maintenance`
--
ALTER TABLE `car_maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `car_rentals`
--
ALTER TABLE `car_rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `car_rental_bookings`
--
ALTER TABLE `car_rental_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_reviews`
--
ALTER TABLE `car_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `car_sales`
--
ALTER TABLE `car_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `customer_documents`
--
ALTER TABLE `customer_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `favorite_cars`
--
ALTER TABLE `favorite_cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `travel_agents`
--
ALTER TABLE `travel_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `travel_bookings`
--
ALTER TABLE `travel_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car_availability`
--
ALTER TABLE `car_availability`
  ADD CONSTRAINT `car_availability_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `car_maintenance`
--
ALTER TABLE `car_maintenance`
  ADD CONSTRAINT `car_maintenance_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `car_rentals`
--
ALTER TABLE `car_rentals`
  ADD CONSTRAINT `car_rentals_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `travel_agents` (`agent_id`) ON DELETE SET NULL;

--
-- Constraints for table `car_rental_bookings`
--
ALTER TABLE `car_rental_bookings`
  ADD CONSTRAINT `car_rental_bookings_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `travel_agents` (`agent_id`);

--
-- Constraints for table `car_reviews`
--
ALTER TABLE `car_reviews`
  ADD CONSTRAINT `car_reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `car_rentals` (`booking_id`);

--
-- Constraints for table `customer_documents`
--
ALTER TABLE `customer_documents`
  ADD CONSTRAINT `customer_documents_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `car_rentals` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
