-- Add pickup tracking columns to car_rentals table
ALTER TABLE car_rentals 
ADD COLUMN IF NOT EXISTS pickup_status VARCHAR(20) DEFAULT 'Pending',
ADD COLUMN IF NOT EXISTS pickup_confirmed_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS return_pickup_status VARCHAR(20) DEFAULT 'Pending',
ADD COLUMN IF NOT EXISTS return_pickup_confirmed_at DATETIME NULL;

-- pickup_status: 'Pending', 'Ready', 'Picked Up'
-- return_pickup_status: 'Pending', 'Ready', 'Picked Up'
