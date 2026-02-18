<?php
// Enhanced Notification System
class NotificationManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Create a new notification with enhanced features
     */
    public function create($data) {
        $user_type = $this->conn->real_escape_string($data['user_type']);
        $user_id = isset($data['user_id']) ? $this->conn->real_escape_string($data['user_id']) : NULL;
        $title = $this->conn->real_escape_string($data['title']);
        $message = $this->conn->real_escape_string($data['message']);
        $category = isset($data['category']) ? $this->conn->real_escape_string($data['category']) : 'general';
        $priority = isset($data['priority']) ? $this->conn->real_escape_string($data['priority']) : 'normal';
        $icon = isset($data['icon']) ? $this->conn->real_escape_string($data['icon']) : 'bell';
        $booking_id = isset($data['booking_id']) ? $this->conn->real_escape_string($data['booking_id']) : NULL;
        $action_url = isset($data['action_url']) ? $this->conn->real_escape_string($data['action_url']) : NULL;
        $action_label = isset($data['action_label']) ? $this->conn->real_escape_string($data['action_label']) : NULL;
        
        $sql = "INSERT INTO notifications (
            user_type, user_id, title, message, category, priority, icon, 
            booking_id, action_url, action_label, is_read
        ) VALUES (
            '$user_type', " . ($user_id ? "'$user_id'" : "NULL") . ", '$title', '$message', 
            '$category', '$priority', '$icon', " . ($booking_id ? "'$booking_id'" : "NULL") . ", 
            " . ($action_url ? "'$action_url'" : "NULL") . ", " . ($action_label ? "'$action_label'" : "NULL") . ", 0
        )";
        
        return $this->conn->query($sql);
    }
    
    /**
     * Quick notification creators
     */
    public function bookingCreated($booking_id, $customer_email, $car_model) {
        // Notify admin
        $this->create([
            'user_type' => 'admin',
            'title' => 'New Booking Request',
            'message' => "New booking for $car_model",
            'category' => 'booking',
            'priority' => 'important',
            'icon' => 'car-front',
            'booking_id' => $booking_id,
            'action_url' => 'index.php?page=all_bookings',
            'action_label' => 'View Booking'
        ]);
        
        // Notify staff
        $this->create([
            'user_type' => 'staff',
            'title' => 'New Booking - Action Required',
            'message' => "Review booking for $car_model",
            'category' => 'booking',
            'priority' => 'important',
            'icon' => 'clipboard-check',
            'booking_id' => $booking_id,
            'action_url' => 'index.php?page=staff_booking_review',
            'action_label' => 'Review Now'
        ]);
        
        // Notify customer
        $this->create([
            'user_type' => 'customer',
            'user_id' => $customer_email,
            'title' => 'Booking Submitted',
            'message' => "Your booking $booking_id is pending review",
            'category' => 'booking',
            'priority' => 'normal',
            'icon' => 'check-circle',
            'booking_id' => $booking_id,
            'action_url' => 'index.php?page=my_bookings',
            'action_label' => 'View Details'
        ]);
    }
    
    public function bookingApproved($booking_id, $customer_email, $car_model) {
        $this->create([
            'user_type' => 'customer',
            'user_id' => $customer_email,
            'title' => '✅ Booking Approved!',
            'message' => "Your booking for $car_model has been approved",
            'category' => 'booking',
            'priority' => 'important',
            'icon' => 'check-circle-fill',
            'booking_id' => $booking_id,
            'action_url' => 'index.php?page=my_bookings',
            'action_label' => 'View Booking'
        ]);
    }
    
    public function paymentDue($booking_id, $customer_email, $amount) {
        $this->create([
            'user_type' => 'customer',
            'user_id' => $customer_email,
            'title' => '💰 Payment Due',
            'message' => "Payment of ₱" . number_format($amount, 2) . " is due",
            'category' => 'payment',
            'priority' => 'critical',
            'icon' => 'exclamation-triangle',
            'booking_id' => $booking_id,
            'action_url' => 'index.php?page=my_payments',
            'action_label' => 'Pay Now'
        ]);
    }
    
    public function pickupReminder($booking_id, $customer_email, $car_model, $pickup_date) {
        $this->create([
            'user_type' => 'customer',
            'user_id' => $customer_email,
            'title' => '📅 Pickup Reminder',
            'message' => "Your $car_model pickup is on " . date('M d, Y', strtotime($pickup_date)),
            'category' => 'booking',
            'priority' => 'important',
            'icon' => 'calendar-event',
            'booking_id' => $booking_id,
            'action_url' => 'index.php?page=my_bookings',
            'action_label' => 'View Details'
        ]);
    }
    
    public function documentRequired($customer_email, $document_type) {
        $this->create([
            'user_type' => 'customer',
            'user_id' => $customer_email,
            'title' => '📄 Document Required',
            'message' => "Please upload your $document_type",
            'category' => 'document',
            'priority' => 'important',
            'icon' => 'file-earmark-text',
            'action_url' => 'index.php?page=documents',
            'action_label' => 'Upload Now'
        ]);
    }
    
    /**
     * Get notifications with enhanced data
     */
    public function getForUser($user_type, $user_id = null, $limit = 20) {
        $where = "user_type = '$user_type'";
        if ($user_id) {
            $where .= " AND user_id = '$user_id'";
        }
        
        $sql = "SELECT *, 
            CASE 
                WHEN TIMESTAMPDIFF(MINUTE, created_at, NOW()) < 1 THEN 'Just now'
                WHEN TIMESTAMPDIFF(MINUTE, created_at, NOW()) < 60 THEN CONCAT(TIMESTAMPDIFF(MINUTE, created_at, NOW()), 'm ago')
                WHEN TIMESTAMPDIFF(HOUR, created_at, NOW()) < 24 THEN CONCAT(TIMESTAMPDIFF(HOUR, created_at, NOW()), 'h ago')
                WHEN TIMESTAMPDIFF(DAY, created_at, NOW()) < 7 THEN CONCAT(TIMESTAMPDIFF(DAY, created_at, NOW()), 'd ago')
                ELSE DATE_FORMAT(created_at, '%b %d')
            END as time_ago
            FROM notifications 
            WHERE $where 
            AND (dismissed_at IS NULL)
            AND (expires_at IS NULL OR expires_at > NOW())
            ORDER BY is_read ASC, created_at DESC 
            LIMIT $limit";
        
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount($user_type, $user_id = null) {
        $where = "user_type = '$user_type' AND is_read = 0";
        if ($user_id) {
            $where .= " AND user_id = '$user_id'";
        }
        
        $result = $this->conn->query("SELECT COUNT(*) as count FROM notifications WHERE $where");
        return $result->fetch_assoc()['count'];
    }
    
    /**
     * Mark as read
     */
    public function markAsRead($notification_id) {
        return $this->conn->query("UPDATE notifications SET is_read = 1 WHERE id = $notification_id");
    }
    
    /**
     * Mark all as read
     */
    public function markAllAsRead($user_type, $user_id = null) {
        $where = "user_type = '$user_type'";
        if ($user_id) {
            $where .= " AND user_id = '$user_id'";
        }
        return $this->conn->query("UPDATE notifications SET is_read = 1 WHERE $where");
    }
    
    /**
     * Dismiss notification
     */
    public function dismiss($notification_id) {
        return $this->conn->query("UPDATE notifications SET dismissed_at = NOW() WHERE id = $notification_id");
    }
    
    /**
     * Get category icon
     */
    public static function getCategoryIcon($category) {
        $icons = [
            'booking' => 'car-front',
            'payment' => 'cash-coin',
            'document' => 'file-earmark-text',
            'message' => 'chat-dots',
            'alert' => 'exclamation-triangle',
            'general' => 'bell'
        ];
        return $icons[$category] ?? 'bell';
    }
    
    /**
     * Get priority badge class
     */
    public static function getPriorityClass($priority) {
        $classes = [
            'critical' => 'bg-danger',
            'important' => 'bg-warning',
            'normal' => 'bg-info'
        ];
        return $classes[$priority] ?? 'bg-secondary';
    }
}
?>
