-- SQL script to remove hotel booking, tour activity, and cruise features
-- Run this script to clean up the database

-- Drop cruise-related tables
DROP TABLE IF EXISTS cruises;

-- Drop hotel-related tables  
DROP TABLE IF EXISTS hotel_bookings;
DROP TABLE IF EXISTS available_hotels;

-- Drop tour-related tables
DROP TABLE IF EXISTS tour_bookings;
DROP TABLE IF EXISTS tour_activities;

-- Update commissions table to remove hotel, tour, and cruise booking types
DELETE FROM commissions WHERE booking_type IN ('Hotel', 'Tour');

-- Update travel_agents specialization options (remove Hotels, Tours, Cruises)
UPDATE travel_agents 
SET specialization = 'All' 
WHERE specialization IN ('Hotels', 'Tours', 'Cruises');

-- Clean up any remaining references
-- Note: Keep flight_bookings, car_rentals, travel_bookings, and other core features