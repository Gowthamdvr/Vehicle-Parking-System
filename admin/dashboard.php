<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

// Stats for Admin
$total_revenue = $pdo->query("SELECT SUM(amount) FROM vehicles")->fetchColumn();
$total_vehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$occupied_slots = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE is_available = 0")->fetchColumn();
$total_slots = $pdo->query("SELECT COUNT(*) FROM parking_slots")->fetchColumn();

// Recent 10 history
$history = $pdo->query("SELECT v.*, s.slot_number FROM vehicles v JOIN parking_slots s ON v.slot_id = s.slot_id ORDER BY v.entry_time DESC LIMIT 10")->fetchAll();

include '../includes/header.php'; 
?>

<div class="row mb-4 fade-in">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Admin Dashboard</h2>
            <p class="text-muted">Overview and management of the system</p>
        </div>
        <div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm bg-gradient text-white" style="background: linear-gradient(45deg, #1d976c, #93f9b9);">
            <div class="card-body">
                <div class="text-uppercase small mb-1 opacity-75">Total Revenue</div>
                <div class="h3 mb-0 fw-bold">$<?php echo number_format($total_revenue ?? 0, 2); ?></div>
                <div class="mt-2 small"><i class="fas fa-chart-line small"></i> Lifetime Earnings</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm bg-gradient text-white" style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <div class="card-body">
                <div class="text-uppercase small mb-1 opacity-75">Total Transactions</div>
                <div class="h3 mb-0 fw-bold"><?php echo $total_vehicles; ?></div>
                <div class="mt-2 small"><i class="fas fa-exchange-alt small"></i> Vehicles Logged</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm bg-gradient text-white" style="background: linear-gradient(45deg, #f6d365, #fda085);">
            <div class="card-body">
                <div class="text-uppercase small mb-1 opacity-75">Slots Booked</div>
                <div class="h3 mb-0 fw-bold"><?php echo $occupied_slots; ?> / <?php echo $total_slots; ?></div>
                <div class="mt-2 small"><i class="fas fa-parking small"></i> Current Occupancy</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm bg-gradient text-white" style="background: linear-gradient(45deg, #36b9cc, #258391);">
            <div class="card-body">
                <div class="text-uppercase small mb-1 opacity-75">Available Slots</div>
                <div class="h3 mb-0 fw-bold"><?php echo ($total_slots - $occupied_slots); ?></div>
                <div class="mt-2 small"><i class="fas fa-check-circle small"></i> Ready for Use</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Recent Parking Records</h5>
                <a href="history.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Vehicle</th>
                            <th>Slot</th>
                            <th>Entry</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($history as $row): ?>
                        <tr>
                            <td>
                                <div><strong><?php echo $row['vehicle_number']; ?></strong></div>
                                <div class="small text-muted"><?php echo $row['vehicle_type']; ?></div>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo $row['slot_number']; ?></span></td>
                            <td class="small"><?php echo date('H:i, d M', strtotime($row['entry_time'])); ?></td>
                            <td>
                                <?php if($row['status'] == 'Parked'): ?>
                                    <span class="badge bg-danger rounded-pill">Parked</span>
                                <?php else: ?>
                                    <span class="badge bg-success rounded-pill">Exited</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 shadow-sm mb-4">
            <h5 class="fw-bold mb-3">Quick Actions</h5>
            <div class="list-group list-group-flush">
                <a href="slots.php" class="list-group-item list-group-item-action px-0 border-0">
                    <i class="fas fa-th me-2 text-primary"></i> Manage Parking Slots
                </a>
                <a href="settings.php" class="list-group-item list-group-item-action px-0 border-0">
                    <i class="fas fa-cog me-2 text-info"></i> Adjust Parking Rates
                </a>
                <a href="history.php" class="list-group-item list-group-item-action px-0 border-0">
                    <i class="fas fa-history me-2 text-warning"></i> Full Reports
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
