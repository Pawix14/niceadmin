# Car Rental Automation Setup

## Features Implemented

### 1. Real-Time Car Availability
- Cars are automatically marked as "Rented" when they have active bookings
- Status updates happen on every page load
- Cars become "Active" again when rental period ends

### 2. Return Date Reminders
- System checks for rentals ending tomorrow
- Sends notification to customer email 1 day before return date
- Reminder is sent only once per booking

## Setup Instructions

### Option 1: Windows Task Scheduler (Recommended for XAMPP)

1. Open Task Scheduler (taskschd.msc)
2. Create Basic Task:
   - Name: "Car Rental Checker"
   - Trigger: Daily at 8:00 AM
   - Action: Start a program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `"d:\xampp\htdocs\niceadmin\cron\check_rentals.php"`
3. Set to run every hour:
   - Go to task properties > Triggers > Edit
   - Check "Repeat task every: 1 hour"
   - Duration: Indefinitely

### Option 2: Manual Cron (Linux/Mac)

Add to crontab:
```
0 * * * * /usr/bin/php /path/to/niceadmin/cron/check_rentals.php
```

### Option 3: Browser-Based (Simple Testing)

Create a file `auto_check.php` in root:
```php
<?php
// Run this URL every hour: http://localhost/niceadmin/auto_check.php
require_once 'cron/check_rentals.php';
echo "Rental check completed!";
?>
```

## Database Changes

The system automatically adds:
- `reminder_sent` column to `car_rental_bookings` table
- Updates car status based on active rentals

## How It Works

1. **Car Availability**: 
   - Checks if car has booking with status "Confirmed" or "Pending"
   - Checks if current date is between pickup and return dates
   - Sets car status to "Rented" if conditions met

2. **Reminders**:
   - Checks for bookings with return_date = tomorrow
   - Only sends if status = "Confirmed" and reminder_sent = 0
   - Inserts notification into notifications table
   - Marks reminder as sent

## Testing

1. Create a booking with return date = tomorrow
2. Run: `php d:\xampp\htdocs\niceadmin\cron\check_rentals.php`
3. Check notifications table for reminder
4. Verify car status changes when booking is active
