-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 03:42 PM
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
-- Table structure for table `available_hotels`
--

CREATE TABLE `available_hotels` (
  `id` int(11) NOT NULL,
  `hotel_name` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `rating` decimal(2,1) DEFAULT 5.0,
  `price_per_night` decimal(10,2) NOT NULL,
  `amenities` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `available_rooms` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `available_hotels`
--

INSERT INTO `available_hotels` (`id`, `hotel_name`, `city`, `country`, `rating`, `price_per_night`, `amenities`, `description`, `available_rooms`, `created_at`) VALUES
(1, 'Grand Hyatt Tokyo', 'Tokyo', 'Japan', 4.8, 6500.00, 'Free WiFi, Pool, Spa, Restaurant', 'Luxury hotel in the heart of Tokyo with stunning city views', 15, '2026-01-13 14:02:35'),
(2, 'Marina Bay Sands', 'Singapore', 'Singapore', 4.9, 8500.00, 'Infinity Pool, Casino, Shopping, Spa', 'Iconic hotel with rooftop infinity pool overlooking the city', 12, '2026-01-13 14:02:35'),
(3, 'The Ritz London', 'London', 'United Kingdom', 4.7, 12000.00, 'Afternoon Tea, Spa, Fine Dining, Concierge', 'Historic luxury hotel in Piccadilly with royal heritage', 8, '2026-01-13 14:02:35'),
(4, 'Hotel Plaza Athénée', 'Paris', 'France', 4.8, 9500.00, 'Michelin Restaurant, Spa, Fashion Boutiques', 'Elegant Parisian palace hotel on Avenue Montaigne', 10, '2026-01-13 14:02:35'),
(5, 'Burj Al Arab', 'Dubai', 'UAE', 5.0, 15000.00, 'Private Beach, Helicopter Pad, Butler Service', 'World\'s most luxurious hotel shaped like a sail', 6, '2026-01-13 14:02:35'),
(6, 'The Peninsula Hong Kong', 'Hong Kong', 'Hong Kong', 4.6, 7500.00, 'Harbor Views, Spa, Rooftop Bar', 'Classic luxury hotel with Victoria Harbor views', 14, '2026-01-13 14:02:35'),
(7, 'Four Seasons Bali', 'Bali', 'Indonesia', 4.9, 5500.00, 'Private Villas, Beach Access, Spa', 'Tropical paradise resort with traditional Balinese design', 20, '2026-01-13 14:02:35'),
(8, 'The St. Regis New York', 'New York', 'USA', 4.7, 11000.00, 'Central Park Views, Butler Service, Fine Dining', 'Timeless luxury in the heart of Manhattan', 9, '2026-01-13 14:02:35'),
(9, 'Mandarin Oriental Bangkok', 'Bangkok', 'Thailand', 4.8, 4500.00, 'River Views, Thai Spa, Cultural Tours', 'Riverside luxury with authentic Thai hospitality', 18, '2026-01-13 14:02:35'),
(10, 'The Westin Sydney', 'Sydney', 'Australia', 4.5, 6000.00, 'Harbor Bridge Views, Pool, Fitness Center', 'Modern hotel with iconic Sydney Harbor views', 16, '2026-01-13 14:02:35'),
(11, 'Hotel Danieli Venice', 'Venice', 'Italy', 4.6, 8800.00, 'Canal Views, Historic Palace, Fine Dining', 'Venetian palace hotel on the Grand Canal', 7, '2026-01-13 14:02:35'),
(12, 'The Oberoi Mumbai', 'Mumbai', 'India', 4.7, 4800.00, 'Ocean Views, Spa, Business Center', 'Contemporary luxury overlooking the Arabian Sea', 13, '2026-01-13 14:02:35'),
(13, 'Shangri-La Hotel Paris', 'Paris', 'France', 4.5, 7200.00, 'Eiffel Tower Views, Asian Spa, Gourmet Restaurant', 'Asian hospitality meets Parisian elegance', 11, '2026-01-13 14:02:35'),
(14, 'The Langham London', 'London', 'United Kingdom', 4.4, 5800.00, 'Traditional Afternoon Tea, Spa, Historic Charm', 'Victorian grandeur in the heart of London', 12, '2026-01-13 14:02:35'),
(15, 'Park Hyatt Tokyo', 'Tokyo', 'Japan', 4.9, 9800.00, 'Mount Fuji Views, Minimalist Design, Spa', 'Serene luxury with Japanese aesthetics', 8, '2026-01-13 14:02:35'),
(16, 'The St. Regis Maldives', 'Male', 'Maldives', 5.0, 18000.00, 'Overwater Villas, Private Beach, Water Sports', 'Ultimate tropical luxury in crystal clear waters', 5, '2026-01-13 14:02:35'),
(17, 'Raffles Hotel Singapore', 'Singapore', 'Singapore', 4.8, 14000.00, 'Colonial Heritage, Long Bar, Tropical Gardens', 'Legendary colonial hotel with timeless elegance', 6, '2026-01-13 14:02:35'),
(18, 'The Savoy London', 'London', 'United Kingdom', 4.6, 10500.00, 'Thames Views, Art Deco Style, Famous Bar', 'Iconic luxury hotel on the Strand', 9, '2026-01-13 14:02:35'),
(19, 'Aman Tokyo', 'Tokyo', 'Japan', 4.9, 16000.00, 'Zen Design, City Views, Holistic Spa', 'Urban sanctuary with traditional Japanese elements', 4, '2026-01-13 14:02:35'),
(20, 'Conrad Maldives', 'Male', 'Maldives', 4.8, 22000.00, 'Underwater Restaurant, Dolphin Lagoon, Spa', 'Luxury resort on pristine coral atoll', 3, '2026-01-13 14:02:35');

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
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_rentals`
--

INSERT INTO `car_rentals` (`id`, `booking_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_age`, `pickup_date`, `dropoff_date`, `pickup_time`, `dropoff_time`, `pickup_location`, `dropoff_location`, `car_type`, `car_model`, `car_image`, `rental_days`, `daily_rate`, `subtotal`, `insurance_fee`, `additional_fees`, `total_amount`, `promo_code`, `discount_amount`, `agent_id`, `agent_commission`, `status`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 'CAR-20260107-457060', 'Paolo', 'pmadridano@gmail.com', '096555214545', 21, '0000-00-00', '0000-00-00', '00:00:00', '00:00:00', '', '', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 8, 2850.00, 22800.00, 0.00, 0.00, 22800.00, '', 0.00, 'AGT001', 2850.00, 'Confirmed', 'Pay at Pickup', 'Pending', '2026-01-07 02:49:08', '2026-01-07 02:49:08'),
(11, 'CAR-20260107-2087BC', 'Paolo', 'pmadridano@gmail.com', '096555214545', 30, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'MRT Taft Station', 'Any Hotel in Metro Manila (Hotel Delivery)', 'SUV', 'Ford Mustang or Similar', 'assets/img/cars/ford-mustang.jpg', 1, 5108.00, 5108.00, 0.00, 0.00, 5108.00, '', 0.00, NULL, 0.00, 'Confirmed', 'Pay at Pickup', 'Pending', '2026-01-07 03:04:02', '2026-01-07 03:04:02'),
(12, 'CAR-20260107-8EAF81', 'Madridano Gabriel', 'pmadridano@gmail.com', '096555214545', 20, '2026-01-10', '2026-01-11', '14:03:00', '22:01:00', 'Pasay City - Mall of Asia', 'Makati City Center', 'Electric', 'Tesla Model 3 or Similar', 'assets/img/cars/tesla-model3.jpg', 1, 4250.00, 4250.00, 600.00, 800.00, 5650.00, '', 0.00, NULL, 0.00, 'Confirmed', 'PayPal', 'Pending', '2026-01-07 03:04:56', '2026-01-07 03:04:56'),
(13, 'CAR-20260107-359E0B', 'POPOP', 'pmadridano@gmail.com', '096555214545', 23, '2026-01-10', '2026-01-11', '10:00:00', '10:00:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'MRT Ayala Station', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 1, 3650.00, 3650.00, 300.00, 500.00, 4450.00, '', 0.00, 'AGT2306', 890.00, 'Confirmed', 'GCash', 'Pending', '2026-01-07 04:30:11', '2026-01-07 04:30:11'),
(14, 'CAR-20260107-0A2ACE', 'POPOP', 'pmadridano@gmail.com', '096555214545', 42, '2026-01-07', '2026-01-16', '10:00:00', '10:00:00', 'Mandaluyong City - Ortigas Center', 'PITX - Parañaque Integrated Terminal Exchange', 'Economy', 'Toyota Corolla or Similar', 'assets/img/cars/toyota-corolla.jpg', 9, 3150.00, 28350.00, 8100.00, 9000.00, 45450.00, '', 0.00, 'AGT001', 5681.25, 'Confirmed', 'Debit Card', 'Pending', '2026-01-07 04:30:40', '2026-01-07 04:30:40'),
(15, 'CAR-20260107-A837D3', 'Paolo', 'pmadridano@gmail.com', '096553222', 32, '2026-01-07', '2026-01-15', '10:00:00', '10:00:00', 'CEB - Mactan-Cebu International Airport', 'Bonifacio Global City (BGC)', 'Luxury', 'BMW X7 or Similar', 'assets/img/cars/bmw-x7.jpg', 8, 5878.00, 47024.00, 7200.00, 6400.00, 60624.00, '', 0.00, 'AGT001', 7578.00, 'Confirmed', 'Credit Card', 'Pending', '2026-01-07 04:31:06', '2026-01-07 04:31:06'),
(16, 'CAR-20260107-7CDF36', 'Gabriel Paolo Madridano', 'pmadridano2@gmail.com', '09619490469', 32, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'DVO - Francisco Bangoy International Airport', 'LRT Buendia Station', 'Electric', 'Tesla Model 3 or Similar', 'assets/img/cars/tesla-model3.jpg', 1, 4250.00, 4250.00, 900.00, 1000.00, 6150.00, '', 0.00, 'AGT001', 768.75, 'Confirmed', 'Debit Card', 'Pending', '2026-01-07 05:23:51', '2026-01-07 05:23:51'),
(17, 'CAR-20260107-93F174', 'James', 'james@gmail.com', '09321321333', 21, '2026-01-09', '2026-01-23', '10:00:00', '10:00:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'PITX - Parañaque Integrated Terminal Exchange', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 14, 3650.00, 51100.00, 0.00, 0.00, 51100.00, '', 0.00, 'AGT2306', 10220.00, 'Confirmed', 'Debit Card', 'Pending', '2026-01-07 05:24:25', '2026-01-07 05:24:25'),
(18, 'CAR-20260107-E6DC28', 'Paolo', 'pmadridano@gmail.com', '09545521115', 20, '2026-01-07', '2026-01-09', '10:00:00', '13:30:00', 'CEB - Mactan-Cebu International Airport', 'DVO - Francisco Bangoy International Airport', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 2, 3650.00, 7300.00, 1200.00, 1600.00, 10100.00, '', 0.00, NULL, 0.00, 'Confirmed', 'Pay at Pickup', 'Pending', '2026-01-07 05:25:18', '2026-01-07 05:25:18'),
(19, 'CAR-20260107-3B78DA', 'Paolo', 'pmadridano@gmail.com', '096555214545', 30, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'MRT Taft Station', 'Makati City Center', 'SUV', 'Ford Mustang or Similar', 'assets/img/cars/ford-mustang.jpg', 1, 5108.00, 5108.00, 600.00, 500.00, 6208.00, '', 0.00, 'AGT2306', 1241.60, 'Confirmed', 'GCash', 'Pending', '2026-01-07 06:02:11', '2026-01-07 06:02:11'),
(20, 'CAR-20260107-AE2928', 'Paolo', 'pmadridano@gmail.com', '096555214545', 20, '2026-01-07', '2026-01-08', '10:00:00', '10:00:00', 'MNL - Manila Ninoy Aquino International Airport', 'Quezon City - Cubao', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 1, 2850.00, 2850.00, 300.00, 200.00, 3350.00, '', 0.00, 'AGT2306', 670.00, 'Confirmed', 'Credit Card', 'Pending', '2026-01-07 06:10:50', '2026-01-07 06:10:50'),
(21, 'CAR-20260113-AA6D62', 'POPOP', 'pmadridano@gmail.com', '096555214545', 23, '2026-01-13', '2026-01-14', '10:00:00', '10:00:00', 'PITX - Parañaque Integrated Terminal Exchange', 'PITX - Parañaque Integrated Terminal Exchange', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 1, 2850.00, 2850.00, 0.00, 500.00, 3350.00, '', 0.00, 'AGT001', 418.75, 'Confirmed', 'Debit Card', 'Pending', '2026-01-13 12:39:54', '2026-01-13 12:39:54'),
(22, 'CAR-20260113-3599A9', 'Paolo', 'pmadridano@gmail.com', '096555214545', 32, '2026-01-13', '2026-01-14', '10:00:00', '10:00:00', 'Shangri-La Hotel', 'Shangri-La Hotel', 'Compact', 'Honda Civic or Similar', 'assets/img/cars/honda-civic.jpg', 1, 3650.00, 3650.00, 300.00, 300.00, 4250.00, '', 0.00, 'AGT2306', 850.00, 'Confirmed', 'Credit Card', 'Pending', '2026-01-13 12:41:39', '2026-01-13 12:41:39'),
(23, 'CAR-20260113-468695', 'Paolo', 'pmadridano@gmail.com', '096555214545', 32, '2026-01-13', '2026-01-15', '10:00:00', '02:00:00', 'PITX - Parañaque Integrated Terminal Exchange', 'PITX - Parañaque Integrated Terminal Exchange', 'Economy', 'Kia Rio or Similar', 'assets/img/cars/kia-rio.jpg', 2, 2850.00, 5700.00, 1200.00, 1600.00, 8500.00, '', 0.00, NULL, 0.00, 'Confirmed', 'Pay at Pickup', 'Pending', '2026-01-13 12:43:48', '2026-01-13 12:43:48');

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
-- Table structure for table `cruises`
--

CREATE TABLE `cruises` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `cruise_line` varchar(100) NOT NULL,
  `ship_name` varchar(100) NOT NULL,
  `itinerary_name` varchar(200) NOT NULL,
  `departure_port` varchar(100) NOT NULL,
  `arrival_port` varchar(100) NOT NULL,
  `departure_date` date NOT NULL,
  `arrival_date` date NOT NULL,
  `duration_nights` int(11) NOT NULL,
  `ports_of_call` text DEFAULT NULL,
  `cabin_type` varchar(50) NOT NULL,
  `deck_number` varchar(20) DEFAULT NULL,
  `cabin_number` varchar(20) DEFAULT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `guest_phone` varchar(20) NOT NULL,
  `guest_count` int(11) NOT NULL DEFAULT 1,
  `room_count` int(11) NOT NULL DEFAULT 1,
  `base_price` decimal(10,2) NOT NULL,
  `port_fees` decimal(10,2) DEFAULT 0.00,
  `taxes` decimal(10,2) DEFAULT 0.00,
  `gratuities` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `agent_id` varchar(50) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `special_requests` text DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `passport_expiry` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cruises`
--

INSERT INTO `cruises` (`id`, `booking_id`, `cruise_line`, `ship_name`, `itinerary_name`, `departure_port`, `arrival_port`, `departure_date`, `arrival_date`, `duration_nights`, `ports_of_call`, `cabin_type`, `deck_number`, `cabin_number`, `guest_name`, `guest_email`, `guest_phone`, `guest_count`, `room_count`, `base_price`, `port_fees`, `taxes`, `gratuities`, `total_amount`, `promo_code`, `discount_amount`, `agent_id`, `agent_commission`, `status`, `payment_method`, `payment_status`, `special_requests`, `passport_number`, `passport_expiry`, `created_at`, `updated_at`) VALUES
(1, 'CRU-20260107-F0F61B', 'Dream Cruises', 'Genting Dream', '3-Night Southeast Asia Cruise: Singapore - Melaka - Singapore', 'Singapore (Marina Bay Cruise Centre)', 'Singapore', '2026-02-06', '2026-02-08', 2, 'Singapore, Melaka, Malaysia, Singapore', 'suite', NULL, NULL, 'Paolo', 'gama.@gmail.com', '09231232131', 2, 1, 44800.00, 2800.00, 4480.00, 6720.00, 58800.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'Debit Card', 'Pending', 'Coke', 'n/a', NULL, '2026-01-07 04:26:07', '2026-01-07 04:26:07'),
(2, 'CRU-20260107-7B48F1', 'Royal Caribbean International', 'Symphony of the Seas', '7-Night Caribbean Cruise', 'Miami, Florida', 'St. Maarten', '2026-02-06', '2026-02-13', 7, 'Miami, Nassau, Bahamas, CocoCay, St. Thomas, St. Maarten', 'villa', NULL, NULL, 'James', 'Hja@gmail.com', '09231232131', 1, 1, 235200.00, 3360.00, 23520.00, 35280.00, 297360.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'Debit Card', 'Pending', 'Hja@gmail.com', NULL, NULL, '2026-01-07 04:26:47', '2026-01-07 04:26:47'),
(3, 'CRU-20260107-F7F6C0', 'Dream Cruises', 'Genting Dream', '4-Night Southeast Asia Cruise: Singapore - Phuket - Singapore', 'Singapore (Marina Bay Cruise Centre)', 'Singapore', '2026-02-06', '2026-02-09', 3, 'Singapore, Phuket, Thailand, Singapore', 'interior', NULL, NULL, 'Paolo', 'Hja@gmail.com', '09231232131', 4, 1, 16800.00, 2800.00, 1680.00, 2520.00, 23800.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'Pay Later', 'Pending', 'ail.com', NULL, NULL, '2026-01-07 04:27:11', '2026-01-07 04:27:11'),
(4, 'CRU-20260107-059D8F', 'Dream Cruises', 'Genting Dream', '4-Night Southeast Asia Cruise: Singapore - Phuket - Singapore', 'Singapore (Marina Bay Cruise Centre)', 'Singapore', '2026-02-06', '2026-02-09', 3, 'Singapore, Phuket, Thailand, Singapore', 'balcony', '16', '152', 'Paolo', 'gama.@gmail.com', '09231232131', 4, 2, 67200.00, 2800.00, 6720.00, 10080.00, 86800.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'Pay Later', 'Pending', '03', NULL, NULL, '2026-01-07 04:27:44', '2026-01-07 04:27:44'),
(5, 'CRU-20260107-76C8F1', 'Royal Caribbean International', 'Symphony of the Seas', '7-Night Caribbean Cruise', 'Miami, Florida', 'St. Maarten', '2026-02-06', '2026-02-13', 7, 'Miami, Nassau, Bahamas, CocoCay, St. Thomas, St. Maarten', 'villa', '20', '5755', 'Paolo', 'gama.@gmail.com', '09231232131', 2, 2, 470400.00, 3360.00, 47040.00, 70560.00, 591360.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'Bank Transfer', 'Pending', '', NULL, NULL, '2026-01-07 04:28:07', '2026-01-07 04:28:07'),
(6, 'CRU-20260107-EC3833', 'Dream Cruises', 'Genting Dream', '3-Night Southeast Asia Cruise: Singapore - Melaka - Singapore', 'Singapore (Marina Bay Cruise Centre)', 'Singapore', '2026-02-06', '2026-02-08', 2, 'Singapore, Melaka, Malaysia, Singapore', 'oceanview', '17', '1247', 'PAWIX', 'gama.@gmail.com', '09231232131', 2, 5, 84000.00, 2800.00, 8400.00, 12600.00, 107800.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'PayPal', 'Pending', '', NULL, NULL, '2026-01-07 04:28:30', '2026-01-07 04:28:30'),
(7, 'CRU-20260107-4DDD98', 'Royal Caribbean International', 'Symphony of the Seas', '7-Night Caribbean Cruise', 'Miami, Florida', 'St. Maarten', '2026-02-06', '2026-02-13', 7, 'Miami, Nassau, Bahamas, CocoCay, St. Thomas, St. Maarten', 'balcony', '18', '7844', 'PAWIX', 'gama.@gmail.com', '09231232131', 2, 2, 156800.00, 3360.00, 15680.00, 23520.00, 199360.00, NULL, 0.00, 'AGT2306', 39872.00, 'Confirmed', 'Debit Card', 'Pending', '', NULL, NULL, '2026-01-07 04:29:08', '2026-01-07 04:29:08'),
(8, 'CRU-20260107-294C6C', 'Royal Caribbean International', 'Symphony of the Seas', '7-Night Caribbean Cruise', 'Miami, Florida', 'St. Maarten', '2026-02-06', '2026-02-13', 7, 'Miami, Nassau, Bahamas, CocoCay, St. Thomas, St. Maarten', 'balcony', '5', '1322', 'Kilo', 'loki@gmail.com', '0954646412', 1, 2, 156800.00, 3360.00, 15680.00, 23520.00, 199360.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'Credit Card', 'Pending', 'saeqseqweasd', NULL, NULL, '2026-01-07 05:25:54', '2026-01-07 05:25:54'),
(9, 'CRU-20260113-8C9E09', 'Dream Cruises', 'Genting Dream', '3-Night Southeast Asia Cruise: Singapore - Melaka - Singapore', 'Singapore (Marina Bay Cruise Centre)', 'Singapore', '2026-02-12', '2026-02-14', 2, 'Singapore, Melaka, Malaysia, Singapore', 'interior', '18', '152', 'PAWIX', 'gama.@gmail.com', '09231232131', 2, 2, 22400.00, 2800.00, 2240.00, 3360.00, 30800.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'Pay Later', 'Pending', 'qweqweq', 'n/a', NULL, '2026-01-13 12:43:20', '2026-01-13 12:43:20'),
(10, 'CRU-20260113-4EE1AC', 'Norwegian Cruise Line', 'Norwegian Joy', '7-Night Alaska Glacier Cruise', 'Seattle, Washington', 'Victoria, BC', '2026-02-12', '2026-02-19', 7, 'Seattle, Juneau, Skagway, Glacier Bay, Ketchikan, Victoria, BC', 'suite', '2', '5755', 'PAWIX', 'Hja@gmail.com', '09231232131', 2, 3, 470400.00, 5600.00, 47040.00, 70560.00, 593600.00, NULL, 0.00, NULL, 0.00, 'Confirmed', 'PayPal', 'Pending', 'weqwe', 'n/a', '2026-01-23', '2026-01-13 14:23:32', '2026-01-13 14:23:32');

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
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `seat_class` enum('Economy','Premium Economy','Business','First Class') NOT NULL,
  `seat_number` varchar(10) DEFAULT NULL,
  `passengers` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `agent_id` varchar(20) DEFAULT NULL,
  `agent_commission` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Boarded','Completed','Cancelled') DEFAULT 'Pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flight_bookings`
--

INSERT INTO `flight_bookings` (`id`, `booking_id`, `customer_id`, `passenger_name`, `airline`, `flight_number`, `departure_airport`, `arrival_airport`, `departure_date`, `arrival_date`, `departure_time`, `arrival_time`, `seat_class`, `seat_number`, `passengers`, `total_amount`, `agent_id`, `agent_commission`, `status`, `booking_date`) VALUES
(1, 'FLT-20260103-938', NULL, 'Paolo', 'Cebu Pacific', 'PR 100', 'Cebu (CEB)', 'NRT - Tokyo', '2026-01-04 06:00:00', '2026-01-04 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 40000.00, '', 0.00, 'Confirmed', '2026-01-03 02:44:04'),
(2, 'FLT-20260103-825', NULL, 'king', 'Philippine Airlines', 'FC 100', 'Cebu (CEB)', 'Japan', '2026-01-04 06:00:00', '2026-01-04 11:00:00', NULL, NULL, 'First Class', NULL, 1, 8923.00, 'AGT002', 892.30, 'Completed', '2026-01-03 03:14:32'),
(3, 'FLT-20260107-414', NULL, 'polo', 'Cebu Pacific', 'PR 111', 'Manila (MNL)', 'NRT - Tokyo', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 6200.00, 'AGT2306', 1240.00, 'Completed', '2026-01-07 05:47:17'),
(4, 'FLT-20260107-629', NULL, 'Paolo', 'Philippine Airlines', 'FC 154', 'Cebu (CEB)', 'Japan', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Business', NULL, 1, 2450.00, 'AGT001', 306.25, 'Completed', '2026-01-07 06:14:18'),
(5, 'FLT-20260107-222', NULL, 'james', 'Thai Airways', 'THA012', 'Bangkok (BKK)', 'NRT - POL', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 2322.00, '', 0.00, 'Boarded', '2026-01-07 07:01:52'),
(6, 'FLT-20260107-682', NULL, 'Kilio', 'Hong Kong Airlines', 'PR 203', 'Hong Kong (HKG)', 'Japan', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 2333.00, '', 0.00, 'Confirmed', '2026-01-07 07:02:14'),
(7, 'FLT-20260107-334', NULL, 'Kilio', 'Garuda Indonesia', 'VIP321', 'Jakarta (CGK)', 'Manila', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'First Class', NULL, 1, 53122.00, '', 0.00, 'Confirmed', '2026-01-07 07:02:49'),
(8, 'FLT-20260107-533', NULL, 'king', 'Korean Air', 'KIl012', 'Seoul (ICN)', 'NRT - Tokyo', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Business', NULL, 1, 4320.00, '', 0.00, 'Confirmed', '2026-01-07 07:03:15'),
(9, 'FLT-20260107-109', NULL, 'king', 'Cathay Pacific', 'HKG045', 'Hong Kong (HKG)', 'BKK-Bangkok', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 6500.00, '', 0.00, 'Completed', '2026-01-07 07:03:53'),
(10, 'FLT-20260107-362', NULL, 'Paolo', 'Singapore Airlines', 'PR 422', 'Singapore (SIN)', 'ICN-Seoul', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'First Class', NULL, 1, 9000.00, '', 0.00, 'Boarded', '2026-01-07 07:04:25'),
(11, 'FLT-20260107-057', NULL, 'Kilio', 'All Nippon Airways', 'FC 152', 'Tokyo (NRT)', 'ICN-Seoul', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Business', NULL, 1, 6550.00, '', 0.00, 'Confirmed', '2026-01-07 07:04:47'),
(12, 'FLT-20260107-953', NULL, 'king', 'Garuda Indonesia', 'THA012', 'Jakarta (CGK)', 'HKG-Hongkong', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 4631.00, '', 0.00, 'Confirmed', '2026-01-07 07:05:14'),
(13, 'FLT-20260107-820', NULL, 'Kilio', 'Korean Air', 'FC 123', 'Seoul (ICN)', 'BKK-Bangkok', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 4500.00, '', 0.00, 'Confirmed', '2026-01-07 07:05:35'),
(14, 'FLT-20260107-147', NULL, 'james', 'Cebu Pacific', 'FC 157', 'Cebu (CEB)', 'NRT - Tokyo', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'First Class', NULL, 1, 15000.00, 'AGT001', 1875.00, 'Confirmed', '2026-01-07 07:05:56'),
(15, 'FLT-20260107-612', NULL, 'james', 'Malaysia Airlines', 'THA022', 'Kuala Lumpur (KUL)', 'CEB-Cebu', '2026-01-08 06:00:00', '2026-01-01 11:12:00', NULL, NULL, 'Premium Economy', NULL, 1, 24.00, '', 0.00, 'Completed', '2026-01-07 07:06:43'),
(16, 'FLT-20260107-798', NULL, 'king', 'Philippine Airlines', 'PR 009', 'Davao (DVO)', 'CEB-Cebu', '2026-01-07 06:00:00', '2026-01-08 07:00:00', NULL, NULL, 'Economy', NULL, 1, 600.00, '', 0.00, 'Confirmed', '2026-01-07 07:07:30'),
(17, 'FLT-20260107-284', NULL, 'Paolo', 'AirAsia', 'PR 546', 'Kuala Lumpur (KUL)', 'ICN-Seoul', '2026-01-08 06:00:00', '2026-01-08 11:00:00', NULL, NULL, 'Premium Economy', NULL, 1, 9000.00, '', 0.00, 'Confirmed', '2026-01-07 07:07:56'),
(18, 'FLT-20260113-080', NULL, 'Paolo', 'ANA', 'PR 100', 'MNL', 'HND', '2026-01-13 19:28:00', '2026-01-05 19:29:00', NULL, NULL, 'Business', '14A', 1, 1500.00, NULL, 0.00, 'Completed', '2026-01-13 11:26:14'),
(19, 'FLT-20260113-871', NULL, 'king', 'British Airways', 'PR 013', 'MNL', 'JFK', '2026-02-14 06:00:00', '2026-02-17 19:28:00', NULL, NULL, 'Business', '14AB', 1, 12300.00, NULL, 0.00, 'Cancelled', '2026-01-13 11:28:25'),
(20, 'FLT-2026011313093872', NULL, 'Paolo', 'Scoot', 'SQ 915', 'SIN', 'MNL', '2026-01-13 20:08:00', '2026-01-23 11:00:00', NULL, NULL, 'Business', NULL, 3, 840.00, NULL, 0.00, 'Confirmed', '2026-01-13 12:09:38'),
(21, 'FLT-2026011313275180', NULL, 'Paolo', 'Philippine Airlines', 'PR 508', 'SIN', 'MNL', '2026-01-13 13:32:00', '2026-01-13 23:35:00', NULL, NULL, 'Business', NULL, 4, 1000.00, NULL, 0.00, 'Confirmed', '2026-01-13 12:27:51'),
(22, 'FLT-2026011313311025', NULL, 'Paolo', 'Cathay Pacific', 'CX 710', 'DXB', 'MNL', '2026-01-14 11:00:00', '2026-01-15 00:10:00', NULL, NULL, 'First Class', NULL, 3, 1050.00, NULL, 0.00, 'Confirmed', '2026-01-13 12:31:10'),
(23, 'FLT-2026011313341320', NULL, 'james', 'Emirates', 'EK 332', 'LAX', 'NRT', '2026-02-06 00:00:00', '0000-00-00 00:00:00', NULL, NULL, 'Economy', NULL, 1, 420.00, NULL, 0.00, 'Confirmed', '2026-01-13 12:34:13'),
(24, 'FLT-2026011313353829', NULL, 'james', 'Philippine Airlines', 'PR 507', 'MNL', 'SIN', '2026-01-21 00:00:00', '0000-00-00 00:00:00', NULL, NULL, 'Economy', NULL, 3, 750.00, NULL, 0.00, 'Completed', '2026-01-13 12:35:38'),
(25, 'FLT-2026011315364520', NULL, 'james', 'Emirates', 'EK 332', 'NRT', 'SIN', '2026-01-14 00:00:00', '0000-00-00 00:00:00', NULL, NULL, 'Economy', NULL, 2, 840.00, NULL, 0.00, 'Confirmed', '2026-01-13 14:36:45'),
(26, 'FLT-2026011315383376', NULL, 'james', 'Emirates', 'EK 332', 'NRT', 'SIN', '2026-01-14 00:00:00', '2026-01-14 00:00:00', '14:20:00', '16:30:00', 'Economy', NULL, 2, 840.00, NULL, 0.00, 'Confirmed', '2026-01-13 14:38:33'),
(27, 'FLT-2026011315395290', NULL, 'james', 'Emirates', 'EK 332', 'NRT', 'SIN', '2026-01-14 00:00:00', '2026-01-14 00:00:00', '14:20:00', '16:30:00', 'Economy', NULL, 2, 840.00, NULL, 0.00, 'Confirmed', '2026-01-13 14:39:52'),
(28, 'FLT-2026011315411522', NULL, 'polo', 'Qatar Airways', 'QR 931', 'MNL', 'SIN', '2026-01-13 00:00:00', '2026-01-14 00:00:00', '16:45:00', '20:15:00', 'Economy', NULL, 1, 350.00, NULL, 0.00, 'Confirmed', '2026-01-13 14:41:15');

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

--
-- Dumping data for table `hotel_bookings`
--

INSERT INTO `hotel_bookings` (`id`, `booking_id`, `customer_id`, `guest_name`, `hotel_name`, `country`, `city`, `room_type`, `check_in`, `check_out`, `guests`, `nights`, `total_amount`, `agent_id`, `agent_commission`, `status`, `booking_date`) VALUES
(22, 'HTL-20241215-001', 'CUST-001', 'John Smith', 'Grand Hyatt Tokyo', 'Japan', 'Tokyo', 'Deluxe', '2024-12-20', '2024-12-23', 2, 3, 18500.00, 'AGT-001', 925.00, 'Confirmed', '2026-01-13 13:52:40'),
(23, 'HTL-20241215-002', 'CUST-002', 'Maria Garcia', 'Marina Bay Sands', 'Singapore', 'Singapore', 'Suite', '2024-12-22', '2024-12-25', 2, 3, 25000.00, 'AGT-002', 1250.00, 'Confirmed', '2026-01-13 13:52:40'),
(24, 'HTL-20241215-003', 'CUST-003', 'David Wilson', 'The Ritz London', 'United Kingdom', 'London', 'Suite', '2024-12-18', '2024-12-21', 2, 3, 32000.00, 'AGT-001', 1600.00, 'Confirmed', '2026-01-13 13:52:40'),
(25, 'HTL-20241215-004', 'CUST-004', 'Sarah Johnson', 'Hotel Plaza Athénée', 'France', 'Paris', 'Deluxe', '2024-12-25', '2024-12-28', 2, 3, 28000.00, 'AGT-003', 1400.00, 'Confirmed', '2026-01-13 13:52:40'),
(26, 'HTL-20241215-005', 'CUST-005', 'Michael Brown', 'Burj Al Arab', 'UAE', 'Dubai', 'Suite', '2025-01-05', '2025-01-08', 2, 3, 45000.00, 'AGT-002', 2250.00, 'Confirmed', '2026-01-13 13:52:40'),
(27, 'HTL-20241215-006', 'CUST-006', 'Emma Davis', 'The Peninsula Hong Kong', 'Hong Kong', 'Hong Kong', 'Deluxe', '2024-12-30', '2025-01-02', 2, 3, 22000.00, 'AGT-001', 1100.00, 'Confirmed', '2026-01-13 13:52:40'),
(28, 'HTL-20241215-007', 'CUST-007', 'James Miller', 'Atlantis The Palm', 'UAE', 'Dubai', 'Family', '2025-01-10', '2025-01-14', 4, 4, 35000.00, 'AGT-003', 1750.00, 'Confirmed', '2026-01-13 13:52:40'),
(29, 'HTL-20241215-008', 'CUST-008', 'Lisa Anderson', 'Four Seasons Bali', 'Indonesia', 'Bali', 'Suite', '2025-01-15', '2025-01-18', 2, 3, 19500.00, 'AGT-002', 975.00, 'Confirmed', '2026-01-13 13:52:40'),
(30, 'HTL-20241215-009', 'CUST-009', 'Robert Taylor', 'The St. Regis New York', 'USA', 'New York', 'Deluxe', '2024-12-28', '2024-12-31', 2, 3, 38000.00, 'AGT-001', 1900.00, 'Confirmed', '2026-01-13 13:52:40'),
(31, 'HTL-20241215-010', 'CUST-010', 'Jennifer White', 'Mandarin Oriental Bangkok', 'Thailand', 'Bangkok', 'Double', '2025-01-08', '2025-01-11', 2, 3, 15000.00, 'AGT-003', 750.00, 'Confirmed', '2026-01-13 13:52:40'),
(32, 'HTL-20241215-011', 'CUST-011', 'Christopher Lee', 'The Westin Sydney', 'Australia', 'Sydney', 'Deluxe', '2025-01-12', '2025-01-15', 2, 3, 21000.00, 'AGT-002', 1050.00, 'Confirmed', '2026-01-13 13:52:40'),
(33, 'HTL-20241215-012', 'CUST-012', 'Amanda Clark', 'Hotel Danieli Venice', 'Italy', 'Venice', 'Suite', '2024-12-26', '2024-12-29', 2, 3, 26500.00, 'AGT-001', 1325.00, 'Confirmed', '2026-01-13 13:52:40'),
(34, 'HTL-20241215-013', 'CUST-013', 'Daniel Rodriguez', 'The Oberoi Mumbai', 'India', 'Mumbai', 'Deluxe', '2025-01-20', '2025-01-23', 2, 3, 16500.00, 'AGT-003', 825.00, 'Confirmed', '2026-01-13 13:52:40'),
(35, 'HTL-20241215-014', 'CUST-014', 'Michelle Martinez', 'Shangri-La Hotel Paris', 'France', 'Paris', 'Double', '2025-01-06', '2025-01-09', 2, 3, 18000.00, 'AGT-002', 900.00, 'Confirmed', '2026-01-13 13:52:40'),
(36, 'HTL-20241215-015', 'CUST-015', 'Kevin Thompson', 'The Langham London', 'United Kingdom', 'London', 'Single', '2024-12-24', '2024-12-27', 1, 3, 12000.00, 'AGT-001', 600.00, 'Confirmed', '2026-01-13 13:52:40'),
(37, 'HTL-20241215-016', 'CUST-016', 'Rachel Green', 'Park Hyatt Tokyo', 'Japan', 'Tokyo', 'Suite', '2025-01-25', '2025-01-28', 2, 3, 29000.00, 'AGT-003', 1450.00, 'Pending', '2026-01-13 13:52:40'),
(38, 'HTL-20241215-017', 'CUST-017', 'Mark Johnson', 'The Fullerton Hotel Singapore', 'Singapore', 'Singapore', 'Deluxe', '2025-02-01', '2025-02-04', 2, 3, 20000.00, 'AGT-002', 1000.00, 'Pending', '2026-01-13 13:52:40'),
(39, 'HTL-20241215-018', 'CUST-018', 'Laura Wilson', 'Rosewood Hong Kong', 'Hong Kong', 'Hong Kong', 'Suite', '2025-01-18', '2025-01-21', 2, 3, 31000.00, 'AGT-001', 1550.00, 'Confirmed', '2026-01-13 13:52:40'),
(40, 'HTL-20241215-019', 'CUST-019', 'Steven Brown', 'The Chedi Muscat', 'Oman', 'Muscat', 'Deluxe', '2025-02-05', '2025-02-08', 2, 3, 17500.00, 'AGT-003', 875.00, 'Pending', '2026-01-13 13:52:40'),
(41, 'HTL-20241215-020', 'CUST-020', 'Nicole Davis', 'Aman Tokyo', 'Japan', 'Tokyo', 'Suite', '2025-02-10', '2025-02-13', 2, 3, 42000.00, 'AGT-002', 2100.00, 'Confirmed', '2026-01-13 13:52:40'),
(42, 'HTL-20241215-021', 'CUST-021', 'Alexander Kim', 'The St. Regis Maldives', 'Maldives', 'Male', 'Suite', '2025-02-15', '2025-02-18', 2, 3, 55000.00, 'AGT-001', 2750.00, 'Confirmed', '2026-01-13 13:52:40'),
(43, 'HTL-20241215-022', 'CUST-022', 'Sophia Chen', 'Raffles Hotel Singapore', 'Singapore', 'Singapore', 'Suite', '2025-02-20', '2025-02-23', 2, 3, 48000.00, 'AGT-002', 2400.00, 'Confirmed', '2026-01-13 13:52:40'),
(44, 'HTL-20241215-023', 'CUST-023', 'William Zhang', 'The Savoy London', 'United Kingdom', 'London', 'Deluxe', '2025-02-25', '2025-02-28', 2, 3, 35000.00, 'AGT-003', 1750.00, 'Pending', '2026-01-13 13:52:40'),
(45, 'HTL-20260113-930', 'CUST-NEW', 'PAWIX', 'Burj Al Arab', 'UAE', 'Dubai', 'Suite', '2026-01-14', '2026-01-16', 2, 2, 30000.00, NULL, 0.00, 'Confirmed', '2026-01-13 14:02:46'),
(46, 'HTL-20260113-580', 'CUST-NEW', 'Paolo', 'Burj Al Arab', 'UAE', 'Dubai', 'Double', '2026-01-14', '2026-01-16', 2, 2, 30000.00, NULL, 0.00, 'Confirmed', '2026-01-13 14:03:08'),
(47, 'HTL-20260113-272', 'CUST-NEW', 'Paolo', 'The Westin Sydney', 'Australia', 'Sydney', 'Double', '2026-01-15', '2026-01-17', 2, 2, 12000.00, NULL, 0.00, 'Confirmed', '2026-01-13 14:16:54');

-- --------------------------------------------------------

--
-- Table structure for table `tour_activities`
--

CREATE TABLE `tour_activities` (
  `id` int(11) NOT NULL,
  `tour_id` varchar(20) NOT NULL,
  `tour_name` varchar(150) NOT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `tour_type` enum('Sightseeing','Adventure','Cultural','Food','Nature','Historical','Private','Group') DEFAULT 'Private',
  `tour_date` date NOT NULL,
  `duration_days` int(11) DEFAULT 1,
  `duration_nights` int(11) DEFAULT 0,
  `duration_hours` int(11) DEFAULT 4,
  `max_participants` int(11) DEFAULT 10,
  `available_slots` int(11) DEFAULT 10,
  `price_per_person` decimal(10,2) NOT NULL,
  `highlights` text DEFAULT NULL,
  `included` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Available','Fully Booked','Cancelled','Completed') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_activities`
--

INSERT INTO `tour_activities` (`id`, `tour_id`, `tour_name`, `country`, `city`, `tour_type`, `tour_date`, `duration_days`, `duration_nights`, `duration_hours`, `max_participants`, `available_slots`, `price_per_person`, `highlights`, `included`, `description`, `status`, `created_at`) VALUES
(9, 'TOUR-ADV-001', 'Tokyo Adventure Quest', 'Japan', 'Tokyo', 'Adventure', '2024-12-18', 3, 2, 4, 8, 6, 6500.00, 'Shibuya crossing, robot restaurant, Mount Fuji day trip', 'Accommodation, breakfast, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(10, 'TOUR-ADV-002', 'Nepal Himalaya Trek', 'Nepal', 'Kathmandu', 'Adventure', '2024-12-25', 7, 6, 4, 6, 6, 12000.00, 'Everest base camp, mountain climbing, sherpa guides', 'Equipment, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(11, 'TOUR-ADV-003', 'New Zealand Bungee Jump', 'New Zealand', 'Queenstown', 'Adventure', '2025-01-05', 4, 3, 4, 10, 10, 8500.00, 'Bungee jumping, skydiving, white water rafting', 'Accommodation, activities, safety gear', NULL, 'Available', '2026-01-13 13:25:52'),
(12, 'TOUR-ADV-004', 'Amazon Jungle Expedition', 'Brazil', 'Manaus', 'Adventure', '2025-01-12', 5, 4, 4, 8, 8, 9500.00, 'Jungle trekking, wildlife spotting, river adventures', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(13, 'TOUR-CUL-001', 'Cultural Heritage India', 'India', 'Delhi', 'Cultural', '2025-01-10', 7, 6, 4, 10, 10, 9500.00, 'Taj Mahal, Red Fort, traditional dance shows', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(14, 'TOUR-CUL-002', 'Ancient Egypt Discovery', 'Egypt', 'Cairo', 'Cultural', '2024-12-20', 6, 5, 4, 12, 10, 11000.00, 'Pyramids, Sphinx, Nile cruise, museums', 'Accommodation, meals, entry tickets', NULL, 'Available', '2026-01-13 13:25:52'),
(15, 'TOUR-CUL-003', 'Moroccan Heritage Tour', 'Morocco', 'Marrakech', 'Cultural', '2024-12-28', 5, 4, 4, 8, 8, 7500.00, 'Medina tours, traditional crafts, desert camp', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(16, 'TOUR-CUL-004', 'Chinese Culture Immersion', 'China', 'Beijing', 'Cultural', '2025-01-15', 8, 7, 4, 15, 15, 8500.00, 'Great Wall, Forbidden City, tea ceremony', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(17, 'TOUR-FOO-001', 'Food Tour Bangkok', 'Thailand', 'Bangkok', 'Food', '2025-01-20', 2, 1, 4, 10, 10, 4500.00, 'Street food markets, cooking classes, temple visits', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(18, 'TOUR-FOO-002', 'Italian Culinary Journey', 'Italy', 'Rome', 'Food', '2024-12-22', 4, 3, 4, 8, 5, 9500.00, 'Pasta making, wine tasting, local markets', 'Accommodation, meals, cooking classes', NULL, 'Available', '2026-01-13 13:25:52'),
(19, 'TOUR-FOO-003', 'French Gastronomy Tour', 'France', 'Lyon', 'Food', '2025-01-08', 3, 2, 4, 6, 6, 12000.00, 'Michelin restaurants, cheese tasting, bakery visits', 'Accommodation, meals, tastings', NULL, 'Available', '2026-01-13 13:25:52'),
(20, 'TOUR-FOO-004', 'Japanese Sushi Experience', 'Japan', 'Osaka', 'Food', '2024-12-30', 2, 1, 4, 8, 8, 7500.00, 'Sushi making, sake tasting, fish market tour', 'Accommodation, meals, classes', NULL, 'Available', '2026-01-13 13:25:52'),
(21, 'TOUR-NAT-001', 'Amazon Rainforest', 'Peru', 'Iquitos', 'Nature', '2025-01-18', 6, 5, 4, 8, 8, 10500.00, 'Wildlife observation, canopy walks, river cruises', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(22, 'TOUR-NAT-002', 'African Safari', 'Tanzania', 'Serengeti', 'Nature', '2024-12-26', 7, 6, 4, 6, 6, 15000.00, 'Big Five game drives, Maasai village visit', 'Accommodation, meals, safari vehicle', NULL, 'Available', '2026-01-13 13:25:52'),
(23, 'TOUR-NAT-003', 'Costa Rica Eco Adventure', 'Costa Rica', 'San Jose', 'Nature', '2025-01-12', 5, 4, 4, 10, 10, 8500.00, 'Rainforest hikes, volcano tours, wildlife spotting', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(24, 'TOUR-NAT-004', 'Norwegian Fjords', 'Norway', 'Bergen', 'Nature', '2025-01-25', 4, 3, 4, 12, 12, 11500.00, 'Fjord cruises, northern lights, glacier walks', 'Accommodation, meals, cruise', NULL, 'Available', '2026-01-13 13:25:52'),
(25, 'TOUR-HIS-001', 'London Historical Walk', 'United Kingdom', 'London', 'Historical', '2024-12-22', 1, 0, 4, 15, 15, 3500.00, 'Tower of London, Westminster Abbey, British Museum', 'Entry tickets, guide, lunch', NULL, 'Available', '2026-01-13 13:25:52'),
(26, 'TOUR-HIS-002', 'Ancient Rome Discovery', 'Italy', 'Rome', 'Historical', '2024-12-28', 3, 2, 4, 12, 12, 6500.00, 'Colosseum, Roman Forum, Vatican City', 'Accommodation, entry tickets, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(27, 'TOUR-HIS-003', 'Greek Mythology Tour', 'Greece', 'Athens', 'Historical', '2025-01-05', 4, 3, 4, 10, 10, 7500.00, 'Acropolis, Parthenon, ancient Agora', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(28, 'TOUR-HIS-004', 'Medieval Castles Tour', 'Germany', 'Munich', 'Historical', '2025-01-15', 5, 4, 4, 8, 7, 8500.00, 'Neuschwanstein Castle, medieval towns', 'Accommodation, meals, transportation', NULL, 'Available', '2026-01-13 13:25:52'),
(29, 'TOUR-BEA-001', 'Bali Beach Paradise', 'Indonesia', 'Bali', '', '2024-12-25', 4, 3, 4, 6, 6, 7500.00, 'Surfing lessons, beach hopping, temple visits', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(30, 'TOUR-BEA-002', 'Maldives Island Hopping', 'Maldives', 'Male', '', '2025-01-10', 5, 4, 4, 4, 4, 18000.00, 'Private beaches, snorkeling, water sports', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(31, 'TOUR-BEA-003', 'Caribbean Paradise', 'Barbados', 'Bridgetown', '', '2024-12-30', 6, 5, 4, 8, 8, 12000.00, 'Beach relaxation, diving, island tours', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(32, 'TOUR-BEA-004', 'Hawaiian Island Adventure', 'USA', 'Honolulu', '', '2025-01-20', 7, 6, 4, 10, 10, 14000.00, 'Volcano tours, beach activities, luau dinner', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(33, 'TOUR-CIT-001', 'New York City Explorer', 'USA', 'New York', '', '2024-12-20', 3, 2, 4, 15, 15, 8500.00, 'Times Square, Central Park, Broadway shows', 'Accommodation, tickets, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(34, 'TOUR-CIT-002', 'Paris City of Lights', 'France', 'Paris', '', '2024-12-28', 4, 3, 4, 12, 12, 9500.00, 'Eiffel Tower, Louvre, Seine cruise', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(35, 'TOUR-CIT-003', 'Dubai Modern Marvels', 'UAE', 'Dubai', '', '2025-01-08', 3, 2, 4, 10, 10, 11000.00, 'Burj Khalifa, desert safari, shopping malls', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(36, 'TOUR-CIT-004', 'Singapore Urban Discovery', 'Singapore', 'Singapore', '', '2025-01-15', 2, 1, 4, 12, 12, 6500.00, 'Marina Bay, Gardens by the Bay, Chinatown', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(37, 'TOUR-WIL-001', 'Wildlife Safari Kenya', 'Kenya', 'Nairobi', '', '2025-01-15', 6, 5, 4, 8, 8, 18000.00, 'Masai Mara game drives, Big Five spotting', 'Accommodation, meals, safari vehicle', NULL, 'Available', '2026-01-13 13:25:52'),
(38, 'TOUR-WIL-002', 'Galapagos Wildlife Tour', 'Ecuador', 'Quito', '', '2025-01-22', 8, 7, 4, 6, 6, 22000.00, 'Unique species, snorkeling, nature walks', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(39, 'TOUR-WIL-003', 'Borneo Orangutan Safari', 'Malaysia', 'Kota Kinabalu', '', '2024-12-28', 5, 4, 4, 8, 8, 12000.00, 'Orangutan sanctuary, jungle trekking', 'Accommodation, meals, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(40, 'TOUR-WIL-004', 'Antarctic Wildlife Expedition', 'Antarctica', 'Ushuaia', '', '2025-02-01', 10, 9, 4, 12, 12, 35000.00, 'Penguins, whales, ice formations', 'Accommodation, meals, expedition', NULL, 'Available', '2026-01-13 13:25:52'),
(41, 'TOUR-SIG-001', 'Grand Canyon Spectacular', 'USA', 'Las Vegas', 'Sightseeing', '2024-12-25', 2, 1, 4, 20, 20, 5500.00, 'Helicopter tours, sunset viewing, hiking trails', 'Accommodation, transportation, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(42, 'TOUR-SIG-002', 'Swiss Alps Panorama', 'Switzerland', 'Zurich', 'Sightseeing', '2025-01-10', 4, 3, 4, 15, 15, 12000.00, 'Mountain railways, scenic views, cable cars', 'Accommodation, transportation, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(43, 'TOUR-SIG-003', 'Niagara Falls Wonder', 'Canada', 'Toronto', 'Sightseeing', '2024-12-30', 2, 1, 4, 25, 25, 4500.00, 'Waterfall views, boat rides, observation decks', 'Accommodation, activities, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(44, 'TOUR-SIG-004', 'Northern Lights Iceland', 'Iceland', 'Reykjavik', 'Sightseeing', '2025-01-18', 3, 2, 4, 12, 12, 9500.00, 'Aurora viewing, hot springs, glacier tours', 'Accommodation, transportation, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(45, 'TOUR-ROM-001', 'Romantic Paris Evening', 'France', 'Paris', '', '2024-12-15', 1, 0, 4, 2, 2, 8500.00, 'Candlelit dinner cruise on Seine River, Eiffel Tower visit', 'Dinner, transportation, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(46, 'TOUR-ROM-002', 'Romantic Santorini Sunset', 'Greece', 'Santorini', '', '2024-12-20', 2, 1, 4, 2, 2, 12000.00, 'Private sunset viewing, wine tasting, couples spa', 'Accommodation, meals, spa', NULL, 'Available', '2026-01-13 13:25:52'),
(47, 'TOUR-ROM-003', 'Venice Gondola Romance', 'Italy', 'Venice', '', '2024-12-28', 2, 1, 4, 2, 2, 9500.00, 'Gondola rides, romantic dinners, St. Marks Square', 'Accommodation, meals, activities', NULL, 'Available', '2026-01-13 13:25:52'),
(48, 'TOUR-ROM-004', 'Bali Romantic Retreat', 'Indonesia', 'Ubud', '', '2025-01-05', 3, 2, 4, 2, 2, 11000.00, 'Couples massage, private villa, sunset dinner', 'Accommodation, meals, spa', NULL, 'Available', '2026-01-13 13:25:52'),
(49, 'TOUR-FAM-001', 'Family Fun Singapore', 'Singapore', 'Singapore', '', '2024-12-28', 3, 2, 4, 12, 12, 5500.00, 'Universal Studios, Night Safari, Gardens by the Bay', 'Accommodation, tickets, meals', NULL, 'Available', '2026-01-13 13:25:52'),
(50, 'TOUR-FAM-002', 'Disney World Orlando', 'USA', 'Orlando', '', '2025-01-08', 5, 4, 4, 15, 15, 12000.00, 'Theme parks, character meets, water parks', 'Accommodation, tickets, meals', NULL, 'Available', '2026-01-13 13:25:52'),
(51, 'TOUR-FAM-003', 'London Family Adventure', 'United Kingdom', 'London', '', '2024-12-22', 4, 3, 4, 10, 10, 8500.00, 'Harry Potter studios, London Eye, museums', 'Accommodation, tickets, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(52, 'TOUR-FAM-004', 'Tokyo Family Discovery', 'Japan', 'Tokyo', '', '2025-01-12', 4, 3, 4, 8, 8, 9500.00, 'Disneyland, robot shows, anime districts', 'Accommodation, tickets, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(53, 'TOUR-PHO-001', 'Photography Tour Iceland', 'Iceland', 'Reykjavik', '', '2025-01-05', 5, 4, 4, 8, 8, 15000.00, 'Northern Lights, waterfalls, glaciers, volcanic landscapes', 'Accommodation, equipment, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(54, 'TOUR-PHO-002', 'African Photo Safari', 'South Africa', 'Cape Town', '', '2025-01-20', 7, 6, 4, 6, 6, 18000.00, 'Wildlife photography, landscape shots, sunset captures', 'Accommodation, equipment, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(55, 'TOUR-PHO-003', 'Cherry Blossom Japan', 'Japan', 'Kyoto', '', '2025-03-15', 4, 3, 4, 10, 10, 11000.00, 'Sakura photography, temple shots, traditional gardens', 'Accommodation, equipment, guide', NULL, 'Available', '2026-01-13 13:25:52'),
(56, 'TOUR-PHO-004', 'Patagonia Landscape Photo', 'Argentina', 'Buenos Aires', '', '2025-02-10', 8, 7, 4, 8, 8, 16000.00, 'Mountain photography, glacier shots, wildlife captures', 'Accommodation, equipment, guide', NULL, 'Available', '2026-01-13 13:25:52');

-- --------------------------------------------------------

--
-- Table structure for table `tour_bookings`
--

CREATE TABLE `tour_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `participant_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
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
  `special_requests` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `duration_days` int(11) DEFAULT 1,
  `duration_nights` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_bookings`
--

INSERT INTO `tour_bookings` (`id`, `booking_id`, `customer_id`, `participant_name`, `email`, `phone`, `tour_name`, `country`, `city`, `tour_type`, `tour_date`, `duration_hours`, `participants`, `price_per_person`, `total_amount`, `agent_id`, `agent_commission`, `special_requests`, `status`, `booking_date`, `duration_days`, `duration_nights`) VALUES
(1, 'TOUR-20260103-302', NULL, 'LL', NULL, NULL, 'LL', 'Philippines', 'Cagayan de Oro', 'Sightseeing', '2026-01-03', 4, 1, 500.00, 0.00, 'AGT002', 0.00, NULL, 'Confirmed', '2026-01-03 02:34:35', 1, 0),
(2, 'TOUR-20260103-955', NULL, 'PA', NULL, NULL, 'PA', 'Japan', 'Tokyo', 'Food', '2026-01-03', 4, 1, 6000.00, 0.00, 'AGT002', 0.00, NULL, 'Completed', '2026-01-03 02:36:10', 1, 0),
(3, 'TOUR-20260103-440', NULL, '152', NULL, NULL, '152', 'Australia', 'Perth', 'Adventure', '2026-01-03', 4, 12, 1500.00, 18000.00, 'AGT002', 1800.00, NULL, 'Completed', '2026-01-03 02:39:15', 1, 0),
(4, 'TOUR-20260103-274', NULL, 'Lion', NULL, NULL, 'Lion', 'France', 'Paris', 'Sightseeing', '2026-01-10', 4, 10, 1900.00, 19000.00, 'AGT001', 2375.00, NULL, 'Completed', '2026-01-03 03:14:53', 1, 0),
(5, 'TOUR-20260106-901', NULL, 'LL', NULL, NULL, 'LL', 'Philippines', 'Davao', 'Adventure', '2026-01-06', 6, 4, 400.00, 1600.00, 'AGT2306', 320.00, NULL, 'Confirmed', '2026-01-06 04:59:57', 1, 0),
(6, 'TOUR-20260107-528', NULL, 'Wild Life Visits', NULL, NULL, 'Wild Life Visits', 'Japan', 'Tokyo', 'Nature', '2026-01-07', 5, 5, 6500.00, 32500.00, 'AGT001', 4062.50, NULL, 'Completed', '2026-01-07 06:13:53', 1, 0),
(7, 'TOUR-20260107-435', NULL, 'Paris Tour', NULL, NULL, 'Paris Tour', 'France', 'Paris', 'Adventure', '2026-01-07', 4, 2, 4500.00, 9000.00, '', 0.00, NULL, '', '2026-01-07 07:22:50', 1, 0),
(8, 'TOUR-20260107-699', NULL, 'Food Tours', NULL, NULL, 'Food Tours', 'Canada', 'Montreal', 'Food', '2026-01-07', 4, 5, 570.00, 2850.00, '', 0.00, NULL, 'Completed', '2026-01-07 07:23:16', 1, 0),
(9, 'TOUR-20260107-215', NULL, 'Cultures Show', NULL, NULL, 'Cultures Show', 'Australia', 'Sydney', 'Cultural', '2026-01-07', 4, 10, 5720.00, 57200.00, 'AGT2306', 11440.00, NULL, '', '2026-01-07 07:23:46', 1, 0),
(10, 'TOUR-20260107-057', NULL, 'Museum Visits', NULL, NULL, 'Museum Visits', 'Germany', 'Cologne', 'Sightseeing', '2026-01-07', 4, 4, 800.00, 3200.00, '', 0.00, NULL, 'Completed', '2026-01-07 07:24:11', 1, 0),
(11, 'TOUR-20260107-646', NULL, 'Sports Fest', NULL, NULL, 'Sports Fest', 'Japan', 'Osaka', 'Adventure', '2026-01-07', 4, 10, 680.00, 6800.00, '', 0.00, NULL, '', '2026-01-07 07:24:32', 1, 0),
(12, 'TOUR-20260107-635', NULL, 'Night Foods', NULL, NULL, 'Night Foods', 'Singapore', 'Singapore', 'Food', '2026-01-07', 4, 6, 540.00, 3240.00, '', 0.00, NULL, '', '2026-01-07 07:24:56', 1, 0),
(13, 'TOUR-20260107-074', NULL, 'City Walks', NULL, NULL, 'City Walks', 'United Arab Emirates', 'Dubai', 'Adventure', '2026-01-07', 4, 5, 800.00, 4000.00, 'AGT001', 500.00, NULL, '', '2026-01-07 07:25:17', 1, 0),
(14, 'TOUR-20260107-042', NULL, 'Wild Life Visits', NULL, NULL, 'Wild Life Visits', 'Philippines', 'Davao', 'Nature', '2026-01-07', 4, 8, 700.00, 5600.00, '', 0.00, NULL, '', '2026-01-07 07:25:36', 1, 0),
(15, 'TOUR-20260113-419', NULL, 'Gabriel Paolo', NULL, NULL, 'Wild Life Visits', 'Japan', 'Tokyo', 'Nature', '2026-01-07', 4, 1, 1233.00, 1233.00, 'AGT8711', 123.30, NULL, 'Completed', '2026-01-13 12:54:05', 1, 0),
(16, 'TOUR-20260113-723', NULL, 'Gabriel Paolo', NULL, NULL, 'Wild Life Visits', 'Japan', 'Tokyo', 'Nature', '2026-01-07', 4, 2, 2000.00, 4000.00, 'AGT9237', 200.00, NULL, 'Completed', '2026-01-13 12:55:44', 1, 0),
(17, 'TOUR-20260113-495', NULL, 'Gabriel Paolo', NULL, NULL, 'Medieval Castles Tour', 'Germany', 'Munich', 'Historical', '2025-01-15', 4, 1, 8500.00, 8500.00, 'AGT001', 1062.50, NULL, 'Confirmed', '2026-01-13 13:39:42', 5, 4),
(18, 'TOUR-20260113-893', NULL, 'Gabriel Paolo', NULL, NULL, 'Tokyo Adventure Quest', 'Japan', 'Tokyo', 'Adventure', '2024-12-18', 4, 2, 6500.00, 13000.00, 'AGT002', 1300.00, NULL, 'Completed', '2026-01-13 14:30:51', 3, 2),
(19, 'TOUR-20260113-062', NULL, 'Gabriel Paolo', 'g@gmail.com', '096134214123', 'Italian Culinary Journey', 'Italy', 'Rome', 'Food', '2024-12-22', 4, 3, 9500.00, 28500.00, 'AGT002', 2850.00, '', 'Completed', '2026-01-13 14:32:48', 4, 3),
(20, 'TOUR-20260113-877', NULL, 'Paolo', 'justin@gmail.com', '096134214123', 'Ancient Egypt Discovery', 'Egypt', 'Cairo', 'Cultural', '2024-12-20', 4, 2, 11000.00, 22000.00, NULL, 0.00, 'wqeq', 'Completed', '2026-01-13 14:34:23', 6, 5);

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
(1, 'AGT001', 'Maria Santos', 'maria@travelph.com', '+639171234567', 'Philippine Travel Experts', 'All', 12.50, 58, 125000.00, 'Active', '2025-12-29 06:10:37'),
(2, 'AGT002', 'John Lim', 'john@asiantravel.com', '+65981234567', 'Asian Travel Hub', 'Flights', 10.00, 33, 85000.00, 'Active', '2025-12-29 06:10:37'),
(3, 'AGT003', 'Sarah Johnson', 'sarah@luxurytravel.com', '+441234567890', 'Luxury Travel Co.', 'Packages', 15.00, 31, 104675.00, 'Active', '2025-12-29 06:10:37'),
(4, 'AGT9237', 'James Bond', 'James@gmail.com', '09940213443', 'Loko', 'Tours', 5.00, 0, 0.00, 'Active', '2026-01-03 03:15:35'),
(5, 'AGT2306', 'Paolo M', 'Pmadridano@gmail.com', '096134214123', 'KI', 'All', 20.00, 8, 0.00, 'Active', '2026-01-06 04:59:22'),
(6, 'AGT8711', 'LOKK', 'lok@gmail.co', '09564642123', 'Tours', 'Tours', 10.00, 0, 0.00, 'Active', '2026-01-06 05:56:37');

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
-- Indexes for table `available_hotels`
--
ALTER TABLE `available_hotels`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `cruises`
--
ALTER TABLE `cruises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `agent_id` (`agent_id`);

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
-- Indexes for table `tour_activities`
--
ALTER TABLE `tour_activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_id` (`tour_id`);

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
-- AUTO_INCREMENT for table `available_hotels`
--
ALTER TABLE `available_hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `car_rentals`
--
ALTER TABLE `car_rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `car_rental_bookings`
--
ALTER TABLE `car_rental_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `cruises`
--
ALTER TABLE `cruises`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `hotel_bookings`
--
ALTER TABLE `hotel_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tour_activities`
--
ALTER TABLE `tour_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tour_bookings`
--
ALTER TABLE `tour_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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

--
-- Constraints for table `cruises`
--
ALTER TABLE `cruises`
  ADD CONSTRAINT `cruises_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `travel_agents` (`agent_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
