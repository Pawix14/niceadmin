-- Fix for existing car_rentals records
-- First add the missing column, then update records:

ALTER TABLE car_rentals ADD COLUMN license_expiry DATE;

UPDATE car_rentals SET 
    license_number = 'N00-00-000000',
    license_expiry = '2025-12-31'
WHERE license_number = '' OR license_number IS NULL OR license_expiry IS NULL;