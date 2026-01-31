document.addEventListener('DOMContentLoaded', function() {
    // Add any global JS logic here
    console.log('ParkEase Loaded');

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
