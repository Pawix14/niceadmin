-- Insert 15 car reviews using existing booking IDs from car_rentals table
-- First, get the booking IDs and update them with reviews

-- Get first 15 completed bookings and add reviews
INSERT INTO car_reviews (booking_id, customer_email, customer_name, car_model, rating, review_text, created_at)
SELECT 
    booking_id,
    customer_email,
    customer_name,
    car_model,
    CASE 
        WHEN MOD(id, 5) = 0 THEN 5
        WHEN MOD(id, 5) = 1 THEN 4
        WHEN MOD(id, 5) = 2 THEN 5
        WHEN MOD(id, 5) = 3 THEN 4
        ELSE 3
    END as rating,
    CASE 
        WHEN MOD(id, 15) = 0 THEN 'Excellent car! Very fuel efficient and comfortable for long drives. The pickup process was smooth and the car was in pristine condition.'
        WHEN MOD(id, 15) = 1 THEN 'Great experience overall. The car handled well and was perfect for city driving. Only minor issue was the AC took a while to cool down.'
        WHEN MOD(id, 15) = 2 THEN 'Amazing sports car! The power and handling are incredible. Perfect for a weekend getaway. Highly recommend!'
        WHEN MOD(id, 15) = 3 THEN 'Love the electric experience! Silent, smooth, and eco-friendly. The autopilot feature made highway driving a breeze.'
        WHEN MOD(id, 15) = 4 THEN 'Luxury at its finest. Comfortable seats, great sound system, and smooth ride. A bit pricey but worth it for special occasions.'
        WHEN MOD(id, 15) = 5 THEN 'Absolutely stunning car! The interior is luxurious and the drive is incredibly smooth. Staff was very professional.'
        WHEN MOD(id, 15) = 6 THEN 'Perfect for off-road adventures! Took it to the mountains and it handled everything like a champ. Very spacious too.'
        WHEN MOD(id, 15) = 7 THEN 'Reliable and comfortable sedan. Great for family trips. Good fuel economy and plenty of trunk space.'
        WHEN MOD(id, 15) = 8 THEN 'Decent car but had some minor issues with the Bluetooth connection. Otherwise, it was okay for the price.'
        WHEN MOD(id, 15) = 9 THEN 'Good value for money. Clean car, responsive staff, and easy booking process. Would rent again!'
        WHEN MOD(id, 15) = 10 THEN 'Excellent SUV! Perfect size for a small family. Great visibility and very comfortable on long drives.'
        WHEN MOD(id, 15) = 11 THEN 'Nice mid-size sedan. Comfortable ride and good features. The infotainment system was easy to use.'
        WHEN MOD(id, 15) = 12 THEN 'Premium experience all around! The car is beautiful, drives like a dream, and has all the latest tech features.'
        WHEN MOD(id, 15) = 13 THEN 'Solid choice for daily driving. Good fuel efficiency and comfortable interior. No complaints!'
        ELSE 'Surprisingly great car! Modern features, smooth ride, and excellent customer service. Exceeded my expectations!'
    END as review_text,
    DATE_ADD(dropoff_date, INTERVAL 1 DAY) as created_at
FROM car_rentals
WHERE status = 'Completed'
LIMIT 15;
