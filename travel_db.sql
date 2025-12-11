-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 12:02 PM
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
-- Database: `travel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `country_code` varchar(3) DEFAULT NULL,
  `city_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_code`, `city_name`) VALUES
(1, 'USA', 'New York'),
(2, 'USA', 'Los Angeles'),
(3, 'USA', 'Chicago'),
(4, 'USA', 'Miami'),
(5, 'USA', 'Las Vegas'),
(6, 'UK', 'London'),
(7, 'UK', 'Manchester'),
(8, 'UK', 'Birmingham'),
(9, 'UK', 'Liverpool'),
(10, 'UK', 'Edinburgh'),
(11, 'JPN', 'Tokyo'),
(12, 'JPN', 'Osaka'),
(13, 'JPN', 'Kyoto'),
(14, 'JPN', 'Yokohama'),
(15, 'JPN', 'Nagoya'),
(16, 'FRA', 'Paris'),
(17, 'FRA', 'Marseille'),
(18, 'FRA', 'Lyon'),
(19, 'FRA', 'Toulouse'),
(20, 'FRA', 'Nice'),
(21, 'GER', 'Berlin'),
(22, 'GER', 'Munich'),
(23, 'GER', 'Hamburg'),
(24, 'GER', 'Frankfurt'),
(25, 'GER', 'Cologne'),
(26, 'AUS', 'Sydney'),
(27, 'AUS', 'Melbourne'),
(28, 'AUS', 'Brisbane'),
(29, 'AUS', 'Perth'),
(30, 'AUS', 'Adelaide'),
(31, 'CAN', 'Toronto'),
(32, 'CAN', 'Vancouver'),
(33, 'CAN', 'Montreal'),
(34, 'CAN', 'Calgary'),
(35, 'CAN', 'Ottawa'),
(36, 'UAE', 'Dubai'),
(37, 'UAE', 'Abu Dhabi'),
(38, 'UAE', 'Sharjah'),
(39, 'UAE', 'Ajman'),
(40, 'UAE', 'Ras Al Khaimah'),
(41, 'SGP', 'Singapore'),
(42, 'PHL', 'Manila'),
(43, 'PHL', 'Cebu'),
(44, 'PHL', 'Davao'),
(45, 'PHL', 'Cagayan de Oro'),
(46, 'PHL', 'Bacolod');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `country_code` varchar(3) DEFAULT NULL,
  `country_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_code`, `country_name`) VALUES
(1, 'USA', 'United States'),
(2, 'UK', 'United Kingdom'),
(3, 'JPN', 'Japan'),
(4, 'FRA', 'France'),
(5, 'GER', 'Germany'),
(6, 'AUS', 'Australia'),
(7, 'CAN', 'Canada'),
(8, 'UAE', 'United Arab Emirates'),
(9, 'SGP', 'Singapore'),
(10, 'PHL', 'Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `travel_bookings`
--

CREATE TABLE `travel_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `traveler_name` varchar(100) NOT NULL,
  `travel_type` enum('Airplane','Ship','Bus','Train','Car') NOT NULL,
  `from_country` varchar(100) DEFAULT NULL,
  `from_city` varchar(50) NOT NULL,
  `to_country` varchar(100) DEFAULT NULL,
  `to_city` varchar(50) NOT NULL,
  `status` enum('Booked','Cancelled','Completed') DEFAULT 'Booked',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_bookings`
--

INSERT INTO `travel_bookings` (`id`, `booking_id`, `traveler_name`, `travel_type`, `from_country`, `from_city`, `to_country`, `to_city`, `status`, `booking_date`) VALUES
(1, 'TRV-20251209-177', 'Paolo', 'Airplane', 'United State', 'New York', 'Philippines', 'Manila', 'Booked', '2025-12-09 04:42:38'),
(2, 'TRV-20251209-406', 'Paolo Madridano', 'Ship', 'USA', 'New York', 'France', 'Marseille', 'Cancelled', '2025-12-09 04:54:03'),
(3, 'TRV-20251209-452', 'James Harden', 'Train', 'Philippines', 'Manila', 'Philippines', 'Cagayan de Oro', 'Completed', '2025-12-09 04:54:30'),
(4, 'TRV-20251209-179', 'Boang Na', 'Airplane', 'UK', 'Manchester', 'USA', 'Los Angeles', 'Booked', '2025-12-09 04:54:57'),
(5, 'TRV-20251209-663', 'Gabriel', 'Bus', 'Japan', 'Osaka', 'Japan', 'Yokohama', 'Completed', '2025-12-09 05:14:17'),
(6, 'TRV-20251209-471', 'HAHA', 'Car', 'Philippines', 'Bacolod', 'Philippines', 'Davao', 'Booked', '2025-12-09 05:14:39'),
(7, 'TRV-20251209-098', 'Test', 'Ship', 'France', 'Paris', 'Canada', 'Toronto', 'Cancelled', '2025-12-09 05:15:01'),
(8, 'TRV-20251209-882', 'Test1', 'Airplane', 'UAE', 'Dubai', 'Singapore', 'Singapore', 'Completed', '2025-12-09 05:20:27'),
(9, 'TRV-20251209-838', 'example', 'Train', 'USA', 'Las Vegas', 'USA', 'New York', 'Cancelled', '2025-12-09 05:20:39'),
(10, 'TRV-20251209-739', 'Poaola', 'Car', 'Germany', 'Frankfurt', 'Australia', 'Brisbane', 'Cancelled', '2025-12-09 05:21:00'),
(11, 'TRV-20251209-129', 'Gab', 'Ship', 'UK', 'Birmingham', 'USA', 'Los Angeles', 'Booked', '2025-12-09 05:21:11'),
(12, 'TRV-20251209-229', 'Travel', 'Car', 'Canada', 'Calgary', 'Japan', 'Osaka', 'Booked', '2025-12-09 05:21:29'),
(13, 'TRV-20251209-646', 'TEST', 'Train', 'UAE', 'Ras Al Khaimah', 'USA', 'Miami', 'Booked', '2025-12-09 05:21:45'),
(14, 'TRV-20251209-710', 'Gabriel2', 'Airplane', 'Philippines', 'Davao', 'France', 'Nice', 'Completed', '2025-12-09 05:21:59'),
(15, 'TRV-20251209-616', 'Paolo', 'Ship', 'France', 'Nice', 'Japan', 'Kyoto', 'Completed', '2025-12-09 07:27:27'),
(16, 'TRV-20251209-183', 'Paolo', 'Ship', 'USA', 'Los Angeles', 'Philippines', 'Cagayan de Oro', 'Completed', '2025-12-09 07:28:05'),
(17, 'TRV-20251209-956', 'Paolo', 'Car', 'Australia', 'Adelaide', 'Canada', 'Montreal', 'Completed', '2025-12-09 07:28:15'),
(18, 'TRV-20251209-886', 'Paolo', 'Bus', 'Philippines', 'Cagayan de Oro', 'Germany', 'Berlin', 'Completed', '2025-12-09 07:29:04'),
(19, 'TRV-20251209-484', 'Gabriel', 'Ship', 'UK', 'Manchester', 'USA', 'Las Vegas', 'Completed', '2025-12-09 07:29:15'),
(20, 'TRV-20251210-072', 'Paolo', 'Ship', 'Japan', 'Kyoto', 'Philippines', 'Davao', 'Cancelled', '2025-12-10 03:18:23'),
(21, 'TRV-20251210-085', 'Pawix', 'Car', 'Australia', 'Melbourne', 'USA', 'Las Vegas', 'Booked', '2025-12-10 03:18:39'),
(22, 'TRV-20251210-566', 'KO', 'Ship', 'UK', 'London', 'Germany', 'Cologne', 'Completed', '2025-12-10 03:18:55'),
(23, 'TRV-20251210-844', 'Gabriel', 'Ship', 'France', 'Paris', 'Philippines', 'Cagayan de Oro', 'Completed', '2025-12-10 03:29:51'),
(24, 'TRV-20251210-165', 'Gabriel', 'Bus', 'UK', 'Manchester', 'UK', 'Birmingham', 'Completed', '2025-12-10 03:30:07'),
(25, 'TRV-20251210-008', 'Gabriel', 'Car', 'Philippines', 'Bacolod', 'Philippines', 'Cebu', 'Booked', '2025-12-10 03:30:18'),
(26, 'TRV-20251210-610', 'James', 'Airplane', 'Singapore', 'Singapore', 'UK', 'London', 'Cancelled', '2025-12-10 05:10:48'),
(27, 'TRV-20251210-003', 'Paolo', 'Train', 'Japan', 'Nagoya', 'Japan', 'Tokyo', 'Completed', '2025-12-10 05:10:59'),
(28, 'TRV-20251210-864', 'James ', 'Airplane', 'UK', 'Edinburgh', 'UAE', 'Sharjah', 'Cancelled', '2025-12-10 05:11:13'),
(29, 'TRV-20251210-417', 'KO', 'Airplane', 'UK', 'Manchester', 'Canada', 'Montreal', 'Completed', '2025-12-10 05:13:19'),
(30, 'TRV-20251210-035', 'KO', 'Train', 'Philippines', 'Davao', 'Japan', 'Nagoya', 'Cancelled', '2025-12-10 05:13:29'),
(31, 'TRV-20251210-521', 'James', 'Bus', 'Canada', 'Vancouver', 'Philippines', 'Cebu', 'Booked', '2025-12-10 05:13:40'),
(32, 'TRV-20251210-581', 'James', 'Airplane', 'USA', 'New York', 'France', 'Toulouse', 'Completed', '2025-12-10 05:13:48'),
(33, 'TRV-20251210-325', 'Pawix', 'Car', 'Australia', 'Perth', 'Australia', 'Perth', 'Cancelled', '2025-12-10 05:13:59'),
(34, 'TRV-20251210-425', 'Gabriel', 'Airplane', 'UK', 'Manchester', 'USA', 'Chicago', 'Cancelled', '2025-12-10 05:14:08'),
(35, 'TRV-20251210-050', 'Gabriel', 'Car', 'Japan', 'Osaka', 'Japan', 'Tokyo', 'Cancelled', '2025-12-10 05:17:32'),
(36, 'TRV-20251211-341', 'Gabriel', 'Bus', 'USA', 'Chicago', 'Japan', 'Osaka', 'Cancelled', '2025-12-11 10:34:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_country` (`country_code`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `country_name` (`country_name`),
  ADD UNIQUE KEY `country_code` (`country_code`);

--
-- Indexes for table `travel_bookings`
--
ALTER TABLE `travel_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `travel_bookings`
--
ALTER TABLE `travel_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
