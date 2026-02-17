-- Create favorite_cars table
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
