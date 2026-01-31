<?php 
require_once 'includes/db_connect.php';
include 'includes/header.php'; 

// Fetch some overview stats for the landing page
$available_slots = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE is_available = 1")->fetchColumn();
$total_slots = $pdo->query("SELECT COUNT(*) FROM parking_slots")->fetchColumn();
?>

<div class="hero-section text-center py-5 fade-in">
    <div class="container">
        <h1 class="display-4 fw-bold text-dark mb-3">Smart Parking Solutions</h1>
        <p class="lead text-muted mb-5">Efficient, secure, and automated vehicle management for everyone.</p>
        
        <div class="row g-4 justify-content-center">
            <!-- User Login -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-lg hover-lift">
                    <div class="card-body p-5">
                        <div class="icon-box mb-4 mx-auto bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h3 class="fw-bold">User Portal</h3>
                        <p class="text-muted small">Book slots, make payments, and track your parking status online.</p>
                        <div class="d-grid gap-2 mt-4">
                            <a href="user/login.php" class="btn btn-success btn-lg">User Login</a>
                            <a href="user/register.php" class="btn btn-link text-decoration-none text-success">Create a New Account</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Login -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-lg hover-lift">
                    <div class="card-body p-5">
                        <div class="icon-box mb-4 mx-auto bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <h3 class="fw-bold">Staff Login</h3>
                        <p class="text-muted small">Manage vehicle entry/exit and process on-site parking requests.</p>
                        <div class="d-grid gap-2 mt-4">
                            <a href="staff/login.php" class="btn btn-primary btn-lg">Staff Portal</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Login -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-lg hover-lift">
                    <div class="card-body p-5">
                        <div class="icon-box mb-4 mx-auto bg-dark bg-opacity-10 text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                        <h3 class="fw-bold">Administrator</h3>
                        <p class="text-muted small">Full system access: Manage slots, view reports, and configure rates.</p>
                        <div class="d-grid gap-2 mt-4">
                            <a href="admin/login.php" class="btn btn-dark btn-lg">Admin Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 p-4 bg-white rounded-pill shadow-sm d-inline-block px-5">
            <span class="text-muted me-3"><i class="fas fa-parking text-primary me-2"></i> Current Availability:</span>
            <span class="h5 fw-bold text-primary mb-0"><?php echo $available_slots; ?> / <?php echo $total_slots; ?> Slots Free</span>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}
.icon-box i {
    font-size: 2.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>
