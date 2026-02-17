<!-- Toast Notification Container -->
<div id="toastContainer" style="position: fixed; top: 80px; right: 20px; z-index: 9999; width: 350px;"></div>

<style>
.toast-notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 16px;
    margin-bottom: 10px;
    animation: slideIn 0.3s ease-out;
    border-left: 4px solid #3b82f6;
}
@keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.toast-notification.success { border-left-color: #10b981; }
.toast-notification.warning { border-left-color: #f59e0b; }
.toast-notification.error { border-left-color: #ef4444; }
</style>

<script>
function showToast(title, message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong>${title}</strong>
                <p class="mb-0 small mt-1">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="btn-close btn-sm"></button>
        </div>
    `;
    document.getElementById('toastContainer').appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
</script>
