-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2025 at 11:14 AM
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
(12, 'UAE', 'Dubai', 'DXB');

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
  `due_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `commission_id`, `agent_id`, `booking_id`, `booking_type`, `booking_amount`, `commission_rate`, `commission_amount`, `payment_id`, `status`, `due_date`, `paid_date`, `created_at`) VALUES
(1, 'COM-20251229-437', 'AGT003', 'TRV-20251229-835', 'Travel', 64500.00, 15.00, 9675.00, NULL, 'Pending', '2026-01-28', NULL, '2025-12-29 06:12:24');

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
  `full_name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `loyalty_points` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_id`, `full_name`, `email`, `phone`, `address`, `country`, `loyalty_points`, `status`, `registration_date`) VALUES
(1, 'CUST001', 'Gabriel Paolo Madridano', 'gabriel@email.com', '+639123456789', 'Manila, Philippines', 'Philippines', 2500, 'Active', '2025-12-29 06:10:37'),
(2, 'CUST002', 'Maria Rivera', 'maria.r@email.com', '+639987654321', 'Cebu City, Philippines', 'Philippines', 1200, 'Active', '2025-12-29 06:10:37'),
(3, 'CUST003', 'James Wilson', 'james.w@email.com', '+12125551234', 'New York, USA', 'United States', 800, 'Active', '2025-12-29 06:10:37');

-- --------------------------------------------------------

--
-- Table structure for table `flight_bookings`
--

CREATE TABLE `flight_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `passenger_name` varchar(150) NOT NULL,
  `airline` varchar(100) NOT NULL,
  `flight_number` varchar(50) NOT NULL,
  `departure_airport` varchar(100) NOT NULL,
  `arrival_airport` varchar(100) NOT NULL,
  `departure_date` datetime NOT NULL,
  `arrival_date` datetime NOT NULL,
  `seat_class` enum('Economy','Premium Economy','Business','First Class') NOT NULL,
  `passengers` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `agent_id` varchar(20) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Boarded','Completed','Cancelled') DEFAULT 'Pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_bookings`
--

CREATE TABLE `hotel_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `guest_name` varchar(150) NOT NULL,
  `hotel_name` varchar(150) NOT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `room_type` enum('Single','Double','Suite','Deluxe','Family') NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guests` int(11) DEFAULT 1,
  `nights` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `agent_id` varchar(20) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Checked-in','Checked-out','Cancelled') DEFAULT 'Pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `payment_id` varchar(20) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `booking_type` enum('Travel','Hotel','Tour','Flight') NOT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Credit Card','Bank Transfer','GCash','PayMaya','Cash') DEFAULT 'Credit Card',
  `payment_status` enum('Pending','Paid','Failed','Refunded') DEFAULT 'Pending',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `agent_commission_paid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `review_id` varchar(20) NOT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `review_type` enum('Travel','Hotel','Tour','Flight','General') NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `response` text DEFAULT NULL,
  `status` enum('Pending','Published','Hidden') DEFAULT 'Pending',
  `review_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_bookings`
--

CREATE TABLE `tour_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `participant_name` varchar(150) NOT NULL,
  `tour_name` varchar(150) NOT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `tour_type` enum('Sightseeing','Adventure','Cultural','Food','Nature','Historical') NOT NULL,
  `tour_date` date NOT NULL,
  `duration_hours` int(11) DEFAULT 4,
  `participants` int(11) DEFAULT 1,
  `price_per_person` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `agent_id` varchar(20) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'AGT001', 'Maria Santos', 'maria@travelph.com', '+639171234567', 'Philippine Travel Experts', 'All', 12.50, 45, 125000.00, 'Active', '2025-12-29 06:10:37'),
(2, 'AGT002', 'John Lim', 'john@asiantravel.com', '+65981234567', 'Asian Travel Hub', 'Flights', 10.00, 32, 85000.00, 'Active', '2025-12-29 06:10:37'),
(3, 'AGT003', 'Sarah Johnson', 'sarah@luxurytravel.com', '+441234567890', 'Luxury Travel Co.', 'Packages', 15.00, 29, 104675.00, 'Active', '2025-12-29 06:10:37');

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
  `agent_id` varchar(20) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_bookings`
--

INSERT INTO `travel_bookings` (`id`, `booking_id`, `customer_id`, `traveler_name`, `travel_type`, `from_country`, `from_city`, `to_country`, `to_city`, `departure_date`, `return_date`, `passengers`, `total_amount`, `agent_id`, `agent_commission`, `status`, `notes`, `booking_date`) VALUES
(1, 'TRV-20251229-835', 'CUST001', 'Paolo', 'Train', 'United Arab Emirates', 'gg', 'Philippines', 'gg', '2025-12-29', '2025-12-29', 6, 64500.00, 'AGT003', 0.00, 'Confirmed', '', '2025-12-29 06:12:24');

--
-- Indexes for dumped tables
--

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
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `flight_bookings`
--
ALTER TABLE `flight_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indexes for table `hotel_bookings`
--
ALTER TABLE `hotel_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `review_id` (`review_id`);

--
-- Indexes for table `tour_bookings`
--
ALTER TABLE `tour_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

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
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `flight_bookings`
--
ALTER TABLE `flight_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotel_bookings`
--
ALTER TABLE `hotel_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_bookings`
--
ALTER TABLE `tour_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `travel_agents`
--
ALTER TABLE `travel_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `travel_bookings`
--
ALTER TABLE `travel_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_code`) REFERENCES `countries` (`country_code`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
