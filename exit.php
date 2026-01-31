<?php 
require_once 'includes/db_connect.php';

$message = "";
$messageType = "";
$vehicle_details = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['search_vehicle'])) {
        $vehicle_number = strtoupper(trim($_POST['vehicle_number']));
        $stmt = $pdo->prepare("SELECT v.*, s.slot_number FROM vehicles v JOIN parking_slots s ON v.slot_id = s.slot_id WHERE v.vehicle_number = ? AND v.status = 'Parked'");
        $stmt->execute([$vehicle_number]);
        $vehicle_details = $stmt->fetch();
        
        if (!$vehicle_details) {
            $message = "Vehicle not found or already exited!";
            $messageType = "danger";
        }
    }

    if (isset($_POST['process_exit'])) {
        $vehicle_id = $_POST['vehicle_id'];
        $slot_id = $_POST['slot_id'];
        $amount = $_POST['amount'];

        $pdo->beginTransaction();
        try {
            // Update vehicle status
            $stmt = $pdo->prepare("UPDATE vehicles SET exit_time = NOW(), amount = ?, status = 'Exited' WHERE vehicle_id = ?");
            $stmt->execute([$amount, $vehicle_id]);
            
            // Free the slot
            $stmtSlot = $pdo->prepare("UPDATE parking_slots SET is_available = 1 WHERE slot_id = ?");
            $stmtSlot->execute([$slot_id]);
            
            $pdo->commit();
            $message = "Vehicle exited successfully! Amount Collected: $" . $amount;
            $messageType = "success";
            $vehicle_details = null;
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

include 'includes/header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 fade-in">
            <h3 class="fw-bold mb-4 text-center text-danger">Process Vehicle Exit</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" name="vehicle_number" class="form-control form-control-lg" placeholder="Enter Vehicle Number (e.g. MH 12 AB 1234)" required>
                    <button class="btn btn-primary px-4" type="submit" name="search_vehicle">Search</button>
                </div>
            </form>

            <?php if ($vehicle_details): ?>
                <div class="p-4 border rounded bg-light">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Vehicle Summary</h5>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <span class="text-muted small d-block">Vehicle Number</span>
                            <strong><?php echo $vehicle_details['vehicle_number']; ?></strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted small d-block">Owner Name</span>
                            <strong><?php echo $vehicle_details['owner_name'] ?: 'N/A'; ?></strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted small d-block">Entry Time</span>
                            <strong><?php echo date('M d, Y h:i A', strtotime($vehicle_details['entry_time'])); ?></strong>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted small d-block">Assigned Slot</span>
                            <strong><span class="badge bg-primary"><?php echo $vehicle_details['slot_number']; ?></span></strong>
                        </div>
                    </div>

                    <hr>

                    <?php 
                        // Calculate amount
                        $entry_time = new DateTime($vehicle_details['entry_time']);
                        $current_time = new DateTime();
                        $interval = $entry_time->diff($current_time);
                        $hours = ceil(($interval->days * 24) + $interval->h + ($interval->i / 60));
                        
                        // Get rates from settings
                        $settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
                        $rate = ($vehicle_details['vehicle_type'] == 'Two-Wheeler') ? $settings['two_wheeler_rate'] : $settings['four_wheeler_rate'];
                        $total_amount = $hours * $rate;
                    ?>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="text-muted small d-block">Duration (Rounded Hours)</span>
                            <span class="h5 fw-bold"><?php echo $hours; ?> hour(s)</span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Calculated Amount</span>
                            <span class="h3 fw-bold text-success">$<?php echo number_format($total_amount, 2); ?></span>
                        </div>
                    </div>

                    <form method="POST" action="" class="mt-4">
                        <input type="hidden" name="vehicle_id" value="<?php echo $vehicle_details['vehicle_id']; ?>">
                        <input type="hidden" name="slot_id" value="<?php echo $vehicle_details['slot_id']; ?>">
                        <input type="hidden" name="amount" value="<?php echo $total_amount; ?>">
                        <div class="d-grid">
                            <button type="submit" name="process_exit" class="btn btn-danger btn-lg text-white">Confirm Exit & Payment</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
