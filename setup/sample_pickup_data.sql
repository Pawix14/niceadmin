-- Sample data for pickup demonstration

-- Update 2 bookings to have pickup_status='Ready' (customer needs to pick up)
UPDATE car_rentals 
SET status='Confirmed', pickup_status='Ready', pickup_location='Main Office - 123 Downtown St'
WHERE booking_id IN (
  SELECT booking_id FROM (
    SELECT booking_id FROM car_rentals WHERE status='Pending' LIMIT 2
  ) AS temp
);

-- Update 2 bookings to have return_pickup_status='Ready' (staff needs to pick up returned cars)
UPDATE car_rentals 
SET status='Completed', 
    return_pickup_status='Ready', 
    dropoff_location='Airport Terminal 2',
    actual_return_date=NOW()
WHERE booking_id IN (
  SELECT booking_id FROM (
    SELECT booking_id FROM car_rentals WHERE status='Confirmed' LIMIT 2
  ) AS temp
);
