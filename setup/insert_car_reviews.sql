-- Insert 15 car reviews for different cars with varied ratings and comments

INSERT INTO car_reviews (booking_id, customer_email, customer_name, car_model, rating, review_text, created_at) VALUES
('BK001', 'john.doe@email.com', 'John Doe', 'Toyota Corolla', 5, 'Excellent car! Very fuel efficient and comfortable for long drives. The pickup process was smooth and the car was in pristine condition.', '2024-11-15 10:30:00'),
('BK002', 'jane.smith@email.com', 'Jane Smith', 'Honda Civic', 4, 'Great experience overall. The car handled well and was perfect for city driving. Only minor issue was the AC took a while to cool down.', '2024-11-18 14:20:00'),
('BK003', 'mike.wilson@email.com', 'Mike Wilson', 'Ford Mustang', 5, 'Amazing sports car! The power and handling are incredible. Perfect for a weekend getaway. Highly recommend!', '2024-11-20 09:15:00'),
('BK004', 'sarah.jones@email.com', 'Sarah Jones', 'Tesla Model 3', 5, 'Love the electric experience! Silent, smooth, and eco-friendly. The autopilot feature made highway driving a breeze.', '2024-11-22 16:45:00'),
('BK005', 'david.brown@email.com', 'David Brown', 'BMW 3 Series', 4, 'Luxury at its finest. Comfortable seats, great sound system, and smooth ride. A bit pricey but worth it for special occasions.', '2024-11-25 11:00:00'),
('BK006', 'emily.davis@email.com', 'Emily Davis', 'Mercedes-Benz C-Class', 5, 'Absolutely stunning car! The interior is luxurious and the drive is incredibly smooth. Staff was very professional.', '2024-11-28 13:30:00'),
('BK007', 'chris.miller@email.com', 'Chris Miller', 'Jeep Wrangler', 5, 'Perfect for off-road adventures! Took it to the mountains and it handled everything like a champ. Very spacious too.', '2024-12-01 10:00:00'),
('BK008', 'lisa.garcia@email.com', 'Lisa Garcia', 'Toyota Camry', 4, 'Reliable and comfortable sedan. Great for family trips. Good fuel economy and plenty of trunk space.', '2024-12-03 15:20:00'),
('BK009', 'robert.martinez@email.com', 'Robert Martinez', 'Nissan Altima', 3, 'Decent car but had some minor issues with the Bluetooth connection. Otherwise, it was okay for the price.', '2024-12-05 09:45:00'),
('BK010', 'amanda.rodriguez@email.com', 'Amanda Rodriguez', 'Hyundai Elantra', 4, 'Good value for money. Clean car, responsive staff, and easy booking process. Would rent again!', '2024-12-08 14:10:00'),
('BK011', 'kevin.lee@email.com', 'Kevin Lee', 'Mazda CX-5', 5, 'Excellent SUV! Perfect size for a small family. Great visibility and very comfortable on long drives.', '2024-12-10 11:30:00'),
('BK012', 'michelle.white@email.com', 'Michelle White', 'Chevrolet Malibu', 4, 'Nice mid-size sedan. Comfortable ride and good features. The infotainment system was easy to use.', '2024-12-12 16:00:00'),
('BK013', 'daniel.harris@email.com', 'Daniel Harris', 'Audi A4', 5, 'Premium experience all around! The car is beautiful, drives like a dream, and has all the latest tech features.', '2024-12-15 10:45:00'),
('BK014', 'jessica.clark@email.com', 'Jessica Clark', 'Volkswagen Jetta', 4, 'Solid choice for daily driving. Good fuel efficiency and comfortable interior. No complaints!', '2024-12-18 13:15:00'),
('BK015', 'thomas.lewis@email.com', 'Thomas Lewis', 'Kia Optima', 5, 'Surprisingly great car! Modern features, smooth ride, and excellent customer service. Exceeded my expectations!', '2024-12-20 09:30:00');

-- Update review counts for cars (optional - for display purposes)
UPDATE cars SET review_count = (SELECT COUNT(*) FROM car_reviews WHERE car_reviews.car_model = cars.name) WHERE name IN (
  'Toyota Corolla', 'Honda Civic', 'Ford Mustang', 'Tesla Model 3', 'BMW 3 Series',
  'Mercedes-Benz C-Class', 'Jeep Wrangler', 'Toyota Camry', 'Nissan Altima', 'Hyundai Elantra',
  'Mazda CX-5', 'Chevrolet Malibu', 'Audi A4', 'Volkswagen Jetta', 'Kia Optima'
);
