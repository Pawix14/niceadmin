-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2026 at 04:40 AM
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
(1, 'ADM001', 'admin', '$2y$10$e5kfnM6EOQxR/PF7ikmFeuCkfB25rREF9NM58w5bXCativDz7ZDKu', 'System Administrator', 'admin@paradise.com', NULL, 'Super Admin', 'Active', '2026-02-13 11:28:28', '2026-02-09 04:19:37', '2026-02-13 03:28:28');

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
(11, 'Honda Civic or Similar', 'Compact', 3650.00, 'assets/img/cars/honda-civic.jpg', '5 seats, Premium Sound, Automatic, GPS Navigation', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:12:20'),
(20, 'Kia Rio or Similar', 'Economy', 2850.00, 'assets/img/cars/kia-rio.jpg', '4-5 seats, Air Conditioning, Automatic, Fuel Efficient', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 1, '2026-02-09 05:13:22'),
(21, 'Toyota Corolla ', 'Economy', 3150.00, 'assets/img/cars/toyota-corolla.jpg', '5 seats, Air Conditioning, Automatic, Spacious Trunk', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(22, 'Mitsubishi Mirage', 'Economy', 2650.00, 'assets/img/cars/mitsubishi-mirage.jpg', '4 seats, Air Conditioning, Manual, Compact', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(23, 'Hyundai Accent', 'Economy', 2950.00, 'assets/img/cars/hyundai-accent.jpg', '5 seats, Air Conditioning, Automatic, Bluetooth', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(24, 'Honda Civic', 'Compact', 3650.00, 'assets/img/cars/honda-civic.jpg', '5 seats, Premium Sound, Automatic, GPS Navigation', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 1, '2026-02-09 05:13:22'),
(25, 'Mazda 3', 'Compact', 3850.00, 'assets/img/cars/mazda-3.jpg', '5 seats, Leather Seats, Automatic, Sunroof', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(26, 'Volkswagen Jetta ', 'Compact', 3950.00, 'assets/img/cars/volkswagen-jetta.jpg', '5 seats, Turbo Engine, Automatic, Premium Audio', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(27, 'Ford Mustang or Similar', 'SUV', 5108.00, 'assets/img/cars/ford-mustang.jpg', '4 seats, Sports Car, Automatic, Premium Features', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(30, 'BMW X7 or Similar', 'Luxury', 5878.00, 'assets/img/cars/bmw-x7.jpg', '7 seats, Leather Seats, Automatic, Premium Package', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(33, 'Tesla Model 3 or Similar', 'Electric', 4250.00, 'assets/img/cars/tesla-model3.jpg', '5 seats, Electric, Autopilot, Premium Interior', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 0, '2026-02-09 05:13:22'),
(35, 'Hyundai Ioniq 5 ', 'Electric', 4500.00, 'assets/img/cars/hyundai-ioniq-5.jpg', '5 seats, Electric, V2L, Ultra Fast Charging', 'Gasoline', 'Automatic', 5, 200, NULL, NULL, NULL, NULL, 'Active', 1, '2026-02-09 05:13:22');

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
(1, 7, 'Tire Change', 'wewqe', 2340.00, '2026-02-13', '2026-02-21', 'admin', 'Completed', '2026-02-13 03:12:01');

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
  `license_expiry` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_rentals`
--

INSERT INTO `car_rentals` (`id`, `booking_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_age`, `pickup_date`, `dropoff_date`, `pickup_time`, `dropoff_time`, `pickup_location`, `dropoff_location`, `car_type`, `car_model`, `car_image`, `rental_days`, `daily_rate`, `subtotal`, `insurance_fee`, `additional_fees`, `total_amount`, `promo_code`, `discount_amount`, `agent_id`, `agent_commission`, `status`, `admin_notes`, `payment_method`, `payment_status`, `created_at`, `updated_at`, `license_number`, `license_expiry`) VALUES
(1, 'CAR-20260107-457060', 'Paolo', 'pmadridano@gmail.com', '096555214545', 21, '0000-00-00', '0000-00-00', '00:00:00', '00:00:00', '', '', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 8, 2850.00, 22800.00, 0.00, 0.00, 22800.00, '', 0.00, 'AGT001', 2850.00, 'Confirmed', NULL, 'Pay at Pickup', 'Pending', '2026-01-07 02:49:08', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(11, 'CAR-20260107-2087BC', 'Paolo', 'pmadridano@gmail.com', '096555214545', 30, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'MRT Taft Station', 'Any Hotel in Metro Manila (Hotel Delivery)', 'SUV', 'Ford Mustang or Similar', 'assets/img/cars/ford-mustang.jpg', 1, 5108.00, 5108.00, 0.00, 0.00, 5108.00, '', 0.00, NULL, 0.00, 'Confirmed', NULL, 'Pay at Pickup', 'Pending', '2026-01-07 03:04:02', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(12, 'CAR-20260107-8EAF81', 'Madridano Gabriel', 'pmadridano@gmail.com', '096555214545', 20, '2026-01-10', '2026-01-11', '14:03:00', '22:01:00', 'Pasay City - Mall of Asia', 'Makati City Center', 'Electric', 'Tesla Model 3 or Similar', 'assets/img/cars/tesla-model3.jpg', 1, 4250.00, 4250.00, 600.00, 800.00, 5650.00, '', 0.00, NULL, 0.00, 'Confirmed', NULL, 'PayPal', 'Pending', '2026-01-07 03:04:56', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(13, 'CAR-20260107-359E0B', 'POPOP', 'pmadridano@gmail.com', '096555214545', 23, '2026-01-10', '2026-01-11', '10:00:00', '10:00:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'MRT Ayala Station', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 1, 3650.00, 3650.00, 300.00, 500.00, 4450.00, '', 0.00, 'AGT2306', 890.00, 'Confirmed', NULL, 'GCash', 'Pending', '2026-01-07 04:30:11', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(14, 'CAR-20260107-0A2ACE', 'POPOP', 'pmadridano@gmail.com', '096555214545', 42, '2026-01-07', '2026-01-16', '10:00:00', '10:00:00', 'Mandaluyong City - Ortigas Center', 'PITX - Parañaque Integrated Terminal Exchange', 'Economy', 'Toyota Corolla or Similar', 'assets/img/cars/toyota-corolla.jpg', 9, 3150.00, 28350.00, 8100.00, 9000.00, 45450.00, '', 0.00, 'AGT001', 5681.25, 'Confirmed', NULL, 'Debit Card', 'Pending', '2026-01-07 04:30:40', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(15, 'CAR-20260107-A837D3', 'Paolo', 'pmadridano@gmail.com', '096553222', 32, '2026-01-07', '2026-01-15', '10:00:00', '10:00:00', 'CEB - Mactan-Cebu International Airport', 'Bonifacio Global City (BGC)', 'Luxury', 'BMW X7 or Similar', 'assets/img/cars/bmw-x7.jpg', 8, 5878.00, 47024.00, 7200.00, 6400.00, 60624.00, '', 0.00, 'AGT001', 7578.00, 'Confirmed', NULL, 'Credit Card', 'Pending', '2026-01-07 04:31:06', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(16, 'CAR-20260107-7CDF36', 'Gabriel Paolo Madridano', 'pmadridano2@gmail.com', '09619490469', 32, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'DVO - Francisco Bangoy International Airport', 'LRT Buendia Station', 'Electric', 'Tesla Model 3 or Similar', 'assets/img/cars/tesla-model3.jpg', 1, 4250.00, 4250.00, 900.00, 1000.00, 6150.00, '', 0.00, 'AGT001', 768.75, 'Confirmed', NULL, 'Debit Card', 'Pending', '2026-01-07 05:23:51', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(17, 'CAR-20260107-93F174', 'James', 'james@gmail.com', '09321321333', 21, '2026-01-09', '2026-01-23', '10:00:00', '10:00:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'PITX - Parañaque Integrated Terminal Exchange', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 14, 3650.00, 51100.00, 0.00, 0.00, 51100.00, '', 0.00, 'AGT2306', 10220.00, 'Confirmed', NULL, 'Debit Card', 'Pending', '2026-01-07 05:24:25', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(18, 'CAR-20260107-E6DC28', 'Paolo', 'pmadridano@gmail.com', '09545521115', 20, '2026-01-07', '2026-01-09', '10:00:00', '13:30:00', 'CEB - Mactan-Cebu International Airport', 'DVO - Francisco Bangoy International Airport', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 2, 3650.00, 7300.00, 1200.00, 1600.00, 10100.00, '', 0.00, NULL, 0.00, 'Confirmed', NULL, 'Pay at Pickup', 'Pending', '2026-01-07 05:25:18', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(19, 'CAR-20260107-3B78DA', 'Paolo', 'pmadridano@gmail.com', '096555214545', 30, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'MRT Taft Station', 'Makati City Center', 'SUV', 'Ford Mustang or Similar', 'assets/img/cars/ford-mustang.jpg', 1, 5108.00, 5108.00, 600.00, 500.00, 6208.00, '', 0.00, 'AGT2306', 1241.60, 'Confirmed', NULL, 'GCash', 'Pending', '2026-01-07 06:02:11', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(20, 'CAR-20260107-AE2928', 'Paolo', 'pmadridano@gmail.com', '096555214545', 20, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'MNL - Manila Ninoy Aquino International Airport', 'Quezon City - Cubao', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 1, 2850.00, 2850.00, 300.00, 200.00, 3350.00, '', 0.00, 'AGT2306', 670.00, 'Confirmed', NULL, 'Credit Card', 'Pending', '2026-01-07 06:10:50', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(21, 'CAR-20260113-AA6D62', 'POPOP', 'pmadridano@gmail.com', '096555214545', 23, '2026-01-13', '2026-01-14', '10:00:00', '10:00:00', 'PITX - Parañaque Integrated Terminal Exchange', 'PITX - Parañaque Integrated Terminal Exchange', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 1, 2850.00, 2850.00, 0.00, 500.00, 3350.00, '', 0.00, 'AGT001', 418.75, 'Confirmed', NULL, 'Debit Card', 'Pending', '2026-01-13 12:39:54', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(22, 'CAR-20260113-3599A9', 'Paolo', 'pmadridano@gmail.com', '096555214545', 32, '2026-01-13', '2026-01-14', '10:00:00', '10:00:00', 'Shangri-La Hotel', 'Shangri-La Hotel', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 1, 3650.00, 3650.00, 300.00, 300.00, 4250.00, '', 0.00, 'AGT2306', 850.00, 'Confirmed', NULL, 'Credit Card', 'Pending', '2026-01-13 12:41:39', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(23, 'CAR-20260113-468695', 'Paolo', 'pmadridano@gmail.com', '096555214545', 32, '2026-01-13', '2026-01-15', '10:00:00', '02:00:00', 'PITX - Parañaque Integrated Terminal Exchange', 'PITX - Parañaque Integrated Terminal Exchange', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 2, 2850.00, 5700.00, 1200.00, 1600.00, 8500.00, '', 0.00, NULL, 0.00, 'Confirmed', NULL, 'Pay at Pickup', 'Pending', '2026-01-13 12:43:48', '2026-02-09 04:03:58', 'N00-00-000000', '2025-12-31'),
(24, 'CAR-20260209-EAD63F', 'Paolo', 'pmadridano@gmail.com', '096555214545', 45, '2026-02-09', '2026-02-13', '10:00:00', '10:00:00', 'MRT Taft Station', 'MRT Ayala Station', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 4, 3650.00, 14600.00, 1200.00, 2000.00, 17800.00, '', 0.00, NULL, 0.00, 'Confirmed', 'Your car is ready', 'Pay at Pickup', 'Pending', '2026-02-09 05:53:50', '2026-02-09 05:54:20', 'A01-02-030304', '2026-02-26'),
(25, 'CAR-20260209-B5641C', 'Gabrie', 'Paolo@gmail.com', '096555214545', 21, '2026-02-09', '2026-02-10', '10:00:00', '10:00:00', 'CRK - Clark International Airport', 'CEB - Mactan-Cebu International Airport', 'SUV', 'Ford Mustang or Similar', 'assets/img/cars/ford-mustang.jpg', 1, 3831.00, 3831.00, 900.00, 1000.00, 4871.35, 'WELCOME2024', 859.65, NULL, 0.00, 'Confirmed', 'ok', 'GCash', 'Pending', '2026-02-09 05:58:03', '2026-02-09 05:58:29', 'A01-20-201303', '2026-02-12'),
(26, 'CAR-20260209-5EBDB0', 'Madridano', 'Madridano@gmail.com', '09619490469', 23, '2026-02-09', '2026-02-11', '10:00:00', '10:00:00', 'Okada Manila', 'Okada Manila', 'Luxury', 'BMW X7 or Similar', 'assets/img/cars/bmw-x7.jpg', 2, 5878.00, 11756.00, 1200.00, 2000.00, 12712.60, 'PAOLO123', 2243.40, 'AGT2306', 2542.52, 'Completed', '45', 'Pay at Pickup', 'Pending', '2026-02-09 06:25:09', '2026-02-09 06:31:06', 'A01-30-620434', '2026-02-11'),
(27, 'CAR-20260209-318224', 'Madridano Gabriel', 'Pa2lo@gmail.com', '09619490469', 32, '2026-02-09', '2026-02-10', '10:00:00', '10:00:00', 'DVO - Francisco Bangoy International Airport', 'CEB - Mactan-Cebu International Airport', 'Compact', 'Honda Civic', 'assets/img/cars/honda-civic.jpg', 1, 3650.00, 3650.00, 300.00, 0.00, 3950.00, '', 0.00, 'AGT2306', 790.00, 'Confirmed', 'OOOOOOOOOOOOO', 'GCash', 'Pending', '2026-02-09 06:32:03', '2026-02-09 06:36:43', 'A01-30-620434', '2030-11-01'),
(28, 'CAR-20260209-992441', 'POPOP', 'paolo@gmail.com', '09619490469', 23, '2026-02-09', '2026-02-10', '10:00:00', '10:00:00', 'CEB - Mactan-Cebu International Airport', 'CEB - Mactan-Cebu International Airport', 'Compact', 'Mazda 3', 'assets/img/cars/mazda-3.jpg', 1, 3850.00, 3850.00, 600.00, 1000.00, 5450.00, '', 0.00, NULL, 0.00, 'Completed', 'qweqw', 'Pay at Pickup', 'Pending', '2026-02-09 06:38:17', '2026-02-09 06:38:30', 'A01-02-024303', '2026-03-14');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `country_code` varchar(3) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `airport_code` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_code`, `city_name`, `airport_code`) VALUES
(1, 'PHL', 'Manila', 'MNL'),
(2, 'PHL', 'Cebu', 'CEB'),
(3, 'PHL', 'Davao', 'DVO'),
(4, 'PHL', 'Cagayan de Oro', 'CGY'),
(5, 'PHL', 'Bacolod', 'BCD'),
(6, 'USA', 'New York', 'JFK'),
(7, 'USA', 'Los Angeles', 'LAX'),
(8, 'USA', 'Chicago', 'ORD'),
(9, 'JPN', 'Tokyo', 'NRT'),
(10, 'JPN', 'Osaka', 'KIX'),
(11, 'SGP', 'Singapore', 'SIN'),
(12, 'UAE', 'Dubai', 'DXB'),
(77, 'AUS', 'Sydney', NULL),
(78, 'AUS', 'Melbourne', NULL),
(79, 'AUS', 'Brisbane', NULL),
(80, 'AUS', 'Perth', NULL),
(81, 'CAN', 'Toronto', NULL),
(82, 'CAN', 'Vancouver', NULL),
(83, 'CAN', 'Montreal', NULL),
(84, 'CAN', 'Calgary', NULL),
(85, 'FRA', 'Paris', NULL),
(86, 'FRA', 'Marseille', NULL),
(87, 'FRA', 'Lyon', NULL),
(88, 'FRA', 'Toulouse', NULL),
(89, 'GER', 'Berlin', NULL),
(90, 'GER', 'Munich', NULL),
(91, 'GER', 'Hamburg', NULL),
(92, 'GER', 'Cologne', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` int(11) NOT NULL,
  `commission_id` varchar(20) NOT NULL,
  `agent_id` varchar(20) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `booking_type` enum('Travel','Hotel','Tour','Flight') NOT NULL,
  `booking_amount` decimal(10,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `payment_id` varchar(20) DEFAULT NULL,
  `status` enum('Pending','Paid','Cancelled') DEFAULT 'Pending',
  `booking_date` date DEFAULT NULL,
  `due_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `calculated_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `commission_id`, `agent_id`, `booking_id`, `booking_type`, `booking_amount`, `commission_rate`, `commission_amount`, `payment_id`, `status`, `booking_date`, `due_date`, `paid_date`, `created_at`, `calculated_date`) VALUES
(1, 'COM-20251229-437', 'AGT003', 'TRV-20251229-835', 'Travel', 64500.00, 15.00, 9675.00, NULL, 'Pending', NULL, '2026-01-28', NULL, '2025-12-29 06:12:24', '2026-01-07 14:32:38');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `country_code` varchar(3) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `continent` varchar(50) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'PHP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_code`, `country_name`, `continent`, `currency`) VALUES
(1, 'PHL', 'Philippines', 'Asia', 'PHP'),
(2, 'USA', 'United States', 'North America', 'USD'),
(3, 'JPN', 'Japan', 'Asia', 'JPY'),
(4, 'SGP', 'Singapore', 'Asia', 'SGD'),
(5, 'UAE', 'United Arab Emirates', 'Asia', 'AED'),
(6, 'UK', 'United Kingdom', 'Europe', 'GBP'),
(7, 'FRA', 'France', 'Europe', 'EUR'),
(8, 'GER', 'Germany', 'Europe', 'EUR'),
(9, 'AUS', 'Australia', 'Oceania', 'AUD'),
(10, 'CAN', 'Canada', 'North America', 'CAD');

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
(1, 'CUST8350', 'paolo123', '$2y$10$l2vwH.YGmjREiLtltR7aAu1HgLNn5ijFlEGa0HviScdBeVzfw257m', 'Gabriel Paolo Madridano', 'paolo@gmail.com', '09940213443', 'Km6 Upper Balulang', 'uploads/profiles/profile_CUST8350_1770611917.png', 'Active', '2026-02-13 11:35:42', '2026-02-09 04:31:55', '2026-02-13 03:35:42');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_type`, `user_id`, `title`, `message`, `booking_id`, `is_read`, `created_at`) VALUES
(1, 'admin', NULL, 'New Car Rental Booking', 'New booking from Paolo for Honda Civic or Similar', 'CAR-20260209-EAD63F', 1, '2026-02-09 05:53:50'),
(2, 'customer', 'pmadridano@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-EAD63F has been Confirmed by admin. Note: Your car is ready', 'CAR-20260209-EAD63F', 0, '2026-02-09 05:54:20'),
(3, 'admin', NULL, 'New Car Rental Booking', 'New booking from Gabrie for Ford Mustang or Similar - Pending Review', 'CAR-20260209-B5641C', 1, '2026-02-09 05:58:03'),
(4, 'customer', 'Paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-B5641C has been submitted and is pending admin review.', 'CAR-20260209-B5641C', 1, '2026-02-09 05:58:03'),
(5, 'customer', 'Paolo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-B5641C has been Confirmed by admin. Note: ok', 'CAR-20260209-B5641C', 1, '2026-02-09 05:58:29'),
(6, 'admin', NULL, 'Message from Customer: Gabriel Paolo Madridano', 'OK Sir - From: paolo@gmail.com', NULL, 1, '2026-02-09 06:03:04'),
(7, 'admin', NULL, 'New Car Rental Booking', 'New booking from Madridano for BMW X7 or Similar - Pending Review', 'CAR-20260209-5EBDB0', 1, '2026-02-09 06:25:09'),
(8, 'customer', 'Madridano@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-5EBDB0 has been submitted and is pending admin review.', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:25:09'),
(9, 'customer', 'Madridano@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-5EBDB0 has been Confirmed by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:25:43'),
(10, 'customer', 'Madridano@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-5EBDB0 has been Confirmed by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:27:52'),
(11, 'customer', 'Madridano@gmail.com', 'Booking Cancelled', 'Your booking CAR-20260209-5EBDB0 has been Cancelled by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:27:57'),
(12, 'customer', 'paolo@gmail.com', 'Message from Admin', 'rer', NULL, 1, '2026-02-09 06:29:41'),
(13, 'customer', 'Madridano@gmail.com', 'Booking Active', 'Your booking CAR-20260209-5EBDB0 has been Active by admin. Note: PO', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:29:45'),
(14, 'customer', 'Madridano@gmail.com', 'Booking Completed', 'Your booking CAR-20260209-5EBDB0 has been Completed by admin. Note: 45', 'CAR-20260209-5EBDB0', 0, '2026-02-09 06:31:06'),
(15, 'admin', NULL, 'New Car Rental Booking', 'New booking from Madridano Gabriel for Honda Civic - Pending Review', 'CAR-20260209-318224', 1, '2026-02-09 06:32:03'),
(16, 'customer', 'Pa2lo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-318224 has been submitted and is pending admin review.', 'CAR-20260209-318224', 0, '2026-02-09 06:32:03'),
(17, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:32:18'),
(18, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:32:49'),
(19, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:32:59'),
(20, 'customer', 'Pa2lo@gmail.com', 'Booking Completed', 'Your booking CAR-20260209-318224 has been Completed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:35:29'),
(21, 'customer', 'Pa2lo@gmail.com', 'Booking Confirmed', 'Your booking CAR-20260209-318224 has been Confirmed by admin. Note: OOOOOOOOOOOOO', 'CAR-20260209-318224', 0, '2026-02-09 06:36:43'),
(22, 'admin', NULL, 'New Car Rental Booking', 'New booking from POPOP for Mazda 3 - Pending Review', 'CAR-20260209-992441', 1, '2026-02-09 06:38:17'),
(23, 'customer', 'paolo@gmail.com', 'Booking Submitted', 'Your booking CAR-20260209-992441 has been submitted and is pending admin review.', 'CAR-20260209-992441', 1, '2026-02-09 06:38:17'),
(24, 'customer', 'paolo@gmail.com', 'Booking Completed', 'Your booking CAR-20260209-992441 has been Completed by admin. Note: qweqw', 'CAR-20260209-992441', 1, '2026-02-09 06:38:30'),
(25, 'admin', NULL, 'Message from Customer: Gabriel Paolo Madridano', 'r - From: paolo@gmail.com', NULL, 1, '2026-02-13 03:17:43');

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
(5, 'AGT2306', 'Paolo M', 'Pmadridano@gmail.com', '096134214123', 'KI', 'All', 20.00, 2, 0.00, 'Active', '2026-01-06 04:59:22'),
(6, 'AGT8711', 'LOKK', 'lok@gmail.co', '09564642123', 'Tours', 'All', 10.00, 0, 0.00, 'Active', '2026-01-06 05:56:37');

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
-- Indexes for table `car_sales`
--
ALTER TABLE `car_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_code` (`country_code`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commission_id` (`commission_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `country_code` (`country_code`),
  ADD UNIQUE KEY `country_name` (`country_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `car_maintenance`
--
ALTER TABLE `car_maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `car_rentals`
--
ALTER TABLE `car_rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `car_rental_bookings`
--
ALTER TABLE `car_rental_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_sales`
--
ALTER TABLE `car_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_code`) REFERENCES `countries` (`country_code`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
