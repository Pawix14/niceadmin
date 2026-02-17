# Pickup Notification System

## Overview
This system manages car pickup notifications and confirmations for both customers and staff at specified locations.

## Database Changes
Run this SQL first:
```sql
ALTER TABLE car_rentals 
ADD COLUMN IF NOT EXISTS pickup_status VARCHAR(20) DEFAULT 'Pending',
ADD COLUMN IF NOT EXISTS pickup_confirmed_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS return_pickup_status VARCHAR(20) DEFAULT 'Pending',
ADD COLUMN IF NOT EXISTS return_pickup_confirmed_at DATETIME NULL;
```

## Workflow

### 1. Staff Approves Booking
**File:** `staff_booking_review.php`
- When staff clicks "Approve" button
- System updates: `status='Confirmed'`, `pickup_status='Ready'`
- Customer receives notification: "Your booking has been approved! Please pick up your car at: [pickup_location]"

### 2. Customer Picks Up Car
**File:** `my_bookings.php`
- Customer sees "Confirm Pickup" button when `status='Confirmed'` AND `pickup_status='Ready'`
- Customer clicks button to confirm they picked up the car
- System updates: `pickup_status='Picked Up'`, `status='Active'`, `pickup_confirmed_at=NOW()`
- Staff receives notification: "Customer has picked up car for booking [ID] at [location]"

### 3. Customer Returns Car
**File:** `my_bookings.php`
- Customer clicks "Return Car" button
- System calculates refund (if early return)
- System updates: `status='Completed'`, `return_pickup_status='Ready'`
- Staff receives notification: "Customer returned car for booking [ID]. Pick up at: [dropoff_location]"

### 4. Staff Picks Up Returned Car
**File:** `pickup_management.php`
- Staff sees list of cars ready for pickup at return locations
- Staff clicks "Confirm Pickup Complete" button
- System updates: `return_pickup_status='Picked Up'`, `return_pickup_confirmed_at=NOW()`

## Status Flow
```
Pending → Confirmed (pickup_status='Ready') → Active (pickup_status='Picked Up') → Completed (return_pickup_status='Ready') → Picked Up (return_pickup_status='Picked Up')
```

## Files Created/Modified

### New Files:
1. **setup/add_pickup_columns.sql** - Database schema changes
2. **modules/pickup_handler.php** - AJAX handler for pickup actions
3. **modules/pickup_management.php** - Staff page to manage pickups
4. **PICKUP_NOTIFICATION_GUIDE.md** - This documentation

### Modified Files:
1. **modules/staff_booking_review.php**
   - Added pickup location to approval notification
   - Sets `pickup_status='Ready'` on approval

2. **modules/my_bookings.php**
   - Added "Confirm Pickup" button for customers
   - Added staff notification on car return
   - Sets `return_pickup_status='Ready'` on return

## Notifications

### Customer Notifications:
- **On Approval:** "Booking approved! Pick up car at [location]"

### Staff Notifications:
- **On Customer Pickup:** "Customer picked up car at [location]"
- **On Car Return:** "Car ready for pickup at [return location]"

## Action Buttons

### Customer Side (my_bookings.php):
- **Confirm Pickup** - Shows when booking is Confirmed and ready for pickup
- **Return Car** - Shows when booking is Active
- **Rebook** - Shows when booking is Completed

### Staff Side (pickup_management.php):
- **Confirm Pickup Complete** - For returned cars ready for staff pickup

## Testing Steps

1. **Run SQL script:**
   ```bash
   Execute: setup/add_pickup_columns.sql in phpMyAdmin
   ```

2. **Staff approves booking:**
   - Go to Staff Booking Review
   - Approve a booking with documents and payment
   - Check customer receives notification with pickup location

3. **Customer confirms pickup:**
   - Login as customer
   - Go to My Bookings
   - Click "Confirm Pickup" button
   - Status should change to "Active"
   - Staff should receive notification

4. **Customer returns car:**
   - Click "Return Car" button
   - Staff should receive notification with return location
   - Check refund calculation if early return

5. **Staff picks up returned car:**
   - Go to Pickup Management page
   - See car in "Ready for Staff Pickup" section
   - Click "Confirm Pickup Complete"
   - Car should be removed from list

## Key Features
✅ Location-based pickup notifications
✅ Two-way confirmation (customer pickup, staff return pickup)
✅ Automatic status updates
✅ Real-time notifications
✅ Refund calculation on early returns
✅ Staff dashboard for pickup management
