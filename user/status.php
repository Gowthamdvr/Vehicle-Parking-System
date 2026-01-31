<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT v.*, s.slot_number FROM vehicles v JOIN parking_slots s ON v.slot_id = s.slot_id WHERE v.user_id = ? AND v.status = 'Parked'");
$stmt->execute([$user_id]);
$parking = $stmt->fetch();

if (!$parking) {
    header("Location: dashboard.php");
    exit;
}

$message = "";
if (isset($_POST['process_exit'])) {
    $amount = $_POST['amount'];
    $vehicle_id = $parking['vehicle_id'];
    $slot_id = $parking['slot_id'];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE vehicles SET exit_time = NOW(), amount = ?, status = 'Exited' WHERE vehicle_id = ?");
        $stmt->execute([$amount, $vehicle_id]);
        
        $stmtSlot = $pdo->prepare("UPDATE parking_slots SET is_available = 1 WHERE slot_id = ?");
        $stmtSlot->execute([$slot_id]);
        
        $pdo->commit();
        header("Location: dashboard.php?msg=Parking ended. Total paid: $".$amount);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}

// Calculate current amount
$entry_time = new DateTime($parking['entry_time']);
$current_time = new DateTime();
$interval = $entry_time->diff($current_time);
$hours = ceil(($interval->days * 24) + $interval->h + ($interval->i / 60));
if($hours == 0) $hours = 1;

$settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
$rate = ($parking['vehicle_type'] == 'Two-Wheeler') ? $settings['two_wheeler_rate'] : $settings['four_wheeler_rate'];
$total_amount = $hours * $rate;

include '../includes/header.php';
?>

<div class="row justify-content-center fade-in">
    <div class="col-md-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-primary mb-0">Live Parking Status</h3>
                <span class="badge bg-success rounded-pill px-3 py-2">Active</span>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">Vehicle Number</small>
                        <span class="h5 fw-bold"><?php echo $parking['vehicle_number']; ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">Parking Slot</small>
                        <span class="h5 fw-bold"><?php echo $parking['slot_number']; ?> (<?php echo $parking['vehicle_type']; ?>)</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">Entry Time</small>
                        <span class="h5 fw-bold"><?php echo date('h:i A, d M', strtotime($parking['entry_time'])); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">Duration so far</small>
                        <span class="h5 fw-bold"><?php echo $hours; ?> Hour(s)</span>
                    </div>
                </div>
            </div>

            <div class="card bg-light border-0 p-4 mb-4 text-center">
                <h4 class="text-muted mb-1">Total Payable Amount</h4>
                <h1 class="display-4 fw-bold text-success">$<?php echo number_format($total_amount, 2); ?></h1>
            </div>

            <form action="" method="POST" onsubmit="return confirm('Do you want to end your parking and pay $<?php echo number_format($total_amount, 2); ?>?')">
                <input type="hidden" name="amount" value="<?php echo $total_amount; ?>">
                <div class="d-grid gap-2">
                    <button type="submit" name="process_exit" class="btn btn-danger btn-lg">Exit Parking & Pay Now</button>
                    <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
