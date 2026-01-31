<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// Check if user has an active parking
$stmt = $pdo->prepare("SELECT v.*, s.slot_number FROM vehicles v JOIN parking_slots s ON v.slot_id = s.slot_id WHERE v.user_id = ? AND v.status = 'Parked'");
$stmt->execute([$user_id]);
$active_parking = $stmt->fetch();

include '../includes/header.php';
?>

<div class="row fade-in">
    <div class="col-md-12 mb-4">
        <h2 class="fw-bold">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <p class="text-muted">Manage your parking sessions here.</p>
    </div>
</div>

<div class="row">
    <?php if ($active_parking): ?>
        <div class="col-md-12 mb-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary">Active Parking Session</h5>
                    <p class="card-text">
                        <strong>Vehicle:</strong> <?php echo $active_parking['vehicle_number']; ?><br>
                        <strong>Slot:</strong> <?php echo $active_parking['slot_number']; ?><br>
                        <strong>Entry Time:</strong> <?php echo date('M d, Y h:i A', strtotime($active_parking['entry_time'])); ?>
                    </p>
                    <a href="status.php" class="btn btn-primary">View Status & Exit</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100 p-4 text-center">
                <i class="fas fa-parking fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold">New Parking</h4>
                <p class="text-muted">Need a spot? Book your parking slot now.</p>
                <a href="select_slot.php" class="btn btn-primary btn-lg mt-auto">Book a Slot</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-6 mb-4">
        <div class="card h-100 p-4 text-center">
            <i class="fas fa-history fa-3x text-secondary mb-3"></i>
            <h4 class="fw-bold">My History</h4>
            <p class="text-muted">View your previous parking records.</p>
            <a href="history.php" class="btn btn-outline-secondary btn-lg mt-auto">View History</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
