-- Sample Data Generator for CarGo Car Rental System
-- This script creates 15 bookings with customer accounts and approved payments

-- First, create customer accounts
INSERT INTO customers (username, password, full_name, email, phone, address, created_at) VALUES
('john.doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'john.doe@email.com', '+63 917 123 4567', '123 Makati Ave, Makati City', '2026-01-15 10:30:00'),
('maria.santos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria Santos', 'maria.santos@email.com', '+63 918 234 5678', '456 Quezon Ave, Quezon City', '2026-01-16 11:00:00'),
('pedro.cruz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro Cruz', 'pedro.cruz@email.com', '+63 919 345 6789', '789 BGC, Taguig City', '2026-01-17 09:15:00'),
('ana.reyes', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana Reyes', 'ana.reyes@email.com', '+63 920 456 7890', '321 Ortigas, Pasig City', '2026-01-18 14:20:00'),
('carlos.garcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos Garcia', 'carlos.garcia@email.com', '+63 921 567 8901', '654 Manila Bay, Manila', '2026-01-19 08:45:00'),
('lisa.tan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa Tan', 'lisa.tan@email.com', '+63 922 678 9012', '987 Alabang, Muntinlupa', '2026-01-20 13:30:00'),
('miguel.lopez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel Lopez', 'miguel.lopez@email.com', '+63 923 789 0123', '147 Eastwood, Quezon City', '2026-01-21 10:00:00'),
('sofia.martinez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofia Martinez', 'sofia.martinez@email.com', '+63 924 890 1234', '258 Rockwell, Makati', '2026-01-22 15:45:00'),
('david.wong', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David Wong', 'david.wong@email.com', '+63 925 901 2345', '369 Greenhills, San Juan', '2026-01-23 09:30:00'),
('elena.rivera', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Elena Rivera', 'elena.rivera@email.com', '+63 926 012 3456', '741 Ayala, Makati', '2026-01-24 11:15:00'),
('robert.kim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert Kim', 'robert.kim@email.com', '+63 927 123 4567', '852 MOA, Pasay', '2026-01-25 14:00:00'),
('carmen.flores', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carmen Flores', 'carmen.flores@email.com', '+63 928 234 5678', '963 Cubao, Quezon City', '2026-01-26 10:45:00'),
('james.lee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'James Lee', 'james.lee@email.com', '+63 929 345 6789', '159 Kapitolyo, Pasig', '2026-01-27 13:20:00'),
('isabel.ramos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabel Ramos', 'isabel.ramos@email.com', '+63 930 456 7890', '357 Bonifacio, Taguig', '2026-01-28 09:00:00'),
('daniel.chen', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Daniel Chen', 'daniel.chen@email.com', '+63 931 567 8901', '468 Ortigas, Mandaluyong', '2026-01-29 16:30:00');

-- Insert 15 sample bookings with varied statuses and payment types
INSERT INTO car_rentals (
    booking_id, customer_name, customer_email, customer_phone, customer_age,
    license_number, license_expiry, pickup_date, dropoff_date, pickup_time, dropoff_time,
    pickup_location, dropoff_location, car_type, car_model, rental_days, daily_rate,
    subtotal, insurance_fee, additional_fees, total_amount, status, payment_method,
    payment_type, amount_paid, remaining_balance, payment_status, invoice_number,
    receipt_file, payment_verified_by, payment_verified_at, created_at
) VALUES
-- Booking 1: Completed with Full Payment
('CAR-20260115-A1B2C3', 'John Doe', 'john.doe@email.com', '+63 917 123 4567', 35, 'N01-23-456789', '2028-12-31', '2026-01-20', '2026-01-25', '10:00:00', '10:00:00', 'MNL - Manila Ninoy Aquino International Airport', 'MNL - Manila Ninoy Aquino International Airport', 'Sedan', 'Toyota Camry', 5, 2500.00, 12500.00, 1500.00, 0, 14000.00, 'Completed', 'GCash', 'Full Payment', 14000.00, 0, 'Paid', 'INV-20260115-0001', 'REC-20260115-0001', 'Gabriel', '2026-01-15 11:00:00', '2026-01-15 10:30:00'),

-- Booking 2: Active with Downpayment (50% paid)
('CAR-20260116-D4E5F6', 'Maria Santos', 'maria.santos@email.com', '+63 918 234 5678', 28, 'M02-34-567890', '2029-06-30', '2026-02-18', '2026-02-22', '09:00:00', '09:00:00', 'Makati City Center', 'Makati City Center', 'SUV', 'Honda CR-V', 4, 3500.00, 14000.00, 1200.00, 0, 15200.00, 'Active', 'PayPal', 'Downpayment', 7600.00, 7600.00, 'Paid', 'INV-20260116-0002', 'REC-20260116-0002', 'Gabriel', '2026-01-16 12:00:00', '2026-01-16 11:00:00'),

-- Booking 3: Confirmed with Full Payment
('CAR-20260117-G7H8I9', 'Pedro Cruz', 'pedro.cruz@email.com', '+63 919 345 6789', 42, 'P03-45-678901', '2027-11-15', '2026-02-20', '2026-02-23', '14:00:00', '14:00:00', 'Bonifacio Global City (BGC)', 'Bonifacio Global City (BGC)', 'Luxury', 'BMW 5 Series', 3, 5000.00, 15000.00, 2700.00, 0, 17700.00, 'Confirmed', 'Credit Card', 'Full Payment', 17700.00, 0, 'Paid', 'INV-20260117-0003', 'REC-20260117-0003', 'Gabriel', '2026-01-17 10:00:00', '2026-01-17 09:15:00'),

-- Booking 4: Completed with Downpayment (Fully paid)
('CAR-20260118-J1K2L3', 'Ana Reyes', 'ana.reyes@email.com', '+63 920 456 7890', 31, 'A04-56-789012', '2028-08-20', '2026-01-22', '2026-01-27', '11:00:00', '11:00:00', 'Quezon City - Cubao', 'Quezon City - Cubao', 'Compact', 'Toyota Vios', 5, 1800.00, 9000.00, 1500.00, 0, 10500.00, 'Completed', 'GCash', 'Downpayment', 10500.00, 0, 'Paid', 'INV-20260118-0004', 'REC-20260118-0004', 'Gabriel', '2026-01-18 15:00:00', '2026-01-18 14:20:00'),

-- Booking 5: Active with Full Payment
('CAR-20260119-M4N5O6', 'Carlos Garcia', 'carlos.garcia@email.com', '+63 921 567 8901', 38, 'C05-67-890123', '2029-03-10', '2026-02-19', '2026-02-24', '08:00:00', '08:00:00', 'Pasay City - Mall of Asia', 'Pasay City - Mall of Asia', 'Van', 'Toyota Hiace', 5, 4000.00, 20000.00, 1500.00, 0, 21500.00, 'Active', 'Debit Card', 'Full Payment', 21500.00, 0, 'Paid', 'INV-20260119-0005', 'REC-20260119-0005', 'Gabriel', '2026-01-19 09:30:00', '2026-01-19 08:45:00'),

-- Booking 6: Confirmed with Downpayment (50% paid)
('CAR-20260120-P7Q8R9', 'Lisa Tan', 'lisa.tan@email.com', '+63 922 678 9012', 29, 'L06-78-901234', '2028-05-25', '2026-02-21', '2026-02-25', '13:00:00', '13:00:00', 'CEB - Mactan-Cebu International Airport', 'CEB - Mactan-Cebu International Airport', 'Electric', 'Tesla Model 3 or Similar', 4, 3800.00, 15200.00, 1200.00, 0, 16400.00, 'Confirmed', 'PayPal', 'Downpayment', 8200.00, 8200.00, 'Paid', 'INV-20260120-0006', 'REC-20260120-0006', 'Gabriel', '2026-01-20 14:00:00', '2026-01-20 13:30:00'),

-- Booking 7: Completed with Full Payment
('CAR-20260121-S1T2U3', 'Miguel Lopez', 'miguel.lopez@email.com', '+63 923 789 0123', 45, 'M07-89-012345', '2027-12-31', '2026-01-25', '2026-01-28', '10:30:00', '10:30:00', 'Mandaluyong City - Ortigas Center', 'Mandaluyong City - Ortigas Center', 'Sedan', 'Honda Accord', 3, 2800.00, 8400.00, 900.00, 0, 9300.00, 'Completed', 'GCash', 'Full Payment', 9300.00, 0, 'Paid', 'INV-20260121-0007', 'REC-20260121-0007', 'Gabriel', '2026-01-21 11:00:00', '2026-01-21 10:00:00'),

-- Booking 8: Active with Downpayment (50% paid)
('CAR-20260122-V4W5X6', 'Sofia Martinez', 'sofia.martinez@email.com', '+63 924 890 1234', 33, 'S08-90-123456', '2029-07-15', '2026-02-22', '2026-02-26', '15:00:00', '15:00:00', 'MRT Ayala Station', 'MRT Ayala Station', 'SUV', 'Mitsubishi Montero Sport', 4, 3600.00, 14400.00, 1200.00, 0, 15600.00, 'Active', 'Credit Card', 'Downpayment', 7800.00, 7800.00, 'Paid', 'INV-20260122-0008', 'REC-20260122-0008', 'Gabriel', '2026-01-22 16:00:00', '2026-01-22 15:45:00'),

-- Booking 9: Confirmed with Full Payment
('CAR-20260123-Y7Z8A9', 'David Wong', 'david.wong@email.com', '+63 925 901 2345', 40, 'D09-01-234567', '2028-10-20', '2026-02-23', '2026-02-27', '09:30:00', '09:30:00', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Any Hotel in Metro Manila (Hotel Delivery)', 'Luxury', 'Mercedes-Benz E-Class', 4, 5500.00, 22000.00, 2400.00, 0, 24400.00, 'Confirmed', 'PayPal', 'Full Payment', 24400.00, 0, 'Paid', 'INV-20260123-0009', 'REC-20260123-0009', 'Gabriel', '2026-01-23 10:00:00', '2026-01-23 09:30:00'),

-- Booking 10: Completed with Downpayment (Fully paid)
('CAR-20260124-B1C2D3', 'Elena Rivera', 'elena.rivera@email.com', '+63 926 012 3456', 27, 'E10-12-345678', '2029-04-30', '2026-01-28', '2026-02-02', '12:00:00', '12:00:00', 'CRK - Clark International Airport', 'CRK - Clark International Airport', 'Compact', 'Mazda 3', 5, 2200.00, 11000.00, 1500.00, 0, 12500.00, 'Completed', 'GCash', 'Downpayment', 12500.00, 0, 'Paid', 'INV-20260124-0010', 'REC-20260124-0010', 'Gabriel', '2026-01-24 12:00:00', '2026-01-24 11:15:00'),

-- Booking 11: Active with Full Payment
('CAR-20260125-E4F5G6', 'Robert Kim', 'robert.kim@email.com', '+63 927 123 4567', 36, 'R11-23-456789', '2028-09-15', '2026-02-24', '2026-02-28', '14:30:00', '14:30:00', 'Shangri-La Hotel', 'Shangri-La Hotel', 'Van', 'Nissan Urvan', 4, 3800.00, 15200.00, 1200.00, 0, 16400.00, 'Active', 'Debit Card', 'Full Payment', 16400.00, 0, 'Paid', 'INV-20260125-0011', 'REC-20260125-0011', 'Gabriel', '2026-01-25 15:00:00', '2026-01-25 14:00:00'),

-- Booking 12: Confirmed with Downpayment (50% paid)
('CAR-20260126-H7I8J9', 'Carmen Flores', 'carmen.flores@email.com', '+63 928 234 5678', 32, 'C12-34-567890', '2029-01-20', '2026-02-25', '2026-03-01', '11:00:00', '11:00:00', 'DVO - Francisco Bangoy International Airport', 'DVO - Francisco Bangoy International Airport', 'Sedan', 'Hyundai Elantra', 4, 2400.00, 9600.00, 1200.00, 0, 10800.00, 'Confirmed', 'PayPal', 'Downpayment', 5400.00, 5400.00, 'Paid', 'INV-20260126-0012', 'REC-20260126-0012', 'Gabriel', '2026-01-26 11:30:00', '2026-01-26 10:45:00'),

-- Booking 13: Completed with Full Payment
('CAR-20260127-K1L2M3', 'James Lee', 'james.lee@email.com', '+63 929 345 6789', 41, 'J13-45-678901', '2027-10-31', '2026-01-30', '2026-02-03', '13:45:00', '13:45:00', 'Okada Manila', 'Okada Manila', 'SUV', 'Ford Everest', 4, 3700.00, 14800.00, 1200.00, 0, 16000.00, 'Completed', 'Credit Card', 'Full Payment', 16000.00, 0, 'Paid', 'INV-20260127-0013', 'REC-20260127-0013', 'Gabriel', '2026-01-27 14:00:00', '2026-01-27 13:20:00'),

-- Booking 14: Active with Downpayment (50% paid)
('CAR-20260128-N4O5P6', 'Isabel Ramos', 'isabel.ramos@email.com', '+63 930 456 7890', 30, 'I14-56-789012', '2028-11-30', '2026-02-26', '2026-03-02', '09:15:00', '09:15:00', 'Solaire Resort & Casino', 'Solaire Resort & Casino', 'Electric', 'Nissan Leaf', 4, 3500.00, 14000.00, 1200.00, 0, 15200.00, 'Active', 'GCash', 'Downpayment', 7600.00, 7600.00, 'Paid', 'INV-20260128-0014', 'REC-20260128-0014', 'Gabriel', '2026-01-28 10:00:00', '2026-01-28 09:00:00'),

-- Booking 15: Confirmed with Full Payment
('CAR-20260129-Q7R8S9', 'Daniel Chen', 'daniel.chen@email.com', '+63 931 567 8901', 37, 'D15-67-890123', '2029-02-28', '2026-02-27', '2026-03-03', '16:45:00', '16:45:00', 'LRT Buendia Station', 'LRT Buendia Station', 'Luxury', 'Audi A6', 4, 5200.00, 20800.00, 2400.00, 0, 23200.00, 'Confirmed', 'PayPal', 'Full Payment', 23200.00, 0, 'Paid', 'INV-20260129-0015', 'REC-20260129-0015', 'Gabriel', '2026-01-29 17:00:00', '2026-01-29 16:30:00');

-- Summary Report
SELECT 
    'Total Bookings' as Metric, 
    COUNT(*) as Value 
FROM car_rentals 
WHERE booking_id LIKE 'CAR-202601%'
UNION ALL
SELECT 
    'Total Revenue', 
    CONCAT('₱', FORMAT(SUM(amount_paid), 2))
FROM car_rentals 
WHERE booking_id LIKE 'CAR-202601%'
UNION ALL
SELECT 
    'Completed Bookings', 
    COUNT(*)
FROM car_rentals 
WHERE booking_id LIKE 'CAR-202601%' AND status = 'Completed'
UNION ALL
SELECT 
    'Active Bookings', 
    COUNT(*)
FROM car_rentals 
WHERE booking_id LIKE 'CAR-202601%' AND status = 'Active'
UNION ALL
SELECT 
    'Confirmed Bookings', 
    COUNT(*)
FROM car_rentals 
WHERE booking_id LIKE 'CAR-202601%' AND status = 'Confirmed';
