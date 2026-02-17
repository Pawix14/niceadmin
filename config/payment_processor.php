<?php
/**
 * Payment Processing System
 * Handles all payment operations with professional flow
 */

class PaymentProcessor {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Process payment for booking
     */
    public function processPayment($booking_id, $payment_data) {
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // Get booking details
            $booking = $this->getBookingDetails($booking_id);
            
            if (!$booking) {
                throw new Exception('Booking not found');
            }
            
            // Validate payment amount
            if ($payment_data['amount'] != $booking['total_amount']) {
                throw new Exception('Payment amount mismatch');
            }
            
            // Generate payment reference
            $payment_reference = $this->generatePaymentReference();
            
            // Insert payment record
            $stmt = $this->conn->prepare("INSERT INTO payments 
                (booking_id, customer_id, amount, payment_method, payment_reference, 
                transaction_id, payment_status, payment_date, payment_details) 
                VALUES (?, ?, ?, ?, ?, ?, 'Completed', NOW(), ?)");
            
            $payment_details = json_encode($payment_data);
            $stmt->bind_param("iidssss", 
                $booking_id,
                $booking['customer_id'],
                $payment_data['amount'],
                $payment_data['payment_method'],
                $payment_reference,
                $payment_data['transaction_id'] ?? null,
                $payment_details
            );
            
            $stmt->execute();
            $payment_id = $this->conn->insert_id;
            
            // Update booking payment status
            $stmt = $this->conn->prepare("UPDATE car_rentals 
                SET payment_status = 'Paid', 
                    payment_method = ?,
                    payment_reference = ?,
                    paid_at = NOW(),
                    status = 'Confirmed'
                WHERE id = ?");
            
            $stmt->bind_param("ssi", 
                $payment_data['payment_method'],
                $payment_reference,
                $booking_id
            );
            
            $stmt->execute();
            
            // Generate invoice
            $invoice_number = $this->generateInvoice($booking_id, $payment_id);
            
            // Log payment activity
            $this->logPaymentActivity($payment_id, 'Payment Completed', $booking['customer_id']);
            
            // Commit transaction
            $this->conn->commit();
            
            return [
                'success' => true,
                'payment_id' => $payment_id,
                'payment_reference' => $payment_reference,
                'invoice_number' => $invoice_number,
                'message' => 'Payment processed successfully'
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate unique payment reference
     */
    private function generatePaymentReference() {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
    
    /**
     * Generate invoice number
     */
    private function generateInvoiceNumber() {
        return 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get booking details
     */
    private function getBookingDetails($booking_id) {
        $stmt = $this->conn->prepare("SELECT * FROM car_rentals WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Generate invoice
     */
    public function generateInvoice($booking_id, $payment_id) {
        $invoice_number = $this->generateInvoiceNumber();
        
        $stmt = $this->conn->prepare("INSERT INTO invoices 
            (invoice_number, booking_id, payment_id, invoice_date, due_date, 
            subtotal, tax_amount, discount_amount, total_amount, status) 
            SELECT ?, id, ?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY),
            total_amount, 0, discount_amount, total_amount, 'Paid'
            FROM car_rentals WHERE id = ?");
        
        $stmt->bind_param("sii", $invoice_number, $payment_id, $booking_id);
        $stmt->execute();
        
        return $invoice_number;
    }
    
    /**
     * Process refund
     */
    public function processRefund($payment_id, $refund_amount, $reason) {
        $this->conn->begin_transaction();
        
        try {
            // Get payment details
            $stmt = $this->conn->prepare("SELECT * FROM payments WHERE id = ?");
            $stmt->bind_param("i", $payment_id);
            $stmt->execute();
            $payment = $stmt->get_result()->fetch_assoc();
            
            if (!$payment) {
                throw new Exception('Payment not found');
            }
            
            // Generate refund reference
            $refund_reference = 'REF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
            
            // Insert refund record
            $stmt = $this->conn->prepare("INSERT INTO refunds 
                (payment_id, booking_id, refund_amount, refund_reason, 
                refund_reference, refund_status, refund_date) 
                VALUES (?, ?, ?, ?, ?, 'Completed', NOW())");
            
            $stmt->bind_param("iidss", 
                $payment_id,
                $payment['booking_id'],
                $refund_amount,
                $reason,
                $refund_reference
            );
            
            $stmt->execute();
            
            // Update payment status
            $stmt = $this->conn->prepare("UPDATE payments 
                SET payment_status = 'Refunded', refund_amount = ? 
                WHERE id = ?");
            
            $stmt->bind_param("di", $refund_amount, $payment_id);
            $stmt->execute();
            
            // Update booking
            $stmt = $this->conn->prepare("UPDATE car_rentals 
                SET payment_status = 'Refunded', refund_amount = ? 
                WHERE id = ?");
            
            $stmt->bind_param("di", $refund_amount, $payment['booking_id']);
            $stmt->execute();
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'refund_reference' => $refund_reference,
                'message' => 'Refund processed successfully'
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get payment methods
     */
    public function getPaymentMethods() {
        return [
            'gcash' => ['name' => 'GCash', 'icon' => 'bi-phone', 'enabled' => true],
            'paymaya' => ['name' => 'PayMaya', 'icon' => 'bi-wallet2', 'enabled' => true],
            'bank_transfer' => ['name' => 'Bank Transfer', 'icon' => 'bi-bank', 'enabled' => true],
            'credit_card' => ['name' => 'Credit/Debit Card', 'icon' => 'bi-credit-card', 'enabled' => true],
            'cash' => ['name' => 'Cash on Pickup', 'icon' => 'bi-cash-stack', 'enabled' => true]
        ];
    }
    
    /**
     * Log payment activity
     */
    private function logPaymentActivity($payment_id, $activity, $user_id) {
        $stmt = $this->conn->prepare("INSERT INTO payment_logs 
            (payment_id, activity, user_id, created_at) 
            VALUES (?, ?, ?, NOW())");
        
        $stmt->bind_param("isi", $payment_id, $activity, $user_id);
        $stmt->execute();
    }
    
    /**
     * Get payment statistics for admin
     */
    public function getPaymentStats($date_from = null, $date_to = null) {
        $where = "WHERE payment_status = 'Completed'";
        
        if ($date_from && $date_to) {
            $where .= " AND DATE(payment_date) BETWEEN '$date_from' AND '$date_to'";
        }
        
        $sql = "SELECT 
            COUNT(*) as total_transactions,
            SUM(amount) as total_revenue,
            AVG(amount) as average_transaction,
            payment_method,
            COUNT(*) as method_count
            FROM payments 
            $where
            GROUP BY payment_method";
        
        $result = $this->conn->query($sql);
        $stats = [];
        
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }
        
        return $stats;
    }
    
    /**
     * Verify payment (for admin)
     */
    public function verifyPayment($payment_id, $admin_id) {
        $stmt = $this->conn->prepare("UPDATE payments 
            SET verified_by = ?, verified_at = NOW(), payment_status = 'Verified' 
            WHERE id = ?");
        
        $stmt->bind_param("ii", $admin_id, $payment_id);
        
        if ($stmt->execute()) {
            $this->logPaymentActivity($payment_id, 'Payment Verified by Admin', $admin_id);
            return ['success' => true, 'message' => 'Payment verified'];
        }
        
        return ['success' => false, 'message' => 'Verification failed'];
    }
}
?>
