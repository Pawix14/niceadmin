// Real-Time Notification System
class NotificationSystem {
    constructor() {
        this.lastCheck = Date.now();
        this.unreadCount = 0;
        this.soundEnabled = localStorage.getItem('notificationSound') !== 'false';
        this.init();
    }
    
    init() {
        this.updateBadge();
        this.startPolling();
        this.setupEventListeners();
    }
    
    startPolling() {
        // Check for new notifications every 30 seconds
        setInterval(() => this.checkForNew(), 30000);
    }
    
    async checkForNew() {
        try {
            const response = await fetch('modules/get_notifications.php?full=1&since=' + this.lastCheck);
            const data = await response.json();
            
            if (data.unread_count !== this.unreadCount) {
                this.unreadCount = data.unread_count;
                this.updateBadge();
                
                // Show new notifications as toasts
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notif => {
                        if (!notif.is_read && new Date(notif.created_at) > new Date(this.lastCheck)) {
                            this.showToast(notif);
                        }
                    });
                }
            }
            
            this.lastCheck = Date.now();
        } catch (error) {
            console.error('Failed to check notifications:', error);
        }
    }
    
    updateBadge() {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = this.unreadCount;
            badge.style.display = this.unreadCount > 0 ? 'inline-block' : 'none';
        }
    }
    
    showToast(notification) {
        const toast = document.createElement('div');
        toast.className = `notification-toast priority-${notification.priority || 'normal'}`;
        
        const icon = this.getIcon(notification.category);
        const priorityBadge = notification.priority === 'critical' ? '<span class="badge bg-danger">URGENT</span>' : '';
        
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="bi bi-${icon}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">
                    ${priorityBadge}
                    <strong>${this.escapeHtml(notification.title)}</strong>
                </div>
                <div class="toast-message">${this.escapeHtml(notification.message)}</div>
                <div class="toast-time">${notification.time_ago || 'Just now'}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        // Add click handler
        toast.addEventListener('click', (e) => {
            if (!e.target.closest('.toast-close')) {
                if (notification.action_url) {
                    window.location.href = notification.action_url;
                } else {
                    window.location.href = 'index.php?page=notifications';
                }
            }
        });
        
        document.body.appendChild(toast);
        
        // Play sound
        if (this.soundEnabled) {
            this.playNotificationSound();
        }
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
    
    playNotificationSound() {
        // Simple beep sound using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (e) {
            console.log('Sound not supported');
        }
    }
    
    getIcon(category) {
        const icons = {
            'booking': 'car-front',
            'payment': 'cash-coin',
            'document': 'file-earmark-text',
            'message': 'chat-dots',
            'alert': 'exclamation-triangle',
            'general': 'bell'
        };
        return icons[category] || 'bell';
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    setupEventListeners() {
        // Mark all as read button
        const markAllBtn = document.querySelector('[data-action="mark-all-read"]');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.markAllAsRead());
        }
        
        // Sound toggle
        const soundToggle = document.querySelector('[data-action="toggle-sound"]');
        if (soundToggle) {
            soundToggle.addEventListener('click', () => this.toggleSound());
        }
    }
    
    async markAllAsRead() {
        try {
            await fetch('modules/mark_notification_read.php?mark_all=1');
            this.unreadCount = 0;
            this.updateBadge();
            location.reload();
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    }
    
    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        localStorage.setItem('notificationSound', this.soundEnabled);
        
        const icon = document.querySelector('[data-action="toggle-sound"] i');
        if (icon) {
            icon.className = this.soundEnabled ? 'bi bi-volume-up' : 'bi bi-volume-mute';
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.notificationSystem = new NotificationSystem();
});
