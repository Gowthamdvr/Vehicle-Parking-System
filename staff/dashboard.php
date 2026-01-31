<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

// Staff specific stats
$total_parked = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE status = 'Parked'")->fetchColumn();
$available_slots = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE is_available = 1")->fetchColumn();

include '../includes/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-md-8">
        <h2 class="fw-bold">Staff Operations Dashboard</h2>
        <p class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['staff_name']); ?>. Manage on-site parking here.</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="card bg-primary text-white p-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-car-side fa-3x me-3 opacity-50"></i>
                <div>
                    <h5 class="mb-0">Vehicles Currently Parked</h5>
                    <h2 class="fw-bold"><?php echo $total_parked; ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white p-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-th fa-3x me-3 opacity-50"></i>
                <div>
                    <h5 class="mb-0">Available Spaces</h5>
                    <h2 class="fw-bold"><?php echo $available_slots; ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm text-center">
            <i class="fas fa-sign-in-alt fa-3x text-primary mb-3"></i>
            <h4>New Entry</h4>
            <p class="text-muted">Register a vehicle at the entrance gate.</p>
            <a href="../entry.php" class="btn btn-primary btn-lg mt-auto">Process Entry</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm text-center border-danger-subtle">
            <i class="fas fa-sign-out-alt fa-3x text-danger mb-3"></i>
            <h4>Vehicle Exit</h4>
            <p class="text-muted">Calculate bill and release vehicle at the exit gate.</p>
            <a href="../exit.php" class="btn btn-danger btn-lg mt-auto text-white">Process Exit</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
