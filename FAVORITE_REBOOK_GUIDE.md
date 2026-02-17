# Favorite Cars & Rebook Feature Implementation Guide

## Overview
This guide explains how to add:
1. **Favorite Cars** - Allow customers to mark cars as favorites
2. **Rebook** - Quick rebook button that auto-selects the car and goes to calendar

## Step 1: Create Database Table
Run this SQL in phpMyAdmin:
```sql
CREATE TABLE IF NOT EXISTS favorite_cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(100) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    car_type VARCHAR(50) NOT NULL,
    car_image VARCHAR(255),
    daily_rate DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (customer_email, car_model)
);
```

## Step 2: Add Favorite Button to Car Cards
In `car_rental.php`, find the car card section (around line 450) and add a heart icon button:

**Find this code:**
```php
<div class="d-flex gap-1">
    <a href="index.php?page=car_availability_customer&car_name=<?php echo urlencode($car['name']); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-calendar-week me-1"></i>Select
    </a>
</div>
```

**Replace with:**
```php
<div class="d-flex gap-1">
    <button class="btn btn-outline-danger btn-sm favorite-btn" 
            data-car-model="<?php echo htmlspecialchars($car['name']); ?>"
            data-car-type="<?php echo htmlspecialchars($car['type']); ?>"
            data-car-image="<?php echo htmlspecialchars($car['image']); ?>"
            data-car-rate="<?php echo $car['daily_rate']; ?>">
        <i class="bi bi-heart"></i>
    </button>
    <a href="index.php?page=car_availability_customer&car_name=<?php echo urlencode($car['name']); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-calendar-week me-1"></i>Select
    </a>
</div>
```

## Step 3: Add JavaScript for Favorite Functionality
Add this JavaScript before the closing `</script>` tag in `car_rental.php`:

```javascript
// Favorite car functionality
document.querySelectorAll('.favorite-btn').forEach(btn => {
    // Check if car is already favorited
    checkFavoriteStatus(btn);
    
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const carModel = this.dataset.carModel;
        const isFavorite = this.classList.contains('favorited');
        
        if (isFavorite) {
            removeFavorite(carModel, this);
        } else {
            addFavorite(this);
        }
    });
});

function checkFavoriteStatus(btn) {
    fetch('modules/favorite_car_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=check&car_model=' + encodeURIComponent(btn.dataset.carModel)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.is_favorite) {
            btn.classList.add('favorited', 'btn-danger');
            btn.classList.remove('btn-outline-danger');
            btn.querySelector('i').classList.replace('bi-heart', 'bi-heart-fill');
        }
    });
}

function addFavorite(btn) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('car_model', btn.dataset.carModel);
    formData.append('car_type', btn.dataset.carType);
    formData.append('car_image', btn.dataset.carImage);
    formData.append('car_rate', btn.dataset.carRate);
    
    fetch('modules/favorite_car_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.classList.add('favorited', 'btn-danger');
            btn.classList.remove('btn-outline-danger');
            btn.querySelector('i').classList.replace('bi-heart', 'bi-heart-fill');
            alert('✅ Added to favorites!');
        } else {
            alert('❌ ' + data.message);
        }
    });
}

function removeFavorite(carModel, btn) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('car_model', carModel);
    
    fetch('modules/favorite_car_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.classList.remove('favorited', 'btn-danger');
            btn.classList.add('btn-outline-danger');
            btn.querySelector('i').classList.replace('bi-heart-fill', 'bi-heart');
            alert('✅ Removed from favorites!');
        }
    });
}
```

## Step 4: Add Rebook Button to My Bookings
In `my_bookings.php`, find the action buttons section and add a rebook button:

**Find the action column (around line 200):**
```php
<td>
    <a href="index.php?page=booking_details&id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-info">
        <i class="bi bi-eye"></i> View
    </a>
</td>
```

**Add rebook button:**
```php
<td>
    <a href="index.php?page=booking_details&id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-info">
        <i class="bi bi-eye"></i> View
    </a>
    <?php if ($booking['status'] == 'Completed'): ?>
    <a href="index.php?page=car_rental&rebook=<?php echo urlencode($booking['car_model']); ?>" class="btn btn-sm btn-success">
        <i class="bi bi-arrow-repeat"></i> Rebook
    </a>
    <?php endif; ?>
</td>
```

## Step 5: Handle Rebook Parameter in car_rental.php
Add this PHP code at the top of `car_rental.php` (after database connection):

```php
// Check for rebook parameter
$rebook_car = isset($_GET['rebook']) ? $_GET['rebook'] : null;
```

Then in the JavaScript section, add this to the DOMContentLoaded event:

```javascript
// Check for rebook parameter
const rebookCar = '<?php echo $rebook_car ? addslashes($rebook_car) : ""; ?>';
if (rebookCar) {
    // Auto-select the car
    const carCards = document.querySelectorAll('.car-item');
    carCards.forEach(card => {
        const cardTitle = card.querySelector('.car-card-title');
        if (cardTitle && cardTitle.textContent.trim() === rebookCar) {
            const priceText = card.querySelector('.car-card-price').textContent;
            const rate = parseFloat(priceText.replace(/[^0-9.]/g, ''));
            const typeText = card.querySelector('.badge').textContent;
            const imgSrc = card.querySelector('.car-card-img').src;
            
            selectedCar = {
                id: rebookCar.toLowerCase().replace(/ /g, '-'),
                name: rebookCar,
                type: typeText,
                image: imgSrc,
                rate: rate
            };
            
            // Update UI
            document.getElementById('selectedCarPlaceholder').style.display = 'none';
            document.getElementById('selectedCarDetails').style.display = 'block';
            document.getElementById('selectedCarImage').src = selectedCar.image;
            document.getElementById('selectedCarName').textContent = selectedCar.name;
            document.getElementById('selectedCarType').textContent = selectedCar.type + ' Car';
            document.getElementById('selectedCarRate').textContent = '₱' + selectedCar.rate.toFixed(2) + '/day';
            
            // Update form fields
            document.getElementById('form_car_model').value = selectedCar.name;
            document.getElementById('form_car_type').value = selectedCar.type;
            document.getElementById('form_car_image').value = selectedCar.image;
            document.getElementById('form_daily_rate').value = selectedCar.rate;
            
            // Hide car selection, show rental details
            document.getElementById('carSelectionCard').style.display = 'none';
            
            // Scroll to rental details
            document.querySelector('.card.mb-4:nth-of-type(2)').scrollIntoView({ behavior: 'smooth' });
            
            updatePrice();
            updateBookNowButton();
        }
    });
}
```

## Files Created
1. `setup/create_favorite_cars_table.sql` - Database table creation
2. `modules/favorite_car_handler.php` - PHP handler for favorite operations

## Testing
1. Run the SQL script to create the table
2. Login as a customer
3. Click the heart icon on any car - it should turn red
4. Click again to remove from favorites
5. Complete a booking, then click "Rebook" button - car should auto-select and scroll to calendar

## Features Summary
- ❤️ **Favorite Cars**: Click heart icon to save favorite cars
- 🔄 **Rebook**: One-click rebook from completed bookings
- 📅 **Auto-scroll**: Automatically scrolls to date selection when rebooking
- 💾 **Persistent**: Favorites are saved per customer email
